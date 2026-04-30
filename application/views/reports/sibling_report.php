<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-bus"></i> <?php //echo $this->lang->line('transport'); 
                                        ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_studentinformation') ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form role="form" id="reportform" action="<?php echo site_url('report/siblingsearchvalidation') ?>" method="post" class="">
                        <div class="box-body">
                            <div class="row">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-6 col-md-6">
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
                                        <span class="text-danger" id="error_class_id"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                        </select>
                                        <span class="text-danger" id="error_section_id"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="">
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"> </i> <?php echo $this->lang->line('sibling_report'); ?></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php echo $this->lang->line('sibling_report') . " " . $this->customlib->get_postmessage();
                                                        ?></div>
                            <table class="table table-striped table-bordered table-hover sibling-list" id="sibling-list">
                                <thead>
                                    <tr>
                                        <?php if ($sch_setting->father_name) { ?>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <?php }
                                        if ($sch_setting->mother_name) { ?>
                                            <th><?php echo $this->lang->line('mother_name'); ?></th>
                                        <?php }
                                        if ($sch_setting->guardian_name) { ?>
                                            <th><?php echo $this->lang->line('guardian_name') ?></th>
                                        <?php }
                                        if ($sch_setting->guardian_phone) { ?>
                                            <th><?php echo $this->lang->line('guardian_phone') ?></th>
                                        <?php } ?>
                                        <th><?php echo $this->lang->line('student_name_sibling'); ?></th>
                                        <th><?php echo $this->lang->line('class'); ?></th>
                                        <?php if ($sch_setting->admission_date) { ?>
                                            <th><?php echo $this->lang->line('admission_date'); ?></th>
                                        <?php } ?>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($resultlist)) {    ?>
                                        <?php
                                    } else {
                                        $count = 1;
                                        foreach ($resultlist as $student) {
                                            if (count($student) > 1) {
                                        ?>
                                                <tr>
                                                    <?php if ($sch_setting->father_name) { ?>
                                                        <td><?php echo $student[0]['father_name']; ?></td>
                                                    <?php }
                                                    if ($sch_setting->mother_name) { ?>
                                                        <td><?php echo $student[0]['mother_name']; ?></td>
                                                    <?php }
                                                    if ($sch_setting->guardian_name) { ?>
                                                        <td><?php echo $student[0]['guardian_name']; ?></td>
                                                    <?php }
                                                    if ($sch_setting->guardian_phone) { ?>
                                                        <td><?php echo $student[0]['guardian_phone']; ?></td>
                                                    <?php } ?>
                                                    <td>
                                                        <table>
                                                            <?php foreach ($student as $value) { ?>
                                                                <tr>
                                                                    <td>
                                                                        <a href="<?php echo base_url(); ?>student/view/<?php echo $value['id']; ?>"><?php echo $this->customlib->getFullName($value['firstname'], $value['middlename'], $value['lastname'], $sch_setting->middlename, $sch_setting->lastname) . ' (' . $value['admission_no'] . ')'; ?></a>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </table>
                                                    </td>
                                                    <td>
                                                        <table>
                                                            <?php foreach ($student as $value) { ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php echo $value['class'] . " (" . $value['section'] . ")"; ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </table>
                                                    </td>
                                                    <?php if ($sch_setting->admission_date) { ?>
                                                        <td>
                                                            <table>
                                                                <?php foreach ($student as $value) { ?>
                                                                    <tr>
                                                                        <td>
                                                                            <?php
                                                                            if (!empty($value['admission_date'])) {
                                                                                echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['admission_date']));
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            </table>
                                                        </td>
                                                    <?php } ?>
                                                    <td class="pull-right">
                                                        <table width="100%">
                                                            <?php foreach ($student as $value) { ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php
                                                                        if (!empty($value['gender'])) {
                                                                            echo $this->lang->line(strtolower($value['gender']));
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </table>
                                                    </td>
                                                </tr>
                                    <?php
                                                $count++;
                                            }
                                        }
                                    }
                                    ?>
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
    $(document).ready(function() {
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


        // Initialize empty datatable for proper empty state
        if (typeof emptyDatatable === 'function') {
            emptyDatatable('sibling-list', 'data');
        } else {
            console.error('emptyDatatable function not found');
        }
        // Handle class selection change for sections
        $(document).on('change', '#class_id', function(e) {
            var class_ids = $(this).val();

            // Clear section dropdown
            $('#section_id').empty();

            if (class_ids && class_ids.length > 0) {
                var div_data = '';
                var processedSections = new Set();
                var sectionsToAdd = [];

                // Process each selected class
                var processedClasses = 0;

                class_ids.forEach(function(class_id, index) {
                    $.ajax({
                        type: "GET",
                        url: baseurl + "sections/getByClass",
                        data: {
                            'class_id': class_id
                        },
                        dataType: "json",
                        success: function(data) {
                            $.each(data, function(i, obj) {
                                var sectionKey = obj.section_id + '_' + obj.section;
                                if (!processedSections.has(sectionKey)) {
                                    processedSections.add(sectionKey);
                                    sectionsToAdd.push({
                                        id: obj.section_id,
                                        name: obj.section
                                    });
                                }
                            });

                            processedClasses++;

                            // If all classes have been processed, update the section dropdown
                            if (processedClasses === class_ids.length) {
                                // Sort sections by name
                                sectionsToAdd.sort(function(a, b) {
                                    return a.name.localeCompare(b.name);
                                });

                                // Add sections to dropdown
                                sectionsToAdd.forEach(function(section) {
                                    div_data += '<option value="' + section.id + '">' + section.name + '</option>';
                                });

                                $('#section_id').html(div_data);

                                // Reload SumoSelect for section dropdown
                                if ($('#section_id')[0].sumo) {
                                    $('#section_id')[0].sumo.reload();
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching sections for class ' + class_id + ':', error);
                            processedClasses++;
                        }
                    });
                });
            } else {
                // No classes selected, clear sections
                $('#section_id').empty();
                if ($('#section_id')[0].sumo) {
                    $('#section_id')[0].sumo.reload();
                }
            }
        });
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('submit', '#reportform', function(e) {
            e.preventDefault(); // avoid to execute the actual submit of the form.
            var $this = $(this).find("button[type=submit]:focus");
            var form = $(this);
            var url = form.attr('action');

            // Use standard form serialization - works with both single and multi-select
            var form_data = form.serializeArray();
            form_data.push({
                name: 'search_type',
                value: $this.attr('value')
            });

            // Show loading state
            $this.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Searching...');

            $.ajax({
                type: "POST",
                url: url,
                data: form_data,
                dataType: 'json',
                success: function(data) {
                    if (data.status == 1) {
                        $('[id^=error]').html("");

                        if (typeof initDatatable === 'function') {
                            initDatatable('sibling-list', 'report/dtsiblingreportlist', data.params, [], 100);
                        } else {
                            console.error('initDatatable function not found');
                        }
                    } else {
                        // Handle validation errors
                        $.each(data.error, function(key, value) {
                            $('#error_' + key).text(value);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Sibling report search error:', error);
                    alert('An error occurred while processing your request. Please try again.');
                },
                complete: function() {
                    // Reset button state
                    $this.prop('disabled', false).html('<i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>');
                }
            });
        });
    });
</script>