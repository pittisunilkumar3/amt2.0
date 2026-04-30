<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<!-- Local Chart.js (v2.8.0) -->
<script src="<?php echo base_url(); ?>backend/js/Chart.min.js"></script>
<script>
    // Safeguard the Chart object because layout/footer.php loads an old 1.x version 
    // that would otherwise overwrite this v2.8.0 version.
    window.ChartV2 = window.Chart;
</script>

<style>
/* 
PREMIUM DESIGN SYSTEM for SmartCampus 
Color Palette: 
- Primary Gradient: #667eea to #764ba2 (Purple/Pink)
- Info: #00c0ef (Cyan)
- Success: #00a65a (Green)
- Warning: #f39c12 (Yellow)
- Danger: #dd4b39 (Red)
*/

:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --card-shadow: 0 4px 20px 0 rgba(0,0,0,0.05);
    --border-radius: 12px;
}

/* Page Layout */
.content-wrapper {
    background-color: #f4f7f6 !important;
}

.box {
    border-radius: var(--border-radius) !important;
    border: none !important;
    box-shadow: var(--card-shadow) !important;
    margin-bottom: 25px;
}

.box-header {
    background: transparent !important;
    border-bottom: 1px solid #f0f0f0 !important;
    padding: 15px 20px !important;
}

.box-title {
    font-size: 18px !important;
    font-weight: 600 !important;
    color: #2d3436 !important;
}

