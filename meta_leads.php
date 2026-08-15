<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Meta Sync
Description: Automatically collect leads from Facebook & Instagram and add them into Perfex CRM manually or in bulk.
Author: Virrat Global
Author URI: https://virratglobal.com/
Version: 1.0.0
Requires at least: 2.3.*
*/

define('META_LEADS_MODULE_NAME', 'meta_leads');

hooks()->add_action('admin_init', 'meta_leads_module_init_menu_items');
hooks()->add_action('admin_init', 'meta_leads_permissions');

register_activation_hook(META_LEADS_MODULE_NAME, 'meta_leads_module_activation_hook');

function meta_leads_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

register_language_files(META_LEADS_MODULE_NAME, [META_LEADS_MODULE_NAME]);

function meta_leads_module_init_menu_items()
{
    $CI = &get_instance();

    // Show menu for all staff (they will only see their own assigned leads/campaigns)
    if (is_staff_logged_in()) {
        $CI->app_menu->add_sidebar_menu_item('meta_leads', [
            'name'     => _l('meta_leads'),
            'href'     => admin_url('meta_leads/submitted_leads'),
            'position' => 30,
            'icon'     => 'fa fa-facebook-square'
        ]);

        $CI->app_menu->add_sidebar_children_item('meta_leads', [
            'slug'     => 'meta_leads_submitted',
            'name'     => _l('meta_leads_submitted'),
            'href'     => admin_url('meta_leads/submitted_leads'),
            'position' => 1,
        ]);

        $CI->app_menu->add_sidebar_children_item('meta_leads', [
            'slug'     => 'meta_leads_lead_settings',
            'name'     => _l('meta_leads_lead_settings'),
            'href'     => admin_url('meta_leads/lead_settings'),
            'position' => 2,
        ]);

        $CI->app_menu->add_sidebar_children_item('meta_leads', [
            'slug'     => 'meta_leads_sync_history',
            'name'     => _l('meta_leads_sync_history'),
            'href'     => admin_url('meta_leads/sync_history'),
            'position' => 3,
        ]);
    }

    if (is_admin()) {
        $CI->app_menu->add_sidebar_children_item('meta_leads', [
            'slug'     => 'meta_leads_settings',
            'name'     => _l('meta_leads_settings'),
            'href'     => admin_url('meta_leads/settings'),
            'position' => 4,
        ]);
    }
}

/**
 * Register module permissions
 */
function meta_leads_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'     => _l('permission_view') . '(' . _l('permission_global') . ')',
        'view_own' => _l('permission_view_own'),
        'create'   => _l('permission_create'),
        'edit'     => _l('permission_edit'),
        'delete'   => _l('permission_delete'),
    ];

    register_staff_capabilities('meta_leads', $capabilities, _l('meta_leads'));
}
