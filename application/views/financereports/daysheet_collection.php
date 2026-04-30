<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
/* ========================================================================
   DAYSHEET DESIGN SYSTEM
   ======================================================================== */
:root {
    --ds-primary: #667eea;
    --ds-primary-dark: #5a67d8;
    --ds-success: #48bb78;
    --ds-danger: #f56565;
    --ds-warning: #ed8936;
    --ds-info: #4299e1;
    --ds-purple: #9f7aea;
    --ds-bg: #f7fafc;
    --ds-card: #ffffff;
    --ds-border: #e2e8f0;
    --ds-text: #2d3748;
    --ds-text-muted: #718096;
    --ds-shadow: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -1px rgba(0,0,0,.05);
    --ds-shadow-lg: 0 10px 25px -5px rgba(0,0,0,.1), 0 4px 10px -5px rgba(0,0,0,.04);
    --ds-radius: 10px;
}

/* Custom Scrollbars */
.ds-table-scroll::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.ds-table-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.ds-table-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}
.ds-table-scroll::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Also for modals */
.modal-body::-webkit-scrollbar {
    width: 6px;
}
.modal-body::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.modal-body::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

/* Page Title */
.ds-page-title {
    font-weight: 700 !important;
    color: var(--ds-text);
    font-size: 22px !important;
    margin-bottom: 0;
}

