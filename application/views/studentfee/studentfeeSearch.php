<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?> </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form  action="<?php echo site_url('studentfee/search') ?>" method="post" class="class_search_form">
                                        <?php echo $this->customlib->getCSRF(); ?>
                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <div class="row">
                                    

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('class'); ?></label>
                                                <select id="class_id" name="class_id[]" class="form-control multiselect-dropdown" multiple>
                                                    <?php
                                    foreach ($classlist as $class) {
                                        ?>
                                          <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
                                            echo "selected=selected";
                                        }
                                        ?>><?php echo $class['class'] ?></option>
                                                                                            <?php
                                    }
                                    ?>
                                                                                    </select>
                                                 <span class="text-danger" id="error_class_id"></span>
                                            </div>

                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('section'); ?></label>
                                                <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                                </select>
                                                <span class="text-danger" id="error_section_id"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">

                                              

                                             <button type="submit" class="btn btn-primary btn-sm pull-right" name="class_search" data-loading-text="Please wait.." value="class_search"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>

                                            </div>
                                        </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div class="row">
                                   
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('search_by_keyword'); ?></label>
            <input type="text" name="search_text" id="search_text" class="form-control" value="<?php echo set_value('search_text'); ?>" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                                                 <span class="text-danger" id="error_search_text"></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group">
                                               <button type="submit" class="btn btn-primary btn-sm pull-right" name="keyword_search" data-loading-text="Please wait.." value="keyword_search"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                            </div>
                                        </div>
                                  
                                </div>
                            </div>
                        </div>
                    </form>
                    </div>


                        <div class="">
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo $this->lang->line('student'); ?> <?php echo $this->lang->line('list'); ?>
                                    <?php echo form_error('student'); ?></h3>
                                <div class="box-tools pull-right"></div>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    
                              
                                <table class="table table-striped table-bordered table-hover student-list" data-export-title="<?php echo $this->lang->line('student')." ".$this->lang->line('list'); ?>">
                                    <thead>

                                        <tr>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('section'); ?></th>

                                            <th><?php echo $this->lang->line('admission_no'); ?></th>

                                            <th><?php echo $this->lang->line('student'); ?> <?php echo $this->lang->line('name'); ?></th>
                                            <?php if ($sch_setting->father_name) {?>
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <?php }?>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <th><?php echo $this->lang->line('phone'); ?></th>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>

                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                                  </div>
                            </div><!--./box-body-->
                        </div>
                    </div>

            </div>

        </div>

    </section>
</div>