/* Premium Statistics Cards */
.stat-card {
    position: relative;
    display: block;
    margin-bottom: 20px;
    border-radius: var(--border-radius);
    overflow: hidden;
    color: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 110px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.stat-card .inner {
    padding: 15px 20px;
}

.stat-card h3 {
    font-size: 28px;
    font-weight: bold;
    margin: 0 0 5px 0;
    white-space: nowrap;
    padding: 0;
}

.stat-card p {
    font-size: 14px;
    opacity: 0.9;
    margin-bottom: 0;
}

.stat-card .icon {
    position: absolute;
    top: 10px;
    right: 15px;
    z-index: 0;
    font-size: 50px;
    color: rgba(255, 255, 255, 0.15);
    transition: all 0.3s linear;
}

.stat-card:hover .icon {
    font-size: 60px;
}

.bg-purple-gradient { background: var(--primary-gradient); }
.bg-info-gradient { background: linear-gradient(135deg, #00c0ef 0%, #0073b7 100%); }
.bg-success-gradient { background: linear-gradient(135deg, #00a65a 0%, #008d4c 100%); }
.bg-warning-gradient { background: linear-gradient(135deg, #f39c12 0%, #db8b0b 100%); }
.bg-danger-gradient { background: linear-gradient(135deg, #dd4b39 0%, #c23321 100%); }

/* Grid Cards for Fee Groups */
#feeGroupGrid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.fee-group-card {
    background: #fff;
    border-radius: var(--border-radius);
    padding: 18px;
    box-shadow: var(--card-shadow);
    border: 1px solid #f0f0f0;
    transition: all 0.2s ease;
}

.fee-group-card:hover {
    border-color: #667eea;
}

.fee-group-title {
    font-size: 15px;
    font-weight: bold;
    color: #2d3436;
    margin-bottom: 12px;
    border-bottom: 1px dashed #eee;
    padding-bottom: 8px;
}

.fee-group-detail {
    display: flex;
    justify-content: space-between;
    margin-bottom: 6px;
    font-size: 13px;
    color: #636e72;
}

.fee-group-detail strong {
    color: #2d3436;
}

.progress-wrapper {
    margin-top: 15px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 5px;
}

.custom-progress {
    height: 10px;
    border-radius: 5px;
    background-color: #f1f2f6;
    margin-bottom: 0;
    overflow: hidden;
}

/* Charts Styling */
.chart-box {
    background: #fff;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--card-shadow);
    margin-bottom: 25px;
}

.chart-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.chart-header i {
    font-size: 16px;
    margin-right: 10px;
    color: #764ba2;
}

.chart-header h4 {
    margin: 0;
    font-weight: 600;
    font-size: 16px;
}

.chart-container-inner {
    position: relative;
    height: 280px; /* Reduced height as requested */
    width: 100%;
}

/* Tables */
.table-responsive {
    border: none !important;
}

#feeGroupTable {
    border-collapse: separate;
    border-spacing: 0 8px;
}

#feeGroupTable thead th {
    background: #f8f9fa;
    border: none;
    color: #636e72;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
    padding: 12px 15px;
}

#feeGroupTable tbody tr {
    background: #fff;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    transition: all 0.2s ease;
}

#feeGroupTable tbody tr:hover {
    background: #fdfdfd;
    transform: scale(1.005);
}

#feeGroupTable td {
    border: none;
    padding: 12px 15px;
    vertical-align: middle;
}

/* Sidebar and Header Integration */
.content-header h1 {
    font-weight: 700 !important;
    color: #2d3436;
    font-size: 24px !important;
}

/* Grid containers - all columns must allow overflow */
.row,
.col-md-3,
.col-md-4,
.col-sm-3,
.col-sm-4,
.col-xs-12,
[class*="col-"],
.box,
.box-body,
.box-primary,
section.content,
.content-wrapper,
.form-group {
    overflow: visible !important;
    position: relative !important;
}

/* Fix SumoSelect for the new design */

.SumoSelect {
    position: relative !important;
    z-index: 1000 !important;
}
.SumoSelect.open {
    z-index: 999999 !important;
}

.SumoSelect > .CaptionCont {
    border-radius: 6px !important;
    border: 1px solid #d2d6de !important;
    height: 34px !important;
    overflow: visible !important;
}

.SumoSelect > .optWrapper > .options {
    max-height: 150px !important;
    overflow-y: auto !important;
}

.SumoSelect > .optWrapper {
    position: absolute !important;
    z-index: 999999 !important;
    top: 100% !important;
    left: 0 !important;
    width: 100% !important;
    max-height: 320px !important;
    background: #fff !important;
    border: 1px solid #ddd !important;
    border-radius: 4px !important;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175) !important;
    transform: translateZ(0) !important; /* Creates new stacking context */
}

/* Custom Scrollbar for dropdowns */
.SumoSelect > .optWrapper > .options::-webkit-scrollbar {
    width: 6px;
}
.SumoSelect > .optWrapper > .options::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 10px;
}

/* Accessibility for dropdowns in boxes */
.box-body {
    overflow: visible !important;
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-bar-chart"></i> Fee Group-wise Collection Report
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>financereports/finance">Finance Reports</a></li>
            <li class="active">Fee Group-wise Collection</li>
        </ol>
    </section>

    <section class="content">
        <?php $this->load->view('financereports/_finance'); ?>
        <div class="row">
            <div class="col-md-12">
                <!-- Filter Section -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
                    </div>
                    <div class="box-body">
                        <form id="filterForm" method="post">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Session <span class="req">*</span></label>
                                        <select class="form-control" name="session_id" id="session_id" required>
                                            <?php foreach ($sessionlist as $session) { ?>
                                                <option value="<?php echo $session['id']; ?>" <?php echo ($session['id'] == $session_id) ? 'selected' : ''; ?>>
                                                    <?php echo $session['session']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Class</label>
                                        <select class="form-control multiselect-dropdown" name="class_ids[]" id="class_ids" multiple="multiple" style="width: 100%;">
                                            <?php foreach ($classlist as $class) { ?>
                                                <option value="<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <small class="text-muted">Hold Ctrl to select multiple</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Section</label>
                                        <select class="form-control multiselect-dropdown" name="section_ids[]" id="section_ids" multiple="multiple" style="width: 100%;">
                                        </select>
                                        <small class="text-muted">Hold Ctrl to select multiple</small>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fee Group</label>
                                        <select class="form-control multiselect-dropdown" name="feegroup_ids[]" id="feegroup_ids" multiple="multiple" style="width: 100%;"></select>
                                        <small class="text-muted">Hold Ctrl to select multiple</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>From Date</label>
                                        <input type="date" class="form-control" name="from_date" id="from_date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>To Date</label>
                                        <input type="date" class="form-control" name="to_date" id="to_date">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Date Grouping</label>
                                        <select class="form-control" name="date_grouping" id="date_grouping">
                                            <option value="none">None</option>
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block" id="searchBtn">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Section -->
                <div id="summarySection" style="display: none; margin-bottom: 20px;">
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <div class="stat-card bg-info-gradient">
                                <div class="inner">
                                    <h3 id="summary_total_groups">0</h3>
                                    <p>Fee Groups</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-folder-open"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <div class="stat-card bg-purple-gradient">
                                <div class="inner">
                                    <h3><?php echo $currency_symbol; ?> <span id="summary_total_amount">0.00</span></h3>
                                    <p>Total Targeted Fee</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-money"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <div class="stat-card bg-success-gradient">
                                <div class="inner">
                                    <h3><?php echo $currency_symbol; ?> <span id="summary_collected">0.00</span></h3>
                                    <p>Collected (<span id="summary_percentage">0</span>%)</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-6">
                            <div class="stat-card bg-danger-gradient">
                                <div class="inner">
                                    <h3><?php echo $currency_symbol; ?> <span id="summary_balance">0.00</span></h3>
                                    <p>Total Balance</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-exclamation-triangle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4x4 Grid Section -->
                <div id="gridSection" style="display: none;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-th"></i> Fee Group-wise Collection (Top 16)</h3>
                        </div>
                        <div class="box-body">
                            <div class="grid-container" id="feeGroupGrid">
                                <!-- Grid cards will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div id="chartsSection" style="display: none;">
                    <div class="row">
                        <div class="col-md-12">
                            <span id="chartVersionInfo" style="font-size: 10px; color: #ccc; margin-left: 15px;"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-box">
                                <div class="chart-header">
                                    <i class="fa fa-pie-chart"></i>
                                    <h4>Collection Distribution</h4>
                                </div>
                                <div class="chart-container-inner">
                                    <canvas id="collectionPieChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="chart-box">
                                <div class="chart-header">
                                    <i class="fa fa-bar-chart"></i>
                                    <h4>Fee Group Comparison</h4>
                                </div>
                                <div class="chart-container-inner">
                                    <canvas id="collectionBarChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="chart-box">
                                <div class="chart-header">
                                    <i class="fa fa-line-chart"></i>
                                    <h4 id="trendChartTitle">Collection Trend</h4>
                                </div>
                                <div class="chart-container-inner">
                                    <canvas id="collectionTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Table Section -->
                <div id="tableSection" style="display: none;">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-table"></i> Detailed Fee Collection Records</h3>
                            <div class="box-tools pull-right export-buttons">
                                <button type="button" class="btn btn-success btn-sm btn-export" id="exportExcel">
                                    <i class="fa fa-file-excel-o"></i> Export Excel
                                </button>
                                <button type="button" class="btn btn-info btn-sm btn-export" id="exportCSV">
                                    <i class="fa fa-file-text-o"></i> Export CSV
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="feeGroupTable">
                                    <thead>
                                        <tr>
                                            <th>Admission No</th>
                                            <th>Student Name</th>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Fee Group</th>
                                            <th>Total Fee</th>
                                            <th>Collected</th>
                                            <th>Balance</th>
                                            <th>Collection %</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="feeGroupTableBody">
                                        <!-- Table rows will be populated here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- No Data Message -->
                <div id="noDataSection" style="display: none;">
                    <div class="box box-warning">
                        <div class="box-body">
                            <div class="no-data-message">
                                <i class="fa fa-info-circle fa-3x"></i>
                                <p>No data available for the selected filters. Please adjust your search criteria.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
var currency_symbol = <?php echo json_encode($currency_symbol); ?>;
var base_url = '<?php echo base_url(); ?>';
var pieChart = null;
var barChart = null;
var trendChart = null;
var currentData = null;

$(document).ready(function() {
    // Restore Chart object if it was overwritten by footer (layout/footer.php)
    // We specifically need v2.8.0 which we saved to window.ChartV2
    if (window.ChartV2) {
        window.Chart = window.ChartV2;
        console.log("Chart.js v2.8.0 restored.");
    }

    // Initialize SumoSelect for all multi-select dropdowns
    if (typeof $.fn.SumoSelect !== 'undefined') {
        $('.multiselect-dropdown').SumoSelect({
            placeholder: 'Select Options',
            csvDispCount: 3,
            captionFormat: '{0} Selected',
            captionFormatAllSelected: 'All Selected ({0})',
            selectAll: true,
            search: true,
            searchText: 'Search...',
            noMatch: 'No matches found "{0}"',
            okCancelInMulti: true,
            isClickAwayOk: true,
            locale: ['OK', 'Cancel', 'Select All'],
            up: false,
            showTitle: true
        });
    }

    // Initialize Select2
    $('.select2').select2({
        placeholder: 'Select options',
        allowClear: true
    });

    // Load fee groups on page load
    loadFeeGroups();

    // Load sections and fee groups when class is selected
    $('#class_ids').on('change', function() {
        loadSections();
        loadFeeGroups();
    });

    // Load fee groups when section is selected
    $('#section_ids').on('change', function() {
        loadFeeGroups();
    });

    // Load fee groups when session is selected
    $('#session_id').on('change', function() {
        loadFeeGroups();
    });

    // Handle form submission
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        loadFeeGroupData();
    });

    // Export buttons
    $('#exportExcel').on('click', function() {
        exportReport('excel');
    });

    $('#exportCSV').on('click', function() {
        exportReport('csv');
    });

    // Initialize DataTable
    initializeDataTable();
});

/**
 * Load fee groups for filter dropdown
 */
function loadFeeGroups() {
    var session_id = $('#session_id').val();
    var class_ids = $('#class_ids').val() || [];
    var section_ids = $('#section_ids').val() || [];

    $.ajax({
        url: base_url + 'admin/feegroup/get_feegroup',
        type: 'POST',
        data: { 
            session_id: session_id,
            class_ids: class_ids,
            section_ids: section_ids
        },
        dataType: 'json',
        success: function(response) {
            var options = '';
            if (response && response.length > 0) {
                $.each(response, function(index, group) {
                    options += '<option value="' + group.id + '">' + group.name + '</option>';
                });
            }
            $('#feegroup_ids').html(options);
            
            // Reload SumoSelect if initialized
            if ($('#feegroup_ids')[0].sumo) {
                $('#feegroup_ids')[0].sumo.reload();
            }
        },
        error: function() {
            // Just empty it on error
            $('#feegroup_ids').html('');
            if ($('#feegroup_ids')[0].sumo) {
                $('#feegroup_ids')[0].sumo.reload();
            }
        }
    });
}

/**
 * Load sections based on selected classes
 */
function loadSections() {
    var class_ids = $('#class_ids').val();

    if (!class_ids || class_ids.length === 0) {
        $('#section_ids').empty();
        if ($('#section_ids')[0].sumo) {
            $('#section_ids')[0].sumo.reload();
        }
        return;
    }

    $.ajax({
        url: base_url + 'sections/getByClass',
        type: 'POST',
        data: { class_id: class_ids },
        dataType: 'json',
        success: function(response) {
            var options = '';
            if (response && response.length > 0) {
                $.each(response, function(index, section) {
                    options += '<option value="' + section.section_id + '">' + section.section + '</option>';
                });
            }
            $('#section_ids').html(options);
            
            // Reload SumoSelect if initialized
            if ($('#section_ids')[0].sumo) {
                $('#section_ids')[0].sumo.reload();
            }
        },
        error: function() {
            $('#section_ids').html('');
            if ($('#section_ids')[0].sumo) {
                $('#section_ids')[0].sumo.reload();
            }
        }
    });
}

/**
 * Load fee group-wise data
 */
function loadFeeGroupData() {
    var formData = {
        session_id: $('#session_id').val(),
        class_ids: $('#class_ids').val() || [],
        section_ids: $('#section_ids').val() || [],
        feegroup_ids: $('#feegroup_ids').val() || [],
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val(),
        date_grouping: $('#date_grouping').val()
    };

    // Show loading
    $('#searchBtn').html('<i class="fa fa-spinner fa-spin"></i> Loading...').prop('disabled', true);

    $.ajax({
        url: base_url + 'financereports/getFeeGroupwiseData',
        type: 'POST',
        data: formData,
        dataType: 'json',
        success: function(response) {
            try {
                if (response.status == 1) {
                    currentData = response;

                    if (response.grid_data && response.grid_data.length > 0) {
                        // Show sections first so charts can calculate dimensions correctly
                        $('#summarySection, #gridSection, #chartsSection, #tableSection').fadeIn();
                        $('#noDataSection').hide();

                        // Update Summary
                        updateSummary(response.summary);

                        // Populate Grid
                        populateGrid(response.grid_data);

                        // Populate Charts (if Chart.js is available)
                        if (typeof Chart !== 'undefined') {
                            var version = (Chart.version) ? Chart.version : '1.x (legacy)';
                            $('#chartVersionInfo').text('Chart.js engine: ' + version);
                            
                            // Use a small timeout to ensure fadeIn has started/finished and elements have dimensions
                            setTimeout(function() {
                                populateCharts(response.grid_data, response.trend_data);
                            }, 500);
                        } else {
                            $('#chartVersionInfo').text('Chart.js NOT FOUND');
                            console.warn('Chart.js library not loaded.');
                        }

                        // Populate Table
                        populateTable(response.detailed_data);
                    } else {
                        hideAllSections();
                        $('#noDataSection').fadeIn();
                    }
                } else {
                    alert('Error: ' + response.message);
                    hideAllSections();
                }
            } catch (err) {
                console.error("Rendering Error:", err);
                alert('A rendering error occurred: ' + err.message);
                // Ensure sections are hidden if rendering fails halfway
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX Error:", status, error);
            alert('An error occurred while loading data. Please try again.');
            hideAllSections();
        },
        complete: function() {
            // Always restore button state
            $('#searchBtn').html('<i class="fa fa-search"></i> Search').prop('disabled', false);
        }
    });
}

/**
 * Update summary statistics
 */
function updateSummary(summary) {
    $('#summary_total_groups').text(summary.total_fee_groups);
    $('#summary_total_amount').text(formatNumber(summary.total_amount));
    $('#summary_collected').text(formatNumber(summary.total_collected));
    $('#summary_balance').text(formatNumber(summary.total_balance));
    $('#summary_percentage').text(summary.collection_percentage);
}

/**
 * Populate 4x4 grid with fee group cards
 */
function populateGrid(data) {
    var html = '';
    var maxCards = 16; // 4x4 grid
    var displayData = data.slice(0, maxCards);

    $.each(displayData, function(index, item) {
        var progressColor = getProgressColor(item.collection_percentage);

        html += '<div class="fee-group-card">';
        html += '  <div class="fee-group-title" title="' + item.fee_group_name + '">' + item.fee_group_name + '</div>';
        
        html += '  <div class="fee-group-detail">';
        html += '    <span>Total:</span>';
        html += '    <strong>' + currency_symbol + ' ' + formatNumber(item.total_amount) + '</strong>';
        html += '  </div>';
        
        html += '  <div class="fee-group-detail">';
        html += '    <span>Collected:</span>';
        html += '    <strong class="text-success">' + currency_symbol + ' ' + formatNumber(item.amount_collected) + '</strong>';
        html += '  </div>';
        
        html += '  <div class="fee-group-detail">';
        html += '    <span>Balance:</span>';
        html += '    <strong class="text-danger">' + currency_symbol + ' ' + formatNumber(item.balance_amount) + '</strong>';
        html += '  </div>';
        
        html += '  <div class="progress-wrapper">';
        html += '    <div class="progress-label">';
        html += '      <span>Progress</span>';
        html += '      <span>' + item.collection_percentage + '%</span>';
        html += '    </div>';
        html += '    <div class="progress custom-progress">';
        html += '      <div class="progress-bar ' + progressColor + '" role="progressbar" style="width: ' + item.collection_percentage + '%"></div>';
        html += '    </div>';
        html += '  </div>';
        html += '</div>';
    });

    $('#feeGroupGrid').html(html);
}

/**
 * Get progress bar color based on percentage
 */
function getProgressColor(percentage) {
    if (percentage >= 80) return 'progress-bar-success';
    if (percentage >= 50) return 'progress-bar-warning';
    return 'progress-bar-danger';
}

/**
 * Populate charts
 */
function populateCharts(data, trendData) {
    var maxChartData = 10; // Show top 10 in charts
    var chartData = data.slice(0, maxChartData);

    var labels = [];
    var collectedData = [];
    var balanceData = [];
    var colors = generateColors(chartData.length);

    $.each(chartData, function(index, item) {
        labels.push(item.fee_group_name);
        collectedData.push(parseFloat(item.amount_collected) || 0);
        balanceData.push(parseFloat(item.balance_amount) || 0);
    });

    // Destroy existing charts
    if (pieChart) pieChart.destroy();
    if (barChart) barChart.destroy();

    // Pie Chart
    var pieCtx = document.getElementById('collectionPieChart').getContext('2d');
    var ChartLib = window.ChartV2 || window.Chart;
    pieChart = new ChartLib(pieCtx, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: collectedData,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'right',
                labels: {
                    boxWidth: 12,
                    fontSize: 11
                }
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index] || '';
                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        if (label) {
                            label += ': ';
                        }
                        label += currency_symbol + ' ' + formatNumber(value);
                        return label;
                    }
                }
            }
        }
    });

    // Bar Chart
    var barCtx = document.getElementById('collectionBarChart').getContext('2d');
    var ChartLib = window.ChartV2 || window.Chart;
    barChart = new ChartLib(barCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Collected',
                    data: collectedData,
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Balance',
                    data: balanceData,
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'top'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return currency_symbol + ' ' + formatNumber(value);
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + currency_symbol + ' ' + formatNumber(tooltipItem.yLabel);
                    }
                }
            }
        }
    });

    // Trend Chart (Line Chart for Collection Trend)
    var trendCtx = document.getElementById('collectionTrendChart').getContext('2d');
    var trendLabels = [];
    var trendValues = [];
    
    if (trendData && trendData.length > 0) {
        $.each(trendData, function(i, item) {
            trendLabels.push(item.label);
            trendValues.push(item.amount);
        });
        $('#collectionTrendChart').closest('.row').show();
    } else {
        $('#collectionTrendChart').closest('.row').hide();
    }

    if (trendChart) trendChart.destroy();
    
    var ChartLib = window.ChartV2 || window.Chart;
    
    trendChart = new ChartLib(trendCtx, {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Collection Amount',
                data: trendValues,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                pointRadius: 3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            legend: {
                position: 'top'
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        callback: function(value) {
                            return currency_symbol + ' ' + formatNumber(value);
                        }
                    }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        return 'Amount: ' + currency_symbol + ' ' + formatNumber(tooltipItem.yLabel);
                    }
                }
            }
        }
    });
}

