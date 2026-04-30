<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$s = $payrollSettings; // settings row
$readonly = isset($readonly) ? $readonly : false;
$reqHrs = isset($s['required_hours_per_day']) ? (float)$s['required_hours_per_day'] : 8.00;
$graceMins = isset($s['late_grace_minutes']) ? (int)$s['late_grace_minutes'] : 15;
$shType = isset($s['short_hour_deduction_type']) ? $s['short_hour_deduction_type'] : 'disabled';
$shValue = isset($s['short_hour_deduction_value']) ? number_format((float)$s['short_hour_deduction_value'], 2) : '0.00';
$shThreshold = isset($s['short_hour_threshold']) ? number_format((float)$s['short_hour_threshold'], 2) : '1.00';
?>
<div class="content-wrapper" style="min-height: 393px;">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> <?php echo $this->lang->line('payroll_auto_settings'); ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 class="box-title"><i class="fa fa-cogs"></i> <?php echo $this->lang->line('payroll_auto_settings'); ?></h3>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group pull-right">
                                    <a href="<?php echo base_url() ?>admin/payroll" type="button" class="btn btn-primary btn-xs">
                                        <i class="fa fa-arrow-left"></i> <?php echo $this->lang->line('back'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($readonly): ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-lock"></i> <strong>Read-Only View</strong> — Contact your administrator to change payroll settings.
                    </div>
                    <?php endif; ?>
                    <form class="form-horizontal" action="<?php echo site_url('admin/payroll/savepayrollSettings') ?>" method="post" id="payrollsettingsform"<?php echo $readonly ? ' novalidate' : ''; ?>>
                    <?php if ($readonly): ?>
                    <script>
                    (function(){
                        var form = document.getElementById('payrollsettingsform');
                        if(form){
                            var els = form.querySelectorAll('input, select, textarea, button');
                            els.forEach(function(el){ el.disabled = true; });
                            form.onsubmit = function(){ return false; };
                        }
                    })();
                    </script>
                    <?php endif; ?>
                        <div class="box-body">

                            <!-- Info Box -->
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i>
                                <strong>How it works:</strong> Auto payroll generates payslips for all active staff based on their biometric attendance.
                                <ul style="margin-top:8px;margin-bottom:0;">
                                    <li><strong>Monthly Staff:</strong> Per Day Salary = Basic Salary ÷ Working Days in month. Deductions for absent/late/half-day/short-hours.</li>
                                    <li><strong>Hourly Staff:</strong> Earnings = Total Hours Worked × Hourly Rate. Set <code>salary_type=hourly</code> and <code>hourly_rate</code> on the staff profile.</li>
                                    <li><strong>Hours Worked</strong> = First biometric punch-in to last punch-out each day (accumulated for the payroll period).</li>
                                    <li><strong>Payroll Cutoff:</strong> If set, attendance after the cutoff day counts toward the <em>next</em> month's payroll.</li>
                                    <li><strong>Per Day mode:</strong> Deduction = Days × Value × Per Day Salary (e.g., Absent=1.00 means full day salary deducted)</li>
                                    <li><strong>Fixed mode:</strong> Deduction = Days × Flat Amount (e.g., <?php echo $currency_symbol; ?>50 per late arrival regardless of salary)</li>
                                    <li><strong>Short Hour:</strong> If staff worked less than required hours, a per-hour or fixed deduction is applied per occurrence.</li>
                                    <li><strong>Days with no attendance record = Absent</strong></li>
                                    <li><strong>Admin can edit any auto-generated payslip</strong> after generation</li>
                                </ul>
                            </div>

                            <!-- Section 1: Auto Payroll Toggle -->
                            <div class="box box-default collapsed-box" style="margin-bottom:15px;">
                                <div class="box-header with-border" style="cursor:pointer;" data-widget="collapse">
                                    <h3 class="box-title"><i class="fa fa-robot"></i> Auto Payroll Schedule</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo $this->lang->line('auto_payroll_enabled'); ?></label>
                                        <div class="col-sm-6">
                                            <label class="radio-inline">
                                                <input type="radio" name="auto_payroll_enabled" value="yes" <?php echo (isset($s['auto_payroll_enabled']) && $s['auto_payroll_enabled'] == 'yes') ? 'checked' : ''; ?>>
                                                <?php echo $this->lang->line('yes'); ?>
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="auto_payroll_enabled" value="no" <?php echo (!isset($s['auto_payroll_enabled']) || $s['auto_payroll_enabled'] != 'yes') ? 'checked' : ''; ?>>
                                                <?php echo $this->lang->line('no'); ?>
                                            </label>
                                            <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                When enabled, cron will auto-generate payroll on the configured day of each month.
                                                Set up cron: <code>curl -s <?php echo base_url(); ?>cron/autoGeneratePayroll/YOUR_CRON_SECRET_KEY &gt; /dev/null</code>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo $this->lang->line('auto_payroll_day'); ?></label>
                                        <div class="col-sm-3">
                                            <select name="auto_payroll_day" class="form-control">
                                                <?php for ($d = 1; $d <= 28; $d++): ?>
                                                <option value="<?php echo $d; ?>" <?php echo (isset($s['auto_payroll_day']) && $s['auto_payroll_day'] == $d) ? 'selected' : ''; ?>>
                                                    <?php echo $d; ?><?php echo ($d == 1 || $d == 21 || $d == 31) ? 'st' : (($d == 2 || $d == 22) ? 'nd' : (($d == 3 || $d == 23) ? 'rd' : 'th')); ?> of every month
                                                </option>
                                                <?php endfor; ?>
                                            </select>
                                            <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                Generates payroll for the <strong>previous month</strong> on this day.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Payroll Cutoff Date -->
                            <div class="box box-default" style="margin-bottom:15px;">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-calendar-times-o"></i> Payroll Cutoff Date</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Payroll Cutoff Day</label>
                                        <div class="col-sm-4">
                                            <select name="payroll_cutoff_day" class="form-control" id="payroll_cutoff_day">
                                                <option value="0" <?php echo (!isset($s['payroll_cutoff_day']) || $s['payroll_cutoff_day'] == 0) ? 'selected' : ''; ?>>
                                                    No Cutoff (entire month)
                                                </option>
                                                <?php for ($d = 1; $d <= 28; $d++): ?>
                                                <option value="<?php echo $d; ?>" <?php echo (isset($s['payroll_cutoff_day']) && $s['payroll_cutoff_day'] == $d) ? 'selected' : ''; ?>>
                                                    <?php echo $d; ?><?php echo ($d == 1 || $d == 21 || $d == 31) ? 'st' : (($d == 2 || $d == 22) ? 'nd' : (($d == 3 || $d == 23) ? 'rd' : 'th')); ?> of every month
                                                </option>
                                                <?php endfor; ?>
                                            </select>
                                            <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                <i class="fa fa-exclamation-triangle text-warning"></i>
                                                <strong>Example:</strong> If set to <strong>25th</strong>, payroll for January will only count attendance from <strong>Jan 1 to Jan 25</strong>.
                                                Attendance from Jan 26–31 will count toward <strong>February's</strong> payroll.
                                                This is useful for mid-month salary processing.
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-offset-3 col-sm-9">
                                        <div class="alert alert-warning alert-xs" id="cutoff_example_alert" style="display:none; padding:8px 12px; margin-bottom:0;">
                                            <i class="fa fa-calculator"></i> <span id="cutoff_example_text"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Working Hours & Grace Period -->
                            <div class="box box-default" style="margin-bottom:15px;">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-clock-o"></i> Daily Working Hours &amp; Late Grace Period</h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Required Hours per Day</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="number" name="required_hours_per_day" class="form-control" id="required_hours_per_day"
                                                       value="<?php echo $reqHrs; ?>" min="1" max="24" step="0.25">
                                                <span class="input-group-addon">hours</span>
                                            </div>
                                            <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                Standard working hours per day. Used for short-hour deduction calculation and per-day salary base for hourly staff.
                                                <br>E.g., <strong>8.00</strong> means staff must work 8 hours. <strong>9.00</strong> means 9 hours.
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Late Arrival Grace Period</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="number" name="late_grace_minutes" class="form-control" id="late_grace_minutes"
                                                       value="<?php echo $graceMins; ?>" min="0" max="60" step="1">
                                                <span class="input-group-addon">minutes</span>
                                            </div>
                                            <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                Staff arriving within this many minutes after shift start will <strong>not</strong> be marked late.
                                                <br>E.g., <strong>15</strong> means arriving at 9:15 AM for a 9:00 AM shift is still considered "on time".
                                            </span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Shift Start Time</label>
                                        <div class="col-sm-3">
                                            <div class="input-group">
                                                <input type="time" name="shift_start_time" class="form-control" id="shift_start_time"
                                                       value="<?php echo isset($s['shift_start_time']) ? $s['shift_start_time'] : '09:00'; ?>">
                                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                            </div>
                                            <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                Standard shift start time. Used with grace period to determine "late" status.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Working Days Method -->
                            <div class="box box-default" style="margin-bottom:15px;">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-calendar"></i> <?php echo $this->lang->line('working_days_method'); ?></h3>
                                </div>
                                <div class="box-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label"><?php echo $this->lang->line('working_days_method'); ?></label>
                                        <div class="col-sm-6">
                                            <select name="working_days_method" class="form-control">
                                                <option value="exclude_sundays" <?php echo (isset($s['working_days_method']) && $s['working_days_method'] == 'exclude_sundays') ? 'selected' : ''; ?>>
                                                    Exclude Sundays only (Mon–Sat = 6 days/week)
                                                </option>
                                                <option value="exclude_sundays_saturdays" <?php echo (isset($s['working_days_method']) && $s['working_days_method'] == 'exclude_sundays_saturdays') ? 'selected' : ''; ?>>
                                                    Exclude Sundays &amp; Saturdays (Mon–Fri = 5 days/week)
                                                </option>
                                                <option value="all_days" <?php echo (isset($s['working_days_method']) && $s['working_days_method'] == 'all_days') ? 'selected' : ''; ?>>
                                                    All days (no weekly off — use when staff work 7 days)
                                                </option>
                                            </select>
                                            <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                Determines how many working days are in each month. This affects per-day salary calculation.
                                                Working days are further limited by the payroll cutoff date if set above.
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5: Short Hour Deduction -->
                            <div class="box box-default" style="margin-bottom:15px;">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-hourglass-end"></i> Short Hour Deduction</h3>
                                </div>
                                <div class="box-body">
                                    <div class="alert alert-info alert-xs" style="padding:10px 15px;">
                                        <i class="fa fa-info-circle"></i>
                                        When a staff member <strong>works less than the required hours</strong> on a day, you can deduct from their salary.
                                        <br>E.g., Required: 8 hrs. If staff works 6.5 hrs → shortage of 1.5 hrs → deduction applied.
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Short Hour Deduction</label>
                                        <div class="col-sm-6">
                                            <select name="short_hour_deduction_type" class="form-control" id="short_hour_deduction_type">
                                                <option value="disabled" <?php echo ($shType === 'disabled') ? 'selected' : ''; ?>>
                                                    Disabled (no short-hour deduction)
                                                </option>
                                                <option value="per_hour" <?php echo ($shType === 'per_hour') ? 'selected' : ''; ?>>
                                                    Per Hour × Shortage Hours (<?php echo $currency_symbol; ?>/hour)
                                                </option>
                                                <option value="fixed" <?php echo ($shType === 'fixed') ? 'selected' : ''; ?>>
                                                    Fixed Amount Per Occurrence (<?php echo $currency_symbol; ?>)
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div id="short_hour_settings" style="display:<?php echo ($shType !== 'disabled') ? 'block' : 'none'; ?>;">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">
                                                Deduction Value
                                                <span class="short_hour_per_hour_label">(<?php echo $currency_symbol; ?> per hour of shortage)</span>
                                                <span class="short_hour_fixed_label">(<?php echo $currency_symbol; ?> per occurrence)</span>
                                            </label>
                                            <div class="col-sm-3">
                                                <div class="input-group">
                                                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                                                    <input type="text" name="short_hour_deduction_value" class="form-control" id="short_hour_deduction_value"
                                                           value="<?php echo $shValue; ?>">
                                                </div>
                                                <span class="text-muted text-xs" style="display:block;margin-top:4px;" id="sh_value_help">
                                                    <?php if ($shType === 'per_hour'): ?>
                                                    Deduction = shortage hours × this value. E.g., 1.5 hrs short × <?php echo $currency_symbol; ?><?php echo $shValue; ?> = <?php echo $currency_symbol; ?><?php echo number_format(1.5 * (float)$shValue, 2); ?>
                                                    <?php elseif ($shType === 'fixed'): ?>
                                                    Deduction = this flat amount per day with shortage. E.g., <?php echo $currency_symbol; ?><?php echo $shValue; ?> per short-day.
                                                    <?php endif; ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Minimum Shortage to Trigger Deduction</label>
                                            <div class="col-sm-3">
                                                <div class="input-group">
                                                    <input type="number" name="short_hour_threshold" class="form-control" id="short_hour_threshold"
                                                           value="<?php echo $shThreshold; ?>" min="0.25" max="12" step="0.25">
                                                    <span class="input-group-addon">hours</span>
                                                </div>
                                                <span class="text-muted text-xs" style="display:block;margin-top:4px;">
                                                    <i class="fa fa-exclamation-triangle text-warning"></i>
                                                    Only deduct if the shortage is <strong>at least this many hours</strong>.
                                                    <br>E.g., <strong>1.00</strong> means if staff worked 7.5 hrs (shortage 0.5 hrs), <strong>no deduction</strong>.
                                                    If staff worked 6.5 hrs (shortage 1.5 hrs), <strong>deduction applies</strong>.
                                                    Set to <strong>0.25</strong> to deduct for any shortage.
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Live Example -->
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Live Example</label>
                                            <div class="col-sm-8">
                                                <div class="well well-sm" id="short_hour_example" style="background:#f9f9f9;">
                                                    <strong>Scenario:</strong> Staff worked 5 days, each short by different amounts:
                                                    <table class="table table-bordered table-condensed" style="margin-top:8px;margin-bottom:0;">
                                                        <thead>
                                                            <tr style="background:#eee;">
                                                                <th>Day</th>
                                                                <th>Required</th>
                                                                <th>Worked</th>
                                                                <th>Shortage</th>
                                                                <th>Deduction</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="short_hour_example_body">
                                                        </tbody>
                                                    </table>
                                                    <div style="margin-top:8px;">
                                                        <strong>Total Deduction: <span id="sh_total_deduction" class="text-danger">—</span></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 6: Hourly Rate Info -->
                            <div class="box box-default collapsed-box" style="margin-bottom:15px;">
                                <div class="box-header with-border" style="cursor:pointer;" data-widget="collapse">
                                    <h3 class="box-title"><i class="fa fa-clock-o"></i> Hourly Staff Setup</h3>
                                    <div class="box-tools pull-right">
                                        <button type="button" class="btn btn-box-tool"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <div class="box-body">
                                    <div class="alert alert-info alert-xs" style="padding:10px 15px;">
                                        <i class="fa fa-info-circle"></i>
                                        To pay staff by the hour, edit their staff profile and set:
                                        <ul style="margin-top:5px;margin-bottom:0;">
                                            <li><code>salary_type</code> = <strong>hourly</strong> (default is "monthly")</li>
                                            <li><code>hourly_rate</code> = e.g. 250.00 (<?php echo $currency_symbol; ?> per hour)</li>
                                        </ul>
                                        <strong>How hourly payroll works:</strong>
                                        <ul style="margin-top:5px;margin-bottom:0;">
                                            <li>System calculates <strong>total hours worked</strong> from first biometric punch-in to last punch-out each day</li>
                                            <li><strong>Earnings = Hours × Hourly Rate</strong></li>
                                            <li>Short-hour, absent, late, half-day deductions still apply</li>
                                            <li>Per-day salary for deductions is calculated as <code>hourly_rate × required_hours_per_day</code></li>
                                        </ul>
                                        <strong>Note:</strong> Hourly staff should have <code>basic_salary = 0</code> (or it's ignored).
                                    </div>
                                </div>
                            </div>

                            <!-- Section 7: Deduction Rules (Absent/Late/Half-Day) -->
                            <div class="box box-default" style="margin-bottom:15px;">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-percent"></i> <?php echo $this->lang->line('deduction_rules'); ?></h3>
                                </div>
                                <div class="box-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width:20%;">Attendance Type</th>
                                                <th style="width:35%;">Deduction Type</th>
                                                <th style="width:25%;">Deduction Value</th>
                                                <th style="width:20%;">Example (Basic <?php echo $currency_symbol; ?>30,000, 24 days)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Absent Row -->
                                            <tr>
                                                <td>
                                                    <strong class="text-danger">Absent</strong>
                                                    <div class="text-muted text-xs">Full day not present</div>
                                                </td>
                                                <td>
                                                    <select name="absent_deduction_type" class="form-control deduction-type-select" data-row="absent">
                                                        <option value="per_day" <?php echo (isset($s['absent_deduction_type']) && $s['absent_deduction_type'] == 'per_day') ? 'selected' : ''; ?>>
                                                            Per Day Salary × Multiplier
                                                        </option>
                                                        <option value="fixed" <?php echo (isset($s['absent_deduction_type']) && $s['absent_deduction_type'] == 'fixed') ? 'selected' : ''; ?>>
                                                            Fixed Amount (<?php echo $currency_symbol; ?>)
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="absent_deduction_value" class="form-control" value="<?php echo isset($s['absent_deduction_value']) ? number_format((float)$s['absent_deduction_value'], 2) : '1.00'; ?>" id="absent_deduction_value">
                                                        <span class="input-group-addon absent-unit">days salary</span>
                                                    </div>
                                                </td>
                                                <td class="text-muted text-xs" id="absent_example"></td>
                                            </tr>
                                            <!-- Late Row -->
                                            <tr>
                                                <td>
                                                    <strong class="text-warning">Late Arrival</strong>
                                                    <div class="text-muted text-xs">Checked in after grace period</div>
                                                </td>
                                                <td>
                                                    <select name="late_deduction_type" class="form-control deduction-type-select" data-row="late">
                                                        <option value="per_day" <?php echo (isset($s['late_deduction_type']) && $s['late_deduction_type'] == 'per_day') ? 'selected' : ''; ?>>
                                                            Per Day Salary × Multiplier
                                                        </option>
                                                        <option value="fixed" <?php echo (isset($s['late_deduction_type']) && $s['late_deduction_type'] == 'fixed') ? 'selected' : ''; ?>>
                                                            Fixed Amount (<?php echo $currency_symbol; ?>)
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="late_deduction_value" class="form-control" value="<?php echo isset($s['late_deduction_value']) ? number_format((float)$s['late_deduction_value'], 2) : '0.50'; ?>" id="late_deduction_value">
                                                        <span class="input-group-addon late-unit">days salary</span>
                                                    </div>
                                                </td>
                                                <td class="text-muted text-xs" id="late_example"></td>
                                            </tr>
                                            <!-- Half Day Row -->
                                            <tr>
                                                <td>
                                                    <strong class="text-info">Half Day</strong>
                                                    <div class="text-muted text-xs">Worked less than full day</div>
                                                </td>
                                                <td>
                                                    <select name="half_day_deduction_type" class="form-control deduction-type-select" data-row="half_day">
                                                        <option value="per_day" <?php echo (isset($s['half_day_deduction_type']) && $s['half_day_deduction_type'] == 'per_day') ? 'selected' : ''; ?>>
                                                            Per Day Salary × Multiplier
                                                        </option>
                                                        <option value="fixed" <?php echo (isset($s['half_day_deduction_type']) && $s['half_day_deduction_type'] == 'fixed') ? 'selected' : ''; ?>>
                                                            Fixed Amount (<?php echo $currency_symbol; ?>)
                                                        </option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" name="half_day_deduction_value" class="form-control" value="<?php echo isset($s['half_day_deduction_value']) ? number_format((float)$s['half_day_deduction_value'], 2) : '0.50'; ?>" id="half_day_deduction_value">
                                                        <span class="input-group-addon half_day-unit">days salary</span>
                                                    </div>
                                                </td>
                                                <td class="text-muted text-xs" id="half_day_example"></td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div class="text-muted text-xs" style="margin-top:10px;">
                                        <i class="fa fa-lightbulb-o"></i>
                                        <strong>Tips:</strong>
                                        Per Day Salary multiplier of <strong>1.00</strong> = full day deduction.
                                        <strong>0.50</strong> = half day. <strong>0.25</strong> = quarter day.
                                        For fixed amount, enter the exact amount to deduct per occurrence (e.g., 50 means <?php echo $currency_symbol; ?>50 per late arrival).
                                        For <strong>hourly staff</strong>, "Per Day Salary" is calculated as <code>hourly_rate × required hours per day</code>.
                                    </div>
                                </div>
                            </div>

                        </div><!--./box-body-->
                        <div class="box-footer">
                            <?php if (!$readonly): ?>
                            <button type="submit" class="btn btn-info pull-right">
                                <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Update examples and unit labels on change
        updateAllExamples();
        updateCutoffExample();
        updateShortHourExample();
        updateShortHourLabels();

        $('.deduction-type-select').on('change', function() {
            updateUnitLabel($(this).data('row'));
            updateExample($(this).data('row'));
        });
        $('[id$="_deduction_value"]').on('input', function() {
            updateExample(this.id.replace('_deduction_value', ''));
        });

        $('#payroll_cutoff_day').on('change', function() {
            updateCutoffExample();
        });

        // Short hour deduction type toggle
        $('#short_hour_deduction_type').on('change', function() {
            var v = $(this).val();
            if (v === 'disabled') {
                $('#short_hour_settings').slideUp();
            } else {
                $('#short_hour_settings').slideDown();
                updateShortHourLabels();
                updateShortHourExample();
            }
        });

        $('#short_hour_deduction_value, #short_hour_threshold, #required_hours_per_day').on('input change', function() {
            updateShortHourExample();
        });

        function updateShortHourLabels() {
            var type = $('#short_hour_deduction_type').val();
            if (type === 'per_hour') {
                $('.short_hour_per_hour_label').show();
                $('.short_hour_fixed_label').hide();
            } else if (type === 'fixed') {
                $('.short_hour_per_hour_label').hide();
                $('.short_hour_fixed_label').show();
            } else {
                $('.short_hour_per_hour_label').hide();
                $('.short_hour_fixed_label').hide();
            }
        }

        function updateShortHourExample() {
            var type = $('#short_hour_deduction_type').val();
            var value = parseFloat($('#short_hour_deduction_value').val()) || 0;
            var threshold = parseFloat($('#short_hour_threshold').val()) || 1;
            var reqHrs = parseFloat($('#required_hours_per_day').val()) || 8;
            var cs = '<?php echo $currency_symbol; ?>';

            if (type === 'disabled') return;

            var scenarios = [
                {day: 'Mon', worked: 7.5},
                {day: 'Tue', worked: 6.0},
                {day: 'Wed', worked: reqHrs},  // perfect
                {day: 'Thu', worked: 5.0},
                {day: 'Fri', worked: 7.75}
            ];

            var html = '';
            var totalDeduction = 0;
            var totalShortDays = 0;

            scenarios.forEach(function(sc) {
                var shortage = Math.max(0, reqHrs - sc.worked);
                shortage = Math.round(shortage * 100) / 100;
                var deduction = 0;
                var triggered = shortage >= threshold;
                var rowClass = '', status = '';

                if (shortage === 0) {
                    rowClass = 'success';
                    status = '<span class="label label-success">OK</span>';
                } else if (!triggered) {
                    rowClass = 'warning';
                    status = '<span class="label label-warning">Within threshold</span>';
                } else {
                    rowClass = 'danger';
                    if (type === 'per_hour') {
                        deduction = shortage * value;
                        status = cs + deduction.toFixed(2);
                    } else {
                        deduction = value;
                        status = cs + value.toFixed(2) + ' (fixed)';
                    }
                    totalDeduction += deduction;
                    totalShortDays++;
                }

                html += '<tr class="' + rowClass + '">';
                html += '<td>' + sc.day + '</td>';
                html += '<td>' + reqHrs + ' hrs</td>';
                html += '<td>' + sc.worked.toFixed(2) + ' hrs</td>';
                html += '<td>' + (shortage > 0 ? '<strong class="text-danger">-' + shortage.toFixed(2) + ' hrs</strong>' : '—') + '</td>';
                html += '<td>' + status + '</td>';
                html += '</tr>';
            });

            $('#short_hour_example_body').html(html);
            $('#sh_total_deduction').html(cs + totalDeduction.toFixed(2) + ' (' + totalShortDays + ' days affected)');

            // Update help text
            if (type === 'per_hour') {
                $('#sh_value_help').html('Deduction = shortage hours × this value.<br>E.g., 1.5 hrs short × ' + cs + value.toFixed(2) + ' = ' + cs + (1.5 * value).toFixed(2));
            } else {
                $('#sh_value_help').html('Deduction = ' + cs + value.toFixed(2) + ' flat per day with shortage above threshold.');
            }
        }

        function getVal(id) {
            var v = parseFloat($('#' + id + '_deduction_value').val());
            return isNaN(v) ? 0 : v;
        }

        function getType(row) {
            return $('select[name="' + row + '_deduction_type"]').val();
        }

        function updateUnitLabel(row) {
            var type = getType(row);
            var unit = type === 'fixed' ? '<?php echo $currency_symbol; ?>/day' : 'days salary';
            $('.' + row + '-unit').text(unit);
        }

        function updateExample(row) {
            var type = getType(row);
            var val = getVal(row);
            var perDay = 1250; // 30000/24
            var el = $('#' + row + '_example');
            var deductions = 3; // assume 3 days of this type
            var deduction;

            if (type === 'fixed') {
                deduction = deductions * val;
                el.html('3 days = <?php echo $currency_symbol; ?>' + deduction.toLocaleString('en-IN', {minimumFractionDigits: 2}));
            } else {
                deduction = deductions * perDay * val;
                el.html('3 days = ' + deductions + ' × ' + val + ' × <?php echo $currency_symbol; ?>' + perDay.toLocaleString('en-IN') + ' = <?php echo $currency_symbol; ?>' + deduction.toLocaleString('en-IN', {minimumFractionDigits: 2}));
            }
        }

        function updateAllExamples() {
            ['absent', 'late', 'half_day'].forEach(function(row) {
                updateUnitLabel(row);
                updateExample(row);
            });
        }

        function updateCutoffExample() {
            var cutoffDay = parseInt($('#payroll_cutoff_day').val());
            var el = $('#cutoff_example_alert');
            var text = $('#cutoff_example_text');
            
            if (cutoffDay > 0) {
                var now = new Date();
                var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
                var currentMonth = monthNames[now.getMonth()];
                var daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate();
                
                text.html('<strong>Current month example:</strong> ' + currentMonth + ' payroll will count attendance from <strong>1st to ' + cutoffDay + getOrdinal(cutoffDay) + '</strong> only' +
                    (cutoffDay < daysInMonth ? '. Days ' + (cutoffDay + 1) + '–' + daysInMonth + ' roll into next month.' : '.') +
                    ' <strong>Working days</strong> and <strong>per-day salary</strong> are calculated based on this cutoff period.');
                el.show();
            } else {
                el.hide();
            }
        }

        function getOrdinal(n) {
            var s = ['th','st','nd','rd'];
            var v = n % 100;
            return n + (s[(v-20)%10] || s[v] || s[0]);
        }
    });
</script>
