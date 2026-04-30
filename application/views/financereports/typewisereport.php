<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
    /* Dropdown scrollable */
    .dropdown-menu-scrollable {
        max-height: 200px !important;
        overflow-y: auto !important;
        padding-left: 15px;
    }

    .dropdown-menu-scrollable .form-check {
        margin: 5px 0;
        padding-left: 10px;
    }

    .form-check {
        margin: 5px 0;
    }

    .form-check-input {
        margin-right: 10px;
    }

    .form-check-label {
        cursor: pointer;
    }

    .form-check-input:checked + .form-check-label::before {
        background-color: #007bff;
        border-color: #007bff;
    }

    .search-bar {
        margin-bottom: 10px;
        padding: 0 10px;
    }

    /* Modern Summary Cards */
    .summary-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 15px;
        margin-bottom: 25px;
    }

    .summary-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        display: flex;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #f0f0f0;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .summary-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .summary-card .icon-box {
        width: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #fff;
        flex-shrink: 0;
    }

    .summary-card .content-box {
        padding: 12px 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .summary-card .label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.8px;
        color: #7f8c8d;
        margin-bottom: 4px;
        white-space: nowrap;
    }

    .summary-card .value {
        font-size: 17px;
        font-weight: 800;
        color: #2c3e50;
    }

    .bg-gradient-blue { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); }
    .bg-gradient-green { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-gradient-orange { background: linear-gradient(135deg, #f12711 0%, #f5af19 100%); }
    .bg-gradient-teal { background: linear-gradient(135deg, #3ca5b0 0%, #b8f2e6 100%); color: #2c3e50 !important; }
    .bg-gradient-teal .icon-box { color: #2c3e50 !important; }
    .bg-gradient-red { background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%); }
    .bg-gradient-purple { background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); }

    /* Dynamic table styling */
    .table-responsive-scroll {
        overflow-x: auto !important;
        width: 100% !important;
        min-height: 400px !important;
        background: #fff;
        border-radius: 8px;
        border: 1px solid #eee;
        position: relative;
    }

    #headerTable {
        font-size: 12px !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: max-content !important;
        min-width: 100% !important;
        table-layout: auto !important;
        white-space: nowrap !important;
        margin-bottom: 0 !important;
    }
    
    #headerTable th {
        border: 1px solid #ddd !important;
        padding: 10px 15px !important;
        text-align: center !important;
        vertical-align: middle !important;
        font-weight: 700 !important;
        background-color: #f8f9fa !important;
    }
    
    #headerTable td {
        border: 1px solid #ddd !important;
        padding: 8px 12px !important;
        vertical-align: middle !important;
        text-align: center !important;
    }
    
    #headerTable .total-bg {
        background-color: #2c3e50 !important;
        color: white !important;
        font-weight: bold !important;
    }
    
    #headerTable thead tr:first-child th {
        background-color: #2c3e50 !important;
        color: white !important;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    #headerTable thead tr:nth-child(2) th {
        background-color: #34495e !important;
        color: white !important;
        font-size: 10px !important;
        padding: 6px !important;
    }
    
    .fee-column {
        min-width: 100px !important;
    }
    
    .student-info {
        min-width: 120px !important;
    }

    .fee-groups-col {
        white-space: normal !important;
        width: 180px !important;
        min-width: 180px !important;
        text-align: left !important;
    }
    
    /* Highlight balance columns */
    #headerTable td:last-child {
        background-color: #fff9f0 !important;
        font-weight: 900 !important;
    }

    /* Print specific styles */
    @media print {
        #headerTable {
            font-size: 8px !important;
            width: 100% !important;
            table-layout: fixed !important;
        }
        .summary-container, .search_btn, .box-header { display: none !important; }
        .table-responsive-scroll { overflow: visible !important; }
    }
</style>


