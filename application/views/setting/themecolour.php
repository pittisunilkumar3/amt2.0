<div class="content-wrapper" style="min-height: 348px;">
    <section class="content">
        <div class="row">

            <?php $this->load->view('setting/_settingmenu'); ?>

            <div class="col-md-10">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-palette"></i> <?php echo $this->lang->line('theme_colour'); ?></h3>
                    </div>
                    <div class="">
                        <form role="form" id="themecolour_form" method="post">
                            <input type="hidden" name="sch_id" value="<?php echo $result->id; ?>">
                            <div class="box-body">

                                <div class="col-md-12">
                                    <p class="text-muted" style="margin-bottom: 20px; font-size: 13px;">
                                        <i class="fa fa-info-circle"></i> <?php echo $this->lang->line('theme_colour_help'); ?>
                                    </p>
                                </div>

                                <!-- ===== HEADER SECTION ===== -->
                                <div class="col-md-12">
                                    <div class="box box-solid" style="border-top: 3px solid #367fa9; margin-bottom: 15px;">
                                        <div class="box-header" style="padding: 8px 12px; background: #f8f8f8;">
                                            <h4 class="box-title" style="font-size: 14px; font-weight: bold;"><i class="fa fa-header"></i> <?php echo $this->lang->line('header_settings'); ?></h4>
                                        </div>
                                        <div class="box-body" style="padding: 12px;">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('header_colour'); ?></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon colour-preview-box" id="header_preview" style="min-width: 40px; height: 36px; background-color: <?php echo $result->theme_header_colour ? $result->theme_header_colour : '#367fa9'; ?>;"></span>
                                                            <input type="color" name="theme_header_colour" id="theme_header_colour" class="form-control" style="height: 36px;" value="<?php echo $result->theme_header_colour ? $result->theme_header_colour : '#367fa9'; ?>">
                                                            <span class="input-group-addon colour-hex-box" style="min-width: 80px; font-family: monospace; font-size: 13px; cursor: pointer;" onclick="copyHex(this)" title="<?php echo $this->lang->line('click_to_copy'); ?>"><?php echo $result->theme_header_colour ? $result->theme_header_colour : '#367fa9'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>
                                                            <?php echo $this->lang->line('header_gradient_end'); ?>
                                                            <small style="color: #999;">(<?php echo $this->lang->line('optional_same_disable'); ?>)</small>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon colour-preview-box" id="header_gradient_preview" style="min-width: 40px; height: 36px; background-color: <?php echo $result->theme_header_gradient ? $result->theme_header_gradient : '#367fa9'; ?>"></span>
                                                            <input type="color" name="theme_header_gradient" id="theme_header_gradient" class="form-control" style="height: 36px;" value="<?php echo $result->theme_header_gradient ? $result->theme_header_gradient : '#367fa9'; ?>">
                                                            <span class="input-group-addon colour-hex-box" style="min-width: 80px; font-family: monospace; font-size: 13px; cursor: pointer;" onclick="copyHex(this)" title="<?php echo $this->lang->line('click_to_copy'); ?>"><?php echo $result->theme_header_gradient ? $result->theme_header_gradient : '#367fa9'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="padding-top: 25px;">
                                                    <button type="button" class="btn btn-default btn-sm match-header-btn" title="<?php echo $this->lang->line('match_gradient_to_colour'); ?>">
                                                        <i class="fa fa-link"></i> <?php echo $this->lang->line('match'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== SIDEBAR SECTION ===== -->
                                <div class="col-md-12">
                                    <div class="box box-solid" style="border-top: 3px solid #222d32; margin-bottom: 15px;">
                                        <div class="box-header" style="padding: 8px 12px; background: #f8f8f8;">
                                            <h4 class="box-title" style="font-size: 14px; font-weight: bold;"><i class="fa fa-bars"></i> <?php echo $this->lang->line('sidebar_settings'); ?></h4>
                                        </div>
                                        <div class="box-body" style="padding: 12px;">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('sidebar_colour'); ?></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon colour-preview-box" id="sidebar_preview" style="min-width: 40px; height: 36px; background-color: <?php echo $result->theme_sidebar_colour ? $result->theme_sidebar_colour : '#222d32'; ?>;"></span>
                                                            <input type="color" name="theme_sidebar_colour" id="theme_sidebar_colour" class="form-control" style="height: 36px;" value="<?php echo $result->theme_sidebar_colour ? $result->theme_sidebar_colour : '#222d32'; ?>">
                                                            <span class="input-group-addon colour-hex-box" style="min-width: 80px; font-family: monospace; font-size: 13px; cursor: pointer;" onclick="copyHex(this)" title="<?php echo $this->lang->line('click_to_copy'); ?>"><?php echo $result->theme_sidebar_colour ? $result->theme_sidebar_colour : '#222d32'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>
                                                            <?php echo $this->lang->line('sidebar_gradient_end'); ?>
                                                            <small style="color: #999;">(<?php echo $this->lang->line('optional_same_disable'); ?>)</small>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon colour-preview-box" id="sidebar_gradient_preview" style="min-width: 40px; height: 36px; background-color: <?php echo $result->theme_sidebar_gradient ? $result->theme_sidebar_gradient : '#222d32'; ?>"></span>
                                                            <input type="color" name="theme_sidebar_gradient" id="theme_sidebar_gradient" class="form-control" style="height: 36px;" value="<?php echo $result->theme_sidebar_gradient ? $result->theme_sidebar_gradient : '#222d32'; ?>">
                                                            <span class="input-group-addon colour-hex-box" style="min-width: 80px; font-family: monospace; font-size: 13px; cursor: pointer;" onclick="copyHex(this)" title="<?php echo $this->lang->line('click_to_copy'); ?>"><?php echo $result->theme_sidebar_gradient ? $result->theme_sidebar_gradient : '#222d32'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="padding-top: 25px;">
                                                    <button type="button" class="btn btn-default btn-sm match-sidebar-btn" title="<?php echo $this->lang->line('match_gradient_to_colour'); ?>">
                                                        <i class="fa fa-link"></i> <?php echo $this->lang->line('match'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== BODY BACKGROUND SECTION ===== -->
                                <div class="col-md-12">
                                    <div class="box box-solid" style="border-top: 3px solid #ecf0f5; margin-bottom: 15px;">
                                        <div class="box-header" style="padding: 8px 12px; background: #f8f8f8;">
                                            <h4 class="box-title" style="font-size: 14px; font-weight: bold;"><i class="fa fa-desktop"></i> <?php echo $this->lang->line('body_settings'); ?></h4>
                                        </div>
                                        <div class="box-body" style="padding: 12px;">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label>
                                                            <?php echo $this->lang->line('body_bg_colour'); ?>
                                                            <small style="color: #999;">(<?php echo $this->lang->line('leave_blank_default'); ?>)</small>
                                                        </label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon colour-preview-box" id="bodybg_preview" style="min-width: 40px; height: 36px; background-color: <?php echo $result->theme_body_bg ? $result->theme_body_bg : '#ecf0f5'; ?>; border: 1px solid #ddd;"></span>
                                                            <input type="color" name="theme_body_bg" id="theme_body_bg" class="form-control" style="height: 36px;" value="<?php echo $result->theme_body_bg ? $result->theme_body_bg : '#ecf0f5'; ?>">
                                                            <span class="input-group-addon colour-hex-box" style="min-width: 80px; font-family: monospace; font-size: 13px; cursor: pointer;" onclick="copyHex(this)" title="<?php echo $this->lang->line('click_to_copy'); ?>"><?php echo $result->theme_body_bg ? $result->theme_body_bg : '#ecf0f5'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== ACCENT GRADIENT SECTION ===== -->
                                <div class="col-md-12">
                                    <div class="box box-solid" style="border-top: 3px solid #f33057; margin-bottom: 15px;">
                                        <div class="box-header" style="padding: 8px 12px; background: #f8f8f8;">
                                            <h4 class="box-title" style="font-size: 14px; font-weight: bold;">
                                                <i class="fa fa-paint-brush"></i> <?php echo $this->lang->line('accent_gradient'); ?>
                                                <small style="color: #999; font-weight: normal; font-size: 11px;">(<?php echo $this->lang->line('accent_gradient_help'); ?>)</small>
                                            </h4>
                                        </div>
                                        <div class="box-body" style="padding: 12px;">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('gradient_start_colour'); ?></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon colour-preview-box" id="accent_start_preview" style="min-width: 40px; height: 36px; background-color: <?php echo $result->theme_accent_start ? $result->theme_accent_start : '#f33057'; ?>;"></span>
                                                            <input type="color" name="theme_accent_start" id="theme_accent_start" class="form-control" style="height: 36px;" value="<?php echo $result->theme_accent_start ? $result->theme_accent_start : '#f33057'; ?>">
                                                            <span class="input-group-addon colour-hex-box" style="min-width: 80px; font-family: monospace; font-size: 13px; cursor: pointer;" onclick="copyHex(this)" title="<?php echo $this->lang->line('click_to_copy'); ?>"><?php echo $result->theme_accent_start ? $result->theme_accent_start : '#f33057'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label><?php echo $this->lang->line('gradient_end_colour'); ?></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon colour-preview-box" id="accent_end_preview" style="min-width: 40px; height: 36px; background-color: <?php echo $result->theme_accent_end ? $result->theme_accent_end : '#3858f9'; ?>;"></span>
                                                            <input type="color" name="theme_accent_end" id="theme_accent_end" class="form-control" style="height: 36px;" value="<?php echo $result->theme_accent_end ? $result->theme_accent_end : '#3858f9'; ?>">
                                                            <span class="input-group-addon colour-hex-box" style="min-width: 80px; font-family: monospace; font-size: 13px; cursor: pointer;" onclick="copyHex(this)" title="<?php echo $this->lang->line('click_to_copy'); ?>"><?php echo $result->theme_accent_end ? $result->theme_accent_end : '#3858f9'; ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="padding-top: 25px;">
                                                    <div id="accent_gradient_bar" style="height: 36px; border-radius: 4px; background: linear-gradient(90deg, <?php echo $result->theme_accent_start ? $result->theme_accent_start : '#f33057'; ?>, <?php echo $result->theme_accent_end ? $result->theme_accent_end : '#3858f9'; ?>); border: 1px solid #ddd;"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ===== LIVE PREVIEW ===== -->
                                <div class="col-md-12">
                                    <div class="box box-solid" style="border-top: 3px solid #666; margin-bottom: 15px;">
                                        <div class="box-header" style="padding: 8px 12px; background: #f8f8f8;">
                                            <h4 class="box-title" style="font-size: 14px; font-weight: bold;"><i class="fa fa-eye"></i> <?php echo $this->lang->line('live_preview'); ?></h4>
                                        </div>
                                        <div class="box-body" style="padding: 12px;">
                                            <div id="preview_box" style="border-radius: 6px; overflow: hidden; border: 1px solid #ccc; max-width: 100%; min-height: 160px;">
                                                <!-- Preview Header -->
                                                <div id="preview_header" style="padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; background: <?php echo $result->theme_header_colour ? $result->theme_header_colour : '#367fa9'; ?>;">
                                                    <span style="color: #fff; font-weight: bold; font-size: 15px;"><i class="fa fa-bars"></i> AMT Admin Panel</span>
                                                    <span style="color: rgba(255,255,255,0.8); font-size: 13px;"><i class="fa fa-bell"></i> <i class="fa fa-user"></i></span>
                                                </div>
                                                <div style="display: flex; min-height: 120px;">
                                                    <!-- Preview Sidebar -->
                                                    <div id="preview_sidebar" style="width: 180px; padding: 10px 0; background: <?php echo $result->theme_sidebar_colour ? $result->theme_sidebar_colour : '#222d32'; ?>;">
                                                        <div style="padding: 7px 15px; color: rgba(255,255,255,0.7); font-size: 12px; background: rgba(0,0,0,0.1);"><i class="fa fa-tachometer"></i> Dashboard</div>
                                                        <div id="preview_sidebar_active" style="padding: 7px 15px; color: #fff; font-size: 12px; background: rgba(255,255,255,0.15); border-left: 3px solid rgba(255,255,255,0.8);"><i class="fa fa-users"></i> Students</div>
                                                        <div style="padding: 7px 15px; color: rgba(255,255,255,0.5); font-size: 12px;"><i class="fa fa-money"></i> Fees</div>
                                                        <div style="padding: 7px 15px; color: rgba(255,255,255,0.5); font-size: 12px;"><i class="fa fa-graduation-cap"></i> Academics</div>
                                                        <div style="padding: 7px 15px; color: rgba(255,255,255,0.5); font-size: 12px;"><i class="fa fa-cog"></i> Settings</div>
                                                    </div>
                                                    <!-- Preview Content -->
                                                    <div id="preview_body" style="flex: 1; padding: 15px; background: <?php echo $result->theme_body_bg ? $result->theme_body_bg : '#ecf0f5'; ?>; font-size: 13px; color: #333; position: relative; overflow: hidden;">
                                                        <!-- Accent gradient stripe -->
                                                        <div id="preview_accent_stripe" style="position: absolute; top: 0; left: 0; right: 0; height: 40px; background: linear-gradient(45deg, <?php echo $result->theme_accent_start ? $result->theme_accent_start : '#f33057'; ?>, <?php echo $result->theme_accent_end ? $result->theme_accent_end : '#3858f9'; ?>); opacity: 0.15;"></div>
                                                        <div style="position: relative; z-index: 1; font-weight: bold; margin-bottom: 10px; font-size: 14px;">Dashboard</div>
                                                        <div style="display: flex; gap: 10px;">
                                                            <div style="flex:1; background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ddd;">
                                                                <div style="font-size: 11px; color: #777;">Students</div>
                                                                <div style="font-size: 20px; font-weight: bold; color: #333;">1,234</div>
                                                            </div>
                                                            <div style="flex:1; background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ddd;">
                                                                <div style="font-size: 11px; color: #777;">Revenue</div>
                                                                <div style="font-size: 20px; font-weight: bold; color: #333;">₹45,678</div>
                                                            </div>
                                                            <div style="flex:1; background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ddd;">
                                                                <div style="font-size: 11px; color: #777;">Attendance</div>
                                                                <div style="font-size: 20px; font-weight: bold; color: #333;">92%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <?php if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) { ?>
                                    <button type="button" class="btn btn-danger reset_themecolour pull-left" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <i class="fa fa-undo"></i> <?php echo $this->lang->line('reset_to_default'); ?></button>
                                    <button type="button" class="btn btn-primary submit_themecolour pull-right" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
                                <?php } ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.colour-preview-box {
    border-radius: 0 !important;
}
.colour-hex-box {
    transition: background 0.2s;
}
.colour-hex-box:hover {
    background: #e0e0e0;
}
</style>

<script type="text/javascript">
    // Helper: generate darker shade
    function darkenColor(hex, amount) {
        var r = Math.max(0, parseInt(hex.slice(1,3), 16) - amount);
        var g = Math.max(0, parseInt(hex.slice(3,5), 16) - amount);
        var b = Math.max(0, parseInt(hex.slice(5,7), 16) - amount);
        return '#' + r.toString(16).padStart(2,'0') + g.toString(16).padStart(2,'0') + b.toString(16).padStart(2,'0');
    }

    // Copy hex to clipboard
    function copyHex(el) {
        var text = el.textContent.trim();
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text);
            var orig = el.textContent;
            el.textContent = '<?php echo $this->lang->line('copied'); ?>!';
            setTimeout(function() { el.textContent = orig; }, 800);
        }
    }

    // Update live preview
    function updateLivePreview() {
        var headerColour   = $('#theme_header_colour').val();
        var headerGradient = $('#theme_header_gradient').val();
        var sidebarColour  = $('#theme_sidebar_colour').val();
        var sidebarGradient = $('#theme_sidebar_gradient').val();
        var bodyBg         = $('#theme_body_bg').val();

        // Update hex displays
        $('#header_preview').next().next('.colour-hex-box').text(headerColour);
        $('#header_gradient_preview').next().next('.colour-hex-box').text(headerGradient);
        $('#sidebar_preview').next().next('.colour-hex-box').text(sidebarColour);
        $('#sidebar_gradient_preview').next().next('.colour-hex-box').text(sidebarGradient);
        $('#bodybg_preview').next().next('.colour-hex-box').text(bodyBg);

        // Update swatches
        $('#header_preview').css('background-color', headerColour);
        $('#header_gradient_preview').css('background-color', headerGradient);
        $('#sidebar_preview').css('background-color', sidebarColour);
        $('#sidebar_gradient_preview').css('background-color', sidebarGradient);
        $('#bodybg_preview').css('background-color', bodyBg);

        // Preview header
        if (headerColour.toLowerCase() === headerGradient.toLowerCase()) {
            $('#preview_header').css('background', headerColour);
        } else {
            $('#preview_header').css('background', 'linear-gradient(90deg, ' + headerColour + ' 0%, ' + headerGradient + ' 100%)');
        }

        // Preview sidebar
        if (sidebarColour.toLowerCase() === sidebarGradient.toLowerCase()) {
            $('#preview_sidebar').css('background', sidebarColour);
        } else {
            $('#preview_sidebar').css('background', 'linear-gradient(180deg, ' + sidebarColour + ' 0%, ' + sidebarGradient + ' 100%)');
        }

        // Preview body
        $('#preview_body').css('background', bodyBg);

        // Preview accent gradient
        var accentStart = $('#theme_accent_start').val();
        var accentEnd = $('#theme_accent_end').val();
        $('#accent_start_preview').css('background-color', accentStart);
        $('#accent_end_preview').css('background-color', accentEnd);
        $('#accent_start_preview').next().next('.colour-hex-box').text(accentStart);
        $('#accent_end_preview').next().next('.colour-hex-box').text(accentEnd);
        var accentGrad = 'linear-gradient(90deg, ' + accentStart + ', ' + accentEnd + ')';
        $('#accent_gradient_bar').css('background', accentGrad);
        $('#preview_accent_stripe').css('background', 'linear-gradient(45deg, ' + accentStart + ', ' + accentEnd + ')');
    }

    // Bind all colour inputs
    $('input[type="color"]').on('input', updateLivePreview);

    // Match gradient to main colour (solid)
    $('.match-header-btn').on('click', function() {
        var colour = $('#theme_header_colour').val();
        $('#theme_header_gradient').val(colour);
        updateLivePreview();
    });
    $('.match-sidebar-btn').on('click', function() {
        var colour = $('#theme_sidebar_colour').val();
        $('#theme_sidebar_gradient').val(colour);
        updateLivePreview();
    });

    // Save
    $(".submit_themecolour").on('click', function() {
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("schsettings/savethemecolour") ?>',
            type: 'POST',
            data: $('#themecolour_form').serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status == "fail") {
                    var message = "";
                    $.each(data.error, function(index, value) { message += value; });
                    errorMsg(message);
                } else {
                    successMsg(data.message);
                    setTimeout(function() { location.reload(); }, 1000);
                }
                $this.button('reset');
            },
            error: function() {
                errorMsg('<?php echo $this->lang->line('something_went_wrong'); ?>');
                $this.button('reset');
            }
        });
    });

    // Reset
    $(".reset_themecolour").on('click', function() {
        if (!confirm('<?php echo $this->lang->line('reset_theme_confirm'); ?>')) return;
        var $this = $(this);
        $this.button('loading');
        $.ajax({
            url: '<?php echo site_url("schsettings/resethemecolour") ?>',
            type: 'POST',
            dataType: 'json',
            success: function(data) {
                if (data.status == "success") {
                    successMsg(data.message);
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    errorMsg(data.message || '<?php echo $this->lang->line('something_went_wrong'); ?>');
                }
                $this.button('reset');
            },
            error: function() {
                errorMsg('<?php echo $this->lang->line('something_went_wrong'); ?>');
                $this.button('reset');
            }
        });
    });
</script>
