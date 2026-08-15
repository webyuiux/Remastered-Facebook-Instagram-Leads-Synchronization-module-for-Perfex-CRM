<?php
defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'meta_leads_data')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'meta_leads_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `meta_lead_id` varchar(100) NOT NULL,
  `form_id` varchar(100) DEFAULT NULL,
  `page_id` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `platform` varchar(50) DEFAULT NULL,
  `raw_data` TEXT DEFAULT NULL,
  `form_name` varchar(255) DEFAULT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `status` varchar(50) DEFAULT \'Pending\',
  `assigned_staff` int(11) DEFAULT 0,
  `lead_status` int(11) DEFAULT 0,
  `lead_source` int(11) DEFAULT 0,
  `note` TEXT DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `meta_lead_id` (`meta_lead_id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
} else {
    // Add columns if they don't exist (for upgrades from older versions)
    $alter_checks = [
        'company'        => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `company` varchar(255) DEFAULT NULL AFTER `phone`",
        'city'           => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `city` varchar(255) DEFAULT NULL AFTER `company`",
        'lead_status'    => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `lead_status` int(11) DEFAULT 0",
        'lead_source'    => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `lead_source` int(11) DEFAULT 0",
        'page_id'        => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `page_id` varchar(100) DEFAULT NULL AFTER `form_id`",
        'raw_data'       => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `raw_data` TEXT DEFAULT NULL",
        'page_name'      => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `page_name` varchar(255) DEFAULT NULL",
        'platform'       => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `platform` varchar(50) DEFAULT NULL",
        'assigned_staff' => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `assigned_staff` int(11) DEFAULT 0",
        'note'           => "ALTER TABLE `" . db_prefix() . "meta_leads_data` ADD `note` TEXT DEFAULT NULL",
    ];
    foreach ($alter_checks as $col => $sql) {
        if (!$CI->db->field_exists($col, db_prefix() . 'meta_leads_data')) {
            $CI->db->query($sql);
        }
    }
}

if (!$CI->db->table_exists(db_prefix() . 'meta_lead_settings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'meta_lead_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` varchar(100) NOT NULL,
  `form_name` varchar(255) DEFAULT NULL,
  `page_id` varchar(100) DEFAULT NULL,
  `page_name` varchar(255) DEFAULT NULL,
  `assigned_staff` int(11) DEFAULT 0,
  `lead_status` int(11) DEFAULT 0,
  `lead_source` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
} else {
    if (!$CI->db->field_exists('assigned_staff', db_prefix() . 'meta_lead_settings')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'meta_lead_settings` ADD `assigned_staff` int(11) DEFAULT 0');
    }
    if (!$CI->db->field_exists('lead_status', db_prefix() . 'meta_lead_settings')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'meta_lead_settings` ADD `lead_status` int(11) DEFAULT 0');
    }
    if (!$CI->db->field_exists('lead_source', db_prefix() . 'meta_lead_settings')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'meta_lead_settings` ADD `lead_source` int(11) DEFAULT 0');
    }
    if (!$CI->db->field_exists('page_id', db_prefix() . 'meta_lead_settings')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'meta_lead_settings` ADD `page_id` varchar(100) DEFAULT NULL');
    }
    if (!$CI->db->field_exists('page_name', db_prefix() . 'meta_lead_settings')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'meta_lead_settings` ADD `page_name` varchar(255) DEFAULT NULL');
    }
}

if (!$CI->db->table_exists(db_prefix() . 'meta_leads_sync_history')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'meta_leads_sync_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` datetime NOT NULL,
  `meta_lead_id` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `message` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}
