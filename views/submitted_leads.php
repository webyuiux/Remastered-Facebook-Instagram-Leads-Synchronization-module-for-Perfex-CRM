<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
/* ═══════════════════════════════════════
   Meta Leads – High-Fidelity Design
   ═══════════════════════════════════════ */
.ml-page *, .ml-page *::before, .ml-page *::after { box-sizing: border-box; }
.ml-page { padding: 40px; font-family: 'Inter', sans-serif !important; background: #f8fafc; min-height: 100vh; color: #1e293b; }
.ml-header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
.ml-title { font-size: 28px !important; font-weight: 700 !important; color: #0f172a !important; margin: 0 0 8px 0 !important; letter-spacing: -0.02em; }
.ml-subtitle { font-size: 15px; color: #64748b; margin: 0; line-height: 1.5; }

/* Dashboard Cards */
.ml-grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 32px; }
.ml-stat-card { background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); border: 1px solid #e2e8f0; }
.ml-stat-title { font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; }
.ml-stat-val-row { display: flex; align-items: baseline; justify-content: space-between; }
.ml-stat-val { font-size: 32px; font-weight: 700; color: #0f172a; line-height: 1; margin:0;}
.ml-stat-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }

/* Buttons */
.ml-btn { height: 40px; padding: 0 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: 1px solid #e2e8f0; background: #f8fafc; color: #334155; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-family: inherit; transition: all 0.2s; white-space: nowrap; text-decoration: none !important; }
.ml-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }
.ml-btn-primary { background: #2563eb !important; border-color: #2563eb !important; color: #ffffff !important; }
.ml-btn-primary:hover { background: #1d4ed8 !important; }

/* Table Section */
.ml-table-card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 32px; }
.ml-table-ctrls { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: #fafafa; }
.ml-search-box { position: relative; width: 300px; }
.ml-search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
.ml-search-box input { width: 100%; height: 38px; padding: 0 16px 0 36px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; font-size: 14px; color: #334155; font-family: inherit; outline: none; transition: border-color 0.2s; }
.ml-search-box input:focus { border-color: #2563eb; }

/* Table */
.ml-table { width: 100%; border-collapse: collapse; }
.ml-table th { padding: 14px 24px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; background: #ffffff; border-bottom: 1px solid #f1f5f9; }
.ml-table td { padding: 16px 24px; font-size: 14px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ml-table tr:last-child td { border-bottom: none; }
.ml-table tr.row-sel td { background: #eff6ff; }
.ml-table tr:hover td { background: #f8fafc; }

/* Avatars & Data */
.ml-lead-info { display: flex; align-items: center; gap: 12px; }
.ml-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: #4338ca; background: #e0e7ff; flex-shrink: 0; }
.ml-lead-name { font-weight: 600; color: #0f172a; text-decoration: none !important; }
.ml-lead-name:hover { text-decoration: underline !important; color: #2563eb; }
.ml-lead-email { font-size: 13px; color: #64748b; margin-top: 2px; }

/* Forms & Badges */
.ml-plat { display: flex; align-items: center; gap: 6px; font-weight: 500; color: #334155; }
.ml-plat i { color: #1877F2; font-size: 16px; }
.ml-form-badge { background: #f1f5f9; color: #475569; padding: 6px 12px; border-radius: 6px; font-size: 13px; font-weight: 500; display: inline-block; white-space: nowrap; }

/* Status Pills */
.ml-pill { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; }
.ml-pill-pending { background: #ffedd5; color: #c2410c; }
.ml-pill-added { background: #e0e7ff; color: #4338ca; }

/* Pagination controls */
.ml-pag-wrap { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; background: #fafafa; border-top: 1px solid #e2e8f0; }
.ml-pag-info { font-size: 13px; color: #64748b; font-weight: 500; }
.ml-pag-btns { display: flex; gap: 4px; }
.ml-pag-btns a { display: inline-flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: 6px; background: #ffffff; border: 1px solid #e2e8f0; color: #334155; font-size: 13px; font-weight: 600; text-decoration: none !important; transition: all 0.2s; }
.ml-pag-btns a:hover { background: #f1f5f9; }
.ml-pag-btns a.active { background: #2563eb; color: #ffffff; border-color: #2563eb; }
.ml-pag-btns a.disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }

/* Checkboxes */
.ml-chk { width: 16px; height: 16px; border-radius: 4px; border: 1px solid #cbd5e1; cursor: pointer; accent-color: #2563eb; }

/* Dropdowns */
.ml-dd { position: relative; display: inline-block; }
.ml-dd-menu { display: none; position: absolute; top: calc(100% + 4px); right: 0; min-width: 220px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); z-index: 999; padding: 8px; }
.ml-dd.open .ml-dd-menu { display: block; }

/* Mock Graphics */
.ml-mock-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-top: 32px; }
.ml-mock-dark { background: #1e293b; border-radius: 16px; padding: 40px; color: #ffffff; position: relative; overflow: hidden; }
.ml-mock-light { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 40px; text-align: center; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); }
.ml-micon { width: 56px; height: 56px; border-radius: 16px; background: #e0e7ff; color: #4f46e5; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 24px; }
.ml-mock-btn { display: inline-flex; background: #ffffff; color: #0f172a; padding: 12px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; margin-top: 24px; text-decoration: none !important; }
.ml-mock-dark::after { content: ''; position: absolute; right: -50px; top: -50px; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%); pointer-events: none; }

/* ── Responsive ── */
@media (max-width: 1024px) {
    .ml-grid-4 { grid-template-columns: repeat(2, 1fr); }
    .ml-mock-grid { grid-template-columns: 1fr; }
    .ml-mock-light { display: none; }
}
@media (max-width: 768px) {
    .ml-page { padding: 16px; }
    .ml-header-flex { flex-direction: column; align-items: flex-start; gap: 12px; }
    .ml-header-flex > div:last-child { display: flex; flex-wrap: wrap; gap: 8px; width: 100%; }
    .ml-grid-4 { grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px; }
    .ml-stat-card { padding: 16px; }
    .ml-stat-val { font-size: 24px; }
    .ml-table-card { border-radius: 10px; }
    .ml-table-ctrls { flex-direction: column; align-items: stretch; gap: 10px; padding: 12px 16px; }
    .ml-search-box { width: 100%; }
    .ml-table th, .ml-table td { padding: 12px 14px; font-size: 13px; }
    /* Hide less important columns on tablet */
    .ml-table th:nth-child(5),
    .ml-table td:nth-child(5) { display: none; } /* Platform */
    .ml-pag-wrap { flex-direction: column; gap: 10px; padding: 12px 16px; }
    .ml-dd-menu { right: auto; left: 0; }
    .ml-mock-grid { display: none; }
    .ml-btn { font-size: 13px; padding: 0 12px; height: 36px; }
}
@media (max-width: 480px) {
    .ml-grid-4 { grid-template-columns: 1fr 1fr; gap: 10px; }
    .ml-stat-card { padding: 14px 12px; }
    .ml-stat-val { font-size: 20px; }
    .ml-stat-title { font-size: 10px; }
    .ml-title { font-size: 22px !important; }
    .ml-subtitle { font-size: 13px; }
    /* Stack table rows as cards on mobile */
    .ml-table thead { display: none; }
    .ml-table, .ml-table tbody, .ml-table tr, .ml-table td { display: block; width: 100%; }
    .ml-table tr { border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 10px; padding: 12px; background: #fff; }
    .ml-table tr:hover td { background: none; }
    .ml-table td { border: none; padding: 4px 0; font-size: 13px; display: flex; align-items: center; gap: 8px; }
    .ml-table td::before { content: attr(data-label); font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; min-width: 70px; flex-shrink: 0; }
    .ml-table td:nth-child(1)::before { content: ''; min-width: 0; } /* checkbox — no label */
    .ml-table td:nth-child(5),
    .ml-table td:nth-child(6) { display: flex; } /* show all on card layout */
    .ml-avatar { width: 32px; height: 32px; font-size: 12px; }
    .ml-pag-btns { flex-wrap: wrap; }
    .ml-table-ctrls { padding: 10px 12px; }
    /* Lead modal */
    .ml-modal-grid { grid-template-columns: 1fr !important; }
}

/* Filter Modal Specifics */
.ml-filter-group { margin-bottom: 20px; }
.ml-filter-label { font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 10px; display: flex; align-items: center; gap: 8px; }
.ml-filter-label i { color: #2563eb; font-size: 14px; opacity: 0.8; }
.ml-filter-input { width: 100%; height: 46px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0 16px; font-size: 14px; color: #1e293b; outline: none; transition: all 0.2s; background: #fff; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
.ml-filter-input:focus { border-color: #2563eb; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); background: #fff; }
.ml-filter-input:hover { border-color: #cbd5e1; }
select.ml-filter-input { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 40px; }
.ml-filter-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
@media (max-width: 600px) { .ml-filter-grid { grid-template-columns: 1fr; } }

</style>

<?php
/* ── Helpers ── */
$staff       = isset($staff) && is_array($staff) ? $staff : [];
$leads       = isset($leads) && is_array($leads) ? $leads : [];
$mapped_campaigns = isset($mapped_campaigns) && is_array($mapped_campaigns) ? $mapped_campaigns : [];
$current_status        = $current_status ?? '';
$current_campaign      = $current_campaign ?? '';
$current_filter_staff  = $current_filter_staff ?? '';
$current_date_from     = $current_date_from ?? '';
$current_date_to       = $current_date_to ?? '';
$stat_total   = $stat_total ?? 0;
$stat_pending = $stat_pending ?? 0;
$stat_added   = $stat_added ?? 0;

$campaign_lookup = [];
if (!empty($mapped_campaigns)) {
    foreach ($mapped_campaigns as $mc) {
        $n = !empty($mc['form_name']) ? $mc['form_name'] : (!empty($mc['page_name']) ? $mc['page_name'] : '');
        if ($n && !empty($mc['form_id'])) {
            $campaign_lookup[(string)$mc['form_id']] = $n;
        }
    }
}
if (!function_exists('ml_campaign')) {
    function ml_campaign($lead, $lkp) {
        if (!empty($lead['form_id']) && !empty($lkp[(string)$lead['form_id']])) return $lkp[(string)$lead['form_id']];
        $fn = trim($lead['form_name'] ?? '');
        if (preg_match('/^(Form\s*)?\d{10,}$/', $fn)) return !empty($lead['page_name']) ? $lead['page_name'] : 'Campaign ' . $fn;
        return $fn ?: '-';
    }
}
if (!function_exists('ml_initials')) {
    function ml_initials($name) {
        preg_match_all('/\b\w/', $name ?? '', $matches);
        $init = strtoupper(implode('', array_slice($matches[0], 0, 2)));
        return $init ?: '?';
    }
}
$all_ids = [];
if (!empty($leads)) { foreach ($leads as $l) { $all_ids[] = (int)$l['id']; } }
?>

<div class="ml-page">
    <div class="ml-header-flex">
        <div>
            <h1 class="ml-title">Submitted Leads</h1>
            <p class="ml-subtitle">Real-time synchronization from Meta Lead Ads campaigns.</p>
        </div>
        <div style="display:flex;gap:12px;">
            <!-- Filters Trigger (Modal) -->
            <button class="ml-btn" type="button" data-toggle="modal" data-target="#filterModal">
                <i class="fa fa-filter text-muted"></i> Filters
                <?php if (!empty($current_status) || !empty($current_campaign) || !empty($current_filter_staff) || !empty($current_date_from) || !empty($current_date_to)): ?>
                <span style="background:#2563eb;color:#fff;border-radius:10px;font-size:10px;padding:2px 8px;margin-left:6px;font-weight:700;">Active</span>
                <?php endif; ?>
            </button>

            <!-- Bulk Manage Dropdown -->
            <div class="ml-dd" id="dd-bulk">
                <button class="ml-btn ml-btn-primary" id="dd-bulk-toggle" type="button">
                    <i class="fa fa-tasks"></i> Bulk Manage <i class="fa fa-caret-down" style="margin-left:4px;"></i>
                </button>
                <div class="ml-dd-menu" id="dd-bulk-menu" style="min-width:190px;">
                    <a class="ml-dd-item" href="#" onclick="triggerBulk('mass_add');return false;" style="display:flex;align-items:center;gap:8px;padding:9px 14px;color:#059669;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fa fa-plus-circle"></i> Add to CRM
                    </a>
                    <a class="ml-dd-item" href="#" onclick="triggerBulk('mass_delete');return false;" style="display:flex;align-items:center;gap:8px;padding:9px 14px;color:#dc2626;font-size:13px;font-weight:600;text-decoration:none;">
                        <i class="fa fa-trash-o"></i> Delete Selected
                    </a>
                    <div style="border-top:1px solid #f1f5f9;margin:4px 0;"></div>
                    <a class="ml-dd-item" href="#" onclick="exportLeads('csv');return false;" style="display:flex;align-items:center;gap:8px;padding:9px 14px;color:#0f172a;font-size:13px;text-decoration:none;">
                        <i class="fa fa-file-text-o text-muted"></i> Export CSV
                    </a>
                    <a class="ml-dd-item" href="#" onclick="exportLeads('excel');return false;" style="display:flex;align-items:center;gap:8px;padding:9px 14px;color:#0f172a;font-size:13px;text-decoration:none;">
                        <i class="fa fa-file-excel-o text-muted"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- DASHBOARD CARDS -->
    <div class="ml-grid-4">
        <div class="ml-stat-card">
            <div class="ml-stat-title">Total Sync</div>
            <div class="ml-stat-val-row">
                <div class="ml-stat-val"><?php echo $stat_total ?? 0; ?></div>
                <div class="ml-stat-badge" style="background:#eff6ff;color:#1e40af;"><i class="fa fa-refresh"></i> Processed</div>
            </div>
        </div>
        <div class="ml-stat-card">
            <div class="ml-stat-title">Total Added</div>
            <div class="ml-stat-val-row">
                <div class="ml-stat-val"><?php echo $stat_added ?? 0; ?></div>
                <div class="ml-stat-badge" style="background:#f0fdf4;color:#166534;"><i class="fa fa-check"></i> Stable</div>
            </div>
        </div>
        <div class="ml-stat-card">
            <div class="ml-stat-title">Sync Accuracy</div>
            <div class="ml-stat-val-row">
                <div class="ml-stat-val">99.2%</div>
                <div class="ml-stat-badge" style="background:#eff6ff;color:#1e40af;"><i class="fa fa-shield"></i> High</div>
            </div>
        </div>
        <div class="ml-stat-card">
            <div class="ml-stat-title">Mapped Synced Campaigns</div>
            <div class="ml-stat-val-row">
                <div class="ml-stat-val"><?php echo str_pad(count($mapped_campaigns ?? []), 2, '0', STR_PAD_LEFT); ?></div>
                <div class="ml-stat-badge" style="background:#f1f5f9;color:#475569;">Active</div>
            </div>
        </div>
    </div>

    <!-- MAIN TABLE -->
    <div class="ml-table-card">
        <div class="ml-table-ctrls">
            <div style="display:flex;align-items:center;gap:16px;">
                <div class="ml-search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" id="ml-search" placeholder="Search leads, campaigns...">
                </div>
                <select id="ml-perpage" style="height:38px;border:1px solid #e2e8f0;border-radius:8px;padding:0 12px;color:#475569;outline:none;">
                    <option value="10">10 rows</option>
                    <option value="25" selected>25 rows</option>
                    <option value="50">50 rows</option>
                    <option value="100">100 rows</option>
                </select>
                <div style="font-size:13px;color:#64748b;font-weight:500;display:none;" id="sel-badge-wrap">
                    <span id="sel-n" style="font-weight:700;color:#2563eb;">0</span> selected globally
                </div>
            </div>
            <div>
                <button class="ml-btn" onclick="location.reload()" style="height:38px;padding:0 12px;"><i class="fa fa-refresh"></i> Refresh</button>
            </div>
        </div>

        <table class="ml-table" id="leads-tbl">
            <thead>
                <tr>
                    <th style="width:40px;"><input type="checkbox" class="ml-chk" id="chk-all"></th>
                    <th>Lead Information</th>
                    <th>Form Name</th>
                    <th>Date Received</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($leads)): 
                    foreach ($leads as $lead):
                        $camp = ml_campaign($lead, $campaign_lookup);
                        $lead_safe = htmlspecialchars(json_encode($lead, JSON_UNESCAPED_UNICODE|JSON_HEX_APOS|JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8');
                        $is_added = ($lead['status'] == 'Added');
                        $colors = ['#e0e7ff'=>'#4338ca', '#fce7f3'=>'#be185d', '#dcfce7'=>'#15803d', '#ffedd5'=>'#c2410c', '#e2e8f0'=>'#334155'];
                        $bg = array_keys($colors)[crc32((string)($lead['name']??'')) % count($colors)];
                        $col = $colors[$bg];
                ?>
                <tr data-id="<?php echo (int)$lead['id']; ?>" data-status="<?php echo htmlspecialchars($lead['status'],ENT_QUOTES,'UTF-8'); ?>" data-campaign="<?php echo htmlspecialchars((string)$lead['form_id'],ENT_QUOTES,'UTF-8'); ?>">
                    <td data-label=""><input type="checkbox" class="ml-chk mass-chk" value="<?php echo (int)$lead['id']; ?>"></td>
                    <td data-label="Lead">
                        <div class="ml-lead-info">
                            <div class="ml-avatar" style="background:<?php echo $bg; ?>;color:<?php echo $col; ?>;">
                                <?php echo ml_initials($lead['name']); ?>
                            </div>
                            <div>
                                <a href="#" class="ml-lead-name view-lead" data-lead="<?php echo $lead_safe; ?>" data-camp="<?php echo htmlspecialchars($camp,ENT_QUOTES,'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($lead['name']??'-',ENT_QUOTES,'UTF-8'); ?>
                                </a>
                                <div class="ml-lead-email"><?php echo htmlspecialchars($lead['email']??'-',ENT_QUOTES,'UTF-8'); ?></div>
                            </div>
                        </div>
                    </td>

                    <td data-label="Campaign"><div class="ml-form-badge"><?php echo htmlspecialchars($camp,ENT_QUOTES,'UTF-8'); ?></div></td>
                    <td data-label="Date">
                        <div style="font-size:13px;color:#1e293b;font-weight:500;"><?php echo date('M d, Y',strtotime($lead['date_added'])); ?></div>
                        <div style="font-size:12px;color:#64748b;"><?php echo date('H:i A',strtotime($lead['date_added'])); ?></div>
                    </td>
                    <td data-label="Status">
                        <span class="ml-pill <?php echo $is_added ? 'ml-pill-added' : 'ml-pill-pending'; ?>">
                            <?php echo $is_added ? 'Added' : 'Pending'; ?>
                        </span>
                    </td>
                    <td data-label="Actions" style="text-align:right;">
                        <div style="display:inline-flex;gap:4px;">

                            <?php if (!$is_added): ?>
                            <a href="<?php echo admin_url('meta_leads/add_to_crm/'.(int)$lead['id']); ?>" class="ml-btn" style="height:32px;padding:0 10px;" title="Push to CRM"><i class="fa fa-save" style="color:#059669;"></i></a>
                            <?php endif; ?>
                            <a href="<?php echo admin_url('meta_leads/delete_lead/'.(int)$lead['id']); ?>" class="ml-btn _delete" style="height:32px;padding:0 10px;" title="Delete"><i class="fa fa-trash" style="color:#dc2626;"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr id="ml-empty-row"><td colspan="6" style="text-align:center;padding:60px 20px;color:#94a3b8;">
                    <i class="fa fa-inbox" style="font-size:48px;margin-bottom:16px;"></i>
                    <div style="font-size:16px;font-weight:500;color:#64748b;">No leads found in this view</div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div id="ml-pagination-wrap"></div>
    </div>

    <!-- MOCKGRAPHICS REMOVED -->

</div>


<!-- BULK CONFIRM MODAL -->
<div class="modal fade" id="bulkConfirmModal" tabindex="-1">
    <div class="modal-dialog" style="max-width:420px;">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;border-radius:16px 16px 0 0;padding:20px 24px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="bulk-modal-title" style="font-size:18px;font-weight:700;color:#0f172a;">Confirm Bulk Action</h4>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p id="bulk-modal-msg" style="font-size:14px;color:#475569;margin:0;"></p>
            </div>
            <div class="modal-footer" style="padding:16px 24px;border-top:1px solid #e2e8f0;border-radius:0 0 16px 16px;background:#f8fafc;">
                <button class="ml-btn" data-dismiss="modal">Cancel</button>
                <button class="ml-btn ml-btn-primary" id="btn-confirm-bulk-run" style="min-width:130px;">Confirm</button>
            </div>
        </div>
    </div>
</div>


<!-- LEAD MODAL -->
<div class="modal fade" id="leadModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:16px;border:none;">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e2e8f0;border-radius:16px 16px 0 0;padding:24px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="lead-modal-title" style="font-size:22px;font-weight:700;color:#0f172a;margin-bottom:4px;">Lead Name</h4>
                <div id="lead-modal-sub" style="font-size:14px;color:#64748b;font-weight:500;">Campaign</div>
            </div>
            <div class="modal-body" style="padding:24px;background:#f1f5f9;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;" class="ml-modal-grid">
                    <div style="background:#fff;border-radius:12px;padding:24px;border:1px solid #e2e8f0;">
                        <h6 style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:16px;letter-spacing:0.05em;">Contact Information</h6>
                        <div style="margin-bottom:12px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Name:</strong> <span id="d-name" style="color:#1e293b;font-weight:500;">-</span></div>
                        <div style="margin-bottom:12px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Email:</strong> <span id="d-email" style="color:#1e293b;font-weight:500;">-</span></div>
                        <div style="margin-bottom:12px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Phone:</strong> <span id="d-phone" style="color:#1e293b;font-weight:500;">-</span></div>
                        <div style="margin-bottom:0px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Company:</strong> <span id="d-company" style="color:#1e293b;font-weight:500;">-</span></div>
                    </div>
                    <div style="background:#fff;border-radius:12px;padding:24px;border:1px solid #e2e8f0;display:flex;flex-direction:column;">
                        <h6 style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:16px;letter-spacing:0.05em;">Acquisition Source</h6>
                        <div style="margin-bottom:12px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Form:</strong> <span id="d-camp" style="color:#1e293b;font-weight:500;">-</span></div>
                        <div style="margin-bottom:12px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Platform:</strong> <span id="d-plat" style="color:#1e293b;font-weight:500;">-</span></div>
                        <div style="margin-bottom:12px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Date:</strong> <span id="d-date" style="color:#1e293b;font-weight:500;">-</span></div>
                        <div style="margin-bottom:16px;font-size:14px;display:flex;"><strong style="width:80px;color:#64748b;">Status:</strong> <span id="d-status">-</span></div>
                        <div style="flex:1;display:flex;flex-direction:column;">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
                                <label style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.05em;margin:0;">Notes</label>
                                <span id="note-save-indicator" style="font-size:11px;color:#94a3b8;opacity:0;transition:opacity 0.4s;"><i class="fa fa-check" style="color:#059669;"></i> Saved</span>
                            </div>
                            <textarea id="d-note" data-lead-id="" placeholder="Add a note about this lead..." style="flex:1;min-height:90px;width:100%;border:1px solid #e2e8f0;border-radius:8px;padding:10px 12px;font-size:13px;font-family:inherit;color:#334155;resize:vertical;outline:none;transition:border-color 0.2s;line-height:1.5;"></textarea>
                        </div>
                    </div>
                </div>
                <div style="margin-top:24px;background:#1e293b;border-radius:12px;padding:24px;">
                    <h6 style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;margin-bottom:12px;letter-spacing:0.05em;">Raw JSON Payload</h6>
                    <pre id="d-raw" style="font-size:12px;background:none;border:none;color:#e2e8f0;padding:0;margin:0;font-family:monospace;"></pre>
                </div>
            </div>
            <div class="modal-footer" id="lead-modal-footer" style="padding:20px 24px;border-top:1px solid #e2e8f0;border-radius:0 0 16px 16px;background:#f8fafc;">
            </div>
        </div>
    </div>
</div>

<!-- FILTER MODAL (REDESIGNED PROPPER POPUP) -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:20px;border:none;box-shadow: 0 25px 50px -12px rgb(0 0 0 / 0.35); overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-bottom: none; padding: 32px 32px 24px; position: relative;">
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8; font-size: 24px; position: absolute; right: 24px; top: 24px;">&times;</button>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <div style="width: 48px; height: 48px; background: rgba(37, 99, 235, 0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(37, 99, 235, 0.3);">
                        <i class="fa fa-filter" style="color: #60a5fa; font-size: 20px;"></i>
                    </div>
                    <div>
                        <h4 class="modal-title" style="font-size: 20px; font-weight: 700; color: #ffffff; margin: 0;">Refine Leads</h4>
                        <p style="font-size: 13px; color: #94a3b8; margin: 4px 0 0;">Apply filters to narrow down your lead results.</p>
                    </div>
                </div>
            </div>
            <form action="<?php echo admin_url('meta_leads/submitted_leads'); ?>" method="get">
                <div class="modal-body" style="padding: 32px; background: #ffffff;">
                    
                    <div class="ml-filter-grid" style="margin-bottom: 24px;">
                        <?php if (is_admin() || has_permission('meta_leads', '', 'view')): ?>
                        <div class="ml-filter-group" style="margin-bottom:0;">
                            <label class="ml-filter-label"><i class="fa fa-user-circle"></i> Staff Member</label>
                            <select name="filter_staff" class="ml-filter-input" style="cursor: pointer; appearance: none; -webkit-appearance: none;">
                                <option value="">All Staff</option>
                                <?php foreach ($staff as $s): ?>
                                <option value="<?php echo htmlspecialchars($s['staffid'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($current_filter_staff==$s['staffid'])?'selected':''; ?>>
                                    <?php echo htmlspecialchars($s['firstname'] . ' ' . $s['lastname'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="ml-filter-group" style="margin-bottom:0;">
                            <label class="ml-filter-label"><i class="fa fa-check-square"></i> Lead Status</label>
                            <select name="status" class="ml-filter-input" style="cursor: pointer; appearance: none; -webkit-appearance: none;">
                                <option value="">All Statuses</option>
                                <option value="Pending" <?php echo ($current_status=='Pending')?'selected':''; ?>>Pending</option>
                                <option value="Added" <?php echo ($current_status=='Added')?'selected':''; ?>>Added</option>
                            </select>
                        </div>
                    </div>

                    <div class="ml-filter-group" style="margin-bottom: 24px;">
                        <label class="ml-filter-label"><i class="fa fa-bullhorn"></i> Campaign (Form)</label>
                        <select name="campaign" class="ml-filter-input" style="cursor: pointer; appearance: none; -webkit-appearance: none;">
                            <option value="">All Mapped Campaigns</option>
                            <?php if (!empty($mapped_campaigns)): foreach ($mapped_campaigns as $mc): ?>
                            <option value="<?php echo htmlspecialchars((string)$mc['form_id'],ENT_QUOTES,'UTF-8'); ?>" <?php echo ($current_campaign==$mc['form_id'])?'selected':''; ?>>
                                <?php echo htmlspecialchars(!empty($mc['form_name'])?(string)$mc['form_name']:(string)($mc['page_name']??'Campaign'),ENT_QUOTES,'UTF-8'); ?>
                            </option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px;">
                        <label class="ml-filter-label" style="margin-bottom:16px; color: #475569;"><i class="fa fa-calendar"></i> Date Received Range</label>
                        <div class="ml-filter-grid">
                            <div>
                                <span style="font-size:11px;color:#94a3b8;font-weight:700;display:block;margin-bottom:8px;letter-spacing: 0.05em;">FROM DATE</span>
                                <div style="position: relative;">
                                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($current_date_from, ENT_QUOTES, 'UTF-8'); ?>" class="ml-filter-input">
                                </div>
                            </div>
                            <div>
                                <span style="font-size:11px;color:#94a3b8;font-weight:700;display:block;margin-bottom:8px;letter-spacing: 0.05em;">TO DATE</span>
                                <div style="position: relative;">
                                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($current_date_to, ENT_QUOTES, 'UTF-8'); ?>" class="ml-filter-input">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="padding: 24px 32px; border-top: 1px solid #f1f5f9; background: #ffffff; display: flex; align-items: center; justify-content: space-between;">
                    <button type="button" class="ml-btn" id="btn-clear-filters-modal" style="border: 1px solid #fee2e2; color: #dc2626; background: #fef2f2; height: 44px; padding: 0 20px; font-weight: 600;">
                        <i class="fa fa-refresh" style="font-size:12px; margin-right:6px;"></i> Reset All
                    </button>
                    <div style="display: flex; gap: 12px;">
                        <button type="button" class="ml-btn" data-dismiss="modal" style="height: 44px; padding: 0 20px; font-weight: 600; border: 1px solid #e2e8f0; background: #fff;">Cancel</button>
                        <button type="submit" class="ml-btn ml-btn-primary" style="min-width: 140px; height: 44px; padding: 0 24px; font-weight: 600; background: #2563eb; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);">Apply Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    </div>
</div>
<?php init_tail(); ?>

<script>
(function(){
'use strict';
var admin_url = window.admin_url || '<?php echo admin_url(); ?>';
var ALL_IDS = <?php echo json_encode(array_values($all_ids)); ?>;
var selected = new Set();
var tbRows = $('#leads-tbl tbody tr').not('#ml-empty-row').get();
var pageLen = parseInt($('#ml-perpage').val()) || 25;
var currPage = 1;

function drawTable() {
    var q = $('#ml-search').val().toLowerCase();
    var fs = $('#filter-status').val();
    var fc = $('#filter-campaign').val();
    
    var filtered = tbRows.filter(function(tr){ 
        var $t = $(tr);
        var st = $t.data('status') || '';
        var cp = String($t.attr('data-campaign') || '');
        
        var matchQ = (q === '' || $t.text().toLowerCase().indexOf(q) > -1);
        var matchS = (!fs || st === fs);
        var matchC = (!fc || cp === fc);
        
        return matchQ && matchS && matchC;
    });
    
    var maxPage = Math.ceil(filtered.length / pageLen) || 1;
    if (currPage > maxPage) currPage = maxPage;
    var start = (currPage - 1) * pageLen;
    var end = start + pageLen;
    
    $(tbRows).hide();
    $(filtered.slice(start, end)).show();
    
    if (filtered.length === 0) {
        if ($('#ml-empty-row').length === 0) {
            $('#leads-tbl tbody').append('<tr id="ml-empty-row"><td colspan="6" style="text-align:center;padding:60px 20px;color:#94a3b8;"><i class="fa fa-inbox" style="font-size:48px;margin-bottom:16px;"></i><div style="font-size:16px;font-weight:500;color:#64748b;">No leads found in this view</div></td></tr>');
        } else {
            $('#ml-empty-row').show();
        }
    } else {
        $('#ml-empty-row').hide();
    }
    
    var phtml = '<div class="ml-pag-wrap">';
    phtml += '<div class="ml-pag-info">Showing '+ (filtered.length>0 ? start+1 : 0) +' to '+ Math.min(filtered.length, end) +' of '+ filtered.length +' results</div>';
    phtml += '<div class="ml-pag-btns">';
    phtml += '<a href="#" class="'+(currPage==1?'disabled':'')+'" data-page="'+(currPage>1?currPage-1:1)+'"><i class="fa fa-angle-left"></i></a>';
    
    var startPage = Math.max(1, currPage - 2);
    var endPage = Math.min(maxPage, currPage + 2);
    if (endPage - startPage < 4) {
        if (startPage === 1) endPage = Math.min(maxPage, startPage + 4);
        else if (endPage === maxPage) startPage = Math.max(1, endPage - 4);
    }
    
    for (var i = startPage; i <= endPage; i++) {
        phtml += '<a href="#" class="'+(currPage==i?'active':'')+'" data-page="'+i+'">'+i+'</a>';
    }
    
    phtml += '<a href="#" class="'+(currPage==maxPage?'disabled':'')+'" data-page="'+(currPage<maxPage?currPage+1:maxPage)+'"><i class="fa fa-angle-right"></i></a>';
    phtml += '</div></div>';
    $('#ml-pagination-wrap').html(phtml);
    
    $('.mass-chk').each(function(){
        this.checked = selected.has(String($(this).val()));
        $(this).closest('tr').toggleClass('row-sel', selected.has(String($(this).val())));
    });
}

// Ensure JS Scope bindings
$(document).on('click', '#ml-pagination-wrap a', function(e){
    e.preventDefault();
    var p = $(this).data('page');
    if (p && !$(this).hasClass('disabled')) { currPage = parseInt(p); drawTable(); }
});



$('#ml-perpage').on('change', function(){ pageLen = parseInt(this.value); currPage=1; drawTable(); });
// Filters are submitted via the Apply button - no auto-submit on change
$('#btn-clear-filters').on('click', function(){ window.location.href = '<?php echo admin_url('meta_leads/submitted_leads'); ?>'; });
var sTimer;
$('#ml-search').on('input', function(){ clearTimeout(sTimer); sTimer = setTimeout(function(){ currPage=1; drawTable(); }, 200); });
drawTable();

// Selection Logic
$('#chk-all').on('change', function(){
    var on = this.checked;
    if(on){
        $(tbRows).filter(':visible').not('#ml-empty-row').each(function(){ selected.add(String($(this).attr('data-id'))); });
    } else { selected.clear(); }
    
    $(tbRows).filter(':visible').not('#ml-empty-row').each(function(){
        $(this).find('.mass-chk').prop('checked', on);
        $(this).toggleClass('row-sel', on);
    });
    syncBadge();
});

$(document).on('change', '.mass-chk', function(){
    var id = String(this.value);
    if(this.checked) selected.add(id); else selected.delete(id);
    $(this).closest('tr').toggleClass('row-sel', this.checked);
    syncHeader();
    syncBadge();
});

function syncHeader(){
    var cb = document.getElementById('chk-all');
    if(!cb) return;
    cb.checked = selected.size > 0 && selected.size >= ALL_IDS.length;
    cb.indeterminate = selected.size > 0 && selected.size < ALL_IDS.length;
}

function syncBadge(){
    var n = selected.size;
    var el = document.getElementById('sel-badge-wrap');
    if (el) { el.style.display = (n > 0) ? 'block' : 'none'; }
    var sn = document.getElementById('sel-n');
    if (sn) sn.textContent = n;
}

// Dropdown Logic
function initDd(b, m){
    var btn = document.getElementById(b), menu = document.getElementById(m);
    if(!btn || !menu) return;
    btn.onclick = function(e){
        e.stopPropagation();
        var wasOpen = btn.closest('.ml-dd').classList.contains('open');
        document.querySelectorAll('.ml-dd.open').forEach(el => el.classList.remove('open'));
        if(!wasOpen) btn.closest('.ml-dd').classList.add('open');
    }
    menu.onclick = function(e){
        e.stopPropagation();
    }
}
initDd('dd-bulk-toggle', 'dd-bulk-menu');
$('#btn-clear-filters-modal').on('click', function(){ window.location.href = '<?php echo admin_url('meta_leads/submitted_leads'); ?>'; });
document.onclick = () => document.querySelectorAll('.ml-dd.open').forEach(el => el.classList.remove('open'));

// View Lead Modal
var _currentLeadEl = null; // track which .view-lead element is currently open

$(document).on('click', '.view-lead', function(e){
    e.preventDefault();
    _currentLeadEl = this; // remember the element
    var lead = $(this).data('lead'), camp = $(this).data('camp');
    $('#lead-modal-title').text(lead.name);
    $('#lead-modal-sub').text(camp);
    $('#d-name').text(lead.name); $('#d-email').text(lead.email); $('#d-phone').text(lead.phone); $('#d-company').text(lead.company || '-');
    $('#d-camp').text(camp); $('#d-plat').text(lead.platform || 'Meta'); $('#d-date').text(lead.date_added);
    $('#d-status').html(lead.status === 'Added' ? '<span class="ml-pill ml-pill-added">Added</span>' : '<span class="ml-pill ml-pill-pending">Pending</span>');
    $('#d-raw').text(JSON.stringify(JSON.parse(lead.raw_data || '{}'), null, 2));
    // Notes — load existing note from the lead data (updated in DOM after each save)
    var existingNote = lead.note || '';
    $('#d-note').val(existingNote).attr('data-lead-id', lead.id);
    _lastSavedNote = existingNote; // reset baseline so we detect actual changes
    $('#note-save-indicator').css('opacity', 0);
    var footer = '<button class="ml-btn" data-dismiss="modal">Close</button>';
    if(lead.status !== 'Added') footer += '<a href="'+admin_url+'meta_leads/add_to_crm/'+lead.id+'" class="ml-btn ml-btn-primary" style="text-decoration:none;">Add to CRM</a>';
    $('#lead-modal-footer').html(footer);
    $('#leadModal').modal('show');
});

// Save note to server and update DOM so next popup open shows it
function saveNoteNow(lid, noteVal, showIndicator) {
    if (!lid) return;
    $.post(admin_url + 'meta_leads/save_lead_note', { id: lid, note: noteVal }, function(r){
        if (r && r.success) {
            // Update the data-lead attribute on the row element so note persists on next popup open
            if (_currentLeadEl) {
                var leadData = $(_currentLeadEl).data('lead');
                if (leadData) {
                    leadData.note = noteVal;
                    $(_currentLeadEl).data('lead', leadData);
                    // Also update the raw HTML attribute so data() stays in sync after modal close
                    var encoded = JSON.stringify(leadData)
                        .replace(/&/g,'&amp;').replace(/'/g,'&#039;').replace(/"/g,'&quot;');
                    _currentLeadEl.setAttribute('data-lead', encoded);
                }
            }
            if (showIndicator) {
                $('#note-save-indicator').html('<i class="fa fa-check" style="color:#059669;"></i> Saved').css('opacity', 1);
                setTimeout(function(){ $('#note-save-indicator').css('opacity', 0); }, 2500);
            }
        }
    }, 'json');
}

// Auto-save with 800ms debounce while typing
var _noteTimer = null;
var _lastSavedNote = '';

$('#d-note').on('input', function(){
    clearTimeout(_noteTimer);
    var $ta = $(this);
    var lid  = $ta.attr('data-lead-id');
    if (!lid) return;
    $('#note-save-indicator').css('opacity', 0);
    _noteTimer = setTimeout(function(){
        var note = $ta.val();
        if (note === _lastSavedNote) return; // nothing changed
        _lastSavedNote = note;
        saveNoteNow(lid, note, true);
    }, 800);
});

// Save immediately when modal closes (catches unsaved debounce state)
$('#leadModal').on('hide.bs.modal', function(){
    clearTimeout(_noteTimer);
    var $ta  = $('#d-note');
    var lid  = $ta.attr('data-lead-id');
    var note = $ta.val();
    if (lid && note !== _lastSavedNote) {
        _lastSavedNote = note;
        saveNoteNow(lid, note, false); // no indicator since modal is closing
    }
    _currentLeadEl = null;
});

// Focus styles for textarea
$('#d-note').on('focus', function(){ $(this).css('border-color','#2563eb'); });
$('#d-note').on('blur',  function(){ $(this).css('border-color','#e2e8f0'); });


// Bulk Action — triggered from dropdown
var _bulkAction = '';
window.triggerBulk = function(action) {
    var ids = Array.from(selected);
    if(ids.length === 0){ alert_float('warning','Please select at least one lead first.'); return; }
    _bulkAction = action;
    var isDelete = (action === 'mass_delete');
    var label = isDelete ? 'Delete Selected' : 'Add to CRM';
    var icon  = isDelete ? 'fa-trash-o' : 'fa-plus-circle';
    var color = isDelete ? '#dc2626' : '#059669';
    $('#bulk-modal-title').html('<i class="fa '+icon+'" style="color:'+color+';margin-right:8px;"></i>'+label);
    $('#bulk-modal-msg').html(
        isDelete
        ? '<strong style="color:#dc2626;">'+ids.length+' lead(s)</strong> will be permanently deleted. This cannot be undone.'
        : '<strong style="color:#059669;">'+ids.length+' lead(s)</strong> will be added to Perfex CRM Leads.'
    );
    $('#btn-confirm-bulk-run').text(label)
        .css({'background': isDelete ? '#dc2626' : '#2563eb', 'border-color': isDelete ? '#dc2626' : '#2563eb'});
    $('#bulkConfirmModal').modal('show');
};

$('#btn-confirm-bulk-run').off('click').on('click', function(){

    var ids = Array.from(selected);
    var action = _bulkAction;
    if(!action || ids.length === 0) return;

    var $btn = $(this);
    $btn.prop('disabled', true).text('Processing...');

    $.ajax({
        url: admin_url + 'meta_leads/bulk_action_process',
        type: 'POST',
        data: { action: action, ids: ids },
        dataType: 'json',
        success: function(r){
            $('#bulkConfirmModal').modal('hide');
            if(r.success) { 
                alert_float('success', r.message); 
                selected.clear(); 
                syncBadge(); 
                syncHeader(); 
                setTimeout(() => location.reload(), 1200);
            } else { 
                alert_float('danger', r.message || 'Action failed'); 
                $btn.prop('disabled', false).text('Confirm');
            }
        },

        error: function(xhr, status, err){
            alert_float('danger', 'Request failed ('+xhr.status+'): '+err);
            $btn.prop('disabled', false);
        }
    });
});

window.exportLeads = (fmt) => {
    var ids = Array.from(selected);
    location.href = admin_url + 'meta_leads/export?format='+fmt+(ids.length?'&ids='+ids.join(','):'');
};

})();
</script>
