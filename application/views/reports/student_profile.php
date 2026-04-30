<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>

/* Student Profile Table - Enable horizontal scroll for wide tables */
.box-body.table-responsive {
    overflow-x: auto !important;
}

#student-profile-list {
    width: auto !important;
    min-width: 100%;
    table-layout: auto !important;
    white-space: nowrap;
}

#student-profile-list th {
    white-space: nowrap;
}

#student-profile-list tbody td {
    white-space: nowrap;
}

/* Allow empty state message to wrap */
#student-profile-list tbody td.dataTables_empty {
    white-space: normal !important;
}

/* Custom scrollbar styling for student profile table */
.box-body.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.box-body.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.box-body.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.box-body.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<style type="text/css">
    /*REQUIRED*/
    .carousel-row {
        margin-bottom: 10px;
    }
    .slide-row {
        padding: 0;
        background-color: #ffffff;
        min-height: 150px;
        border: 1px solid #e7e7e7;
        overflow: hidden;
        height: auto;
        position: relative;
    }
    .slide-carousel {
        width: 20%;
        float: left;
        display: inline-block;
    }
    .slide-carousel .carousel-indicators {
        margin-bottom: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .5);
    }
    .slide-carousel .carousel-indicators li {
        border-radius: 0;
        width: 20px;
        height: 6px;
    }
    .slide-carousel .carousel-indicators .active {
        margin: 1px;
    }
    .slide-content {
        position: absolute;
        top: 0;
        left: 20%;
        display: block;
        float: left;
        width: 80%;
        max-height: 76%;
        padding: 1.5% 2% 2% 2%;
        overflow-y: auto;
    }
    .slide-content h4 {
        margin-bottom: 3px;
        margin-top: 0;
    }
    .slide-footer {
        position: absolute;
        bottom: 0;
        left: 20%;
        width: 78%;
        height: 20%;
        margin: 1%;
    }
    /* Scrollbars */
    .slide-content::-webkit-scrollbar {
        width: 5px;
    }
    .slide-content::-webkit-scrollbar-thumb:vertical {
        margin: 5px;
        background-color: #999;
        -webkit-border-radius: 5px;
    }
    .slide-content::-webkit-scrollbar-button:start:decrement,
    .slide-content::-webkit-scrollbar-button:end:increment {
        height: 5px;
        display: block;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-bus"></i> <?php //echo $this->lang->line('transport'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_studentinformation'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form role="form" id="reportform" action="<?php echo site_url('report/searchstudentprofilevalidation') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="col-sm-6 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search_by_admission_date'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">

                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {

                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                                <?php } ?>
                                    </select>
                                    <span class="text-danger" id="error_search_type"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>
                            <div id='date_result'>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('class'); ?></label>
                                    <select id="class_id" name="class_id[]" class="form-control multiselect-dropdown" multiple>
                                        <?php
                                        foreach ($classlist as $class) {
                                            ?>
                                            <option value="<?php echo $class['id'] ?>" <?php
                                            if ($class_id == $class['id']) {
                                                echo "selected =selected";
                                            }
                                            ?>><?php echo $class['class'] ?></option>
                                                    <?php
                                                    $count++;
                                                }
                                                ?>
                                    </select>
                                    <span class="text-danger" id="error_class_id"><?php echo form_error('class_id'); ?></span>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('section'); ?></label>
                                    <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                    </select>
                                    <span class="text-danger" id="error_section_id"><?php echo form_error('section_id'); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="">
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php echo $this->lang->line('student_profile'); ?></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label"> <?php echo $this->lang->line('student_profile').' '.
                                            $this->customlib->get_postmessage();
                                            ?></div>
                            <table class="table table-striped table-bordered table-hover student-profile-list" id="student-profile-list" data-export-title="<?php echo $this->lang->line('student_profile'); ?>">
                                <thead>
                                    <tr>
                                        <?php if (!$adm_auto_insert) {
                                            ?>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <?php
                                        }
                                        if ($sch_setting->roll_no) {
                                            ?>
                                            <th><?php echo $this->lang->line('roll_number'); ?></th>
                                        <?php } ?>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <th><?php echo $this->lang->line('section'); ?></th>
                                        <th><?php echo $this->lang->line('first_name'); ?></th>
                                        <?php if ($sch_setting->middlename) { ?>
                                         <th><?php echo $this->lang->line('middle_name'); ?></th>
<?php } if ($sch_setting->lastname) { ?>
                                            <th><?php echo $this->lang->line('last_name'); ?></th>
                                        <?php } ?>
                                        <th><?php echo $this->lang->line('gender'); ?></th>
                                        <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                        <?php if ($sch_setting->category) { ?>
                                            <th><?php echo $this->lang->line('category'); ?></th>
                                        <?php } if ($sch_setting->religion) { ?>
                                            <th><?php echo $this->lang->line('religion'); ?></th>
                                        <?php } if ($sch_setting->cast) { ?>
                                            <th><?php echo $this->lang->line('caste'); ?></th>
                                        <?php } if ($sch_setting->mobile_no) { ?>
                                            <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                        <?php } if ($sch_setting->student_email) { ?>
                                            <th><?php echo $this->lang->line('email'); ?></th>
                                        <?php } if ($sch_setting->admission_date) { ?>
                                            <th><?php echo $this->lang->line('admission_date'); ?></th>
                                        <?php } if ($sch_setting->is_blood_group) { ?>
                                            <th><?php echo $this->lang->line('blood_group'); ?></th>
                                        <?php } if ($sch_setting->is_student_house) { ?>
                                            <th><?php echo $this->lang->line('house') ?></th>
                                        <?php } if ($sch_setting->student_height) { ?>
                                            <th><?php echo $this->lang->line('height'); ?></th>
                                        <?php } if ($sch_setting->student_weight) { ?>
                                            <th><?php echo $this->lang->line('weight'); ?></th>
                                        <?php } if ($sch_setting->measurement_date) { ?>
                                            <th><?php echo $this->lang->line('measurement_date'); ?></th>
                                        <?php } ?>
                                        <th><?php echo $this->lang->line('fees_discount'); ?></th>
                                        <?php if ($sch_setting->father_name) { ?>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <?php } if ($sch_setting->father_phone) { ?>
                                            <th><?php echo $this->lang->line('father_phone'); ?></th>
                                        <?php } if ($sch_setting->father_occupation) { ?>
                                            <th><?php echo $this->lang->line('father_occupation'); ?></th>
                                        <?php } if ($sch_setting->mother_name) { ?>
                                            <th><?php echo $this->lang->line('mother_name'); ?></th>
                                        <?php } if ($sch_setting->mother_phone) { ?>
                                            <th><?php echo $this->lang->line('mother_phone'); ?></th>
                                       <?php } if ($sch_setting->mother_occupation) { ?>
                                            <th><?php echo $this->lang->line('mother_occupation'); ?></th>
                                        <?php } ?>
                                        
                                         <?php if ($sch_setting->guardian_name) { ?>
                                            <th><?php echo $this->lang->line('if_guardian_is'); ?></th>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                        <?php } if ($sch_setting->guardian_relation) { ?>
                                            <th><?php echo $this->lang->line('guardian_relation'); ?></th>
                                        <?php } if ($sch_setting->guardian_phone) { ?>
                                        <th><?php echo $this->lang->line('guardian_phone'); ?></th>
                                   <?php } if ($sch_setting->guardian_occupation) { ?>
                                        <th><?php echo $this->lang->line('guardian_occupation'); ?></th><?php } if ($sch_setting->guardian_email) { ?>
                                            <th><?php echo $this->lang->line('guardian_email'); ?></th>
                                        <?php } if ($sch_setting->guardian_address) { ?>
                                            <th><?php echo $this->lang->line('guardian_address'); ?></th>

                                        <?php } if ($sch_setting->current_address) { ?>
                                            <th><?php echo $this->lang->line('current_address'); ?></th>
                                        <?php } if ($sch_setting->permanent_address) { ?>
                                            <th><?php echo $this->lang->line('permanent_address'); ?></th>
                                        <?php } if ($sch_setting->route_list) { ?>
                                            <th><?php echo $this->lang->line('route_list'); ?></th>
                                        <?php } if ($sch_setting->hostel_id) { ?>
                                            <th><?php echo $this->lang->line('hostel_details'); ?></th>
                                        <?php } ?>
                                        <th><?php echo $this->lang->line('room_no'); ?></th>
                                        <?php if ($sch_setting->bank_account_no) { ?>
                                            <th><?php echo $this->lang->line('bank_account_number'); ?></th>
                                        <?php } if ($sch_setting->bank_name) { ?>
                                            <th><?php echo $this->lang->line('bank_name'); ?></th>
                                         <?php } if ($sch_setting->ifsc_code) { ?>
                                        <th><?php echo $this->lang->line('ifsc_code'); ?></th>
                                        <?php } if ($sch_setting->national_identification_no) { ?>
                                            <th><?php echo $this->lang->line('national_identification_number'); ?></th>
                                        <?php } if ($sch_setting->local_identification_no) { ?>
                                            <th><?php echo $this->lang->line('local_identification_number'); ?></th>
                                        <?php } if ($sch_setting->rte) { ?>
                                            <th><?php echo $this->lang->line('rte'); ?></th>
                                        <?php } if ($sch_setting->previous_school_details) { ?>
                                            <th><?php echo $this->lang->line('previous_school_details'); ?></th>
<?php } if ($sch_setting->student_note) { ?>
                                            <th><?php echo $this->lang->line('note'); ?></th>
                                    <?php } ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- DataTable will populate this dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>  
</section>
</div>

<script>
<?php
if ($search_type == 'period') {
    ?>

        $(document).ready(function () {
            showdate('period');
        });

    <?php
}
?>
</script>

<script type="text/javascript">
// Global helper functions for user feedback
function showSuccessMessage(message) {
    $('.alert').remove(); // Remove any existing alerts
    var alertHtml = '<div class="alert alert-success alert-dismissible" role="alert">' +
                   '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                   '<span aria-hidden="true">&times;</span></button>' +
                   '<i class="fa fa-check-circle"></i> ' + message +
                   '</div>';
    $('.box-body').prepend(alertHtml);

    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut();
    }, 5000);
}

