<?php $currency_symbol = $this->customlib->getSchoolCurrencyFormat(); ?>
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
<style type="text/css">
    .borderwhite {
        border-top-color: #fff !important;
    }

    .box-header>.box-tools {
        display: none;
    }

    .sidebar-collapse #barChart {
        height: 100% !important;
    }

    .sidebar-collapse #lineChart {
        height: 100% !important;
    }

    /*.fc-day-grid-container{overflow: visible !important;}*/
    .tooltip-inner {
        max-width: 135px;
    }

    /* Financial Summary Cards Styling */
    .hover-expand-effect {
        transition: all 0.3s ease;
        cursor: pointer;
        margin-bottom: 20px;
    }

    .hover-expand-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .info-box {
        border-radius: 8px;
        overflow: hidden;
    }

    .info-box-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    .info-box-number {
        font-size: 22px;
        font-weight: bold;
        line-height: 1.2;
    }

    .info-box-text {
        font-size: 14px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .progress-description {
        font-size: 12px;
        opacity: 0.8;
        margin-top: 5px;
    }



    .filter-controls {
        display: flex;
        align-items: center;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-group {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .filter-group label {
        margin: 0;
        font-weight: normal;
        white-space: nowrap;
    }

    .filter-group select,
    .filter-group input {
        min-width: 120px;
    }

    .btn-apply-filter {
        background-color: #3c8dbc;
        color: white;
        border: none;
        padding: 6px 15px;
        border-radius: 3px;
        cursor: pointer;
    }

    .btn-apply-filter:hover {
        background-color: #2e6da4;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .info-box-number {
            font-size: 18px;
        }

        .info-box-text {
            font-size: 12px;
        }

        .filter-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-group {
            justify-content: space-between;
        }
    }

    .roles-carousel-box .roles-carousel-inner {
        position: relative;
        min-height: 40px;
    }

    .roles-carousel-box .role-item {
        display: none;
    }

    .roles-carousel-box .roles-carousel-controls {
        margin-top: 8px;
        text-align: right;
    }

    .roles-carousel-box .roles-carousel-controls .btn {
        padding: 2px 6px;
    }

    @media (max-width: 991px) {
        .monthly-widgets-row>.col-md-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }

    @media (max-width: 767px) {
        .monthly-widgets-row>.col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="">

            <?php if ($mysqlVersion && $sqlMode && strpos($sqlMode->mode, 'ONLY_FULL_GROUP_BY') !== false) { ?>
                <div class="alert alert-danger">
                    Smart School may not work properly because ONLY_FULL_GROUP_BY is enabled, consult with your hosting provider to disable ONLY_FULL_GROUP_BY in sql_mode configuration.
                </div>
            <?php } ?>

            <?php
            $show    = false;
            $role    = $this->customlib->getStaffRole();
            $role_id = json_decode($role)->id;
            foreach ($notifications as $notice_key => $notice_value) {

                if ($role_id == 7) {
                    $show = true;
                } elseif (date($this->customlib->getSchoolDateFormat()) >= date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($notice_value->publish_date))) {
                    $show = true;
                }
                if ($show) {
            ?>
                    <div class="dashalert alert alert-success alert-dismissible" role="alert">
                        <button type="button" class="alertclose close close_notice" data-dismiss="alert" aria-label="Close" data-noticeid="<?php echo $notice_value->id; ?>"><span aria-hidden="true">&times;</span></button>
                        <a href="<?php echo site_url('admin/notification') ?>"><?php echo $notice_value->title; ?></a>
                    </div>
            <?php
                }
            }
            ?>
        </div>
        <!-- Top Summary Section Title -->
        <div class="row">
            <div class="col-md-12">
                <div class="dashboard-page-header">
                    <div class="dashboard-page-header__left">
                        <h3 class="dashboard-welcome-title">Hi, Welcome back!</h3>
                        <div class="dashboard-welcome-breadcrumb">
                            <span class="dashboard-breadcrumb-item">Dashboard</span>
                            <span class="dashboard-breadcrumb-sep">
                                <i class="ri-arrow-right-double-fill"></i>
                            </span>
                            <span class="dashboard-breadcrumb-item"><?php echo $this->customlib->getAdminSessionUserName(); ?></span>
                        </div>
                    </div>

                    <?php
                    $show_header_expense = isset($can_view_expense) && $can_view_expense;
                    $show_header_profit = isset($can_view_income, $can_view_expense) && $can_view_income && $can_view_expense;
                    $profit_is_positive = (isset($net_profit) && $net_profit >= 0);
                    ?>

                    <?php if ($show_header_expense || $show_header_profit) { ?>
                        <div class="dashboard-top-metrics dashboard-top-metrics--animate" data-bars-animate>
                            <?php if ($show_header_expense) { ?>
                                <div class="dashboard-metric dashboard-metric--expense">
                                    <div class="dashboard-metric__text">
                                        <div class="dashboard-metric__label">EXPENSES</div>
                                        <div class="dashboard-metric__value" id="total_expense_display"><?php echo $currency_symbol . number_format($total_expense, 2); ?></div>
                                        <div class="dashboard-metric__sub" id="expense_period"><?php echo $current_month; ?></div>
                                    </div>
                                    <div class="dashboard-metric__bars" aria-hidden="true">
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($show_header_profit) { ?>
                                <div class="dashboard-metric <?php echo $profit_is_positive ? 'dashboard-metric--profit' : 'dashboard-metric--loss'; ?>" id="net_profit_card">
                                    <div class="dashboard-metric__text">
                                        <div class="dashboard-metric__label" id="net_profit_label"><?php echo ($net_profit >= 0) ? 'PROFIT' : 'LOSS'; ?></div>
                                        <div class="dashboard-metric__value" id="net_profit_display"><?php echo $currency_symbol . number_format(abs($net_profit), 2); ?></div>
                                        <div class="dashboard-metric__sub" id="profit_period"><?php echo $current_month; ?></div>
                                    </div>
                                    <div class="dashboard-metric__bars" aria-hidden="true">
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                        <span class="dashboard-metric__bar"></span>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php
        $demo_rings = (string) $this->input->get('demo_rings');

        $fees_paid_display = $total_paid;
        $fees_total_display = $total_fees;
        $fees_pct_display = (float) $fessprogressbar;

        $leads_done_display = $total_complete + 0;
        $leads_total_display = $total_enquiry;
        $leads_pct_display = (float) $fenquiryprogressbar;

        $staff_present_display = $Staffattendence_data + 0;
        $staff_total_display = $getTotalStaff_data;
        $staff_pct_display = (float) $percentTotalStaff_data;

        if ($demo_rings === '1') {
            $fees_paid_display = 72500;
            $fees_total_display = 100000;
            $fees_pct_display = 72.5;

            $leads_done_display = 38;
            $leads_total_display = 50;
            $leads_pct_display = 76;

            $staff_present_display = 41;
            $staff_total_display = 52;
            $staff_pct_display = 78.8;

            // Demo Data for Bar Chart
            $current_month_days = [];
            $days_collection = [];
            $days_expense = [];
            for ($i = 1; $i <= 30; $i++) {
                $current_month_days[] = $i;
                $days_collection[] = rand(5000, 25000);
                $days_expense[] = rand(2000, 15000);
            }

            // Demo Data for Donut Chart
            $incomegraph = [
                ['income_category' => 'Tuition Fees', 'total' => 45000],
                ['income_category' => 'Transport Fees', 'total' => 12000],
                ['income_category' => 'Hostel Fees', 'total' => 8500],
                ['income_category' => 'Library Fines', 'total' => 1200],
                ['income_category' => 'Donations', 'total' => 5000]
            ];

            $total_income = 0;
            foreach ($incomegraph as $inc_value) {
                $total_income += $inc_value['total'];
            }

            // Demo Data for Line Chart (Expenses vs Collection)
            $yearly_collection = [];
            $yearly_expense = [];
            $total_month = ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar'];
            for ($i = 0; $i < 12; $i++) {
                // Generate sinusoidal/wavy dummy data for natural look
                $yearly_collection[] = 40000 + (sin($i) * 15000) + rand(-2000, 5000);
                $yearly_expense[] = 30000 + (cos($i) * 10000) + rand(-2000, 5000);
            }
            $line_chart = true; // Force show line chart

            // Demo Data for Expense Donut
            $expensegraph = [
                ['exp_category' => 'Salaries', 'total' => 150000],
                ['exp_category' => 'Maintenance', 'total' => 45000],
                ['exp_category' => 'Utilities', 'total' => 25000],
                ['exp_category' => 'Events', 'total' => 15000],
                ['exp_category' => 'Misc', 'total' => 5000]
            ];
        }
        ?>

        <div class="row dashboard-top-cards-row">
            <?php
            if ($this->module_lib->hasActive('fees_collection')) {
                if ($this->rbac->hasPrivilege('fees_awaiting_payment_widegts', 'can_view')) {
            ?>
                    <div class="<?php echo $std_graphclass; ?>">
                        <div class="topprograssstart top-summary-card" style="--pct: <?php echo (float)$fees_pct_display; ?>;" data-card="fees">
                            <div class="top-summary-card__title"><?php echo $this->lang->line('fees_awaiting_payment'); ?></div>
                            <div class="top-summary-card__corner-icon" aria-hidden="true"><i class="ri-wallet-3-line"></i></div>
                            <div class="top-summary-card__ring" aria-hidden="true">
                                <div class="top-summary-card__ring-inner">
                                    <div class="top-summary-card__ring-value"><?php echo $fees_paid_display; ?></div>
                                    <div class="top-summary-card__ring-sub">/<?php echo $fees_total_display; ?></div>
                                </div>
                            </div>
                            <div class="top-summary-card__footer"><span class="top-summary-card__dot"></span>Pending Action</div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
            <?php
                }
            }
            ?>


            <?php
            if ($this->module_lib->hasActive('front_office')) {
                if ($this->rbac->hasPrivilege('conveted_leads_widegts', 'can_view')) {
            ?>
                    <div class="<?php echo $std_graphclass; ?>">
                        <div class="topprograssstart top-summary-card" style="--pct: <?php echo (float)$leads_pct_display; ?>;" data-card="leads">
                            <div class="top-summary-card__title"><?php echo $this->lang->line('converted_leads'); ?></div>
                            <div class="top-summary-card__corner-icon" aria-hidden="true"><i class="ri-user-add-line"></i></div>
                            <div class="top-summary-card__ring" aria-hidden="true">
                                <div class="top-summary-card__ring-inner">
                                    <div class="top-summary-card__ring-value"><?php echo $leads_done_display; ?></div>
                                    <div class="top-summary-card__ring-sub">/<?php echo $leads_total_display; ?></div>
                                </div>
                            </div>
                            <div class="top-summary-card__footer"><span class="top-summary-card__dot"></span>Success Rate</div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
                <?php
                }
            }
            if ($this->rbac->hasPrivilege('staff_present_today_widegts', 'can_view')) {
                ?>
                <div class="<?php echo $std_graphclass; ?>">
                    <div class="topprograssstart top-summary-card" style="--pct: <?php echo (float)$staff_pct_display; ?>;" data-card="staff">
                        <div class="top-summary-card__title"><?php echo $this->lang->line('staff_present_today'); ?></div>
                        <div class="top-summary-card__corner-icon" aria-hidden="true"><i class="ri-calendar-check-line"></i></div>
                        <div class="top-summary-card__ring" aria-hidden="true">
                            <div class="top-summary-card__ring-inner">
                                <div class="top-summary-card__ring-value"><?php echo $staff_present_display; ?></div>
                                <div class="top-summary-card__ring-sub">/<?php echo $staff_total_display; ?></div>
                            </div>
                        </div>
                        <div class="top-summary-card__footer"><span class="top-summary-card__dot"></span>Attendance</div>
                    </div><!--./topprograssstart-->
                </div><!--./col-md-3-->
                <?php
            }
            if ($this->module_lib->hasActive('student_attendance') && $sch_setting->attendence_type == 0) {
                if ($this->rbac->hasPrivilege('student_present_today_widegts', 'can_view')) {
                    $student_present_today_count = 0 + $attendence_data['total_half_day'] + $attendence_data['total_late'] + $attendence_data['total_present'];
                    $student_present_today_pct = 0;
                    if ($total_students > 0) {
                        $student_present_today_pct = ($student_present_today_count / $total_students) * 100;
                    }
                ?>
                    <div class="<?php echo $std_graphclass; ?>">
                        <div class="topprograssstart top-summary-card" style="--pct: <?php echo (float)$student_present_today_pct; ?>;" data-card="student">
                            <div class="top-summary-card__title"><?php echo $this->lang->line('student_present_today'); ?></div>
                            <div class="top-summary-card__corner-icon" aria-hidden="true"><i class="ri-graduation-cap-line"></i></div>
                            <div class="top-summary-card__ring" aria-hidden="true">
                                <div class="top-summary-card__ring-inner">
                                    <div class="top-summary-card__ring-value"><?php echo $student_present_today_count; ?></div>
                                    <div class="top-summary-card__ring-sub">/<?php echo $total_students; ?></div>
                                </div>
                            </div>
                            <div class="top-summary-card__footer"><span class="top-summary-card__dot"></span>Attendance</div>
                        </div><!--./topprograssstart-->
                    </div><!--./col-md-3-->
            <?php }
            }
            ?>
        </div><!--./row-->

        <!-- Date Filter Section + Financial Summary Cards (combined row) -->
        <?php if (($this->module_lib->hasActive('income')) || ($this->module_lib->hasActive('expense'))) { ?>
            <div class="row dashboard-filter-summary-row">
                <div class="col-lg-4 col-md-12">
                    <div class="date-filter-section date-filter-card">
                        <div class="date-filter-card__title"><i class="ri-filter-3-line" aria-hidden="true"></i> FILTER DASHBOARD</div>
                        <div class="filter-controls">
                            <div class="filter-group">
                                <label for="filter_type">Select Period</label>
                                <select id="filter_type" class="form-control">
                                    <option value="current">Current Month</option>
                                    <option value="today">Today</option>
                                    <option value="weekly">Weekly (Last 7 Days)</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>

                            <div class="filter-group" id="monthly_filter" style="display: none;">
                                <label for="month_select">Month</label>
                                <select id="month_select" class="form-control">
                                    <option value="1">January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                                <select id="year_select" class="form-control">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025" selected>2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>

                            <div class="filter-group" id="yearly_filter" style="display: none;">
                                <label for="year_only_select">Year</label>
                                <select id="year_only_select" class="form-control">
                                    <option value="2023">2023</option>
                                    <option value="2024">2024</option>
                                    <option value="2025" selected>2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>

                            <div class="filter-group" id="custom_filter" style="display: none;">
                                <label for="start_date">From</label>
                                <input type="date" id="start_date" class="form-control" value="<?php echo date('Y-m-01'); ?>">
                                <label for="end_date">To</label>
                                <input type="date" id="end_date" class="form-control" value="<?php echo date('Y-m-t'); ?>">
                            </div>

                            <button type="button" id="apply_filter" class="btn-apply-filter">
                                <i class="fa fa-refresh"></i> Apply Filter
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8 col-md-12" id="summary_cards">
                    <div class="row dashboard-summary-cards-row">
                        <!-- Total Income Card -->
                        <?php if ($can_view_income) { ?>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box hover-expand-effect dashboard-summary-card dashboard-summary-card--income">
                                    <div class="dashboard-summary-card__left">
                                        <div class="dashboard-summary-card__title-row">
                                            <span class="info-box-icon" aria-hidden="true">
                                                <i class="ri-money-rupee-circle-line"></i>
                                            </span>
                                            <span class="info-box-text">Total Income</span>
                                        </div>
                                        <span class="info-box-number" id="total_income_display">
                                            <?php echo $currency_symbol . number_format($total_income, 2); ?>
                                        </span>
                                        <span class="progress-description" id="income_period">
                                            <?php echo $current_month; ?>
                                        </span>
                                    </div>
                                    <div class="dashboard-summary-card__right" aria-hidden="true">
                                        <div class="dashboard-income-trend">
                                            <svg class="dashboard-income-trend__svg" viewBox="0 0 64 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 22 L20 18 L30 20 L42 10 L58 6" stroke="#22C55E" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
                                                <circle cx="58" cy="6" r="2.6" fill="#22C55E" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Fee Collection Card -->
                        <?php if ($can_view_fees) { ?>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                <div class="info-box hover-expand-effect dashboard-summary-card dashboard-summary-card--fees">
                                    <div class="dashboard-summary-card__left">
                                        <div class="dashboard-summary-card__title-row">
                                            <span class="info-box-icon" aria-hidden="true">
                                                <i class="ri-bank-card-line"></i>
                                            </span>
                                            <span class="info-box-text">Fee Collection</span>
                                        </div>
                                        <span class="info-box-number" id="total_fee_collection_display">
                                            <?php echo $currency_symbol . number_format($total_fee_collection, 2); ?>
                                        </span>
                                        <span class="progress-description" id="fee_period">
                                            <?php echo $current_month; ?>
                                        </span>
                                    </div>
                                    <div class="dashboard-summary-card__right" aria-hidden="true">
                                        <div class="dashboard-fee-mini-panel">
                                            <div class="dashboard-fee-mini-bars">
                                                <span class="dashboard-fee-mini-bar"></span>
                                                <span class="dashboard-fee-mini-bar"></span>
                                                <span class="dashboard-fee-mini-bar"></span>
                                                <span class="dashboard-fee-mini-bar"></span>
                                                <span class="dashboard-fee-mini-bar"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="row dashboard-chart-row dashboard-chart-row--monthly">
            <?php
            $bar_chart = true;

            if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense'))) {
                if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) {

                    $div_rol  = 3;
                    $userdata = $this->customlib->getUserData();
            ?>
                    <div class="col-lg-7 col-md-7 col-sm-12 col60">
                        <div class="box box-primary borderwhite fees-chart-card dashboard-chart-card dashboard-chart-card--bar">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('fees_collection_expenses_for'); ?> <?php echo $this->lang->line(strtolower(date('F'))) . " " . date('Y');

                                                                                                                        ?></h3>

                            </div>
                            <div class="box-body">
                                <div class="chart dashboard-chart-canvas" data-chart="bar">
                                    <div class="dashboard-chart-empty" id="dashboard_empty_barChart" style="display:none;">
                                        <div class="dashboard-chart-empty__inner">
                                            <div class="dashboard-chart-empty__art" aria-hidden="true">
                                                <svg class="dashboard-empty-bars" viewBox="0 0 320 140" xmlns="http://www.w3.org/2000/svg">
                                                    <defs>
                                                        <linearGradient id="dashEmptyBarGrad" x1="0" y1="0" x2="1" y2="1">
                                                            <stop offset="0" stop-color="#4f46e5" stop-opacity="0.22" />
                                                            <stop offset="1" stop-color="#7c3aed" stop-opacity="0.10" />
                                                        </linearGradient>
                                                        <linearGradient id="dashEmptyBarGrad2" x1="0" y1="0" x2="0" y2="1">
                                                            <stop offset="0" stop-color="#22c55e" stop-opacity="0.22" />
                                                            <stop offset="1" stop-color="#06b6d4" stop-opacity="0.10" />
                                                        </linearGradient>
                                                    </defs>
                                                    <rect x="1" y="1" width="318" height="138" rx="18" fill="url(#dashEmptyBarGrad)" />
                                                    <g opacity="0.9">
                                                        <rect x="52" y="70" width="16" height="44" rx="8" fill="#4f46e5" opacity="0.35" />
                                                        <rect x="76" y="52" width="16" height="62" rx="8" fill="#4f46e5" opacity="0.24" />
                                                        <rect x="100" y="78" width="16" height="36" rx="8" fill="#4f46e5" opacity="0.18" />
                                                        <rect x="144" y="60" width="16" height="54" rx="8" fill="#22c55e" opacity="0.22" />
                                                        <rect x="168" y="44" width="16" height="70" rx="8" fill="#22c55e" opacity="0.16" />
                                                        <rect x="192" y="72" width="16" height="42" rx="8" fill="#22c55e" opacity="0.12" />
                                                        <rect x="236" y="66" width="16" height="48" rx="8" fill="#f97316" opacity="0.16" />
                                                        <rect x="260" y="54" width="16" height="60" rx="8" fill="#f97316" opacity="0.10" />
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="dashboard-chart-empty__title"><?php echo $this->lang->line('no_data_found'); ?></div>
                                            <div class="dashboard-chart-empty__desc">No monthly activity is available yet for this period. Once fees collection or expenses are recorded, you’ll see a daily breakdown here.</div>
                                            <div class="dashboard-chart-empty__chips" aria-hidden="true">
                                                <span class="dashboard-chart-chip"><i class="ri-calendar-line"></i> Try another month</span>
                                                <span class="dashboard-chart-chip"><i class="ri-filter-3-line"></i> Check filters</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="chart-scroll-wrapper">
                                        <canvas id="barChart" height="300"></canvas>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div><!--./col-lg-7-->
            <?php }
            }
            ?>
            <?php
            if ($this->module_lib->hasActive('income')) {
                if ($this->rbac->hasPrivilege('income_donut_graph', 'can_view')) {
            ?>
                    <div class="col-lg-5 col-md-5 col-sm-12 col40">
                        <div class="box box-primary borderwhite fees-chart-card dashboard-chart-card dashboard-chart-card--donut">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('income') . " - " . $this->lang->line(strtolower(date('F'))) . " " . date('Y');  ?></h3>
                            </div>
                            <div class="box-body">
                                <div class="chart-responsive dashboard-chart-canvas" data-chart="donut-income">
                                    <div class="dashboard-chart-empty" id="dashboard_empty_doughnut_income" style="display:none;">
                                        <div class="dashboard-chart-empty__inner">
                                            <div class="dashboard-chart-empty__ring" aria-hidden="true">
                                                <svg class="dashboard-empty-ring" viewBox="0 0 220 220" xmlns="http://www.w3.org/2000/svg">
                                                    <defs>
                                                        <filter id="dashRingShadow" x="-20%" y="-20%" width="140%" height="140%">
                                                            <feDropShadow dx="0" dy="4" stdDeviation="6" flood-color="#0f172a" flood-opacity="0.08" />
                                                        </filter>
                                                    </defs>
                                                    <!-- Dashed line for empty portion (connecting lines style) -->
                                                    <circle cx="110" cy="110" r="82" fill="none" stroke="#d1d5db" stroke-width="2.5" stroke-dasharray="6 6" opacity="0.6" stroke-linecap="round" />
                                                    <!-- Colorful donut segments (bottom-right half) -->
                                                    <circle cx="110" cy="110" r="82" fill="none" stroke="#ec4899" stroke-width="16" stroke-linecap="round" stroke-dasharray="64 451" transform="rotate(225 110 110)" filter="url(#dashRingShadow)" />
                                                    <circle cx="110" cy="110" r="82" fill="none" stroke="#60a5fa" stroke-width="16" stroke-linecap="round" stroke-dasharray="64 451" transform="rotate(270 110 110)" filter="url(#dashRingShadow)" />
                                                    <circle cx="110" cy="110" r="82" fill="none" stroke="#fbbf24" stroke-width="16" stroke-linecap="round" stroke-dasharray="64 451" transform="rotate(315 110 110)" filter="url(#dashRingShadow)" />
                                                    <circle cx="110" cy="110" r="82" fill="none" stroke="#a78bfa" stroke-width="16" stroke-linecap="round" stroke-dasharray="64 451" transform="rotate(0 110 110)" filter="url(#dashRingShadow)" />
                                                    <!-- Center white circle -->
                                                    <circle cx="110" cy="110" r="62" fill="#ffffff" />
                                                    <!-- Icon circle background -->
                                                    <circle cx="110" cy="100" r="16" fill="#ede9fe" />
                                                    <!-- Trending chart icon (replacing plus) -->
                                                    <path d="M103 104 L108 100 L113 103 L117 96" stroke="#7c3aed" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none" opacity="0.9" />
                                                    <circle cx="117" cy="96" r="1.5" fill="#7c3aed" opacity="0.9" />
                                                    <!-- Text inside circle -->
                                                    <text x="110" y="128" text-anchor="middle" font-size="11" font-weight="700" fill="#0f172a" letter-spacing="-0.2">Awaiting your</text>
                                                    <text x="110" y="142" text-anchor="middle" font-size="11" font-weight="700" fill="#0f172a" letter-spacing="-0.2">income data!</text>
                                                </svg>
                                            </div>
                                            <div class="dashboard-chart-empty__desc">This chart will show how your income is distributed across categories for the selected period.</div>
                                            <div class="dashboard-chart-empty__chips" aria-hidden="true">
                                                <span class="dashboard-chart-chip"><i class="ri-shield-check-line"></i> Safe &amp; private</span>
                                                <span class="dashboard-chart-chip"><i class="ri-time-line"></i> Updates instantly</span>
                                            </div>
                                        </div>
                                    </div>
                                    <canvas id="doughnut-chart" class="" height="320"></canvas>
                                    <div class="donut-inner-text">
                                        <div class="donut-inner-text__label">Total Income</div>
                                        <div class="donut-inner-text__value" id="donut_total_val"><?php echo $currency_symbol . number_format($total_income, 2); ?></div>
                                    </div>
                                </div>

                            </div>
                        </div><!--./col-md-6-->
                    </div><!--./col-lg-5-->
            <?php
                }
            }
            ?>
        </div><!--./row-->

        <div class="row dashboard-chart-row dashboard-chart-row--session">
            <?php
            $line_chart = true;
            if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense'))) {
                if ($this->rbac->hasPrivilege('fees_collection_and_expense_yearly_chart', 'can_view')) {
                    $div_rol = 3;
            ?>
                    <div class="col-lg-7 col-md-7 col-sm-12 col60">
                        <div class="box box-info borderwhite fees-chart-card dashboard-chart-card--line">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('fees_collection_expenses_for_session'); ?> <?php echo $this->setting_model->getCurrentSessionName(); ?></h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                    <button class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart-responsive dashboard-chart-canvas">
                                    <canvas id="lineChart" height="95"></canvas>
                                    <div id="dashboard_empty_lineChart" class="dashboard-chart-empty" style="display: none;">
                                        <div class="dashboard-chart-empty-state">
                                            <div class="dashboard-chart-empty-state__image-container dashboard-chart-empty-state__image-container--growth">
                                                <img src="<?php echo base_url(); ?>backend/images/line-chart.gif" alt="No Data" class="dashboard-chart-empty-state__image">
                                            </div>
                                            <h3 class="dashboard-chart-empty-state__title"><?php echo $this->lang->line('no_record_found'); ?></h3>
                                            <p class="dashboard-chart-empty-state__description">
                                                It looks quiet in here. Start recording fees and expenses to see the trends!
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--./col-lg-7-->
                <?php
                }
            }
            if ($this->module_lib->hasActive('expense')) {
                ?>
                <?php if ($this->rbac->hasPrivilege('expense_donut_graph', 'can_view')) {
                ?>
                    <div class="col-lg-5 col-md-5 col-sm-12 col40">
                        <div class="box box-primary borderwhite fees-chart-card dashboard-chart-card--donut-expense">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo $this->lang->line('expense') . " - " . $this->lang->line(strtolower(date('F'))) . " " . date('Y');  ?></h3>
                            </div><!--./info-box-->
                            <div class="box-body">
                                <div class="chart-responsive dashboard-chart-canvas">
                                    <canvas id="doughnut-chart1" class=""></canvas>
                                    <div id="dashboard_empty_doughnut-chart1" class="dashboard-chart-empty" style="display: none;">
                                        <div class="dashboard-chart-empty-state">
                                            <div class="dashboard-chart-empty-state__image-container">
                                                <img src="<?php echo base_url(); ?>backend/images/money-bag.gif" alt="No Data" class="dashboard-chart-empty-state__image">
                                            </div>
                                            <h3 class="dashboard-chart-empty-state__title">No Expenses Recorded</h3>
                                            <p class="dashboard-chart-empty-state__description">
                                                Start tracking your spending for <?php echo date('F'); ?> to see insights here.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--./col-lg-5-->
            <?php }
            }
            ?>
        </div><!--./row-->
        <div class="row overview-cards-row">

            <?php
            if ($this->module_lib->hasActive('fees_collection')) {
                if ($this->rbac->hasPrivilege('fees_overview_widegts', 'can_view')) {
            ?>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="overview-card fees-premium-card">
                            <div class="fees-header">
                                <div class="fees-header-icon">
                                    <i class="fa fa-credit-card"></i>
                                </div>
                                <div class="fees-header-text">
                                    <div class="fees-premium-card__header">
                                        <h5 class="fees-premium-card__title"><?php echo $this->lang->line('fees_overview'); ?> •</h5>
                                        <span class="fees-premium-card__session-pill">AY <?php echo $this->setting_model->getCurrentSessionName(); ?></span>
                                    </div>
                                    <span class="fees-premium-card__label">SESSION OVERVIEW</span>
                                </div>
                            </div>

                            <div class="fees-premium-card__main-metrics">
                                <div class="fees-premium-card__metric">
                                    <h2 class="fees-premium-card__total-due">
                                        <?php
                                        $total_due_amt = 0;
                                        if (!empty($fees_awaiting)) {
                                            foreach ($fees_awaiting as $f) {
                                                if (isset($f->is_system)) {
                                                    $total_due_amt += ($f->is_system) ? $f->amount : $f->fee_amount;
                                                }
                                            }
                                        }
                                        echo $currency_symbol . (($total_due_amt >= 1000) ? (round($total_due_amt / 1000) . 'k') : amountFormat($total_due_amt));
                                        ?>
                                    </h2>
                                    <span class="fees-premium-card__label">Total Due</span>
                                </div>
                                <div class="fees-premium-card__total-students">
                                    <span class="fees-premium-card__count"><?php echo $total_students; ?></span>
                                    <span class="fees-premium-card__label">Total Students</span>
                                </div>
                            </div>

                            <div class="fees-orb-wrapper">
                                <div class="fees-wavy-orb"></div>
                            </div>

                            <div class="fees-status-list">
                                <!-- UNPAID -->
                                <div class="fees-status-item">
                                    <div class="fees-status-info">
                                        <div class="fees-status-indicator fees-status-indicator--unpaid"></div>
                                        <div class="fees-status-labels">
                                            <span class="status-name"><?php echo $this->lang->line('unpaid'); ?></span>
                                            <span class="status-detail"><?php echo $fees_overview['total_unpaid']; ?> Students</span>
                                        </div>
                                    </div>
                                    <div class="fees-status-values">
                                        <span class="status-percent"><?php echo round($fees_overview['unpaid_progress']); ?>%</span>
                                    </div>
                                </div>
                                <!-- PARTIAL -->
                                <div class="fees-status-item">
                                    <div class="fees-status-info">
                                        <div class="fees-status-indicator fees-status-indicator--partial"></div>
                                        <div class="fees-status-labels">
                                            <span class="status-name"><?php echo $this->lang->line('partial'); ?></span>
                                            <span class="status-detail"><?php echo $fees_overview['total_partial']; ?> Students</span>
                                        </div>
                                    </div>
                                    <div class="fees-status-values">
                                        <span class="status-percent"><?php echo round($fees_overview['partial_progress']); ?>%</span>
                                    </div>
                                </div>
                                <!-- PAID -->
                                <div class="fees-status-item">
                                    <div class="fees-status-info">
                                        <div class="fees-status-indicator fees-status-indicator--paid"></div>
                                        <div class="fees-status-labels">
                                            <span class="status-name"><?php echo $this->lang->line('paid'); ?></span>
                                            <span class="status-detail"><?php echo $fees_overview['total_paid']; ?> Students</span>
                                        </div>
                                    </div>
                                    <div class="fees-status-values">
                                        <span class="status-percent"><?php echo round($fees_overview['paid_progress']); ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div><!--./overview-card-->
                    </div><!--./col-md-3-->
                <?php
                }
            }
            if ($this->module_lib->hasActive('front_office')) {
                if ($this->rbac->hasPrivilege('enquiry_overview_widegts', 'can_view')) {
                ?>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="overview-card enquiry-premium-card">
                            <div class="enq-header">
                                <div class="enq-header-icon">
                                    <i class="fa fa-comments"></i>
                                </div>
                                <div class="enq-header-text">
                                    <h5 class="fees-premium-card__title">Enquiry Board •</h5>
                                    <span class="fees-premium-card__label">LIVE STATUS OVERVIEW</span>
                                </div>
                            </div>

                            <div class="enquiry-grid">
                                <!-- ACTIVE -->
                                <div class="enquiry-status-box status-box--active">
                                    <div class="status-box-header">
                                        <span class="status-box-title">Active</span>
                                        <i class="fa fa-comments-o status-box-icon"></i>
                                    </div>
                                    <div class="status-box-count"><?php echo $enquiry_overview['active']; ?></div>
                                    <div class="status-progress-row">
                                        <div class="status-progress-bg">
                                            <div class="status-accent-bar" style="width: <?php echo $enquiry_overview['active_progress']; ?>%"></div>
                                        </div>
                                        <span class="status-box-percent"><?php echo round($enquiry_overview['active_progress']); ?>%</span>
                                    </div>
                                </div>
                                <!-- WON -->
                                <div class="enquiry-status-box status-box--won">
                                    <div class="status-box-header">
                                        <span class="status-box-title">Won</span>
                                        <i class="fa fa-star status-box-icon"></i>
                                    </div>
                                    <div class="status-box-count"><?php echo $enquiry_overview['won']; ?></div>
                                    <div class="status-progress-row">
                                        <div class="status-progress-bg">
                                            <div class="status-accent-bar" style="width: <?php echo $enquiry_overview['won_progress']; ?>%"></div>
                                        </div>
                                        <span class="status-box-percent"><?php echo round($enquiry_overview['won_progress']); ?>%</span>
                                    </div>
                                </div>
                                <!-- PASSIVE -->
                                <div class="enquiry-status-box status-box--passive">
                                    <div class="status-box-header">
                                        <span class="status-box-title">Passive</span>
                                        <i class="fa fa-clock-o status-box-icon"></i>
                                    </div>
                                    <div class="status-box-count"><?php echo $enquiry_overview['passive']; ?></div>
                                    <div class="status-progress-row">
                                        <div class="status-progress-bg">
                                            <div class="status-accent-bar" style="width: <?php echo $enquiry_overview['passive_progress']; ?>%"></div>
                                        </div>
                                        <span class="status-box-percent"><?php echo round($enquiry_overview['passive_progress']); ?>%</span>
                                    </div>
                                </div>
                                <!-- LOST -->
                                <div class="enquiry-status-box status-box--lost">
                                    <div class="status-box-header">
                                        <span class="status-box-title">Lost</span>
                                        <i class="fa fa-heart-o status-box-icon"></i>
                                    </div>
                                    <div class="status-box-count"><?php echo $enquiry_overview['lost']; ?></div>
                                    <div class="status-progress-row">
                                        <div class="status-progress-bg">
                                            <div class="status-accent-bar" style="width: <?php echo $enquiry_overview['lost_progress']; ?>%"></div>
                                        </div>
                                        <span class="status-box-percent"><?php echo round($enquiry_overview['lost_progress']); ?>%</span>
                                    </div>
                                </div>
                                <!-- DEAD -->
                                <div class="enquiry-status-box status-box--dead">
                                    <div class="status-box-header">
                                        <span class="status-box-title">Dead Leads</span>
                                        <i class="fa fa-user-times status-box-icon"></i>
                                    </div>
                                    <div class="status-box-count"><?php echo $enquiry_overview['dead']; ?></div>
                                    <div class="status-progress-row">
                                        <div class="status-progress-bg">
                                            <div class="status-accent-bar" style="width: <?php echo $enquiry_overview['dead_progress']; ?>%"></div>
                                        </div>
                                        <span class="status-box-percent"><?php echo round($enquiry_overview['dead_progress']); ?>%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="enquiry-footer">
                                <div class="enquiry-footer-left">
                                    <span class="total-enquiries-label">Total Enquiries</span>
                                    <span class="total-enquiries-value"><?php echo $total_enquiry; ?></span>
                                </div>
                                <a href="<?php echo site_url('admin/enquiry'); ?>" class="view-analytics-link">View Analytics <i class="fa fa-arrow-right"></i></a>
                            </div>
                        </div><!--./overview-card-->
                    </div><!--./col-md-3-->
                <?php
                }
            }

            if ($this->module_lib->hasActive('library')) {
                if ($this->rbac->hasPrivilege('book_overview_widegts', 'can_view')) {
                ?>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="overview-card library-premium-card">
                            <div class="lib-header">
                                <div class="lib-header-icon">
                                    <i class="fa fa-book"></i>
                                </div>
                                <h5 class="fees-premium-card__title" style="margin:0"><?php echo $this->lang->line('library_overview'); ?> •</h5>
                            </div>

                            <div class="lib-tiles-grid">
                                <!-- DUE FOR RETURN -->
                                <div class="lib-tile lib-tile--orange">
                                    <span class="lib-tile-name">Due for Return</span>
                                    <div class="lib-tile-content">
                                        <i class="fa fa-clock-o lib-tile-icon"></i>
                                        <span class="lib-tile-value"><?php echo $book_overview['dueforreturn']; ?></span>
                                    </div>
                                </div>
                                <!-- RETURNED -->
                                <div class="lib-tile lib-tile--blue">
                                    <span class="lib-tile-name">Returned</span>
                                    <div class="lib-tile-content">
                                        <i class="fa fa-check-circle lib-tile-icon"></i>
                                        <span class="lib-tile-value"><?php echo $book_overview['forreturn']; ?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="lib-progress-row">
                                <!-- ISSUED CIRCLE -->
                                <div class="lib-circle-container">
                                    <div class="lib-circle-wrapper">
                                        <svg class="lib-circle-svg" viewBox="0 0 80 80">
                                            <circle class="lib-circle-bg" cx="40" cy="40" r="35" />
                                            <?php
                                            $issued_p = $book_overview['issued_progress'];
                                            $offset = 219.9 - (219.9 * ($issued_p / 100));
                                            ?>
                                            <circle class="lib-circle-fill" cx="40" cy="40" r="35" stroke-dasharray="219.9" stroke-dashoffset="<?php echo $offset; ?>" />
                                            <circle class="lib-circle-dot" cx="40" cy="5" r="3" />
                                        </svg>
                                        <div class="lib-circle-info">
                                            <span class="lib-circle-percent"><?php echo round($issued_p); ?>%</span>
                                        </div>
                                    </div>
                                    <div class="lib-circle-labels text-center" style="margin-top: 5px;">
                                        <span class="lib-circle-label" style="display:block">Issued</span>
                                        <span class="lib-circle-sub">OUT OF <?php echo $book_overview['total']; ?></span>
                                    </div>
                                </div>

                                <!-- AVAILABLE CIRCLE -->
                                <div class="lib-circle-container">
                                    <div class="lib-circle-wrapper">
                                        <svg class="lib-circle-svg" viewBox="0 0 80 80">
                                            <circle class="lib-circle-bg" cx="40" cy="40" r="35" />
                                            <?php
                                            $avail_p = $book_overview['availble_progress'];
                                            $offset_v = 219.9 - (219.9 * ($avail_p / 100));
                                            ?>
                                            <circle class="lib-circle-fill" cx="40" cy="40" r="35" stroke-dasharray="219.9" stroke-dashoffset="<?php echo $offset_v; ?>" style="stroke: #10b981;" />
                                            <circle class="lib-circle-dot" cx="40" cy="5" r="3" style="fill: #10b981;" />
                                        </svg>
                                        <div class="lib-circle-info">
                                            <span class="lib-circle-percent"><?php echo round($avail_p); ?>%</span>
                                        </div>
                                    </div>
                                    <div class="lib-circle-labels text-center" style="margin-top: 5px;">
                                        <span class="lib-circle-label" style="display:block">Available</span>
                                        <span class="lib-circle-sub">OUT OF <?php echo $book_overview['total']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div><!--./overview-card-->
                    </div><!--./col-md-3-->
                <?php
                }
            }
            if ($this->module_lib->hasActive('student_attendance')) {
                if ($this->rbac->hasPrivilege('today_attendance_widegts', 'can_view')) {
                ?>
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <div class="overview-card attendance-premium-card">
                            <div class="att-header">
                                <div class="att-header-icon">
                                    <i class="fa fa-graduation-cap"></i>
                                </div>
                                <div class="att-header-text">
                                    <h5 class="fees-premium-card__title"><?php echo $this->lang->line('student_today_attendance'); ?> •</h5>
                                    <span class="att-header-subtitle">Today's Overview</span>
                                </div>
                            </div>

                            <div class="att-total-row">
                                <span class="att-total-label">Total Students</span>
                                <span class="att-total-value"><?php echo number_format($total_students); ?></span>
                            </div>

                            <div class="att-stacked-bar">
                                <div class="att-segment att-segment--present" style="width: <?php echo !empty($attendence_data['present']) ? $attendence_data['present'] : '0%'; ?>"></div>
                                <div class="att-segment att-segment--late" style="width: <?php echo !empty($attendence_data['late']) ? $attendence_data['late'] : '0%'; ?>"></div>
                                <div class="att-segment att-segment--absent" style="width: <?php echo !empty($attendence_data['absent']) ? $attendence_data['absent'] : '0%'; ?>"></div>
                                <div class="att-segment att-segment--halfday" style="width: <?php echo !empty($attendence_data['half_day']) ? $attendence_data['half_day'] : '0%'; ?>"></div>
                            </div>

                            <div class="att-grid">
                                <!-- PRESENT -->
                                <div class="att-box">
                                    <div class="att-box-dot att-box-dot--present"></div>
                                    <span class="att-box-label">PRESENT</span>
                                    <div class="att-box-content">
                                        <span class="att-box-count"><?php echo !empty($attendence_data['total_present']) ? $attendence_data['total_present'] : 0; ?></span>
                                        <span class="att-box-percent att-box-percent--present"><?php echo !empty($attendence_data['present']) ? $attendence_data['present'] : '0%'; ?></span>
                                    </div>
                                </div>
                                <!-- LATE -->
                                <div class="att-box">
                                    <div class="att-box-dot att-box-dot--late"></div>
                                    <span class="att-box-label">LATE</span>
                                    <div class="att-box-content">
                                        <span class="att-box-count"><?php echo !empty($attendence_data['total_late']) ? $attendence_data['total_late'] : 0; ?></span>
                                        <span class="att-box-percent att-box-percent--late"><?php echo !empty($attendence_data['late']) ? $attendence_data['late'] : '0%'; ?></span>
                                    </div>
                                </div>
                                <!-- ABSENT -->
                                <div class="att-box">
                                    <div class="att-box-dot att-box-dot--absent"></div>
                                    <span class="att-box-label">ABSENT</span>
                                    <div class="att-box-content">
                                        <span class="att-box-count"><?php echo !empty($attendence_data['total_absent']) ? $attendence_data['total_absent'] : 0; ?></span>
                                        <span class="att-box-percent att-box-percent--absent"><?php echo !empty($attendence_data['absent']) ? $attendence_data['absent'] : '0%'; ?></span>
                                    </div>
                                </div>
                                <!-- HALF DAY -->
                                <div class="att-box">
                                    <div class="att-box-dot att-box-dot--halfday"></div>
                                    <span class="att-box-label">HALF DAY</span>
                                    <div class="att-box-content">
                                        <span class="att-box-count"><?php echo !empty($attendence_data['total_half_day']) ? $attendence_data['total_half_day'] : 0; ?></span>
                                        <span class="att-box-percent att-box-percent--halfday"><?php echo !empty($attendence_data['half_day']) ? $attendence_data['half_day'] : '0%'; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div><!--./overview-card-->
                    </div><!--./col-md-3-->
            <?php
                }
            }

            $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

            $div_col    = 12;
            $div_rol    = 12;
            $bar_chart  = true;
            $line_chart = true;
            if ($this->rbac->hasPrivilege('staff_role_count_widget', 'can_view')) {
                $div_col = 9;
                $div_rol = 12;
            }

            $widget_col = array();
            if ($this->rbac->hasPrivilege('Monthly fees_collection_widget', 'can_view')) {
                $widget_col[0] = 1;
                $div_rol       = 3;
            }

            if ($this->rbac->hasPrivilege('monthly_expense_widget', 'can_view')) {
                $widget_col[1] = 2;
                $div_rol       = 3;
            }

            if ($this->rbac->hasPrivilege('student_count_widget', 'can_view')) {
                $widget_col[2] = 3;
                $div_rol       = 3;
            }
            $div = sizeof($widget_col);
            if (!empty($widget_col)) {
                $widget = 3;
            } else {

                $widget = 12;
            }
            ?>
            <div class="row dashboard-bottom-section">
                <!-- Sidebar Column: Monthly Widgets -->
                <div class="col-md-3 col-sm-12 dashboard-sidebar-outer">
                    <div class="dashboard-sidebar-inner">
                        <div class="sidebar-section-header">
                            <span class="sidebar-section-title">OVERVIEW</span>
                        </div>

                        <div class="dashboard-sidebar-widgets">
                            <?php
                            if ($this->module_lib->hasActive('fees_collection')) {
                                if ($this->rbac->hasPrivilege('Monthly fees_collection_widget', 'can_view')) {
                            ?>
                                    <div class="sidebar-stat-card fees-stat">
                                        <a href="<?php echo site_url('studentfee') ?>">
                                            <div class="stat-card-inner">
                                                <div class="stat-icon-box bg-green-soft">
                                                    <i class="ri-coin-line"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label"><?php echo $this->lang->line('monthly_fees_collection'); ?></span>
                                                    <h3 class="stat-value text-collection">Collection</h3>

                                                    <!-- Dynamic Progress for Fees -->
                                                    <div class="stat-dynamic-progress">
                                                        <div class="progress-bar-shell">
                                                            <div class="progress-bar-fill bg-success" style="width: 75%;"></div>
                                                        </div>
                                                        <div class="stat-footer">
                                                            <span class="stat-amount"><?php if ($month_collection) {
                                                                                            echo $currency_symbol . amountFormat($month_collection);
                                                                                        } ?></span>
                                                            <span class="stat-trend trend-up"><i class="ri-line-chart-line"></i> Trending +12%</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="stat-icon-ghost"><i class="ri-coin-line"></i></div>
                                            </div>
                                        </a>
                                    </div>
                            <?php
                                }
                            }
                            ?>

                            <?php
                            if ($this->module_lib->hasActive('expense')) {
                                if ($this->rbac->hasPrivilege('monthly_expense_widget', 'can_view')) {
                            ?>
                                    <div class="sidebar-stat-card expense-stat">
                                        <a href="<?php echo site_url('admin/expense') ?>">
                                            <div class="stat-card-inner">
                                                <div class="stat-icon-box bg-red-soft">
                                                    <i class="ri-bank-card-line"></i>
                                                </div>
                                                <div class="stat-content">
                                                    <span class="stat-label"><?php echo $this->lang->line('monthly_expenses'); ?></span>
                                                    <h3 class="stat-value text-track">Track</h3>
                                                    <div class="stat-footer">
                                                        <span class="stat-amount"><?php if ($month_expense) {
                                                                                        echo $currency_symbol . amountFormat($month_expense);
                                                                                    } ?></span>
                                                        <span class="stat-badge badge-warning"><i class="ri-error-warning-line"></i> PENDING</span>
                                                    </div>
                                                </div>
                                                <div class="stat-icon-ghost"><i class="ri-bank-card-line"></i></div>
                                            </div>
                                        </a>
                                    </div>
                            <?php
                                }
                            }
                            ?>

                            <?php
                            if ($this->rbac->hasPrivilege('student_count_widget', 'can_view')) {
                            ?>
                                <div class="sidebar-stat-card student-stat">
                                    <a href="<?php echo site_url('student/search') ?>">
                                        <div class="stat-card-inner">
                                            <div class="stat-icon-box bg-blue-soft">
                                                <i class="ri-group-line"></i>
                                            </div>
                                            <div class="stat-content">
                                                <span class="stat-label">Total Students</span>
                                                <h3 class="stat-value"><?php echo $total_students; ?></h3>
                                                <div class="stat-footer">
                                                    <span class="stat-badge badge-success">Active Now</span>
                                                </div>
                                            </div>
                                            <div class="stat-icon-ghost"><i class="ri-group-line"></i></div>
                                        </div>
                                    </a>
                                </div>
                            <?php }
                            ?>

                            <?php if ($this->rbac->hasPrivilege('staff_role_count_widget', 'can_view')) { ?>
                                <div class="sidebar-stat-card operators-stat roles-carousel-box">
                                    <div class="stat-card-inner">
                                        <div class="stat-icon-box bg-yellow-soft">
                                            <i class="ri-admin-line"></i>
                                        </div>
                                        <div class="stat-content">
                                            <span class="stat-label">Operators</span>
                                            <div class="roles-carousel-inner">
                                                <?php foreach ($roles as $key => $value) { ?>
                                                    <div class="role-item">
                                                        <h3 class="stat-value"><?php echo $value; ?></h3>
                                                        <div class="stat-footer">
                                                            <span class="stat-badge badge-info"><?php echo $key; ?></span>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                            <!-- Manual Carousel Triggers -->
                                            <div class="roles-carousel-controls">
                                                <button type="button" class="roles-nav roles-prev"><i class="ri-arrow-left-s-line"></i></button>
                                                <button type="button" class="roles-nav roles-next"><i class="ri-arrow-right-s-line"></i></button>
                                            </div>
                                            <div class="roles-dots"></div>
                                        </div>
                                        <div class="stat-icon-ghost"><i class="ri-admin-line"></i></div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>

                        <div class="dashboard-sidebar-footer">
                            <a href="<?php echo site_url('schsettings') ?>" class="sidebar-footer-link">
                                <div class="footer-link-inner">
                                    <div class="footer-icon-box">
                                        <i class="ri-settings-line"></i>
                                    </div>
                                    <span class="footer-text">System Settings</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Column: Calendar -->
                <div class="col-md-9 col-sm-12">
                    <?php
                    if ($this->module_lib->hasActive('calendar_to_do_list')) {
                        if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_view')) {
                    ?>
                            <div class="calendar-wrapper-premium">
                                <div class="box box-primary borderwhite calendar-premium-box">
                                    <div class="box-body">
                                        <!-- Custom Integrated Header -->
                                        <div class="calendar-custom-header">
                                            <div class="cal-header-left">
                                                <h3 id="cal-title" class="cal-dynamic-title">December 2025</h3>
                                                <div class="cal-nav-group">
                                                    <button type="button" class="cal-btn-nav cal-prev-btn" title="Previous"><i class="ri-arrow-left-s-line"></i></button>
                                                    <button type="button" class="cal-btn-today">Today</button>
                                                    <button type="button" class="cal-btn-nav cal-next-btn" title="Next"><i class="ri-arrow-right-s-line"></i></button>
                                                </div>
                                            </div>
                                            <div class="cal-header-right">
                                                <div class="cal-view-group">
                                                    <button type="button" class="cal-btn-view active" data-view="month">Month</button>
                                                    <button type="button" class="cal-btn-view" data-view="agendaWeek">Week</button>
                                                    <button type="button" class="cal-btn-view" data-view="agendaDay">Day</button>
                                                </div>
                                                <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_add')) { ?>
                                                    <button type="button" class="cal-btn-new-event">
                                                        <i class="ri-add-line"></i> <span>New Event</span>
                                                    </button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <!-- THE CALENDAR -->
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </div>
            </div><!--./row-->
        </div><!--./row-->
</div>
</div>

<script>
    $(document).ready(function() {
        $('#viewEventModal,#newEventModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });

        $('.roles-carousel-box').each(function() {
            var $carousel = $(this);
            var $inner = $carousel.find('.roles-carousel-inner');
            var $items = $inner.find('.role-item');
            var $dotsContainer = $carousel.find('.roles-dots');
            var current = 0;

            // build dots based on items
            $dotsContainer.empty();
            if ($items.length > 1) {
                $items.each(function() {
                    $('<span class="role-dot"></span>').appendTo($dotsContainer);
                });
            }
            var $dots = $dotsContainer.find('.role-dot');

            function showItem(index, animate) {
                var prevIndex = current;
                if (animate && prevIndex !== index) {
                    // Modern slide-and-fade
                    $items.eq(prevIndex).animate({
                        opacity: 0,
                        top: '-15px'
                    }, 400, function() {
                        $(this).hide().css({
                            top: '0',
                            opacity: 1
                        });
                    });

                    $items.eq(index).css({
                            display: 'flex',
                            opacity: 0,
                            top: '15px'
                        })
                        .show()
                        .animate({
                            opacity: 1,
                            top: '0'
                        }, 400);
                } else {
                    $items.hide();
                    $items.eq(index).css({
                        display: 'flex'
                    }).show();
                }
                $dots.removeClass('active');
                $dots.eq(index).addClass('active');
                current = index;
            }

            if ($items.length > 0) {
                showItem(0, false);
            }

            function goNext() {
                if ($items.length <= 1) return;
                var next = (current + 1) % $items.length;
                showItem(next, true);
            }

            function goPrev() {
                if ($items.length <= 1) return;
                var prev = (current - 1 + $items.length) % $items.length;
                showItem(prev, true);
            }

            var autoTimer;

            function startCycle() {
                autoTimer = setInterval(goNext, 4000);
            }

            function stopCycle() {
                clearInterval(autoTimer);
            }

            startCycle();

            $carousel.find('.roles-prev').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                stopCycle();
                goPrev();
                startCycle();
            });

            $carousel.find('.roles-next').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                stopCycle();
                goNext();
                startCycle();
            });

            $dots.on('click', function(e) {
                e.preventDefault();
                var idx = $(this).index();
                if (idx !== current) {
                    stopCycle();
                    showItem(idx, true);
                    startCycle();
                }
            });

            $carousel.hover(
                function() {
                    stopCycle();
                },
                function() {
                    startCycle();
                }
            );
        });
    });
</script>

<div id="newEventModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line("add_new_event"); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" id="addevent_form" method="post" enctype="multipart/form-data" action="">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('event_title'); ?></label><small class="req"> *</small>
                                <input class="form-control" name="title" id="input-field">
                                <span class="text-danger"><?php echo form_error('title'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('description'); ?></label>
                                <textarea name="description" class="form-control" id="desc-field"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-12 col-sm-12">
                            <div class="row">
                                <div class="col-md-6 col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('event_from'); ?><small class="req"> *</small></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-sm-6">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('event_to'); ?><small class="req"> *</small></label>
                                        <div class="input-group">
                                            <div class="input-group-addon"><i class="fa fa-calendar"></i></div>
                                            <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('event_color'); ?></label>
                                <input type="hidden" name="eventcolor" autocomplete="off" id="eventcolor" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php
                                $i      = 0;
                                $colors = '';
                                foreach ($event_colors as $color) {
                                    $color_selected_class = 'cpicker-small';
                                    if ($i == 0) {
                                        $color_selected_class = 'cpicker-big';
                                    }
                                    $colors .= "<div class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
                                    $i++;
                                }
                                echo '<div class="cpicker-wrapper">';
                                echo $colors;
                                echo '</div>';
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="pt15 displayblock overflow-hidden w-100"><?php echo $this->lang->line('event_type'); ?></label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="public" id="public"><?php echo $this->lang->line('public'); ?>
                                </label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="private" checked id="private"><?php echo $this->lang->line('private'); ?>
                                </label>
                                <label class="radio-inline w-xs-45 ml-xs-0">
                                    <input type="radio" name="event_type" value="sameforall" id="public"><?php echo $this->lang->line('all'); ?> <?php echo json_decode($role)->name; ?>
                                </label>
                                <label class="radio-inline w-xs-45">
                                    <input type="radio" name="event_type" value="protected" id="public"><?php echo $this->lang->line('protected'); ?>
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <input type="submit" class="btn btn-primary submit_addevent pull-right" value="<?php echo $this->lang->line('save'); ?>">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="viewEventModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-dialog2 modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('edit_event'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form role="form" method="post" id="updateevent_form" enctype="multipart/form-data" action="">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_title') ?></label>
                            <input class="form-control" name="title" placeholder="" id="event_title">
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('description') ?></label>
                            <textarea name="description" class="form-control" placeholder="" id="event_desc"></textarea>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('event_from'); ?></label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" autocomplete="off" name="event_from" class="form-control pull-right event_from">
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="exampleInputEmail1"><?php echo $this->lang->line('event_to'); ?></label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" autocomplete="off" name="event_to" class="form-control pull-right event_to">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="eventid" id="eventid">
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_color') ?></label>
                            <input type="hidden" name="eventcolor" autocomplete="off" placeholder="Event Color" id="event_color" class="form-control">
                        </div>
                        <div class="form-group col-md-12">
                            <?php
                            $i      = 0;
                            $colors = '';
                            foreach ($event_colors as $color) {
                                $colorid              = trim($color, "#");
                                $color_selected_class = 'cpicker-small';
                                if ($i == 0) {
                                    $color_selected_class = 'cpicker-big';
                                }
                                $colors .= "<div id=" . $colorid . " class='calendar-cpicker cpicker " . $color_selected_class . "' data-color='" . $color . "' style='background:" . $color . ";border:1px solid " . $color . "; border-radius:100px'></div>";
                                $i++;
                            }
                            echo '<div class="cpicker-wrapper selectevent">';
                            echo $colors;
                            echo '</div>';
                            ?>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="exampleInputEmail1"><?php echo $this->lang->line('event_type') ?></label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="public" id="public"><?php echo $this->lang->line('public') ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="private" id="private"><?php echo $this->lang->line('private') ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="sameforall" id="public"><?php echo $this->lang->line('all') ?> <?php echo json_decode($role)->name; ?>
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="eventtype" value="protected" id="public"><?php echo $this->lang->line('protected') ?>
                            </label>
                        </div>
                        <div class="col-xs-11 col-sm-11 col-md-11 col-lg-11">
                            <input type="submit" class="btn btn-primary submit_update pull-right" value="<?php echo $this->lang->line('save'); ?>">
                        </div>
                        <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                            <?php if ($this->rbac->hasPrivilege('calendar_to_do_list', 'can_delete')) { ?>
                                <input type="button" id="delete_event" class="btn btn-primary submit_delete pull-right" value="<?php echo $this->lang->line('delete'); ?>">
                            <?php } ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#viewEventModal,#newEventModal').modal({
            backdrop: 'static',
            keyboard: false,
            show: false
        });
    });
</script>

<style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script type="text/javascript">
    // Helper function to toggle Donut Chart center text visibility
    function toggleDonutText(show) {
        var el = document.querySelector('.donut-inner-text');
        if (el) {
            el.style.display = show ? 'flex' : 'none';
        }
    }

    <?php if ($this->rbac->hasPrivilege('income_donut_graph', 'can_view') && ($this->module_lib->hasActive('income'))) {
    ?>
        var dashboard_income_donut_data = [<?php $s = 1;
                                            foreach ($incomegraph as $value) {
                                            ?><?php echo $value['total']; ?>, <?php } ?>];
        var dashboard_income_donut_has_data = false;
        if (dashboard_income_donut_data && dashboard_income_donut_data.length) {
            for (var di = 0; di < dashboard_income_donut_data.length; di++) {
                if (Number(dashboard_income_donut_data[di]) > 0) {
                    dashboard_income_donut_has_data = true;
                    break;
                }
            }
        }

        if (!dashboard_income_donut_has_data) {
            var incomeEmptyEl = document.getElementById('dashboard_empty_doughnut_income');
            if (incomeEmptyEl) {
                incomeEmptyEl.style.display = 'flex';
            }
            var incomeCanvasEl = document.getElementById('doughnut-chart');
            if (incomeCanvasEl) {
                incomeCanvasEl.style.display = 'none';
                if (incomeCanvasEl.parentNode && incomeCanvasEl.parentNode.classList) {
                    incomeCanvasEl.parentNode.classList.add('is-empty');
                }
                toggleDonutText(false);
            }
        } else {
            var incomeEmptyEl2 = document.getElementById('dashboard_empty_doughnut_income');
            if (incomeEmptyEl2) {
                incomeEmptyEl2.style.display = 'none';
            }
            var incomeCanvasEl2 = document.getElementById('doughnut-chart');
            if (incomeCanvasEl2) {
                incomeCanvasEl2.style.display = '';
                if (incomeCanvasEl2.parentNode && incomeCanvasEl2.parentNode.classList) {
                    incomeCanvasEl2.parentNode.classList.remove('is-empty');
                }
                toggleDonutText(true);
            }
            new Chart(document.getElementById("doughnut-chart"), {
                type: 'doughnut',
                data: {
                    labels: [<?php foreach ($incomegraph as $value) { ?> "<?php echo $value['income_category']; ?>", <?php } ?>],
                    datasets: [{
                        label: "Income",
                        backgroundColor: [<?php
                                            $chart_colors = ['#a78bfa', '#fbbf24', '#60a5fa', '#ec4899', '#34d399', '#f87171', '#818cf8']; // Vibrant palette
                                            $i = 0;
                                            foreach ($incomegraph as $value) {
                                                echo '"' . $chart_colors[$i % count($chart_colors)] . '",';
                                                $i++;
                                            }
                                            ?>],
                        data: dashboard_income_donut_data
                    }]
                },
                options: {
                    responsive: true,
                    circumference: Math.PI,
                    rotation: -Math.PI,
                    legend: {
                        position: 'top',
                    },
                    cutoutPercentage: 75, // Thinner ring for modern look
                    title: {
                        display: false,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    maintainAspectRatio: false
                }
            });
        }
    <?php
    }
    if (($this->rbac->hasPrivilege('expense_donut_graph', 'can_view')) && ($this->module_lib->hasActive('expense'))) {
    ?>
        new Chart(document.getElementById("doughnut-chart1"), {
            type: 'doughnut',
            data: {
                labels: [<?php foreach ($expensegraph as $value) { ?> "<?php echo $value['exp_category']; ?>", <?php } ?>],
                datasets: [{
                    label: "Population (millions)",
                    backgroundColor: [<?php
                                        $vibrantColors = ['#6366f1', '#f59e0b', '#10b981', '#ec4899', '#3b82f6', '#8b5cf6', '#f43f5e', '#14b8a6'];
                                        $colorIndex = 0;
                                        foreach ($expensegraph as $value) {
                                            $color = $vibrantColors[$colorIndex % count($vibrantColors)];
                                            echo "'$color',";
                                            $colorIndex++;
                                        }
                                        ?>],
                    data: [<?php foreach ($expensegraph as $value) { ?><?php echo $value['total']; ?>, <?php } ?>]
                }]
            },
            options: {
                responsive: true,
                circumference: Math.PI,
                rotation: -Math.PI,
                legend: {
                    display: false, // Hide default broken legend
                },
                title: {
                    display: true,
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                },
                cutoutPercentage: 75, // Thinner ring (matches Income Donut)
                maintainAspectRatio: true,
                aspectRatio: 2.2, // Broad arch look
                customTooltips: function(tooltip) {
                    var tooltipEl = $('#chartjs-tooltip');
                    if (!tooltip) {
                        tooltipEl.css({
                            opacity: 0
                        });
                        return;
                    }
                    if (!tooltipEl[0]) {
                        $('body').append('<div id="chartjs-tooltip"></div>');
                        tooltipEl = $('#chartjs-tooltip');
                    }
                    if (tooltip.body) {
                        var bodyLines = tooltip.body[0].lines;
                        var innerHtml = '<div class="tooltip-header">' + tooltip.title + '</div>';
                        bodyLines.forEach(function(line, i) {
                            var fill = tooltip.labelColors[i].backgroundColor;
                            var parts = line.split(':');
                            var label = parts[0];
                            var value = parts[1];
                            innerHtml += '<div class="tooltip-body-item">';
                            innerHtml += '<span class="tooltip-color-box" style="background-color:' + fill + '"></span>';
                            innerHtml += '<span class="tooltip-label">' + label + ':</span>';
                            innerHtml += '<span class="tooltip-value">' + value + '</span>';
                            innerHtml += '</div>';
                        });
                        tooltipEl.html(innerHtml);
                    }
                    var canvasOffset = $(this.chart.canvas).offset();
                    tooltipEl.css({
                        opacity: 1,
                        left: canvasOffset.left + tooltip.caretX + 'px',
                        top: canvasOffset.top + tooltip.caretY + 'px',
                        fontFamily: tooltip._fontFamily,
                        fontSize: tooltip._fontSize
                    });
                }
            }
        });

        // Generate Custom HTML Legend for Expense Chart
        var expense_legend_html = '<div class="chart-legend-custom">';
        <?php
        $vibrantColors = ['#6366f1', '#f59e0b', '#10b981', '#ec4899', '#3b82f6', '#8b5cf6', '#f43f5e', '#14b8a6'];
        $i = 0;
        foreach ($expensegraph as $value) {
            $color = $vibrantColors[$i % count($vibrantColors)];
        ?>
            expense_legend_html += '<div class="legend-item"><span class="legend-color" style="background-color:<?php echo $color; ?>"></span><?php echo $value['exp_category']; ?></div>';
        <?php
            $i++;
        }
        ?>
        expense_legend_html += '</div>';
        // Append legend after the canvas container
        $('#doughnut-chart1').after(expense_legend_html);

        var expense_data = [<?php foreach ($expensegraph as $value) { ?><?php echo $value['total']; ?>, <?php } ?>];
        var has_donut_data = false;
        for (var i = 0; i < expense_data.length; i++) {
            if (Number(expense_data[i]) > 0) {
                has_donut_data = true;
                break;
            }
        }
        if (!has_donut_data) {
            $('#doughnut-chart1').hide();
            $('#dashboard_empty_doughnut-chart1').css('display', 'flex');
        } else {
            $('#doughnut-chart1').show();
            $('#dashboard_empty_doughnut-chart1').hide();
        }
    <?php
    }
    if (($this->module_lib->hasActive('fees_collection')) || ($this->module_lib->hasActive('expense')) || ($this->module_lib->hasActive('income'))) {
    ?>
        $(function() {
            var areaChartOptions = {
                showScale: true,
                scaleShowGridLines: true,
                scaleGridLineColor: "rgba(148, 163, 184, 0.18)",
                scaleGridLineWidth: 1,
                scaleShowHorizontalLines: true,
                scaleShowVerticalLines: false, // Hide vertical lines for cleaner look
                bezierCurve: true,
                bezierCurveTension: 0.4, // Increased smoothing
                pointDot: false, // Hide dots by default for clean look
                pointDotRadius: 4,
                pointDotStrokeWidth: 2,
                pointHitDetectionRadius: 20, // Easier hover
                datasetStroke: true,
                datasetStrokeWidth: 2,
                datasetFill: true,
                maintainAspectRatio: true,
                responsive: true,
                scaleLabel: "<%if (value >= 1000){%><%=Math.round(value/1000)%>k<%} else {%><%=value%><%}%>",
                customTooltips: function(tooltip) {
                    var formatK = function(val) {
                        if (!val) return '0';
                        var n = parseFloat(val.toString().replace(/,/g, ''));
                        if (isNaN(n)) return val;
                        if (n >= 1000) return (n / 1000).toFixed(0) + 'k';
                        return n;
                    };
                    var tooltipEl = $('#chartjs-tooltip');

                    if (!tooltip) {
                        tooltipEl.css({
                            opacity: 0
                        });
                        return;
                    }

                    // Create element on first render
                    if (!tooltipEl[0]) {
                        $('body').append('<div id="chartjs-tooltip"></div>');
                        tooltipEl = $('#chartjs-tooltip');
                    }

                    // Set text matches reference
                    if (tooltip.text) { // Single Tooltip
                        tooltipEl.html(tooltip.text);
                    } else if (tooltip.labels) { // Multi Tooltip
                        var innerHtml = '<div class="tooltip-header">' + tooltip.title + '</div>';
                        for (var i = 0; i < tooltip.labels.length; i++) {
                            var fill = tooltip.legendColors[i].fill;
                            var label = tooltip.labels[i];
                            var valParts = label.split(':');
                            var seriesName = valParts[0];
                            var value = formatK(valParts[1] || '');

                            innerHtml += '<div class="tooltip-body-item">';
                            innerHtml += '<span class="tooltip-color-box" style="background-color:' + fill + '"></span>';
                            innerHtml += '<span class="tooltip-label">' + seriesName + ':</span>';
                            innerHtml += '<span class="tooltip-value">' + value + '</span>';
                            innerHtml += '</div>';
                        }
                        tooltipEl.html(innerHtml);
                    }

                    // Calculate positioning w.r.t canvas
                    var canvasOffset = $(this.chart.canvas).offset();

                    // Display, position, and set styles for font
                    tooltipEl.css({
                        opacity: 1,
                        left: canvasOffset.left + tooltip.x + 'px',
                        top: canvasOffset.top + tooltip.y + 'px',
                        fontFamily: tooltip.fontFamily,
                        fontSize: tooltip.fontSize,
                        fontStyle: tooltip.fontStyle,
                    });
                }
            };

            var bar_chart = "<?php echo $bar_chart ?>";
            var line_chart = "<?php echo $line_chart ?>";
            <?php
            if ($this->rbac->hasPrivilege('fees_collection_and_expense_yearly_chart', 'can_view')) {
            ?>
                if (line_chart) {

                    // Custom Line Chart Extension for Hover Effects
                    Chart.types.Line.extend({
                        name: "LineWithHover",
                        showTooltip: function(ChartElements, forceRedraw) {
                            // 1. Draw base chart (clears previous overlays)
                            this.draw();

                            // 2. Custom Overlays if we have active elements
                            if (ChartElements && ChartElements.length > 0) {
                                var ctx = this.chart.ctx;
                                var firstPt = ChartElements[0];
                                var x = firstPt.x;
                                var scale = this.scale;
                                var yBottom = scale.endPoint;
                                var yTop = scale.startPoint;

                                // A. Draw Vertical Dashed Line
                                ctx.save();
                                ctx.beginPath();
                                ctx.moveTo(x, yTop);
                                ctx.lineTo(x, yBottom);
                                ctx.lineWidth = 1;
                                ctx.strokeStyle = '#94a3b8'; // Cool gray for the line
                                ctx.setLineDash([5, 5]); // Dashed effect
                                ctx.stroke();
                                ctx.restore();

                                // B. Draw Ash Background for Month Label
                                var fontHeight = scale.font || 12; // Approx 12px if not exposed
                                var labelY = yBottom + 8; // Slight padding below axis
                                var pad = 6;
                                var textWidth = ctx.measureText(firstPt.label).width;

                                ctx.save();
                                ctx.fillStyle = '#e2e8f0'; // Ash/Light Gray background
                                // Draw rounded rect effect (simple rect)
                                ctx.fillRect(x - (textWidth / 2) - pad, labelY, textWidth + (pad * 2), 20);
                                ctx.restore();

                                // Redraw label text on top (ensure contrast)
                                ctx.save();
                                ctx.fillStyle = '#475569'; // Darker text color
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'top';
                                ctx.font = scale.font;
                                ctx.fillText(firstPt.label, x, labelY + 2);
                                ctx.restore();

                                // C. Draw Dots ONLY on Hover (Reference Style)
                                ctx.save();
                                ChartElements.forEach(function(pt) {
                                    // Draw outer border (white)
                                    ctx.beginPath();
                                    ctx.arc(pt.x, pt.y, 5, 0, Math.PI * 2);
                                    ctx.fillStyle = pt.strokeColor; // Use the line color (Red/Blue bases)
                                    ctx.strokeStyle = '#ffffff';
                                    ctx.lineWidth = 2;
                                    ctx.fill();
                                    ctx.stroke();
                                });
                                ctx.restore();
                            }

                            // 3. Call original tooltip method to draw the tooltip box (if using default, but we have custom HTML tooltip handler logic too)
                            Chart.types.Line.prototype.showTooltip.apply(this, arguments);
                        }
                    });



                    var lineChartCanvas = $("#lineChart").get(0).getContext("2d");

                    // -- Restoring Data Logic Start --
                    var lineChartOptions = areaChartOptions;
                    lineChartOptions.datasetFill = true;

                    var yearly_collection_array = <?php echo json_encode($yearly_collection) ?>;
                    var yearly_expense_array = <?php echo json_encode($yearly_expense) ?>;

                    var dashboard_line_has_data = false;
                    if (yearly_collection_array && yearly_collection_array.length) {
                        for (var i = 0; i < yearly_collection_array.length; i++) {
                            if (Number(yearly_collection_array[i]) > 0) {
                                dashboard_line_has_data = true;
                                break;
                            }
                        }
                    }
                    if (!dashboard_line_has_data && yearly_expense_array && yearly_expense_array.length) {
                        for (var i = 0; i < yearly_expense_array.length; i++) {
                            if (Number(yearly_expense_array[i]) > 0) {
                                dashboard_line_has_data = true;
                                break;
                            }
                        }
                    }

                    if (!dashboard_line_has_data) {
                        $('#lineChart').hide();
                        $('#dashboard_empty_lineChart').css('display', 'flex');
                    } else {
                        $('#lineChart').show();
                        $('#dashboard_empty_lineChart').hide();
                    }

                    var total_month = <?php echo json_encode($total_month) ?>;
                    var monthShortMap = {
                        'january': 'Jan',
                        'february': 'Feb',
                        'march': 'Mar',
                        'april': 'Apr',
                        'may': 'May',
                        'june': 'Jun',
                        'july': 'Jul',
                        'august': 'Aug',
                        'september': 'Sep',
                        'october': 'Oct',
                        'november': 'Nov',
                        'december': 'Dec'
                    };
                    var total_month_short = [];
                    for (var i = 0; i < total_month.length; i++) {
                        var key = ('' + total_month[i]).toLowerCase();
                        total_month_short.push(monthShortMap[key] || total_month[i]);
                    }
                    var areaChartData_expense_Income = {
                        labels: total_month_short,
                        datasets: [
                            <?php if (($this->module_lib->hasActive('expense'))) { ?> {
                                    label: "Expenses",
                                    fillColor: "rgba(244, 63, 94, 0.25)",
                                    strokeColor: "#f43f5e",
                                    pointColor: "#f43f5e",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "#f43f5e",
                                    data: yearly_expense_array
                                },
                            <?php } ?>
                            <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                    label: "Fees Collection",
                                    fillColor: "rgba(59, 130, 246, 0.25)",
                                    strokeColor: "#3b82f6",
                                    pointColor: "#3b82f6",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "#3b82f6",
                                    data: yearly_collection_array
                                }
                            <?php } ?>
                        ]
                    };
                    // -- Restoring Data Logic End --

                    var lineChart = new Chart(lineChartCanvas).LineWithHover(areaChartData_expense_Income, lineChartOptions);
                }

                var current_month_days = <?php echo json_encode($current_month_days) ?>;
                var days_collection = <?php echo json_encode($days_collection) ?>;
                var days_expense = <?php echo json_encode($days_expense) ?>;
                var areaChartData_classAttendence = {
                    labels: current_month_days,
                    datasets: [
                        <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                label: "Fees Collection",
                                fillColor: "rgba(99, 102, 241, 0.15)", // Indigo-500 low opacity
                                strokeColor: "#6366f1", // Indigo-500
                                pointColor: "#6366f1",
                                pointStrokeColor: "#ffffff",
                                pointHighlightFill: "#ffffff",
                                pointHighlightStroke: "#6366f1",
                                data: days_collection
                            },

                        <?php }
                        if (($this->module_lib->hasActive('expense'))) { ?> {
                                label: "Expenses",
                                fillColor: "rgba(245, 158, 11, 0.15)", // Amber-500 low opacity
                                strokeColor: "#f59e0b", // Amber-500
                                pointColor: "#f59e0b",
                                pointStrokeColor: "#ffffff",
                                pointHighlightFill: "#ffffff",
                                pointHighlightStroke: "#f59e0b",
                                data: days_expense
                            }

                        <?php } ?>
                    ]
                };


            <?php }
            if ($this->rbac->hasPrivilege('fees_collection_and_expense_monthly_chart', 'can_view')) { ?>
                if (bar_chart) {
                    var current_month_days = <?php echo json_encode($current_month_days) ?>;
                    var days_collection = <?php echo json_encode($days_collection) ?>;
                    var days_expense = <?php echo json_encode($days_expense) ?>;

                    var dashboard_bar_has_data = false;
                    if (days_collection && days_collection.length) {
                        for (var bc = 0; bc < days_collection.length; bc++) {
                            if (Number(days_collection[bc]) > 0) {
                                dashboard_bar_has_data = true;
                                break;
                            }
                        }
                    }
                    if (!dashboard_bar_has_data && days_expense && days_expense.length) {
                        for (var be = 0; be < days_expense.length; be++) {
                            if (Number(days_expense[be]) > 0) {
                                dashboard_bar_has_data = true;
                                break;
                            }
                        }
                    }

                    if (!dashboard_bar_has_data) {
                        var barEmptyEl = document.getElementById('dashboard_empty_barChart');
                        if (barEmptyEl) {
                            barEmptyEl.style.display = 'flex';
                        }
                        var barCanvasEl = document.getElementById('barChart');
                        if (barCanvasEl) {
                            barCanvasEl.style.display = 'none';
                            if (barCanvasEl.parentNode && barCanvasEl.parentNode.classList) {
                                barCanvasEl.parentNode.classList.add('is-empty');
                            }
                        }
                        return;
                    }

                    var barEmptyEl2 = document.getElementById('dashboard_empty_barChart');
                    if (barEmptyEl2) {
                        barEmptyEl2.style.display = 'none';
                    }
                    var barCanvasEl2 = document.getElementById('barChart');
                    if (barCanvasEl2) {
                        barCanvasEl2.style.display = '';
                        if (barCanvasEl2.parentNode && barCanvasEl2.parentNode.classList) {
                            barCanvasEl2.parentNode.classList.remove('is-empty');
                        }
                    }

                    var areaChartData_classAttendence = {
                        labels: current_month_days,
                        datasets: [
                            <?php if (($this->module_lib->hasActive('income'))) { ?> {
                                    label: "Fees Collection",
                                    fillColor: "rgba(99, 102, 241, 1)",
                                    strokeColor: "#6366f1",
                                    pointColor: "#6366f1",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "#6366f1",
                                    data: days_collection
                                },

                            <?php } ?>
                            <?php if (($this->module_lib->hasActive('expense'))) { ?> {
                                    label: "Expenses",
                                    fillColor: "rgba(245, 158, 11, 1)",
                                    strokeColor: "#f59e0b",
                                    pointColor: "#f59e0b",
                                    pointStrokeColor: "#ffffff",
                                    pointHighlightFill: "#ffffff",
                                    pointHighlightStroke: "#f59e0b",
                                    data: days_expense
                                }

                            <?php } ?>
                        ]
                    };

                    var barChartCanvas = $("#barChart").get(0).getContext("2d");
                    var barChart = new Chart(barChartCanvas);
                    var barChartData = areaChartData_classAttendence;
                    // barChartData.datasets[1].fillColor = "rgba(233, 30, 99, 0.9)";
                    // barChartData.datasets[1].strokeColor = "rgba(233, 30, 99, 0.9)";
                    var barChartOptions = {
                        scaleBeginAtZero: true,
                        scaleShowGridLines: true,
                        scaleGridLineColor: "rgba(0,0,0,.05)",
                        scaleGridLineWidth: 1,
                        scaleShowHorizontalLines: false,
                        scaleShowVerticalLines: false,
                        barShowStroke: true,
                        barStrokeWidth: 2,
                        barValueSpacing: 8, // Increased spacing for cleaner look
                        barDatasetSpacing: 2,
                        responsive: true,
                        maintainAspectRatio: false,
                        scaleLabel: "<%= (Number(value) >= 1000000) ? (value/1000000 + 'M') : ((Number(value) >= 1000) ? (value/1000 + 'k') : value) %>",
                        scaleFontSize: ($(window).width() < 768) ? 9 : 11, // Responsive font size
                        barValueSpacing: ($(window).width() < 768) ? 5 : 8, // Responsive bar spacing
                    };
                    barChartOptions.datasetFill = false;
                    barChart.Bar(barChartData, barChartOptions);
                }
            <?php } ?>
        });
    <?php
    }
    ?>

    $(document).ready(function() {
        // Date filter functionality
        $('#filter_type').change(function() {
            var filterType = $(this).val();

            // Hide all filter groups
            $('#monthly_filter, #yearly_filter, #custom_filter').hide();

            // Show relevant filter group
            if (filterType === 'monthly') {
                $('#monthly_filter').show();
            } else if (filterType === 'yearly') {
                $('#yearly_filter').show();
            } else if (filterType === 'custom') {
                $('#custom_filter').show();
            }
        });

        // Apply filter button click
        $('#apply_filter').click(function() {
            var filterType = $('#filter_type').val();
            var data = {
                filter_type: filterType
            };

            // Add specific filter data based on type
            if (filterType === 'monthly') {
                data.month = $('#month_select').val();
                data.year = $('#year_select').val();
            } else if (filterType === 'yearly') {
                data.year = $('#year_only_select').val();
            } else if (filterType === 'custom') {
                data.start_date = $('#start_date').val();
                data.end_date = $('#end_date').val();
            }

            // Show loading state
            $(this).html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            $(this).prop('disabled', true);

            // Make AJAX request
            $.ajax({
                type: 'POST',
                url: base_url + 'admin/admin/getDashboardSummary',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        updateSummaryCards(response.data);
                    } else {
                        alert('Error loading data. Please try again.');
                    }
                },
                error: function() {
                    alert('Error loading data. Please try again.');
                },
                complete: function() {
                    // Reset button state
                    $('#apply_filter').html('<i class="fa fa-refresh"></i> Apply Filter');
                    $('#apply_filter').prop('disabled', false);
                }
            });
        });

        function updateSummaryCards(data) {
            var currencySymbol = '<?php echo $currency_symbol; ?>';

            console.log('=== UPDATE SUMMARY CARDS DEBUG ===');
            console.log('Received data:', data);
            console.log('Permissions:', data.permissions);

            // Update income card only if user has permission
            if (data.permissions && data.permissions.can_view_income) {
                $('#total_income_display').text(currencySymbol + numberFormat(data.total_income, 2));
                $('#income_period').text(data.period_display);
                // Update Donut Center Text
                $('#donut_total_val').text(currencySymbol + numberFormat(data.total_income, 2));
            }

            // Update expense card only if user has permission
            if (data.permissions && data.permissions.can_view_expense) {
                $('#total_expense_display').text(currencySymbol + numberFormat(data.total_expense, 2));
                $('#expense_period').text(data.period_display);
            }

            // Update fee collection card only if user has permission
            if (data.permissions && data.permissions.can_view_fees) {
                var feeCollectionFormatted = currencySymbol + numberFormat(data.total_fee_collection, 2);
                console.log('Formatted fee collection:', feeCollectionFormatted);
                $('#total_fee_collection_display').text(feeCollectionFormatted);
                $('#fee_period').text(data.period_display);
                console.log('Fee collection card updated');
            }

            // Update net profit/loss card only if user has permission for both income and expense
            if (data.permissions && data.permissions.can_view_profit) {
                var netProfit = data.net_profit;
                var isProfit = netProfit >= 0;

                $('#net_profit_display').text(currencySymbol + numberFormat(Math.abs(netProfit), 2));
                $('#net_profit_label').text(isProfit ? 'PROFIT' : 'LOSS');
                $('#profit_period').text(data.period_display);

                var cardElement = $('#net_profit_card');
                cardElement.removeClass('dashboard-metric--profit dashboard-metric--loss');
                cardElement.addClass(isProfit ? 'dashboard-metric--profit' : 'dashboard-metric--loss');
            }

            animateHeaderMetricBars();
        }

        function animateHeaderMetricBars() {
            var containers = document.querySelectorAll('[data-bars-animate]');
            if (!containers || !containers.length) {
                return;
            }

            containers.forEach(function(container) {
                container.classList.remove('dashboard-top-metrics--animate');
                void container.offsetWidth;
                container.classList.add('dashboard-top-metrics--animate');
                window.setTimeout(function() {
                    container.classList.remove('dashboard-top-metrics--animate');
                }, 1200);
            });
        }

        function numberFormat(number, decimals) {
            return parseFloat(number).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        }

        animateHeaderMetricBars();

        $(document).on('click', '.close_notice', function() {
            var data = $(this).data();
            $.ajax({
                type: "POST",
                url: base_url + "admin/notification/read",
                data: {
                    'notice': data.noticeid
                },
                dataType: "json",
                success: function(data) {
                    if (data.status == "fail") {

                        errorMsg(data.msg);
                    } else {
                        successMsg(data.msg);
                    }

                }
            });
        });
    });
</script>