<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Meta_leads_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->ensure_db_structure();
    }

    private function ensure_db_structure()
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;

        if (@$this->db->table_exists(db_prefix() . 'meta_lead_settings')) {
            $cols = ['page_id'=>'varchar(100)','page_name'=>'varchar(255)','assigned_staff'=>'int(11)','lead_status'=>'int(11)','lead_source'=>'int(11)'];
            foreach ($cols as $col => $type) {
                if (!@$this->db->field_exists($col, db_prefix() . 'meta_lead_settings')) {
                    $default = strpos($type,'int')!==false ? ' DEFAULT 0' : ' DEFAULT NULL';
                    @$this->db->query('ALTER TABLE `' . db_prefix() . 'meta_lead_settings` ADD `' . $col . '` ' . $type . $default);
                }
            }
        }
        if (@$this->db->table_exists(db_prefix() . 'meta_leads_data')) {
            $cols = ['page_id'=>'varchar(100)','raw_data'=>'TEXT','platform'=>'varchar(50)','assigned_staff'=>'int(11)','lead_status'=>'int(11)','lead_source'=>'int(11)','note'=>'TEXT'];
            foreach ($cols as $col => $type) {
                if (!@$this->db->field_exists($col, db_prefix() . 'meta_leads_data')) {
                    $default = strpos($type,'int')!==false ? ' DEFAULT 0' : ' DEFAULT NULL';
                    @$this->db->query('ALTER TABLE `' . db_prefix() . 'meta_leads_data` ADD `' . $col . '` ' . $type . $default);
                }
            }
        }

        $this->db->db_debug = $debug;
    }

    /**
     * One-time repair: updates assigned_staff in meta_leads_data based on current form mappings.
     * Fixes existing leads that were synced before assigned_staff was being populated correctly.
     * Call this after saving a mapping or on admin dashboard load.
     */
    public function repair_assigned_staff()
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        try {
            // Read all mappings from options table
            $this->db->like('name', 'meta_leads_mapping_');
            $opts = $this->db->get(db_prefix() . 'options')->result_array();
            foreach ($opts as $opt) {
                $m = @json_decode($opt['value'], true);
                if (!is_array($m) || empty($m['form_id'])) continue;
                $form_id        = $m['form_id'];
                $assigned_staff = isset($m['assigned_staff']) ? (int)$m['assigned_staff'] : 0;
                $lead_status    = isset($m['lead_status'])    ? (int)$m['lead_status']    : 0;
                $lead_source    = isset($m['lead_source'])    ? (int)$m['lead_source']    : 0;
                // Update all leads with this form_id that still have assigned_staff = 0
                $this->db->where('form_id', $form_id);
                $this->db->where('assigned_staff', 0);
                $this->db->update(db_prefix() . 'meta_leads_data', [
                    'assigned_staff' => $assigned_staff,
                    'lead_status'    => $lead_status,
                    'lead_source'    => $lead_source,
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'repair_assigned_staff error: ' . $e->getMessage());
        }
        $this->db->db_debug = $debug;
    }

    /**
     * Save or update a note on a lead.
     */
    public function save_lead_note($lead_id, $note)
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        $this->db->where('id', (int)$lead_id);
        $result = $this->db->update(db_prefix() . 'meta_leads_data', ['note' => $note]);
        $this->db->db_debug = $debug;
        return $result;
    }

    public function get_submitted_leads($status = '', $platform = '', $campaign = '', $filter_staff = '', $date_from = '', $date_to = '')
    {
        $prefix = db_prefix();
        $this->db->select($prefix . 'meta_leads_data.*');
        $this->db->from($prefix . 'meta_leads_data');

        if (!is_admin() && !has_permission('meta_leads', '', 'view')) {
            // Staff: show only leads assigned to them (by assigned_staff column)
            $uid = (int) get_staff_user_id();
            $this->db->where($prefix . 'meta_leads_data.assigned_staff', $uid);
        } elseif (!empty($filter_staff)) {
            // Admin filtering by specific staff
            $this->db->where($prefix . 'meta_leads_data.assigned_staff', (int)$filter_staff);
        }

        if (!empty($status)) {
            $this->db->where($prefix . 'meta_leads_data.status', $status);
        }
        if (!empty($platform)) {
            $this->db->where($prefix . 'meta_leads_data.platform', $platform);
        }
        if (!empty($campaign)) {
            $this->db->where($prefix . 'meta_leads_data.form_id', $campaign);
        }
        if (!empty($date_from)) {
            $this->db->where($prefix . 'meta_leads_data.date_added >=', $date_from . ' 00:00:00');
        }
        if (!empty($date_to)) {
            $this->db->where($prefix . 'meta_leads_data.date_added <=', $date_to . ' 23:59:59');
        }

        $this->db->order_by($prefix . 'meta_leads_data.date_added', 'DESC');
        return $this->db->get()->result_array();
    }

    /**
     * Helper to count leads for staff - uses assigned_staff column directly
     */
    public function count_leads_for_staff($staff_id, $staff_form_ids = [], $status = '')
    {
        $prefix = db_prefix();
        // Use assigned_staff column directly — most reliable approach
        $this->db->where('assigned_staff', (int)$staff_id);
        if ($status) {
            $this->db->where('status', $status);
        }
        return $this->db->count_all_results($prefix . 'meta_leads_data');
    }

    /**
     * Get form_ids from saved mappings assigned to a specific staff member.
     * Safe version: disables db_debug, uses @ suppression on legacy table.
     */
    public function get_staff_form_ids_public($staff_id)
    {
        $staff_id = (int) $staff_id;
        $ids = [];

        // 1. Check options table (primary mapping storage — always exists in Perfex)
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        try {
            $this->db->like('name', 'meta_leads_mapping_');
            $q = $this->db->get(db_prefix() . 'options');
            $opts = $q ? $q->result_array() : [];
            foreach ($opts as $opt) {
                $m = @json_decode($opt['value'], true);
                if (is_array($m) && !empty($m['form_id'])) {
                    $assign = isset($m['assigned_staff']) ? (is_array($m['assigned_staff']) ? $m['assigned_staff'][0] : $m['assigned_staff']) : 0;
                    if ((int)$assign === $staff_id) {
                        $ids[] = $m['form_id'];
                    }
                }
            }
        } catch (Exception $e) {}

        // 2. Also check legacy meta_lead_settings table (may not exist)
        try {
            if (@$this->db->table_exists(db_prefix() . 'meta_lead_settings')) {
                $this->db->where('assigned_staff', $staff_id);
                $q2 = $this->db->get(db_prefix() . 'meta_lead_settings');
                $old = $q2 ? $q2->result_array() : [];
                if (is_array($old)) {
                    foreach ($old as $r) {
                        if (!empty($r['form_id']) && !in_array($r['form_id'], $ids)) {
                            $ids[] = $r['form_id'];
                        }
                    }
                }
            }
        } catch (Exception $e) {}

        $this->db->db_debug = $debug;
        return $ids;
    }

    /**
     * Get all saved mapping form_ids (admin use, for filter lists).
     */
    public function get_all_mapped_form_ids()
    {
        $ids = [];
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;
        try {
            $this->db->like('name', 'meta_leads_mapping_');
            $opts = $this->db->get(db_prefix() . 'options')->result_array();
            foreach ($opts as $opt) {
                $m = @json_decode($opt['value'], true);
                if (is_array($m) && !empty($m['form_id'])) {
                    $ids[] = $m['form_id'];
                }
            }
        } catch (Exception $e) {}
        try {
            if (@$this->db->table_exists(db_prefix() . 'meta_lead_settings')) {
                $q3 = $this->db->get(db_prefix() . 'meta_lead_settings');
                $old = $q3 ? $q3->result_array() : [];
                if (is_array($old)) {
                    foreach ($old as $r) {
                        if (!empty($r['form_id']) && !in_array($r['form_id'], $ids)) {
                            $ids[] = $r['form_id'];
                        }
                    }
                }
            }
        } catch (Exception $e) {}
        $this->db->db_debug = $debug;
        return $ids;
    }

    public function get_lead($id)
    {
        $this->db->where('id', $id);
        if (!is_admin()) {
            $this->db->where('assigned_staff', get_staff_user_id());
        }
        return $this->db->get(db_prefix() . 'meta_leads_data')->row();
    }

    public function add_to_crm($id)
    {
        $meta_lead = $this->get_lead($id);
        if (!$meta_lead || $meta_lead->status == 'Added') {
            return false;
        }

        $description = 'Imported from Meta Lead Ads. Platform: ' . $meta_lead->platform . '. Form: ' . $meta_lead->form_name . "\n\nRaw Sync Data:\nCompany: " . $meta_lead->company . "\nCity: " . $meta_lead->city . "\nPhone: " . $meta_lead->phone . "\n";
        if (isset($meta_lead->raw_data) && !empty($meta_lead->raw_data)) {
            $custom = json_decode($meta_lead->raw_data, true);
            if (is_array($custom) && count($custom) > 0) {
                $description .= "\n--- Custom Form Questions & Data ---\n";
                foreach ($custom as $q => $a) {
                    $description .= ucfirst(str_replace('_', ' ', $q)) . ': ' . $a . "\n";
                }
            }
        }

        $this->load->model('leads_model');
        $lead_data = [
            'name'        => $meta_lead->name,
            'email'       => $meta_lead->email,
            'phonenumber' => $meta_lead->phone,
            'company'     => $meta_lead->company,
            'city'        => $meta_lead->city,
            'source'      => (!empty($meta_lead->lead_source) && $meta_lead->lead_source > 0) ? $meta_lead->lead_source : 1,
            'status'      => (!empty($meta_lead->lead_status) && $meta_lead->lead_status > 0) ? $meta_lead->lead_status : 1,
            'assigned'    => $meta_lead->assigned_staff ?? 0,
            'description' => $description,
        ];

        $lead_id = $this->leads_model->add($lead_data);
        if ($lead_id) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'meta_leads_data', ['status' => 'Added']);
            return true;
        }
        return false;
    }

    public function delete_lead($id)
    {
        $this->db->where('id', $id);
        if (!is_admin()) {
            $this->db->where('assigned_staff', get_staff_user_id());
        }
        return $this->db->delete(db_prefix() . 'meta_leads_data');
    }

    public function log_sync($meta_lead_id, $status, $message)
    {
        $this->db->insert(db_prefix() . 'meta_leads_sync_history', [
            'date_added'   => date('Y-m-d H:i:s'),
            'meta_lead_id' => $meta_lead_id,
            'status'       => $status,
            'message'      => $message
        ]);
    }

    public function get_form_mapping($form_id)
    {
        $debug = $this->db->db_debug;
        $this->db->db_debug = false;

        // Primary: options table
        $this->db->where('name', 'meta_leads_mapping_' . $form_id);
        $opt = $this->db->get(db_prefix() . 'options')->row();
        if ($opt && !empty($opt->value)) {
            $decoded = @json_decode($opt->value, true);
            if (is_array($decoded)) {
                $this->db->db_debug = $debug;
                return $decoded;
            }
        }

        // Fallback: legacy table
        $result = null;
        try {
            if (@$this->db->table_exists(db_prefix() . 'meta_lead_settings')) {
                $this->db->where('form_id', $form_id);
                $result = $this->db->get(db_prefix() . 'meta_lead_settings')->row_array();
            }
        } catch (Exception $e) {}

        $this->db->db_debug = $debug;
        return $result;
    }

    public function save_form_mapping($data)
    {
        if (isset($data['form_id'])) {
            $opt_name = 'meta_leads_mapping_' . $data['form_id'];
            $this->db->where('name', $opt_name);
            if ($this->db->get(db_prefix() . 'options')->row()) {
                $this->db->where('name', $opt_name);
                $this->db->update(db_prefix() . 'options', ['value' => json_encode($data)]);
            } else {
                $this->db->insert(db_prefix() . 'options', ['name' => $opt_name, 'value' => json_encode($data)]);
            }
        }
        return true;
    }

    /**
     * Get all mapped campaigns (plural)
     */
    public function get_form_mappings()
    {
        $mappings = [];
        $this->db->like('name', 'meta_leads_mapping_');
        $q4 = $this->db->get(db_prefix() . 'options');
        $opts = $q4 ? $q4->result_array() : [];
        foreach ($opts as $opt) {
            $decoded = @json_decode($opt['value'], true);
            if (is_array($decoded)) {
                $mappings[] = $decoded;
            }
        }

        // Support legacy table if exists
        try {
            if ($this->db->table_exists(db_prefix() . 'meta_lead_settings')) {
                $q5 = $this->db->get(db_prefix() . 'meta_lead_settings');
                $old = $q5 ? $q5->result_array() : [];
                foreach ($old as $r) {
                    // check if already in mappings to avoid duplicates
                    $exists = false;
                    foreach ($mappings as $m) {
                        if ($m['form_id'] == $r['form_id']) { $exists = true; break; }
                    }
                    if (!$exists) { $mappings[] = $r; }
                }
            }
        } catch (Exception $e) {}

        return $mappings;
    }
}