function showErrorMessage(message) {
    $('.alert').remove(); // Remove any existing alerts
    var alertHtml = '<div class="alert alert-danger alert-dismissible" role="alert">' +
                   '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                   '<span aria-hidden="true">&times;</span></button>' +
                   '<i class="fa fa-exclamation-triangle"></i> ' + message +
                   '</div>';
    $('.box-body').prepend(alertHtml);

    // Auto-hide after 8 seconds
    setTimeout(function() {
        $('.alert-danger').fadeOut();
    }, 8000);
}
</script>

<script type="text/javascript">
$(document).ready(function () {
    // Initialize SumoSelect for multi-select dropdowns

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

    // Initialize empty datatable
    if (typeof emptyDatatable === 'function') {
        emptyDatatable('student-profile-list','data');
    }

    // Handle class dropdown changes for section population
    $(document).on('change', '#class_id', function (e) {
        var sectionDropdown = $('#section_id')[0];
        if (sectionDropdown && sectionDropdown.sumo) {
            sectionDropdown.sumo.removeAll();
        }

        var class_ids = $(this).val();
        var base_url = baseurl;

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
            });
        }
    });



    // Enhanced loading state for SumoSelect dropdowns
    function showDropdownLoading(selector) {
        $(selector).prop('disabled', true);
        $(selector).next('.SumoSelect').addClass('loading');
    }

    function hideDropdownLoading(selector) {
        $(selector).prop('disabled', false);
        $(selector).next('.SumoSelect').removeClass('loading');
    }
});
</script>

