<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    /* Student Info Card Modernization */
    #report-by-name-page .student-profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        gap: 25px;
        align-items: flex-start;
        border: 1px solid #f0f0f0;
        position: relative;
        overflow: hidden;
    }

    #report-by-name-page .student-profile-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #3498db;
    }

    #report-by-name-page .student-image-wrapper {
        flex-shrink: 0;
        width: 120px;
        height: 120px;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #eee;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #report-by-name-page .student-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    #report-by-name-page .student-details-grid {
        flex-grow: 1;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 15px 30px;
        padding-top: 5px;
    }

    #report-by-name-page .info-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        border-bottom: 1px solid #fafafa;
        padding-bottom: 8px;
    }

    #report-by-name-page .info-item:last-child { border-bottom: none; }

    #report-by-name-page .info-item .label {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 700;
        color: #95a5a6;
        letter-spacing: 0.8px;
        margin-bottom: 4px;
    }

    #report-by-name-page .info-item .value {
        font-size: 14px;
        font-weight: 600;
        color: #2c3e50;
    }

    #report-by-name-page .badge-rte {
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
    }
    #report-by-name-page .badge-rte-yes { background: #e74c3c; color: #fff; }
    #report-by-name-page .badge-rte-no { background: #fdf0f0; color: #e74c3c; border: 1px solid #fab1a0; }

    /* Table & Action Alignment Fixes */
    #report-by-name-page .table-responsive {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        border: 1px solid #eee;
        margin-top: 15px;
    }

    /* DataTables Control Positioning */
    #report-by-name-page .dt-buttons {
        margin-bottom: 20px !important;
        margin-top: 20px;
        float: left !important;
        display: flex;
        gap: 5px;
    }

    #report-by-name-page .dataTables_filter {
        margin-bottom: 20px !important;
    }

    #report-by-name-page .box-header {
        padding: 15px 0 10px 0 !important;
        border-bottom: 2px solid #f4f4f4;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #report-by-name-page .box-header .box-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        margin: 0 !important;
        color: #2c3e50 !important;
    }

    #report-by-name-page .dates {
        clear: both;
        font-size: 13px;
        color: #2c3e50;
        margin: 10px 0 20px 0;
        font-weight: 700;
        padding: 8px 15px;
        background: #f8f9fa;
        border-radius: 6px;
        border-left: 3px solid #3498db;
        display: inline-block;
    }

    /* Table Design */
    #report-by-name-page .example.table {
        margin-top: 0 !important;
        border: 1px solid #eee;
    }

    #report-by-name-page .example th {
        background-color: #f8f9fa !important;
        color: #2c3e50 !important;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 12px 10px !important;
        border-bottom: 2px solid #3498db !important;
    }

    #report-by-name-page .example td {
        vertical-align: middle !important;
        padding: 10px 8px !important;
        font-size: 13px;
    }

    #report-by-name-page tr.box-solid.total-bg td {
        background-color: #2c3e50 !important;
        color: #fff !important;
        font-weight: 700 !important;
        font-size: 14px;
    }

    @media (max-width: 992px) {
        #report-by-name-page .student-profile-card { flex-direction: column; align-items: center; }
        #report-by-name-page .student-details-grid { grid-template-columns: repeat(2, 1fr); width: 100%; }
        #report-by-name-page .student-profile-card::before { width: 100%; height: 4px; }
    }

