<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
/* CSS Fix for Student Fee Search Page - Prevents styling loss during validation */

/* Ensure core layout elements maintain styling */
.content-wrapper {
    min-height: 100% !important;
    background-color: #ecf0f5 !important;
    z-index: 800 !important;
}

.content-header {
    padding: 15px !important;
    margin: 0 0 0 0 !important;
}

.content {
    min-height: 250px !important;
    padding: 0 15px !important;
}

/* Fix for box styling during validation errors */
.box {
    position: relative !important;
    border-radius: 3px !important;
    background: #ffffff !important;
    border-top: 3px solid #d2d6de !important;
    margin-bottom: 20px !important;
    width: 100% !important;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1) !important;
}

.box.box-primary {
    border-top-color: #3c8dbc !important;
}

.box-header {
    color: #444 !important;
    display: block !important;
    padding: 10px !important;
    position: relative !important;
}

.box-header.with-border {
    border-bottom: 1px solid #f4f4f4 !important;
}

.box-body {
    border-top-left-radius: 0 !important;
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 3px !important;
    border-bottom-left-radius: 3px !important;
    padding: 10px !important;
}

/* Ensure form elements maintain styling during validation */
.form-control {
    display: block !important;
    width: 100% !important;
    padding: 6px 12px !important;
    font-size: 14px !important;
    line-height: 1.42857143 !important;
    color: #555 !important;
    background-color: #fff !important;
    background-image: none !important;
    border: 1px solid #ccc !important;
    border-radius: 4px !important;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075) !important;
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s !important;
}

.form-control:focus {
    border-color: #66afe9 !important;
    outline: 0 !important;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6) !important;
}

/* Fix for error state styling */
.form-control.error,
.has-error .form-control {
    border-color: #a94442 !important;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075) !important;
}

.form-control.error:focus,
.has-error .form-control:focus {
    border-color: #843534 !important;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 6px #ce8483 !important;
}

/* Fix for validation error messages */
.text-danger {
    color: #a94442 !important;
    font-size: 12px !important;
    margin-top: 5px !important;
    display: block !important;
}

/* Ensure buttons maintain styling */
.btn {
    display: inline-block !important;
    padding: 6px 12px !important;
    margin-bottom: 0 !important;
    font-size: 14px !important;
    font-weight: normal !important;
    line-height: 1.42857143 !important;
    text-align: center !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
    cursor: pointer !important;
    border: 1px solid transparent !important;
    border-radius: 4px !important;
    text-decoration: none !important;
}

.btn-primary {
    color: #fff !important;
    background-color: #337ab7 !important;
    border-color: #2e6da4 !important;
}

.btn-primary:hover,
.btn-primary:focus {
    color: #fff !important;
    background-color: #286090 !important;
    border-color: #204d74 !important;
}

/* Fix for row and column layout */
.row {
    margin-right: -15px !important;
    margin-left: -15px !important;
}

.col-md-12,
.col-md-6,
.col-sm-6,
.col-sm-12 {
    position: relative !important;
    min-height: 1px !important;
    padding-right: 15px !important;
    padding-left: 15px !important;
}

/* Responsive fixes */
@media (min-width: 768px) {
    .col-sm-6 {
        width: 50% !important;
        float: left !important;
    }
    .col-sm-12 {
        width: 100% !important;
        float: left !important;
    }
}

@media (min-width: 992px) {
    .col-md-6 {
        width: 50% !important;
        float: left !important;
    }
    .col-md-12 {
        width: 100% !important;
        float: left !important;
    }
}

/* Fix for form group spacing */
.form-group {
    margin-bottom: 15px !important;
}

.form-group label {
    display: inline-block !important;
    max-width: 100% !important;
    margin-bottom: 5px !important;
    font-weight: bold !important;
}

/* Ensure icons are displayed properly */
.fa {
    display: inline-block !important;
    font: normal normal normal 14px/1 FontAwesome !important;
    font-size: inherit !important;
    text-rendering: auto !important;
    -webkit-font-smoothing: antialiased !important;
    -moz-osx-font-smoothing: grayscale !important;
}

/* Fix for DataTable styling */
.table {
    width: 100% !important;
    max-width: 100% !important;
    margin-bottom: 20px !important;
    background-color: transparent !important;
}

.table > thead > tr > th,
.table > tbody > tr > th,
.table > tfoot > tr > th,
.table > thead > tr > td,
.table > tbody > tr > td,
.table > tfoot > tr > td {
    padding: 8px !important;
    line-height: 1.42857143 !important;
    vertical-align: top !important;
    border-top: 1px solid #ddd !important;
}

.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f9f9f9 !important;
}

.table-bordered {
    border: 1px solid #ddd !important;
}

