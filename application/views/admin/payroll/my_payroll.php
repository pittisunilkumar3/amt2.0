<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$staff = $staff;
$ps = $payrollSettings;
$statusLabels = array('generated' => '<span class="label label-warning">Generated</span>', 'paid' => '<span class="label label-success">Paid</span>');

$cutoffDay = (!empty($ps)) ? (int)$ps['payroll_cutoff_day'] : 0;
$reqHrs = (!empty($ps)) ? (float)$ps['required_hours_per_day'] : 8;
?>
<div class="content-wrapper" style="min-height: 393px;">
    <section class="content-header">
        <h1><i class="fa fa-id-card-o"></i> <?php echo $this->lang->line('my_payroll'); ?></h1>
    </section>
    <section class="content">

        <!-- Top Summary Cards -->
        <div class="row" style="margin-bottom: 15px;">
            <!-- Staff Info -->
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-user"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Employee</span>
                        <span class="info-box-number">
                            <?php echo $staff['name'] . ' ' . $staff['surname']; ?>
                        </span>
                        <div style="font-size:12px; margin-top:6px; color:#777;">
                            <i class="fa fa-id-badge"></i> <?php echo $staff['employee_id']; ?>
                            <?php if (!empty($staff['department'])): ?><br><i class="fa fa-building"></i> <?php echo $staff['department']; ?><?php endif; ?>
                            <?php if (!empty($staff['designation'])): ?><br><i class="fa fa-briefcase"></i> <?php echo $staff['designation']; ?><?php endif; ?>
                            <br><span class="label <?php echo ($salary_type == 'hourly') ? 'label-info' : 'label-primary'; ?>"><?php echo ucfirst($salary_type); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- This Month Attendance -->
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-calendar-check-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?php echo $current_month . ' ' . $current_year; ?> Attendance</span>
                        <span class="info-box-number">
                            <?php echo $present_days; ?> <small>/ <?php echo $working_days; ?> days</small>
                        </span>
                        <div style="font-size:12px; margin-top:6px; color:#777;">
                            <?php if ($cutoffDay > 0): ?>
                                <span class="text-info"><i class="fa fa-scissors"></i> Cutoff: <?php echo $cutoffDay . $this->customlib->getOrdinalSuffix($cutoffDay); ?></span><br>
                            <?php endif; ?>
                            <span class="text-success"><i class="fa fa-check"></i> <?php echo $present_days; ?> Present</span> &nbsp;
                            <span class="text-warning"><i class="fa fa-clock-o"></i> <?php echo $late_days; ?> Late</span> &nbsp;
                            <span class="text-danger"><i class="fa fa-times"></i> <?php echo $absent_days; ?> Absent</span> &nbsp;
                            <span class="text-primary"><i class="fa fa-adjust"></i> <?php echo $half_day_days; ?> Half</span>
                            <?php if ($salary_type == 'hourly'): ?>
                                <br><span class="text-purple"><i class="fa fa-hourglass-half"></i> <?php echo number_format($total_hours_worked, 2); ?> hrs (req: <?php echo $reqHrs; ?>)</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Financial Summary</span>
                        <span class="info-box-number"><?php echo $currency_symbol . number_format($estimated_earnings, 2); ?></span>
                        <div style="font-size:12px; margin-top:6px; color:#777;">
                            <?php if ($salary_type == 'hourly'): ?>
                                Rate: <?php echo $currency_symbol . number_format($hourly_rate, 2); ?>/hr<br>
                            <?php else: ?>
                                Per Day: <?php echo $currency_symbol . number_format($per_day_salary, 2); ?><br>
                            <?php endif; ?>
                            <span class="text-success"><i class="fa fa-arrow-circle-o-up"></i> Paid: <?php echo $currency_symbol . number_format($total_paid, 2); ?></span><br>
                            <span class="text-warning"><i class="fa fa-clock-o"></i> Pending: <?php echo $currency_symbol . number_format($total_pending, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payroll Settings Summary -->
            <div class="col-md-3 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-cogs"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Payroll Config</span>
                        <span class="info-box-number" style="font-size:16px;">
                            <?php echo (!empty($ps) && isset($ps['working_days_method'])) ? $this->customlib->humanizeWorkingDaysMethod($ps['working_days_method']) : 'Not Configured'; ?>
                        </span>
                        <div style="font-size:12px; margin-top:6px; color:#777;">
                            Working: <?php echo $working_days; ?> days/month<br>
                            <?php if ($cutoffDay > 0): ?>
                                Cutoff: Every <?php echo $cutoffDay . $this->customlib->getOrdinalSuffix($cutoffDay); ?><br>
                            <?php else: ?>
                                Cutoff: <em>Full month</em><br>
                            <?php endif; ?>
                            Required: <?php echo $reqHrs; ?> hrs/day<br>
                            Grace: <?php echo (!empty($ps) && isset($ps['late_grace_minutes'])) ? (int)$ps['late_grace_minutes'] : 0; ?> min
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslip History Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-file-text-o"></i> Payslip History</h3>
                        <div class="box-tools pull-right">
                            <span class="text-muted text-sm" style="line-height:30px;">
                                Total Paid: <strong class="text-success"><?php echo $currency_symbol . number_format($total_paid, 2); ?></strong>
                                &nbsp;&nbsp;|&nbsp;&nbsp;
                                Total Pending: <strong class="text-warning"><?php echo $currency_symbol . number_format($total_pending, 2); ?></strong>
                            </span>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (empty($payslips)): ?>
                        <div class="text-center" style="padding:60px 20px; color:#999;">
                            <i class="fa fa-inbox" style="font-size:48px; margin-bottom:15px; display:block;"></i>
                            <h4>No Payslips Found</h4>
                            <p>Payroll has not been generated yet for any month.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table id="payslipHistoryTable" class="table table-bordered table-striped table-hover" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Month / Year</th>
                                        <th>Type</th>
                                        <th>Hours</th>
                                        <th>Basic</th>
                                        <th>Allowances</th>
                                        <th>Deductions</th>
                                        <th class="text-right">Net Salary</th>
                                        <th>Status</th>
                                        <th>Paid On</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; foreach ($payslips as $pslip): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td>
                                            <strong><?php echo $pslip['month']; ?></strong>
                                            <span class="text-muted"> <?php echo $pslip['year']; ?></span>
                                        </td>
                                        <td>
                                            <span class="label <?php echo (isset($pslip['salary_type']) && $pslip['salary_type'] == 'hourly') ? 'label-info' : 'label-primary'; ?>">
                                                <?php echo ucfirst(isset($pslip['salary_type']) ? $pslip['salary_type'] : 'monthly'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (isset($pslip['total_hours_worked']) && $pslip['total_hours_worked'] > 0): ?>
                                                <?php echo number_format((float)$pslip['total_hours_worked'], 2); ?> hrs
                                                <?php if (isset($pslip['hourly_rate']) && $pslip['hourly_rate'] > 0): ?>
                                                    <br><small class="text-muted">@ <?php echo $currency_symbol . number_format((float)$pslip['hourly_rate'], 2); ?>/hr</small>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $currency_symbol . number_format((float)$pslip['basic'], 2); ?></td>
                                        <td class="text-success">+ <?php echo $currency_symbol . number_format((float)$pslip['total_allowance'], 2); ?></td>
                                        <td class="text-danger">− <?php echo $currency_symbol . number_format((float)$pslip['total_deduction'], 2); ?></td>
                                        <td class="text-right"><strong style="font-size:15px;"><?php echo $currency_symbol . number_format((float)$pslip['net_salary'], 2); ?></strong></td>
                                        <td><?php echo isset($statusLabels[$pslip['status']]) ? $statusLabels[$pslip['status']] : '<span class="label label-default">' . ucfirst($pslip['status']) . '</span>'; ?></td>
                                        <td>
                                            <?php if (!empty($pslip['payment_date'])): ?>
                                                <?php echo date('d M Y', strtotime($pslip['payment_date'])); ?>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-default btn-sm view-payslip-btn" data-payslip-id="<?php echo $pslip['id']; ?>" title="View Details">
                                                <i class="fa fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payslip Detail Modal -->
        <div class="modal fade" id="payslipDetailModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header" style="background:#3c8dbc; color:#fff;">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:#fff;">&times;</span></button>
                        <h4 class="modal-title"><i class="fa fa-file-text-o"></i> Payslip Detail</h4>
                    </div>
                    <div class="modal-body" id="payslipDetailBody">
                        <div class="text-center" style="padding:40px;">
                            <i class="fa fa-spinner fa-spin fa-2x"></i> Loading...
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<style>
/* My Payroll page specific styles */
.info-box-number {
    font-size: 20px !important;
    font-weight: 700 !important;
}
.info-box-icon {
    border-radius: 8px !important;
    font-size: 22px !important;
    width: 70px !important;
    height: 70px !important;
    line-height: 70px !important;
}
.info-box {
    border-radius: 8px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
    min-height: 150px !important;
}
.info-box-content {
    margin-left: 70px !important;
}
.bg-yellow {
    background-color: #f39c12 !important;
    color: #fff !important;
}
.bg-purple {
    background-color: #605ca8 !important;
    color: #fff !important;
}
.bg-blue {
    background-color: #3c8dbc !important;
    color: #fff !important;
}
.bg-green {
    background-color: #00a65a !important;
    color: #fff !important;
}
.text-purple {
    color: #605ca8 !important;
}
#payslipHistoryTable thead th {
    background-color: #3c8dbc;
    color: #fff;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
    padding: 10px 8px !important;
}
#payslipHistoryTable tbody td {
    vertical-align: middle;
    padding: 10px 8px !important;
}
#payslipHistoryTable tbody tr:hover {
    background-color: #f5f8fc;
}
</style>

