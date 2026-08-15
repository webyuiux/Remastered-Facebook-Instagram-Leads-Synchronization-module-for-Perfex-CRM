<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meta_leads extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('meta_leads_model');
    }

    private function _check_permission($cap)
    {
        if (!has_permission('meta_leads', '', $cap) && !is_admin()) {
            access_denied('Meta Leads');
        }
    }

    private function fetch_api($url) {
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response ? $response : "";
        } else {
            $response = @file_get_contents($url);
            return $response ? $response : "";
        }
    }

    public function settings()
    {
        if (!is_admin()) {
            access_denied('Meta Sync');
        }
        if ($this->input->post()) {
            update_option('meta_leads_app_id', $this->input->post('meta_leads_app_id'));
            update_option('meta_leads_app_secret', $this->input->post('meta_leads_app_secret'));
            if (!$this->input->post('meta_leads_verify_token')) {
                update_option('meta_leads_verify_token', md5(time() . rand()));
            } else {
                update_option('meta_leads_verify_token', $this->input->post('meta_leads_verify_token'));
            }
            // Removed global defaults update here
            set_alert('success', _l('settings_updated'));
            redirect(admin_url('meta_leads/settings'));
        }
        
        $data['title'] = _l('meta_leads_settings');
        $this->load->view('settings', $data);
    }
    
    public function oauth() {
        if (!is_admin()) {
            access_denied('Meta Sync');
        }
        $app_id = get_option('meta_leads_app_id');
        $redirect_uri = admin_url('meta_leads/oauth_callback');
        $scope = 'pages_show_list,pages_manage_ads,pages_manage_metadata,pages_read_engagement,leads_retrieval';
        $url = "https://www.facebook.com/v17.0/dialog/oauth?client_id={$app_id}&redirect_uri={$redirect_uri}&scope={$scope}";
        redirect($url);
    }

    public function oauth_callback() {
        if (!is_admin()) {
            access_denied('Meta Sync');
        }
        $code = $this->input->get('code');
        if ($code) {
            $app_id = get_option('meta_leads_app_id');
            $app_secret = get_option('meta_leads_app_secret');
            $redirect_uri = admin_url('meta_leads/oauth_callback');
            $token_url = "https://graph.facebook.com/v17.0/oauth/access_token?client_id={$app_id}&redirect_uri={$redirect_uri}&client_secret={$app_secret}&code={$code}";
            
            $response = $this->fetch_api($token_url);
            $data = json_decode($response, true);
            if (isset($data['access_token'])) {
                // Exchange for long-lived token
                $long_url = "https://graph.facebook.com/v17.0/oauth/access_token?grant_type=fb_exchange_token&client_id={$app_id}&client_secret={$app_secret}&fb_exchange_token={$data['access_token']}";
                $long_response = $this->fetch_api($long_url);
                $long_data = json_decode($long_response, true);
                $token = isset($long_data['access_token']) ? $long_data['access_token'] : $data['access_token'];
                update_option('meta_leads_access_token', $token);
                set_alert('success', 'Successfully connected with Facebook!');
            } else {
                set_alert('warning', 'Failed to connect. Check App configurations.');
            }
        }
        redirect(admin_url('meta_leads/settings'));
    }

    public function submitted_leads()
    {
        try {
        $status   = $this->input->get('status');
        $platform = $this->input->get('platform');
        $campaign = $this->input->get('campaign');
        $filter_staff = $this->input->get('filter_staff');
        $date_from = $this->input->get('date_from');
        $date_to   = $this->input->get('date_to');

        $data['title'] = _l('meta_leads_submitted');
        $data['current_status']       = $status;
        $data['current_platform']     = $platform;
        $data['current_campaign']     = $campaign;
        $data['current_filter_staff'] = $filter_staff;
        $data['current_date_from']    = $date_from;
        $data['current_date_to']      = $date_to;

        // Load staff safely
        $data['staff'] = [];
        try {
            $staff_rows = $this->db->where('active', 1)->get(db_prefix() . 'staff')->result_array();
            $data['staff'] = $staff_rows ?: [];
        } catch (Exception $e) { $data['staff'] = []; }

        // Only show leads that have a saved mapping (mapping-saved leads only)
        $data['leads'] = $this->meta_leads_model->get_submitted_leads($status, $platform, $campaign, $filter_staff, $date_from, $date_to);

        // Stats — scoped to what the current user can see
        $prefix = db_prefix();
        if (is_admin() || has_permission('meta_leads', '', 'view')) {
            $data['stat_total']      = (int)$this->db->count_all($prefix . 'meta_leads_data');
            $data['stat_pending']    = (int)$this->db->where('status', 'Pending')->count_all_results($prefix . 'meta_leads_data');
            $data['stat_added']      = (int)$this->db->where('status', 'Added')->count_all_results($prefix . 'meta_leads_data');
            $data['stat_unassigned'] = (int)$this->db->where('assigned_staff', 0)->count_all_results($prefix . 'meta_leads_data');
        } else {
            // Staff: count only their own assigned leads
            $uid = (int) get_staff_user_id();
            $data['stat_total']      = $this->meta_leads_model->count_leads_for_staff($uid, [], '');
            $data['stat_pending']    = $this->meta_leads_model->count_leads_for_staff($uid, [], 'Pending');
            $data['stat_added']      = $this->meta_leads_model->count_leads_for_staff($uid, [], 'Added');
            $data['stat_unassigned'] = 0;
        }

        // Pass list of mapped campaigns for campaign filter dropdown
        $all_mapped = $this->meta_leads_model->get_form_mappings();
        if (!is_admin() && !has_permission('meta_leads', '', 'view')) {
            $uid = (int) get_staff_user_id();
            if (is_array($all_mapped)) {
                $data['mapped_campaigns'] = array_filter($all_mapped, function($c) use ($uid) {
                    return (isset($c['assigned_staff']) && (int)$c['assigned_staff'] === $uid);
                });
            } else {
                $data['mapped_campaigns'] = [];
            }
        } else {
            $data['mapped_campaigns'] = is_array($all_mapped) ? $all_mapped : [];
        }

        $this->load->view('submitted_leads', $data);
        } catch (\Throwable $th) {
            log_message('error', 'Meta Leads submitted_leads error: ' . $th->getMessage());
            $data['title'] = 'Submitted Leads';
            $data['leads'] = [];
            $data['staff'] = [];
            $data['mapped_campaigns'] = [];
            $data['stat_total'] = $data['stat_pending'] = $data['stat_added'] = $data['stat_unassigned'] = 0;
            $data['current_status'] = $data['current_platform'] = $data['current_campaign'] = $data['current_filter_staff'] = '';
            $data['current_date_from'] = $data['current_date_to'] = '';
            $this->load->view('submitted_leads', $data);
        }
    }






    
    public function add_to_crm($id)
    {

        if ($this->meta_leads_model->add_to_crm($id)) {
            set_alert('success', _l('lead_added_successfully'));
        } else {
            set_alert('warning', _l('problem_adding_lead'));
        }
        redirect(admin_url('meta_leads/submitted_leads'));
    }

    public function delete_lead($id)
    {

        if ($this->meta_leads_model->delete_lead($id)) {
            set_alert('success', _l('meta_leads_deleted'));
        }
        redirect(admin_url('meta_leads/submitted_leads'));
    }

    /**
     * AJAX: auto-save a note on a lead.
     * POST: id, note
     */
    public function save_lead_note()
    {
        $this->output->set_content_type('application/json');
        $id   = (int)$this->input->post('id');
        $note = $this->input->post('note', false) ?? '';

        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Invalid lead ID']);
            die;
        }

        // Staff can only edit notes on their own leads
        if (!is_admin() && !has_permission('meta_leads', '', 'edit')) {
            $lead = $this->meta_leads_model->get_lead($id);
            if (!$lead || (int)$lead['assigned_staff'] !== (int)get_staff_user_id()) {
                echo json_encode(['success' => false, 'message' => 'Access denied']);
                die;
            }
        }

        $ok = $this->meta_leads_model->save_lead_note($id, $note);
        echo json_encode(['success' => (bool)$ok]);
        die;
    }

    public function bulk_action_process()
    {
        // Disable CSRF for this AJAX endpoint
        $this->output->set_content_type('application/json');
        
        if (!$this->input->post()) {
            echo json_encode(['success' => false, 'message' => 'No data received.']);
            die;
        }

        $action = $this->input->post('action');
        $ids    = $this->input->post('ids');

        if (!$action) {
            echo json_encode(['success' => false, 'message' => 'No action specified.']);
            die;
        }

        if (!$ids || !is_array($ids)) {
            echo json_encode(['success' => false, 'message' => 'No items selected.']);
            die;
        }

        // Sanitize IDs
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids);

        if (empty($ids)) {
            echo json_encode(['success' => false, 'message' => 'No valid items selected.']);
            die;
        }

        if(ob_get_length()) ob_clean();

        $count = 0;
        if ($action == 'mass_add') {
            foreach ($ids as $id) {
                if ($this->meta_leads_model->add_to_crm($id)) $count++;
            }
            echo json_encode(['success' => true, 'message' => $count . ' leads successfully added to CRM.']);
        } elseif ($action == 'mass_delete') {
            foreach ($ids as $id) {
                if ($this->meta_leads_model->delete_lead($id)) $count++;
            }
            echo json_encode(['success' => true, 'message' => $count . ' leads deleted.']);
        } elseif ($action == 'change_status') {
            $status_new = $this->input->post('status_val');
            if (empty($status_new)) {
                echo json_encode(['success' => false, 'message' => 'No status specified.']);
                die;
            }
            foreach ($ids as $id) {
                $this->db->where('id', $id);
                if (!is_admin()) $this->db->where('assigned_staff', get_staff_user_id());
                $this->db->update(db_prefix() . 'meta_leads_data', ['status' => $status_new]);
                $count++;
            }
            echo json_encode(['success' => true, 'message' => 'Status updated for ' . $count . ' leads.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid action: ' . htmlspecialchars($action)]);
        }
        die;
    }

    public function export()
    {

        $ids = $this->input->get('ids');
        $format = $this->input->get('format');

        $this->db->select('*');
        if($ids) $this->db->where_in('id', explode(',', $ids));
        $leads = $this->db->get($this->db->dbprefix . 'meta_leads_data')->result_array();

        if ($format == 'csv' || $format == 'excel') {
            if(ob_get_length()) ob_clean();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=meta_leads_export_' . date('Ymd_Hi') . '.csv');
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Name', 'Email', 'Phone', 'Campaign', 'Date', 'Status']);
            foreach ($leads as $lead) {
                fputcsv($output, [
                    $lead['id'],
                    $lead['name'],
                    $lead['email'],
                    $lead['phone'],
                    $lead['form_name'],
                    $lead['date_added'],
                    $lead['status']
                ]);
            }
            fclose($output);
            die;
        } elseif ($format == 'pdf') {
            if(ob_get_length()) ob_clean();
            require_once(APPPATH . 'third_party/tcpdf/tcpdf.php');
            $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->SetTitle('Meta Leads Export');
            $pdf->SetMargins(10, 10, 10);
            $pdf->AddPage();
            $html = '<h2>Leads Export</h2><table border="1" cellpadding="4"><thead><tr><th><b>ID</b></th><th><b>Name</b></th><th><b>Email</b></th><th><b>Phone</b></th><th><b>Campaign</b></th><th><b>Date</b></th></tr></thead><tbody>';
            foreach ($leads as $lead) {
                $html .= '<tr><td>'.$lead['id'].'</td><td>'.$lead['name'].'</td><td>'.$lead['email'].'</td><td>'.$lead['phone'].'</td><td>'.$lead['form_name'].'</td><td>'.$lead['date_added'].'</td></tr>';
            }
            $html .= '</tbody></table>';
            $pdf->writeHTML($html, true, false, true, false, '');
            $pdf->Output('meta_leads_export_' . date('Ymd_Hi') . '.pdf', 'D');
            die;
        }
    }

    public function save_mapping_ajax() {
        if (!is_admin()) {
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            die;
        }
        $post = $this->input->post();
        if (isset($post['form_id'])) {
            $form_id = is_array($post['form_id']) ? $post['form_id'][0] : $post['form_id'];
            if ($form_id) {
                $selected_page = isset($post['selected_page']) ? (is_array($post['selected_page']) ? $post['selected_page'][0] : $post['selected_page']) : '';
                $page_id = isset($post['page_id']) ? (is_array($post['page_id']) ? $post['page_id'][0] : $post['page_id']) : '';
                $page_name = isset($post['page_name']) ? (is_array($post['page_name']) ? $post['page_name'][0] : $post['page_name']) : '';
                
                if(!empty($selected_page)) {
                    $sp_parts = explode('|||', $selected_page);
                    if(count($sp_parts) == 2) {
                        $page_id = $sp_parts[0];
                        $page_name = $sp_parts[1];
                    }
                }

                $data = [
                    'form_id' => $form_id,
                    'form_name' => isset($post['form_name']) ? (is_array($post['form_name']) ? $post['form_name'][0] : $post['form_name']) : '',
                    'page_id' => $page_id,
                    'page_name' => $page_name,
                    'assigned_staff' => isset($post['assigned_staff']) ? intval(is_array($post['assigned_staff']) ? $post['assigned_staff'][0] : $post['assigned_staff']) : 0,
                    'lead_status' => isset($post['lead_status']) ? intval(is_array($post['lead_status']) ? $post['lead_status'][0] : $post['lead_status']) : 0,
                    'lead_source' => isset($post['lead_source']) ? intval(is_array($post['lead_source']) ? $post['lead_source'][0] : $post['lead_source']) : 0
                ];
                $this->meta_leads_model->save_form_mapping($data);
                // Repair: update all existing leads with this form_id to have correct assigned_staff
                $this->meta_leads_model->repair_assigned_staff();
                // Manual sync is now needed.
                echo json_encode(['success' => true, 'message' => 'Campaign mapping saved. Click the Sync button to load past leads.']);
                die;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Failed to save mapping']);
        die;
    }
    
    public function unsync_ajax() {
        if (!is_admin()) {
            echo json_encode(['success' => false, 'message' => 'Not authorized']);
            die;
        }
        $form_id = $this->input->post('form_id');
        if ($form_id) {
            $this->db->where('name', 'meta_leads_mapping_' . $form_id);
            $this->db->delete(db_prefix() . 'options');
        }
        echo json_encode(['success' => true]);
        die;
    }

    public function lead_settings()
    {
        try {
            // Access restricted by Model query for specific staff
            if ($this->input->post()) {

                $post = $this->input->post();
                
                if (isset($post['form_id'])) {
                
                // Safeguard against older cached browser submissions acting as array
                $form_id = is_array($post['form_id']) ? $post['form_id'][0] : $post['form_id'];
                
                if ($form_id) {
                    $selected_page = isset($post['selected_page']) ? (is_array($post['selected_page']) ? $post['selected_page'][0] : $post['selected_page']) : '';
                    $page_id = isset($post['page_id']) ? (is_array($post['page_id']) ? $post['page_id'][0] : $post['page_id']) : '';
                    $page_name = isset($post['page_name']) ? (is_array($post['page_name']) ? $post['page_name'][0] : $post['page_name']) : '';
                    
                    if(!empty($selected_page)) {
                        $sp_parts = explode('|||', $selected_page);
                        if(count($sp_parts) == 2) {
                            $page_id = $sp_parts[0];
                            $page_name = $sp_parts[1];
                        }
                    }

                    $data = [
                        'form_id' => $form_id,
                        'form_name' => isset($post['form_name']) ? (is_array($post['form_name']) ? $post['form_name'][0] : $post['form_name']) : '',
                        'page_id' => $page_id,
                        'page_name' => $page_name,
                        'assigned_staff' => isset($post['assigned_staff']) ? intval(is_array($post['assigned_staff']) ? $post['assigned_staff'][0] : $post['assigned_staff']) : 0,
                        'lead_status' => isset($post['lead_status']) ? intval(is_array($post['lead_status']) ? $post['lead_status'][0] : $post['lead_status']) : 0,
                        'lead_source' => isset($post['lead_source']) ? intval(is_array($post['lead_source']) ? $post['lead_source'][0] : $post['lead_source']) : 0
                    ];
                    @$this->meta_leads_model->save_form_mapping($data);
                    // Repair: assign staff to all existing leads with this form_id
                    try { $this->meta_leads_model->repair_assigned_staff(); } catch(Exception $e) {}
                }
                
                set_alert('success', 'Campaign mapping saved successfully. Use the Sync button on the mapping row to pull historical leads.');
                redirect(admin_url('meta_leads/lead_settings'));
            }
        }

        $data['title'] = _l('meta_leads_lead_settings');
        
        // Load statuses and sources safely
        $data['statuses'] = [];
        $data['sources'] = [];
        try {
            $this->load->model('leads_model');
            $data['statuses'] = $this->leads_model->get_status() ?: [];
            $data['sources'] = $this->leads_model->get_source() ?: [];
        } catch (Exception $e) { }
        
        // Load staff safely via direct DB
        $data['staff'] = [];
        try {
            $staff_rows = $this->db->where('active', 1)->get(db_prefix() . 'staff')->result_array();
            $data['staff'] = $staff_rows ?: [];
        } catch (Exception $e) { $data['staff'] = []; }

        $forms = [];
        $fetched_ids = [];
        $token = get_option('meta_leads_access_token');
        
        // FIX: Read filters from GET only (form method changed to GET in view)
        $filter_staff    = $this->input->get('filter_staff');
        $filter_campaign = $this->input->get('filter_campaign');
        $filter_status   = $this->input->get('filter_status');
        $filter_source   = $this->input->get('filter_source');
        $sort_by         = $this->input->get('sort_by');
        if (!$sort_by) $sort_by = 'high_to_low';

        $data['current_filter_staff']    = $filter_staff;
        $data['current_filter_campaign'] = $filter_campaign;
        $data['current_filter_status']   = $filter_status;
        $data['current_filter_source']   = $filter_source;
        $data['current_sort_by']         = $sort_by;
        
        $fb_pages = [];
        if ($token) {
            $pages_response = $this->fetch_api("https://graph.facebook.com/v17.0/me/accounts?access_token={$token}");
            $pages_data = json_decode($pages_response, true);
            
            if (isset($pages_data['data'])) {
                $fb_pages = $pages_data['data'];
                $data['fb_pages'] = $fb_pages;
                foreach ($pages_data['data'] as $page) {
                    $page_id = $page['id'];
                    $page_token = $page['access_token'];
                    
                    // fetch forms for this page
                    $forms_response = $this->fetch_api("https://graph.facebook.com/v17.0/{$page_id}/leadgen_forms?fields=name,id,leads_count&access_token={$page_token}");
                    $forms_data = json_decode($forms_response, true);
                    
                    if (isset($forms_data['data'])) {
                        foreach ($forms_data['data'] as $fb_form) {
                            $saved_map = $this->meta_leads_model->get_form_mapping($fb_form['id']);
                            // Count how many leads are already synced in DB for this form
                            $synced_c = $this->db->where('form_id', $fb_form['id'])->count_all_results(db_prefix() . 'meta_leads_data');
                            $forms[] = [
                                'page_id'        => $page_id,
                                'page_name'      => $page['name'],
                                'form_id'        => $fb_form['id'],
                                'form_name'      => $fb_form['name'],
                                'leads_count'    => $fb_form['leads_count'] ?? 0,
                                'synced_count'   => $synced_c,
                                'assigned_staff' => $saved_map['assigned_staff'] ?? 0,
                                'lead_status'    => $saved_map['lead_status'] ?? 0,
                                'lead_source'    => $saved_map['lead_source'] ?? 0,
                                'is_mapped'      => !empty($saved_map) ? 1 : 0
                            ];
                            $fetched_ids[] = $fb_form['id'];
                        }
                    }
                }
            }
        }
        
        // Fetch manually mapped or disconnected forms from database (using new options fallback)
        $db_forms = [];
        $this->db->like('name', 'meta_leads_mapping_');
        $q_opt = $this->db->get(db_prefix() . 'options');
        $options = $q_opt ? $q_opt->result_array() : [];
        foreach($options as $opt) {
            $m = @json_decode($opt['value'], true);
            if (is_array($m)) {
                $db_forms[] = $m;
            }
        }
        // Also fetch from old table to not lose data
        $old_db_forms = [];
        if ($this->db->table_exists(db_prefix() . 'meta_lead_settings')) {
            $this->db->db_debug = false;
            $q_old = @$this->db->get(db_prefix() . 'meta_lead_settings');
            $old_db_forms = $q_old ? $q_old->result_array() : [];
            if(is_array($old_db_forms)) {
                foreach($old_db_forms as $old_f) {
                    $db_forms[] = $old_f;
                }
            }
        }
        
        foreach($db_forms as $db_f) {
            if(isset($db_f['form_id']) && !in_array($db_f['form_id'], $fetched_ids)) {
                $synced_c = $this->db->where('form_id', $db_f['form_id'])->count_all_results(db_prefix() . 'meta_leads_data');
                $forms[] = [
                    'page_id'        => $db_f['page_id'] ?? '',
                    'page_name'      => $db_f['page_name'] ?? '',
                    'form_id'        => $db_f['form_id'] ?? '',
                    'form_name'      => $db_f['form_name'] ?? '',
                    'leads_count'    => '-',
                    'synced_count'   => $synced_c,
                    'assigned_staff' => $db_f['assigned_staff'] ?? 0,
                    'lead_status'    => $db_f['lead_status'] ?? 0,
                    'lead_source'    => $db_f['lead_source'] ?? 0,
                    'is_mapped'      => 1
                ];
                $fetched_ids[] = $db_f['form_id'];
            }
        }
        
        // For staff: only show campaigns assigned to them
        // For admin: show all campaigns, apply optional filters
        if (!is_admin() && !has_permission('meta_leads', '', 'view')) {
            $user_id = get_staff_user_id();
            // Staff see any form that is assigned to them (regardless of lead_status/source)
            $forms = array_filter($forms, function($f) use ($user_id) {
                return isset($f['assigned_staff']) && (int)$f['assigned_staff'] === (int)$user_id;
            });
        } else {
            // Admin: apply filter by staff if set
            if ($filter_staff) {
                $forms = array_filter($forms, function($f) use ($filter_staff) {
                    return ($f['assigned_staff'] ?? 0) == $filter_staff;
                });
            }
        }

        // Store ALL visible forms (after staff filter) as campaign dropdown source
        $data['all_visible_forms'] = array_values($forms);

        // Filter by campaign (form_id)
        if (!empty($filter_campaign)) {
            $forms = array_filter($forms, function($f) use ($filter_campaign) {
                return isset($f['form_id']) && $f['form_id'] == $filter_campaign;
            });
        }
        
        // Filter by Status, Source for both Admin and Staff
        if ($filter_status) {
            $forms = array_filter($forms, function($f) use ($filter_status) {
                return (isset($f['lead_status']) && (int)$f['lead_status'] === (int)$filter_status);
            });
        }
        
        if ($filter_source) {
            $forms = array_filter($forms, function($f) use ($filter_source) {
                return (isset($f['lead_source']) && (int)$f['lead_source'] === (int)$filter_source);
            });
        }

        $data['current_filter_name'] = '';
        
        $forms = array_values($forms); // re-index
        
        // Sorting logic
        if ($sort_by == 'low_to_high') {
            usort($forms, function($a, $b) {
                $a_val = is_numeric($a['leads_count']) ? $a['leads_count'] : 0;
                $b_val = is_numeric($b['leads_count']) ? $b['leads_count'] : 0;
                return $a_val - $b_val;
            });
        } elseif ($sort_by == 'mapped') {
            usort($forms, function($a, $b) {
                return (($b['assigned_staff'] ?? 0) > 0 ? 1 : 0) - (($a['assigned_staff'] ?? 0) > 0 ? 1 : 0);
            });
        } elseif ($sort_by == 'unmapped') {
            usort($forms, function($a, $b) {
                return (($a['assigned_staff'] ?? 0) == 0 ? 1 : 0) - (($b['assigned_staff'] ?? 0) == 0 ? 1 : 0);
            });
        } else {
            // default high_to_low
            usort($forms, function($a, $b) {
                $a_val = is_numeric($a['leads_count']) ? $a['leads_count'] : 0;
                $b_val = is_numeric($b['leads_count']) ? $b['leads_count'] : 0;
                return $b_val - $a_val;
            });
        }
        
        $data['api_forms'] = $forms;
        $this->load->view('lead_settings', $data);
        } catch (\Throwable $th) {
            log_message('error', 'Meta Leads lead_settings error: ' . $th->getMessage() . ' in ' . $th->getFile() . ':' . $th->getLine());
            $data['title'] = _l('meta_leads_lead_settings');
            $data['api_forms'] = [];
            $data['all_visible_forms'] = [];
            $data['staff'] = [];
            $data['statuses'] = [];
            $data['sources'] = [];
            $data['current_filter_staff'] = $data['current_filter_campaign'] = '';
            $data['current_filter_status'] = $data['current_filter_source'] = $data['current_sort_by'] = '';
            $data['current_filter_name'] = '';
            $this->load->view('lead_settings', $data);
        }
    }

    public function sync_history()
    {
        try {
        $data['title'] = _l('meta_leads_sync_history');
        
        $status    = $this->input->get('status');
        $date_from = $this->input->get('date_from');
        $date_to   = $this->input->get('date_to');
        
        $data['current_status']    = $status;
        $data['current_date_from'] = $date_from;
        $data['current_date_to']   = $date_to;
        $data['history']           = [];

        $historyTableExists = false;
        $leadsTableExists   = false;
        try {
            $this->db->db_debug = false;
            $historyTableExists = $this->db->table_exists(db_prefix() . 'meta_leads_sync_history');
            $leadsTableExists   = $this->db->table_exists(db_prefix() . 'meta_leads_data');
        } catch (Exception $e) {}

        if ($historyTableExists) {
            $prefix = db_prefix();
            $this->db->db_debug = false;

            // Always use LEFT JOIN so logs appear even if the lead record was deleted
            $select_cols = $prefix . 'meta_leads_sync_history.id, '
                . $prefix . 'meta_leads_sync_history.date_added, '
                . $prefix . 'meta_leads_sync_history.meta_lead_id, '
                . $prefix . 'meta_leads_sync_history.status, '
                . $prefix . 'meta_leads_sync_history.message';

            if ($leadsTableExists) {
                $select_cols .= ', '
                    . $prefix . 'meta_leads_data.page_id, '
                    . $prefix . 'meta_leads_data.form_id, '
                    . $prefix . 'meta_leads_data.form_name, '
                    . $prefix . 'meta_leads_data.page_name, '
                    . $prefix . 'meta_leads_data.assigned_staff';
            }

            $this->db->select($select_cols, false);
            $this->db->from($prefix . 'meta_leads_sync_history');

            if ($leadsTableExists) {
                $this->db->join(
                    $prefix . 'meta_leads_data',
                    $prefix . 'meta_leads_sync_history.meta_lead_id = ' . $prefix . 'meta_leads_data.meta_lead_id',
                    'left'
                );
            }

            if ($status) {
                $this->db->where($prefix . 'meta_leads_sync_history.status', $status);
            }
            if ($date_from) {
                $this->db->where($prefix . 'meta_leads_sync_history.date_added >=', $date_from . ' 00:00:00');
            }
            if ($date_to) {
                $this->db->where($prefix . 'meta_leads_sync_history.date_added <=', $date_to . ' 23:59:59');
            }

            // Staff: only show logs for leads assigned to them (only when leads table exists)
            if ($leadsTableExists && !is_admin() && !has_permission('meta_leads', '', 'view')) {
                $uid = (int) get_staff_user_id();
                $this->db->where($prefix . 'meta_leads_data.assigned_staff', $uid);
            }

            $this->db->order_by($prefix . 'meta_leads_sync_history.date_added', 'DESC');
            $this->db->limit(1000);
            $q = $this->db->get();
            $data['history'] = ($q && !$this->db->error()['code']) ? $q->result_array() : [];
        }

        $data['total_syncs']     = 0;
        $data['failed_attempts'] = 0;
        $data['duplicate_hits']  = 0;

        if ($historyTableExists) {
            $prefix = db_prefix();
            $is_staff_restricted = (!is_admin() && !has_permission('meta_leads', '', 'view'));
            $uid = $is_staff_restricted ? (int) get_staff_user_id() : 0;

            $self = $this;
            $get_count = function($status_val) use ($prefix, $is_staff_restricted, $uid, $self, $leadsTableExists) {
                try {
                    if ($is_staff_restricted && $leadsTableExists) {
                        $self->db->select($prefix . 'meta_leads_sync_history.id', false);
                        $self->db->from($prefix . 'meta_leads_sync_history');
                        $self->db->join($prefix . 'meta_leads_data', $prefix . 'meta_leads_sync_history.meta_lead_id = ' . $prefix . 'meta_leads_data.meta_lead_id', 'left');
                        $self->db->where($prefix . 'meta_leads_sync_history.status', $status_val);
                        $self->db->where($prefix . 'meta_leads_data.assigned_staff', $uid);
                        $q = $self->db->get();
                        return $q ? $q->num_rows() : 0;
                    } else {
                        $self->db->where('status', $status_val);
                        return (int)$self->db->count_all_results($prefix . 'meta_leads_sync_history');
                    }
                } catch (Exception $e) { return 0; }
            };

            $data['total_syncs']     = $get_count('Success');
            $data['failed_attempts'] = $get_count('Failure');
            $data['duplicate_hits']  = $get_count('Duplicate');
        }

        $this->load->view('sync_history', $data);
        } catch (\Throwable $th) {
            log_message('error', 'Meta Leads sync_history error: ' . $th->getMessage() . ' in ' . $th->getFile() . ':' . $th->getLine());
            $data['title'] = _l('meta_leads_sync_history');
            $data['history'] = [];
            $data['total_syncs'] = $data['failed_attempts'] = $data['duplicate_hits'] = 0;
            $data['current_status'] = $data['current_date_from'] = $data['current_date_to'] = '';
            $this->load->view('sync_history', $data);
        }
    }

    /**
     * Clear all sync log records (admin only).
     */
    public function clear_sync_logs()
    {
        if (!is_admin()) {
            set_alert('danger', 'Only administrators can clear sync logs.');
            redirect(admin_url('meta_leads/sync_history'));
            return;
        }
        if ($this->db->table_exists(db_prefix() . 'meta_leads_sync_history')) {
            $this->db->truncate(db_prefix() . 'meta_leads_sync_history');
        }
        set_alert('success', 'Sync logs cleared successfully.');
        redirect(admin_url('meta_leads/sync_history'));
    }

    public function sync_past_leads($form_id, $page_id)
    {
        // Increase PHP execution time for large syncs
        @set_time_limit(300);
        @ini_set('memory_limit', '256M');

        // Validate form_id and page_id
        $form_id = preg_replace('/[^0-9]/', '', (string)$form_id);
        $page_id = preg_replace('/[^0-9]/', '', (string)$page_id);

        if (empty($form_id) || empty($page_id)) {
            set_alert('danger', 'Invalid form or page ID provided.');
            redirect(admin_url('meta_leads/lead_settings'));
            return;
        }

        // Staff can sync their assigned campaigns; validate they actually have access
        if (!is_admin()) {
            $uid = get_staff_user_id();
            $mapping = $this->meta_leads_model->get_form_mapping($form_id);
            if (!$mapping || ($mapping['assigned_staff'] ?? 0) != $uid) {
                set_alert('danger', 'You do not have permission to sync this campaign.');
                redirect(admin_url('meta_leads/lead_settings'));
                return;
            }
        }

        $user_token = get_option('meta_leads_access_token');
        if (empty($user_token)) {
            set_alert('danger', 'Facebook not connected. Please connect your Facebook account in Settings first.');
            redirect(admin_url('meta_leads/lead_settings'));
            return;
        }

        try {
            $page_token_url = "https://graph.facebook.com/v17.0/{$page_id}?fields=access_token&access_token={$user_token}";
            $page_response  = $this->fetch_api($page_token_url);
            $page_data      = $page_response ? @json_decode($page_response, true) : [];

            if (!is_array($page_data)) {
                set_alert('danger', 'Facebook API returned an invalid response. Please try again or re-connect Facebook.');
                redirect(admin_url('meta_leads/lead_settings'));
                return;
            }

            if (!empty($page_data['error'])) {
                $err_msg = $page_data['error']['message'] ?? 'Unknown Facebook API error';
                set_alert('danger', 'Facebook API Error: ' . htmlspecialchars($err_msg) . '. Please re-connect your Facebook account.');
                redirect(admin_url('meta_leads/lead_settings'));
                return;
            }

            if (!isset($page_data['access_token']) || empty($page_data['access_token'])) {
                set_alert('danger', 'Could not retrieve Page Access Token. Ensure your Facebook App has the required permissions (pages_show_list, leads_retrieval) and re-connect.');
                redirect(admin_url('meta_leads/lead_settings'));
                return;
            }

            $page_token = $page_data['access_token'];
            $all_leads  = $this->fetch_all_form_leads_paginated($form_id, $page_token);

            if (!empty($all_leads)) {
                $count = 0;
                foreach ($all_leads as $lead) {
                    if ($this->process_single_lead_from_api($lead, $form_id, $page_id)) {
                        $count++;
                    }
                }
                set_alert('success', $count . ' new past leads synced successfully! (' . count($all_leads) . ' total fetched from Facebook).');
            } else {
                set_alert('info', 'No new leads found for this campaign. All leads may already be synced, or Facebook returned an empty set.');
            }
        } catch (\Throwable $th) {
            log_message('error', 'Meta Leads sync_past_leads error: ' . $th->getMessage() . ' in ' . $th->getFile() . ':' . $th->getLine());
            set_alert('danger', 'Sync encountered an error: ' . htmlspecialchars($th->getMessage()) . '. Check server error logs for details.');
        }

        redirect(admin_url('meta_leads/lead_settings'));
    }
    
    /**
     * Fetch ALL leads from a Facebook form using cursor-based pagination.
     * Returns a flat array of lead objects.
     */
    private function fetch_all_form_leads_paginated($form_id, $page_token)
    {
        $all_leads = [];
        $url = "https://graph.facebook.com/v17.0/{$form_id}/leads?limit=100&access_token={$page_token}";
        $safety_limit = 100; // max 100 pages = 10,000 leads per form
        $page_count   = 0;
        while ($url && $page_count < $safety_limit) {
            $response = $this->fetch_api($url);
            if (!$response) break;
            $data = json_decode($response, true);
            if (!isset($data['data']) || empty($data['data'])) break;
            foreach ($data['data'] as $lead) {
                $all_leads[] = $lead;
            }
            // Facebook pagination cursor
            $url = isset($data['paging']['next']) ? $data['paging']['next'] : null;
            $page_count++;
        }
        return $all_leads;
    }

    private function process_single_lead_from_api($lead_data, $form_id, $page_id) 
    {
        $lead_id = $lead_data['id'];
        $created_time = strtotime($lead_data['created_time']);
        $platform = 'Facebook';
        if(isset($lead_data['platform']) && $lead_data['platform'] == 'ig') {
            $platform = 'Instagram';
        }

        $name = '-';
        $email = '-';
        $phone = '-';
        $company = '-';
        $city = '-';
        
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
                    $custom_fields[$field['name']] = $val;
                }
            }
            if ($name == '-' && (!empty($f_name) || !empty($l_name))) {
                $name = trim($f_name . ' ' . $l_name);
                if(empty($name)) $name = '-';
            }
        }
        
        $this->db->where('meta_lead_id', $lead_id);
        if ($this->db->get(db_prefix() . 'meta_leads_data')->row()) {
            $this->meta_leads_model->log_sync($lead_id, 'Duplicate', 'Duplicate lead ignored. ID: ' . $lead_id . ' (Manual Sync)');
            return false; // Duplicate ignored
        }

        $map = $this->meta_leads_model->get_form_mapping($form_id);
        
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
            'form_name' => $map ? ($map['form_name'] ?? ('Form ' . $form_id)) : ('Form ' . $form_id),
            'page_name' => $map ? ($map['page_name'] ?? '') : '',
            'date_added' => date('Y-m-d H:i:s', $created_time),
            'status' => 'Pending',
            'assigned_staff' => $map['assigned_staff'] ?? 0,
            'lead_status' => $map['lead_status'] ?? 0,
            'lead_source' => $map['lead_source'] ?? 0,
        ];
        
        $this->db->insert(db_prefix() . 'meta_leads_data', $insert);
        $insert_id = $this->db->insert_id();
        $log_message = "Successfully synced lead data fields.\nName: {$name}\nEmail: {$email}\nPhone: {$phone}\nCompany: {$company}\nCity: {$city}";
        $this->meta_leads_model->log_sync($lead_id, 'Success (Manual Sync)', $log_message);
        
        // NOTE: Leads are stored in Meta Leads module only.
        // Use "Add to CRM" button or Bulk Manage > Add to CRM to push to Perfex CRM leads.
        
        return true;
    }

    /**
     * Reset Module - clears all leads, mappings, sync logs, and module options.
     * Admin only.
     */
    public function reset_module()
    {
        if (!is_admin()) {
            access_denied('Meta Sync');
        }

        if ($this->input->post('confirm_reset') == '1') {
            // Delete all submitted leads
            if ($this->db->table_exists(db_prefix() . 'meta_leads_data')) {
                $this->db->truncate(db_prefix() . 'meta_leads_data');
            }
            // Delete all sync history logs
            if ($this->db->table_exists(db_prefix() . 'meta_leads_sync_history')) {
                $this->db->truncate(db_prefix() . 'meta_leads_sync_history');
            }
            // Delete all form mappings from legacy table
            if ($this->db->table_exists(db_prefix() . 'meta_lead_settings')) {
                $this->db->truncate(db_prefix() . 'meta_lead_settings');
            }
            // Delete all mapping options from options table
            $this->db->like('name', 'meta_leads_mapping_');
            $this->db->delete(db_prefix() . 'options');
            // Delete module settings (access token, app id, app secret, verify token)
            $keys = ['meta_leads_app_id', 'meta_leads_app_secret', 'meta_leads_access_token', 'meta_leads_verify_token'];
            foreach ($keys as $key) {
                $this->db->where('name', $key);
                $this->db->delete(db_prefix() . 'options');
            }

            set_alert('success', 'Module has been fully reset. All leads, mappings, sync logs, and settings have been deleted.');
            redirect(admin_url('meta_leads/settings'));
        }

        redirect(admin_url('meta_leads/settings'));
    }

    /**
     * Get lead details for the popup.
     */
    public function get_lead_ajax($id)
    {
        // Access restricted by Model call
        $lead = $this->meta_leads_model->get_lead($id);
        if ($lead) {
            echo json_encode($lead);
        } else {
            echo json_encode(['error' => 'Lead not found']);
        }
        die;
    }

    public function sync_all_forms()
    {
        if (!is_admin()) {
            access_denied('Meta Sync');
        }
        $user_token = get_option('meta_leads_access_token');
        if (!$user_token) {
            set_alert('danger', 'Facebook not connected.');
            redirect(admin_url('meta_leads/lead_settings'));
            return;
        }

        $pages_response = $this->fetch_api("https://graph.facebook.com/v17.0/me/accounts?access_token={$user_token}");
        $pages_data = json_decode($pages_response, true);

        $total = 0;
        if (isset($pages_data['data'])) {
            foreach ($pages_data['data'] as $page) {
                $page_id    = $page['id'];
                $page_token = $page['access_token'];
                $forms_response = $this->fetch_api("https://graph.facebook.com/v17.0/{$page_id}/leadgen_forms?fields=name,id,leads_count&access_token={$page_token}");
                $forms_data = json_decode($forms_response, true);
                if (isset($forms_data['data'])) {
                    foreach ($forms_data['data'] as $fb_form) {
                        $form_id = $fb_form['id'];
                        $all_leads = $this->fetch_all_form_leads_paginated($form_id, $page_token);
                        foreach ($all_leads as $lead) {
                            if ($this->process_single_lead_from_api($lead, $form_id, $page_id)) {
                                $total++;
                            }
                        }
                    }
                }
            }
        }

        set_alert('success', $total . ' new past leads synced from all campaign forms!');
        redirect(admin_url('meta_leads/lead_settings'));
    }

    /**
     * Admin: repair assigned_staff column for all leads based on current mappings.
     * Accessible via: /admin/meta_leads/repair_staff_leads
     */
    public function repair_staff_leads()
    {
        if (!is_admin()) {
            echo json_encode(['success' => false, 'message' => 'Admin only']);
            die;
        }
        try {
            $this->meta_leads_model->repair_assigned_staff();
            set_alert('success', 'Lead staff assignments have been repaired based on current campaign mappings.');
        } catch (Exception $e) {
            set_alert('danger', 'Repair failed: ' . $e->getMessage());
        }
        redirect(admin_url('meta_leads/submitted_leads'));
    }

}


