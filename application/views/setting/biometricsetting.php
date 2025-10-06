<div class="content-wrapper" style="min-height: 348px;">  
    <section class="content">
        <div class="row">
        
            <?php $this->load->view('setting/_settingmenu'); ?>
            
            <!-- left column -->
            <div class="col-md-10">
                <!-- general form elements -->




                
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix"><i class="fa fa-gear"></i> <?php echo $this->lang->line('biometricsetting'); ?></h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="">
                        <form role="form" id="attendancetype_form" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="sch_id" value="<?php echo $result->id; ?>">
                            <div class="box-body">                       
                                <div class="row">
                                    
                                    <br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-sm-3"> <?php echo $this->lang->line('checkintimestart'); ?> <i class="fa fa-question-circle cursor-pointer text-sky-blue" data-toggle="tooltip" data-placement="top" title="<?php echo ('Enter Biometric checkin time start');?>"></i></label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control datetimepicker" name="checkintimestart" value="<?php echo ($section_value->time !=0) ? $section_value->time :"" ?>" id="checkintimestart" placeholder="Enter time">                                                            
                                                        <div class="input-group-addon">
                                                            <span class="fa fa-clock-o"></span>
                                                        </div>
                                                    </div>         
                                                </div>
                                            </div>
                                        </div>
                                    </div> 

                                    <br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-sm-3"> <?php echo $this->lang->line('checkintimeend'); ?> <i class="fa fa-question-circle cursor-pointer text-sky-blue" data-toggle="tooltip" data-placement="top" title="<?php echo ('Enter Biometric checkin time end');?>"></i></label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control datetimepicker" name="checkintimeend" value="<?php echo ($section_value->time !=0) ? $section_value->time :"" ?>" id="checkintimeend" placeholder="Enter time">                                                            
                                                        <div class="input-group-addon">
                                                            <span class="fa fa-clock-o"></span>
                                                        </div>
                                                    </div>         
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-sm-3"> <?php echo $this->lang->line('checkoutstart'); ?> <i class="fa fa-question-circle cursor-pointer text-sky-blue" data-toggle="tooltip" data-placement="top" title="<?php echo ('Enter Biometric checkout time Start');?>"></i></label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control datetimepicker" name="checkoutstart" value="<?php echo ($section_value->time !=0) ? $section_value->time :"" ?>" id="checkoutstart" placeholder="Enter time">                                                            
                                                        <div class="input-group-addon">
                                                            <span class="fa fa-clock-o"></span>
                                                        </div>
                                                    </div>         
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <br>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="col-sm-3"> <?php echo $this->lang->line('checkoutend'); ?> <i class="fa fa-question-circle cursor-pointer text-sky-blue" data-toggle="tooltip" data-placement="top" title="<?php echo ('Enter Biometric checkout time end');?>"></i></label>
                                                <div class="col-sm-3">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control datetimepicker" name="checkoutend" value="<?php echo ($section_value->time !=0) ? $section_value->time :"" ?>" id="checkoutend" placeholder="Enter time">                                                            
                                                        <div class="input-group-addon">
                                                            <span class="fa fa-clock-o"></span>
                                                        </div>
                                                    </div>         
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    

                                </div><!--./row--> 
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <!-- <?php
                                if ($this->rbac->hasPrivilege('general_setting', 'can_edit')) {
                                    ?> -->
                                    <button type="button" class="btn btn-primary submit_schsetting pull-right edit_attendancetype" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> <?php echo $this->lang->line('processing'); ?>"> <?php echo $this->lang->line('save'); ?></button>
                                    <!-- <?php
                                }
                                ?> -->
                            </div>
                        </form>
                    </div><!-- /.box-body -->
                </div>









            </div><!--/.col (left) -->
            <!-- right column -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<!-- new END -->
</div><!-- /.content-wrapper -->

<script type="text/javascript">
     $('input[type=radio][name=biometric]').change(function() {
        if (this.value == '1') {
            $('#save_class_time_hide_show').removeClass('hide'); 
        }
        else if (this.value == '0') {
             $('#save_class_time_hide_show').addClass('hide');   
        }
    }); 
     
    window.onload = function(){  
        var biometric = '<?php echo $result->biometric; ?>';  
        if(biometric == '1'){
            $('#save_class_time_hide_show').removeClass('hide'); 
        }else if(biometric == '0'){
            $('#save_class_time_hide_show').addClass('hide');   
        }
    }  
</script> 

<script type="text/javascript">
 $(document).ready(function() {
    // Initialize datetimepicker
    $('.datetimepicker').datetimepicker({
        format: 'hh:mm A',
        stepping: 5
    });

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle biometric radio button change
    $('input[type=radio][name=biometric]').change(function() {
        $('#save_class_time_hide_show').toggleClass('hide', this.value !== '1');
    });

    // Handle form submission
    $(".edit_attendancetype").on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        
        // Validate form
        if (!validateForm()) {
            errorMsg('Please fill in all required time fields');
            return false;
        }

        $this.button('loading');

        $.ajax({
            url: base_url + 'schsettings/biometraicattendancetypesave',
            type: 'POST',
            data: $('#attendancetype_form').serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === "fail") {
                    let message = "";
                    if (typeof response.error === 'object') {
                        $.each(response.error, function(index, value) {
                            message += value + "<br>";
                        });
                    } else {
                        message = response.error;
                    }
                    errorMsg(message);
                } else {
                    successMsg(response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            },
            error: function(xhr, status, error) {
                errorMsg('An error occurred while saving. Please try again.');
                console.error('Ajax Error:', error);
            },
            complete: function() {
                $this.button('reset');
            }
        });
    });

    // Form validation function
    function validateForm() {
        let isValid = true;
        const requiredFields = [
            'checkintimestart',
            'checkintimeend',
            'checkoutstart',
            'checkoutend'
        ];

        requiredFields.forEach(field => {
            const value = $(`#${field}`).val();
            if (!value || value.trim() === '') {
                isValid = false;
                $(`#${field}`).addClass('error');
            } else {
                $(`#${field}`).removeClass('error');
            }
        });

        return isValid;
    }

    // Clear error class on input change
    $('.datetimepicker').on('change', function() {
        $(this).removeClass('error');
    });
});
</script>

<script type="text/javascript">
    $('.datetimepicker').datetimepicker({
      format: 'hh:mm A',
});

$(document).on('submit','#form_timetable',function(e){

    // this is the id of the form


    e.preventDefault(); // avoid to execute the actual submit of the form.

    var form = $(this);
    var actionUrl = form.attr('action');
      var submit_button = form.find(':submit');
    $.ajax({
        type: "POST",
        url: actionUrl,
        data: form.serialize(), // serializes the form's elements.
        dataType: "JSON", // serializes the form's elements.
                    beforeSend: function () {

                        submit_button.button('loading');
                    },
                    success: function (data)
                    {

                        var message = "";
                        if (!data.status) {

                            $.each(data.error, function (index, value) {

                                message += value;
                            });

                            errorMsg(message);

                        } else {
                            successMsg(data.message);
                           
                        }
                    },
                    error: function (xhr) { // if error occured
                        submit_button.button('reset');
                        alert("<?php echo $this->lang->line('error_occurred_please_try_again'); ?>");

                    },
                    complete: function () {
                        submit_button.button('reset');
                    }
                });    
            });


     $(document).on('change','.copy_other',function(){
        if(this.checked) {           
            var first_due= $('form#form_timetable').find('input.datetimepicker').filter(':visible:first').val();          
            $('form#form_timetable').find('.datetimepicker').val(first_due);  
            
        }
    });
</script>