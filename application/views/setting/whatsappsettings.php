<style>
    .whatsapp-badge {
        background-color: #25D366;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
    }
    .status-sent { color: #28a745; }
    .status-delivered { color: #17a2b8; }
    .status-read { color: #007bff; }
    .status-failed { color: #dc3545; }
    .status-pending { color: #ffc107; }
    .status-received { color: #6f42c1; }
    .nav-tabs > li.active > a {
        font-weight: bold;
    }
    .provider-logo {
        width: 24px;
        height: 24px;
        margin-right: 6px;
        vertical-align: middle;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">

            <!-- Left Column: Config Tabs -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-whatsapp" style="color: #25D366;"></i>
                            <?php echo $this->lang->line('whatsapp_messaging'); ?>
                        </h3>
                    </div>

                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_meta" data-toggle="tab">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" class="provider-logo" onerror="this.style.display='none'">
                                    Meta WhatsApp Official
                                </a>
                            </li>
                            <li>
                                <a href="#tab_twilio" data-toggle="tab">
                                    <img src="https://www.twilio.com/marketing/bundles/brand-guidelines/Twilio_Logo_Red.png" class="provider-logo" onerror="this.style.display='none'">
                                    Twilio
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <!-- =================== META TAB =================== -->
                            <div class="tab-pane active" id="tab_meta">

                                <?php if (isset($tables_missing) && $tables_missing) { ?>
                                    <div class="callout callout-danger" style="margin: 15px;">
                                        <h4><i class="fa fa-exclamation-triangle"></i> Database Tables Not Found</h4>
                                        <p>The required WhatsApp database tables have not been created yet.</p>
                                        <p>Please run the SQL migration file to set up the database:</p>
                                        <pre style="background:#f9f9f9; padding:10px; border:1px solid #ddd;">mysql -u root your_database_name &lt; whatsapp_integration.sql</pre>
                                        <p class="text-muted">File location: <code>application/../whatsapp_integration.sql</code></p>
                                    </div>
                                <?php } else { ?>

                                <form id="meta_config_form" method="post" accept-charset="utf-8">
                                    <div class="box-body">

                                        <div class="callout callout-info">
                                            <h4><i class="fa fa-info-circle"></i> Meta Developer Setup</h4>
                                            <p>These credentials come from your <a href="https://business.facebook.com/" target="_blank">Meta Business</a> dashboard:</p>
                                            <ol>
                                                <li>Create a Meta App with WhatsApp Business API added</li>
                                                <li>Get your <strong>Phone Number ID</strong> from WhatsApp > API Setup</li>
                                                <li>Get your <strong>Permanent Access Token</strong> (System User Token recommended)</li>
                                                <li>Get your <strong>Business Account ID</strong> from Business Settings</li>
                                                <li>Create message templates in WhatsApp Manager</li>
                                            </ol>
                                        </div>

                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('status'); ?></label>
                                            <div class="toggle-switch">
                                                <label>
                                                    <input type="checkbox" name="is_active" id="is_active" value="1" <?php echo (isset($config->is_active) && $config->is_active) ? 'checked' : ''; ?>>
                                                    <span class="label-informer">Enable WhatsApp Messaging</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Access Token <small class="req">*</small></label>
                                            <div class="input-group">
                                                <input type="password" name="access_token" id="access_token" class="form-control"
                                                    value="<?php echo isset($config->access_token) ? $config->access_token : ''; ?>"
                                                    placeholder="Paste your permanent access token">
                                                <span class="input-group-addon" style="cursor:pointer;" onclick="toggleTokenVisibility('access_token', 'token_eye_icon')">
                                                    <i class="fa fa-eye" id="token_eye_icon"></i>
                                                </span>
                                            </div>
                                            <small class="text-muted">Use a System User Token with whatsapp_business_messaging permission</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Registered Phone Number <small class="req">*</small></label>
                                            <input type="text" name="phone_number_id" id="phone_number_id" class="form-control"
                                                value="<?php echo isset($config->phone_number_id) ? $config->phone_number_id : ''; ?>"
                                                placeholder="e.g., 123456789012345">
                                            <small class="text-muted">Phone Number ID from Meta App Dashboard > WhatsApp > API Setup</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Language <small class="req">*</small></label>
                                            <select name="api_version" id="api_version" class="form-control">
                                                <option value="v21.0" <?php echo (isset($config->api_version) && $config->api_version == 'v21.0') ? 'selected' : ''; ?>>v21.0</option>
                                                <option value="v20.0" <?php echo (isset($config->api_version) && $config->api_version == 'v20.0') ? 'selected' : ''; ?>>v20.0</option>
                                                <option value="v19.0" <?php echo (isset($config->api_version) && $config->api_version == 'v19.0') ? 'selected' : ''; ?>>v19.0</option>
                                                <option value="v18.0" <?php echo (isset($config->api_version) && $config->api_version == 'v18.0') ? 'selected' : ''; ?>>v18.0</option>
                                            </select>
                                            <small class="text-muted">API Version</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Business Account ID</label>
                                            <input type="text" name="business_account_id" id="business_account_id" class="form-control"
                                                value="<?php echo isset($config->business_account_id) ? $config->business_account_id : ''; ?>"
                                                placeholder="e.g., 123456789012345">
                                        </div>

                                        <div class="form-group">
                                            <label>Webhook Verify Token</label>
                                            <input type="text" name="verify_token" id="verify_token" class="form-control"
                                                value="<?php echo isset($config->verify_token) ? $config->verify_token : ''; ?>"
                                                placeholder="Custom string for webhook verification">
                                            <small class="text-muted">Must match the verify token in Meta webhook settings</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Webhook URL</label>
                                            <div class="input-group">
                                                <input type="text" name="webhook_url" id="webhook_url" class="form-control" readonly
                                                    value="<?php echo site_url('admin/whatsappconfig/webhook'); ?>">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-default btn-flat" onclick="copyToClipboard(document.getElementById('webhook_url').value)">
                                                        <i class="fa fa-copy"></i> Copy
                                                    </button>
                                                </span>
                                            </div>
                                            <small class="text-muted">
                                                Subscribe to: <code>messages</code> and <code>message_status</code> fields
                                            </small>
                                        </div>

                                    </div>
                                    <div class="box-footer">
                                        <button type="button" class="btn btn-success" onclick="saveMetaConfig()">
                                            <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                                        </button>
                                        <button type="button" class="btn btn-info" id="test_meta_btn" onclick="testMetaConnection()">
                                            <i class="fa fa-plug"></i> Test Connection
                                        </button>
                                        <span id="test_meta_result"></span>
                                        <a href="https://business.facebook.com/" target="_blank" class="btn btn-link pull-right" style="color:#1877F2;">
                                            <i class="fa fa-external-link"></i> Meta Business Manager
                                        </a>
                                    </div>
                                </form>
                                <?php } ?>

                            </div><!-- ./tab_meta -->

                            <!-- =================== TWILIO TAB =================== -->
                            <div class="tab-pane" id="tab_twilio">

                                <?php if (isset($tables_missing) && $tables_missing) { ?>
                                    <div class="callout callout-danger" style="margin: 15px;">
                                        <h4><i class="fa fa-exclamation-triangle"></i> Database Tables Not Found</h4>
                                        <p>Run the SQL migration first.</p>
                                    </div>
                                <?php } else { ?>

                                <form id="twilio_config_form" method="post" accept-charset="utf-8">
                                    <div class="box-body">

                                        <div class="callout callout-info">
                                            <h4><i class="fa fa-info-circle"></i> Twilio WhatsApp Setup</h4>
                                            <p>Use your Twilio account to send WhatsApp messages via Twilio's WhatsApp Business API:</p>
                                            <ol>
                                                <li>Create a Twilio account at <a href="https://www.twilio.com/" target="_blank">twilio.com</a></li>
                                                <li>Enable the WhatsApp Sandbox or get a Twilio phone number with WhatsApp</li>
                                                <li>Get your <strong>Account SID</strong> and <strong>Auth Token</strong> from the dashboard</li>
                                                <li>Enter your <strong>Twilio WhatsApp Phone Number</strong> in E.164 format</li>
                                            </ol>
                                        </div>

                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('status'); ?></label>
                                            <div class="toggle-switch">
                                                <label>
                                                    <input type="checkbox" name="twilio_is_active" id="twilio_is_active" value="1" <?php echo (isset($config->twilio_is_active) && $config->twilio_is_active) ? 'checked' : ''; ?>>
                                                    <span class="label-informer">Enable Twilio WhatsApp Messaging</span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Twilio Account SID <small class="req">*</small></label>
                                            <input type="text" name="twilio_account_sid" id="twilio_account_sid" class="form-control"
                                                value="<?php echo isset($config->twilio_account_sid) ? $config->twilio_account_sid : ''; ?>"
                                                placeholder="e.g., ACd4d1c4644724f5e0a7598fc570ea09b">
                                            <small class="text-muted">Found in your Twilio Console dashboard</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Authentication Token <small class="req">*</small></label>
                                            <div class="input-group">
                                                <input type="password" name="twilio_auth_token" id="twilio_auth_token" class="form-control"
                                                    value="<?php echo isset($config->twilio_auth_token) ? $config->twilio_auth_token : ''; ?>"
                                                    placeholder="Paste your auth token">
                                                <span class="input-group-addon" style="cursor:pointer;" onclick="toggleTokenVisibility('twilio_auth_token', 'twilio_token_eye_icon')">
                                                    <i class="fa fa-eye" id="twilio_token_eye_icon"></i>
                                                </span>
                                            </div>
                                            <small class="text-muted">Keep this secret. Click the eye icon to show/hide.</small>
                                        </div>

                                        <div class="form-group">
                                            <label>Registered Phone Number <small class="req">*</small></label>
                                            <input type="text" name="twilio_phone_number" id="twilio_phone_number" class="form-control"
                                                value="<?php echo isset($config->twilio_phone_number) ? $config->twilio_phone_number : ''; ?>"
                                                placeholder="e.g., +14155238886">
                                            <small class="text-muted">Your Twilio WhatsApp number in E.164 format</small>
                                        </div>

                                    </div>
                                    <div class="box-footer">
                                        <button type="button" class="btn btn-success" onclick="saveTwilioConfig()">
                                            <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                                        </button>
                                        <button type="button" class="btn btn-info" id="test_twilio_btn" onclick="testTwilioConnection()">
                                            <i class="fa fa-plug"></i> Test Connection
                                        </button>
                                        <span id="test_twilio_result"></span>
                                        <a href="https://console.twilio.com/" target="_blank" class="btn btn-link pull-right" style="color:#F22F46;">
                                            <i class="fa fa-external-link"></i> Twilio Console
                                        </a>
                                    </div>
                                </form>
                                <?php } ?>

                            </div><!-- ./tab_twilio -->

                        </div><!-- ./tab-content -->
                    </div><!-- ./nav-tabs-custom -->
                </div><!-- ./box -->
            </div>

            <!-- Right Column: Recent Messages & Stats -->
            <div class="col-md-4">
                <!-- Quick Stats -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-bar-chart"></i> Quick Stats</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="fa fa-whatsapp"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Today</span>
                                        <span class="info-box-number" id="today_count">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Pending</span>
                                        <span class="info-box-number" id="pending_count">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-center">
                                <div class="info-box">
                                    <span class="info-box-icon bg-blue"><i class="fa fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Sent</span>
                                        <span class="info-box-number" id="sent_count">-</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="info-box">
                                    <span class="info-box-icon bg-red"><i class="fa fa-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Failed</span>
                                        <span class="info-box-number" id="failed_count">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Messages -->
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-history"></i> Recent Messages</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('admin/whatsappconfig/messages'); ?>" class="btn btn-box-tool btn-sm">
                                View All
                            </a>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 400px; overflow-y: auto;">
                        <?php if (!empty($recent_messages)) { ?>
                            <?php foreach ($recent_messages as $msg) { ?>
                                <div class="media" style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                                    <div class="media-left">
                                        <?php if ($msg->message_type == 'outgoing') { ?>
                                            <i class="fa fa-arrow-right text-blue" style="font-size: 18px;"></i>
                                        <?php } else { ?>
                                            <i class="fa fa-arrow-left text-purple" style="font-size: 18px;"></i>
                                        <?php } ?>
                                    </div>
                                    <div class="media-body">
                                        <small class="text-muted">
                                            <?php echo $msg->recipient_phone; ?>
                                            <?php if ($msg->recipient_name) { ?>
                                                — <?php echo $msg->recipient_name; ?>
                                            <?php } ?>
                                        </small>
                                        <br>
                                        <small>
                                            <?php if ($msg->template_name && $msg->template_name != 'text_message') { ?>
                                                <span class="whatsapp-badge"><?php echo $msg->template_name; ?></span>
                                            <?php } ?>
                                            <span class="status-<?php echo $msg->status; ?>">
                                                <i class="fa fa-circle" style="font-size: 8px;"></i> <?php echo ucfirst($msg->status); ?>
                                            </span>
                                        </small>
                                        <br>
                                        <small class="text-muted"><?php echo date('d M Y h:i A', strtotime($msg->created_at)); ?></small>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <p class="text-muted text-center">No messages sent yet</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function toggleTokenVisibility(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function copyToClipboard(text) {
    var tempInput = document.createElement('input');
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);
}

function saveMetaConfig() {
    var btn = event.target;
    $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    $.ajax({
        url: '<?php echo site_url('admin/whatsappconfig/save'); ?>',
        type: 'POST',
        data: $('#meta_config_form').serialize(),
        dataType: 'json',
        success: function(response) {
            $(btn).prop('disabled', false).html('<i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>');
            if (response.status == "success") {
                successMsg(response.message);
            } else {
                var message = "";
                $.each(response.error, function(index, value) { message += value; });
                errorMsg(message);
            }
        },
        error: function() {
            $(btn).prop('disabled', false).html('<i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>');
            errorMsg('Request failed');
        }
    });
}

function saveTwilioConfig() {
    var btn = event.target;
    $(btn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
    $.ajax({
        url: '<?php echo site_url('admin/whatsappconfig/savetwilio'); ?>',
        type: 'POST',
        data: $('#twilio_config_form').serialize(),
        dataType: 'json',
        success: function(response) {
            $(btn).prop('disabled', false).html('<i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>');
            if (response.status == "success") {
                successMsg(response.message);
            } else {
                var message = "";
                $.each(response.error, function(index, value) { message += value; });
                errorMsg(message);
            }
        },
        error: function() {
            $(btn).prop('disabled', false).html('<i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>');
            errorMsg('Request failed');
        }
    });
}

function testMetaConnection() {
    var btn = document.getElementById('test_meta_btn');
    var resultSpan = document.getElementById('test_meta_result');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
    resultSpan.innerHTML = '';

    $.ajax({
        url: '<?php echo site_url('admin/whatsappconfig/testconnection'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plug"></i> Test Connection';
            if (response.success) {
                resultSpan.innerHTML = ' <span class="label label-success"><i class="fa fa-check"></i> ' + response.message + '</span>';
            } else {
                resultSpan.innerHTML = ' <span class="label label-danger"><i class="fa fa-times"></i> ' + response.message + '</span>';
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plug"></i> Test Connection';
            resultSpan.innerHTML = ' <span class="label label-danger"><i class="fa fa-times"></i> Connection failed</span>';
        }
    });
}

function testTwilioConnection() {
    var btn = document.getElementById('test_twilio_btn');
    var resultSpan = document.getElementById('test_twilio_result');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Testing...';
    resultSpan.innerHTML = '';

    $.ajax({
        url: '<?php echo site_url('admin/whatsappconfig/testtwilio'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plug"></i> Test Connection';
            if (response.success) {
                resultSpan.innerHTML = ' <span class="label label-success"><i class="fa fa-check"></i> ' + response.message + '</span>';
            } else {
                resultSpan.innerHTML = ' <span class="label label-danger"><i class="fa fa-times"></i> ' + response.message + '</span>';
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-plug"></i> Test Connection';
            resultSpan.innerHTML = ' <span class="label label-danger"><i class="fa fa-times"></i> Connection failed</span>';
        }
    });
}

// Load stats on page load
$(document).ready(function() {
    <?php if (!isset($tables_missing) || !$tables_missing) { ?>
    $.ajax({
        url: '<?php echo site_url('admin/whatsappconfig/getStats'); ?>',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.today !== undefined) $('#today_count').text(data.today);
            if (data.sent !== undefined) $('#sent_count').text(data.sent);
            if (data.pending !== undefined) $('#pending_count').text(data.pending);
            if (data.failed !== undefined) $('#failed_count').text(data.failed);
        }
    });
    <?php } ?>
});
</script>
