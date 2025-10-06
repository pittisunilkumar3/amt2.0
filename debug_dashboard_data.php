<?php
/**
 * Debug Dashboard Data
 * Quick script to check if dashboard data is being generated correctly
 */

// Simple debug without full CI bootstrap
echo "<h1>Dashboard Data Debug</h1>";
echo "<style>body{font-family:Arial,sans-serif;} pre{background:#f5f5f5;padding:10px;border:1px solid #ddd;}</style>";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p style='color:green;'>✅ Database connection successful</p>";
    
    // Current month date range
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
    
    echo "<h2>Date Range: " . date('F Y') . "</h2>";
    echo "<p>Start: {$start_date} | End: {$end_date}</p>";
    
    // Test Income Data Query
    echo "<h2>Income Data Query Test</h2>";
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
    
    echo "<pre>";
    echo "SQL Query:\n" . $income_sql . "\n\n";
    echo "Parameters: ['{$start_date}', '{$end_date}']\n\n";
    echo "Results:\n";
    print_r($income_results);
    echo "</pre>";
    
    // Test Expense Data Query
    echo "<h2>Expense Data Query Test</h2>";
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
    
    echo "<pre>";
    echo "SQL Query:\n" . $expense_sql . "\n\n";
    echo "Parameters: ['{$start_date}', '{$end_date}']\n\n";
    echo "Results:\n";
    print_r($expense_results);
    echo "</pre>";
    
    // Generate Chart Data Preview
    if (!empty($income_results) || !empty($expense_results)) {
        echo "<h2>Chart Data Preview</h2>";
        
        if (!empty($income_results)) {
            echo "<h3>Income Chart Data:</h3>";
            echo "<pre>";
            echo "Labels: [";
            foreach ($income_results as $row) {
                echo "'{$row['income_category']}', ";
            }
            echo "]\n";
            
            echo "Data: [";
            foreach ($income_results as $row) {
                echo "{$row['total']}, ";
            }
            echo "]\n";
            echo "</pre>";
        }
        
        if (!empty($expense_results)) {
            echo "<h3>Expense Chart Data:</h3>";
            echo "<pre>";
            echo "Labels: [";
            foreach ($expense_results as $row) {
                echo "'{$row['exp_category']}', ";
            }
            echo "]\n";
            
            echo "Data: [";
            foreach ($expense_results as $row) {
                echo "{$row['total']}, ";
            }
            echo "]\n";
            echo "</pre>";
        }
        
        echo "<p style='color:green;font-size:18px;'><strong>✅ Data is available for charts!</strong></p>";
    } else {
        echo "<p style='color:red;font-size:18px;'><strong>❌ No data found for current month</strong></p>";
    }
    
    // Check raw data counts
    echo "<h2>Raw Data Verification</h2>";
    
    // Income count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM income WHERE date_format(date,'%Y-%m') = ?");
    $stmt->execute([date('Y-m')]);
    $income_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Expense count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM expenses WHERE date_format(date,'%Y-%m') = ?");
    $stmt->execute([date('Y-m')]);
    $expense_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "<ul>";
    echo "<li>Income records this month: <strong>{$income_count}</strong></li>";
    echo "<li>Expense records this month: <strong>{$expense_count}</strong></li>";
    echo "</ul>";
    
    if ($income_count > 0 && $expense_count > 0) {
        echo "<p style='color:green;'>✅ Both income and expense data exist for current month</p>";
    } else {
        echo "<p style='color:orange;'>⚠️ Limited data available - charts may appear empty</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><em>Debug completed at: " . date('Y-m-d H:i:s') . "</em></p>";
?>