<script type="text/javascript">
$(document).ready(function(){
$(document).on('submit','#reportform',function(e){
    e.preventDefault(); // avoid to execute the actual submit of the form.
    console.log('🔍 Student Profile Form Submit Started');

    var $this = $(this).find("button[type=submit]:focus");
    var form = $(this);
    var url = form.attr('action');

    // Get form data using a more reliable method for SumoSelect
    var form_data = [];

    // Add CSRF token
    var csrf_token = $('input[name="ci_csrf_token"]').val();
    if (csrf_token) {
        form_data.push({name: 'ci_csrf_token', value: csrf_token});
    }

    // Get class values
    var class_values = $('#class_id').val() || [];
    console.log('📤 Class Values:', class_values);
    if (class_values && class_values.length > 0) {
        for (var i = 0; i < class_values.length; i++) {
            if (class_values[i]) {
                form_data.push({name: 'class_id[]', value: class_values[i]});
            }
        }
    }

    // Get section values
    var section_values = $('#section_id').val() || [];
    console.log('📤 Section Values:', section_values);
    if (section_values && section_values.length > 0) {
        for (var i = 0; i < section_values.length; i++) {
            if (section_values[i]) {
                form_data.push({name: 'section_id[]', value: section_values[i]});
            }
        }
    }

    // Get search type
    var search_type = $('select[name="search_type"]').val() || '';
    form_data.push({name: 'search_type', value: search_type});

    console.log('📤 Final Form Data:', form_data);
    console.log('📤 Form URL:', url);

    $.ajax({
           url: url,
           type: "POST",
           dataType:'JSON',
           data: form_data, // serializes the form's elements.
              beforeSend: function () {
                console.log('⏳ AJAX Request Starting...');
                $('[id^=error]').html("");
                $this.button('loading');
               },
              success: function(response) { // your success handler
                console.log('✅ AJAX Success Response:', response);

                if(!response.status){
                    console.log('❌ Validation Failed:', response.error);
                    $.each(response.error, function(key, value) {
                    $('#error_' + key).html(value);
                    });
                }else{
                   console.log('🎯 Validation Success - Initializing DataTable');
                   console.log('📊 DataTable Params:', response.params);

                   // Ensure the table element exists and is ready
                   var tableElement = $('.student-profile-list');
                   if (tableElement.length === 0) {
                       console.error('❌ Table element .student-profile-list not found!');
                       showErrorMessage('Table element not found. Please refresh the page.');
                       return;
                   }

                   console.log('📊 Table element found:', tableElement.length);

                   // Check if initDatatable function exists
                   if (typeof initDatatable === 'function') {
                       console.log('✅ initDatatable function found');
                       console.log('🔧 Calling initDatatable with params:', response.params);

                       try {
                           // Debug: Check table structure
                           var tableHeaders = $('.student-profile-list thead th').length;
                           console.log('📊 Table has ' + tableHeaders + ' header columns');

                           // Debug: Log the exact parameters being passed
                           console.log('🔧 DataTable Parameters:');
                           console.log('  - Selector: student-profile-list');
                           console.log('  - URL: report/dtstudentprofilereportlist');
                           console.log('  - Params:', JSON.stringify(response.params));

                           // Initialize new DataTable using class selector (initDatatable expects class names)
                           initDatatable('student-profile-list','report/dtstudentprofilereportlist',response.params);
                           console.log('✅ initDatatable called successfully');

                           // Debug: Check if DataTable was actually created
                           setTimeout(function() {
                               var dtInstance = $('.student-profile-list').DataTable();
                               if (dtInstance) {
                                   console.log('📊 DataTable instance created');
                                   console.log('📊 DataTable info:', dtInstance.page.info());
                               } else {
                                   console.error('❌ DataTable instance not found');
                               }
                           }, 1000);

                       } catch (error) {
                           console.error('❌ Error calling initDatatable:', error);
                           console.error('Error stack:', error.stack);
                           showErrorMessage('Error initializing data table: ' + error.message);
                       }
                   } else {
                       console.error('❌ initDatatable function not found!');
                       console.log('Available window functions:', Object.keys(window).filter(key => typeof window[key] === 'function' && key.includes('init')));
                       showErrorMessage('DataTable initialization function not found. Please refresh the page.');
                   }
                }
              },
              error: function(jqXHR, textStatus, errorThrown) { // your error handler
                console.error('❌ AJAX Error Details:');
                console.error('Status:', jqXHR.status);
                console.error('Status Text:', jqXHR.statusText);
                console.error('Response Text:', jqXHR.responseText);
                console.error('Error Thrown:', errorThrown);
                showErrorMessage('An error occurred while processing your request. Please try again.');
              },
              complete: function() {
                console.log('🏁 AJAX Request Complete');
                $this.button('reset');
              }
    });
});
});
</script>