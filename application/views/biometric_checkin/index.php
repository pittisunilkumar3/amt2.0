<!-- Biometric Check-in Report Page Specific Styles -->
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/themes/white/biometric-checkin-report.css">

<div class="content-wrapper biometric-checkin-page">
    <section class="content">
        <?php $this->load->view('attendencereports/_attendance'); ?>
        
        <!-- Modern Statistics Cards -->
        <div class="bcr-stats-row">
            <!-- Total Staff Card -->
            <div class="bcr-stat-card bcr-stat-card--staff-total">
                <div class="bcr-stat-card__content">
                    <div class="bcr-stat-card__icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <div class="bcr-stat-card__label">Total Staff</div>
                    <div class="bcr-stat-card__value"><?php echo $statistics['staff']['total']; ?></div>
                </div>
                <i class="fa fa-users bcr-stat-card__watermark"></i>
            </div>

            <!-- Staff Checked In Card -->
            <div class="bcr-stat-card bcr-stat-card--staff-checked">
                <div class="bcr-stat-card__content">
                    <div class="bcr-stat-card__icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="bcr-stat-card__label">Staff Checked In</div>
                    <div class="bcr-stat-card__value"><?php echo $statistics['staff']['checked_in']; ?></div>
                    <div class="bcr-stat-card__percentage"><?php echo $statistics['staff']['percentage']; ?>% of total</div>
                </div>
                <i class="fa fa-check-circle bcr-stat-card__watermark"></i>
            </div>

            <!-- Total Students Card -->
            <div class="bcr-stat-card bcr-stat-card--student-total">
                <div class="bcr-stat-card__content">
                    <div class="bcr-stat-card__icon">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                    <div class="bcr-stat-card__label">Total Students</div>
                    <div class="bcr-stat-card__value"><?php echo $statistics['students']['total']; ?></div>
                </div>
                <i class="fa fa-graduation-cap bcr-stat-card__watermark"></i>
            </div>

            <!-- Students Checked In Card -->
            <div class="bcr-stat-card bcr-stat-card--student-checked">
                <div class="bcr-stat-card__content">
                    <div class="bcr-stat-card__icon">
                        <i class="fa fa-check-circle"></i>
                    </div>
                    <div class="bcr-stat-card__label">Students Checked In</div>
                    <div class="bcr-stat-card__value"><?php echo $statistics['students']['checked_in']; ?></div>
                    <div class="bcr-stat-card__percentage"><?php echo $statistics['students']['percentage']; ?>% of total</div>
                </div>
                <i class="fa fa-check-circle bcr-stat-card__watermark"></i>
            </div>
        </div>

        <!-- Main Content Card - Report Generation -->
        <div class="bcr-main-card">
            <div class="bcr-main-card__header">
                <h3 class="bcr-main-card__title">
                    <i class="fa fa-file-text-o"></i>
                    Report Generation
                </h3>
                <p class="bcr-main-card__subtitle">Select a report type and configure parameters</p>
            </div>
            <div class="bcr-main-card__body">
                <!-- Report Selection Grid -->
                <div class="bcr-reports-grid">
                    <!-- Staff Check-in Report Panel -->
                    <div class="bcr-report-panel bcr-report-panel--staff">
                        <div class="bcr-report-panel__header">
                            <div class="bcr-report-panel__icon">
                                <i class="fa fa-briefcase"></i>
                            </div>
                            <h4 class="bcr-report-panel__title">Staff Check-in Report</h4>
                        </div>
                        <div class="bcr-report-panel__body">
                            <form action="<?php echo base_url('biometric_checkin_report/staff_checkin'); ?>" method="post">
                                <div class="bcr-form-group">
                                    <label class="bcr-form-label">Select Date</label>
                                    <input type="date" name="date" class="bcr-form-control" value="<?php echo $date; ?>" required>
                                </div>
                                <button type="submit" class="bcr-btn bcr-btn--primary">
                                    <i class="fa fa-file-text-o"></i>
                                    View Staff Check-in Report
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Student Check-in Report Panel -->
                    <div class="bcr-report-panel bcr-report-panel--student">
                        <div class="bcr-report-panel__header">
                            <div class="bcr-report-panel__icon">
                                <i class="fa fa-graduation-cap"></i>
                            </div>
                            <h4 class="bcr-report-panel__title">Student Check-in Report</h4>
                        </div>
                        <div class="bcr-report-panel__body">
                            <form action="<?php echo base_url('biometric_checkin_report/student_checkin'); ?>" method="post" id="studentForm">
                                <div class="bcr-form-group">
                                    <label class="bcr-form-label">Select Date</label>
                                    <input type="date" name="date" class="bcr-form-control" value="<?php echo $date; ?>" required>
                                </div>
                                <div class="bcr-form-row">
                                    <div class="bcr-form-group">
                                        <label class="bcr-form-label">Class (Optional)</label>
                                        <select name="class_id" id="class_id" class="bcr-form-control">
                                            <option value="">All Classes</option>
                                            <?php foreach ($classlist as $class) { ?>
                                                <option value="<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="bcr-form-group">
                                        <label class="bcr-form-label">Section (Optional)</label>
                                        <select name="section_id" id="section_id" class="bcr-form-control">
                                            <option value="">All Sections</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="bcr-btn bcr-btn--success">
                                    <i class="fa fa-file-text-o"></i>
                                    View Student Check-in Report
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Last Sync Footer -->
                <div class="bcr-sync-footer">
                    Last data sync: <strong>Today at <?php echo date('h:i A'); ?></strong>
                </div>
            </div>
        </div>

        <!-- Today's Summary Box -->
        <div class="bcr-summary">
            <div class="bcr-summary__header">
                <div class="bcr-summary__icon">
                    <i class="fa fa-info"></i>
                </div>
                <h4 class="bcr-summary__title">
                    Today's Summary
                    <span class="bcr-summary__date">(<?php echo date('F d, Y', strtotime($date)); ?>)</span>
                </h4>
            </div>
            <div class="bcr-summary__grid">
                <!-- Staff Summary -->
                <div class="bcr-summary__section">
                    <div class="bcr-summary__section-title">
                        <i class="fa fa-users"></i>
                        Staff Statistics
                    </div>
                    <div class="bcr-summary__stat">
                        <span class="bcr-summary__stat-label">Checked In</span>
                        <span class="bcr-summary__stat-value bcr-summary__stat-value--success">
                            <?php echo $statistics['staff']['checked_in']; ?> / <?php echo $statistics['staff']['total']; ?>
                        </span>
                    </div>
                    <div class="bcr-summary__stat">
                        <span class="bcr-summary__stat-label">Not Checked In</span>
                        <span class="bcr-summary__stat-value bcr-summary__stat-value--danger">
                            <?php echo $statistics['staff']['not_checked_in']; ?> staff members
                        </span>
                    </div>
                    <div class="bcr-summary__stat">
                        <span class="bcr-summary__stat-label">Attendance Rate</span>
                        <span class="bcr-summary__stat-value bcr-summary__stat-value--info">
                            <?php echo $statistics['staff']['percentage']; ?>%
                        </span>
                    </div>
                </div>

                <!-- Student Summary -->
                <div class="bcr-summary__section">
                    <div class="bcr-summary__section-title">
                        <i class="fa fa-graduation-cap"></i>
                        Student Statistics
                    </div>
                    <div class="bcr-summary__stat">
                        <span class="bcr-summary__stat-label">Checked In</span>
                        <span class="bcr-summary__stat-value bcr-summary__stat-value--success">
                            <?php echo $statistics['students']['checked_in']; ?> / <?php echo $statistics['students']['total']; ?>
                        </span>
                    </div>
                    <div class="bcr-summary__stat">
                        <span class="bcr-summary__stat-label">Not Checked In</span>
                        <span class="bcr-summary__stat-value bcr-summary__stat-value--danger">
                            <?php echo $statistics['students']['not_checked_in']; ?> students
                        </span>
                    </div>
                    <div class="bcr-summary__stat">
                        <span class="bcr-summary__stat-label">Attendance Rate</span>
                        <span class="bcr-summary__stat-value bcr-summary__stat-value--info">
                            <?php echo $statistics['students']['percentage']; ?>%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
$(document).ready(function() {
    // Load sections when class is selected
    $('#class_id').change(function() {
        var class_id = $(this).val();
        if (class_id) {
            $.ajax({
                url: '<?php echo base_url('biometric_checkin_report/getSectionByClass'); ?>',
                type: 'POST',
                data: {class_id: class_id},
                dataType: 'json',
                success: function(data) {
                    $('#section_id').html('<option value="">All Sections</option>');
                    $.each(data, function(key, value) {
                        $('#section_id').append('<option value="' + value.section_id + '">' + value.section + '</option>');
                    });
                }
            });
        } else {
            $('#section_id').html('<option value="">All Sections</option>');
        }
    });
});
</script>
