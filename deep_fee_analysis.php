<?php
// Deep analysis of the fee collection system
echo "<h2>Deep Analysis: Fee Collection vs Advance Payment Logic</h2>";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>1. Analyzing Recent Zero Amount Records</h3>";
    $stmt = $pdo->query("
        SELECT id, amount_detail, created_at 
        FROM student_fees_deposite 
        WHERE created_at >= '2025-09-05' 
        ORDER BY id DESC LIMIT 5
    ");
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Amount Detail Analysis</th><th>Created</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>";
        
        $decoded = json_decode($row['amount_detail'], true);
        if ($decoded) {
            foreach ($decoded as $inv_no => $details) {
                echo "<strong>Invoice $inv_no:</strong><br>";
                echo "• Amount: " . $details['amount'] . "<br>";
                echo "• Original Amount: " . (isset($details['original_amount']) ? $details['original_amount'] : 'N/A') . "<br>";
                echo "• Advance Applied: " . (isset($details['advance_applied']) ? $details['advance_applied'] : 'N/A') . "<br>";
                echo "• Payment Mode: " . $details['payment_mode'] . "<br>";
                
                if ($details['amount'] == 0) {
                    echo "<span style='color: red;'>❌ ISSUE: Amount is 0!</span><br>";
                    echo "<span style='color: blue;'>🤔 ANALYSIS: This should show the actual fee amount, not the cash balance!</span><br>";
                }
                echo "<hr>";
            }
        }
        
        echo "</td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>2. Checking Correct Fee Records (Non-Zero)</h3>";
    $stmt = $pdo->query("
        SELECT id, amount_detail 
        FROM student_fees_deposite 
        WHERE amount_detail LIKE '%\"amount\":24000%' 
        OR amount_detail LIKE '%\"amount\":3%'
        ORDER BY id DESC LIMIT 3
    ");
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Correct Amount Examples</th></tr>";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>";
        
        $decoded = json_decode($row['amount_detail'], true);
        if ($decoded) {
            foreach ($decoded as $inv_no => $details) {
                echo "<strong>Invoice $inv_no:</strong><br>";
                echo "• Amount: " . $details['amount'] . " ✅<br>";
                echo "• Payment Mode: " . $details['payment_mode'] . "<br>";
                echo "• Has Advance Applied: " . (isset($details['advance_applied']) ? 'Yes' : 'No') . "<br>";
            }
        }
        
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h3>3. Fundamental Issue Analysis</h3>";
    echo "<div style='background: #ffffcc; padding: 10px; border: 1px solid #ccc;'>";
    echo "<h4>THE CORE PROBLEM:</h4>";
    echo "<p><strong>Current Wrong Logic:</strong></p>";
    echo "<p>amount = original_amount - advance_applied</p>";
    echo "<p>Example: amount = 1 - 1 = 0 ❌</p>";
    echo "<br>";
    echo "<p><strong>Correct Logic Should Be:</strong></p>";
    echo "<p>amount = original_amount (always show the actual fee amount)</p>";
    echo "<p>advance_applied = amount paid from advance</p>";
    echo "<p>cash_required = original_amount - advance_applied</p>";
    echo "<br>";
    echo "<p><strong>In Fee Collection:</strong></p>";
    echo "<p>• 'amount' field should represent the FEE AMOUNT being collected</p>";
    echo "<p>• 'advance_applied' field should show how much came from advance</p>";
    echo "<p>• The system should track cash vs advance separately</p>";
    echo "</div>";
    
    echo "<h3>4. Real-World Scenario</h3>";
    echo "<div style='background: #ccffcc; padding: 10px; border: 1px solid #ccc;'>";
    echo "<p><strong>Student owes:</strong> ₹1000 tuition fee</p>";
    echo "<p><strong>Advance balance:</strong> ₹500</p>";
    echo "<p><strong>Current (Wrong) Recording:</strong></p>";
    echo "<p>• amount: ₹500 (1000-500) ❌ Wrong - this looks like only ₹500 was collected</p>";
    echo "<br>";
    echo "<p><strong>Correct Recording Should Be:</strong></p>";
    echo "<p>• amount: ₹1000 ✅ (full fee amount collected)</p>";
    echo "<p>• advance_applied: ₹500 ✅ (₹500 from advance)</p>";
    echo "<p>• cash_collected: ₹500 ✅ (₹500 cash + ₹500 advance = ₹1000 total)</p>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
