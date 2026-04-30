<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
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

    /* Fix table width and prevent horizontal scrolling */
    .student-list {
        width: 100% !important;
        table-layout: auto !important;
    }

    .student-list th,
    .student-list td {
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
        overflow-wrap: break-word !important;
        max-width: 200px;
    }

    /* Specific column widths to prevent table expansion */
    .student-list th:nth-child(1),
    .student-list td:nth-child(1) {
        width: 100px !important;
        max-width: 100px !important;
    }

    .student-list th:nth-child(2),
    .student-list td:nth-child(2) {
        width: 150px !important;
        max-width: 150px !important;
    }

    .student-list th:nth-child(3),
    .student-list td:nth-child(3) {
        width: 100px !important;
        max-width: 100px !important;
    }

    .student-list th:nth-child(4),
    .student-list td:nth-child(4) {
        width: 120px !important;
        max-width: 120px !important;
    }

    .student-list th:nth-child(5),
    .student-list td:nth-child(5) {
        width: 90px !important;
        max-width: 90px !important;
    }

    .student-list th:nth-child(6),
    .student-list td:nth-child(6) {
        width: 60px !important;
        max-width: 60px !important;
    }

    .student-list th:nth-child(7),
    .student-list td:nth-child(7) {
        width: 80px !important;
        max-width: 80px !important;
    }

    .student-list th:nth-child(8),
    .student-list td:nth-child(8) {
        width: 100px !important;
        max-width: 100px !important;
    }

    .student-list th:last-child,
    .student-list td:last-child {
        width: 100px !important;
        max-width: 100px !important;
        text-align: right;
    }

    /* Override DataTables default styles */
    .dataTables_wrapper {
        width: 100% !important;
        overflow-x: visible !important;
    }

    .dataTables_wrapper .dataTables_scroll {
        overflow-x: visible !important;
    }

    .dataTables_wrapper .dataTables_scrollBody {
        overflow-x: visible !important;
    }

    /* Remove table-responsive horizontal scroll */
    .table-responsive {
        overflow-x: visible !important;
    }

    /* Make action buttons wrap if needed */
    .student-list td .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
    }

    .student-list td .btn-xs {
        font-size: 11px;
        padding: 2px 8px;
        margin: 1px;
    }

    /* Responsive adjustments for smaller screens */
    @media (max-width: 1200px) {
        .student-list th,
        .student-list td {
            font-size: 12px;
            padding: 6px 4px;
        }
    }
</style>