<div class="content-wrapper">
    <section class="content-header"></section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('financereports/_finance');?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>
                    <form role="form" action="<?php echo site_url('financereports/typewisebalancereport') ?>" method="post" class="">
                        <div class="box-body row">
                            <?php echo $this->customlib->getCSRF(); ?>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="sch_session_id"><?php echo $this->lang->line('session'); ?></label><small class="req"> *</small>
                                    <select id="sch_session_id" name="sch_session_id" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($sessionlist as $session): ?>
                                            <option value="<?php echo $session['id'] ?>" <?php echo set_select('sch_session_id', $session['id']); ?>><?php echo $session['session'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('sch_session_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('class'); ?></label>
                                    <select autofocus="" id="class_id" name="class_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($classlist as $class) {
                                                ?>
                                            <option value="<?php echo $class['id'] ?>" <?php if (set_value('class_id') == $class['id']) {
                                                    echo "selected=selected";
                                                }
                                                ?>><?php echo $class['class'] ?>
                                            </option>
                                            <?php
                                            $count++;
                                            }
                                            ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('section'); ?></label>
                                    <select  id="section_id" name="section_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('section_id'); ?></span>
                                </div>
                            </div>



                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="feegroup_dropdown"><?php echo $this->lang->line('fees_group'); ?></label>
                                    <div class="dropdown">
                                        <input type="text" class="form-control" id="feegroup_dropdown" placeholder="<?php echo $this->lang->line('select'); ?>" aria-haspopup="true" aria-expanded="false" readonly data-toggle="dropdown">
                                        <div class="dropdown-menu dropdown-menu-scrollable" aria-labelledby="feegroup_dropdown">
                                            <input type="text" class="form-control search-bar" id="feegroup_search" placeholder="<?php echo $this->lang->line('search'); ?>">
                                            <div class="scroll-wrapper">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="check-all" name="check-all">
                                                    <label class="form-check-label" for="check-all">
                                                        Check All
                                                    </label>
                                                </div>
                                                <?php foreach ($feegroupList as $feegroup): ?>
                                                    <div class="form-check feegroup-item">
                                                        <input class="form-check-input feegroup-checkbox" type="checkbox" value="<?php echo $feegroup['id']; ?>" id="feegroup_<?php echo $feegroup['id']; ?>" name="feegroup_ids[]" <?php echo (set_checkbox('feegroup_ids', $feegroup['id']) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="feegroup_<?php echo $feegroup['id']; ?>">
                                                            <?php echo $feegroup['name']; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <input type="hidden" name="feegroup_id" id="feegroup_id" value="">
                                    </div>
                                    <span class="text-danger"><?php echo form_error('feegroup_ids'); ?></span>
                                </div>
                            </div>

<!-- 
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="feetype_dropdown"><?php echo $this->lang->line('fees_type'); ?></label>
                                    <div class="dropdown">
                                        <input type="text" class="form-control" id="feetype_dropdown" placeholder="<?php echo $this->lang->line('select'); ?>" aria-haspopup="true" aria-expanded="false" readonly data-toggle="dropdown">
                                        <div class="dropdown-menu dropdown-menu-scrollable" aria-labelledby="feetype_dropdown">
                                            <input type="text" class="form-control search-bar" id="feetype_search" placeholder="<?php echo $this->lang->line('search'); ?>">
                                            <div class="scroll-wrapper">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="check-all-feetype" name="check-all-feetype">
                                                    <label class="form-check-label" for="check-all-feetype">
                                                        Check All
                                                    </label>
                                                </div>
                                                <?php foreach ($feetypeList as $feetype): ?>
                                                    <div class="form-check feetype-item">
                                                        <input class="form-check-input feetype-checkbox" type="checkbox" value="<?php echo $feetype['id']; ?>" id="feetype_<?php echo $feetype['id']; ?>" name="feetype_ids[]" <?php echo (set_checkbox('feetype_ids', $feetype['id']) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="feetype_<?php echo $feetype['id']; ?>">
                                                            <?php echo $feetype['type']; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <input type="hidden" name="feetype_id" id="feetype_id" value="">
                                    </div>
                                    <span class="text-danger"><?php echo form_error('feetype_id'); ?></span>
                                </div>
                            </div> -->

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="feetype_dropdown"><?php echo $this->lang->line('fees_type'); ?></label>
                                    <div class="dropdown">
                                        <input type="text" class="form-control" id="feetype_dropdown" placeholder="<?php echo $this->lang->line('select'); ?>" aria-haspopup="true" aria-expanded="false" readonly data-toggle="dropdown">
                                        <div class="dropdown-menu dropdown-menu-scrollable" aria-labelledby="feetype_dropdown">
                                            <input type="text" class="form-control search-bar" id="feetype_search" placeholder="<?php echo $this->lang->line('search'); ?>">
                                            <div class="scroll-wrapper">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="check-all-feetype" name="check-all-feetype">
                                                    <label class="form-check-label" for="check-all-feetype">
                                                        Check All
                                                    </label>
                                                </div>
                                                <?php foreach ($feetypeList as $feetype): ?>
                                                    <div class="form-check feetype-item">
                                                        <input class="form-check-input feetype-checkbox" type="checkbox" value="<?php echo $feetype['id']; ?>" id="feetype_<?php echo $feetype['id']; ?>" name="feetype_ids[]" <?php echo (set_checkbox('feetype_ids', $feetype['id']) ? 'checked' : ''); ?>>
                                                        <label class="form-check-label" for="feetype_<?php echo $feetype['id']; ?>">
                                                            <?php echo $feetype['type']; ?>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <input type="hidden" name="feetype_id" id="feetype_id" value="">
                                    </div>
                                    <span class="text-danger"><?php echo form_error('feetype_ids[]'); ?></span>
                                </div>
                            </div>

                            



                            












                            <!-- <div class="col-sm-3">
                               <div class="form-group">
                                    <label for="exampleInputEmail1"><?php echo $this->lang->line('fees_type'); ?></label><small class="req"> *</small>

                                    <select  id="feetype_id" name="feetype_id" class="form-control" >
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                            foreach ($feetypeList as $feetype) {
                                                ?>
                                            <option value="<?php echo $feetype['id'] ?>"<?php
                                                if (set_value('feetype_id') == $feetype['id']) {
                                                        echo "selected =selected";
                                                    }
                                                    ?>><?php echo $feetype['type'] ?>
                                            </option>

                                            <?php
                                        $count++;
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('feetype_id'); ?></span>
                                </div>
                            </div> -->
                            

                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" id="search_btn" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>

                        </div>
                    </form>
                    <?php
                        if (empty($results)) {
                            ?>
                            <div class="box-header ptbnull">
                                <div class="alert alert-info">
                                <?php echo $this->lang->line('no_record_found'); ?>
                                </div>
                            </div>
                                            <?php
                        } else {
                            $sn=0;
                            
                            // Prepare data for dynamic column structure
                            $students = array();
                            $fee_types = array();
                            $fee_groups = array();

                            // Initialize results if not set
                            if (!isset($results)) {
                                $results = array();
                            }
                            
                            // Organize data hierarchically by student and fee group for rowspan implementation
                            if (!empty($results) && is_array($results)) {
                                foreach ($results as $row) {
                                    // Skip invalid rows
                                    if (!is_array($row) || empty($row['admission_no'])) {
                                        continue;
                                    }

                                    $student_key = $row['admission_no'];

                                if (!isset($students[$student_key])) {
                                    $students[$student_key] = array(
                                        'admission_no' => isset($row['admission_no']) ? $row['admission_no'] : '',
                                        'name' => trim((isset($row['firstname']) ? $row['firstname'] : '') . ' ' .
                                                      (isset($row['middlename']) ? $row['middlename'] : '') . ' ' .
                                                      (isset($row['lastname']) ? $row['lastname'] : '')),
                                        'mobileno' => isset($row['mobileno']) ? $row['mobileno'] : '',
                                        'class' => isset($row['class']) ? $row['class'] : '',
                                        'section' => isset($row['section']) ? $row['section'] : '',
                                        'fee_groups' => array() // Store fee groups with their fee type data
                                    );
                                }

                                // Collect unique fee types for column headers (with safety checks)
                                $fee_type_key = isset($row['type']) ? $row['type'] : 'Unknown';
                                if (!empty($fee_type_key)) {
                                    $fee_types[$fee_type_key] = array(
                                        'type' => $fee_type_key
                                    );
                                }

                                // Create unique key for fee group + fee type combination
                                $fee_group_key = isset($row['feegroupname']) ? $row['feegroupname'] : 'Unknown Group';
                                $unique_key = $fee_group_key . '|' . $fee_type_key;

                                // Initialize fee group if not exists
                                if (!isset($students[$student_key]['fee_groups'][$fee_group_key])) {
                                    $students[$student_key]['fee_groups'][$fee_group_key] = array(
                                        'group_name' => $fee_group_key,
                                        'fee_types' => array()
                                    );
                                }

                                // Store fee data by fee type within each fee group (with safe array access)
                                $total = isset($row['total']) ? $row['total'] : 0;
                                $total_fine = isset($row['total_fine']) ? $row['total_fine'] : 0;
                                $total_discount = isset($row['total_discount']) ? $row['total_discount'] : 0;
                                $total_amount = isset($row['total_amount']) ? $row['total_amount'] : 0;

                                $students[$student_key]['fee_groups'][$fee_group_key]['fee_types'][$fee_type_key] = array(
                                    'total' => $total,
                                    'fine' => $total_fine,
                                    'discount' => $total_discount,
                                    'paid' => $total_amount,
                                    'balance' => $total - $total_amount - $total_discount
                                );
                                } // End foreach ($results as $row)
                            } // End if (!empty($results))
                            
                            // Sort fee types for consistent column order
                            ksort($fee_types);

                            // Calculate grand totals for summary
                            $grand_totals = array();
                            foreach ($fee_types as $fee_type_key => $fee_info) {
                                $grand_totals[$fee_type_key] = array(
                                    'total' => 0, 'fine' => 0, 'discount' => 0, 'paid' => 0, 'balance' => 0
                                );
                            }
                            $grand_totals['overall'] = array('total' => 0, 'paid' => 0, 'balance' => 0);

                            // Calculate totals from hierarchical structure
                            if (!empty($students) && is_array($students)) {
                                foreach ($students as $student) {
                                    if (!isset($student['fee_groups']) || !is_array($student['fee_groups'])) {
                                        continue;
                                    }

                                    foreach ($student['fee_groups'] as $fee_group) {
                                        if (!isset($fee_group['fee_types']) || !is_array($fee_group['fee_types'])) {
                                            continue;
                                        }

                                        foreach ($fee_types as $fee_type_key => $fee_info) {
                                            $fee_data = isset($fee_group['fee_types'][$fee_type_key]) ? $fee_group['fee_types'][$fee_type_key] : array(
                                                'total' => 0, 'fine' => 0, 'discount' => 0, 'paid' => 0, 'balance' => 0
                                            );

                                            // Ensure grand_totals array exists for this fee type
                                            if (!isset($grand_totals[$fee_type_key])) {
                                                $grand_totals[$fee_type_key] = array(
                                                    'total' => 0, 'fine' => 0, 'discount' => 0, 'paid' => 0, 'balance' => 0
                                                );
                                            }

                                            $grand_totals[$fee_type_key]['total'] += isset($fee_data['total']) ? $fee_data['total'] : 0;
                                            $grand_totals[$fee_type_key]['fine'] += isset($fee_data['fine']) ? $fee_data['fine'] : 0;
                                            $grand_totals[$fee_type_key]['discount'] += isset($fee_data['discount']) ? $fee_data['discount'] : 0;
                                            $grand_totals[$fee_type_key]['paid'] += isset($fee_data['paid']) ? $fee_data['paid'] : 0;
                                            $grand_totals[$fee_type_key]['balance'] += isset($fee_data['balance']) ? $fee_data['balance'] : 0;

                                            $grand_totals['overall']['total'] += isset($fee_data['total']) ? $fee_data['total'] : 0;
                                            $grand_totals['overall']['paid'] += isset($fee_data['paid']) ? $fee_data['paid'] : 0;
                                            $grand_totals['overall']['balance'] += isset($fee_data['balance']) ? $fee_data['balance'] : 0;
                                        }
                                    }
                                }
                            }
                            
                            // Calculate summary statistics
                            $total_fee_groups = 0;
                            if (!empty($students) && is_array($students)) {
                                foreach ($students as $student) {
                                    if (isset($student['fee_groups']) && is_array($student['fee_groups'])) {
                                        $total_fee_groups += count($student['fee_groups']);
                                    }
                                }
                            }

                            $summary_stats = array(
                                'total_students' => count($students),
                                'total_fee_groups' => $total_fee_groups,
                                'total_fee_types' => count($fee_types),
                                'total_amount' => $grand_totals['overall']['total'],
                                'total_paid' => $grand_totals['overall']['paid'],
                                'total_balance' => $grand_totals['overall']['balance'],
                                'collection_percentage' => $grand_totals['overall']['total'] > 0 ?
                                    round(($grand_totals['overall']['paid'] / $grand_totals['overall']['total']) * 100, 2) : 0
                            );
                    ?>
                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i> <?php echo $this->lang->line('fees_collection_report'); ?> - Dynamic Column View</h3>
                            <div class="box-info">
                                <small class="text-muted">
                                    <i class="fa fa-info-circle"></i> 
                                    This report displays fee collection data with dynamic columns for each fee type and fee group combination.
                                    Each student's fees are shown across columns for easy comparison and analysis.
                                </small>
                            </div>
                        </div>
                        <?php
                        if (!empty($results)) {
                            // Calculate summary statistics
                            $summary_stats = array(
                                'total_students' => count($students),
                                'total_fee_types' => count($fee_types),
                                'total_amount' => $grand_totals['overall']['total'],
                                'total_paid' => $grand_totals['overall']['paid'],
                                'total_balance' => $grand_totals['overall']['balance'],
                                'collection_percentage' => $grand_totals['overall']['total'] > 0 ? 
                                    round(($grand_totals['overall']['paid'] / $grand_totals['overall']['total']) * 100, 2) : 0
                            );
                        ?>
                        <div class="row" style="margin-bottom: 20px;">
                            <div class="col-md-12">
                                <div class="summary-container">
                                    <div class="summary-card">
                                        <div class="icon-box bg-gradient-blue">
                                            <i class="fa fa-users"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="label">Students</div>
                                            <div class="value"><?php echo $summary_stats['total_students']; ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="summary-card">
                                        <div class="icon-box bg-gradient-green">
                                            <i class="fa fa-list"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="label">Fee Types</div>
                                            <div class="value"><?php echo $summary_stats['total_fee_types']; ?></div>
                                        </div>
                                    </div>

                                    <div class="summary-card">
                                        <div class="icon-box bg-gradient-orange">
                                            <i class="fa fa-money"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="label">Total Amount</div>
                                            <div class="value"><?php echo $currency_symbol . number_format($summary_stats['total_amount'], 2); ?></div>
                                        </div>
                                    </div>

                                    <div class="summary-card">
                                        <div class="icon-box bg-gradient-teal">
                                            <i class="fa fa-check-circle"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="label">Total Paid</div>
                                            <div class="value"><?php echo $currency_symbol . number_format($summary_stats['total_paid'], 2); ?></div>
                                        </div>
                                    </div>

                                    <div class="summary-card">
                                        <div class="icon-box bg-gradient-red">
                                            <i class="fa fa-exclamation-triangle"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="label">Balance</div>
                                            <div class="value" style="color: #e74c3c;"><?php echo $currency_symbol . number_format($summary_stats['total_balance'], 2); ?></div>
                                        </div>
                                    </div>

                                    <div class="summary-card">
                                        <div class="icon-box bg-gradient-purple">
                                            <i class="fa fa-percent"></i>
                                        </div>
                                        <div class="content-box">
                                            <div class="label">Collection %</div>
                                            <div class="value"><?php echo $summary_stats['collection_percentage']; ?>%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                        
                        
                                
                                
                        <div class="box-body table-responsive-scroll" id="transfee">
                        <div id="printhead"><center><b><h4><?php echo $this->lang->line('typewisebalancereport') . "<br>";
                            $this->customlib->get_postmessage();
                            ?></h4></b></center>
                        </div>
                            <div class="download_label"><?php echo $this->lang->line('typewisebalancereport') . "<br>";
                            $this->customlib->get_postmessage();
                            ?>
                        </div>
                        
                        <!-- Debug Information -->
                        <?php if (!empty($results)): ?>
                            <div class="alert alert-info" style="margin-bottom: 15px;">
                                <strong><i class="fa fa-info-circle"></i> Report Information:</strong><br>
                                <small>
                                    • Total Records Found: <strong><?php echo count($results); ?></strong><br>
                                    • Unique Students: <strong><?php echo count($students); ?></strong><br>
                                    • Total Fee Groups: <strong><?php echo $summary_stats['total_fee_groups']; ?></strong><br>
                                    • Unique Fee Types: <strong><?php echo count($fee_types); ?></strong><br>
                                    <?php if (!empty($fee_types)): ?>
                                        • Fee Types:
                                        <?php
                                        $fee_list = array();
                                        foreach ($fee_types as $fee_type_key => $fee_info) {
                                            $fee_list[] = $fee_info['type'];
                                        }
                                        echo '<strong>' . implode(', ', $fee_list) . '</strong>';
                                        ?>
                                    <?php endif; ?>
                                </small>
                            </div>
                        <?php endif; ?>
    
   

                            <a class="btn btn-default btn-xs pull-right" id="print" onclick="printDiv()" ><i class="fa fa-print"></i></a>
                            <a class="btn btn-default btn-xs pull-right" id="btnExport" onclick="exportToExcel();"> <i class="fa fa-file-excel-o"></i> </a>

                            <table class="table table-striped" id="headerTable">
                                <thead class="header">
                                    <tr>
                                        <th rowspan="2" class="student-info" style="width: 50px; min-width: 50px;"><?php echo $this->lang->line('s_no'); ?></th>
                                        <th rowspan="2" class="student-info" style="width: 100px; min-width: 100px;"><?php echo $this->lang->line('admission_no'); ?></th>
                                        <th rowspan="2" class="student-info" style="width: 150px; min-width: 150px;"><?php echo $this->lang->line('name'); ?></th>
                                        <th rowspan="2" class="student-info" style="width: 100px; min-width: 100px;"><?php echo $this->lang->line('phone'); ?></th>
                                        <th rowspan="2" class="student-info" style="width: 80px; min-width: 80px;"><?php echo $this->lang->line('class'); ?></th>
                                        <th rowspan="2" class="student-info" style="width: 80px; min-width: 80px;"><?php echo $this->lang->line('section'); ?></th>
                                        <th rowspan="2" class="fee-groups-col" style="background-color: #9b59b6 !important; color: white; width: 180px; min-width: 180px; font-weight: bold; text-align: center !important;"><strong>Fee Group</strong></th>
                                        <?php
                                        if (!empty($fee_types)) {
                                            foreach ($fee_types as $fee_type_key => $fee_info): ?>
                                                <th colspan="5" class="fee-column" style="text-align: center; background-color: #3498db !important; color: white; border: 2px solid #2980b9 !important;">
                                                    <strong><?php echo htmlspecialchars($fee_info['type']); ?></strong>
                                                </th>
                                            <?php endforeach;
                                        } else {
                                            // Show placeholder columns if no fee types found
                                            ?>
                                            <th colspan="5" class="fee-column" style="text-align: center; background-color: #95a5a6 !important; color: white;">
                                                <strong>No Fee Types Selected</strong>
                                            </th>
                                            <?php
                                        }
                                        ?>
                                        <th rowspan="2" style="background-color: #e74c3c !important ; color: white; min-width: 100px;"><strong>Total Amount</strong></th>
                                        <th rowspan="2" style="background-color: #27ae60 !important; color: white; min-width: 100px;"><strong>Total Paid</strong></th>
                                        <th rowspan="2" style="background-color: #f39c12 !important; color: white; min-width: 100px;"><strong>Total Balance</strong></th>
                                    </tr>
                                    <tr>
                                        <?php
                                        if (!empty($fee_types)) {
                                            foreach ($fee_types as $fee_type_key => $fee_info): ?>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important; border: 1px solid #bdc3c7 !important;"><strong>Total</strong></th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important; border: 1px solid #bdc3c7 !important;"><strong>Fine</strong></th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important; border: 1px solid #bdc3c7 !important;"><strong>Discount</strong></th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important; border: 1px solid #bdc3c7 !important;"><strong>Paid</strong></th>
                                                <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important; border: 1px solid #bdc3c7 !important;"><strong>Balance</strong></th>
                                            <?php endforeach;
                                        } else {
                                            // Show placeholder sub-columns
                                            ?>
                                            <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important;">-</th>
                                            <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important;">-</th>
                                            <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important;">-</th>
                                            <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important;">-</th>
                                            <th class="fee-column" style="font-size: 11px; background-color: #ecf0f1 !important; color: #2c3e50 !important;">-</th>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (!empty($students)) {
                                    $sn = 0;

                                    foreach ($students as $student) {
                                        // Skip invalid student data
                                        if (!is_array($student) || !isset($student['fee_groups']) || !is_array($student['fee_groups'])) {
                                            continue;
                                        }

                                        $sn++;
                                        $fee_groups = $student['fee_groups'];
                                        $fee_group_count = count($fee_groups);

                                        // Skip students with no fee groups
                                        if ($fee_group_count == 0) {
                                            continue;
                                        }

                                        // Calculate student totals across all fee groups
                                        $student_total = 0;
                                        $student_paid = 0;
                                        $student_balance = 0;

                                        foreach ($fee_groups as $fee_group) {
                                            if (!isset($fee_group['fee_types']) || !is_array($fee_group['fee_types'])) {
                                                continue;
                                            }

                                            foreach ($fee_group['fee_types'] as $fee_data) {
                                                if (!is_array($fee_data)) {
                                                    continue;
                                                }

                                                $student_total += isset($fee_data['total']) ? $fee_data['total'] : 0;
                                                $student_paid += isset($fee_data['paid']) ? $fee_data['paid'] : 0;
                                                $student_balance += isset($fee_data['balance']) ? $fee_data['balance'] : 0;
                                            }
                                        }

                                        $group_index = 0;
                                        foreach ($fee_groups as $fee_group_name => $fee_group) {
                                            $is_first_row = ($group_index == 0);
                                            $row_style = ($sn % 2 == 0) ? 'background-color: #f8f9fa !important;' : '';
                                ?>
                                    <tr style="<?php echo $row_style; ?>">
                                        <?php if ($is_first_row): ?>
                                            <!-- Student information with rowspan -->
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: center; font-weight: bold; vertical-align: middle; border-right: 2px solid #3498db !important;"><?php echo $sn; ?></td>
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: center; font-weight: bold; color: #3498db; vertical-align: middle; border-right: 2px solid #3498db !important;"><?php echo htmlspecialchars($student['admission_no']); ?></td>
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: left; padding-left: 10px; vertical-align: middle; border-right: 2px solid #3498db;"><?php echo htmlspecialchars($student['name']); ?></td>
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: center; vertical-align: middle; border-right: 2px solid #3498db;"><?php echo htmlspecialchars($student['mobileno']); ?></td>
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: center; color: #8e44ad; font-weight: bold; vertical-align: middle; border-right: 2px solid #3498db;"><?php echo htmlspecialchars($student['class']); ?></td>
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: center; color: #8e44ad; font-weight: bold; vertical-align: middle; border-right: 2px solid #3498db;"><?php echo htmlspecialchars($student['section']); ?></td>
                                        <?php endif; ?>

                                        <!-- Fee Group Column -->
                                        <td class="fee-groups-col" style="padding: 8px; background-color: #f4f3ff !important; color: #6c5ce7 !important; font-size: 12px !important; font-weight: bold !important; text-align: left !important; vertical-align: middle !important; border-right: 2px solid #9b59b6 !important;">
                                            <?php echo htmlspecialchars(isset($fee_group['group_name']) ? $fee_group['group_name'] : 'Unknown Group'); ?>
                                        </td>

                                        <?php
                                        // Display fee type data for this fee group
                                        if (!empty($fee_types)) {
                                            foreach ($fee_types as $fee_type_key => $fee_info):
                                                $fee_data = isset($fee_group['fee_types'][$fee_type_key]) ? $fee_group['fee_types'][$fee_type_key] : array(
                                                    'total' => 0, 'fine' => 0, 'discount' => 0, 'paid' => 0, 'balance' => 0
                                                );
                                        ?>
                                                <td class="fee-column" style="text-align: right; <?php echo $fee_data['total'] > 0 ? 'background-color: #ebf3fd; color: #2980b9; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                    <?php echo $fee_data['total'] > 0 ? number_format($fee_data['total'], 2) : '-'; ?>
                                                </td>
                                                <td class="fee-column" style="text-align: right; <?php echo $fee_data['fine'] > 0 ? 'background-color: #fdf2e9; color: #e67e22; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                    <?php echo $fee_data['fine'] > 0 ? number_format($fee_data['fine'], 2) : '-'; ?>
                                                </td>
                                                <td class="fee-column" style="text-align: right; <?php echo $fee_data['discount'] > 0 ? 'background-color: #eafaf1; color: #27ae60; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                    <?php echo $fee_data['discount'] > 0 ? number_format($fee_data['discount'], 2) : '-'; ?>
                                                </td>
                                                <td class="fee-column" style="text-align: right; <?php echo $fee_data['paid'] > 0 ? 'background-color: #e8f6f3; color: #16a085; font-weight: bold;' : 'color: #95a5a6;'; ?>">
                                                    <?php echo $fee_data['paid'] > 0 ? number_format($fee_data['paid'], 2) : '-'; ?>
                                                </td>
                                                <td class="fee-column" style="text-align: right; <?php echo $fee_data['balance'] > 0 ? 'background-color: #fdedec; color: #e74c3c; font-weight: bold;' : ($fee_data['balance'] == 0 && $fee_data['total'] > 0 ? 'background-color: #eafaf1; color: #27ae60; font-weight: bold;' : 'color: #95a5a6;'); ?>">
                                                    <?php echo $fee_data['balance'] != 0 ? number_format($fee_data['balance'], 2) : ($fee_data['total'] > 0 ? '0.00' : '-'); ?>
                                                </td>
                                        <?php
                                            endforeach;
                                        } else {
                                            // Show placeholder cells if no fee types
                                            ?>
                                            <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                            <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                            <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                            <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                            <td class="fee-column" style="text-align: center; color: #95a5a6;">-</td>
                                            <?php
                                        }
                                        ?>

                                        <?php if ($is_first_row): ?>
                                            <!-- Student totals with rowspan -->
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: right; font-weight: bold; background-color: #fbeee6; color: #d35400; border: 2px solid #e67e22; vertical-align: middle;">
                                                <?php echo number_format($student_total, 2); ?>
                                            </td>
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: right; font-weight: bold; background-color: #eafaf1; color: #27ae60; border: 2px solid #2ecc71; vertical-align: middle;">
                                                <?php echo number_format($student_paid, 2); ?>
                                            </td>
                                            <td rowspan="<?php echo $fee_group_count; ?>" style="text-align: right; font-weight: bold; background-color: #<?php echo $student_balance > 0 ? 'fdedec; color: #e74c3c; border: 2px solid #e74c3c;' : 'eafaf1; color: #27ae60; border: 2px solid #27ae60;'; ?> vertical-align: middle;">
                                                <?php echo number_format($student_balance, 2); ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php
                                            $group_index++;
                                        } // End fee groups loop
                                    } // End students loop
                                ?>

                                <!-- Grand Total Row -->
                                <tr class="total-bg" style="background-color: #2c3e50 !important; color: white !important; font-weight: bold !important; border-top: 3px solid #34495e;">
                                    <td colspan="7" style="text-align: center; font-weight: bold; font-size: 14px; padding: 10px;">
                                        <i class="fa fa-calculator"></i> GRAND TOTAL
                                    </td>
                                    <?php
                                    if (!empty($fee_types)) {
                                        foreach ($fee_types as $fee_type_key => $fee_info): ?>
                                            <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($grand_totals[$fee_type_key]['total'], 2); ?></td>
                                            <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($grand_totals[$fee_type_key]['fine'], 2); ?></td>
                                            <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($grand_totals[$fee_type_key]['discount'], 2); ?></td>
                                            <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($grand_totals[$fee_type_key]['paid'], 2); ?></td>
                                            <td class="fee-column" style="text-align: right; font-weight: bold;"><?php echo number_format($grand_totals[$fee_type_key]['balance'], 2); ?></td>
                                        <?php endforeach;
                                    } else {
                                        // Show placeholder cells in grand total
                                        ?>
                                        <td class="fee-column" style="text-align: center;">-</td>
                                        <td class="fee-column" style="text-align: center;">-</td>
                                        <td class="fee-column" style="text-align: center;">-</td>
                                        <td class="fee-column" style="text-align: center;">-</td>
                                        <td class="fee-column" style="text-align: center;">-</td>
                                        <?php
                                    }
                                    ?>
                                    <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($grand_totals['overall']['total'], 2); ?></td>
                                    <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($grand_totals['overall']['paid'], 2); ?></td>
                                    <td style="text-align: right; font-weight: bold; font-size: 14px;"><?php echo number_format($grand_totals['overall']['balance'], 2); ?></td>
                                </tr>
                                <?php
                                } // End if students not empty
                                else {
                                    // Show message when no students found
                                ?>
                                    <tr>
                                        <td colspan="<?php echo 10 + (count($fee_types) * 5); ?>" style="text-align: center; padding: 20px; color: #7f8c8d; font-style: italic;">
                                            <i class="fa fa-info-circle"></i> No student data found for the selected criteria. Please adjust your filters and try again.
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                     <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
</div>



<iframe id="txtArea1" style="display:none"></iframe>

<script>

    $(document).ready(function(){
        var class_id = $('#class_id').val();
        var section_id = '<?php echo $selected_section; ?>';
        getSectionByClass(class_id, section_id);
    })

    $(document).on('change', '#class_id', function (e) {
        $('#section_id').html("");
        var class_id = $(this).val();
        getSectionByClass(class_id, 0);
    });

    function getSectionByClass(class_id, section_id) {

        if (class_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                beforeSend: function () {
                    $('#section_id').addClass('dropdownloading');
                },
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {

                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                },
                complete: function () {
                    $('#section_id').removeClass('dropdownloading');
                }
            });
        }
    }


    document.getElementById("print").style.display = "block";
    document.getElementById("btnExport").style.display = "block";
    document.getElementById("printhead").style.display = "none";

    function printDiv() {
        document.getElementById("print").style.display = "none";
        document.getElementById("btnExport").style.display = "none";
        document.getElementById("printhead").style.display = "block";
        
        // Create a print-specific version with smaller fonts
        var divElements = document.getElementById('transfee').innerHTML;
        var printContent = divElements.replace(/class="table table-striped table-bordered table-hover"/g, 'class="table table-bordered" style="font-size: 8px;"');
        
        var oldPage = document.body.innerHTML;
        document.body.innerHTML =
                "<html><head><title><?php echo $this->lang->line('typewisebalancereport'); ?></title>" +
                "<style>@page { size: landscape; margin: 0.5in; } " +
                "table { border-collapse: collapse; width: 100%; font-size: 8px; } " +
                "th, td { border: 1px solid #000; padding: 2px; text-align: center; } " +
                "th { background-color: #f0f0f0; font-weight: bold; } " +
                ".total-bg { background-color: #d9d9d9 !important; font-weight: bold; }" +
                "</style></head><body>" +
                printContent + "</body>";
        window.print();
        document.body.innerHTML = oldPage;
        document.getElementById("printhead").style.display = "none";
        location.reload(true);
    }

    function fnExcelReport(){
        exportToExcel();
    }

    function exportToExcel(){
        var htmls = "";
        var uri = 'data:application/vnd.ms-excel;base64,';
        var template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table border="1">{table}</table></body></html>';
        var base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        };

        var format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        };
        
        var tab_text = "<tr>";
        var textRange;
        var j = 0;
        var val="";
        tab = document.getElementById('headerTable'); // id of table

        // Add report title
        tab_text += "<tr><td colspan='20' style='text-align:center;font-weight:bold;font-size:16px;'><?php echo $this->lang->line('typewisebalancereport'); ?></td></tr>";
        tab_text += "<tr><td colspan='20' style='text-align:center;'><?php echo date('Y-m-d H:i:s'); ?></td></tr>";
        tab_text += "<tr><td colspan='20'></td></tr>";

        for (j = 0; j < tab.rows.length; j++)
        {
            tab_text = tab_text + tab.rows[j].innerHTML + "</tr>";
        }

        var ctx = {
            worksheet : 'Type Wise Balance Report',
            table : tab_text
        }

        var link = document.createElement("a");
        link.download = "typewise_balance_report_" + new Date().toISOString().slice(0,10) + ".xls";
        link.href = uri + base64(format(template, ctx));
        link.click();
    }

</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<!-- 
<script>
    $(document).ready(function() {
        // Handle checkbox selection
        $('.dropdown-menu-scrollable').on('click', function(e) {
            e.stopPropagation();
        });

        // Check All functionality
        $('#check-all').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.feegroup-checkbox').prop('checked', isChecked);
            updateDropdownText();
            updateHiddenInput();
        });

        // Handle checkbox change
        $('.feegroup-checkbox').on('change', function() {
            updateDropdownText();
            updateHiddenInput();
        });

        // Update dropdown text based on selection
        function updateDropdownText() {
            var selectedGroups = $('.feegroup-checkbox:checked').map(function() {
                return $(this).next('.form-check-label').text();
            }).get().join(', ');

            if (selectedGroups === '') {
                selectedGroups = '<?php echo $this->lang->line('select'); ?>';
            }

            $('#feegroup_dropdown').val(selectedGroups);
        }

        // Update hidden input with selected IDs
        function updateHiddenInput() {
            var selectedIds = $('.feegroup-checkbox:checked').map(function() {
                return $(this).val();
            }).get().join(',');

            $('#feegroup_id').val(selectedIds);
        }

        // Initialize dropdown text on page load
        updateDropdownText();
    });
</script> -->

<script>
    $(document).ready(function() {
        // Handle checkbox selection
        $('.dropdown-menu-scrollable').on('click', function(e) {
            e.stopPropagation();
        });

        // Check All functionality
        $('#check-all').on('change', function() {
            var isChecked = $(this).prop('checked');
            $('.feegroup-checkbox').prop('checked', isChecked);
            updateDropdownText();
            updateHiddenInput();
        });

        // Handle checkbox change
        $('.feegroup-checkbox').on('change', function() {
            updateDropdownText();
            updateHiddenInput();
        });

        // Update dropdown text based on selection
        function updateDropdownText() {
            var selectedGroups = $('.feegroup-checkbox:checked').map(function() {
                return $(this).next('.form-check-label').text();
            }).get().join(', ');

            if (selectedGroups === '') {
                selectedGroups = '<?php echo $this->lang->line('select'); ?>';
            }

            $('#feegroup_dropdown').val(selectedGroups);
        }

        // Update hidden input with selected IDs
        function updateHiddenInput() {
            var selectedIds = $('.feegroup-checkbox:checked').map(function() {
                return $(this).val();
            }).get().join(',');

            $('#feegroup_id').val(selectedIds);
        }

        // Search functionality for checkboxes
        $('#feegroup_search').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            $('.feegroup-item').each(function() {
                var itemText = $(this).text().toLowerCase();
                $(this).toggle(itemText.indexOf(searchText) !== -1);
            });
        });

        // Initialize dropdown text on page load
        updateDropdownText();
    });
