<?php
// Debug the specific receipt that shows zero amount
echo "<h2>Debug Receipt 54/1 with Zero Amount</h2>";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check for records with invoice pattern 54/1
    echo "<h3>Looking for Receipt 54/1</h3>";
    
    // In school systems, the receipt might be in different formats, let me check recent records
    $stmt = $pdo->query("SELECT * FROM student_fees_deposite WHERE created_at >= '2025-09-05' ORDER BY id DESC LIMIT 10");
    
    echo "<table border='1' style='font-size: 12px;'>";
    echo "<tr><th>ID</th><th>Amount Detail</th><th>Created At</th><th>Status</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td style='max-width: 400px; word-break: break-all;'>" . htmlspecialchars($row['amount_detail']) . "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
        
        // Decode and analyze the JSON
        $decoded = json_decode($row['amount_detail'], true);
        if ($decoded) {
            echo "<tr><td colspan='4'>";
            echo "<strong>Analysis for ID " . $row['id'] . ":</strong><br>";
            foreach ($decoded as $inv_no => $details) {
                echo "Invoice $inv_no: ";
                echo "Amount=" . $details['amount'] . ", ";
                echo "Original=" . (isset($details['original_amount']) ? $details['original_amount'] : 'N/A') . ", ";
                echo "Advance Applied=" . (isset($details['advance_applied']) ? $details['advance_applied'] : 'N/A');
                echo "<br>";
                
                if ($details['amount'] == 0) {
                    echo "<span style='color: red;'>❌ ZERO AMOUNT DETECTED!</span><br>";
                    if (isset($details['advance_applied']) && $details['advance_applied'] > 0) {
                        echo "<span style='color: orange;'>📝 Advance payment was applied: " . $details['advance_applied'] . "</span><br>";
                    }
                }
            }
            echo "</td></tr>";
        }
    }
    echo "</table>";
    
    // Let me also check the fee calculation methods to see if there's an issue
    echo "<h3>Debug Raw Calculation</h3>";
    
    // Simulate what might be happening
    $test_amount = "1.00"; // What user might input
    $converted_amount = floatval($test_amount); // Basic conversion
    $advance_balance = 1.0;
    $advance_applied = min($advance_balance, $converted_amount);
    $remaining = $converted_amount - $advance_applied;
    
    echo "<p>Test calculation:</p>";
    echo "<p>Input: $test_amount</p>";
    echo "<p>Converted: $converted_amount</p>";
    echo "<p>Advance balance: $advance_balance</p>";
    echo "<p>Advance applied: $advance_applied</p>";
    echo "<p>Remaining: $remaining</p>";
    
    if ($remaining == 0) {
        echo "<p style='color: red;'>❌ This would result in zero amount!</p>";
        echo "<p style='color: blue;'>💡 Issue: Basic conversion without proper currency handling</p>";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
