<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<style type="text/css">
    .page-break {
        display: block;
        page-break-before: always;
    }

    @media print {
        .page-break {
            display: block;
            page-break-before: always;
        }

        .col-sm-1,
        .col-sm-2,
        .col-sm-3,
        .col-sm-4,
        .col-sm-5,
        .col-sm-6,
        .col-sm-7,
        .col-sm-8,
        .col-sm-9,
        .col-sm-10,
        .col-sm-11,
        .col-sm-12 {
            float: left;
        }

        .col-sm-12 {
            width: 100%;
        }

        .col-sm-11 {
            width: 91.66666667%;
        }

        .col-sm-10 {
            width: 83.33333333%;
        }

        .col-sm-9 {
            width: 75%;
        }

        .col-sm-8 {
            width: 66.66666667%;
        }

        .col-sm-7 {
            width: 58.33333333%;
        }

        .col-sm-6 {
            width: 50%;
        }

        .col-sm-5 {
            width: 41.66666667%;
        }

        .col-sm-4 {
            width: 33.33333333%;
        }

        .col-sm-3 {
            width: 25%;
        }

        .col-sm-2 {
            width: 16.66666667%;
        }

        .col-sm-1 {
            width: 8.33333333%;
        }

        .col-sm-pull-12 {
            right: 100%;
        }

        .col-sm-pull-11 {
            right: 91.66666667%;
        }

        .col-sm-pull-10 {
            right: 83.33333333%;
        }

        .col-sm-pull-9 {
            right: 75%;
        }

        .col-sm-pull-8 {
            right: 66.66666667%;
        }

        .col-sm-pull-7 {
            right: 58.33333333%;
        }

        .col-sm-pull-6 {
            right: 50%;
        }

        .col-sm-pull-5 {
            right: 41.66666667%;
        }

        .col-sm-pull-4 {
            right: 33.33333333%;
        }

        .col-sm-pull-3 {
            right: 25%;
        }

        .col-sm-pull-2 {
            right: 16.66666667%;
        }

        .col-sm-pull-1 {
            right: 8.33333333%;
        }

        .col-sm-pull-0 {
            right: auto;
        }

        .col-sm-push-12 {
            left: 100%;
        }

        .col-sm-push-11 {
            left: 91.66666667%;
        }

        .col-sm-push-10 {
            left: 83.33333333%;
        }

        .col-sm-push-9 {
            left: 75%;
        }

        .col-sm-push-8 {
            left: 66.66666667%;
        }

        .col-sm-push-7 {
            left: 58.33333333%;
        }

        .col-sm-push-6 {
            left: 50%;
        }

        .col-sm-push-5 {
            left: 41.66666667%;
        }

        .col-sm-push-4 {
            left: 33.33333333%;
        }

        .col-sm-push-3 {
            left: 25%;
        }

        .col-sm-push-2 {
            left: 16.66666667%;
        }

        .col-sm-push-1 {
            left: 8.33333333%;
        }

        .col-sm-push-0 {
            left: auto;
        }

        .col-sm-offset-12 {
            margin-left: 100%;
        }

        .col-sm-offset-11 {
            margin-left: 91.66666667%;
        }

        .col-sm-offset-10 {
            margin-left: 83.33333333%;
        }

        .col-sm-offset-9 {
            margin-left: 75%;
        }

        .col-sm-offset-8 {
            margin-left: 66.66666667%;
        }

        .col-sm-offset-7 {
            margin-left: 58.33333333%;
        }

        .col-sm-offset-6 {
            margin-left: 50%;
        }

        .col-sm-offset-5 {
            margin-left: 41.66666667%;
        }

        .col-sm-offset-4 {
            margin-left: 33.33333333%;
        }

        .col-sm-offset-3 {
            margin-left: 25%;
        }

        .col-sm-offset-2 {
            margin-left: 16.66666667%;
        }

        .col-sm-offset-1 {
            margin-left: 8.33333333%;
        }

        .col-sm-offset-0 {
            margin-left: 0%;
        }

        .visible-xs {
            display: none !important;
        }

        .hidden-xs {
            display: block !important;
        }

        table.hidden-xs {
            display: table;
        }

        tr.hidden-xs {
            display: table-row !important;
        }

        th.hidden-xs,
        td.hidden-xs {
            display: table-cell !important;
        }

        .hidden-xs.hidden-print {
            display: none !important;
        }

        .hidden-sm {
            display: none !important;
        }

        .visible-sm {
            display: block !important;
        }

        table.visible-sm {
            display: table;
        }

        tr.visible-sm {
            display: table-row !important;
        }

        th.visible-sm,
        td.visible-sm {
            display: table-cell !important;
        }
    }