/* ---------- STAT CARDS ---------- */
.ds-stat-row { margin-bottom: 20px; }
.ds-stat-card {
    border-radius: var(--ds-radius);
    padding: 20px 22px;
    color: #fff;
    position: relative;
    overflow: hidden;
    box-shadow: var(--ds-shadow);
    transition: transform .2s, box-shadow .2s;
    margin-bottom: 15px;
    min-height: 110px;
}
.ds-stat-card:hover { transform: translateY(-3px); box-shadow: var(--ds-shadow-lg); }
.ds-stat-card .inner h3 { font-size: 26px; font-weight: 700; margin: 0 0 4px; }
.ds-stat-card .inner p  { font-size: 13px; margin: 0; opacity: .9; }
.ds-stat-card .icon { position: absolute; top: 14px; right: 16px; font-size: 46px; opacity: .18; }
.bg-gradient-blue   { background: linear-gradient(135deg,#667eea,#764ba2); }
.bg-gradient-green  { background: linear-gradient(135deg,#38b2ac,#48bb78); }
.bg-gradient-purple { background: linear-gradient(135deg,#805ad5,#b794f4); }
.bg-gradient-red    { background: linear-gradient(135deg,#f56565,#fc8181); }
.bg-gradient-amber  { background: linear-gradient(135deg,#dd6b20,#ed8936); }
.bg-gradient-teal   { background: linear-gradient(135deg,#319795,#4fd1c5); }

/* ---- QUICK-DATE BUTTONS ---- */
.ds-date-btns { margin-bottom: 8px; }
.ds-date-btns .btn {
    border-radius: 20px;
    padding: 5px 16px;
    font-size: 12px;
    margin-right: 6px;
    font-weight: 600;
    border: 1px solid var(--ds-border);
    background: #fff;
    color: var(--ds-text);
    transition: all .15s;
}
.ds-date-btns .btn.active,
.ds-date-btns .btn:hover {
    background: var(--ds-primary);
    color: #fff;
    border-color: var(--ds-primary);
}

/* ---------- FILTER BOX ---------- */
.ds-filter-box {
    background: var(--ds-card);
    border-radius: var(--ds-radius);
    box-shadow: var(--ds-shadow);
    border: 1px solid var(--ds-border);
    padding: 20px;
    margin-bottom: 20px;
}
.ds-filter-box label { font-weight: 600; color: var(--ds-text); font-size: 13px; }
.ds-filter-box .form-control { border-radius: 6px; border: 1px solid var(--ds-border); }

/* ---------- SECTION CARDS ---------- */
.ds-section {
    background: var(--ds-card);
    border-radius: var(--ds-radius);
    box-shadow: var(--ds-shadow);
    border: 1px solid var(--ds-border);
    margin-bottom: 20px;
    overflow: hidden;
}
.ds-section-header {
    padding: 14px 20px;
    border-bottom: 1px solid var(--ds-border);
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.ds-section-header h4 { margin: 0; font-weight: 700; font-size: 15px; color: var(--ds-text); }
.ds-section-header h4 i { margin-right: 8px; color: var(--ds-primary); }
.ds-section-body { padding: 16px 20px; }

/* ---------- COLLECTION GRID ---------- */
.ds-grid-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.ds-grid-table th { background: #f1f5f9; color: var(--ds-text-muted); text-transform: uppercase; font-size: 11px; letter-spacing: .4px; padding: 10px 12px; border-bottom: 2px solid var(--ds-border); white-space: nowrap; }
.ds-grid-table td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; white-space: nowrap; }
.ds-grid-table tbody tr:hover { background: #f7fafc; }
.ds-grid-table .acct-name { font-weight: 600; color: var(--ds-text); max-width: 200px; overflow: hidden; text-overflow: ellipsis; }
.ds-grid-table .amount-cell { text-align: right; font-family: 'Segoe UI', monospace; }
.ds-grid-table .amount-cell.has-value { color: var(--ds-success); font-weight: 600; }
.ds-grid-table .total-row { background: #f1f5f9; font-weight: 700; }
.ds-grid-table .total-row td { border-top: 2px solid var(--ds-primary); }
.ds-grid-table .grand-total { font-size: 15px; color: var(--ds-primary); }

/* ---------- TABS ---------- */
.ds-tabs { border-bottom: 2px solid var(--ds-border); margin-bottom: 0; padding: 0 20px; background: #fafbfc; }
.ds-tabs li { margin-bottom: -2px; }
.ds-tabs li a { border: none !important; border-radius: 0 !important; font-weight: 600; font-size: 13px; color: var(--ds-text-muted); padding: 12px 18px; transition: color .15s; cursor: pointer; }
.ds-tabs li a:hover { background: transparent; color: var(--ds-primary); }
.ds-tabs li.active a { color: var(--ds-primary) !important; border-bottom: 3px solid var(--ds-primary) !important; background: transparent !important; }

/* ---------- DATA TABLE ---------- */
.ds-data-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.ds-data-table th { background: #f8fafc; font-size: 11px; text-transform: uppercase; letter-spacing: .4px; color: var(--ds-text-muted); padding: 10px 10px; border-bottom: 2px solid var(--ds-border); white-space: nowrap; }
.ds-data-table td { padding: 9px 10px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.ds-data-table tbody tr:hover { background: #fafbfc; }
.ds-data-table .text-right { text-align: right; }
.ds-data-table .total-row { background: #f1f5f9; font-weight: 700; }
.ds-data-table .total-row td { border-top: 2px solid var(--ds-border); }

/* ---------- ACCOUNT BALANCE SECTION ---------- */
.ds-acct-section-title {
    font-weight: 700;  font-size: 14px; color: var(--ds-text);
    margin: 18px 0 10px; padding-bottom: 6px; border-bottom: 2px solid var(--ds-border);
}
.ds-acct-section-title i { color: var(--ds-primary); margin-right: 6px; }

/* ---------- EXPENSE / INCOME SIDEBAR ---------- */
.ds-sidebar-card {
    border-radius: var(--ds-radius);
    border: 1px solid var(--ds-border);
    margin-bottom: 14px;
    overflow: hidden;
}
.ds-sidebar-card .header {
    padding: 10px 14px;
    font-weight: 700;
    font-size: 13px;
    color: #fff;
}
.ds-sidebar-card .header.expense-header { background: linear-gradient(135deg,#f56565,#fc8181); }
.ds-sidebar-card .header.income-header  { background: linear-gradient(135deg,#48bb78,#68d391); }
.ds-sidebar-card .body { padding: 10px 14px; }
.ds-sidebar-card .item { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dotted #eee; font-size: 12px; }
.ds-sidebar-card .item:last-child { border-bottom: none; }
.ds-sidebar-card .item .label { color: var(--ds-text-muted); }
.ds-sidebar-card .item .value { font-weight: 700; }
.ds-sidebar-card .total-item { display: flex; justify-content: space-between; padding: 8px 0 0; font-weight: 700; font-size: 13px; border-top: 2px solid var(--ds-border); margin-top: 4px; }

/* ---------- MONTHLY TREND ---------- */
.ds-trend-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.ds-trend-table th { background: #f1f5f9; padding: 8px 10px; font-size: 11px; text-transform: uppercase; letter-spacing: .3px; color: var(--ds-text-muted); border-bottom: 2px solid var(--ds-border); white-space: nowrap; text-align: right; }
.ds-trend-table th:first-child { text-align: left; }
.ds-trend-table td { padding: 8px 10px; border-bottom: 1px solid #f0f0f0; text-align: right; white-space: nowrap; }
.ds-trend-table td:first-child { text-align: left; font-weight: 600; }
.ds-trend-table tbody tr:hover { background: #f7fafc; }

/* Chart Specific */
.ds-chart-container { position: relative; height: 280px; width: 100%; }

/* Responsive scroll for wide tables */
.ds-table-scroll { 
    overflow-x: auto; 
    -webkit-overflow-scrolling: touch; 
    max-height: 500px;
    overflow-y: auto;
    position: relative;
    border: 1px solid var(--ds-border);
    border-radius: 4px;
}
.ds-table-scroll thead th {
    position: sticky;
    top: 0;
    z-index: 10;
    box-shadow: inset 0 -2px 0 var(--ds-border);
}

/* Tab search */
.ds-tab-search {
    margin-bottom: 12px;
    display: flex;
    justify-content: flex-end;
}
.ds-tab-search .input-group {
    width: 250px;
}
.ds-tab-search input {
    height: 32px;
    font-size: 13px;
    border-radius: 6px 0 0 6px !important;
}
.ds-tab-search .input-group-addon {
    padding: 6px 12px;
    background: #f8fafc;
}

/* No data */
.ds-no-data { text-align: center; padding: 40px 20px; color: var(--ds-text-muted); font-size: 14px; }
.ds-no-data i { font-size: 40px; display: block; margin-bottom: 10px; opacity: .4; }

/* Loading Overlay */
.ds-loading { text-align: center; padding: 60px 20px; }
.ds-loading i { font-size: 32px; color: var(--ds-primary); }

/* ---------- EXPORT BUTTONS ---------- */
.ds-export-btns { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
.ds-export-btns .btn {
    border-radius: 6px;
    padding: 5px 12px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid var(--ds-border);
    background: #fff;
    color: var(--ds-text);
    transition: all .15s;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ds-export-btns .btn:hover { background: #f1f5f9; border-color: #cbd5e0; }
.ds-export-btns .btn-excel { color: #217346; border-color: #217346; }
.ds-export-btns .btn-excel:hover { background: #217346; color: #fff; }
.ds-export-btns .btn-csv { color: #2563eb; border-color: #2563eb; }
.ds-export-btns .btn-csv:hover { background: #2563eb; color: #fff; }
.ds-export-btns .btn-print { color: #7c3aed; border-color: #7c3aed; }
.ds-export-btns .btn-print:hover { background: #7c3aed; color: #fff; }
.ds-export-btns .btn-pdf { color: #dc2626; border-color: #dc2626; }
.ds-export-btns .btn-pdf:hover { background: #dc2626; color: #fff; }
.ds-global-export { display: flex; gap: 8px; margin-top: 8px; }
.ds-global-export .btn {
    border-radius: 6px; padding: 6px 16px; font-size: 12px; font-weight: 600; transition: all .15s;
}

/* Print */
@media print {
    .ds-filter-box, .ds-date-btns, .no-print, .ds-export-btns, .ds-global-export { display: none !important; }
    .ds-stat-card { box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .ds-section { box-shadow: none !important; break-inside: avoid; }
    .ds-table-scroll { max-height: none !important; overflow: visible !important; }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1 class="ds-page-title"><i class="fa fa-calendar-check-o"></i> Fee Collection Daysheet</h1>
    </section>

    <section class="content">
        <?php $this->load->view('financereports/_finance'); ?>

        <!-- ============ FILTER SECTION ============ -->
        <div class="ds-filter-box">
            <div class="ds-date-btns no-print">
                <button class="btn active" data-range="today">Today</button>
                <button class="btn" data-range="7days">Last 7 Days</button>
                <button class="btn" data-range="30days">This Month</button>
                <button class="btn" data-range="custom">Custom Range</button>
            </div>
            <div class="row" style="margin-top:12px;">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date From</label>
                        <input type="date" class="form-control" id="ds_date_from" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Date To</label>
                        <input type="date" class="form-control" id="ds_date_to" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Account</label>
                        <select class="form-control" id="ds_account">
                            <option value="all">All Accounts</option>
                            <?php if (!empty($accounts)) { foreach ($accounts as $acc) { ?>
                                <option value="<?php echo $acc['id']; ?>"><?php echo $acc['name']; ?></option>
                            <?php } } ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block" id="ds_search_btn" style="border-radius:6px;">
                            <i class="fa fa-search"></i> Get Daysheet
                        </button>
                    </div>
                    <div class="ds-global-export no-print" id="globalExportBtns" style="display:none;">
                        <button class="btn btn-default" onclick="printDaysheet()" title="Print entire daysheet"><i class="fa fa-print"></i> Print All</button>
                        <button class="btn btn-default" onclick="exportAllToExcel()" title="Export all tables to Excel"><i class="fa fa-file-excel-o"></i> Export All</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============ RESULTS (shown after search) ============ -->
        <div id="ds_results" style="display:none;">

            <!-- STAT CARDS -->
            <div class="row ds-stat-row">
                <div class="col-lg-3 col-sm-6">
                    <div class="ds-stat-card bg-gradient-blue">
                        <div class="inner">
                            <h3 id="stat_today_collection"><?php echo $currency_symbol; ?>0</h3>
                            <p>Today's Collection</p>
                        </div>
                        <div class="icon"><i class="fa fa-money"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="ds-stat-card bg-gradient-green">
                        <div class="inner">
                            <h3 id="stat_month_collection"><?php echo $currency_symbol; ?>0</h3>
                            <p>This Month</p>
                        </div>
                        <div class="icon"><i class="fa fa-calendar"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="ds-stat-card bg-gradient-purple">
                        <div class="inner">
                            <h3 id="stat_year_collection"><?php echo $currency_symbol; ?>0</h3>
                            <p>This Financial Year</p>
                        </div>
                        <div class="icon"><i class="fa fa-line-chart"></i></div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="ds-stat-card bg-gradient-red">
                        <div class="inner">
                            <h3 id="stat_today_expenses"><?php echo $currency_symbol; ?>0</h3>
                            <p>Today's Expenses</p>
                        </div>
                        <div class="icon"><i class="fa fa-arrow-circle-down"></i></div>
                    </div>
                </div>
            </div>

            <!-- DAY FEE COLLECTIONS GRID -->
            <div class="ds-section">
                <div class="ds-section-header">
                    <h4><i class="fa fa-table"></i> Day Fee Collections</h4>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <span id="ds_range_label" style="font-size:12px;color:var(--ds-text-muted);"></span>
                        <div class="ds-export-btns no-print">
                            <button class="btn btn-excel" onclick="exportTableToExcel('collectionGridTable','DayCollections')" title="Export to Excel"><i class="fa fa-file-excel-o"></i> Excel</button>
                            <button class="btn btn-csv" onclick="exportTableToCSV('collectionGridTable','DayCollections')" title="Export to CSV"><i class="fa fa-file-text-o"></i> CSV</button>
                        </div>
                    </div>
                </div>
                <div class="ds-section-body" style="padding:0;">
                    <div class="ds-table-scroll">
                        <table class="ds-grid-table" id="collectionGridTable">
                            <thead id="gridHead"></thead>
                            <tbody id="gridBody"></tbody>
                        </table>
                    </div>
                    <div class="ds-no-data" id="gridNoData" style="display:none;">
                        <i class="fa fa-inbox"></i> No collections found for the selected date range.
                    </div>
                </div>
            </div>

            <!-- TABBED DETAIL SECTION -->
            <div class="ds-section">
                <div class="ds-section-header" style="border-bottom:none;padding-bottom:0;">
                    <ul class="nav nav-tabs ds-tabs" role="tablist" style="border-bottom:none;margin:0;padding:0;background:transparent;">
                        <li class="active"><a href="#tab_detailed" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> Detailed</a></li>
                        <li><a href="#tab_feetype" role="tab" data-toggle="tab"><i class="fa fa-pie-chart"></i> Fee Type Wise</a></li>
                        <li><a href="#tab_account" role="tab" data-toggle="tab"><i class="fa fa-bank"></i> Account Wise</a></li>
                    </ul>
                    <div class="ds-export-btns no-print">
                        <button class="btn btn-excel" onclick="exportActiveTab('excel')" title="Export active tab to Excel"><i class="fa fa-file-excel-o"></i> Excel</button>
                        <button class="btn btn-csv" onclick="exportActiveTab('csv')" title="Export active tab to CSV"><i class="fa fa-file-text-o"></i> CSV</button>
                        <button class="btn btn-print" onclick="printSection('tabContent')" title="Print active tab"><i class="fa fa-print"></i> Print</button>
                    </div>
                </div>
                <div class="tab-content">
                    <!-- DETAILED TAB -->
                    <div class="tab-pane active" id="tab_detailed">
                        <div class="ds-section-body" style="padding:10px 16px;">
                            <div class="ds-tab-search">
                                <div class="input-group">
                                    <input type="text" id="detailedSearch" class="form-control" placeholder="Search students, receipts...">
                                    <span class="input-group-addon"><i class="fa fa-search"></i></span>
                                </div>
                            </div>
                            <div class="ds-table-scroll">
                                <table class="ds-data-table" id="detailedTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Date</th>
                                            <th>Receipt No</th>
                                            <th>Student</th>
                                            <th>Adm No</th>
                                            <th>Class/Section</th>
                                            <th>Description</th>
                                            <th>Account</th>
                                            <th class="text-right">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detailedBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- FEE TYPE WISE TAB -->
                    <div class="tab-pane" id="tab_feetype">
                        <div class="ds-section-body" style="padding:10px 16px;">
                            <div class="ds-table-scroll">
                                <table class="ds-data-table">
                                    <thead>
                                        <tr>
                                                <th>#</th>
                                                <th colspan="2">Groups & Fee Types</th>
                                                <th class="text-right">Receipts</th>
                                                <th class="text-right">Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="feeTypeBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- ACCOUNT WISE TAB -->
                    <div class="tab-pane" id="tab_account">
                        <div class="ds-section-body" style="padding:10px 16px;">
                            <div class="ds-table-scroll">
                                <table class="ds-data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Account</th>
                                            <th class="text-right">Credits (+)</th>
                                            <th class="text-right">Debits (-)</th>
                                            <th class="text-right">Net</th>
                                            <th class="text-right">Transactions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="accountBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ACCOUNT BALANCE + EXPENSES ROW -->
            <div class="row">
                <!-- ACCOUNT BALANCES (left 8 cols) -->
                <div class="col-md-8">
                    <div class="ds-section">
                        <div class="ds-section-header">
                            <h4><i class="fa fa-balance-scale"></i> Account Balance Summary</h4>
                            <div class="ds-export-btns no-print">
                                <button class="btn btn-excel" onclick="exportAccountBalance('excel')" title="Export to Excel"><i class="fa fa-file-excel-o"></i> Excel</button>
                                <button class="btn btn-csv" onclick="exportAccountBalance('csv')" title="Export to CSV"><i class="fa fa-file-text-o"></i> CSV</button>
                            </div>
                        </div>
                        <div class="ds-section-body">
                            <!-- Consolidated Accounts -->
                            <div id="accountSummarySection">
                                <h5 class="ds-acct-section-title"><i class="fa fa-university"></i> Accounts Balance Summary</h5>
                                <div class="ds-table-scroll">
                                    <table class="ds-data-table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Account</th>
                                                <th class="text-right">Opening Bal (+)</th>
                                                <th class="text-right">Collections (+)</th>
                                                <th class="text-right">Withdrawals (-)</th>
                                                <th class="text-right">Transfers In (+)</th>
                                                <th class="text-right">Transfers Out (-)</th>
                                                <th class="text-right">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody id="accountBalanceBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- EXPENSES & INCOME SIDEBAR (right 4 cols) -->
                <div class="col-md-4">
                    <!-- Expenses Card -->
                    <div class="ds-sidebar-card">
                        <div class="header expense-header"><i class="fa fa-arrow-circle-down"></i> Expenses</div>
                        <div class="body" id="expenseSidebar">
                            <div class="ds-no-data" style="padding:20px 0;"><i class="fa fa-check-circle"></i> No expenses</div>
                        </div>
                    </div>
                    <!-- Income Card -->
                    <div class="ds-sidebar-card">
                        <div class="header income-header"><i class="fa fa-arrow-circle-up"></i> Income</div>
                        <div class="body" id="incomeSidebar">
                            <div class="ds-no-data" style="padding:20px 0;"><i class="fa fa-check-circle"></i> No income</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MONTHLY TREND -->
            <div class="ds-section">
                <div class="ds-section-header">
                    <h4><i class="fa fa-line-chart"></i> Fee Collection Trend (Financial Year)</h4>
                    <div class="ds-export-btns no-print">
                        <button class="btn btn-excel" onclick="exportTrendToExcel()" title="Export to Excel"><i class="fa fa-file-excel-o"></i> Excel</button>
                        <button class="btn btn-csv" onclick="exportTrendToCSV()" title="Export to CSV"><i class="fa fa-file-text-o"></i> CSV</button>
                    </div>
                </div>
                <div class="ds-section-body">
                    <div class="ds-table-scroll">
                        <table class="ds-trend-table">
                            <thead id="trendHead"></thead>
                            <tbody id="trendBody"></tbody>
                        </table>
                    </div>
                    <div style="margin-top:20px;">
                        <div class="ds-chart-container">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /ds_results -->

        <!-- INITIAL STATE -->
        <div id="ds_initial" class="ds-section" style="margin-top:10px;">
            <div class="ds-no-data" style="padding:60px 20px;">
                <i class="fa fa-calendar-check-o" style="font-size:50px;"></i>
                <p style="font-size:16px;margin-top:10px;">Select a date range and click <strong>Get Daysheet</strong> to view the collection report.</p>
            </div>
        </div>

        <!-- FEE TYPE DETAILS MODAL -->
        <div class="modal fade" id="feeDetailsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="border-radius: var(--ds-radius); overflow: hidden;">
                    <div class="modal-header" style="background: linear-gradient(135deg, var(--ds-primary), var(--ds-primary-dark)); color: #fff; border-bottom: none;">
                        <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 1; font-size: 28px; font-weight: 300; margin-top: -5px;">&times;</button>
                        <h4 class="modal-title" style="font-weight: 700; display: flex; align-items: center;">
                            <i class="fa fa-list-alt" style="margin-right:12px; opacity: .8;"></i> 
                            <span id="modalHeaderTitle">Receipt Details</span>
                        </h4>
                    </div>
                    <div class="modal-body" style="padding: 0; max-height: 70vh; overflow-y: auto;">
                        <div class="ds-table-scroll">
                            <table class="ds-data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt</th>
                                        <th>Student</th>
                                        <th>Adm No</th>
                                        <th>Account</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="modalBodyContent"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 10px 20px; border-top: 1px solid var(--ds-border);">
                        <button type="button" class="btn btn-default" data-dismiss="modal" style="border-radius: 6px; font-weight: 600; padding: 6px 20px;">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>

<script src="<?php echo base_url(); ?>backend/js/Chart.min.js"></script>
<script>
// Save Chart.js v2.8.0 before footer loads v1.0.2
if (window.Chart && window.Chart.defaults && window.Chart.defaults.global) {
    window.ChartV2 = window.Chart;
}
</script>
<script>
var currency_symbol = '<?php echo $currency_symbol; ?>';
var baseurl = '<?php echo base_url(); ?>';
var currentDetailedReceipts = [];
var trendChartInstance = null;

$(document).ready(function() {

    // ===== DATE QUICK BUTTONS =====
    $('.ds-date-btns .btn').on('click', function() {
        $('.ds-date-btns .btn').removeClass('active');
        $(this).addClass('active');
        var range = $(this).data('range');
        var today = new Date();
        var from  = new Date();

        if (range === 'today') {
            // from = today
        } else if (range === '7days') {
            from.setDate(today.getDate() - 6);
        } else if (range === '30days') {
            from = new Date(today.getFullYear(), today.getMonth(), 1);
        } else {
            return;
        }

        $('#ds_date_from').val(formatDateISO(from));
        $('#ds_date_to').val(formatDateISO(today));
    });

    // ===== SEARCH BUTTON =====
    $('#ds_search_btn').on('click', function() {
        loadDaysheet();
    });

    // Auto-load today on page load
    loadDaysheet();
});

function formatDateISO(d) {
    return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
}

function formatNumber(num) {
    return parseFloat(num || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

function formatDateDisplay(dateStr) {
    var d = new Date(dateStr + 'T00:00:00');
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return String(d.getDate()).padStart(2,'0') + ' ' + months[d.getMonth()];
}

function loadDaysheet() {
    var from = $('#ds_date_from').val();
    var to   = $('#ds_date_to').val();
    var acct = $('#ds_account').val();

    $('#ds_search_btn').html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);
    $('#ds_initial').hide();

    $.ajax({
        url: baseurl + 'financereports/getDaysheetData',
        type: 'POST',
        dataType: 'json',
        data: { from_date: from, to_date: to, account_id: acct },
        success: function(resp) {
            if (resp.status === 1) {
                currentDetailedReceipts = resp.detailed_receipts || [];
                $('#ds_results').show();
                renderStats(resp.stats);
                renderCollectionGrid(resp.grid_dates, resp.grid_data);
                renderDetailedTable(resp.detailed_receipts);
                renderFeeTypeTable(resp.feetype_consolidated);
                renderAccountTable(resp.account_consolidated);
                renderAccountBalances(resp.cash_summaries, resp.bank_summaries);
                renderExpenseIncome(resp.expenses_by_head, resp.income_by_head);
                renderMonthlyTrend(resp.monthly_trend);
                $('#ds_range_label').text(formatDateDisplay(from) + ' – ' + formatDateDisplay(to));
                $('#globalExportBtns').show();
            } else {
                alert(resp.message || 'Error loading data');
            }
        },
        error: function(xhr) {
            console.error('AJAX Error', xhr.responseText);
            alert('Error loading daysheet data. Please try again.');
        },
        complete: function() {
            $('#ds_search_btn').html('<i class="fa fa-search"></i> Get Daysheet').prop('disabled', false);
        }
    });
}

// ===== RENDER STAT CARDS =====
function renderStats(s) {
    $('#stat_today_collection').text(currency_symbol + formatNumber(s.today_collection));
    $('#stat_month_collection').text(currency_symbol + formatNumber(s.month_collection));
    $('#stat_year_collection').text(currency_symbol + formatNumber(s.year_collection));
    $('#stat_today_expenses').text(currency_symbol + formatNumber(s.today_expenses));
}

// ===== RENDER COLLECTION GRID =====
function renderCollectionGrid(dates, data) {
    if (!data || data.length === 0) {
        $('#collectionGridTable').hide();
        $('#gridNoData').show();
        return;
    }
    $('#collectionGridTable').show();
    $('#gridNoData').hide();

    // Header
    var hdr = '<tr><th style="text-align:left;">Collection Account</th>';
    $.each(dates, function(i, d) {
        hdr += '<th class="text-right">' + formatDateDisplay(d) + '</th>';
    });
    hdr += '<th class="text-right" style="background:#e8edf2;">Total</th></tr>';
    $('#gridHead').html(hdr);

    // Body
    var html = '';
    var dayTotals = {};
    $.each(dates, function(i, d) { dayTotals[d] = 0; });
    var grandTotal = 0;

    $.each(data, function(i, row) {
        html += '<tr>';
        html += '<td class="acct-name" title="' + row.account_name + '">' + row.account_name + '</td>';
        $.each(dates, function(j, d) {
            var amt = row.daily[d] || 0;
            dayTotals[d] += amt;
            html += '<td class="amount-cell' + (amt > 0 ? ' has-value' : '') + '">' + (amt > 0 ? currency_symbol + formatNumber(amt) : '0.00') + '</td>';
        });
        grandTotal += row.total;
        html += '<td class="amount-cell has-value" style="background:#f8fafc;font-weight:700;">' + currency_symbol + formatNumber(row.total) + '</td>';
        html += '</tr>';
    });

    // Total row
    html += '<tr class="total-row">';
    html += '<td style="font-weight:700;">Day Total</td>';
    $.each(dates, function(j, d) {
        html += '<td class="amount-cell">' + currency_symbol + formatNumber(dayTotals[d]) + '</td>';
    });
    html += '<td class="amount-cell grand-total">' + currency_symbol + formatNumber(grandTotal) + '</td>';
    html += '</tr>';

    $('#gridBody').html(html);
}

// ===== DETAILED RECEIPTS TAB =====
var currentDetailedReceipts = []; // Global storage for filtering

function renderDetailedTable(receipts) {
    var html = '';
    if (!receipts || receipts.length === 0) {
        html = '<tr><td colspan="9" class="ds-no-data"><i class="fa fa-inbox"></i> No records to display</td></tr>';
    } else {
        var total = 0;
        $.each(receipts, function(i, r) {
            var studentName = (r.firstname || '') + ' ' + (r.lastname || '');
            var classSection = (r.class || '-') + ' / ' + (r.section || '-');
            total += parseFloat(r.amount || 0);
            html += '<tr>';
            html += '<td>' + (i+1) + '</td>';
            html += '<td>' + (r.date || '-') + '</td>';
            html += '<td>' + (r.receiptid || '-') + '</td>';
            html += '<td>' + (studentName.trim() || '-') + '</td>';
            html += '<td>' + (r.admission_no || '-') + '</td>';
            html += '<td>' + classSection + '</td>';
            html += '<td>' + (r.description || r.type || '-') + '</td>';
            html += '<td>' + (r.account_name || '-') + '</td>';
            html += '<td class="text-right">' + currency_symbol + formatNumber(r.amount) + '</td>';
            html += '</tr>';
        });
        html += '<tr class="total-row" style="position: sticky; bottom: 0; background: #f8fafc; z-index: 5;"><td colspan="8" class="text-right"><strong>Total</strong></td><td class="text-right"><strong>' + currency_symbol + formatNumber(total) + '</strong></td></tr>';
    }
    $('#detailedBody').html(html);
}

// Search handling for detailed table
$(document).on('keyup', '#detailedSearch', function() {
    var val = $(this).val().toLowerCase();
    var filtered = currentDetailedReceipts.filter(function(r) {
        var studentName = ((r.firstname || '') + ' ' + (r.lastname || '')).toLowerCase();
        return studentName.indexOf(val) > -1 || 
               (r.receiptid || '').toLowerCase().indexOf(val) > -1 || 
               (r.admission_no || '').toLowerCase().indexOf(val) > -1 ||
               (r.description || '').toLowerCase().indexOf(val) > -1 ||
               (r.account_name || '').toLowerCase().indexOf(val) > -1;
    });
    renderDetailedTable(filtered);
});

// ===== FEE TYPE WISE TAB (Grouped by Fee Group) =====
function renderFeeTypeTable(data) {
    var html = '';
    if (!data || data.length === 0) {
        html = '<tr><td colspan="5" class="ds-no-data"><i class="fa fa-inbox"></i> No records</td></tr>';
    } else {
        // Group data by fee group
        var groups = {};
        $.each(data, function(i, r) {
            var gName = r.fee_group_name || 'Unassigned';
            if (!groups[gName]) {
                groups[gName] = { types: [], totalAmt: 0, totalCount: 0 };
            }
            groups[gName].types.push(r);
            groups[gName].totalAmt += parseFloat(r.total_amount || 0);
            groups[gName].totalCount += parseInt(r.receipt_count || 0);
        });

        var grandTotalAmt = 0, grandTotalCount = 0;
        var sNo = 1;

        // Sort group names and iterate
        Object.keys(groups).sort().forEach(function(gName) {
            var group = groups[gName];
            grandTotalAmt += group.totalAmt;
            grandTotalCount += group.totalCount;

            // Group Header Row
            html += '<tr style="background:#f8fafc; font-weight:700; border-top:2px solid #e2e8f0;">';
            html += '<td colspan="3"><i class="fa fa-folder-open-o" style="color:var(--ds-primary);margin-right:8px;"></i>' + gName + '</td>';
            html += '<td class="text-right">' + group.totalCount + '</td>';
            html += '<td class="text-right">' + currency_symbol + formatNumber(group.totalAmt) + '</td>';
            html += '</tr>';

            // Fee Type Rows under this group
            $.each(group.types, function(j, r) {
                html += '<tr>';
                html += '<td style="padding-left:30px; color:var(--ds-text-muted); font-size:11px;">' + (sNo++) + '</td>';
                html += '<td colspan="2" style="padding-left:30px;">' + (r.fee_type_name || '-') + '</td>';
                html += '<td class="text-right"><a href="javascript:void(0)" onclick="showReceiptDetails(\'' + r.fgft_id + '\', \'' + (r.fee_group_name + ' / ' + r.fee_type_name) + '\')" style="font-weight:600; text-decoration:underline; color:var(--ds-primary);">' + (r.receipt_count || 0) + '</a></td>';
                html += '<td class="text-right">' + currency_symbol + formatNumber(r.total_amount) + '</td>';
                html += '</tr>';
            });
        });

        // Grand Total Row
        html += '<tr class="total-row" style="position: sticky; bottom: 0; background: #edf2f7; border-top: 2px solid var(--ds-primary); z-index: 5;">';
        html += '<td colspan="3" class="text-right"><strong>Grand Total</strong></td>';
        html += '<td class="text-right"><strong>' + grandTotalCount + '</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(grandTotalAmt) + '</strong></td>';
        html += '</tr>';
    }
    $('#feeTypeBody').html(html);
}

// ===== SHOW RECEIPT DETAILS MODAL =====
function showReceiptDetails(fgftId, title) {
    var filtered = currentDetailedReceipts.filter(function(r) {
        return String(r.fee_groups_feetype_id) === String(fgftId);
    });

    var html = '';
    if (filtered.length === 0) {
        html = '<tr><td colspan="6" class="ds-no-data">No details available for this selection.</td></tr>';
    } else {
        $.each(filtered, function(i, r) {
            var studentName = (r.firstname || '') + ' ' + (r.lastname || '');
            html += '<tr>';
            html += '<td>' + (r.date || '-') + '</td>';
            html += '<td>' + (r.receiptid || '-') + '</td>';
            html += '<td>' + studentName.trim() + '</td>';
            html += '<td>' + (r.admission_no || '-') + '</td>';
            html += '<td>' + (r.account_name || '-') + '</td>';
            html += '<td class="text-right">' + currency_symbol + formatNumber(r.amount) + '</td>';
            html += '</tr>';
        });
    }

    $('#modalHeaderTitle').text(title);
    $('#modalBodyContent').html(html);
    $('#feeDetailsModal').modal('show');
}

// ===== ACCOUNT CONSOLIDATED TAB =====
function renderAccountTable(data) {
    var html = '';
    if (!data || data.length === 0) {
        html = '<tr><td colspan="6" class="ds-no-data"><i class="fa fa-inbox"></i> No records</td></tr>';
    } else {
        var totalCr = 0, totalDr = 0, totalTx = 0;
        $.each(data, function(i, r) {
            var cr = parseFloat(r.total_credit || 0);
            var dr = parseFloat(r.total_debit || 0);
            totalCr += cr; totalDr += dr; totalTx += parseInt(r.transaction_count || 0);
            html += '<tr>';
            html += '<td>' + (i+1) + '</td>';
            html += '<td>' + (r.account_name || '-') + '</td>';
            html += '<td class="text-right" style="color:var(--ds-success);">' + currency_symbol + formatNumber(cr) + '</td>';
            html += '<td class="text-right" style="color:var(--ds-danger);">' + currency_symbol + formatNumber(dr) + '</td>';
            html += '<td class="text-right" style="font-weight:700;">' + currency_symbol + formatNumber(cr - dr) + '</td>';
            html += '<td class="text-right">' + r.transaction_count + '</td>';
            html += '</tr>';
        });
        html += '<tr class="total-row" style="position: sticky; bottom: 0; background: #edf2f7; border-top: 2px solid var(--ds-primary); z-index: 5;"><td colspan="2" class="text-right"><strong>Total</strong></td>';
        html += '<td class="text-right" style="color:var(--ds-success);"><strong>' + currency_symbol + formatNumber(totalCr) + '</strong></td>';
        html += '<td class="text-right" style="color:var(--ds-danger);"><strong>' + currency_symbol + formatNumber(totalDr) + '</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(totalCr - totalDr) + '</strong></td>';
        html += '<td class="text-right"><strong>' + totalTx + '</strong></td></tr>';
    }
    $('#accountBody').html(html);
}

// ===== ACCOUNT BALANCE SUMMARY =====
function renderAccountBalances(cash, bank) {
    var combined = (cash || []).concat(bank || []);
    renderBalanceTable('accountBalanceBody', combined);
}

function renderBalanceTable(bodyId, data) {
    var html = '';
    if (!data || data.length === 0) {
        html = '<tr><td colspan="8" class="ds-no-data" style="padding:15px;"><i class="fa fa-check-circle"></i> No activity</td></tr>';
    } else {
        var totals = {ob:0, col:0, wd:0, ti:0, to:0, bal:0};
        $.each(data, function(i, r) {
            totals.ob  += r.opening_balance;
            totals.col += r.collections;
            totals.wd  += r.withdrawals;
            totals.ti  += r.transfers_in;
            totals.to  += r.transfers_out;
            totals.bal += r.closing_balance;
            html += '<tr>';
            html += '<td>' + (i+1) + '</td>';
            html += '<td>' + r.account_name + '</td>';
            html += '<td class="text-right">' + currency_symbol + formatNumber(r.opening_balance) + '</td>';
            html += '<td class="text-right" style="color:var(--ds-success);">' + currency_symbol + formatNumber(r.collections) + '</td>';
            html += '<td class="text-right" style="color:var(--ds-danger);">' + currency_symbol + formatNumber(r.withdrawals) + '</td>';
            html += '<td class="text-right" style="color:var(--ds-info);">' + currency_symbol + formatNumber(r.transfers_in) + '</td>';
            html += '<td class="text-right" style="color:var(--ds-warning);">' + currency_symbol + formatNumber(r.transfers_out) + '</td>';
            html += '<td class="text-right" style="font-weight:700;">' + currency_symbol + formatNumber(r.closing_balance) + '</td>';
            html += '</tr>';
        });
        html += '<tr class="total-row" style="position: sticky; bottom: 0; background: #f8fafc; z-index: 5; border-top: 2px solid var(--ds-border);">';
        html += '<td colspan="2" class="text-right"><strong>Total</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(totals.ob) + '</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(totals.col) + '</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(totals.wd) + '</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(totals.ti) + '</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(totals.to) + '</strong></td>';
        html += '<td class="text-right"><strong>' + currency_symbol + formatNumber(totals.bal) + '</strong></td>';
        html += '</tr>';
    }
    $('#' + bodyId).html(html);
}

// ===== EXPENSE / INCOME SIDEBAR =====
function renderExpenseIncome(expenses, income) {
    // Expenses
    if (expenses && expenses.length > 0) {
        var html = '';
        var total = 0;
        $.each(expenses, function(i, e) {
            var amt = parseFloat(e.total_amount || 0);
            total += amt;
            html += '<div class="item"><span class="label">' + (e.exp_category || 'Other') + '</span><span class="value" style="color:var(--ds-danger);">' + currency_symbol + formatNumber(amt) + '</span></div>';
        });
        html += '<div class="total-item"><span>Total Expenses</span><span style="color:var(--ds-danger);">' + currency_symbol + formatNumber(total) + '</span></div>';
        $('#expenseSidebar').html(html);
    } else {
        $('#expenseSidebar').html('<div class="ds-no-data" style="padding:15px 0;font-size:12px;"><i class="fa fa-check-circle" style="font-size:20px;"></i> No expenses in this period</div>');
    }

    // Income
    if (income && income.length > 0) {
        var html = '';
        var total = 0;
        $.each(income, function(i, inc) {
            var amt = parseFloat(inc.total_amount || 0);
            total += amt;
            html += '<div class="item"><span class="label">' + (inc.income_category || 'Other') + '</span><span class="value" style="color:var(--ds-success);">' + currency_symbol + formatNumber(amt) + '</span></div>';
        });
        html += '<div class="total-item"><span>Total Income</span><span style="color:var(--ds-success);">' + currency_symbol + formatNumber(total) + '</span></div>';
        $('#incomeSidebar').html(html);
    } else {
        $('#incomeSidebar').html('<div class="ds-no-data" style="padding:15px 0;font-size:12px;"><i class="fa fa-check-circle" style="font-size:20px;"></i> No income in this period</div>');
    }
}

// ===== MONTHLY TREND =====
function renderMonthlyTrend(data) {
    if (!data || data.length === 0) {
        $('#trendHead').html('');
        $('#trendBody').html('<tr><td class="ds-no-data"><i class="fa fa-inbox"></i> No trend data</td></tr>');
        return;
    }

    // Table
    var hdr = '<tr><th style="text-align:left;">Month</th><th>Transactions</th><th>Amount</th></tr>';
    $('#trendHead').html(hdr);

    var html = '';
    var labels = [], values = [];
    $.each(data, function(i, m) {
        labels.push(m.month_label);
        values.push(parseFloat(m.total_amount));
        html += '<tr>';
        html += '<td>' + m.month_label + '</td>';
        html += '<td style="text-align:right;">' + m.transaction_count + '</td>';
        html += '<td style="text-align:right;">' + currency_symbol + formatNumber(m.total_amount) + '</td>';
        html += '</tr>';
    });
    $('#trendBody').html(html);

    // Chart
    renderTrendChart(labels, values);
}

function renderTrendChart(labels, values) {
    if (trendChartInstance) trendChartInstance.destroy();

    var ctx = document.getElementById('trendChart').getContext('2d');
    var ChartLib = window.ChartV2 || window.Chart;

    trendChartInstance = new ChartLib(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Collection Amount',
                data: values,
                backgroundColor: 'rgba(102,126,234,0.15)',
                borderColor: 'rgba(102,126,234,1)',
                borderWidth: 2,
                pointRadius: 4,
                pointBackgroundColor: 'rgba(102,126,234,1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(v) { return currency_symbol + formatNumber(v); }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(t) { return currency_symbol + formatNumber(t.yLabel); }
                }
            }
        }
    });
}

// =========================================================================
// EXPORT UTILITIES
// =========================================================================

/**
 * Extract data from an HTML table into a 2D array
 */
function tableToArray(tableId) {
    var rows = [];
    var $table = $('#' + tableId);
    if (!$table.length) return rows;

    $table.find('thead tr, tbody tr').each(function() {
        var row = [];
        $(this).find('th, td').each(function() {
            var txt = $(this).text().replace(/\s+/g, ' ').trim();
            row.push(txt);
        });
        if (row.length > 0) rows.push(row);
    });
    return rows;
}

/**
 * Convert 2D array to CSV string
 */
function arrayToCSV(data) {
    return data.map(function(row) {
        return row.map(function(cell) {
            // Escape quotes and wrap in quotes if contains comma/newline
            var s = String(cell).replace(/"/g, '""');
            if (s.indexOf(',') > -1 || s.indexOf('\n') > -1 || s.indexOf('"') > -1) {
                s = '"' + s + '"';
            }
            return s;
        }).join(',');
    }).join('\n');
}

/**
 * Trigger download of a file
 */
function downloadFile(content, filename, mimeType) {
    var blob = new Blob([content], { type: mimeType + ';charset=utf-8;' });
    var link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(link.href);
}

/**
 * Get date range string for filenames
 */
function getDateRangeLabel() {
    var from = $('#ds_date_from').val() || 'today';
    var to   = $('#ds_date_to').val() || 'today';
    return from + '_to_' + to;
}

// ---- EXPORT TABLE TO CSV ----
function exportTableToCSV(tableId, sheetName) {
    var data = tableToArray(tableId);
    if (data.length === 0) { alert('No data to export.'); return; }
    var csv = arrayToCSV(data);
    var filename = sheetName + '_' + getDateRangeLabel() + '.csv';
    downloadFile(csv, filename, 'text/csv');
}

// ---- EXPORT TABLE TO EXCEL (HTML table → .xls) ----
function exportTableToExcel(tableId, sheetName) {
    var $table = $('#' + tableId);
    if (!$table.length || $table.find('tbody tr').length === 0) { alert('No data to export.'); return; }

    var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    html += '<head><meta charset="utf-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
    html += '<x:Name>' + sheetName + '</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head>';
    html += '<body>';
    html += '<h3>' + sheetName + ' (' + getDateRangeLabel().replace(/_/g, ' ') + ')</h3>';
    html += '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-family:Arial;font-size:12px;">';
    html += $table.html().replace(/<a[^>]*>|<\/a>/g, '').replace(/<button[^>]*>.*?<\/button>/g, '');
    html += '</table></body></html>';

    var filename = sheetName + '_' + getDateRangeLabel() + '.xls';
    downloadFile(html, filename, 'application/vnd.ms-excel');
}

// ---- EXPORT ACTIVE TAB ----
function exportActiveTab(format) {
    var activePane = $('.tab-content .tab-pane.active');
    var $table = activePane.find('table.ds-data-table');
    if (!$table.length) { alert('No data to export.'); return; }

    var tabName = 'DetailedReceipts';
    if (activePane.attr('id') === 'tab_feetype')  tabName = 'FeeTypeWise';
    if (activePane.attr('id') === 'tab_account')  tabName = 'AccountWise';

    // Give table a temp id for export
    var tempId = 'export_temp_' + Date.now();
    $table.attr('id', tempId);

    if (format === 'excel') {
        exportTableToExcel(tempId, tabName);
    } else {
        exportTableToCSV(tempId, tabName);
    }
    $table.removeAttr('id');
}

// ---- EXPORT ACCOUNT BALANCE ----
function exportAccountBalance(format) {
    // Build a combined array from the account balance table
    var $section = $('#accountSummarySection');
    var $table = $section.find('table.ds-data-table');
    if (!$table.length) { alert('No data to export.'); return; }

    var tempId = 'export_acct_' + Date.now();
    $table.attr('id', tempId);
    if (format === 'excel') {
        exportTableToExcel(tempId, 'AccountBalance');
    } else {
        exportTableToCSV(tempId, 'AccountBalance');
    }
    $table.removeAttr('id');
}

// ---- EXPORT MONTHLY TREND ----
function exportTrendToExcel() {
    var $table = $('.ds-trend-table');
    if (!$table.length || $table.find('tbody tr').length === 0) { alert('No data to export.'); return; }
    var tempId = 'export_trend_' + Date.now();
    $table.attr('id', tempId);
    exportTableToExcel(tempId, 'MonthlyTrend');
    $table.removeAttr('id');
}
function exportTrendToCSV() {
    var $table = $('.ds-trend-table');
    if (!$table.length || $table.find('tbody tr').length === 0) { alert('No data to export.'); return; }
    var tempId = 'export_trend_' + Date.now();
    $table.attr('id', tempId);
    exportTableToCSV(tempId, 'MonthlyTrend');
    $table.removeAttr('id');
}

// ---- EXPORT ALL TABLES TO A SINGLE EXCEL ----
function exportAllToExcel() {
    var html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    html += '<head><meta charset="utf-8"></head><body>';
    html += '<h2>Fee Collection Daysheet - ' + getDateRangeLabel().replace(/_/g, ' ') + '</h2>';

    // 1. Collection Grid
    var $grid = $('#collectionGridTable');
    if ($grid.length && $grid.find('tbody tr').length > 0) {
        html += '<h3>Day Fee Collections</h3>';
        html += buildExcelTable($grid);
    }

    // 2. Detailed Receipts
    var $detailed = $('#tab_detailed table.ds-data-table');
    if ($detailed.length && $detailed.find('tbody tr').length > 0) {
        html += '<br><h3>Detailed Receipts</h3>';
        html += buildExcelTable($detailed);
    }

    // 3. Fee Type Wise
    var $feetype = $('#tab_feetype table.ds-data-table');
    if ($feetype.length && $feetype.find('tbody tr').length > 0) {
        html += '<br><h3>Fee Type Wise</h3>';
        html += buildExcelTable($feetype);
    }

    // 4. Account Wise
    var $acctwise = $('#tab_account table.ds-data-table');
    if ($acctwise.length && $acctwise.find('tbody tr').length > 0) {
        html += '<br><h3>Account Wise</h3>';
        html += buildExcelTable($acctwise);
    }

    // 5. Account Balance
    var $balance = $('#accountSummarySection table.ds-data-table');
    if ($balance.length && $balance.find('tbody tr').length > 0) {
        html += '<br><h3>Account Balance Summary</h3>';
        html += buildExcelTable($balance);
    }

    // 6. Monthly Trend
    var $trend = $('.ds-trend-table');
    if ($trend.length && $trend.find('tbody tr').length > 0) {
        html += '<br><h3>Monthly Trend</h3>';
        html += buildExcelTable($trend);
    }

    html += '</body></html>';
    downloadFile(html, 'Daysheet_Complete_' + getDateRangeLabel() + '.xls', 'application/vnd.ms-excel');
}

function buildExcelTable($table) {
    var tableHtml = '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-family:Arial;font-size:11px;">';
    tableHtml += $table.html().replace(/<a[^>]*>|<\/a>/g, '').replace(/<button[^>]*>.*?<\/button>/g, '').replace(/<i[^>]*>.*?<\/i>/g, '');
    tableHtml += '</table>';
    return tableHtml;
}

// ---- PRINT ENTIRE DAYSHEET ----
function printDaysheet() {
    var $results = $('#ds_results').clone();
    // Remove export buttons and no-print elements
    $results.find('.no-print, .ds-export-btns, .ds-global-export, .ds-tab-search').remove();
    // Show all tab panes for printing
    $results.find('.tab-pane').addClass('active in').css('display', 'block');
    $results.find('.ds-table-scroll').css({'max-height': 'none', 'overflow': 'visible'});

    var printWin = window.open('', '_blank', 'width=1100,height=800');
    printWin.document.write('<html><head><title>Fee Collection Daysheet</title>');
    printWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/bootstrap/css/bootstrap.min.css">');
    printWin.document.write('<style>');
    printWin.document.write('body { font-family: Arial, sans-serif; padding: 15px; font-size: 12px; }');
    printWin.document.write('h2 { text-align: center; margin-bottom: 20px; }');
    printWin.document.write('table { width: 100%; border-collapse: collapse; margin-bottom: 20px; page-break-inside: auto; }');
    printWin.document.write('th, td { border: 1px solid #ccc; padding: 6px 8px; font-size: 11px; }');
    printWin.document.write('th { background: #f5f5f5; font-weight: 600; }');
    printWin.document.write('.ds-stat-card { display: inline-block; width: 23%; margin: 0.5%; padding: 12px; border-radius: 8px; color: #fff; vertical-align: top; }');
    printWin.document.write('.ds-stat-card h3 { margin: 0 0 4px; font-size: 18px; } .ds-stat-card p { margin: 0; font-size: 11px; }');
    printWin.document.write('.bg-gradient-blue { background: #667eea; } .bg-gradient-green { background: #48bb78; } .bg-gradient-purple { background: #805ad5; } .bg-gradient-red { background: #f56565; }');
    printWin.document.write('.ds-section { border: 1px solid #ddd; margin-bottom: 16px; border-radius: 6px; overflow: hidden; }');
    printWin.document.write('.ds-section-header { background: #f8f9fa; padding: 8px 14px; border-bottom: 1px solid #ddd; } .ds-section-header h4 { margin: 0; font-size: 14px; }');
    printWin.document.write('.ds-section-body { padding: 10px 14px; }');
    printWin.document.write('.ds-acct-section-title { font-weight: 700; font-size: 13px; border-bottom: 2px solid #ddd; padding-bottom: 4px; margin: 14px 0 8px; }');
    printWin.document.write('.icon { display: none; }');
    printWin.document.write('.ds-sidebar-card { border: 1px solid #ddd; margin-bottom: 10px; border-radius: 6px; overflow: hidden; }');
    printWin.document.write('.ds-sidebar-card .header { padding: 6px 10px; color: #fff; font-weight: 700; font-size: 12px; }');
    printWin.document.write('.expense-header { background: #f56565; } .income-header { background: #48bb78; }');
    printWin.document.write('.ds-sidebar-card .body { padding: 6px 10px; } .ds-sidebar-card .item { display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px dotted #eee; font-size: 11px; }');
    printWin.document.write('.total-row { background: #f1f5f9; font-weight: 700; }');
    printWin.document.write('.text-right { text-align: right; }');
    printWin.document.write('.ds-tabs { display: none; }');
    printWin.document.write('.has-value { color: #217346; font-weight: 600; }');
    printWin.document.write('@media print { @page { margin: 8mm; size: A4 landscape; } * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; } }');
    printWin.document.write('</style></head><body>');
    printWin.document.write('<h2>Fee Collection Daysheet</h2>');
    printWin.document.write('<p style="text-align:center;color:#666;font-size:12px;">Date Range: ' + ($('#ds_date_from').val() || '') + ' to ' + ($('#ds_date_to').val() || '') + '</p>');
    printWin.document.write($results.html());
    printWin.document.write('</body></html>');
    printWin.document.close();
    setTimeout(function() { printWin.print(); }, 600);
}

// ---- PRINT SPECIFIC SECTION ----
function printSection(sectionType) {
    var $content;
    var title = 'Daysheet Section';

    if (sectionType === 'tabContent') {
        var activePane = $('.tab-content .tab-pane.active');
        $content = activePane.clone();
        if (activePane.attr('id') === 'tab_detailed')  title = 'Detailed Receipts';
        if (activePane.attr('id') === 'tab_feetype')   title = 'Fee Type Wise Summary';
        if (activePane.attr('id') === 'tab_account')   title = 'Account Wise Summary';
    }

    $content.find('.no-print, .ds-export-btns, .ds-tab-search').remove();
    $content.find('.ds-table-scroll').css({'max-height': 'none', 'overflow': 'visible'});

    var printWin = window.open('', '_blank', 'width=1000,height=700');
    printWin.document.write('<html><head><title>' + title + '</title>');
    printWin.document.write('<link rel="stylesheet" href="' + baseurl + 'backend/bootstrap/css/bootstrap.min.css">');
    printWin.document.write('<style>');
    printWin.document.write('body { font-family: Arial, sans-serif; padding: 20px; }');
    printWin.document.write('table { width: 100%; border-collapse: collapse; }');
    printWin.document.write('th, td { border: 1px solid #ccc; padding: 6px 8px; font-size: 12px; }');
    printWin.document.write('th { background: #f5f5f5; font-weight: 600; }');
    printWin.document.write('.total-row { background: #f1f5f9; font-weight: 700; }');
    printWin.document.write('.text-right { text-align: right; }');
    printWin.document.write('@media print { @page { margin: 10mm; } }');
    printWin.document.write('</style></head><body>');
    printWin.document.write('<h3 style="text-align:center;margin-bottom:5px;">' + title + '</h3>');
    printWin.document.write('<p style="text-align:center;color:#666;font-size:12px;margin-bottom:15px;">Date Range: ' + ($('#ds_date_from').val() || '') + ' to ' + ($('#ds_date_to').val() || '') + '</p>');
    printWin.document.write($content.html());
    printWin.document.write('</body></html>');
    printWin.document.close();
    setTimeout(function() { printWin.print(); }, 500);
}
</script>
