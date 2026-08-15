<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
.ml-page *, .ml-page *::before, .ml-page *::after { box-sizing: border-box; }
.ml-page { padding: 40px; font-family: 'Inter', sans-serif !important; background: #f8fafc; min-height: 100vh; color: #1e293b; }
.ml-title-wrap { margin-bottom: 32px; }
.ml-title { font-size: 28px !important; font-weight: 700 !important; color: #0f172a !important; margin: 0 0 8px 0 !important; letter-spacing: -0.02em; }
.ml-subtitle { font-size: 15px; color: #64748b; margin: 0; line-height: 1.5; max-width: 800px; }

.ml-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 32px; }
@media(max-width: 1024px){ .ml-grid { grid-template-columns: 1fr; } }

.ml-card { background: #ffffff; border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05); padding: 32px; margin-bottom: 32px; position: relative; overflow: hidden; }

/* Facebook Connect Block */
.ml-fb-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 32px; }
.ml-fb-icon-text { display: flex; align-items: center; gap: 16px; }
.ml-fb-icon { width: 56px; height: 56px; background: #1877F2; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; box-shadow: 0 4px 12px rgba(24,119,242,0.3); }
.ml-fb-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
.ml-fb-sub { font-size: 14px; color: #64748b; }

.ml-btn-fb { background: #1877F2; color: #ffffff !important; border: none; padding: 14px 24px; font-size: 15px; font-weight: 700; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none !important; transition: background 0.2s; }
.ml-btn-fb:hover { background: #145dbf; }

.ml-input-group { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.ml-fgroup label { font-size: 12px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; display: block; }
.ml-finput { width: 100%; height: 44px; padding: 0 16px; background: #f1f5f9 !important; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 15px; color: #334155; font-weight: 500; }
.ml-finput:focus { outline: none; border-color: #94a3b8; }
.ml-finput.copyable { background: #ffffff !important; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none; color: #2563eb; font-weight: 600; font-size: 13px; font-family: monospace; }

/* Copy Wrapper */
.ml-copy-wrap { display: flex; align-items: stretch; margin-bottom: 24px; }
.ml-copy-btn { background: #e2e8f0; border: 1px solid #e2e8f0; border-left: none; border-top-right-radius: 8px; border-bottom-right-radius: 8px; width: 44px; display: flex; align-items: center; justify-content: center; color: #475569; cursor: pointer; transition: all 0.2s; }
.ml-copy-btn:hover { background: #cbd5e1; color: #0f172a; }

/* Webhook Info */
.ml-wh-title { font-size: 18px; font-weight: 700; color: #0f172a; margin-bottom: 8px; }
.ml-wh-desc { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.6; }

/* Guide */
.ml-guide { margin-top: 32px; }
.ml-guide-title { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.ml-guide-step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 16px; font-size: 13.5px; color: #475569; line-height: 1.5; }
.ml-step-no { width: 20px; height: 20px; border-radius: 50%; background: #e0e7ff; color: #4f46e5; font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }

/* Live Status Mock */
.ml-live-card { text-align: center; padding: 40px 20px; }
.ml-pulse-icon { width: 64px; height: 64px; background: #ecfdf5; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; color: #059669; font-size: 32px; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(5,150,105,0.1); }
.ml-live-title { font-size: 20px; font-weight: 700; color: #0f172a; margin-bottom: 12px; }
.ml-live-badge { display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; background: #ecfdf5; color: #059669; border-radius: 20px; font-weight: 700; font-size: 12px; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 16px; }
.ml-dot { width: 6px; height: 6px; background: #059669; border-radius: 50%; display: inline-block; animation: pulse 2s infinite; }
@keyframes pulse { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.5); } 100% { opacity: 1; transform: scale(1); } }
.ml-live-desc { font-size: 14px; color: #64748b; line-height: 1.6; max-width: 300px; margin: 0 auto; }

.ml-save-btn { background: #2563eb; color: #ffffff !important; border: none; padding: 14px 24px; font-size: 15px; font-weight: 600; border-radius: 8px; cursor: pointer; width: 100%; margin-top: 24px; transition: background 0.2s; }
.ml-save-btn:hover { background: #1d4ed8; }

.ml-danger-btn { background: #fef2f2; color: #dc2626; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; border: 1px solid #fca5a5; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
.ml-danger-btn:hover { background: #dc2626; color: #ffffff; border-color: #dc2626; text-decoration: none; }

/* ── Responsive ── */
@media (max-width: 1024px) {
    .ml-grid { grid-template-columns: 1fr; }
}
@media (max-width: 768px) {
    .ml-page { padding: 16px !important; }
    .ml-title-wrap { margin-bottom: 20px; }
    .ml-title { font-size: 24px !important; }
    .ml-subtitle { font-size: 14px; }
    .ml-card { padding: 20px; border-radius: 12px; }
    .ml-fb-header { flex-direction: column; gap: 16px; align-items: flex-start; }
    .ml-input-group { grid-template-columns: 1fr; }
    .ml-guide-step { font-size: 13px; }
    .ml-live-card { padding: 24px 16px; }
    .ml-save-btn, .ml-danger-btn { font-size: 14px; padding: 12px 20px; }
}
@media (max-width: 480px) {
    .ml-title { font-size: 20px !important; }
    .ml-subtitle { font-size: 13px; }
    .ml-fb-icon { width: 44px; height: 44px; font-size: 22px; }
    .ml-fb-title { font-size: 16px; }
    .ml-card { padding: 16px; margin-bottom: 16px; }
    .ml-copy-wrap { flex-direction: column; }
    .ml-copy-wrap .ml-finput { border-radius: 8px; border-right: 1px solid #e2e8f0; }
    .ml-copy-btn { width: 100%; height: 40px; border-radius: 8px; border-left: 1px solid #e2e8f0; border-top: none; }
    .ml-btn-fb { width: 100%; }
    .ml-save-btn { font-size: 14px; }
}
</style>

<div class="ml-page">
    <div class="ml-title-wrap">
        <h1 class="ml-title">Meta Lead Ads Sync</h1>
        <p class="ml-subtitle">Configure your Facebook integration to automatically pull leads from your lead forms directly into Perfex CRM. Ensure your App credentials are up to date.</p>
    </div>
    
    <?php echo form_open(admin_url('meta_leads/settings'), ['id'=>'settings_form']); ?>
    <div class="ml-grid">
        <!-- LEFT COLUMN -->
        <div>
            <div class="ml-card">
                <div class="ml-fb-header">
                    <div class="ml-fb-icon-text">
                        <div class="ml-fb-icon"><i class="fa-brands fa-meta"></i></div>
                        <div>
                            <div class="ml-fb-title">Facebook Connectivity</div>
                            <div class="ml-fb-sub">Manage your App Credentials</div>
                        </div>
                    </div>
                </div>

                <div class="ml-input-group">
                    <div class="ml-fgroup">
                        <label>App ID</label>
                        <input type="text" name="meta_leads_app_id" class="ml-finput" value="<?php echo get_option('meta_leads_app_id'); ?>" placeholder="Enter Meta App ID">
                    </div>
                    <div class="ml-fgroup">
                        <label>App Secret</label>
                        <input type="password" name="meta_leads_app_secret" class="ml-finput" value="<?php echo get_option('meta_leads_app_secret'); ?>" placeholder="••••••••••••••••">
                    </div>
                </div>
                
                <button type="submit" class="ml-save-btn">Save Main Credentials</button>
                <a href="<?php echo admin_url('meta_leads/oauth'); ?>" class="ml-btn-fb" style="width: 100%; margin-top:24px;">
                    <i class="fa-brands fa-facebook" style="font-size:22px;"></i> Connect with Facebook
                </a>
            </div>

            <!-- Optional: Global/Default Automation Settings mock (can be replaced by specific form mapping) -->
            <div class="ml-card ml-live-card">
                <div class="ml-pulse-icon"><i class="fa fa-crosshairs"></i></div>
                <div class="ml-live-title">Live Status</div>
                <?php if (get_option('meta_leads_access_token')): ?>
                    <div class="ml-live-badge"><span class="ml-dot"></span> CONNECTED & ACTIVE</div>
                    <p class="ml-live-desc">Meta webhook connection is healthy. Your API credentials are fully authenticated.</p>
                <?php else: ?>
                    <div class="ml-live-badge" style="background:#fef2f2; color:#b91c1c;"><span class="ml-dot" style="background:#b91c1c;"></span> AUTHENTICATION REQUIRED</div>
                    <p class="ml-live-desc">Please click "Connect with Facebook" above to generate a new OAuth token.</p>
                <?php endif; ?>
                
                <div style="margin-top:40px; border-top: 1px solid #e2e8f0; padding-top: 32px; text-align:center;">
                    <button type="button" class="ml-danger-btn" data-toggle="modal" data-target="#resetModuleModal">
                        <i class="fa fa-exclamation-triangle"></i> Hard Reset Module Variables
                    </button>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN -->
        <div>
            <div class="ml-card" style="height: 100%;">
                <h3 class="ml-wh-title">Webhook Configuration</h3>
                <p class="ml-wh-desc">Use these details to set up the Real-Time Updates in your Facebook App's Webhook settings.</p>

                <div class="ml-fgroup">
                    <label>Callback URL</label>
                    <div class="ml-copy-wrap">
                        <input type="text" readonly class="ml-finput copyable" value="<?php echo site_url('meta_leads/webhook'); ?>">
                        <button type="button" class="ml-copy-btn" title="Copy"><i class="fa fa-copy"></i></button>
                    </div>
                </div>

                <div class="ml-fgroup">
                    <label>Verify Token</label>
                    <div class="ml-copy-wrap">
                        <input type="text" readonly class="ml-finput copyable" value="<?php echo get_option('meta_leads_verify_token'); ?>">
                        <button type="button" class="ml-copy-btn" title="Copy"><i class="fa fa-copy"></i></button>
                    </div>
                </div>

                <div class="ml-fgroup" style="margin-top:20px;">
                    <label>OAuth Redirect URI <span style="font-weight:400;color:#94a3b8;float:right;text-transform:none;">(For FB Login Tab)</span></label>
                    <div class="ml-copy-wrap" style="margin-bottom:0;">
                        <input type="text" readonly class="ml-finput copyable" value="<?php echo admin_url('meta_leads/oauth_callback'); ?>">
                        <button type="button" class="ml-copy-btn" title="Copy"><i class="fa fa-copy"></i></button>
                    </div>
                </div>

                <div class="ml-guide">
                    <div class="ml-guide-title"><i class="fa fa-info-circle text-muted"></i> Setup Guide</div>
                    <div class="ml-guide-step">
                        <div class="ml-step-no">1</div>
                        <div>Create a Meta App and select "Business" as the app type in the developer dashboard.</div>
                    </div>
                    <div class="ml-guide-step">
                        <div class="ml-step-no">2</div>
                        <div>Add the "Facebook Login for Business" product to handle authentication.</div>
                    </div>
                    <div class="ml-guide-step">
                        <div class="ml-step-no">3</div>
                        <div>Configure <b>Webhooks</b> to subscribe to the <code>leadgen</code> field for Pages.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>

</div>

<!-- Reset Modal -->
<div class="modal fade" id="resetModuleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:12px; border:none; overflow:hidden;">
            <div class="modal-header" style="background:#fef2f2; border-bottom:1px solid #fee2e2; padding:16px 20px;">
                <button type="button" class="close" data-dismiss="modal" style="margin-top:-2px;">&times;</button>
                <h4 class="modal-title" style="color:#991b1b; font-weight:700; font-size:16px;">Confirm Full Reset</h4>
            </div>
            <div class="modal-body" style="padding:24px;">
                <p style="color:#4b5563; font-size:14px; line-height:1.6; margin-bottom:24px;">This will erase all leads, linked campaigns, and Facebook credentials. This action cannot be undone.</p>
                <?php echo form_open(admin_url('meta_leads/reset_module')); ?>
                <input type="hidden" name="confirm_reset" value="1">
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, Reset Everything</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>

    </div>
</div>
<?php init_tail(); ?>
<script>
$('.ml-copy-btn').on('click', function(){
    var input = $(this).closest('.ml-copy-wrap').find('input');
    input.select();
    document.execCommand("copy");
    $(this).html('<i class="fa fa-check" style="color:#059669;"></i>');
    var el = $(this);
    setTimeout(function() { el.html('<i class="fa fa-copy"></i>'); }, 2000);
});
</script>