.table-bordered > thead > tr > th,
.table-bordered > tbody > tr > th,
.table-bordered > tfoot > tr > th,
.table-bordered > thead > tr > td,
.table-bordered > tbody > tr > td,
.table-bordered > tfoot > tr > td {
    border: 1px solid #ddd !important;
}

/* Fix for alert messages */
.alert {
    padding: 15px !important;
    margin-bottom: 20px !important;
    border: 1px solid transparent !important;
    border-radius: 4px !important;
}

.alert-success {
    color: #3c763d !important;
    background-color: #dff0d8 !important;
    border-color: #d6e9c6 !important;
}

.alert-danger {
    color: #a94442 !important;
    background-color: #f2dede !important;
    border-color: #ebccd1 !important;
}

/* Clearfix for proper layout */
.clearfix:before,
.clearfix:after {
    content: " " !important;
    display: table !important;
}

.clearfix:after {
    clear: both !important;
}

/* Fix for pull-right alignment */
.pull-right {
    float: right !important;
}

.pull-left {
    float: left !important;
}
/* Multi-select dropdown enhancements */
.SumoSelect {
    width: 100% !important;
}

.SumoSelect > .CaptionCont {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    background-color: #fff;
    min-height: 34px;
    padding: 6px 12px;
}

.SumoSelect > .CaptionCont > span {
    line-height: 1.42857143;
    color: #555;
    padding-right: 20px;
}

.SumoSelect > .CaptionCont > span.placeholder {
    color: #999;
    font-style: italic;
}

.SumoSelect.open > .CaptionCont,
.SumoSelect:focus > .CaptionCont,
.SumoSelect:hover > .CaptionCont {
    border-color: #66afe9;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);
}

.SumoSelect .optWrapper {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
    background-color: #fff;
    z-index: 9999;
}

.SumoSelect .optWrapper ul.options {
    max-height: 200px;
    overflow-y: auto;
}

.SumoSelect .optWrapper ul.options li {
    padding: 8px 12px;
    border-bottom: 1px solid #f4f4f4;
}

.SumoSelect .optWrapper ul.options li:hover {
    background-color: #f5f5f5;
}

.SumoSelect .optWrapper ul.options li.selected {
    background-color: #337ab7;
    color: #fff;
}

.SumoSelect .search-txt {
    border: 1px solid #d2d6de;
    border-radius: 3px;
    padding: 6px 12px;
    margin: 5px;
    width: calc(100% - 10px);
}

/* Responsive design improvements */
@media (max-width: 768px) {
    .col-sm-6.col-md-6 {
        margin-bottom: 15px;
    }

    .SumoSelect > .CaptionCont {
        min-height: 40px;
        padding: 8px 12px;
    }

    .form-group label {
        font-weight: 600;
        margin-bottom: 5px;
    }
}

@media (max-width: 480px) {
    .SumoSelect > .CaptionCont {
        min-height: 44px;
        padding: 10px 12px;
    }
}

/* Form styling improvements */
.form-group label {
    margin-bottom: 5px;
    font-weight: 500;
}

/* Select all/clear all button styling */
.SumoSelect .select-all {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    padding: 8px 12px;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    display: block !important;
}

.SumoSelect .select-all:hover {
    background-color: #e9ecef;
}

/* Ensure Select All option is visible */
.SumoSelect .optWrapper .options li.opt {
    display: list-item !important;
    padding: 6px 12px;
    cursor: pointer;
}

.SumoSelect .optWrapper .options li.opt:hover {
    background-color: #f5f5f5;
}

/* Select All specific styling */
.SumoSelect .optWrapper .options li.opt.select-all {
    background-color: #e3f2fd;
    border-bottom: 1px solid #bbdefb;
    font-weight: 600;
    color: #1976d2;
}

.SumoSelect .optWrapper .options li.opt.select-all:hover {
    background-color: #bbdefb;
}

/* Loading state for dropdowns */
.SumoSelect.loading > .CaptionCont {
    opacity: 0.6;
    pointer-events: none;
}

