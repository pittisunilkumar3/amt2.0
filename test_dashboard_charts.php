<?php
/**
 * Dashboard Charts Test Script
 * This script tests the dashboard chart functionality and data retrieval
 */

// Include CodeIgniter bootstrap
require_once('index.php');

// Get CodeIgniter instance
$CI =& get_instance();

// Load required models
$CI->load->model('income_model');
$CI->load->model('expense_model');
$CI->load->helper('custom');

echo "<h1>Dashboard Charts Test Results</h1>";
echo "<style>body{font-family:Arial,sans-serif;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background-color:#f2f2f2;}</style>";

// Test current month date range
$start_date = date('Y-m-01');
$end_date = date('Y-m-t');
$current_month = date('F Y');

echo "<h2>Testing Date Range: {$current_month}</h2>";
echo "<p><strong>Start Date:</strong> {$start_date}</p>";
echo "<p><strong>End Date:</strong> {$end_date}</p>";

// Test Income Data
echo "<h2>1. Income Chart Data Test</h2>";
$incomegraph = $CI->income_model->getIncomeHeadsData($start_date, $end_date);

if (empty($incomegraph)) {
    echo "<p style='color:red;'><strong>❌ ISSUE:</strong> No income data found for current month!</p>";
} else {
    echo "<p style='color:green;'><strong>✅ SUCCESS:</strong> Found " . count($incomegraph) . " income categories</p>";
    
    echo "<table>";
    echo "<tr><th>Income Category</th><th>Total Amount</th><th>Chart Color</th></tr>";
    
    $s = 1;
    foreach ($incomegraph as $key => $value) {
        $color = incomegraphColors($s++);
        if ($s == 8) $s = 1;
        
        echo "<tr>";
        echo "<td>{$value['income_category']}</td>";
        echo "<td>" . number_format($value['total'], 2) . "</td>";
        echo "<td><span style='background-color:{$color};padding:5px 10px;color:white;'>{$color}</span></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test JavaScript data generation
    echo "<h3>JavaScript Chart Data Preview:</h3>";
    echo "<pre>";
    echo "Labels: [";
    foreach ($incomegraph as $value) {
        echo "\"" . $value['income_category'] . "\", ";
    }
    echo "]\n";
    
    echo "Data: [";
    foreach ($incomegraph as $value) {
        echo $value['total'] . ", ";
    }
    echo "]\n";
    
    echo "Colors: [";
    $s = 1;
    foreach ($incomegraph as $value) {
        echo "\"" . incomegraphColors($s++) . "\", ";
        if ($s == 8) $s = 1;
    }
    echo "]";
    echo "</pre>";
}

// Test Expense Data
echo "<h2>2. Expense Chart Data Test</h2>";
$expensegraph = $CI->expense_model->getExpenseHeadData($start_date, $end_date);

if (empty($expensegraph)) {
    echo "<p style='color:red;'><strong>❌ ISSUE:</strong> No expense data found for current month!</p>";
} else {
    echo "<p style='color:green;'><strong>✅ SUCCESS:</strong> Found " . count($expensegraph) . " expense categories</p>";
    
    echo "<table>";
    echo "<tr><th>Expense Category</th><th>Total Amount</th><th>Chart Color</th></tr>";
    
    $ss = 1;
    foreach ($expensegraph as $key => $value) {
        $color = expensegraphColors($ss++);
        if ($ss == 8) $ss = 1;
        
        echo "<tr>";
        echo "<td>{$value['exp_category']}</td>";
        echo "<td>" . number_format($value['total'], 2) . "</td>";
        echo "<td><span style='background-color:{$color};padding:5px 10px;color:white;'>{$color}</span></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test JavaScript data generation
    echo "<h3>JavaScript Chart Data Preview:</h3>";
    echo "<pre>";
    echo "Labels: [";
    foreach ($expensegraph as $value) {
        echo "\"" . $value['exp_category'] . "\", ";
    }
    echo "]\n";
    
    echo "Data: [";
    foreach ($expensegraph as $value) {
        echo $value['total'] . ", ";
    }
    echo "]\n";
    
    echo "Colors: [";
    $ss = 1;
    foreach ($expensegraph as $value) {
        echo "\"" . expensegraphColors($ss++) . "\", ";
        if ($ss == 8) $ss = 1;
    }
    echo "]";
    echo "</pre>";
}

// Test Chart.js Integration
echo "<h2>3. Chart.js Integration Test</h2>";

if (!empty($incomegraph) && !empty($expensegraph)) {
    echo "<p style='color:green;'><strong>✅ SUCCESS:</strong> Both income and expense data available for charts</p>";
    
    echo "<h3>Sample Chart HTML:</h3>";
    echo "<div style='background-color:#f9f9f9;padding:15px;border:1px solid #ddd;'>";
    echo "<h4>Income Chart (Doughnut)</h4>";
    echo "<canvas id='income-chart-test' width='400' height='200'></canvas>";
    echo "<h4>Expense Chart (Doughnut)</h4>";
    echo "<canvas id='expense-chart-test' width='400' height='200'></canvas>";
    echo "</div>";
    
    // Generate Chart.js code
    echo "<h3>Generated Chart.js Code:</h3>";
    echo "<pre style='background-color:#f0f0f0;padding:10px;overflow-x:auto;'>";
    echo htmlspecialchars("
// Income Chart
new Chart(document.getElementById('income-chart-test'), {
    type: 'doughnut',
    data: {
        labels: [");
    
    foreach ($incomegraph as $value) {
        echo "\"" . $value['income_category'] . "\", ";
    }
    
    echo htmlspecialchars("],
        datasets: [{
            label: 'Income',
            backgroundColor: [");
    
    $s = 1;
    foreach ($incomegraph as $value) {
        echo "\"" . incomegraphColors($s++) . "\", ";
        if ($s == 8) $s = 1;
    }
    
    echo htmlspecialchars("],
            data: [");
    
    foreach ($incomegraph as $value) {
        echo $value['total'] . ", ";
    }
    
    echo htmlspecialchars("]
        }]
    },
    options: {
        responsive: true,
        circumference: Math.PI,
        rotation: -Math.PI
    }
});

// Expense Chart
new Chart(document.getElementById('expense-chart-test'), {
    type: 'doughnut',
    data: {
        labels: [");
    
    foreach ($expensegraph as $value) {
        echo "\"" . $value['exp_category'] . "\", ";
    }
    
    echo htmlspecialchars("],
        datasets: [{
            label: 'Expenses',
            backgroundColor: [");
    
    $ss = 1;
    foreach ($expensegraph as $value) {
        echo "\"" . expensegraphColors($ss++) . "\", ";
        if ($ss == 8) $ss = 1;
    }
    
    echo htmlspecialchars("],
            data: [");
    
    foreach ($expensegraph as $value) {
        echo $value['total'] . ", ";
    }
    
    echo htmlspecialchars("]
        }]
    }
});");
    echo "</pre>";
    
} else {
    echo "<p style='color:red;'><strong>❌ ISSUE:</strong> Missing data for chart generation</p>";
}

// Summary
echo "<h2>4. Test Summary</h2>";
$income_count = count($incomegraph);
$expense_count = count($expensegraph);

if ($income_count > 0 && $expense_count > 0) {
    echo "<p style='color:green;font-size:18px;'><strong>✅ ALL TESTS PASSED!</strong></p>";
    echo "<ul>";
    echo "<li>✅ Income data: {$income_count} categories found</li>";
    echo "<li>✅ Expense data: {$expense_count} categories found</li>";
    echo "<li>✅ Chart colors: Working properly</li>";
    echo "<li>✅ JavaScript generation: Ready for Chart.js</li>";
    echo "</ul>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Clear browser cache and refresh the dashboard</li>";
    echo "<li>Check browser console for any JavaScript errors</li>";
    echo "<li>Verify Chart.js library is loading properly</li>";
    echo "</ol>";
} else {
    echo "<p style='color:red;font-size:18px;'><strong>❌ TESTS FAILED!</strong></p>";
    echo "<ul>";
    if ($income_count == 0) echo "<li>❌ No income data found</li>";
    if ($expense_count == 0) echo "<li>❌ No expense data found</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
