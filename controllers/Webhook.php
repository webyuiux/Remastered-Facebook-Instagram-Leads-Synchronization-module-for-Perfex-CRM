<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webhook extends App_Controller
{
    /**
     * Fetch a URL using cURL (preferred) or file_get_contents as fallback.
     * Works regardless of PHP allow_url_fopen setting.
     */
    private function fetch_url($url)
    {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_USERAGENT, 'MetaLeads-Webhook/1.0');
            $response = curl_exec($ch);
            curl_close($ch);
            return $response ?: '';
        }
        return @file_get_contents($url) ?: '';
    }

    public function index()
    {
        // Meta Webhook Verification
        if ($this->input->get('hub_mode') == 'subscribe' && $this->input->get('hub_verify_token')) {
            if ($this->input->get('hub_verify_token') === get_option('meta_leads_verify_token')) {
                echo $this->input->get('hub_challenge');
                exit;
            } else {
                header('HTTP/1.1 403 Forbidden');
                exit;
            }
        }

        // Receive Lead Data
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($data && isset($data['entry'])) {
            $this->load->model('meta_leads/meta_leads_model');
            $user_token = get_option('meta_leads_access_token');

            foreach ($data['entry'] as $entry) {
                if (isset($entry['changes'])) {
                    foreach ($entry['changes'] as $change) {
                        if ($change['field'] == 'leadgen') {
                            $lead_id = $change['value']['leadgen_id'];
                            $form_id = $change['value']['form_id'];
                            $page_id = $change['value']['page_id'];
                            $created_time = $change['value']['created_time'];
                            
                            $name = '-';
                            $email = '-';
                            $phone = '-';
                            $company = '-';
                            $city = '-';
                            $platform = 'Facebook';
                            
                            // Fetch real lead data using Page Access Token generated from User Token
                            if ($user_token) {
                                $page_token_url = "https://graph.facebook.com/v17.0/{$page_id}?fields=access_token&access_token={$user_token}";
                                $page_response = $this->fetch_url($page_token_url);
                                $page_data = $page_response ? @json_decode($page_response, true) : [];
                                
                                if (isset($page_data['access_token'])) {
                                    $page_token = $page_data['access_token'];
                                    
                                    $lead_url = "https://graph.facebook.com/v17.0/{$lead_id}?access_token={$page_token}";
                                    $lead_response = $this->fetch_url($lead_url);
                                    $lead_data = $lead_response ? @json_decode($lead_response, true) : [];
                                    
                                    if(isset($lead_data['platform']) && $lead_data['platform'] == 'ig') {
                                        $platform = 'Instagram';
                                    }
                                    
                                    if (isset($lead_data['field_data'])) {
                                        $f_name = '';
                                        $l_name = '';
                                        $custom_fields = [];
                                        foreach ($lead_data['field_data'] as $field) {
                                            $n = strtolower($field['name']);
                                            $val = $field['values'][0] ?? '';
                                            
                                            if (($n === 'full_name' || $n === 'name') && $name == '-') { $name = $val; }
                                            elseif ($n === 'first_name') { $f_name = $val; }
                                            elseif ($n === 'last_name') { $l_name = $val; }
                                            elseif (strpos($n, 'email') !== false && $email == '-') { $email = $val; }
                                            elseif (strpos($n, 'phone') !== false && $phone == '-') { $phone = $val; }
                                            elseif (strpos($n, 'company') !== false && $company == '-') { $company = $val; }
                                            elseif (strpos($n, 'city') !== false && $city == '-') { $city = $val; }
                                            else {
                                                // Store non-standard fields as custom data
                                                $custom_fields[$field['name']] = $val;
                                            }
                                        }
                                        if ($name == '-' && (!empty($f_name) || !empty($l_name))) {
                                            $name = trim($f_name . ' ' . $l_name);
                                            if(empty($name)) $name = '-';
                                        }
                                    }
                                }
                            }
                            
                            // Check for form mapping in DB
                            $map = $this->meta_leads_model->get_form_mapping($form_id);
                            
                            $this->db->where('meta_lead_id', $lead_id);
                            if ($this->db->get(db_prefix() . 'meta_leads_data')->row()) {
                                $this->meta_leads_model->log_sync($lead_id, 'Duplicate', 'Duplicate lead ignored. ID: ' . $lead_id);
                                continue;
                            }
                            
                            $insert = [
                                'meta_lead_id' => $lead_id,
                                'form_id' => $form_id,
                                'page_id' => $page_id,
                                'name' => $name,
                                'email' => $email,
                                'phone' => $phone,
                                'company' => $company,
                                'city' => $city,
                                'raw_data' => json_encode($custom_fields),
                                'platform' => $platform,
                                'form_name' => $map ? $map['form_name'] : 'Form ' . $form_id,
                                'page_name' => $map ? ($map['page_name'] ?? '') : '',
                                'date_added' => date('Y-m-d H:i:s', $created_time),
                                'status' => 'Pending',
                                'assigned_staff' => $map ? $map['assigned_staff'] : 0,
                                'lead_status' => $map ? $map['lead_status'] : 0,
                                'lead_source' => $map ? $map['lead_source'] : 0,
                            ];
                            
                            $this->db->insert(db_prefix() . 'meta_leads_data', $insert);
                            
                            $log_message = "Successfully synced lead data fields.\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nCompany: {$company}\nCity: {$city}";
                            $this->meta_leads_model->log_sync($lead_id, 'Success', $log_message);
                        }
                    }
                }
            }
        }
        header('HTTP/1.1 200 OK');
    }
}
