<?php
// Debug script to check what's actually being stored in student_fees_deposite
echo "<h2>Debug: Student Fees Deposit Amount Storage</h2>";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt_db'; // Adjust database name if different

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>Recent Fee Deposits (Last 10)</h3>";
    $stmt = $pdo->query("SELECT id, amount_detail, created_at FROM student_fees_deposite ORDER BY id DESC LIMIT 10");
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Amount Detail JSON</th><th>Parsed Amounts</th><th>Created At</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td style='max-width: 300px; word-wrap: break-word;'>" . htmlspecialchars($row['amount_detail']) . "</td>";
        
        // Parse the JSON to extract amounts
        $amount_detail = json_decode($row['amount_detail'], true);
        echo "<td>";
        if ($amount_detail) {
            foreach ($amount_detail as $inv_no => $details) {
                echo "Invoice $inv_no:<br>";
                echo "Amount: " . (isset($details['amount']) ? $details['amount'] : 'N/A') . "<br>";
                echo "Discount: " . (isset($details['amount_discount']) ? $details['amount_discount'] : 'N/A') . "<br>";
                echo "Fine: " . (isset($details['amount_fine']) ? $details['amount_fine'] : 'N/A') . "<br>";
                echo "---<br>";
            }
        } else {
            echo "Invalid JSON";
        }
        echo "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Test Currency Conversion Function</h3>";
    
    // Test the currency conversion function
    $test_amounts = [100, 500, 1000, 50.5, 0];
    
    // Include the helper file
    if (file_exists('application/helpers/custom_helper.php')) {
        // We need to simulate CodeIgniter environment
        echo "<p><strong>Testing convertCurrencyFormatToBaseAmount function:</strong></p>";
        echo "<table border='1'>";
        echo "<tr><th>Input Amount</th><th>Expected Result</th><th>Note</th></tr>";
        
        foreach ($test_amounts as $amount) {
            echo "<tr>";
            echo "<td>$amount</td>";
            echo "<td>Should be $amount (if currency price is null/zero)</td>";
            echo "<td>Raw amount should be preserved when conversion fails</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>Check Currency Settings</h3>";
    
    // Check for currency-related tables
    $currency_tables = ['general_settings', 'school_settings', 'currencies'];
    
    foreach ($currency_tables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "<h4>Table: $table</h4>";
                $stmt = $pdo->query("SELECT * FROM $table LIMIT 5");
                $columns = [];
                
                echo "<table border='1'>";
                $first = true;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($first) {
                        echo "<tr>";
                        foreach (array_keys($row) as $col) {
                            echo "<th>$col</th>";
                            $columns[] = $col;
                        }
                        echo "</tr>";
                        $first = false;
                    }
                    
                    echo "<tr>";
                    foreach ($row as $col => $value) {
                        if (stripos($col, 'currency') !== false || stripos($col, 'price') !== false) {
                            echo "<td style='background-color: yellow;'>$value</td>";
                        } else {
                            echo "<td>$value</td>";
                        }
                    }
                    echo "</tr>";
                }
                echo "</table><br>";
            }
        } catch (Exception $e) {
            echo "<p>Table $table not found or error: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    echo "<br><br><strong>Please update the database connection details at the top of this script.</strong>";
}
?>
