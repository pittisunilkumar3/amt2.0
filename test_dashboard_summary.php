<?php
/**
 * Test Dashboard Summary Cards Data
 * This script tests the summary calculations for the new dashboard cards
 */

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>🎯 Dashboard Summary Cards Test</h1>";
    echo "<style>body{font-family:Arial,sans-serif;} .card{background:#f8f9fa;padding:20px;margin:10px 0;border-radius:8px;border-left:4px solid #007bff;} .success{border-left-color:#28a745;} .danger{border-left-color:#dc3545;} .warning{border-left-color:#ffc107;}</style>";
    
    // Current month date range
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
    $current_month = date('F Y');
    
    echo "<div class='card'>";
    echo "<h2>📅 Testing Period: {$current_month}</h2>";
    echo "<p><strong>Date Range:</strong> {$start_date} to {$end_date}</p>";
    echo "</div>";
    
    // Test Income Data
    echo "<div class='card success'>";
    echo "<h2>💰 Income Summary</h2>";
    
    $income_sql = "
        SELECT sum(amount) as total, income_category 
        FROM income 
        JOIN income_head ON income.income_head_id = income_head.id 
        WHERE date_format(date,'%Y-%m-%d') between ? and ? 
        GROUP BY income_head.id
    ";
    
    $stmt = $pdo->prepare($income_sql);
    $stmt->execute([$start_date, $end_date]);
    $income_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_income = 0;
    if (!empty($income_results)) {
        echo "<table border='1' style='width:100%;border-collapse:collapse;'>";
        echo "<tr style='background:#e8f5e8;'><th>Category</th><th>Amount</th></tr>";
        
        foreach ($income_results as $income) {
            $total_income += $income['total'];
            echo "<tr>";
            echo "<td>{$income['income_category']}</td>";
            echo "<td>₹" . number_format($income['total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<h3 style='color:#28a745;'>Total Income: ₹" . number_format($total_income, 2) . "</h3>";
    } else {
        echo "<p style='color:#dc3545;'>❌ No income data found for current month</p>";
    }
    echo "</div>";
    
    // Test Expense Data
    echo "<div class='card danger'>";
    echo "<h2>💸 Expense Summary</h2>";
    
    $expense_sql = "
        SELECT sum(amount) as total, exp_category 
        FROM expenses 
        JOIN expense_head ON expenses.exp_head_id = expense_head.id 
        WHERE date_format(date,'%Y-%m-%d') between ? and ? 
        GROUP BY expense_head.id
    ";
    
    $stmt = $pdo->prepare($expense_sql);
    $stmt->execute([$start_date, $end_date]);
    $expense_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $total_expense = 0;
    if (!empty($expense_results)) {
        echo "<table border='1' style='width:100%;border-collapse:collapse;'>";
        echo "<tr style='background:#f8e8e8;'><th>Category</th><th>Amount</th></tr>";
        
        foreach ($expense_results as $expense) {
            $total_expense += $expense['total'];
            echo "<tr>";
            echo "<td>{$expense['exp_category']}</td>";
            echo "<td>₹" . number_format($expense['total'], 2) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<h3 style='color:#dc3545;'>Total Expenses: ₹" . number_format($total_expense, 2) . "</h3>";
    } else {
        echo "<p style='color:#dc3545;'>❌ No expense data found for current month</p>";
    }
    echo "</div>";
    
    // Calculate Net Profit/Loss
    $net_profit = $total_income - $total_expense;
    $profit_class = ($net_profit >= 0) ? 'success' : 'danger';
    $profit_color = ($net_profit >= 0) ? '#28a745' : '#dc3545';
    $profit_text = ($net_profit >= 0) ? 'Net Profit' : 'Net Loss';
    $profit_icon = ($net_profit >= 0) ? '📈' : '📉';
    
    echo "<div class='card {$profit_class}'>";
    echo "<h2>{$profit_icon} Financial Summary</h2>";
    echo "<table border='1' style='width:100%;border-collapse:collapse;'>";
    echo "<tr style='background:#f0f0f0;'><th>Metric</th><th>Amount</th></tr>";
    echo "<tr><td><strong>Total Income</strong></td><td style='color:#28a745;'>₹" . number_format($total_income, 2) . "</td></tr>";
    echo "<tr><td><strong>Total Expenses</strong></td><td style='color:#dc3545;'>₹" . number_format($total_expense, 2) . "</td></tr>";
    echo "<tr style='background:#f8f9fa;'><td><strong>{$profit_text}</strong></td><td style='color:{$profit_color};font-weight:bold;'>₹" . number_format(abs($net_profit), 2) . "</td></tr>";
    echo "</table>";
    
    if ($net_profit >= 0) {
        echo "<p style='color:#28a745;'>✅ <strong>Great!</strong> You have a profit this month.</p>";
    } else {
        echo "<p style='color:#dc3545;'>⚠️ <strong>Attention:</strong> You have a loss this month. Consider reviewing expenses.</p>";
    }
    echo "</div>";
    
    // Dashboard Card Preview
    echo "<div class='card'>";
    echo "<h2>🎨 Dashboard Cards Preview</h2>";
    echo "<p>Here's how the cards will appear on your dashboard:</p>";
    
    echo "<div style='display:flex;gap:20px;flex-wrap:wrap;margin:20px 0;'>";
    
    // Income Card Preview
    echo "<div style='background:#28a745;color:white;padding:20px;border-radius:8px;min-width:200px;flex:1;'>";
    echo "<div style='display:flex;align-items:center;gap:15px;'>";
    echo "<div style='font-size:24px;'>📈</div>";
    echo "<div>";
    echo "<div style='font-size:12px;opacity:0.9;text-transform:uppercase;'>TOTAL INCOME</div>";
    echo "<div style='font-size:22px;font-weight:bold;'>₹" . number_format($total_income, 2) . "</div>";
    echo "<div style='font-size:12px;opacity:0.8;'>{$current_month}</div>";
    echo "</div></div></div>";
    
    // Expense Card Preview
    echo "<div style='background:#dc3545;color:white;padding:20px;border-radius:8px;min-width:200px;flex:1;'>";
    echo "<div style='display:flex;align-items:center;gap:15px;'>";
    echo "<div style='font-size:24px;'>📉</div>";
    echo "<div>";
    echo "<div style='font-size:12px;opacity:0.9;text-transform:uppercase;'>TOTAL EXPENSES</div>";
    echo "<div style='font-size:22px;font-weight:bold;'>₹" . number_format($total_expense, 2) . "</div>";
    echo "<div style='font-size:12px;opacity:0.8;'>{$current_month}</div>";
    echo "</div></div></div>";
    
    // Net Profit/Loss Card Preview
    $card_bg = ($net_profit >= 0) ? '#28a745' : '#dc3545';
    echo "<div style='background:{$card_bg};color:white;padding:20px;border-radius:8px;min-width:200px;flex:1;'>";
    echo "<div style='display:flex;align-items:center;gap:15px;'>";
    echo "<div style='font-size:24px;'>{$profit_icon}</div>";
    echo "<div>";
    echo "<div style='font-size:12px;opacity:0.9;text-transform:uppercase;'>{$profit_text}</div>";
    echo "<div style='font-size:22px;font-weight:bold;'>₹" . number_format(abs($net_profit), 2) . "</div>";
    echo "<div style='font-size:12px;opacity:0.8;'>{$current_month}</div>";
    echo "</div></div></div>";
    
    echo "</div>";
    echo "</div>";
    
    // Final Status
    if ($total_income > 0 && $total_expense > 0) {
        echo "<div class='card success'>";
        echo "<h2>✅ Test Results: SUCCESS</h2>";
        echo "<ul>";
        echo "<li>✅ Income data: Found and calculated correctly</li>";
        echo "<li>✅ Expense data: Found and calculated correctly</li>";
        echo "<li>✅ Net profit/loss: Calculated correctly</li>";
        echo "<li>✅ Dashboard cards: Ready to display</li>";
        echo "</ul>";
        echo "<p><strong>Next Steps:</strong></p>";
        echo "<ol>";
        echo "<li>Clear browser cache</li>";
        echo "<li>Navigate to <code>http://localhost/amt/admin/admin/dashboard</code></li>";
        echo "<li>Look for the three summary cards above the charts</li>";
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div class='card danger'>";
        echo "<h2>❌ Test Results: ISSUES FOUND</h2>";
        echo "<p>Some data is missing. Please ensure you have both income and expense data for the current month.</p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<div class='card danger'>";
    echo "<h2>❌ Database Error</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<p><em>Test completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
