<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
<script src="<?php echo base_url(); ?>backend/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<style>
    /* Scoped Styles for Email Config Page */
    #email-config-page.content-wrapper {
        background: #f8f9fa;
        font-family: 'Poppins', 'Segoe UI', sans-serif;
    }

    #email-config-page .box {
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,0.03);
        background: #fff;
        border-radius: 12px;
        margin-top: 20px;
    }

    #email-config-page .box-header {
        text-align: center;
        padding: 25px;
        border-bottom: 2px solid #f1f5f9;
        background: #fff;
        border-radius: 12px 12px 0 0;
    }

    #email-config-page .box-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    /* Centered Form Layout */
    #email-config-page .form-horizontal {
        max-width: 1400px; /* Increased from 800px */
        margin: 0 auto;
        padding: 40px 0;
        width: 95%; /* Ensure it takes up most of the screen on smaller wide screens */
    }

    #email-config-page .box-body {
        padding: 0 40px;
    }

    #email-config-page .row {
        display: block !important;
        width: 100%;
    }

    #email-config-page .form-group {
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center; /* Center Flex Items */
    }

    /* Labels */
    #email-config-page .control-label {
        text-align: right !important; /* Right align labels like UI req */
        color: #475569;
        font-weight: 600;
        font-size: 1.4rem;
        padding-top: 10px;
        margin-right: 20px;
    }

    /* Inputs */
    #email-config-page .form-control {
        background: transparent;
        border: none;
        border-bottom: 2px solid #cbd5e1;
        border-radius: 0;
        box-shadow: none;
        height: 50px;
        font-size: 1.4rem;
        color: #0f172a;
        padding: 10px 5px;
        transition: all 0.3s;
    }

    #email-config-page .form-control:focus {
        border-bottom: 2px solid #2563eb;
    }

    /* Selects */
    #email-config-page select.form-control {
         appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0px center;
        background-size: 14px;
        padding-right: 20px;
    }

    /* Button Center */
    #email-config-page .box-footer {
        background: transparent;
        border: none;
        text-align: center;
        padding-bottom: 40px;
    }

    #email-config-page .btn-info {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        border: none;
        box-shadow: 0 10px 20px -10px rgba(37, 99, 235, 0.5);
        padding: 12px 50px;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    #email-config-page .btn-info:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -10px rgba(37, 99, 235, 0.6);
    }
    
    /* Remove Offset for centering */
    #email-config-page .col-md-offset-3 {
        margin-left: 0;
        width: 100%;
        text-align: center;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper" id="email-config-page">
    <!-- Content Header (Page header) -->
    
    <section class="content">
        <div class="row">
            <div class="col-md-12">             
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-envelope"></i> <?php echo $this->lang->line('email_setting'); ?></h3>
                    </div>   
                    <form id="form1" action="<?php echo base_url() ?>emailconfig/index"   name="employeeform" class="form-horizontal form-label-left" method="post" accept-charset="utf-8">

                    <div class="box-body">
                        <div class="row">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <?php 
                                    echo $this->session->flashdata('msg');
                                    $this->session->unset_userdata('msg'); 
                                ?>
                            <?php } ?>   
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                    <?php echo $this->lang->line('email_engine'); ?>
                                </label>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <select autofocus="" id="email_type" name="email_type" class="form-control">

                                        <?php
                                        foreach ($mailMethods as $method_key => $method_value) {
                                            ?>
                                            <option value="<?php echo $method_key ?>"
                                                    <?php if (set_value('email_type', $emaillist->email_type) == $method_key) echo "selected=selected" ?>>
                                                <?php echo $method_value ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                                    <span class="text-danger"><?php echo form_error('email_type'); ?></span>
                                </div>
                            </div>   
                            <?php $display = (set_value('email_type', $emaillist->email_type) != "smtp") ? 'ss-none' : '' ?>
                            <?php $display_ses = (set_value('email_type', $emaillist->email_type) != "aws_ses") ? 'ss-none' : '' ?>
                            <div class="is_disabled <?php echo $display; ?>" >


                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('smtp_username'); ?>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="smtp_username" placeholder="" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('smtp_username', $emaillist->smtp_username); ?>" />
                                        <span class="text-danger"><?php echo form_error('smtp_username'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('smtp_password'); ?>
                                    </label><div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="smtp_password" placeholder="" type="password" class="form-control col-md-7 col-xs-12"  value="<?php echo set_value('smtp_password', $emaillist->smtp_password); ?>" />
                                        <span class="text-danger"><?php echo form_error('smtp_password'); ?></span>
                                    </div></div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('smtp_server'); ?>
                                    </label><div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="smtp_server" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo set_value('smtp_server', $emaillist->smtp_server); ?>"  />
                                        <span class="text-danger"><?php echo form_error('smtp_server'); ?></span>
                                    </div>  </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('smtp_port'); ?>
                                    </label><div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="smtp_port" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo set_value('smtp_port', $emaillist->smtp_port); ?>"  />
                                        <span class="text-danger"><?php echo form_error('smtp_port'); ?></span>
                                    </div></div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('smtp_security'); ?>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select  id="name" name="smtp_security" class="form-control col-md-7 col-xs-12">
                                            <?php 
                            foreach ($smtp_encryption as $encryption_key => $encryption_value) {
                              ?>
                            <option value="<?php echo $encryption_key?>" <?php echo set_select('smtp_security', $encryption_key, (set_value('smtp_security', $emaillist->ssl_tls) == $encryption_key ) ? TRUE : FALSE ); ?> ><?php echo $encryption_value;?></option>
                              <?php
                            }
                                             ?>
                                        </select>
                                   
                                        <span class="text-danger"><?php echo form_error('smtp_security'); ?></span>
                                    </div>
                                </div>  
                                           <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                   <?php echo $this->lang->line('smtp_auth')?>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <select  id="smtp_auth" name="smtp_auth" class="form-control col-md-7 col-xs-12">
                                            <?php 
                            foreach ($smtp_auth as $smtp_auth_key => $smtp_auth_value) {
                              ?>
                           <option value="<?php echo $smtp_auth_key?>" <?php echo set_select('smtp_security', $smtp_auth_key, (set_value('auth_key', $emaillist->smtp_auth) == $smtp_auth_key ) ? TRUE : FALSE ); ?> ><?php echo $smtp_auth_value;?></option>
                              <?php
                            }
                                             ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('smtp_security'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="is_disabled_ses <?php echo $display_ses; ?>" >
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('email'); ?>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="name" name="aws_email" placeholder="" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('aws_email', $emaillist->smtp_username); ?>" />
                                        <span class="text-danger"><?php echo form_error('aws_email'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('access_key_id'); ?>
                                    </label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="access_key" name="access_key" placeholder="" type="text" class="form-control col-md-7 col-xs-12" value="<?php echo set_value('access_key', $emaillist->api_key); ?>" />
                                        <span class="text-danger"><?php echo form_error('access_key'); ?></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('secret_access_key'); ?>
                                    </label><div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="secret_access_key" name="secret_access_key" placeholder="" type="password" class="form-control col-md-7 col-xs-12"  value="<?php echo set_value('secret_access_key', $emaillist->api_secret); ?>" />
                                        <span class="text-danger"><?php echo form_error('secret_access_key'); ?></span>
                                    </div></div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="exampleInputEmail1">
                                        <?php echo $this->lang->line('region'); ?>
                                    </label><div class="col-md-6 col-sm-6 col-xs-12">
                                        <input id="region" name="region" placeholder="" type="text" class="form-control col-md-7 col-xs-12"  value="<?php echo set_value('region', $emaillist->region); ?>"  />
                                        <span class="text-danger"><?php echo form_error('region'); ?></span>
                                    </div>  
                                </div>
                            </div>
                          </div>                            
                        </div>
                        <div class="box-footer">
                           <div class="row">
                                <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                                <?php
                                if ($this->rbac->hasPrivilege('email_setting', 'can_edit')) {
                                    ?>

                                    <button type="submit" class="btn btn-info btnleftinfo"><?php echo $this->lang->line('save'); ?></button>
                                    <?php
                                }
                                ?>
                            </div>
                          </div>
                        </div>
                    </form>
                </div>
            </div>           
        </div></section>

    <div id="myModal" class="modal fade in" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-dialog2">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title"><?php echo $this->lang->line('test_email'); ?></h4>
                </div>
                <div class="modal-body pt0 pb0">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 paddlr">
                            <div class="">
                                <form id="sendform" action="<?php echo base_url() ?>emailconfig/test_mail"   name="employeeform" class="form-horizontal form-label-left" method="post" accept-charset="utf-8"> 
                                    <div class="">


                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="pwd"><?php echo $this->lang->line('email'); ?> </label><small class="req"> *</small>  
                                                <input type="text" id="title" autocomplete="off" class="form-control" value="" name="email">
                                                <span id="name_add_error" class="text-danger"></span>
                                            </div>

                                        </div>
                                    </div>

                            </div><!--./row--> 
                            <div class="box-footer">
                                <div class="pull-right paddA10">

                                    <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('send'); ?></button>
                                </div>
                            </div>
                            </form>  
                        </div>                     
                    </div><!--./col-md-12-->       

                </div><!--./row--> 

            </div>
        </div>
    </div>
</div>
</div>

</div>


<script type="text/javascript">
    $(document).ready(function () {


        $(document).on('change', '#email_type', function () {
            var selected = $(this).val();
            is_disabled(selected);
        });

    });
    function is_disabled(selected) {
        if (selected == "smtp") {
            $('.is_disabled_ses').slideUp();
            $('.is_disabled').slideDown();
        }else if(selected == "aws_ses"){
            $('.is_disabled').slideUp();
            $('.is_disabled_ses').slideDown();
        } else {
            $('.is_disabled_ses').slideUp();
            $('.is_disabled').slideUp();
        }
    }

    function test_mail() {
        $('#myModal').modal('show');
    }

    $(document).ready(function (e) {
        $("#sendform").on('submit', (function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?php echo base_url() ?>emailconfig/test_mail',
                type: "POST",
                data: new FormData(this),
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    if (data.status == "fail") {
                        var message = "";
                        $.each(data.error, function (index, value) {
                            message += value;
                        });
                        errorMsg(message);
                    } else {
                        successMsg(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {}
            });
        }));
    });
</script>