</style>
<div class="content-wrapper" id="report-by-name-page" style="min-height: 1126px;">
    <section class="content-header">
        <h1><i class="fa fa-money"></i> <?php //echo $this->lang->line('fees_collection'); 
                                        ?> <small><?php //echo $this->lang->line('student1'); 
                                                                                                        ?></small> </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('financereports/_finance'); ?>
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form action="<?php echo site_url('financereports/reportbyname') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                        <select autofocus="" id="class_id" name="class_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
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
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                        <select id="section_id" name="section_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('student'); ?></label>
                                        <select id="student_id" name="student_id" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('student_id'); ?></span>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-sm pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php
                    if (isset($student_due_fee)) {
                    ?>

                        <div class="">
                            <div class="box-header">
                                <h3 class="box-title">
                                    <i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('fees_statement'); ?>
                                </h3>
                            </div>
                            <div class="box-body" style="padding-top:0;">

                                <?php
                                if (!empty($student_due_fee)) {
                                    foreach ($student_due_fee as $student_key => $student) {
                                        $grand_fine_amount = 0;

                                ?>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="student-profile-card">
                                                    <div class="student-image-wrapper">
                                                        <img src="<?php
                                                                    if (!empty($student['image'])) {
                                                                        echo base_url() . $student['image'];
                                                                    } else {
                                                                        echo base_url() . "uploads/student_images/no_image.png";
                                                                    }
                                                                    ?>" alt="Student">
                                                    </div>
                                                    <div class="student-details-grid">
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('name'); ?></span>
                                                            <span class="value"><?php echo $this->customlib->getFullName($student['firstname'], $student['middlename'], $student['lastname'], $sch_setting->middlename, $sch_setting->lastname); ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('class_section'); ?></span>
                                                            <span class="value"><?php echo $student['class'] . " (" . $student['section'] . ")" ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('father_name'); ?></span>
                                                            <span class="value"><?php echo $student['father_name']; ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('admission_no'); ?></span>
                                                            <span class="value"><?php echo $student['admission_no']; ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('mobile_number'); ?></span>
                                                            <span class="value"><?php echo $student['mobileno']; ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('roll_number'); ?></span>
                                                            <span class="value"><?php echo $student['roll_no'] ?: '-'; ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('category'); ?></span>
                                                            <span class="value"><?php echo $student['category'] ?: '-'; ?></span>
                                                        </div>
                                                        <div class="info-item">
                                                            <span class="label"><?php echo $this->lang->line('rte'); ?></span>
                                                            <span class="value">
                                                                <span class="badge-rte <?php echo (strtolower($student['rte']) == 'yes') ? 'badge-rte-yes' : 'badge-rte-no'; ?>">
                                                                    <?php echo $student['rte']; ?>
                                                                </span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="dates"><?php echo $this->lang->line('date'); ?>: <?php echo date($this->customlib->getSchoolDateFormat()); ?></div>
                                            </div>
                                        </div>

                                        <div class="table-responsive pb10">
                                            <div class="download_label"> <?php
                                                                            echo $this->lang->line('fees_statement') . " ";
                                                                            $this->customlib->get_postmessage();
                                                                            ?></div>
                                            <table class="table table-striped table-bordered table-hover example table-fixed-header">
                                                <thead class="header">
                                                    <tr>
                                                        <th><?php echo $this->lang->line('fees_group'); ?></th>
                                                        <th><?php echo $this->lang->line('fees_code'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('due_date'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('status'); ?></th>
                                                        <th class="text text-right"><?php echo $this->lang->line('amount'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('payment_id'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('mode'); ?></th>
                                                        <th class="text text-left"><?php echo $this->lang->line('date'); ?></th>
                                                        <th class="text text-right"><?php echo $this->lang->line('discount'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                                        <th class="text text-right"><?php echo $this->lang->line('fine'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                                        <th class="text text-right"><?php echo $this->lang->line('paid'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                                        <th class="text text-right"><?php echo $this->lang->line('balance'); ?> <span><?php echo "(" . $currency_symbol . ")"; ?></span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php

                                                    $total_amount          = "0";
                                                    $total_deposite_amount = "0";
                                                    $total_fine_amount     = "0";
                                                    $total_discount_amount = "0";
                                                    $total_balance_amount  = "0";
                                                    $alot_fee_discount     = 0;
                                                    foreach ($student['fees'] as $key => $fee) {

                                                        foreach ($fee as $fee_key => $fee_value) {
                                                            $fee_paid     = 0;
                                                            $fee_discount = 0;
                                                            $fee_fine     = 0;

                                                            if (!empty($fee_value->amount_detail)) {
                                                                $fee_deposits = json_decode(($fee_value->amount_detail));

                                                                foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                                    $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                                                    $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                                    $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                                                                }
                                                            }
                                                            $total_amount          = $total_amount + $fee_value->amount;
                                                            $total_discount_amount = $total_discount_amount + $fee_discount;
                                                            $total_deposite_amount = $total_deposite_amount + $fee_paid;
                                                            $total_fine_amount     = $total_fine_amount + $fee_fine;
                                                            $feetype_balance       = $fee_value->amount - ($fee_paid + $fee_discount);
                                                            $total_balance_amount  = $total_balance_amount + $feetype_balance;
                                                    ?>
                                                            <?php
                                                            if ($feetype_balance > 0 && strtotime($fee_value->due_date) < strtotime(date('Y-m-d'))) {
                                                            ?>
                                                                <tr class="danger">
                                                                <?php
                                                            } else {
                                                                ?>
                                                                <tr class="dark-gray">
                                                                <?php
                                                            }
                                                                ?>
                                                                <td align="left">
                                                                    <?php
                                                                    if ($fee_value->is_system) {
                                                                        echo $this->lang->line($fee_value->name) . " (" . $this->lang->line($fee_value->type) . ")";
                                                                    } else {
                                                                        echo $fee_value->name . " (" . $fee_value->type . ")";
                                                                    }
                                                                    ?></td>
                                                                <td align="left">
                                                                    <?php
                                                                    if ($fee_value->is_system) {
                                                                        echo $this->lang->line($fee_value->code);
                                                                    } else {
                                                                        echo $fee_value->code;
                                                                    }

                                                                    ?></td>
                                                                <td class="text text-left">

                                                                    <?php
                                                                    if ($fee_value->due_date == "0000-00-00") {
                                                                    } else {
                                                                        if ($fee_value->due_date) {
                                                                            echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_value->due_date));
                                                                        }
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td class="text text-left width85">
                                                                    <?php
                                                                    if ($feetype_balance == 0) {
                                                                    ?><span class="label label-success"><?php echo $this->lang->line('paid'); ?></span><?php
                                                                                                        } else if (!empty($fee_value->amount_detail)) {
                                                                                                            ?><span class="label label-warning"><?php echo $this->lang->line('partial'); ?></span><?php
                                                                                                            } else {
                                                                                                                ?><span class="label label-danger"><?php echo $this->lang->line('unpaid'); ?></span><?php
                                                                                                            }
                                                                                                            ?>
                                                                </td>
                                                                <td class="text text-right"><?php echo amountFormat($fee_value->amount);
                                                                                            if (($fee_value->due_date != "0000-00-00" && $fee_value->due_date != null) && (strtotime($fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                                                            ?>
                                                                        <span data-toggle="popover" class="text text-danger detail_popover"><?php echo " + " . amountFormat($fee_value->fine_amount); ?></span>

                                                                        <div class="fee_detail_popover" style="display: none">
                                                                            <?php
                                                                                                if ($fee_value->fine_amount != "") {
                                                                                                    $grand_fine_amount += $fee_value->fine_amount;
                                                                            ?>
                                                                                <p class="text text-danger"><?php echo $this->lang->line('fine'); ?></p>
                                                                            <?php
                                                                                                }
                                                                            ?>
                                                                        </div>
                                                                    <?php
                                                                                            }
                                                                    ?>
                                                                </td>
                                                                <td></td>
                                                                <td></td>
                                                                <td></td>
                                                                <td class="text text-right"><?php
                                                                                            echo amountFormat($fee_discount);
                                                                                            ?></td>
                                                                <td class="text text-right"><?php
                                                                                            echo amountFormat($fee_fine);
                                                                                            ?></td>
                                                                <td class="text text-right"><?php
                                                                                            echo amountFormat($fee_paid);
                                                                                            ?></td>
                                                                <td class="text text-right"><?php
                                                                                            $display_none = "ss-none";
                                                                                            if ($feetype_balance > 0) {
                                                                                                $display_none = "";

                                                                                                echo amountFormat($feetype_balance);
                                                                                            }
                                                                                            ?>
                                                                </td>
                                                                </tr>

                                                                <?php
                                                                if (!empty($fee_value->amount_detail)) {
                                                                    $fee_deposits = json_decode(($fee_value->amount_detail));

                                                                    foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                                ?>
                                                                        <tr class="white-td">
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td class="text-right"><img src="<?php echo base_url(); ?>backend/images/table-arrow.png" alt="" /></td>
                                                                            <td class="text text-left">

                                                                                <a href="#" data-toggle="popover" class="detail_popover"> <?php echo $fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?></a>
                                                                                <div class="fee_detail_popover" style="display: none">
                                                                                    <?php
                                                                                    if ($fee_deposits_value->description == "") {
                                                                                    ?>
                                                                                        <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                                                    <?php
                                                                                    } else {
                                                                                    ?>
                                                                                        <p class="text text-info"><?php echo $fee_deposits_value->description; ?></p>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            </td>
                                                                            <td class="text text-left"><?php echo $this->lang->line(strtolower($fee_deposits_value->payment_mode)); ?></td>
                                                                            <td class="text text-left">
                                                                                <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date)); ?>
                                                                            </td>
                                                                            <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount_discount); ?></td>
                                                                            <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount_fine); ?></td>
                                                                            <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount); ?></td>
                                                                            <td></td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                                <?php
                                                            }
                                                        }


                                                        if (!empty($student['transport_fees'])) {
                                                            $total_fees_fine_amount = 0;
                                                            foreach ($student['transport_fees'] as $transport_fee_key => $transport_fee_value) {

                                                                $fee_paid         = 0;
                                                                $fee_discount     = 0;
                                                                $fee_fine         = 0;
                                                                $fees_fine_amount = 0;
                                                                $feetype_balance  = 0;

                                                                if (!empty($transport_fee_value->amount_detail)) {
                                                                    $fee_deposits = json_decode(($transport_fee_value->amount_detail));
                                                                    foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                                        $fee_paid     = $fee_paid + $fee_deposits_value->amount;
                                                                        $fee_discount = $fee_discount + $fee_deposits_value->amount_discount;
                                                                        $fee_fine     = $fee_fine + $fee_deposits_value->amount_fine;
                                                                    }
                                                                }

                                                                $feetype_balance = $transport_fee_value->fees - ($fee_paid + $fee_discount);

                                                                if (($transport_fee_value->due_date != "0000-00-00" && $transport_fee_value->due_date != null) && (strtotime($transport_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                                    $fees_fine_amount       = is_null($transport_fee_value->fine_percentage) ? $transport_fee_value->fine_amount : percentageAmount($transport_fee_value->fees, $transport_fee_value->fine_percentage);
                                                                    $total_fees_fine_amount = $total_fees_fine_amount + $fees_fine_amount;
                                                                }

                                                                $total_amount += $transport_fee_value->fees;
                                                                $total_discount_amount += $fee_discount;
                                                                $total_deposite_amount += $fee_paid;
                                                                $total_fine_amount += $fee_fine;
                                                                $total_balance_amount += $feetype_balance;

                                                                if (strtotime($transport_fee_value->due_date) < strtotime(date('Y-m-d'))) {
                                                                ?>
                                                                    <tr class="danger font12">
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <tr class="dark-gray">
                                                                    <?php
                                                                }
                                                                    ?>

                                                                    <td align="left" class="text-rtl-right"><?php echo $this->lang->line('transport_fees'); ?></td>
                                                                    <td align="left" class="text-rtl-right"><?php echo $transport_fee_value->month; ?></td>
                                                                    <td align="left" class="text text-left">
                                                                        <?php echo $this->customlib->dateformat($transport_fee_value->due_date); ?> </td>
                                                                    <td align="left" class="text text-left width85">
                                                                        <?php
                                                                        if ($feetype_balance == 0) {
                                                                        ?><span class="label label-success"><?php echo $this->lang->line('paid'); ?></span><?php
                                                                                            } else if (!empty($transport_fee_value->amount_detail)) {
                                                                                                ?><span class="label label-warning"><?php echo $this->lang->line('partial'); ?></span><?php
                                                                                                } else {
                                                                                                    ?><span class="label label-danger"><?php echo $this->lang->line('unpaid'); ?></span><?php
                                                                                                }
                                                                                                ?>
                                                                    </td>
                                                                    <td class="text text-right">
                                                                        <?php

                                                                        echo amountFormat($transport_fee_value->fees);

                                                                        if (($transport_fee_value->due_date != "0000-00-00" && $transport_fee_value->due_date != null) && (strtotime($transport_fee_value->due_date) < strtotime(date('Y-m-d')))) {
                                                                            $tr_fine_amount = $transport_fee_value->fine_amount;
                                                                            if ($transport_fee_value->fine_type != "" && $transport_fee_value->fine_type == "percentage") {

                                                                                $tr_fine_amount = percentageAmount($transport_fee_value->fees, $transport_fee_value->fine_percentage);
                                                                            }
                                                                        ?>

                                                                            <span data-toggle="popover" class="text text-danger detail_popover"><?php echo " + " . amountFormat($tr_fine_amount); ?></span>
                                                                            <div class="fee_detail_popover" style="display: none">
                                                                                <?php
                                                                                if ($tr_fine_amount != "") {
                                                                                    $grand_fine_amount += $tr_fine_amount;
                                                                                ?>
                                                                                    <p class="text text-danger"><?php echo $this->lang->line('fine'); ?></p>
                                                                                <?php
                                                                                }
                                                                                ?>
                                                                            </div>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td class="text text-left"></td>
                                                                    <td class="text text-left"></td>
                                                                    <td class="text text-left"></td>
                                                                    <td class="text text-right"><?php
                                                                                                echo amountFormat($fee_discount);
                                                                                                ?></td>
                                                                    <td class="text text-right"><?php
                                                                                                echo amountFormat($fee_fine);
                                                                                                ?></td>
                                                                    <td class="text text-right"><?php
                                                                                                echo amountFormat($fee_paid);
                                                                                                ?></td>
                                                                    <td class="text text-right"><?php
                                                                                                $display_none = "ss-none";
                                                                                                if ($feetype_balance > 0) {
                                                                                                    $display_none = "";

                                                                                                    echo amountFormat($feetype_balance);
                                                                                                }
                                                                                                ?>
                                                                    </td>

                                                                    </tr>

                                                                    <?php
                                                                    if (!empty($transport_fee_value->amount_detail)) {

                                                                        $fee_deposits = json_decode(($transport_fee_value->amount_detail));

                                                                        foreach ($fee_deposits as $fee_deposits_key => $fee_deposits_value) {
                                                                    ?>
                                                                            <tr class="white-td">

                                                                                <td align="left"></td>
                                                                                <td align="left"></td>
                                                                                <td align="left"></td>
                                                                                <td align="left"></td>
                                                                                <td class="text-right"><img src="<?php echo base_url(); ?>backend/images/table-arrow.png" alt="" /></td>
                                                                                <td class="text text-left">

                                                                                    <a href="#" data-toggle="popover" class="detail_popover"> <?php echo $transport_fee_value->student_fees_deposite_id . "/" . $fee_deposits_value->inv_no; ?></a>
                                                                                    <div class="fee_detail_popover" style="display: none">
                                                                                        <?php
                                                                                        if ($fee_deposits_value->description == "") {
                                                                                        ?>
                                                                                            <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                                                        <?php
                                                                                        } else {
                                                                                        ?>
                                                                                            <p class="text text-info"><?php echo $fee_deposits_value->description; ?></p>
                                                                                        <?php
                                                                                        }
                                                                                        ?>
                                                                                    </div>
                                                                                </td>
                                                                                <td class="text text-left"><?php echo $this->lang->line(strtolower($fee_deposits_value->payment_mode)); ?></td>
                                                                                <td class="text text-left">
                                                                                    <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($fee_deposits_value->date)); ?>
                                                                                </td>
                                                                                <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount_discount); ?></td>
                                                                                <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount_fine); ?></td>
                                                                                <td class="text text-right"><?php echo amountFormat($fee_deposits_value->amount); ?></td>
                                                                                <td></td>

                                                                            </tr>
                                                                    <?php
                                                                        }
                                                                    }
                                                                    ?>

                                                                <?php
                                                            }
                                                        }


                                                        if (!empty($student['student_discount_fee'])) {

                                                            foreach ($student['student_discount_fee'] as $discount_key => $discount_value) {
                                                                ?>
                                                                    <tr class="dark-light">
                                                                        <td align="left"> <?php echo $this->lang->line('discount'); ?> </td>
                                                                        <td align="left">
                                                                            <?php echo $discount_value['code']; ?>
                                                                        </td>
                                                                        <td align="left"></td>
                                                                        <td align="left" class="text text-left">
                                                                            <?php
                                                                            if ($discount_value['status'] == "applied") {
                                                                            ?>
                                                                                <a href="#" data-toggle="popover" class="detail_popover">

                                                                                    <?php echo $this->lang->line('discount_of') . " " . $currency_symbol . amountFormat($discount_value['amount']) . " " . $this->lang->line($discount_value['status']) . " : " . $discount_value['payment_id']; ?>

                                                                                </a>
                                                                                <div class="fee_detail_popover" style="display: none">
                                                                                    <?php
                                                                                    if ($fee_deposits_value->description == "") {
                                                                                    ?>
                                                                                        <p class="text text-danger"><?php echo $this->lang->line('no_description'); ?></p>
                                                                                    <?php
                                                                                    } else {
                                                                                    ?>
                                                                                        <p class="text text-danger"><?php echo $discount_value['student_fees_discount_description'] ?></p>
                                                                                    <?php
                                                                                    }
                                                                                    ?>
                                                                                </div>
                                                                            <?php
                                                                            } else {
                                                                                echo '<p class="text text-danger">' . $this->lang->line('discount_of') . " " . $currency_symbol . amountFormat($discount_value['amount']) . " " . $this->lang->line($discount_value['status']);
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td></td>
                                                                        <td class="text text-left"></td>
                                                                        <td class="text text-left"></td>
                                                                        <td class="text text-left"></td>
                                                                        <td class="text text-right">
                                                                            <?php
                                                                            $alot_fee_discount = $alot_fee_discount;
                                                                            ?>
                                                                        </td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr>
                                                            <?php
                                                            }
                                                        }
                                                            ?>
                                                            <tr class="box box-solid total-bg">
                                                                <td align="left"></td>
                                                                <td align="left"></td>
                                                                <td align="left"></td>
                                                                <td class="text text-left"><?php echo $this->lang->line('grand_total'); ?></td>
                                                                <td class="text text-right"><?php
                                                                                            echo ($currency_symbol . amountFormat($total_amount));
                                                                                            ?>
                                                                    <span data-toggle="popover" class="text text-danger detail_popover"><?php echo " + " . (amountFormat($grand_fine_amount)); ?></span>

                                                                    <div class="fee_detail_popover" style="display: none">

                                                                        <p class="text text-danger"><?php echo $this->lang->line('fine'); ?></p>

                                                                    </div>

                                                                </td>
                                                                <td align="left"></td>
                                                                <td align="left"></td>
                                                                <td align="left"></td>
                                                                <td class="text text-right"><?php
                                                                                            echo ($currency_symbol . amountFormat($total_discount_amount + $alot_fee_discount));
                                                                                            ?></td>
                                                                <td class="text text-right"><?php
                                                                                            echo ($currency_symbol . amountFormat($total_fine_amount));
                                                                                            ?></td>
                                                                <td class="text text-right"><?php
                                                                                            echo ($currency_symbol . amountFormat($total_deposite_amount));
                                                                                            ?></td>
                                                                <td class="text text-right"><?php
                                                                                            echo ($currency_symbol . amountFormat($total_balance_amount - $alot_fee_discount));
                                                                                            ?></td>
                                                            </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php
                                    }
                                } else {
                                    ?>
                                    <div class="alert alert-info">
                                        <?php echo $this->lang->line('no_record_found'); ?>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                </div>
            <?php
                    } else {
                    }
            ?>
            </div>
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
    <div class="clearfix"></div>
</div>

<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id !== "" && section_id !== "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(i, obj) {
                        var sel = "";
                        if (section_id === obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }

    $(document).ready(function() {
        $('.detail_popover').popover({
            placement: 'right',
            title: '',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function() {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });

        $(document).on('change', '#class_id', function(e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {
                    'class_id': class_id
                },
                dataType: "json",
                success: function(data) {
                    $.each(data, function(i, obj) {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
        $(document).on('change', '#section_id', function(e) {
            getStudentsByClassAndSection();
        });
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        if (class_id != "" || section_id != "") {
            postbackStudentsByClassAndSection(class_id, section_id);
        }
    });

    function getStudentsByClassAndSection() {

        $('#student_id').html("");
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var student_id = '<?php echo set_value('student_id') ?>';
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "student/getByClassAndSection",
            data: {
                'class_id': class_id,
                'section_id': section_id
            },
            dataType: "json",
            success: function(data) {
                $.each(data, function(i, obj) {
                    var sel = "";
                    if (section_id == obj.section_id) {
                        sel = "selected=selected";
                    }

                    if (obj.admission_no == "") {
                        div_data += "<option value=" + obj.id + ">" + obj.full_name + " </option>";
                    } else {
                        div_data += "<option value=" + obj.id + ">" + obj.full_name + " (" + obj.admission_no + ") </option>";
                    }

                });
                $('#student_id').append(div_data);
            }
        });
    }

    function postbackStudentsByClassAndSection(class_id, section_id) {
        $('#student_id').html("");
        var student_id = '<?php echo set_value('student_id') ?>';
        var base_url = '<?php echo base_url() ?>';
        var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
        $.ajax({
            type: "GET",
            url: base_url + "student/getByClassAndSection",
            data: {
                'class_id': class_id,
                'section_id': section_id
            },
            dataType: "json",
            success: function(data) {
                $.each(data, function(i, obj) {
                    var sel = "";
                    if (student_id == obj.id) {
                        sel = "selected=selected";
                    }
                    div_data += "<option value=" + obj.id + " " + sel + ">" + obj.full_name + " (" + obj.admission_no + ") </option>";
                });
                $('#student_id').append(div_data);
            }
        });
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.table-fixed-header').fixedHeader();
    });

    (function($) {

        $.fn.fixedHeader = function(options) {
            var config = {
                topOffset: 70
                //bgColor: 'white'
            };
            if (options) {
                $.extend(config, options);
            }

            return this.each(function() {
                var o = $(this);

                var $win = $(window);
                var $head = $('thead.header', o);
                var isFixed = 0;
                var headTop = $head.length && $head.offset().top - config.topOffset;

                function processScroll() {
                    if (!o.is(':visible')) {
                        return;
                    }
                    if ($('thead.header-copy').size()) {
                        $('thead.header-copy').width($('thead.header').width());
                    }
                    var i;
                    var scrollTop = $win.scrollTop();
                    var t = $head.length && $head.offset().top - config.topOffset;
                    if (!isFixed && headTop !== t) {
                        headTop = t;
                    }
                    if (scrollTop >= headTop && !isFixed) {
                        isFixed = 1;
                    } else if (scrollTop <= headTop && isFixed) {
                        isFixed = 0;
                    }
                    isFixed ? $('thead.header-copy', o).offset({
                        left: $head.offset().left
                    }).removeClass('hide') : $('thead.header-copy', o).addClass('hide');
                }
                $win.on('scroll', processScroll);

                // hack sad times - holdover until rewrite for 2.1
                $head.on('click', function() {
                    if (!isFixed) {
                        setTimeout(function() {
                            $win.scrollTop($win.scrollTop() - 47);
                        }, 10);
                    }
                });

                $head.clone().removeClass('header').addClass('header-copy header-fixed').appendTo(o);
                var header_width = $head.width();
                o.find('thead.header-copy').width(header_width);
                o.find('thead.header > tr:first > th').each(function(i, h) {
                    var w = $(h).width();
                    o.find('thead.header-copy> tr > th:eq(' + i + ')').width(w);
                });
                $head.css({
                    margin: '0 auto',
                    width: o.width(),
                    'background-color': config.bgColor
                });
                processScroll();
            });
        };

    })(jQuery);
    $(document).ready(function() {
        $.extend($.fn.dataTable.defaults, {
            ordering: false,
            paging: false,
            bSort: false,
            info: false
        });
    });
</script>