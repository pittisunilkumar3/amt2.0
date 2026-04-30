<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
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
            <i class="fa fa-user-plus"></i> <?php //echo $this->lang->line('student_information'); ?>
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_studentinformation');?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <div class="box-body">
                        <form role="form" id="reportform" action="<?php echo site_url('report/guardiansearchvalidation') ?>" method="post" class="">
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
                                        <span class="text-danger" id="error_class_id"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id[]" class="form-control multiselect-dropdown" multiple>
                                        </select>
                                        <span class="text-danger" id="error_section_id"></span>
                                    </div>
                                </div>
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
                            <h3 class="box-title titlefix"><i class="fa fa-users"></i> <?php echo form_error('student'); ?> <?php echo $this->lang->line('guardian_report'); ?></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label"><?php
echo $this->lang->line('guardian_report') . "<br>";
$this->customlib->get_postmessage();
?></div>
                            <table class="table table-striped table-bordered table-hover guardian-list" id="guardian-list">
                                <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('class_section'); ?></th>
                                        <th><?php echo $this->lang->line('admission_no'); ?></th>
                                        <th><?php echo $this->lang->line('student_name'); ?></th>
                                        <?php if ($sch_setting->mobile_no) {?>
                                            <th><?php echo $this->lang->line('mobile_number'); ?></th>
                                        <?php }if ($sch_setting->guardian_name) {?>
                                        <th><?php echo $this->lang->line('guardian_name'); ?></th>
                                        <?php }if ($sch_setting->guardian_relation) {?>
                                            <th><?php echo $this->lang->line('guardian_relation'); ?></th>
                                        <?php }if ($sch_setting->guardian_phone) {?>
                                        <th><?php echo $this->lang->line('guardian_phone'); ?></th>
                                        <?php }if ($sch_setting->father_name) {?>
                                            <th><?php echo $this->lang->line('father_name'); ?></th>
                                        <?php }if ($sch_setting->father_phone) {?>
                                            <th><?php echo $this->lang->line('father_phone'); ?></th>
                                        <?php }if ($sch_setting->mother_name) {?>
                                            <th><?php echo $this->lang->line('mother_name'); ?></th>
                                        <?php }if ($sch_setting->mother_phone) {?>
                                            <th><?php echo $this->lang->line('mother_phone'); ?></th>
<?php }?>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div><!--./box box-primary -->
            </div><!-- ./col-md-12 -->
        </div>
</div>
</section>
</div>

<!-- SumoSelect files are already included in layout/header.php -->

<script type="text/javascript">
$(document).ready(function () {
    console.log('Guardian Report: Document ready');
    
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

    // Initialize empty datatable
    if (typeof emptyDatatable === 'function') {
        emptyDatatable('guardian-list','data');
    }
});

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

<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click', '#reportform button[type=submit]', function() {
        $(this).parents("form").find("button[type=submit]").removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $(document).on('submit','#reportform',function(e){
        var $this = $(this).find("button[type=submit][clicked=true]");
        
        // If no button was clicked, don't submit (prevents SumoSelect OK triggering submit)
        if (!$this.length) {
            e.preventDefault();
            return;
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
            success: function(response) {
                if(!response.status){
                    $.each(response.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                }else{
                    $('[id^=error]').html("");

                    if (typeof initDatatable === 'function') {
                        initDatatable('guardian-list','report/dtguardianreportlist',response.params,[],100);
                    }
                }
            },
            error: function(xhr, status, error) {
                $this.button('reset');
            },
            complete: function() {
                $this.button('reset');
                $this.removeAttr("clicked");
            }
        });
    });
});
</script>