/**
 * Generate random colors for charts
 */
function generateColors(count) {
    var palette = [
        'rgba(102, 126, 234, 0.7)',  // Purple
        'rgba(118, 75, 162, 0.7)',   // Pink
        'rgba(75, 192, 192, 0.7)',   // Teal
        'rgba(255, 99, 132, 0.7)',   // Salmon
        'rgba(255, 159, 64, 0.7)',   // Amber
        'rgba(54, 162, 235, 0.7)',   // Blue
        'rgba(153, 102, 255, 0.7)',  // Indigo
        'rgba(46, 204, 113, 0.7)',   // Emerald
        'rgba(241, 196, 15, 0.7)',   // Sun
        'rgba(230, 126, 34, 0.7)'    // Orange
    ];
    
    var colors = [];
    for (var i = 0; i < count; i++) {
        colors.push(palette[i % palette.length]);
    }
    return colors;
}


/**
 * Populate detailed table
 */
function populateTable(data) {
    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable('#feeGroupTable')) {
        $('#feeGroupTable').DataTable().destroy();
    }

    // Clear the table body
    $('#feeGroupTableBody').empty();

    if (data && data.length > 0) {
        // Prepare data for DataTable
        var tableData = [];

        $.each(data, function(index, item) {
            var statusClass = '';
            if (item.payment_status == 'Paid') statusClass = 'label-success';
            else if (item.payment_status == 'Partial') statusClass = 'label-warning';
            else statusClass = 'label-danger';

            tableData.push([
                item.admission_no,
                item.student_name,
                item.class_name,
                item.section_name,
                item.fee_group_name,
                currency_symbol + ' ' + formatNumber(item.total_amount),
                currency_symbol + ' ' + formatNumber(item.amount_collected),
                currency_symbol + ' ' + formatNumber(item.balance_amount),
                item.collection_percentage + '%',
                '<span class="label ' + statusClass + '">' + item.payment_status + '</span>'
            ]);
        });

        // Initialize DataTable with data
        $('#feeGroupTable').DataTable({
            "data": tableData,
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "order": [[0, "asc"]],
            "columnDefs": [
                { "orderable": true, "targets": "_all" },
                { "className": "text-right", "targets": [5, 6, 7, 8] }
            ],
            "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
        });
    } else {
        // Initialize empty DataTable
        initializeDataTable();
    }
}