<script>
$(document).ready(function() {
     emptyDatatable('student-list','fees_data');

});
</script>
<script type="text/javascript">
    $(document).ready(function () {
        var commonOptions = {
            placeholder: 'Select Options',
            csvDispCount: 3,
            captionFormat: '{0} Selected',
            captionFormatAllSelected: 'All Selected ({0})',
            selectAll: true,
            search: false,
            okCancelInMulti: true,
            isClickAwayOk: true,
            locale: ['OK', 'Cancel', 'Select All'],
            up: false,
            showTitle: true
        };

        // Initialize SumoSelect individually by ID to prevent cross-interference
        if ($('#class_id').length) $('#class_id').SumoSelect(commonOptions);
        if ($('#section_id').length) $('#section_id').SumoSelect(commonOptions);

        // Initialize section dropdown on page load if class is pre-selected
        var preSelectedClass = $('#class_id').val();
        if (preSelectedClass && preSelectedClass.length > 0) {
            $('#class_id').trigger('change');
        }

        // Handle class dropdown changes for section population
        $(document).on('change', '#class_id', function (e) {
            var $classSelect = $(this);
            var sectionDropdown = $('#section_id')[0];
            var base_url = '<?php echo base_url() ?>';

            // Defer execution to let SumoSelect state settle
            setTimeout(function() {
                var current_class_ids = $classSelect.val(); // Get current value inside setTimeout
                if (!current_class_ids || current_class_ids.length === 0) {
                    if (sectionDropdown && sectionDropdown.sumo) {
                        sectionDropdown.sumo.removeAll();
                    }
                    return;
                }

                // Show loading state and clear current sections aggressively
                showDropdownLoading('#section_id');
                if (sectionDropdown && sectionDropdown.sumo) {
                    sectionDropdown.sumo.unSelectAll();
                    sectionDropdown.sumo.removeAll();
                    sectionDropdown.sumo.reload();
                }

                // Get sections for all selected classes in ONE request
                $.ajax({
                    type: "POST",
                    url: base_url + "sections/getByClass",
                    data: {
                        'class_id': current_class_ids
                    },
                    dataType: "json",
                    success: function(data) {
                        if (sectionDropdown && sectionDropdown.sumo) {
                            if (data && Array.isArray(data)) {
                                $.each(data, function(i, obj) {
                                    sectionDropdown.sumo.add(obj.section_id, obj.section);
                                });
                            }
                            sectionDropdown.sumo.reload();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    },
                    complete: function() {
                        // Always re-enable the dropdown
                        hideDropdownLoading('#section_id');
                    }
                });
            }, 50);
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){ 
      $("form.class_search_form button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });


        $(document).on('submit', '.class_search_form', function(e) {
            var $this = $(this).find("button[type=submit][clicked=true]");
            
            // If no button was clicked, don't submit (prevents SumoSelect OK triggering submit)
            if (!$this.length) {
                e.preventDefault();
                return;
            }

            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
    // Check if form submission should be prevented (during dropdown changes)
    if (window.preventFormSubmission) {
        console.log('🚫 Form submission blocked - dropdown change in progress');
        setTimeout(function() {
            window.preventFormSubmission = false;
        }, 1000);
        return false;
    }

    try {
        // var $this = $("button[type=submit][clicked=true]"); // This line is now redundant due to the guard above
        var url = form.attr('action');
        var form_data = form.serializeArray();
        form_data.push({name: 'search_type', value: $this.attr('value')});

        console.log('📝 Form submission started');
        console.log('Form URL:', url);
        console.log('Current page URL:', window.location.href);

        // Enhanced validation before AJAX call
        if (!url) {
            console.error('Form action URL is missing');
            showErrorMessage('Form configuration error. Please refresh the page and try again.');
            return false;
        }

        $.ajax({
               url: url,
               type: "POST",
               dataType:'JSON',
               data: form_data, // serializes the form's elements.
               timeout: 30000, // 30 second timeout
                  beforeSend: function () {
                    // Clear previous errors with enhanced error handling
                    try {
                        $('[id^=error]').html("");
                        $this.button('loading');
                        resetFields($this.attr('name'));

                        // Ensure form styling is preserved
                        $('.form-control').removeClass('error');
                        $('.has-error').removeClass('has-error');

                    } catch (beforeSendError) {
                        console.error('Error in beforeSend:', beforeSendError);
                    }
                   },
                  success: function(response) { // your success handler
                    try {
                        if(!response.status){
                            // Handle validation errors while preserving CSS
                            $.each(response.error, function(key, value) {
                                var errorElement = $('#error_' + key);
                                if (errorElement.length) {
                                    errorElement.html(value);
                                    // Add error class to form control
                                    var formControl = $('#' + key);
                                    if (formControl.length) {
                                        formControl.addClass('error');
                                        formControl.closest('.form-group').addClass('has-error');
                                    }
                                } else {
                                    console.warn('Error element not found for key:', key);
                                }
                            });

                            // Show general error message if no specific errors
                            if (Object.keys(response.error).length === 0) {
                                showErrorMessage('Please fill in the required fields and try again.');
                            }
                        } else {
                            // Success - initialize datatable
                            if (typeof initDatatable === 'function') {
                                initDatatable('student-list','studentfee/ajaxSearch',response.params,[],100);
                            } else {
                                console.error('initDatatable function not found');
                                showErrorMessage('Table initialization failed. Please refresh the page.');
                            }
                        }
                    } catch (successError) {
                        console.error('Error in success handler:', successError);
                        showErrorMessage('An error occurred while processing the response.');
                    }
                  },
                 error: function(xhr, status, error) { // your error handler
                     console.error('AJAX Error Details:', {
                         status: status,
                         error: error,
                         responseText: xhr.responseText,
                         statusCode: xhr.status
                     });

                     var errorMessage = 'Network error occurred. ';
                     if (status === 'timeout') {
                         errorMessage += 'Request timed out. Please try again.';
                     } else if (status === 'parsererror') {
                         errorMessage += 'Invalid response format.';
                     } else if (xhr.status === 404) {
                         errorMessage += 'Search endpoint not found.';
                     } else if (xhr.status === 500) {
                         errorMessage += 'Server error occurred.';
                     } else {
                         errorMessage += 'Please check your connection and try again.';
                     }

                     showErrorMessage(errorMessage);
                     $this.button('reset');
                 },
                 complete: function() {
                $this.button('reset');
                // Clear clicked state
                $this.removeAttr("clicked");
            
                     try {
                         // $this.button('reset'); // This line is now redundant
                         // Clear clicked state
                         // $this.removeAttr("clicked"); // This line is now redundant

                         // Ensure form styling is preserved after completion
                         setTimeout(function() {
                             $('.form-control').each(function() {
                                 if (!$(this).hasClass('error')) {
                                     $(this).removeClass('error');
                                     $(this).closest('.form-group').removeClass('has-error');
                                 }
                             });
                         }, 100);

                     } catch (completeError) {
                         console.error('Error in complete handler:', completeError);
                     }
                 }
             });
    } catch (formError) {
        console.error('Form submission error:', formError);
        showErrorMessage('Form submission failed. Please refresh the page and try again.');
    }
});

    });
    function resetFields(search_type){
        if(search_type == "keyword_search"){
            // Reset multi-select dropdowns using SumoSelect
            if ($('#class_id')[0].sumo) {
                $('#class_id')[0].sumo.unSelectAll();
            }
            if ($('#section_id')[0].sumo) {
                $('#section_id')[0].sumo.unSelectAll();
            }
            $('#section_id').html('');
            if ($('#section_id')[0].sumo) {
                $('#section_id')[0].sumo.reload();
            }
        }else if (search_type == "class_search") {
             $('#search_text').val("");
        }
    }

    // Enhanced helper functions for user feedback with CSS preservation
    function showSuccessMessage(message) {
        try {
            $('.alert').remove(); // Remove any existing alerts

            var alertHtml = '<div class="alert alert-success alert-dismissible" role="alert" style="' +
                           'padding: 15px !important; ' +
                           'margin-bottom: 20px !important; ' +
                           'border: 1px solid #d6e9c6 !important; ' +
                           'border-radius: 4px !important; ' +
                           'color: #3c763d !important; ' +
                           'background-color: #dff0d8 !important; ' +
                           'display: block !important;">' +
                           '<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="' +
                           'float: right !important; ' +
                           'font-size: 21px !important; ' +
                           'font-weight: bold !important; ' +
                           'line-height: 1 !important; ' +
                           'color: #000 !important; ' +
                           'text-shadow: 0 1px 0 #fff !important; ' +
                           'opacity: 0.2 !important; ' +
                           'cursor: pointer !important;">' +
                           '<span aria-hidden="true">&times;</span></button>' +
                           '<i class="fa fa-check-circle" style="margin-right: 8px !important;"></i> ' + message +
                           '</div>';

            var targetContainer = $('.box-body').first();
            if (targetContainer.length) {
                targetContainer.prepend(alertHtml);
            } else {
                // Fallback to content area
                $('.content').prepend(alertHtml);
            }

            // Auto-hide after 5 seconds
            setTimeout(function() {
                $('.alert-success').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 5000);

        } catch (error) {
            console.error('Error showing success message:', error);
        }
    }

    function showErrorMessage(message) {
        try {
            $('.alert').remove(); // Remove any existing alerts

            var alertHtml = '<div class="alert alert-danger alert-dismissible" role="alert" style="' +
                           'padding: 15px !important; ' +
                           'margin-bottom: 20px !important; ' +
                           'border: 1px solid #ebccd1 !important; ' +
                           'border-radius: 4px !important; ' +
                           'color: #a94442 !important; ' +
                           'background-color: #f2dede !important; ' +
                           'display: block !important;">' +
                           '<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="' +
                           'float: right !important; ' +
                           'font-size: 21px !important; ' +
                           'font-weight: bold !important; ' +
                           'line-height: 1 !important; ' +
                           'color: #000 !important; ' +
                           'text-shadow: 0 1px 0 #fff !important; ' +
                           'opacity: 0.2 !important; ' +
                           'cursor: pointer !important;">' +
                           '<span aria-hidden="true">&times;</span></button>' +
                           '<i class="fa fa-exclamation-triangle" style="margin-right: 8px !important;"></i> ' + message +
                           '</div>';

            var targetContainer = $('.box-body').first();
            if (targetContainer.length) {
                targetContainer.prepend(alertHtml);
            } else {
                // Fallback to content area
                $('.content').prepend(alertHtml);
            }

            // Auto-hide after 8 seconds
            setTimeout(function() {
                $('.alert-danger').fadeOut(500, function() {
                    $(this).remove();
                });
            }, 8000);

        } catch (error) {
            console.error('Error showing error message:', error);
            // Fallback to basic alert
            alert('Error: ' + message);
        }
    }

    // Enhanced loading state for SumoSelect dropdowns
    function showDropdownLoading(selector) {
        var el = $(selector);
        el.prop('disabled', true);
        if (el[0] && el[0].sumo) {
            el[0].sumo.disable();
        }
        el.next('.SumoSelect').addClass('loading');
    }

    function hideDropdownLoading(selector) {
        var el = $(selector);
        el.prop('disabled', false);
        if (el[0] && el[0].sumo) {
            el[0].sumo.enable();
        }
        el.next('.SumoSelect').removeClass('loading');
    }
</script>