</style>

<html lang="en">

<head>
    <title><?php echo $this->lang->line('fees_receipt'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>backend/dist/css/AdminLTE.min.css">
</head>

<body>
    <?php
    $print_copy = explode(',', $settinglist[0]['is_duplicate_fees_invoice']);
    ?>
    <div class="container">
        <div class="row">
            <div id="content" class="col-lg-12 col-sm-12 ">

                <?php
                if (in_array('0', $print_copy)) {
                    ?>
                    <div class="invoice">
                        <div class="row header ">
                            <div class="col-sm-12">

                                <?php 
    $receipt_template = $this->setting_model->get_receipt_template();
    if ($receipt_template == "custom") {
        echo $this->setting_model->get_receipt_header_content();
    } else {
    ?>
    <img src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/' . $this->setting_model->get_receiptheader()); ?>" style="height: 100px;width: 100%;">
    <?php } ?>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 text text-center">
                                <?php echo $this->lang->line('office_copy'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-6 text-left">
                                <br />
                                <address>
                                    <?php if (!empty($receipt_records[0])) { ?>
                                    <strong><?php echo $this->customlib->getFullName($receipt_records[0]->firstname, $receipt_records[0]->middlename, $receipt_records[0]->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></strong><?php echo " (" . $receipt_records[0]->admission_no . ")"; ?>
                                    <br>

                                    <?php echo $this->lang->line('father_name'); ?>:
                                    <?php echo $student['father_name']; ?><br>
                                    <?php echo $this->lang->line('class'); ?>:
                                    <?php echo $receipt_records[0]->class . " (" . $receipt_records[0]->section . ")"; ?>
                                    <?php } ?>
                                </address>
                            </div>
                            <div class="col-xs-6 text-right">
                                <br />
                                <address>
                                    <strong><?php echo $this->lang->line('date'); ?>:
                                        <?php
                                        $date = date('d-m-Y');
                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($date));
                                        ?>
                                    </strong><br />
                                    <strong>
                                        <?php echo $this->lang->line('payment_id'); ?>:
                                        <?php
                                        $payment_ids = array();
                                        foreach ($receipt_records as $rec) {
                                            $inv_id = isset($rec->student_fees_deposite_id) ? $rec->student_fees_deposite_id : $rec->id;
                                            $sub_id = isset($rec->sub_invoice_id) ? $rec->sub_invoice_id : '';
                                            $payment_ids[] = $inv_id . "/" . $sub_id;
                                        }
                                        echo implode(', ', $payment_ids);
                                        ?>
                                    </strong>
                                    <br />


                                    <strong> <?php echo $this->lang->line('collected_by'); ?>:
                                        <?php
                                        if (!empty($receipt_records[0]) && isJSON($receipt_records[0]->amount_detail)) {
                                            $first_rec = $receipt_records[0];
                                            $sub_inv_id = $first_rec->sub_invoice_id;
                                            $fee = json_decode($first_rec->amount_detail);
                                            $record = $fee->{$sub_inv_id};
                                            if (!empty($record->received_by)) {
                                                echo $record->collected_by;
                                            }
                                        }
                                        ?>
                                    </strong>



                                </address>
                            </div>
                        </div>
                        <hr style="margin-top: 0px;margin-bottom: 0px;" />
                        <div class="row">
                            <?php
                            if (!empty($receipt_records)) {
                                ?>

                                <table class="table table-striped table-responsive" style="font-size: 8pt;">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('fees_group'); ?></th>
                                            <th><?php echo $this->lang->line('fees_code'); ?></th>
                                            <th><?php echo $this->lang->line('mode'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('amount'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('discount'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('fine'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('balance'); ?></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grand_amount = 0;
                                        $grand_discount = 0;
                                        $grand_fine = 0;

                                        if (empty($receipt_records)) {
                                            ?>
                                            <tr>
                                                <td colspan="11" class="text-danger text-center">
                                                    <?php echo $this->lang->line('no_transaction_found'); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            foreach ($receipt_records as $rkey => $feeList) {
                                                $sub_invoice_id = $feeList->sub_invoice_id;

                                                if (isJSON($feeList->amount_detail)) {
                                                    $fee = json_decode($feeList->amount_detail);
                                                    $record = $fee->{$sub_invoice_id};
                                                }

                                                // Determine fee group name
                                                $fee_group_display = '';
                                                if (isset($feeList->name)) {
                                                    if (isset($feeList->is_system) && $feeList->is_system) {
                                                        $fee_group_display = $this->lang->line($feeList->name);
                                                        if (isset($feeList->type)) {
                                                            $fee_group_display .= " (" . $this->lang->line($feeList->type) . ")";
                                                        }
                                                    } else {
                                                        $fee_group_display = $feeList->name;
                                                        if (isset($feeList->type)) {
                                                            $fee_group_display .= " (" . $feeList->type . ")";
                                                        }
                                                    }
                                                }
                                                // Transport / Hostel override
                                                if (isset($feeList->fee_category) && $feeList->fee_category == 'transport') {
                                                    $fee_group_display = $this->lang->line('transport_fees');
                                                }
                                                if (isset($feeList->fee_category) && $feeList->fee_category == 'hostel') {
                                                    $fee_group_display = $this->lang->line('hostel_fees');
                                                }

                                                // Determine fee code
                                                $fee_code_display = '';
                                                if (isset($feeList->code)) {
                                                    if (isset($feeList->is_system) && $feeList->is_system) {
                                                        $fee_code_display = $this->lang->line($feeList->code);
                                                    } else {
                                                        $fee_code_display = $feeList->code;
                                                    }
                                                }
                                                if (isset($feeList->month)) {
                                                    $fee_code_display = $this->lang->line(strtolower($feeList->month));
                                                }

                                                $amount = $record->amount;
                                                $grand_amount += $amount;

                                                $amount_discount = $record->amount_discount;
                                                $grand_discount += $amount_discount;

                                                $amount_fine = $record->amount_fine;
                                                $grand_fine += $amount_fine;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($record->date)); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fee_group_display; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $fee_code_display; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $this->lang->line(strtolower($record->payment_mode)); ?>
                                                    </td>
                                                    <td class="text text-right">
                                                        <?php
                                                        // Show original amount if advance was applied
                                                        if (isset($record->original_amount) && $record->original_amount > $amount) {
                                                            echo '<small>Original: ' . $currency_symbol . amountFormat($record->original_amount) . '</small><br>';
                                                            echo '<small>Cash: ' . $currency_symbol . amountFormat($amount) . '</small><br>';
                                                            if (isset($record->advance_applied)) {
                                                                echo '<small>Advance: ' . $currency_symbol . amountFormat($record->advance_applied) . '</small>';
                                                            }
                                                        } else {
                                                            echo $currency_symbol . (amountFormat($amount));
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="text text-right">
                                                        <?php
                                                        echo $currency_symbol . (amountFormat($amount_discount));
                                                        ?>
                                                    </td>
                                                    <td class="text text-right">
                                                        <?php
                                                        echo $currency_symbol . (amountFormat($amount_fine));
                                                        ?>
                                                    </td>

                                                    <td class="text text-right">
                                                        <?php
                                                        if (isset($feeList->balance) && $feeList->balance !== '') {
                                                            echo $currency_symbol . (amountFormat($feeList->balance));
                                                        }
                                                        ?>
                                                    </td>

                                                </tr>
                                                <?php
                                            }

                                            // Grand total row
                                            if (count($receipt_records) > 1) {
                                            ?>
                                                <tr style="font-weight:bold; background-color: #f9f9f9;">
                                                    <td colspan="4" class="text-right"><strong><?php echo $this->lang->line('grand_total'); ?></strong></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_amount)); ?></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_discount)); ?></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_fine)); ?></td>
                                                    <td></td>
                                                </tr>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="row header">
                            <div class="col-sm-12">
                                <?php echo $this->lang->line('note'); ?>: <?php echo $record->description; ?>
                            </div>
                        </div>

                        <div class="row header">
                            <div class="col-sm-12">
                                <?php echo $this->setting_model->get_receiptfooter(); ?>
                            </div>
                        </div>
                    </div>

                    <?php
                }
                ?>

                <?php
                if (in_array('1', $print_copy)) {
                    ?>


                    <?php
                    if (!$sch_setting->single_page_print) {
                        ?>
                        <div class="page-break"></div>
                    <?php
                    } else {
                        echo "<br><br><hr style='width:100%;'>";
                    } ?>


                    <div class="invoice">
                        <div class="row header ">
                            <div class="col-sm-12">
                                <?php
                                ?>

                                <?php 
    $receipt_template = $this->setting_model->get_receipt_template();
    if ($receipt_template == "custom") {
        echo $this->setting_model->get_receipt_header_content();
    } else {
    ?>
    <img src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/' . $this->setting_model->get_receiptheader()); ?>" style="height: 100px;width: 100%;">
    <?php } ?>
                                <?php ?>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 text text-center">
                                <?php echo $this->lang->line('student_copy'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-6">
                                <br />
                                <address>
                                    <strong>
                                        <?php if (!empty($receipt_records[0])) { ?>
                                        <?php echo $this->customlib->getFullName($receipt_records[0]->firstname, $receipt_records[0]->middlename, $receipt_records[0]->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></strong><?php echo " (" . $receipt_records[0]->admission_no . ")"; ?>
                                    <br>

                                    <?php echo $this->lang->line('father_name'); ?>:
                                    <?php echo $student['father_name']; ?><br>
                                    <?php echo $this->lang->line('class'); ?>:
                                    <?php echo $receipt_records[0]->class . " (" . $receipt_records[0]->section . ")"; ?>
                                    <?php } ?>
                                </address>
                            </div>
                            <div class="col-xs-6 text-right">
                                <br />
                                <address>
                                    <strong>
                                        Date: <?php
                                        $date = date('d-m-Y');
                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($date));
                                        ?></strong><br />
                                    <strong> <?php echo $this->lang->line('payment_id'); ?>:
                                        <?php
                                        $payment_ids = array();
                                        foreach ($receipt_records as $rec) {
                                            $inv_id = isset($rec->student_fees_deposite_id) ? $rec->student_fees_deposite_id : $rec->id;
                                            $sub_id = isset($rec->sub_invoice_id) ? $rec->sub_invoice_id : '';
                                            $payment_ids[] = $inv_id . "/" . $sub_id;
                                        }
                                        echo implode(', ', $payment_ids);
                                        ?>
                                    </strong><br />


                                    <strong> <?php echo $this->lang->line('collected_by'); ?>:
                                        <?php
                                        if (!empty($receipt_records[0]) && isJSON($receipt_records[0]->amount_detail)) {
                                            $first_rec = $receipt_records[0];
                                            $sub_inv_id = $first_rec->sub_invoice_id;
                                            $fee = json_decode($first_rec->amount_detail);
                                            $record = $fee->{$sub_inv_id};
                                            if (!empty($record->received_by)) {
                                                echo $record->collected_by;
                                            }

                                        }
                                        ?>
                                    </strong>

                                </address>
                            </div>
                        </div>
                        <hr style="margin-top: 0px;margin-bottom: 0px;" />
                        <div class="row">
                            <?php
                            if (!empty($receipt_records)) {
                                ?>
                                <table class="table table-striped table-responsive" style="font-size: 8pt;">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('fees_group'); ?></th>
                                            <th><?php echo $this->lang->line('fees_code'); ?></th>
                                            <th><?php echo $this->lang->line('mode'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('amount'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('discount'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('fine'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('balance'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grand_amount = 0;
                                        $grand_discount = 0;
                                        $grand_fine = 0;

                                        if (empty($receipt_records)) {
                                            ?>
                                            <tr>
                                                <td colspan="11" class="text-danger text-center">
                                                    <?php echo $this->lang->line('no_transaction_found'); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            foreach ($receipt_records as $rkey => $feeList) {
                                                $sub_invoice_id = $feeList->sub_invoice_id;

                                                $a = json_decode($feeList->amount_detail);
                                                $record = $a->{$sub_invoice_id};

                                                // Determine fee group name
                                                $fee_group_display = '';
                                                if (isset($feeList->name)) {
                                                    if (isset($feeList->is_system) && $feeList->is_system) {
                                                        $fee_group_display = $this->lang->line($feeList->name);
                                                        if (isset($feeList->type)) {
                                                            $fee_group_display .= " (" . $this->lang->line($feeList->type) . ")";
                                                        }
                                                    } else {
                                                        $fee_group_display = $feeList->name;
                                                        if (isset($feeList->type)) {
                                                            $fee_group_display .= " (" . $feeList->type . ")";
                                                        }
                                                    }
                                                }
                                                if (isset($feeList->fee_category) && $feeList->fee_category == 'transport') {
                                                    $fee_group_display = $this->lang->line('transport_fees');
                                                }
                                                if (isset($feeList->fee_category) && $feeList->fee_category == 'hostel') {
                                                    $fee_group_display = $this->lang->line('hostel_fees');
                                                }

                                                // Determine fee code
                                                $fee_code_display = '';
                                                if (isset($feeList->code)) {
                                                    if (isset($feeList->is_system) && $feeList->is_system) {
                                                        $fee_code_display = $this->lang->line($feeList->code);
                                                    } else {
                                                        $fee_code_display = $feeList->code;
                                                    }
                                                }
                                                if (isset($feeList->month)) {
                                                    $fee_code_display = $this->lang->line(strtolower($feeList->month));
                                                }

                                                $amount = $record->amount;
                                                $grand_amount += $amount;

                                                $amount_discount = $record->amount_discount;
                                                $grand_discount += $amount_discount;

                                                $amount_fine = $record->amount_fine;
                                                $grand_fine += $amount_fine;
                                                ?>
                                            <tr>
                                                <td>
                                                    <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($record->date)); ?>

                                                </td>
                                                <td>
                                                    <?php echo $fee_group_display; ?>
                                                </td>
                                                <td>
                                                    <?php echo $fee_code_display; ?>
                                                </td>
                                                <td>
                                                    <?php echo $this->lang->line(strtolower($record->payment_mode)); ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    // Show original amount if advance was applied
                                                    if (isset($record->original_amount) && $record->original_amount > $amount) {
                                                        echo '<small>Original: ' . $currency_symbol . amountFormat($record->original_amount) . '</small><br>';
                                                        echo '<small>Cash: ' . $currency_symbol . amountFormat($amount) . '</small><br>';
                                                        if (isset($record->advance_applied)) {
                                                            echo '<small>Advance: ' . $currency_symbol . amountFormat($record->advance_applied) . '</small>';
                                                        }
                                                    } else {
                                                        echo $currency_symbol . (amountFormat($amount));
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo $currency_symbol . (amountFormat($amount_discount));
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo $currency_symbol . (amountFormat($amount_fine));
                                                    ?>
                                                </td>

                                                <?php
                                                    ?>
                                                <td class="text text-right">
                                                    <?php
                                                    if (isset($feeList->balance) && $feeList->balance !== '') {
                                                        echo $currency_symbol . (amountFormat($feeList->balance));
                                                    }
                                                    ?>
                                                </td>

                                            </tr>
                                            <?php
                                            }

                                            // Grand total row
                                            if (count($receipt_records) > 1) {
                                            ?>
                                                <tr style="font-weight:bold; background-color: #f9f9f9;">
                                                    <td colspan="4" class="text-right"><strong><?php echo $this->lang->line('grand_total'); ?></strong></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_amount)); ?></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_discount)); ?></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_fine)); ?></td>
                                                    <td></td>
                                                </tr>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="row header ">
                            <div class="col-sm-12">
                                <?php echo $this->lang->line('note'); ?>: <?php echo $record->description; ?>
                            </div>
                        </div>
                        <div class="row header ">
                            <div class="col-sm-12">
                                <?php echo $this->setting_model->get_receiptfooter(); ?>

                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

                <?php
                if (in_array('2', $print_copy)) {
                    ?>


                    <?php
                    if (!$sch_setting->single_page_print) {
                        ?>
                        <div class="page-break"></div>
                    <?php
                    } else {
                        echo "<br><br><hr style='width:100%;'>";
                    } ?>


                    <div class="invoice">
                        <div class="row header ">
                            <div class="col-sm-12">
                                <?php
                                ?>

                                <?php 
    $receipt_template = $this->setting_model->get_receipt_template();
    if ($receipt_template == "custom") {
        echo $this->setting_model->get_receipt_header_content();
    } else {
    ?>
    <img src="<?php echo $this->media_storage->getImageURL('/uploads/print_headerfooter/student_receipt/' . $this->setting_model->get_receiptheader()); ?>" style="height: 100px;width: 100%;">
    <?php } ?>
                                <?php ?>
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 text text-center">
                                <?php echo $this->lang->line('bank_copy'); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-6">
                                <br />
                                <address>
                                    <strong><?php if (!empty($receipt_records[0])) { ?>
                                        <?php echo $this->customlib->getFullName($receipt_records[0]->firstname, $receipt_records[0]->middlename, $receipt_records[0]->lastname, $sch_setting->middlename, $sch_setting->lastname); ?></strong><?php echo " (" . $receipt_records[0]->admission_no . ")"; ?><br>

                                    <?php echo $this->lang->line('father_name'); ?>:
                                    <?php echo $student['father_name']; ?><br>
                                    <?php echo $this->lang->line('class'); ?>:
                                    <?php echo $receipt_records[0]->class . " (" . $receipt_records[0]->section . ")"; ?>
                                    <?php } ?>
                                </address>
                            </div>
                            <div class="col-xs-6 text-right">
                                <br />
                                <address>
                                    <strong>
                                        Date: <?php
                                        $date = date('d-m-Y');
                                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($date));
                                        ?></strong><br />
                                    <strong> <?php echo $this->lang->line('payment_id'); ?>:
                                        <?php
                                        $payment_ids = array();
                                        foreach ($receipt_records as $rec) {
                                            $inv_id = isset($rec->student_fees_deposite_id) ? $rec->student_fees_deposite_id : $rec->id;
                                            $sub_id = isset($rec->sub_invoice_id) ? $rec->sub_invoice_id : '';
                                            $payment_ids[] = $inv_id . "/" . $sub_id;
                                        }
                                        echo implode(', ', $payment_ids);
                                        ?>
                                    </strong><br />


                                    <strong> <?php echo $this->lang->line('collected_by'); ?>:
                                        <?php
                                        if (!empty($receipt_records[0]) && isJSON($receipt_records[0]->amount_detail)) {
                                            $first_rec = $receipt_records[0];
                                            $sub_inv_id = $first_rec->sub_invoice_id;
                                            $fee = json_decode($first_rec->amount_detail);
                                            $record = $fee->{$sub_inv_id};
                                            if (!empty($record->received_by)) {
                                                echo $record->collected_by;
                                            }

                                        }
                                        ?>
                                    </strong>

                                </address>
                            </div>
                        </div>
                        <hr style="margin-top: 0px;margin-bottom: 0px;" />
                        <div class="row">
                            <?php
                            if (!empty($receipt_records)) {
                                ?>
                                <table class="table table-striped table-responsive" style="font-size: 8pt;">
                                    <thead>
                                        <tr>
                                            <th><?php echo $this->lang->line('date'); ?></th>
                                            <th><?php echo $this->lang->line('fees_group'); ?></th>
                                            <th><?php echo $this->lang->line('fees_code'); ?></th>
                                            <th><?php echo $this->lang->line('mode'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('amount'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('discount'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('fine'); ?></th>
                                            <th class="text text-right"><?php echo $this->lang->line('balance'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grand_amount = 0;
                                        $grand_discount = 0;
                                        $grand_fine = 0;

                                        if (empty($receipt_records)) {
                                            ?>
                                            <tr>
                                                <td colspan="11" class="text-danger text-center">
                                                    <?php echo $this->lang->line('no_transaction_found'); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        } else {
                                            foreach ($receipt_records as $rkey => $feeList) {
                                                $sub_invoice_id = $feeList->sub_invoice_id;

                                                $a = json_decode($feeList->amount_detail);
                                                $record = $a->{$sub_invoice_id};

                                                // Determine fee group name
                                                $fee_group_display = '';
                                                if (isset($feeList->name)) {
                                                    if (isset($feeList->is_system) && $feeList->is_system) {
                                                        $fee_group_display = $this->lang->line($feeList->name);
                                                        if (isset($feeList->type)) {
                                                            $fee_group_display .= " (" . $this->lang->line($feeList->type) . ")";
                                                        }
                                                    } else {
                                                        $fee_group_display = $feeList->name;
                                                        if (isset($feeList->type)) {
                                                            $fee_group_display .= " (" . $feeList->type . ")";
                                                        }
                                                    }
                                                }
                                                if (isset($feeList->fee_category) && $feeList->fee_category == 'transport') {
                                                    $fee_group_display = $this->lang->line('transport_fees');
                                                }
                                                if (isset($feeList->fee_category) && $feeList->fee_category == 'hostel') {
                                                    $fee_group_display = $this->lang->line('hostel_fees');
                                                }

                                                // Determine fee code
                                                $fee_code_display = '';
                                                if (isset($feeList->code)) {
                                                    if (isset($feeList->is_system) && $feeList->is_system) {
                                                        $fee_code_display = $this->lang->line($feeList->code);
                                                    } else {
                                                        $fee_code_display = $feeList->code;
                                                    }
                                                }
                                                if (isset($feeList->month)) {
                                                    $fee_code_display = $this->lang->line(strtolower($feeList->month));
                                                }

                                                $amount = $record->amount;
                                                $grand_amount += $amount;

                                                $amount_discount = $record->amount_discount;
                                                $grand_discount += $amount_discount;

                                                $amount_fine = $record->amount_fine;
                                                $grand_fine += $amount_fine;
                                                ?>
                                            <tr>
                                                <td>
                                                    <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($record->date)); ?>

                                                </td>
                                                <td>
                                                    <?php echo $fee_group_display; ?>
                                                </td>
                                                <td>
                                                    <?php echo $fee_code_display; ?>
                                                </td>
                                                <td>
                                                    <?php echo $this->lang->line(strtolower($record->payment_mode)); ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    // Show original amount if advance was applied
                                                    if (isset($record->original_amount) && $record->original_amount > $amount) {
                                                        echo '<small>Original: ' . $currency_symbol . amountFormat($record->original_amount) . '</small><br>';
                                                        echo '<small>Cash: ' . $currency_symbol . amountFormat($amount) . '</small><br>';
                                                        if (isset($record->advance_applied)) {
                                                            echo '<small>Advance: ' . $currency_symbol . amountFormat($record->advance_applied) . '</small>';
                                                        }
                                                    } else {
                                                        echo $currency_symbol . (amountFormat($amount));
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo $currency_symbol . (amountFormat($amount_discount));
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    echo $currency_symbol . (amountFormat($amount_fine));
                                                    ?>
                                                </td>
                                                <td class="text text-right">
                                                    <?php
                                                    if (isset($feeList->balance) && $feeList->balance !== '') {
                                                        echo $currency_symbol . (amountFormat($feeList->balance));
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            }

                                            // Grand total row
                                            if (count($receipt_records) > 1) {
                                            ?>
                                                <tr style="font-weight:bold; background-color: #f9f9f9;">
                                                    <td colspan="4" class="text-right"><strong><?php echo $this->lang->line('grand_total'); ?></strong></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_amount)); ?></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_discount)); ?></td>
                                                    <td class="text text-right"><?php echo $currency_symbol . (amountFormat($grand_fine)); ?></td>
                                                    <td></td>
                                                </tr>
                                            <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                            ?>

                        </div>
                        <div class="row header ">
                            <div class="col-sm-12">
                                <?php echo $this->lang->line('note'); ?>: <?php echo $record->description; ?>
                            </div>
                        </div>
                        <div class="row header ">
                            <div class="col-sm-12">
                                <?php echo $this->setting_model->get_receiptfooter(); ?>

                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <footer>

    </footer>
</body>

</html>