/**
 * Initialize DataTable
 */
function initializeDataTable() {
    $('#feeGroupTable').DataTable({
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": true, "targets": "_all" }
        ],
        "dom": '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip'
    });
}

/**
 * Export report
 */
function exportReport(format) {
    if (!currentData || !currentData.detailed_data || currentData.detailed_data.length === 0) {
        alert('No data available to export');
        return;
    }

    var formData = {
        export_format: format,
        session_id: $('#session_id').val(),
        class_ids: $('#class_ids').val() || [],
        section_ids: $('#section_ids').val() || [],
        feegroup_ids: $('#feegroup_ids').val() || [],
        from_date: $('#from_date').val(),
        to_date: $('#to_date').val()
    };

    // Create a form and submit
    var form = $('<form>', {
        'method': 'POST',
        'action': base_url + 'financereports/exportFeeGroupwiseReport'
    });

    $.each(formData, function(key, value) {
        if (Array.isArray(value)) {
            $.each(value, function(i, v) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': key + '[]',
                    'value': v
                }));
            });
        } else {
            form.append($('<input>', {
                'type': 'hidden',
                'name': key,
                'value': value
            }));
        }
    });

    $('body').append(form);
    form.submit();
    form.remove();
}

/**
 * Hide all sections
 */
function hideAllSections() {
    $('#summarySection').hide();
    $('#gridSection').hide();
    $('#chartsSection').hide();
    $('#tableSection').hide();
    $('#noDataSection').hide();
}

/**
 * Format number with commas
 */
function formatNumber(num) {
    return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}
</script>

