<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
.ml-page *, .ml-page *::before, .ml-page *::after { box-sizing: border-box; }
.ml-page { padding: 40px; font-family: 'Inter', sans-serif !important; background: #f8fafc; min-height: 100vh; color: #1e293b; }
.ml-header-flex { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
.ml-title { font-size: 28px !important; font-weight: 700 !important; color: #0f172a !important; margin: 0 0 8px 0 !important; letter-spacing: -0.02em; }
.ml-subtitle { font-size: 15px; color: #64748b; margin: 0; line-height: 1.5; }

/* Grid Cards */
.ml-grid-3 { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 24px; margin-bottom: 32px; }
.ml-hero-card { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); border-radius: 12px; padding: 32px; color: #ffffff; box-shadow: 0 10px 15px -3px rgba(37,99,235,0.3); position: relative; overflow: hidden; }
.ml-hero-card::after { content: ''; position: absolute; right: -20px; top: -20px; width: 150px; height: 150px; background: rgba(255,255,255,0.1); border-radius: 50%; pointer-events: none; }
.ml-stat-card { background: #ffffff; border-radius: 12px; padding: 24px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); border: 1px solid #e2e8f0; }
.ml-stat-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px; }
.ml-stat-val { font-size: 32px; font-weight: 700; color: #0f172a; line-height: 1; margin:0;}

/* Buttons & Inputs */
.ml-btn { height: 40px; padding: 0 16px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: 1px solid #e2e8f0; background: #ffffff; color: #334155; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-family: inherit; transition: all 0.2s; white-space: nowrap; text-decoration: none !important; }
.ml-btn:hover { background: #f1f5f9; border-color: #cbd5e1; }
.ml-btn-danger { color: #dc2626 !important; border-color: #fecaca !important; }
.ml-btn-danger:hover { background: #fef2f2 !important; }

.ml-finput { height: 40px; padding: 0 12px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; font-size: 14px; color: #334155; outline: none; transition: 0.2s; font-family: inherit; }
.ml-finput:focus { border-color: #2563eb; }

/* Table */
.ml-table-card { background: #ffffff; border-radius: 16px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 32px; }
.ml-table-ctrls { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; background: #fafafa; }
.ml-search-box { position: relative; width: 300px; }
.ml-search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
.ml-search-box input { width: 100%; height: 38px; padding: 0 16px 0 36px; border: 1px solid #e2e8f0; border-radius: 8px; background: #ffffff; font-size: 14px; color: #334155; font-family: inherit; outline: none; }

.ml-table { width: 100%; border-collapse: collapse; }
.ml-table th { padding: 14px 24px; font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; text-align: left; background: #ffffff; border-bottom: 1px solid #f1f5f9; }
.ml-table td { padding: 16px 24px; font-size: 14px; color: #1e293b; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.ml-table tr:hover td { background: #f8fafc; }

/* Status Labels */
.ml-lbl { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 600; letter-spacing: 0.02em; }
.ml-lbl-ok { background: #dcfce7; color: #166534; }
.ml-lbl-fail { background: #fee2e2; color: #991b1b; }
.ml-lbl-dup { background: #fef9c3; color: #854d0e; }
.ml-lbl-info { background: #f1f5f9; color: #475569; }
.ml-meta-id { background: #eff6ff; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; letter-spacing: 0.05em; display: inline-block; }

/* Pagination */
.ml-pag-wrap { display: flex; justify-content: space-between; align-items: center; padding: 16px 24px; background: #fafafa; border-top: 1px solid #e2e8f0; }
.ml-pag-info { font-size: 13px; color: #64748b; font-weight: 500; }
.ml-pag-btns { display: flex; gap: 4px; }
.ml-pag-btns button { min-width: 32px; height: 32px; border-radius: 6px; background: #ffffff; border: 1px solid #e2e8f0; color: #334155; font-size: 13px; font-weight: 600; transition: 0.2s; cursor: pointer; }
.ml-pag-btns button:hover:not(.disabled) { background: #f1f5f9; }
.ml-pag-btns button.disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Responsive ── */
@media (max-width: 1024px) {
    .ml-grid-3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
    .ml-page { padding: 16px; }
    .ml-header-flex { flex-direction: column; align-items: flex-start; gap: 12px; }
    .ml-header-flex > div:last-child { display: flex; flex-wrap: wrap; gap: 8px; width: 100%; }
    .ml-grid-3 { grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
    .ml-hero-card { padding: 20px; }
    .ml-stat-card { padding: 16px; }
    .ml-stat-val { font-size: 24px; }
    .ml-table-ctrls { flex-direction: column; align-items: stretch; gap: 10px; padding: 12px 16px; }
    .ml-search-box { width: 100%; }
    .ml-finput { width: 100%; }
    .ml-table th, .ml-table td { padding: 12px 14px; font-size: 13px; }
    .ml-table th:nth-child(4),
    .ml-table td:nth-child(4) { display: none; } /* hide Form column on tablet */
    .ml-pag-wrap { flex-direction: column; gap: 10px; padding: 12px 16px; }
    .ml-btn { font-size: 13px; padding: 0 12px; height: 36px; }
}
@media (max-width: 480px) {
    .ml-grid-3 { grid-template-columns: 1fr; gap: 10px; }
    .ml-title { font-size: 22px !important; }
    .ml-subtitle { font-size: 13px; }
    /* Stack table as cards */
    .ml-table thead { display: none; }
    .ml-table, .ml-table tbody, .ml-table tr, .ml-table td { display: block; width: 100%; }
    .ml-table tr { border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 10px; padding: 12px; background: #fff; }
    .ml-table tr:hover td { background: none; }
    .ml-table td { border: none; padding: 4px 0; font-size: 13px; display: flex; align-items: flex-start; gap: 8px; }
    .ml-table td::before { content: attr(data-label); font-size: 10px; font-weight: 700; color: #94a3b8; text-transform: uppercase; min-width: 70px; flex-shrink: 0; padding-top: 2px; }
    .ml-table td:nth-child(4) { display: flex; } /* restore hidden column in card layout */
    .ml-pag-btns { flex-wrap: wrap; }
}
</style>

<?php
$history        = isset($history) && is_array($history) ? $history : [];
$current_status    = $current_status ?? '';
$current_date_from = $current_date_from ?? '';
$current_date_to   = $current_date_to ?? '';
$cnt_total = count($history);
$cnt_success = 0; $cnt_fail = 0; $cnt_dup = 0;
if (!empty($history)) {
    foreach ($history as $log) {
        $s = strtolower($log['status'] ?? '');
        if (strpos($s,'success') !== false)   $cnt_success++;
        elseif (strpos($s,'fail') !== false)  $cnt_fail++;
        elseif (strpos($s,'dup') !== false)   $cnt_dup++;
    }
}
?>

<div class="ml-page">
    <div class="ml-header-flex">
        <div>
            <h1 class="ml-title">Synchronization Logs</h1>
            <p class="ml-subtitle">Monitor real-time webhook payloads and delivery statuses.</p>
        </div>
        <div style="display:flex;gap:12px;">
            <form action="<?php echo admin_url('meta_leads/sync_history'); ?>" method="get" id="filter-form" style="display:flex;gap:8px;align-items:center;">
                <select name="status" class="ml-finput" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Success" <?php echo ($current_status=='Success')?'selected':''; ?>>Success</option>
                    <option value="Failure" <?php echo ($current_status=='Failure')?'selected':''; ?>>Failed</option>
                    <option value="Duplicate" <?php echo ($current_status=='Duplicate')?'selected':''; ?>>Duplicate</option>
                </select>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($current_date_from ?? ''); ?>" class="ml-finput" onchange="this.form.submit()">
                <?php if ($current_status || $current_date_from): ?>
                <a href="<?php echo admin_url('meta_leads/sync_history'); ?>" class="ml-btn ml-btn-danger" style="margin-left:4px;">Clear</a>
                <?php endif; ?>
            </form>
            <?php if (is_admin()): ?>
            <a href="<?php echo admin_url('meta_leads/clear_sync_logs'); ?>" onclick="return confirm('Purge all sync logs permanently?');" class="ml-btn ml-btn-danger">
                <i class="fa fa-trash-o"></i> Clear History
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- METRICS -->
    <div class="ml-grid-3">
        <div class="ml-hero-card">
            <div class="ml-stat-title" style="color:#bfdbfe;margin-bottom:8px;">Total Operations Logged</div>
            <div class="ml-stat-val" style="color:#ffffff;font-size:42px;"><?php echo number_format($cnt_total); ?></div>
            <div style="margin-top:20px;font-size:14px;color:#dbeafe;"><i class="fa fa-line-chart"></i> Currently tracking <?php echo $cnt_success; ?> successful syncs.</div>
        </div>
        <div class="ml-stat-card">
            <div class="ml-stat-title" style="color:#991b1b;">Failed Operations</div>
            <div class="ml-stat-val"><?php echo number_format($cnt_fail); ?></div>
            <div style="margin-top:10px;font-size:13px;color:#ef4444;">Require immediate review.</div>
        </div>
        <div class="ml-stat-card">
            <div class="ml-stat-title" style="color:#854d0e;">Duplicate Entries</div>
            <div class="ml-stat-val"><?php echo number_format($cnt_dup); ?></div>
            <div style="margin-top:10px;font-size:13px;color:#ca8a04;">Blocked automatically.</div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="ml-table-card">
        <div class="ml-table-ctrls">
            <div style="display:flex;align-items:center;gap:16px;">
                <div class="ml-search-box">
                    <i class="fa fa-search"></i>
                    <input type="text" id="ml-search" placeholder="Search payloads...">
                </div>
                <select id="ml-perpage" class="ml-finput" style="width:100px;">
                    <option value="25" selected>25 rows</option>
                    <option value="50">50 rows</option>
                    <option value="100">100 rows</option>
                    <option value="500">500 rows</option>
                </select>
            </div>
            <div>
                <button class="ml-btn" onclick="location.reload()" style="padding:0 12px;height:38px;"><i class="fa fa-refresh"></i> Refresh Log</button>
            </div>
        </div>

        <table class="ml-table" id="history-tbl">
            <thead>
                <tr>
                    <th style="width:160px;">Date & Time</th>
                    <th>Meta Source ID / Route</th>
                    <th style="width:120px;">Status</th>
                    <th>Execution Pipeline Message</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($history)):
                    foreach ($history as $log):
                        $s = strtolower($log['status'] ?? '');
                        if (strpos($s,'success') !== false) { $cls = 'ml-lbl-ok'; $lbl = 'SUCCESS'; }
                        elseif (strpos($s,'fail') !== false) { $cls = 'ml-lbl-fail'; $lbl = 'FAILED'; }
                        elseif (strpos($s,'dup') !== false) { $cls = 'ml-lbl-dup'; $lbl = 'DUPLICATE'; }
                        else { $cls = 'ml-lbl-info'; $lbl = 'INFO'; }
                        $cname = !empty($log['form_name']) ? $log['form_name'] : (!empty($log['page_name']) ? $log['page_name'] : 'Form '.$log['form_id']);
                ?>
                <tr>
                    <td data-label="Date">
                        <div style="font-weight:600;color:#1e293b;"><?php echo date('M d, Y',strtotime($log['date_added'])); ?></div>
                        <div style="font-size:12px;color:#64748b;font-family:monospace;"><?php echo date('H:i:s.v',strtotime($log['date_added'])); ?></div>
                    </td>
                    <td data-label="Form">
                        <div class="ml-meta-id"><?php echo htmlspecialchars($log['form_id'] ?? 'SYS_ROUTE'); ?></div>
                        <div style="font-weight:600;color:#1e293b;margin-top:6px;font-size:13px;"><?php echo htmlspecialchars($cname); ?></div>
                    </td>
                    <td data-label="Status"><span class="ml-lbl <?php echo $cls; ?>"><?php echo $lbl; ?></span></td>
                    <td data-label="Message">
                        <div style="font-family:monospace;font-size:13px;color:#475569;background:#f8fafc;padding:8px 12px;border-radius:6px;border:1px solid #f1f5f9;max-height:80px;overflow-y:auto;">
                            <?php echo htmlspecialchars($log['message']??'-'); ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr id="ml-empty-row"><td colspan="4" style="text-align:center;padding:60px 20px;color:#94a3b8;">
                    <i class="fa fa-history" style="font-size:48px;display:block;margin-bottom:16px;color:#cbd5e1;"></i>
                    <div style="font-size:16px;font-weight:500;color:#64748b;">No operation logs available</div>
                </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div id="ml-pagination-wrap"></div>
    </div>
</div>
<?php init_tail(); ?>

<script>
(function(){
'use strict';
var tbRows = $('#history-tbl tbody tr').not('#ml-empty-row').get();
var pageLen = parseInt($('#ml-perpage').val()) || 25;
var currPage = 1;

function drawTable() {
    var q = $('#ml-search').val().toLowerCase();
    var filtered = tbRows.filter(function(tr){ return $(tr).text().toLowerCase().indexOf(q) > -1; });
    var maxPage = Math.ceil(filtered.length / pageLen) || 1;
    if (currPage > maxPage) currPage = maxPage;
    var start = (currPage - 1) * pageLen;
    var end = start + pageLen;
    
    $(tbRows).hide();
    $(filtered.slice(start, end)).show();
    
    if (filtered.length === 0) {
        if ($('#ml-empty-row').length===0) $('#history-tbl tbody').append('<tr id="ml-empty-row"><td colspan="4" style="text-align:center;padding:60px;color:#9ca3af;"><i class="fa fa-history" style="font-size:32px;display:block;margin-bottom:12px;color:#d1d5db;"></i>No logs match search.</td></tr>');
        else $('#ml-empty-row').show();
    } else { $('#ml-empty-row').hide(); }
    
    var phtml = '<div class="ml-pag-wrap">';
    phtml += '<div class="ml-pag-info">Showing '+ (filtered.length>0 ? start+1 : 0) +' to '+ Math.min(filtered.length, end) +' of '+ filtered.length +' logs</div>';
    phtml += '<div class="ml-pag-btns">';
    phtml += '<button onclick="chgPg('+(currPage>1?currPage-1:1)+')" class="'+(currPage==1?'disabled':'')+'"><i class="fa fa-angle-left"></i> Prev</button>';
    phtml += '<button onclick="chgPg('+(currPage<maxPage?currPage+1:maxPage)+')" class="'+(currPage==maxPage?'disabled':'')+'">Next <i class="fa fa-angle-right"></i></button>';
    phtml += '</div></div>';
    $('#ml-pagination-wrap').html(phtml);
}

window.chgPg = function(p) { currPage = p; drawTable(); };
$('#ml-perpage').on('change', function(){ pageLen = parseInt(this.value); currPage=1; drawTable(); });
var sTimer;
$('#ml-search').on('input', function(){ clearTimeout(sTimer); sTimer = setTimeout(function(){ currPage=1; drawTable(); }, 200); });
drawTable();
})();
</script>
