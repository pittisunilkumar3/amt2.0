<?php
// Debug script to test what happens with amount conversion
echo "<h2>Amount Conversion Test</h2>";

// Include the custom helper
include 'application/helpers/custom_helper.php';

// Test different amount formats
$test_amounts = [
    "1.00",
    "1",
    "0.50",
    "100.00",
    "0",
    ""
];

echo "<table border='1'>";
echo "<tr><th>Input</th><th>floatval()</th><th>convertCurrencyFormatToBaseAmount()</th><th>convertBaseAmountCurrencyFormat()</th></tr>";

foreach ($test_amounts as $amount) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($amount) . "</td>";
    echo "<td>" . floatval($amount) . "</td>";
    
    // Test currency conversion
    $converted = @convertCurrencyFormatToBaseAmount($amount);
    echo "<td>" . ($converted !== false ? $converted : 'FAILED') . "</td>";
    
    // Test reverse conversion
    $reverse = @convertBaseAmountCurrencyFormat(floatval($amount));
    echo "<td>" . ($reverse !== false ? $reverse : 'FAILED') . "</td>";
    
    echo "</tr>";
}

echo "</table>";

echo "<h3>Currency Settings Check</h3>";

// Check if we can access the database to see currency settings
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check school settings for currency
    $stmt = $pdo->query("SELECT * FROM sch_settings WHERE name LIKE '%currency%' OR name LIKE '%price%'");
    
    echo "<table border='1'>";
    echo "<tr><th>Setting Name</th><th>Setting Value</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['value']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}
?>
