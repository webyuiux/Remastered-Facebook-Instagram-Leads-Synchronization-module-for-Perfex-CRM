<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php try { 
$staff = isset($staff) && is_array($staff) ? $staff : [];
$api_forms = isset($api_forms) && is_array($api_forms) ? $api_forms : [];
$all_visible_forms = isset($all_visible_forms) && is_array($all_visible_forms) ? $all_visible_forms : $api_forms;
$statuses = isset($statuses) && is_array($statuses) ? $statuses : [];
$sources  = isset($sources) && is_array($sources) ? $sources : [];
$current_filter_staff    = $current_filter_staff ?? '';
$current_filter_campaign = $current_filter_campaign ?? '';
$current_filter_status   = $current_filter_status ?? '';
$current_filter_source   = $current_filter_source ?? '';
$staff_map = [];
foreach($staff as $s) { 
    if(isset($s['staffid'])) $staff_map[$s['staffid']] = ($s['firstname'] ?? '') .' '. ($s['lastname'] ?? ''); 
}

if(!function_exists('ml_initials')) {
    function ml_initials($name) {
        preg_match_all('/\b\w/', $name ?? '', $m);
        return strtoupper(implode('', array_slice($m[0], 0, 2))) ?: '?';
    }
}
$tot_leads = 0; $act_staff = [];
if(!empty($api_forms)){
    foreach($api_forms as $mc){
        if (empty($mc['is_mapped'])) continue;
        if(isset($mc['leads_count']) && is_numeric($mc['leads_count'])) $tot_leads += (int)$mc['leads_count'];
        $astaff = is_array($mc['assigned_staff'] ?? null) ? ($mc['assigned_staff'][0] ?? 0) : ($mc['assigned_staff'] ?? 0);
        if(!empty($astaff)) $act_staff[$astaff] = true;
    }
}
?>
<div id="wrapper">
    <div class="content">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* CSS Styles for the new UI */
.ml-page *, .ml-page *::before, .ml-page *::after { box-sizing: border-box; }
.ml-page { padding: 40px; font-family: 'Inter', sans-serif !important; background: #f8fafc; min-height: 100vh; color: #1e293b; }
.ml-header-flex { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px; }
.ml-title { font-size: 32px !important; font-weight: 700 !important; color: #0f172a !important; margin: 0 0 8px 0 !important; letter-spacing: -0.02em; }
.ml-subtitle { font-size: 15px; color: #64748b; margin: 0; line-height: 1.5; max-width: 600px;}

.ml-grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px; }
.ml-stat-card { background: #ffffff; border-radius: 12px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
.ml-stat-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
.ml-stat-icon-blue { background: #eff6ff; color: #2563eb; }
.ml-stat-icon-orange { background: #fff7ed; color: #ea580c; }
.ml-stat-icon-light { background: #f1f5f9; color: #475569; }
.ml-stat-info { display: flex; flex-direction: column; gap: 4px; }
.ml-stat-label { font-size: 13px; text-transform: uppercase; font-weight: 600; color: #64748b; }
.ml-stat-number { font-size: 28px; font-weight: 700; color: #0f172a; line-height: 1; display:flex; align-items: baseline; gap:8px;}
.ml-stat-badge { font-size: 12px; font-weight: 600; color: #2563eb; }

.ml-btn { height: 42px; padding: 0 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: 1px solid #e2e8f0; background: #ffffff; color: #334155; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; white-space: nowrap; text-decoration: none;}
.ml-btn:hover { background: #f8fafc; }
.ml-btn-primary { background: #1d4ed8 !important; border-color: #1d4ed8 !important; color: #ffffff !important; }
.ml-btn-primary:hover { background: #1e40af !important; }

.ml-table-card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 32px; padding: 0;}
.ml-table-header { padding: 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; }
.ml-table-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
.ml-table-sort { font-size: 13px; color: #64748b; display:flex; align-items:center; gap:8px;}
.ml-table-sort select { border: none; font-weight: 600; color: #2563eb; outline: none; background: transparent; cursor: pointer;}

.ml-table { width: 100%; border-collapse: collapse; }
.ml-table th { padding: 16px 24px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; background: #fafafa;}
.ml-table td { padding: 20px 24px; font-size: 14px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ml-table tr:last-child td { border-bottom: none; }
.ml-table tr:hover td { background: #f8fafc; }

.ml-camp-name { font-weight: 600; color: #0f172a; font-size: 15px;}
.ml-camp-id { font-size: 12px; color: #94a3b8; font-family: monospace; margin-top: 4px; }

.ml-fselect { width: 100%; height: 36px; padding: 0 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 13px; color: #334155; font-weight: 500; font-family: inherit; outline: none; background: #ffffff; }
.ml-fselect:focus { border-color: #2563eb; }

/* Dropdowns */
.ml-dd { position: relative; display: inline-block; }
.ml-dd-menu { display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 220px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 999; padding: 8px; }
.ml-dd.open .ml-dd-menu { display: block; }

.ml-table-footer { padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f1f5f9; background: #fafafa;}

/* ── Responsive ── */
@media (max-width: 1024px) {
    .ml-grid-3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
    .ml-page { padding: 16px; }
    .ml-header-flex { flex-direction: column; align-items: flex-start; gap: 12px; }
    .ml-header-flex > div:last-child { display: flex; flex-wrap: wrap; gap: 8px; width: 100%; }
    .ml-grid-3 { grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .ml-stat-card { padding: 16px; gap: 12px; }
    .ml-stat-icon { width: 44px; height: 44px; font-size: 20px; }
    .ml-stat-number { font-size: 22px; }
    .ml-title { font-size: 24px !important; }
    .ml-table-header { flex-direction: column; align-items: flex-start; gap: 8px; padding: 16px; }
    .ml-table th, .ml-table td { padding: 14px 16px; font-size: 13px; }
    /* Hide less critical columns on tablet */
    .ml-table th:nth-child(4),
    .ml-table td:nth-child(4),
    .ml-table th:nth-child(5),
    .ml-table td:nth-child(5) { display: none; }
    .ml-dd-menu { right: auto; left: 0; }
    .ml-btn { font-size: 13px; padding: 0 12px; height: 38px; }
}
@media (max-width: 480px) {
    .ml-grid-3 { grid-template-columns: 1fr; gap: 10px; }
    .ml-title { font-size: 20px !important; }
    .ml-subtitle { font-size: 13px; }
    /* Stack mapping table as cards */
    .ml-table thead { display: none; }
    .ml-table, .ml-table tbody, .ml-table tr, .ml-table td { display: block; width: 100%; }
    .ml-table tr { border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 10px; padding: 14px; background: #fff; }
    .ml-table tr:hover td { background: none; }
    .ml-table td { border: none; padding: 6px 0; font-size: 13px; display: flex; align-items: center; gap: 8px; }
    .ml-table td::before { content: attr(data-label); font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; min-width: 80px; flex-shrink: 0; }
    .ml-table td:nth-child(4),
    .ml-table td:nth-child(5) { display: flex; } /* restore hidden cols in card layout */
    .ml-fselect { font-size: 13px; }
    .ml-table-footer { flex-direction: column; gap: 8px; align-items: stretch; }
    .ml-table-footer > * { text-align: center; }
}
</style>

<div class="ml-page">
    <div class="ml-header-flex">
        <div>
            <h1 class="ml-title">Campaign Configuration</h1>
            <p class="ml-subtitle">Manage how Meta Lead Ads map to your CRM. Assign internal staff to specific forms and track incoming lead volume per campaign.</p>
        </div>
        <div style="display:flex;gap:12px; align-items:center;">
            <div class="ml-dd" id="dd-filter">
                <button class="ml-btn" id="dd-filter-toggle" type="button">
                    <i class="fa fa-filter text-muted"></i> Filters
                    <?php if (!empty($current_filter_staff) || !empty($current_filter_campaign)): ?>
                    <span style="background:#2563eb;color:#fff;border-radius:10px;font-size:10px;padding:2px 6px;margin-left:4px;">ON</span>
                    <?php endif; ?>
                </button>
                <div class="ml-dd-menu" id="dd-filter-menu" style="min-width:250px;">
                    <form method="get" id="ml-filter-form" style="margin: 0;">
                        <?php if (is_admin() || has_permission('meta_leads', '', 'view')): ?>
                        <div style="margin-bottom:12px;">
                            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:6px;">Staff</label>
                            <select name="filter_staff" id="filter-staff" style="width:100%;height:38px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;outline:none;">
                                <option value="">All Staff</option>
                                <?php foreach ($staff as $s): ?>
                                <option value="<?php echo htmlspecialchars($s['staffid'], ENT_QUOTES, 'UTF-8'); ?>" <?php if(isset($current_filter_staff) && $current_filter_staff == $s['staffid']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars(($s['firstname'] ?? '') . ' ' . ($s['lastname'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        <div style="margin-bottom:12px;">
                            <label style="font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;display:block;margin-bottom:6px;">Campaign</label>
                            <select name="filter_campaign" id="filter-campaign" style="width:100%;height:38px;border:1px solid #e2e8f0;border-radius:6px;padding:0 10px;outline:none;">
                                <option value="">All Campaigns</option>
                                <?php 
                                $campaign_list = isset($all_visible_forms) ? $all_visible_forms : (isset($api_forms) ? $api_forms : []);
                                foreach ($campaign_list as $cf): 
                                    $cf_name = !empty($cf['form_name']) ? $cf['form_name'] : (!empty($cf['page_name']) ? $cf['page_name'] : 'Campaign');
                                ?>
                                <option value="<?php echo htmlspecialchars($cf['form_id'], ENT_QUOTES, 'UTF-8'); ?>" <?php if(isset($current_filter_campaign) && $current_filter_campaign == $cf['form_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($cf_name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="display:flex;gap:6px;">
                            <button type="submit" class="ml-btn ml-btn-primary" style="flex:1;">Apply</button>
                            <a href="<?php echo admin_url('meta_leads/lead_settings'); ?>" class="ml-btn" style="flex:1;text-align:center;text-decoration:none;border-color:#fee2e2;color:#dc2626;">Clear</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <button class="ml-btn ml-btn-primary" data-toggle="modal" data-target="#mapNewFormModal">
                <i class="fa fa-plus"></i> Map New Form
            </button>
        </div>
    </div>
    
    <div class="ml-grid-3">
        <div class="ml-stat-card">
            <div class="ml-stat-icon ml-stat-icon-blue"><i class="fa fa-bullhorn"></i></div>
            <div class="ml-stat-info">
                <div class="ml-stat-label">Active Campaigns</div>
                <div class="ml-stat-number"><?php echo count($api_forms ?? []); ?> <span class="ml-stat-badge">+3 this week</span></div>
            </div>
        </div>
        <div class="ml-stat-card">
            <div class="ml-stat-icon ml-stat-icon-orange"><i class="fa fa-user-plus"></i></div>
            <div class="ml-stat-info">
                <div class="ml-stat-label">Total Leads</div>
                <div class="ml-stat-number"><?php echo number_format($tot_leads); ?></div>
            </div>
        </div>
        <div class="ml-stat-card">
            <div class="ml-stat-icon ml-stat-icon-light"><i class="fa fa-user"></i></div>
            <div class="ml-stat-info">
                <div class="ml-stat-label">Staff Assigned</div>
                <div class="ml-stat-number"><?php echo count($act_staff); ?> / <?php echo count($staff); ?></div>
            </div>
        </div>
    </div>

    <div class="ml-table-card">
        <div class="ml-table-header">
            <h2 class="ml-table-title">Form Mapping & Assignments</h2>
        </div>

        <table class="ml-table">
            <thead>
                <tr>
                    <th>CAMPAIGN NAME / ID</th>
                    <th style="width:140px;">LEADS COUNT<div style="font-size:10px;color:#94a3b8;margin-top:4px;">SYNCED / TOTAL</div></th>
                    <?php if (is_admin() || has_permission('meta_leads', '', 'view')): ?>
                    <th style="width:180px;">ASSIGNED STAFF</th>
                    <th style="width:160px;">STATUS</th>
                    <th style="width:160px;">SOURCE</th>
                    <?php endif; ?>
                    <th style="text-align:right;">ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $mapped_count = 0;
                if (!empty($api_forms)): 
                    foreach ($api_forms as $i => $form):
                        if (empty($form['is_mapped'])) continue;
                        $mapped_count++;
                        $c_staff = $form['assigned_staff'] ?? 0;
                        if(is_array($c_staff)) { $c_staff = $c_staff[0] ?? 0; }
                ?>
                <tr data-form-id="<?php echo htmlspecialchars($form['form_id'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-page-id="<?php echo htmlspecialchars($form['page_id'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-page-name="<?php echo htmlspecialchars($form['page_name'], ENT_QUOTES, 'UTF-8'); ?>"
                    data-form-name="<?php echo htmlspecialchars($form['form_name'], ENT_QUOTES, 'UTF-8'); ?>">
                    <td>
                        <div class="ml-camp-name"><?php echo htmlspecialchars(!empty($form['form_name'])?$form['form_name']:'Form '.$form['form_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <div class="ml-camp-id">CID-<?php echo substr($form['form_id'], 0, 4); ?>-<?php echo substr(md5($form['form_id']), 0, 4); ?></div>
                    </td>
                    <td style="font-weight: 600; color: #0f172a; font-size: 15px;">
                        <?php echo is_numeric($form['synced_count']) ? number_format($form['synced_count']) : 0; ?> / <?php echo is_numeric($form['leads_count']) ? number_format($form['leads_count']) : 0; ?>
                    </td>
                    <?php if (is_admin() || has_permission('meta_leads', '', 'view')): ?>
                    <td>
                        <select class="ml-fselect staff-select">
                            <option value="0">Unassigned</option>
                            <?php foreach ($staff as $s): ?>
                            <option value="<?php echo (int)$s['staffid']; ?>" <?php echo ($c_staff == $s['staffid']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['firstname'] . ' ' . $s['lastname'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select class="ml-fselect status-select">
                            <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo $st['id']; ?>" <?php echo (isset($form['lead_status']) && $form['lead_status'] == $st['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($st['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td>
                        <select class="ml-fselect source-select">
                            <?php foreach ($sources as $sr): ?>
                            <option value="<?php echo $sr['id']; ?>" <?php echo (isset($form['lead_source']) && $form['lead_source'] == $sr['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sr['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <?php endif; ?>
                    <td style="text-align:right;">
                        <div style="display:inline-flex; gap:6px;">
                            <?php if (is_admin()): ?>
                            <button type="button" class="btn btn-danger btn-icon unsync-map-btn"
                                    data-form-id="<?php echo htmlspecialchars($form['form_id'], ENT_QUOTES, 'UTF-8'); ?>"
                                    title="Disconnect Mapping">
                                <i class="fa fa-trash"></i>
                            </button>
                            <?php endif; ?>
                            <a href="<?php echo admin_url('meta_leads/sync_past_leads/'.$form['form_id'].'/'.$form['page_id']); ?>"
                               onclick="return confirm('Pulls historic raw leads up to 90 days from Meta API. Proceed?');"
                               class="btn btn-success btn-icon" title="Sync Past Leads">
                                <i class="fa fa-refresh"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
                <?php if ($mapped_count === 0): ?>
                <tr id="ml-empty-map"><td colspan="<?php echo (is_admin() || has_permission('meta_leads', '', 'view')) ? '6' : '3'; ?>" style="text-align:center;padding:60px 20px;color:#94a3b8;">
                    <i class="fa fa-link" style="font-size:48px;margin-bottom:16px;"></i>
                    <div style="font-size:16px;font-weight:500;color:#64748b;">No campaigns mapped yet</div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="ml-table-footer">
            <div style="font-size: 13px; color: #64748b;">Showing <?php echo $mapped_count; ?> of <?php echo $mapped_count; ?> active forms</div>
            <div style="display: flex; gap: 8px;">
                <button class="ml-btn" style="width: 32px; height: 32px; padding:0; display:flex; align-items:center; justify-content:center;"><i class="fa fa-angle-left"></i></button>
                <button class="ml-btn" style="width: 32px; height: 32px; padding:0; display:flex; align-items:center; justify-content:center;"><i class="fa fa-angle-right"></i></button>
            </div>
        </div>
    </div>

<!-- LINK NEW CAMPAIGN MODAL (same as before for functional mapping) -->
<div class="modal fade" id="mapNewFormModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px;border:none;">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;border-radius:12px 12px 0 0;padding:20px 24px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" style="font-size:18px;font-weight:700;color:#0f172a;"><i class="fa fa-facebook-square" style="color:#1877F2;margin-right:6px;"></i> Link New Meta Form</h4>
            </div>
            <?php echo form_open(admin_url('meta_leads/lead_settings')); ?>
            <div class="modal-body" style="padding:24px;">
                <div style="margin-bottom:20px;">
                    <label style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:8px;display:block;">Select Extracted Form</label>
                    <select id="api_combined_select" class="form-control" style="height:44px;font-size:15px;" required>
                        <option value="">— Choose from Facebook API Array —</option>
                        <?php if (!empty($api_forms)): foreach ($api_forms as $af): ?>
                        <option value='<?php echo htmlspecialchars(json_encode(['form_id'=>$af['form_id'],'form_name'=>$af['form_name'],'page_id'=>$af['page_id'],'page_name'=>$af['page_name']]), ENT_QUOTES, 'UTF-8'); ?>'>
                            <?php echo htmlspecialchars($af['page_name'].' › '.$af['form_name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>
                
                <input type="hidden" name="page_name"  value="">
                <input type="hidden" name="page_id"    value="">
                <input type="hidden" name="form_name"  value="">
                <input type="hidden" name="form_id"    value="">

                <div style="background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e2e8f0;margin-bottom:12px;">
                    <label style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:8px;display:block;">Assign Default Staff Group</label>
                    <select name="assigned_staff" class="form-control">
                        <option value="0">Unassigned (Queue)</option>
                        <?php foreach ($staff as $s): ?>
                        <option value="<?php echo (int)$s['staffid']; ?>"><?php echo htmlspecialchars($s['firstname'].' '.$s['lastname'], ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div style="background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e2e8f0;">
                        <label style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:8px;display:block;">Default Status</label>
                        <select name="lead_status" class="form-control">
                            <?php foreach ($statuses as $st): ?>
                            <option value="<?php echo $st['id']; ?>">
                                <?php echo htmlspecialchars($st['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div style="background:#f8fafc;padding:16px;border-radius:8px;border:1px solid #e2e8f0;">
                        <label style="font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;margin-bottom:8px;display:block;">Lead Source</label>
                        <select name="lead_source" class="form-control">
                            <?php foreach ($sources as $sr): ?>
                            <option value="<?php echo $sr['id']; ?>">
                                <?php echo htmlspecialchars($sr['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="padding:20px 24px;border-top:1px solid #e2e8f0;background:#f8fafc;border-radius:0 0 12px 12px;">
                <button type="button" class="ml-btn" data-dismiss="modal">Cancel</button>
                <button type="submit" class="ml-btn ml-btn-primary">Link Mapping</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

    </div>
</div>
<?php init_tail(); ?>
<script>
$(document).ready(function(){
    $('#api_combined_select').on('change', function(){
        if (this.value){
            var d = JSON.parse(this.value);
            $('input[name="page_name"]').val(d.page_name);
            $('input[name="page_id"]').val(d.page_id);
            $('input[name="form_name"]').val(d.form_name);
            $('input[name="form_id"]').val(d.form_id);
        }
    });

    // Handle map row auto-save
    $(document).on('change', '.staff-select, .status-select, .source-select', function(){
        var row = $(this).closest('tr');
        var data = {
            form_id: row.data('form-id'),
            page_id: row.data('page-id'),
            page_name: row.data('page-name'),
            form_name: row.data('form-name'),
            assigned_staff: row.find('.staff-select').val(),
            lead_status: row.find('.status-select').val(),
            lead_source: row.find('.source-select').val()
        };

        $.post(admin_url + 'meta_leads/save_mapping_ajax', data, function(res){
            // Recalculate staff uniquely assigned 
            var unique = [];
            $('.staff-select').each(function(){
                var val = $(this).val();
                if(val != "0" && unique.indexOf(val) === -1) unique.push(val);
            });
            $('.ml-stat-number').eq(2).text(unique.length + ' / <?php echo count($staff); ?>');
            alert_float('success', 'Mapping Updated');
        });
    });

    // Handle unmap
    $(document).on('click', '.unsync-map-btn', function(){
        if(!confirm('Are you sure you want to completely disconnect this campaign?')) return;
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        $.post(admin_url + 'meta_leads/unsync_ajax', {form_id: $btn.data('form-id')}, function(){
            location.reload();
        });
    });

    // Dropdown Logic
    var fBtn = document.getElementById('dd-filter-toggle'), fMenu = document.getElementById('dd-filter-menu');
    if(fBtn && fMenu){
        fBtn.onclick = function(e){
            e.stopPropagation();
            var dd = this.closest('.ml-dd');
            dd.classList.toggle('open');
        }
        fMenu.onclick = function(e){ e.stopPropagation(); }
    }
    document.onclick = function(){
        document.querySelectorAll('.ml-dd.open').forEach(function(el){ el.classList.remove('open'); });
    };
});
</script>
<?php } catch (\Throwable $th) {
    echo "<div style='margin-left: 250px; margin-top: 80px; padding: 40px; background: #fee2e2; border: 2px solid #ef4444; color: #7f1d1d; font-family: sans-serif; font-size: 18px; line-height: 1.5;'>";
    echo "<h2>View Render Crashed:</h2>";
    echo "<b>Error:</b> " . htmlspecialchars($th->getMessage()) . "<br>";
    echo "<b>File:</b> " . htmlspecialchars($th->getFile()) . " (Line " . $th->getLine() . ")";
    echo "</div>";
} ?>