.SumoSelect.loading > .CaptionCont:after {
    content: "";
    position: absolute;
    right: 10px;
    top: 50%;
    margin-top: -8px;
    width: 16px;
    height: 16px;
    border: 2px solid #ccc;
    border-top-color: #337ab7;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Error message styling */
.text-danger {
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

/* Form alignment improvements */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

/* Alert message styling */
.alert {
    margin-bottom: 20px;
    border-radius: 4px;
}

.alert-success {
    background-color: #dff0d8;
    border-color: #d6e9c6;
    color: #3c763d;
}

.alert-danger {
    background-color: #f2dede;
    border-color: #ebccd1;
    color: #a94442;
}

.alert .fa {
    margin-right: 8px;
}
</style>
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
                            <div class="box-header ptbnull"></div>
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
        console.log('Document ready, jQuery version:', $.fn.jquery);
        console.log('Found multiselect dropdowns:', $('.multiselect-dropdown').length);

        // Debug: Comprehensive page initialization check
        console.log('🚀 STUDENT FEE SEARCH PAGE INITIALIZATION');
        console.log('Current URL:', window.location.href);
        console.log('Page Title:', document.title);
        console.log('Form Action:', $('.class_search_form').attr('action'));

        // Ensure we're on the correct page
        if (window.location.href.indexOf('studentfee') === -1) {
            console.error('🚫 WARNING: Not on studentfee page! Current URL:', window.location.href);
            alert('Page navigation error detected. You will be redirected to the correct page.');
            window.location.href = '<?php echo base_url(); ?>studentfee';
            return;
        }

        // Check if SumoSelect is available
        if (typeof $.fn.SumoSelect === 'undefined') {
            console.error('SumoSelect plugin not loaded!');
            return;
        }

        // Prevent any unwanted form submissions during dropdown changes
        window.preventFormSubmission = false;

        // Initialize SumoSelect for all multi-select dropdowns
        $('.multiselect-dropdown').SumoSelect({
            placeholder: 'Select Options',
            csvDispCount: 3,
            captionFormat: '{0} Selected',
            captionFormatAllSelected: 'All Selected ({0})',
            selectAll: true,
            search: true,
            searchText: 'Search...',
            noMatch: 'No matches found "{0}"',
            okCancelInMulti: true,
            isClickAwayOk: true,
            locale: ['OK', 'Cancel', 'Select All'],
            up: false,
            showTitle: true
        });

        // Initialize section dropdown on page load if class is pre-selected
        var preSelectedClass = $('#class_id').val();
        if (preSelectedClass && preSelectedClass.length > 0) {
            $('#class_id').trigger('change');
        }

        // Handle class dropdown changes for section population
        $(document).on('change', '#class_id', function (e) {
            // Prevent any form submission or page navigation
            e.preventDefault();
            e.stopPropagation();

            // Set flag to prevent form submission
            window.preventFormSubmission = true;

            console.log('🔍 Class dropdown changed - preventing any redirects');
            console.log('Selected class IDs:', $(this).val());
            console.log('Current URL:', window.location.href);
            console.log('Form submission prevented:', window.preventFormSubmission);

            var sectionDropdown = $('#section_id')[0];
            if (sectionDropdown && sectionDropdown.sumo) {
                sectionDropdown.sumo.removeAll();
            }

            var class_ids = $(this).val();
            var base_url = '<?php echo base_url() ?>';

            if (class_ids && class_ids.length > 0) {
                var requests = [];
                var allSections = [];
                var addedSections = {};

                // Get sections for all selected classes
                $.each(class_ids, function(index, class_id) {
                    requests.push(
                        $.ajax({
                            type: "GET",
                            url: base_url + "sections/getByClass",
                            data: {'class_id': class_id},
                            dataType: "json",
                            success: function(data) {
                                if (data && Array.isArray(data)) {
                                    $.each(data, function(i, obj) {
                                        // Avoid duplicate sections
                                        if (!addedSections[obj.section_id]) {
                                            allSections.push({
                                                value: obj.section_id,
                                                text: obj.section
                                            });
                                            addedSections[obj.section_id] = true;
                                        }
                                    });
                                }
                            }
                        })
                    );
                });

                // Wait for all requests to complete
                $.when.apply($, requests).done(function() {
                    // Add sections to dropdown
                    if (sectionDropdown && sectionDropdown.sumo && allSections.length > 0) {
                        $.each(allSections, function(index, section) {
                            sectionDropdown.sumo.add(section.value, section.text);
                        });
                        // Refresh the dropdown to ensure proper display
                        sectionDropdown.sumo.reload();
                    }

                    // Reset form submission flag after dropdown population is complete
                    setTimeout(function() {
                        window.preventFormSubmission = false;
                        console.log('✅ Form submission re-enabled after section population');
                    }, 500);
                });
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function(){ 
      $("form.class_search_form button[type=submit]").click(function() {
        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });


$(document).on('submit','.class_search_form',function(e){
    e.preventDefault(); // avoid to execute the actual submit of the form.

    // Check if form submission should be prevented (during dropdown changes)
    if (window.preventFormSubmission) {
        console.log('🚫 Form submission blocked - dropdown change in progress');
        setTimeout(function() {
            window.preventFormSubmission = false;
        }, 1000);
        return false;
    }

    try {
        var $this = $("button[type=submit][clicked=true]");
        var form = $(this);
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
                     try {
                         $this.button('reset');

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
        $(selector).prop('disabled', true);
        $(selector).next('.SumoSelect').addClass('loading');
    }

    function hideDropdownLoading(selector) {
        $(selector).prop('disabled', false);
        $(selector).next('.SumoSelect').removeClass('loading');
    }
</script>