</script>



<script>
$(document).ready(function() {
    // Handle checkbox selection for fee type

    // Check All functionality for fee type
    $('#check-all-feetype').on('change', function() {
        var isChecked = $(this).prop('checked');
        $('.feetype-checkbox').prop('checked', isChecked);
        updateFeetypeDropdownText();
        updateFeetypeHiddenInput();
    });

    // Handle checkbox change for fee type
    $('.feetype-checkbox').on('change', function() {
        updateFeetypeDropdownText();
        updateFeetypeHiddenInput();
    });

    // Update dropdown text based on selection for fee type
    function updateFeetypeDropdownText() {
        var selectedTypes = $('.feetype-checkbox:checked').map(function() {
            return $(this).next('.form-check-label').text();
        }).get().join(', ');

        if (selectedTypes === '') {
            selectedTypes = '<?php echo $this->lang->line('select'); ?>';
        }

        $('#feetype_dropdown').val(selectedTypes);
    }

    function updateFeetypeHiddenInput() {
        var selectedIds = $('.feetype-checkbox:checked').map(function() {
            return $(this).val();
        }).get().join(',');

        $('#feetype_id').val(selectedIds);
    }

    $('#feetype_search').on('input', function() {
        var searchText = $(this).val().toLowerCase();
        $('.feetype-item').each(function() {
            var itemText = $(this).text().toLowerCase();
            $(this).toggle(itemText.indexOf(searchText) !== -1);
        });
    });

    updateFeetypeDropdownText();
});
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('check-all-feetype');
        const checkboxes = document.querySelectorAll('.feetype-checkbox');
        
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
                if (typeof updateFeetypeDropdownText === 'function') updateFeetypeDropdownText();
                if (typeof updateFeetypeHiddenInput === 'function') updateFeetypeHiddenInput();
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selected = Array.from(checkboxes)
                    .filter(ch => ch.checked)
                    .map(ch => ch.value);
                const hiddenInput = document.getElementById('feetype_id');
                if (hiddenInput) hiddenInput.value = selected.join(',');
            });
        });
    });
</script>

