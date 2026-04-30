<style>
    /* Modern Theme Variables */
    :root {
        --primary-gradient: linear-gradient(135deg, #4ca1f6 0%, #3858f9 100%);
        --success-gradient: linear-gradient(135deg, #00a65a 0%, #00d2ad 100%);
        --card-radius: 16px;
        --input-radius: 10px;
        --transition-base: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        --shadow-elevation: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
    }

    .content-wrapper {
        background: #f4f7fa !important;
    }

    .nav-tabs-custom.theme-card {
        background: #fff;
        border-radius: var(--card-radius) !important;
        box-shadow: var(--shadow-elevation) !important;
        border: none !important;
        overflow: hidden;
        margin-top: 20px;
    }

    /* Modern Tabs Styling */
    .nav-tabs-custom > .nav-tabs {
        background: #fff;
        border-bottom: 2px solid #edf2f7 !important;
        padding: 5px 15px 0;
        display: flex;
        flex-wrap: wrap;
        width: 100%;
    }

    .nav-tabs-custom > .nav-tabs > li {
        margin-bottom: -2px;
        margin-right: 5px;
    }

    .nav-tabs-custom > .nav-tabs > li > a {
        border: none !important;
        border-radius: 0 !important;
        color: #718096 !important;
        font-weight: 600 !important;
        padding: 12px 20px !important;
        position: relative;
        transition: var(--transition-base);
        background: transparent !important;
    }

    .nav-tabs-custom > .nav-tabs > li.active > a {
        color: #3858f9 !important;
        background: transparent !important;
    }

    .nav-tabs-custom > .nav-tabs > li.active > a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--primary-gradient);
        border-radius: 3px 3px 0 0;
    }

    .nav-tabs-custom > .nav-tabs > li > a:hover {
        color: #3858f9 !important;
        background: #f7fafc !important;
    }

    .nav-tabs-custom > .nav-tabs > li.pull-left.header {
        font-size: 18px;
        font-weight: 700;
        color: #2d3748;
        padding: 15px 20px;
        line-height: 1.2;
        margin-right: auto;
    }

    /* Tab Content Styling */
    .tab-content {
        padding: 30px !important;
    }

    .form-group label {
        color: #4a5568;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control {
        border-radius: var(--input-radius) !important;
        border: 2px solid #e2e8f0 !important;
        transition: var(--transition-base);
        box-shadow: none !important;
        height: auto;
        padding: 10px 15px;
    }

    .form-control:focus {
        border-color: #4ca1f6 !important;
        background: #fff !important;
    }

    /* Modern Buttons */
    .btn-primary {
        background: var(--primary-gradient) !important;
        border: none !important;
        border-radius: var(--input-radius) !important;
        padding: 12px 30px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 15px rgba(56, 88, 249, 0.3) !important;
        transition: var(--transition-base) !important;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(56, 88, 249, 0.4) !important;
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    /* WYSIWYG Editor Cohesive Styling */
    .wysihtml5-toolbar {
        background: #f8fafc !important;
        border: 2px solid #e2e8f0 !important;
        border-bottom: none !important;
        border-radius: var(--input-radius) var(--input-radius) 0 0 !important;
        padding: 10px !important;
        margin-bottom: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .wysihtml5-sandbox {
        border: 2px solid #e2e8f0 !important;
        border-top: none !important; /* Ensure they join perfectly */
        border-radius: 0 0 var(--input-radius) var(--input-radius) !important;
        transition: var(--transition-base);
        width: 100% !important;
        box-sizing: border-box !important;
        display: block !important;
        margin-top: 0 !important;
    }

    ul.wysihtml5-toolbar + textarea {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        overflow: hidden !important;
    }

    .wysihtml5-sandbox:focus {
        border-color: #4ca1f6 !important;
    }

    /* Fix for invisible dropdown items in wysihtml5 */
    .wysihtml5-toolbar .dropdown-menu {
        background-color: #fff !important;
        border: none !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        padding: 8px !important;
        border-radius: 10px !important;
    }

    .wysihtml5-toolbar .dropdown-menu > li > a {
        color: #4a5568 !important;
        border-radius: 6px !important;
        padding: 8px 12px !important;
        font-weight: 500 !important;
    }

    .wysihtml5-toolbar .dropdown-menu > li > a:hover {
        background-color: #edf2f7 !important;
        color: #3858f9 !important;
    }

    /* Dropify/File Style Customization */
    .dropify-wrapper {
        border-radius: var(--card-radius) !important;
        border: 2px dashed #cbd5e0 !important;
        transition: var(--transition-base) !important;
    }

    .dropify-wrapper:hover {
        border-color: #4ca1f6 !important;
        background-image: linear-gradient(-45deg, #f6f7fb 25%, transparent 25%, transparent 50%, #f6f7fb 50%, #f6f7fb 75%, transparent 75%, transparent) !important;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .nav-tabs-custom > .nav-tabs {
            flex-direction: column-reverse;
        }
        
        .nav-tabs-custom > .nav-tabs > li.pull-left.header {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #edf2f7;
            padding-bottom: 15px;
        }

        .nav-tabs-custom > .nav-tabs > li {
            width: 100%;
            margin-right: 0;
        }

        .tab-content {
            padding: 20px 15px !important;
        }

        .btn-primary {
            width: 100%;
        }
    }

    /* Alert Styling */
    .alert {
        border-radius: var(--input-radius) !important;
        border: none !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
        margin-bottom: 25px !important;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom theme-card">
                    <ul class="nav nav-tabs pull-right">
                        <li class="pull-left header"> <i class="fa fa-print"></i> <?php echo $this->lang->line('print_headerfooter'); ?></li>
                        <li class="active"><a href="#tab_3" data-toggle="tab"><?php echo $this->lang->line('fees_receipt'); ?></a></li>
                        <li><a href="#tab_4" data-toggle="tab"><?php echo $this->lang->line('payslip') ?></a></li>
                        <li><a href="#tab_1" data-toggle="tab"><?php echo $this->lang->line('online_admission_receipt'); ?></a></li>
                        <li><a href="#tab_2" data-toggle="tab"><?php echo $this->lang->line('online_exam'); ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <?php
                        if ($this->session->flashdata('msg') != '') {
                            $msg = $this->session->flashdata('msg');
                            echo $msg;
                            $this->session->unset_userdata('msg');
                        }
                        ?>
                        
                        <!-- Fees Receipt -->
                        <div class="tab-pane active" id="tab_3">
                            <form role="form" id="form1" enctype="multipart/form-data" action="<?php echo site_url('admin/print_headerfooter/edit') ?>" method="post">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Receipt Template</label><br>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="student_receipt" name="receipt_template" value="image" <?php echo ($result[1]['receipt_template'] == 'image' || empty($result[1]['receipt_template'])) ? 'checked' : ''; ?>> Image Template</label>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="student_receipt" name="receipt_template" value="custom" <?php echo ($result[1]['receipt_template'] == 'custom') ? 'checked' : ''; ?>> Custom HTML Template</label>
                                        </div>
                                        <div class="form-group header-image-group student_receipt_image" <?php echo ($result[1]['receipt_template'] == 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><?php echo $this->lang->line('header_image') . " (2230px X 300px)"; ?><small class="req"> *</small></label>
                                            <input id="documents_student_receipt" data-default-file="<?php echo $this->customlib->getBaseUrl() ?>./uploads/print_headerfooter/student_receipt/<?php echo $result[1]['header_image'] ?>" type="file" class="filestyle form-control" data-height="180" name="header_image">
                                            <input type="hidden" value="student_receipt" name="type">
                                            <span class="text-danger"><?php echo form_error('header_image'); ?></span>
                                        </div>
                                        <div class="form-group header-custom-group student_receipt_custom" <?php echo ($result[1]['receipt_template'] != 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><i class="fa fa-pencil"></i> Custom Header Content</label>
                                            <textarea id="header_textarea1" name="header_message1" class="form-control" style="height: 250px"><?php echo set_value('header_message1', $result[1]['header_content']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-pencil"></i> <?php echo $this->lang->line('footer_content'); ?> </label>
                                            <textarea id="student_textarea" name="message1" class="form-control" style="height: 250px"><?php echo set_value('message1', $result[1]['footer_content']); ?></textarea>
                                            <span class="text-danger"><?php echo form_error('message1'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="pull-right">
                                            <button type="submit" id="submitbtn1" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Payslip -->
                        <div class="tab-pane" id="tab_4">
                            <form role="form" id="form2" action="<?php echo site_url('admin/print_headerfooter/edit') ?>" enctype="multipart/form-data" method="post">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Receipt Template</label><br>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="staff_payslip" name="receipt_template" value="image" <?php echo ($result[0]['receipt_template'] == 'image' || empty($result[0]['receipt_template'])) ? 'checked' : ''; ?>> Image Template</label>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="staff_payslip" name="receipt_template" value="custom" <?php echo ($result[0]['receipt_template'] == 'custom') ? 'checked' : ''; ?>> Custom HTML Template</label>
                                        </div>
                                        <div class="form-group header-image-group staff_payslip_image" <?php echo ($result[0]['receipt_template'] == 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><?php echo $this->lang->line('header_image') . " (2230px X 300px)"; ?><small class="req"> *</small></label>
                                            <input id="documents_staff_payslip" data-default-file="<?php echo $this->customlib->getBaseUrl() ?>./uploads/print_headerfooter/staff_payslip/<?php echo $result[0]['header_image'] ?>" type="file" class="filestyle form-control" data-height="180" name="header_image">
                                            <input type="hidden" value="staff_payslip" name="type">
                                            <span class="text-danger"><?php echo form_error('header_image'); ?></span>
                                        </div>
                                        <div class="form-group header-custom-group staff_payslip_custom" <?php echo ($result[0]['receipt_template'] != 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><i class="fa fa-pencil"></i> Custom Header Content</label>
                                            <textarea id="header_textarea" name="header_message" class="form-control" style="height: 250px"><?php echo set_value('header_message', $result[0]['header_content']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-pencil"></i> <?php echo $this->lang->line('footer_content'); ?> </label>
                                            <textarea id="staff_textarea" name="message" class="form-control" style="height: 250px"><?php echo set_value('message', $result[0]['footer_content']); ?></textarea>
                                            <span class="text-danger"><?php echo form_error('message'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="pull-right">
                                            <button type="submit" id="submitbtn2" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Online Admission -->
                        <div class="tab-pane" id="tab_1">
                            <form role="form" id="form3" enctype="multipart/form-data" action="<?php echo site_url('admin/print_headerfooter/edit') ?>" method="post">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Receipt Template</label><br>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="online_admission" name="receipt_template" value="image" <?php echo ($result[2]['receipt_template'] == 'image' || empty($result[2]['receipt_template'])) ? 'checked' : ''; ?>> Image Template</label>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="online_admission" name="receipt_template" value="custom" <?php echo ($result[2]['receipt_template'] == 'custom') ? 'checked' : ''; ?>> Custom HTML Template</label>
                                        </div>
                                        <div class="form-group header-image-group online_admission_image" <?php echo ($result[2]['receipt_template'] == 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><?php echo $this->lang->line('header_image') . " (2230px X 300px)"; ?><small class="req"> *</small></label>
                                            <input id="documents_online_admission" data-default-file="<?php echo $this->customlib->getBaseUrl() ?>./uploads/print_headerfooter/online_admission_receipt/<?php echo $result[2]['header_image'] ?>" type="file" class="filestyle form-control" data-height="180" name="header_image">
                                            <input type="hidden" value="online_admission_receipt" name="type">
                                            <span class="text-danger"><?php echo form_error('header_image'); ?></span>
                                        </div>
                                        <div class="form-group header-custom-group online_admission_custom" <?php echo ($result[2]['receipt_template'] != 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><i class="fa fa-pencil"></i> Custom Header Content</label>
                                            <textarea id="header_admission_textarea" name="admission_header_message" class="form-control" style="height: 250px"><?php echo set_value('admission_header_message', $result[2]['header_content']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-pencil"></i> <?php echo $this->lang->line('footer_content'); ?> </label>
                                            <textarea id="online_admission_textarea" name="admission_message" class="form-control" style="height: 250px"><?php echo set_value('admission_message', $result[2]['footer_content']); ?></textarea>
                                            <span class="text-danger"><?php echo form_error('admission_message'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="pull-right">
                                            <button type="submit" id="submitbtn3" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Online Exam -->
                        <div class="tab-pane" id="tab_2">
                            <form role="form" id="form4" enctype="multipart/form-data" action="<?php echo site_url('admin/print_headerfooter/edit') ?>" method="post">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Receipt Template</label><br>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="online_exam" name="receipt_template" value="image" <?php echo ($result[3]['receipt_template'] == 'image' || empty($result[3]['receipt_template'])) ? 'checked' : ''; ?>> Image Template</label>
                                            <label class="radio-inline"><input type="radio" class="template-toggle" data-target="online_exam" name="receipt_template" value="custom" <?php echo ($result[3]['receipt_template'] == 'custom') ? 'checked' : ''; ?>> Custom HTML Template</label>
                                        </div>
                                        <div class="form-group header-image-group online_exam_image" <?php echo ($result[3]['receipt_template'] == 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><?php echo $this->lang->line('header_image') . " (2230px X 300px)"; ?><small class="req"> *</small></label>
                                            <input id="documents_online_exam" data-default-file="<?php echo $this->customlib->getBaseUrl() ?>./uploads/print_headerfooter/online_exam/<?php echo $result[3]['header_image'] ?>" type="file" class="filestyle form-control" data-height="180" name="header_image">
                                            <input type="hidden" value="online_exam" name="type">
                                            <span class="text-danger"><?php echo form_error('header_image'); ?></span>
                                        </div>
                                        <div class="form-group header-custom-group online_exam_custom" <?php echo ($result[3]['receipt_template'] != 'custom') ? 'style="display:none;"' : ''; ?>>
                                            <label><i class="fa fa-pencil"></i> Custom Header Content</label>
                                            <textarea id="header_online_exam_textarea" name="online_exam_header_message" class="form-control" style="height: 250px"><?php echo set_value('online_exam_header_message', $result[3]['header_content']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label><i class="fa fa-pencil"></i> <?php echo $this->lang->line('footer_content'); ?></label>
                                            <textarea id="online_exam_textarea" name="online_exam_message" class="form-control" style="height: 250px"><?php echo set_value('online_exam_message', $result[3]['footer_content']); ?></textarea>
                                            <span class="text-danger"><?php echo form_error('online_exam_message'); ?></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="pull-right">
                                            <button type="submit" id="submitbtn4" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save'); ?>"><?php echo $this->lang->line('save'); ?></button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- wysihtml5 JS -->
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>

<script>
    $(function () {
        // Initialize wysihtml5 on each footer content textarea
        var textareaIds = ['student_textarea', 'staff_textarea', 'online_exam_textarea', 'online_admission_textarea', 'header_textarea1', 'header_textarea', 'header_admission_textarea', 'header_online_exam_textarea'];
        textareaIds.forEach(function(id) {
            var $textarea = $('#' + id);
            if ($textarea.length && !$textarea.data('wysihtml5')) {
                $textarea.wysihtml5({
                    "font-styles": true, 
                    "emphasis": true, 
                    "lists": true, 
                    "html": false, 
                    "link": true, 
                    "image": false, 
                    "color": false,
                    "blockquote": false
                });
            }
        });

        // Handle template toggle
        $('.template-toggle').on('change', function() {
            var target = $(this).data('target');
            if ($(this).val() == 'custom') {
                $('.' + target + '_image').hide();
                $('.' + target + '_custom').show();
            } else {
                $('.' + target + '_image').show();
                $('.' + target + '_custom').hide();
            }
        });

        // Form Submission Buttons Loading State
        $('form').on('submit', function() {
            var $btn = $(this).find('button[type="submit"]');
            $btn.button('loading');
        });
    });
</script>