<div class="content-wrapper">
    <section class="content-header">

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

                        <?php if ($this->session->flashdata('msg')) { ?> <div class="alert alert-success"> <?php echo $this->session->flashdata('msg');
                                                                                                            $this->session->unset_userdata('msg'); ?> </div> <?php } ?>
                        <div class="row">
                            <form role="form" action="<?php echo site_url('student/searchvalidation') ?>" method="post" class="class_search_form">
                                <div class="col-md-6">
                                    <div class="row">
                                        <?php echo $this->customlib->getCSRF(); ?>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('class'); ?></label>
                                                <select id="class_id" name="class_id[]" class="form-control multiselect-dropdown" multiple>
                                                    <?php
                                                    if (isset($classlist) && !empty($classlist)) {
                                                        foreach ($classlist as $class) {
                                                    ?>
                                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
                                                                                                            echo "selected=selected";
                                                                                                        }
                                                                                                        ?>><?php echo $class['class'] ?></option>
                                                    <?php
                                                        }
                                                    } else {
                                                        echo '<option value="">No classes available</option>';
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
                                                <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm pull-right checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./col-md-6-->

                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('search_by_keyword'); ?></label>
                                                <input type="text" name="search_text" id="search_text" class="form-control" value="<?php echo set_value('search_text'); ?>" placeholder="<?php echo $this->lang->line('search_by_student_name'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" name="search" value="search_full" class="btn btn-primary pull-right btn-sm checkbox-toggle"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--./col-md-6-->
                            </form>
                        </div><!--./row-->
                    </div>

                    <div class="nav-tabs-custom border0 navnoshadow">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_1" data-toggle="tab" aria-expanded="true"><i class="fa fa-list"></i> <?php echo $this->lang->line('list_view'); ?></a></li>
                            <li class=""><a href="#tab_2" data-toggle="tab" aria-expanded="false"><i class="fa fa-newspaper-o"></i> <?php echo $this->lang->line('details_view'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active table-responsive no-padding overflow-visible-lg" id="tab_1">
                                <table class="table table-striped table-bordered table-hover student-list" data-export-title="<?php echo $this->lang->line('student_list'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <?php if ($sch_setting->father_name) { ?>
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <?php } ?>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <?php if ($sch_setting->category) {
                                            ?>
                                                <?php if ($sch_setting->category) { ?>
                                                    <th><?php echo $this->lang->line('category'); ?></th>
                                                <?php }
                                            }
                                            if ($sch_setting->mobile_no) {
                                                ?>
                                                <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                                <?php
                                            }
                                            if (!empty($fields)) {

                                                foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                    <th><?php echo $fields_value->name; ?></th>
                                            <?php
                                                }
                                            }
                                            ?>
                                            <th class="text-right noExport"><?php echo $this->lang->line('action'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane detail_view_tab" id="tab_2">
                                <?php if (empty($resultlist)) {
                                ?>
                                    <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($resultlist as $student) {

                                        if (empty($student["image"])) {
                                            if ($student['gender'] == 'Female') {
                                                $image = "uploads/student_images/default_female.jpg";
                                            } else {
                                                $image = "uploads/student_images/default_male.jpg";
                                            }
                                        } else {
                                            $image = $student['image'];
                                        }
                                    ?>
                                        <div class="carousel-row">
                                            <div class="slide-row">
                                                <div id="carousel-2" class="carousel slide slide-carousel" data-ride="carousel">
                                                    <div class="carousel-inner">
                                                        <div class="item active">
                                                            <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id'] ?>">
                                                                <?php if ($sch_setting->student_photo) { ?><img class="img-responsive img-thumbnail width150" alt="<?php echo $student["firstname"] . " " . $student["lastname"] ?>" src="<?php echo $this->media_storage->getImageURL($image); ?>" alt="Image"><?php } ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="slide-content">
                                                    <h4><a href="<?php echo base_url(); ?>student/view/<?php echo $student['id'] ?>"> <?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></a></h4>
                                                    <div class="row">
                                                        <div class="col-xs-6 col-md-6">
                                                            <address>
                                                                <strong><b><?php echo $this->lang->line('class'); ?>: </b><?php echo $student['class'] . "(" . $student['section'] . ")" ?></strong><br>
                                                                <b><?php echo $this->lang->line('admission_no'); ?>: </b><?php echo $student['admission_no'] ?><br />
                                                                <b><?php echo $this->lang->line('date_of_birth'); ?>:
                                                                    <?php if ($student["dob"] != null && $student["dob"] != '0000-00-00') {
                                                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($student['dob']));
                                                                    } ?><br>
                                                                    <b><?php echo $this->lang->line('gender'); ?>:&nbsp;</b><?php echo $this->lang->line(strtolower($student['gender'])) ?><br>
                                                            </address>
                                                        </div>
                                                        <div class="col-xs-6 col-md-6">
                                                            <b><?php echo $this->lang->line('local_identification_no'); ?>:&nbsp;</b><?php echo $student['samagra_id'] ?><br>
                                                            <?php if ($sch_setting->guardian_name) { ?>
                                                                <b><?php echo $this->lang->line('guardian_name'); ?>:&nbsp;</b><?php echo $student['guardian_name'] ?><br>
                                                            <?php }
                                                            if ($sch_setting->guardian_name) { ?>
                                                                <b><?php echo $this->lang->line('guardian_phone'); ?>: </b> <abbr title="Phone"><i class="fa fa-phone-square"></i>&nbsp;</abbr> <?php echo $student['guardian_phone'] ?><br> <?php } ?>
                                                            <b><?php echo $this->lang->line('current_address'); ?>:&nbsp;</b><?php echo $student['current_address'] ?> <?php echo $student['city'] ?><br>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="slide-footer">
                                                    <span class="pull-right buttons">
                                                        <a href="<?php echo base_url(); ?>student/view/<?php echo $student['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('view'); ?>">
                                                            <i class="fa fa-reorder"></i>
                                                        </a>
                                                        <?php
                                                        if ($this->rbac->hasPrivilege('student', 'can_edit')) {
                                                        ?>
                                                            <a href="<?php echo base_url(); ?>student/edit/<?php echo $student['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </a>
                                                        <?php
                                                        }
                                                        if ($this->module_lib->hasActive('fees_collection') && $this->rbac->hasPrivilege('collect_fees', 'can_add')) {
                                                        ?>
                                                            <a href="<?php echo base_url(); ?>studentfee/addfee/<?php echo $student['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="" data-original-title="<?php echo $this->lang->line('add_fees'); ?>">
                                                                <?php echo $currency_symbol; ?>
                                                            </a>
                                                        <?php } ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    }
                                    $count++;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div><!--./box box-primary -->

            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
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

        // Listen for DataTable AJAX responses to update the Details View tab automatically
        $(document).on('xhr.dt', '.student-list', function(e, settings, json, xhr) {
            if (json && json.student_detail_view) {
                $('#tab_2').html(json.student_detail_view);
            }
        });

        // Handle class dropdown changes for section population
        $(document).on('change', '#class_id', function(e) {
            var $classSelect = $(this);
            var sectionDropdown = $('#section_id')[0];
            var class_ids = $classSelect.val(); // This will be an array for multi-select
            var base_url = '<?php echo base_url() ?>';

            // Defer execution to let SumoSelect state settle
            setTimeout(function() {
                var current_class_ids = $classSelect.val();
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

<script>
    $(document).ready(function() {
        emptyDatatable('student-list', 'data');
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {

        $("form.class_search_form button[type=submit]").click(function() {
            $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
            $(this).attr("clicked", "true");
        });

        // Enter key handler for search fields
        $('#search_text, #class_id, #section_id').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                var classVal = $('#class_id').val();
                var searchVal = $('#search_text').val();
                if (classVal && classVal.length > 0) {
                    $("button[type=submit][value='search_filter']").click();
                } else if (searchVal && searchVal.trim() !== '') {
                    $("button[type=submit][value='search_full']").click();
                }
            }
        });

        $(document).on('submit', '.class_search_form', function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var $this = $("button[type=submit][clicked=true]");

            // Fallback: if no button was clicked (e.g. SumoSelect OK triggered submit), use first button
            if ($this.length === 0) {
                $this = $("button[type=submit]").first();
                $this.attr("clicked", "true");
            }

            var form = $(this);
            var url = form.attr('action');
            var form_data = form.serializeArray();

            // Fix: SumoSelect may not sync values to original <select> when okCancelInMulti is used.
            // Manually read SumoSelect selected values from the original <select> element.
            var classSelect = $('#class_id')[0];
            var sectionSelect = $('#section_id')[0];

            if (classSelect) {
                // Remove any existing class_id entries from serialized data
                form_data = form_data.filter(function(item) { return item.name !== 'class_id'; });
                // Read selected options from the original <select> (SumoSelect updates these)
                $(classSelect).find('option:selected').each(function() {
                    var val = $(this).val();
                    if (val !== '' && val !== null) {
                        form_data.push({ name: 'class_id', value: val });
                    }
                });
            }

            if (sectionSelect) {
                // Remove any existing section_id entries from serialized data
                form_data = form_data.filter(function(item) { return item.name !== 'section_id'; });
                // Read selected options from the original <select>
                $(sectionSelect).find('option:selected').each(function() {
                    var val = $(this).val();
                    if (val !== '' && val !== null) {
                        form_data.push({ name: 'section_id', value: val });
                    }
                });
            }

            form_data.push({
                name: 'search_type',
                value: $this.attr('value')
            });

            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: form_data, // serializes the form's elements.
                beforeSend: function() {
                    console.log('📤 AJAX Request Starting...');
                    $('[id^=error]').html("");
                    $this.button('loading');
                    resetFields($this.attr('value'));
                },
                success: function(response) { // your success handler
                    console.log('✅ AJAX Response Received:', response);

                    if (!response.status) {
                        console.error('❌ Validation Error:', response.error);
                        $.each(response.error, function(key, value) {
                            $('#error_' + key).html(value);
                        });
                    } else {
                        console.log('🎯 Student Search - Initializing DataTable...');
                        console.log('DataTable ID: student-list');
                        console.log('DataTable URL: student/dtstudentlist');
                        console.log('DataTable Params:', response.params);

                        $('[id^=error]').html("");

                        // Use the same approach as studentReport.php
                        console.log('🎯 Initializing DataTable with response params:', response.params);

                        $('[id^=error]').html("");
                        initDatatable('student-list', 'student/dtstudentlist', response.params, [], 100);
                    }
                },
                error: function(xhr, status, error) { // your error handler
                    console.error('❌ AJAX Error:', status, error);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status Code:', xhr.status);
                    showErrorMessage('Network error occurred. Please check your connection and try again.');
                    $this.button('reset');
                },
                complete: function() {
                    console.log('🏁 AJAX Request Complete');
                    $this.button('reset');
                    // Clear the clicked attribute so future spurious submits (like SumoSelect OK) don't trigger this
                    $this.removeAttr("clicked");
                }
            });

        });

    });

    function resetFields(search_type) {

        if (search_type == "search_full") {
            // Reset multi-select dropdowns using SumoSelect
            if ($('#class_id')[0] && $('#class_id')[0].sumo) {
                $('#class_id')[0].sumo.unSelectAll();
            }
            if ($('#section_id')[0] && $('#section_id')[0].sumo) {
                $('#section_id')[0].sumo.unSelectAll();
            }
        } else if (search_type == "search_filter") {
            $('#search_text').val('');
        }
    }

    // Helper functions for user feedback
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