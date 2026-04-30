<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection'); ?>
        </h1>
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

                        <form action="<?php echo site_url('admin/student_referral/search') ?>" method="post" class="class_search_form">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-11 col-sm-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('reference_staff'); ?></label>

                                                <select id="reference_id" name="reference_id[]" class="form-control multiselect-dropdown" multiple>
                                                    <?php
                                                    foreach ($stafflist as $staff) {
                                                    ?>
                                                        <option value="<?php echo $staff['id']; ?>" <?php
                                                                                                    if (set_value('reference_id') == $staff['id']) {
                                                                                                        echo "selected=selected";
                                                                                                    }
                                                                                                    ?>><?php echo $staff['name'] . " " . $staff['surname']; ?>(<?php echo $staff['designation']; ?>)</option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>

                                                <span class="text-danger" id="error_reference_id"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
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
                                        <div class="col-sm-4">
                                            <div class="form-group">
                                                <label><?php echo $this->lang->line('section'); ?></label>
                                                <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                                </select>
                                                <span class="text-danger" id="error_section_id"></span>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-primary btn-sm pull-right" name="class_search" data-loading-text="Please wait.." value=""><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
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

                                <table class="table table-striped table-bordered table-hover student-list" data-export-title="<?php echo $this->lang->line('student') . " " . $this->lang->line('list'); ?>">
                                    <thead>

                                        <tr>
                                            <th><?php echo $this->lang->line('class'); ?></th>
                                            <th><?php echo $this->lang->line('section'); ?></th>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('student'); ?> <?php echo $this->lang->line('name'); ?></th>
                                            <?php if ($sch_setting->father_name) { ?>
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                            <?php } ?>
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
        emptyDatatable('student-list', 'fees_data');

    });
</script>



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
        if ($('#reference_id').length) $('#reference_id').SumoSelect(commonOptions);

        // Initialize section dropdown on page load if class is pre-selected
        var preSelectedClass = $('#class_id').val();
        if (preSelectedClass && preSelectedClass.length > 0) {
            $('#class_id').trigger('change');
        }

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
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('submit', '.class_search_form', function(e) {
            var $this = $(this).find("button[type=submit]:focus");

            // If no button was clicked, don't submit (prevents SumoSelect OK triggering submit)
            if (!$this.length) {
                e.preventDefault();
                return;
            }

            e.preventDefault(); // avoid to execute the actual submit of the form.
            var form = $(this);
            var url = form.attr('action');
            var form_data = form.serializeArray();
            form_data.push({
                name: 'search_type',
                value: $this.attr('name')
            });
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'JSON',
                data: form_data, // serializes the form's elements.
                beforeSend: function() {
                    $('[id^=error]').html("");
                    $this.button('loading');
                    resetFields($this.attr('name'));
                },
                success: function(response) { // your success handler
                    if (!response.status) {
                        $.each(response.error, function(key, value) {
                            $('#error_' + key).html(value);
                        });
                    } else {
                        initDatatable('student-list', 'admin/student_referral/ajaxSearch', response.params, [], 100);
                    }
                },
                error: function() { // your error handler
                    $this.button('reset');
                },
                complete: function() {
                    $this.button('reset');
                    // Clear state
                    $this.removeAttr("clicked");
                }
            });
        });
    });

    function resetFields(search_type) {
        if (search_type == "keyword_search") {
            // Reset multi-select dropdowns
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
        } else if (search_type == "class_search") {
            $('#search_text').val("");
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