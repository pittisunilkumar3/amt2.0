<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-line-chart"></i> <?php //echo $this->lang->line('reports'); ?> <small> <?php //echo $this->lang->line('filter_by_name1'); ?></small></h1>
    </section>
    <!-- Main content -->
    <section class="content" >
        <?php $this->load->view('reports/_studentinformation');?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form role="form" action="<?php echo site_url('report/studentreportvalidation') ?>" method="post" class="" id="reportform">
                            <div class="row">
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="col-sm-6 col-md-3">
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
$count++;
}
?>
                                        </select>
                                         <span class="text-danger" id="error_class_id"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                        </select>
                                        <span class="text-danger" id="error_section_id"></span>
                                    </div>
                                </div>
                                <?php if ($sch_setting->category) {
    ?>
                                    <div class="col-sm-3 col-md-2">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('category'); ?></label>
                                            <select id="category_id" name="category_id[]" class="form-control multiselect-dropdown" multiple>
                                                <?php
foreach ($categorylist as $category) {
        ?>
                                                    <option value="<?php echo $category['id'] ?>" <?php if (set_value('category_id') == $category['id']) {
            echo "selected=selected";
        }
        ?>><?php echo $category['category'] ?></option>
                                                    <?php
$count++;
    }
    ?>
                                            </select>
                                            <span class="text-danger" id="error_category_id"></span>
                                        </div>
                                    </div>
                                <?php }?>
                                <div class="col-sm-3 col-md-2">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('gender'); ?></label>
                                        <select id="gender" name="gender[]" class="form-control multiselect-dropdown" multiple>
                                            <?php
foreach ($genderList as $key => $value) {
    ?>
                                                <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
        echo "selected";
    }
    ?>><?php echo $value; ?></option>
                                                <?php
}
?>
                                        </select>
                                        <span class="text-danger" id="error_gender"></span>
                                    </div>
                                </div>
                                <?php if ($sch_setting->rte) {
    ?>
                                    <div class="col-sm-3 col-md-2">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('rte'); ?></label>
                                            <select id="rte" name="rte[]" class="form-control multiselect-dropdown" multiple>
                                                <?php
foreach ($RTEstatusList as $k => $rte) {
        ?>
                                                    <option value="<?php echo $k; ?>" <?php if (set_value('rte') == $k) {
            echo "selected";
        }
        ?>><?php echo $rte; ?></option>

                                                    <?php
$count++;
    }
    ?>
                                            </select>
                                            <span class="text-danger" id="error_rte"></span>
                                        </div>
                                    </div>
                                <?php }?>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </div><!--./row-->
                        </form>
                    </div><!--./box-body-->
                        <div class="">
                            <div class="box-header ptbnull">
                                <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo form_error('student'); ?> <?php echo $this->lang->line('student_report'); ?></h3>
                            </div>
                            <div class="box-body table-responsive">
                                    <div class="download_label"> <?php echo $this->lang->line('student_report'); ?></div>
                            <div >
                                <table class="table table-striped table-bordered table-hover student-list" data-export-title="<?php echo $this->lang->line('student_report'); ?>">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('section'); ?></th>
                                            <th><?php echo $this->lang->line('admission_no'); ?></th>
                                            <th><?php echo $this->lang->line('student_name'); ?></th>
                                            <?php if ($sch_setting->father_name) {?>
                                                <th><?php echo $this->lang->line('father_name'); ?></th>
                                                <?php }?>
                                            <th><?php echo $this->lang->line('date_of_birth'); ?></th>
                                            <th><?php echo $this->lang->line('gender'); ?></th>
                                            <?php if ($sch_setting->category) {?>
                                                <th><?php echo $this->lang->line('category'); ?></th>
                                            <?php }if ($sch_setting->mobile_no) {?>
                                                <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                            <?php
}
if ($sch_setting->local_identification_no) {
    ?>
                                                <th><?php echo $this->lang->line('local_identification_number'); ?></th>
                                            <?php }if ($sch_setting->national_identification_no) {?>
                                                <th><?php echo $this->lang->line('national_identification_number'); ?></th>
                                            <?php }if ($sch_setting->rte) {?>
                                                <th><?php echo $this->lang->line('rte'); ?></th>
                                            <?php }?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div><!--./box box-primary -->
            </div><!-- ./col-md-12 -->
        </div>
</div>
</section>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        console.log('Document ready, jQuery version:', $.fn.jquery);
        console.log('Found multiselect dropdowns:', $('.multiselect-dropdown').length);

        // Check if SumoSelect is available
        if (typeof $.fn.SumoSelect === 'undefined') {
            console.error('SumoSelect plugin not loaded!');
            return;
        }

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
        if ($('#category_id').length) $('#category_id').SumoSelect(commonOptions);
        if ($('#gender').length) $('#gender').SumoSelect(commonOptions);
        if ($('#rte').length) $('#rte').SumoSelect(commonOptions);

        // Initialize section dropdown on page load if class is pre-selected
        var preSelectedClass = $('#class_id').val();
        if (preSelectedClass && preSelectedClass.length > 0) {
            $('#class_id').trigger('change');
        }

        // Handle class dropdown changes for section population
        $(document).on('change', '#class_id', function (e) {
            var $classSelect = $(this);
            var sectionDropdown = $('#section_id')[0];
            var class_ids = $classSelect.val();
            var base_url = '<?php echo base_url() ?>';

            // Defer the execution to let SumoSelect finish its internal update
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
                    data: {'class_id': current_class_ids},
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

        // Initialize empty datatable
        emptyDatatable('student-list','data');
    });
</script>

<script type="text/javascript">
$(document).ready(function(){
    $(document).on('submit','#reportform',function(e){
        var $this = $(this).find("button[type=submit]:focus");
        
        // If no submit button is focused, it's likely a SumoSelect OK click or Enter press
        // We should only proceed if it's a legitimate submit
        if (!$this.length) {
            // Check if there's only one submit button and it's NOT focusable (standard behavior for some browsers)
            $this = $(this).find("button[type=submit]");
            if ($this.length > 1) {
                // Multi-button form, we need a focus/clicked state to be sure
                e.preventDefault();
                return;
            }
        }

        e.preventDefault(); // avoid to execute the actual submit of the form.
        var form = $(this);
        var url = form.attr('action');

        // Use standard form serialization - works with both single and multi-select
        var form_data = form.serializeArray();
        form_data.push({name: 'search_type', value: $this.attr('value')});

        $.ajax({
            url: url,
            type: "POST",
            dataType:'JSON',
            data: form_data,
            beforeSend: function () {
                $('[id^=error]').html("");
                $this.button('loading');
            },
            success: function(response) { // your success handler
                if(!response.status){
                    $.each(response.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                }else{
                    $('[id^=error]').html("");
                    initDatatable('student-list','report/dtstudentreportlist',response.params,[],100);
                }
            },
            error: function(xhr, status, error) { // your error handler
                console.error('AJAX Error:', status, error);
                showErrorMessage('Network error occurred. Please check your connection and try again.');
            },
            complete: function() {
                $this.button('reset');
            }
        });
    });
});

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