<?php
// Quick database schema check for student_fees_deposite table
echo "<h2>Database Schema Check</h2>";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt'; // Adjust if different

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>student_fees_deposite Table Structure</h3>";
    $stmt = $pdo->query("DESCRIBE student_fees_deposite");
    
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        foreach ($row as $col => $value) {
            if ($col == 'Field' && strpos($value, 'amount') !== false) {
                echo "<td style='background-color: yellow;'>$value</td>";
            } else {
                echo "<td>$value</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Sample Data from student_fees_deposite</h3>";
    $stmt = $pdo->query("SELECT * FROM student_fees_deposite ORDER BY id DESC LIMIT 3");
    
    $columns = [];
    $first = true;
    echo "<table border='1' style='font-size: 12px;'>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($first) {
            echo "<tr>";
            foreach (array_keys($row) as $col) {
                echo "<th>$col</th>";
            }
            echo "</tr>";
            $first = false;
        }
        
        echo "<tr>";
        foreach ($row as $col => $value) {
            if ($col == 'amount_detail') {
                echo "<td style='max-width: 200px; word-break: break-all;'>" . htmlspecialchars($value) . "</td>";
            } else {
                echo "<td>$value</td>";
            }
        }
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>Check for amount_detail JSON content</h3>";
    $stmt = $pdo->query("SELECT id, amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL ORDER BY id DESC LIMIT 5");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<h4>Record ID: " . $row['id'] . "</h4>";
        echo "<p><strong>Raw JSON:</strong> " . htmlspecialchars($row['amount_detail']) . "</p>";
        
        $decoded = json_decode($row['amount_detail'], true);
        if ($decoded) {
            echo "<p><strong>Decoded JSON:</strong></p>";
            echo "<pre>" . print_r($decoded, true) . "</pre>";
            
            foreach ($decoded as $inv_no => $details) {
                if (isset($details['amount'])) {
                    echo "<p><strong>Invoice $inv_no Amount:</strong> " . $details['amount'] . "</p>";
                    
                    if ($details['amount'] == 0 || $details['amount'] == '0') {
                        echo "<p style='color: red;'>❌ ZERO AMOUNT DETECTED!</p>";
                    } else {
                        echo "<p style='color: green;'>✅ Amount looks good: " . $details['amount'] . "</p>";
                    }
                }
            }
        } else {
            echo "<p style='color: red;'>❌ Invalid JSON format</p>";
        }
        echo "<hr>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    echo "<br><strong>Please update database connection details if needed</strong>";
}
?>
