<?php
// Check current currency settings to understand the proper conversion
echo "<h2>Currency Settings Analysis</h2>";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>School Settings - Currency Related</h3>";
    $stmt = $pdo->query("SELECT * FROM sch_settings LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($settings) {
        echo "<table border='1'>";
        foreach ($settings as $key => $value) {
            if (strpos(strtolower($key), 'currency') !== false || strpos(strtolower($key), 'price') !== false) {
                echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
            }
        }
        echo "</table>";
        
        echo "<h4>All Settings (for reference):</h4>";
        echo "<table border='1' style='font-size: 12px;'>";
        foreach ($settings as $key => $value) {
            echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
    }
    
    echo "<h3>Currency Table</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'currencies'");
    if ($stmt->rowCount() > 0) {
        $stmt = $pdo->query("SELECT * FROM currencies");
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Code</th><th>Symbol</th><th>Base Price</th><th>Is Active</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No currencies table found</p>";
    }
    
    echo "<h3>Sample Fee Amounts</h3>";
    $stmt = $pdo->query("SELECT amount, balance FROM student_fees_master WHERE amount > 0 LIMIT 5");
    echo "<table border='1'>";
    echo "<tr><th>Fee Amount</th><th>Balance</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>" . $row['amount'] . "</td><td>" . $row['balance'] . "</td></tr>";
    }
    echo "</table>";
    
    echo "<h3>Sample Advance Payments</h3>";
    $stmt = $pdo->query("SELECT amount FROM advance_payments WHERE amount > 0 LIMIT 5");
    echo "<table border='1'>";
    echo "<tr><th>Advance Amount</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr><td>" . $row['amount'] . "</td></tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