<script>
$(document).ready(function(){

    // Init DataTable if available
    if ($.fn.DataTable) {
        $('#payslipHistoryTable').DataTable({
            dom: '<"top"lf<"clear">>rt<"bottom"ip<"clear">>',
            pageLength: 25,
            order: [[0, 'desc']],
            columnDefs: [{ orderable: false, targets: [10] }],
            language: {
                emptyTable: 'No payslips found for any month.',
                search: '<i class="fa fa-search"></i>',
                searchPlaceholder: 'Search payslips...'
            }
        });
    }

    // View payslip detail
    $(document).on('click', '.view-payslip-btn', function(){
        var payslipId = $(this).data('payslip-id');
        $('#payslipDetailBody').html('<div class="text-center" style="padding:40px;"><i class="fa fa-spinner fa-spin fa-2x"></i> Loading...</div>');
        $('#payslipDetailModal').modal('show');

        $.ajax({
            url: '<?php echo site_url("admin/payroll/getMyPayslipDetail"); ?>',
            type: 'POST',
            data: {payslip_id: payslipId, <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'},
            dataType: 'json',
            success: function(response){
                if(response.status === 'success'){
                    renderPayslipDetail(response.payslip, response.allowance, response.attendance, response.working_days);
                } else {
                    $('#payslipDetailBody').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function(){
                $('#payslipDetailBody').html('<div class="alert alert-danger">Failed to load payslip detail.</div>');
            }
        });
    });

    function renderPayslipDetail(p, allowance, att, wd) {
        var cs = '<?php echo $currency_symbol; ?>';
        var html = '';

        // Header row
        html += '<div class="row" style="margin-bottom:15px;">';
        html += '<div class="col-sm-6"><h4 style="margin:0;">' + p.month + ' ' + p.year + '</h4></div>';
        html += '<div class="col-sm-6 text-right">';
        if(p.status === 'paid'){
            html += '<span class="label label-success" style="font-size:14px; padding:6px 14px;">PAID</span>';
        } else {
            html += '<span class="label label-warning" style="font-size:14px; padding:6px 14px;">PENDING</span>';
        }
        html += '</div></div>';

        // Salary breakdown
        html += '<div class="row">';
        html += '<div class="col-sm-6">';
        html += '<div class="panel panel-default">';
        html += '<div class="panel-heading" style="background:#3c8dbc; color:#fff;"><strong>Salary Breakdown</strong></div>';
        html += '<table class="table table-bordered table-striped" style="margin:0;">';
        html += '<tr><td><strong>Salary Type</strong></td><td class="text-right">' + (p.salary_type || 'monthly') + '</td></tr>';
        if(p.salary_type === 'hourly'){
            html += '<tr><td>Hourly Rate</td><td class="text-right">' + cs + parseFloat(p.hourly_rate || 0).toFixed(2) + '</td></tr>';
            html += '<tr><td>Total Hours Worked</td><td class="text-right">' + parseFloat(p.total_hours_worked || 0).toFixed(2) + ' hrs</td></tr>';
        }
        html += '<tr><td><strong>Basic Salary</strong></td><td class="text-right">' + cs + parseFloat(p.basic || 0).toFixed(2) + '</td></tr>';
        html += '<tr class="success"><td>+ Allowances</td><td class="text-right">' + cs + parseFloat(p.total_allowance || 0).toFixed(2) + '</td></tr>';
        html += '<tr class="danger"><td>− Deductions</td><td class="text-right">' + cs + parseFloat(p.total_deduction || 0).toFixed(2) + '</td></tr>';
        html += '<tr style="background:#dff0d8; font-size:16px;"><td><strong>Net Salary</strong></td><td class="text-right"><strong>' + cs + parseFloat(p.net_salary || 0).toFixed(2) + '</strong></td></tr>';
        html += '</table></div></div>';

        // Attendance breakdown
        html += '<div class="col-sm-6">';
        html += '<div class="panel panel-default">';
        html += '<div class="panel-heading" style="background:#00a65a; color:#fff;"><strong>Attendance</strong></div>';
        if(att){
            html += '<table class="table table-bordered table-striped" style="margin:0;">';
            html += '<tr><td>Total Working Days</td><td class="text-right"><strong>' + (wd || 0) + '</strong></td></tr>';
            html += '<tr class="success"><td><i class="fa fa-check text-success"></i> Present</td><td class="text-right">' + (att.present || 0) + '</td></tr>';
            html += '<tr class="warning"><td><i class="fa fa-clock-o text-warning"></i> Late</td><td class="text-right">' + (att.late || 0) + '</td></tr>';
            html += '<tr class="danger"><td><i class="fa fa-times text-danger"></i> Absent</td><td class="text-right">' + (att.absent || 0) + '</td></tr>';
            html += '<tr class="info"><td><i class="fa fa-adjust text-info"></i> Half Day</td><td class="text-right">' + (att.half_day || 0) + '</td></tr>';
            if(att.total_hours_worked){
                html += '<tr><td><i class="fa fa-hourglass-half text-purple"></i> Hours Worked</td><td class="text-right"><strong>' + parseFloat(att.total_hours_worked).toFixed(2) + '</strong> hrs</td></tr>';
            }
            html += '</table>';
        } else {
            html += '<div class="panel-body text-center text-muted">Attendance data not available</div>';
        }
        html += '</div></div></div>';

        // Allowance/deduction lines
        if(allowance && allowance.length > 0){
            html += '<div class="row" style="margin-top:15px;"><div class="col-sm-12">';
            html += '<div class="panel panel-default">';
            html += '<div class="panel-heading" style="background:#f4f4f4;"><strong>Allowance & Deduction Details</strong></div>';
            html += '<table class="table table-bordered" style="margin:0;">';
            html += '<tr style="background:#f9f9f9;"><th>Type</th><th>Category</th><th class="text-right">Amount</th></tr>';
            for(var i=0; i < allowance.length; i++){
                var isDeduction = allowance[i].cal_type === 'negative';
                var rowClass = isDeduction ? 'danger' : 'success';
                var sign = isDeduction ? '−' : '+';
                html += '<tr class="' + rowClass + '">';
                html += '<td>' + allowance[i].allowance_type + '</td>';
                html += '<td>' + (isDeduction ? 'Deduction' : 'Allowance') + '</td>';
                html += '<td class="text-right"><strong>' + sign + cs + parseFloat(allowance[i].amount || 0).toFixed(2) + '</strong></td>';
                html += '</tr>';
            }
            html += '</table></div></div></div>';
        }

        // Payment info
        html += '<div class="row" style="margin-top:15px;"><div class="col-sm-12">';
        html += '<div class="panel panel-default">';
        html += '<div class="panel-heading" style="background:#f4f4f4;"><strong>Payment Info</strong></div>';
        html += '<table class="table table-bordered" style="margin:0;">';
        html += '<tr><td width="30%">Payment Mode</td><td>' + (p.payment_mode || '—') + '</td></tr>';
        html += '<tr><td>Payment Date</td><td>' + (p.payment_date || '—') + '</td></tr>';
        html += '<tr><td>Notes</td><td>' + (p.remark || '—') + '</td></tr>';
        html += '<tr><td>Generated On</td><td>' + (p.created_at || '—') + '</td></tr>';
        html += '</table></div></div></div>';

        $('#payslipDetailBody').html(html);
    }
});
</script>
