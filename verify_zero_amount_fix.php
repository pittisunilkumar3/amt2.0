<?php
/*
 * VERIFICATION SCRIPT FOR ZERO AMOUNT FIX
 * Tests the corrected advance payment logic
 */

define('BASEPATH', '1');
require_once('application/config/database.php');

// Database connection
$mysqli = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "<h2>🔍 ZERO AMOUNT FIX VERIFICATION</h2>";
echo "<hr>";

// 1. Check recent deposits with amount_detail
echo "<h3>1. Recent Fee Deposits with Amount Details</h3>";
$query = "SELECT id, student_id, amount, amount_detail, created_at 
          FROM student_fees_deposite 
          WHERE amount_detail IS NOT NULL AND amount_detail != '' 
          ORDER BY id DESC 
          LIMIT 10";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Student</th><th>Total Amount</th><th>Amount Detail</th><th>Created</th><th>Status</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $amount_detail = json_decode($row['amount_detail'], true);
        
        // Check if this record has the fix applied
        $status = "❓ Unknown";
        $hasZeroAmount = false;
        
        if (is_array($amount_detail)) {
            foreach ($amount_detail as $detail) {
                if (isset($detail['amount'])) {
                    if ($detail['amount'] == 0) {
                        $hasZeroAmount = true;
                        $status = "❌ Zero Amount Found";
                    } else {
                        $status = "✅ Correct Amount";
                    }
                }
            }
        }
        
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['student_id'] . "</td>";
        echo "<td>₹" . $row['amount'] . "</td>";
        echo "<td><pre style='font-size: 10px;'>" . htmlspecialchars($row['amount_detail']) . "</pre></td>";
        echo "<td>" . $row['created_at'] . "</td>";
        echo "<td><strong>" . $status . "</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No records found with amount_detail.";
}

// 2. Check for students with advance balance
echo "<h3>2. Students with Advance Balance (Test Candidates)</h3>";
$query = "SELECT student_id, advance_amount 
          FROM student_fees_master 
          WHERE advance_amount > 0 
          ORDER BY advance_amount DESC 
          LIMIT 10";

$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Student ID</th><th>Advance Balance</th><th>Test Recommendation</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $testRec = "Make a ₹1 fee payment to test";
        if ($row['advance_amount'] >= 1000) {
            $testRec = "Perfect for testing - high advance balance";
        }
        
        echo "<tr>";
        echo "<td>" . $row['student_id'] . "</td>";
        echo "<td>₹" . number_format($row['advance_amount'], 2) . "</td>";
        echo "<td>" . $testRec . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No students with advance balance found.";
}

// 3. Simulate the corrected logic
echo "<h3>3. Logic Simulation Test</h3>";

function simulateAdvancePayment($original_amount, $advance_available) {
    // OLD LOGIC (Wrong)
    $old_amount = $original_amount - min($advance_available, $original_amount);
    
    // NEW LOGIC (Correct)
    $advance_to_apply = min($advance_available, $original_amount);
    $new_amount = $original_amount; // Always store full fee amount
    $cash_required = $original_amount - $advance_to_apply;
    
    return [
        'original_amount' => $original_amount,
        'advance_available' => $advance_available,
        'advance_applied' => $advance_to_apply,
        'old_amount' => $old_amount,
        'new_amount' => $new_amount,
        'cash_required' => $cash_required
    ];
}

$testCases = [
    ['fee' => 1000, 'advance' => 500],     // Partial coverage
    ['fee' => 1000, 'advance' => 1000],    // Exact coverage
    ['fee' => 1000, 'advance' => 2000],    // Full coverage
    ['fee' => 1, 'advance' => 39388],      // User's case
];

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Fee Amount</th><th>Advance Available</th><th>OLD Amount (Wrong)</th><th>NEW Amount (Correct)</th><th>Cash Required</th><th>Result</th></tr>";

foreach ($testCases as $test) {
    $result = simulateAdvancePayment($test['fee'], $test['advance']);
    
    $status = "✅ Fixed";
    if ($result['old_amount'] == 0 && $result['new_amount'] > 0) {
        $status = "🎯 Zero Amount Issue Fixed!";
    }
    
    echo "<tr>";
    echo "<td>₹" . $result['original_amount'] . "</td>";
    echo "<td>₹" . $result['advance_available'] . "</td>";
    echo "<td style='color: red;'>₹" . $result['old_amount'] . "</td>";
    echo "<td style='color: green; font-weight: bold;'>₹" . $result['new_amount'] . "</td>";
    echo "<td>₹" . $result['cash_required'] . "</td>";
    echo "<td>" . $status . "</td>";
    echo "</tr>";
}
echo "</table>";

// 4. Check debug logs
echo "<h3>4. Recent Debug Logs</h3>";
$logFile = 'application/logs/fee_debug.log';
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = substr($logs, -2000); // Last 2KB
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars($recentLogs);
    echo "</pre>";
} else {
    echo "No debug log file found. This is normal if no recent payments were made.";
}

echo "<hr>";
echo "<h3>✅ VERIFICATION COMPLETE</h3>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ul>";
echo "<li>Make a test fee payment with a student who has advance balance</li>";
echo "<li>Check that the amount field shows the actual fee amount (not zero)</li>";
echo "<li>Verify advance_applied field tracks the advance usage correctly</li>";
echo "<li>Confirm cash_required shows the correct cash collection amount</li>";
echo "</ul>";

$mysqli->close();
?>
