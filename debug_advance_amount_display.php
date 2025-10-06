<?php
/**
 * Debug script for advance payment fee collection amount display issue
 * This will help identify where the amount is not being displayed correctly
 */

echo "<h1>🔍 Advance Payment Fee Collection - Amount Display Debug</h1>";

echo "<h2>📊 Amount Flow Analysis</h2>";
echo "<ol>";
echo "<li><strong>Frontend Form:</strong> User enters amount in #amount field</li>";
echo "<li><strong>JavaScript Validation:</strong> Amount validated against advance balance</li>";
echo "<li><strong>Form Submission:</strong> collect_from_advance=1 sent to backend</li>";
echo "<li><strong>Backend Processing:</strong> Amount stored in amount_detail JSON</li>";
echo "<li><strong>Database Storage:</strong> amount_detail contains full amount</li>";
echo "<li><strong>Display Logic:</strong> Fee deposits calculated from amount_detail JSON</li>";
echo "</ol>";

echo "<h2>🔧 Current Implementation Status</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #f0f0f0;'>";
echo "<th>Component</th><th>Status</th><th>Details</th>";
echo "</tr>";

echo "<tr>";
echo "<td>Frontend Checkbox</td>";
echo "<td style='color: green;'>✅ Working</td>";
echo "<td>Checkbox appears when advance balance exists</td>";
echo "</tr>";

echo "<tr>";
echo "<td>Amount Validation</td>";
echo "<td style='color: green;'>✅ Working</td>";
echo "<td>Frontend validates amount against advance balance</td>";
echo "</tr>";

echo "<tr>";
echo "<td>Form Submission</td>";
echo "<td style='color: green;'>✅ Working</td>";
echo "<td>collect_from_advance parameter sent to backend</td>";
echo "</tr>";

echo "<tr>";
echo "<td>Backend Amount Storage</td>";
echo "<td style='color: orange;'>⚠️ Check Required</td>";
echo "<td>json_array['amount'] = \$original_amount (full amount stored)</td>";
echo "</tr>";

echo "<tr>";
echo "<td>Database amount_detail</td>";
echo "<td style='color: orange;'>⚠️ Check Required</td>";
echo "<td>JSON should contain full amount, not zero</td>";
echo "</tr>";

echo "<tr>";
echo "<td>Display Calculation</td>";
echo "<td style='color: orange;'>⚠️ Check Required</td>";
echo "<td>fee_paid = sum of amount_detail->amount values</td>";
echo "</tr>";

echo "</table>";

echo "<h2>🎯 Debugging Steps</h2>";
echo "<div style='background-color: #fffacd; padding: 15px; border-left: 5px solid #ffd700;'>";
echo "<h3>Step 1: Check Database Storage</h3>";
echo "<p>After collecting fee from advance payment, check the student_fees_deposite table:</p>";
echo "<pre>";
echo "SELECT id, amount_detail FROM student_fees_deposite 
WHERE id = [latest_record_id]";
echo "</pre>";
echo "<p>The amount_detail JSON should show:</p>";
echo "<pre>";
echo '{\"1\": {
    \"amount\": [full_amount],        # Should NOT be 0
    \"advance_applied\": [full_amount],
    \"cash_amount\": 0,
    \"payment_source\": \"advance\",
    \"amount_discount\": 0,
    \"amount_fine\": 0,
    ...
}}';
echo "</pre>";
echo "</div>";

echo "<div style='background-color: #f0f8ff; padding: 15px; border-left: 5px solid #4169e1; margin-top: 15px;'>";
echo "<h3>Step 2: Check Frontend Display</h3>";
echo "<p>The display logic should calculate fee_paid as:</p>";
echo "<pre>";
echo 'foreach ($fee_deposits as $fee_deposits_value) {
    $fee_paid = $fee_paid + $fee_deposits_value->amount;  # Should sum full amounts
}';
echo "</pre>";
echo "</div>";

echo "<div style='background-color: #f0fff0; padding: 15px; border-left: 5px solid #32cd32; margin-top: 15px;'>";
echo "<h3>Step 3: Test Scenario</h3>";
echo "<ol>";
echo "<li>Student has advance balance of ₹1000</li>";
echo "<li>User collects fee of ₹500 from advance payment</li>";
echo "<li>Database should store amount=500 in amount_detail</li>";
echo "<li>Frontend should display ₹500 as paid amount</li>";
echo "<li>Advance balance should reduce to ₹500</li>";
echo "</ol>";
echo "</div>";

echo "<h2>🔍 Key Code Locations</h2>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr style='background-color: #f0f0f0;'>";
echo "<th>File</th><th>Line/Function</th><th>Purpose</th>";
echo "</tr>";

echo "<tr>";
echo "<td>Studentfee.php</td>";
echo "<td>Line ~875: addstudentfee()</td>";
echo "<td>Backend processing - stores amount in json_array</td>";
echo "</tr>";

echo "<tr>";
echo "<td>Studentfeemaster_model.php</td>";
echo "<td>Line ~616: fee_deposit()</td>";
echo "<td>Database insertion - stores amount_detail JSON</td>";
echo "</tr>";

echo "<tr>";
echo "<td>studentAddfee.php</td>";
echo "<td>Lines ~810, ~1094: fee calculation</td>";
echo "<td>Frontend display - calculates fee_paid from amount_detail</td>";
echo "</tr>";

echo "</table>";

echo "<h2>🚨 Potential Issues</h2>";
echo "<ul>";
echo "<li><strong>Issue 1:</strong> amount_detail JSON storing 0 instead of full amount</li>";
echo "<li><strong>Issue 2:</strong> Display logic not recognizing advance payment amounts</li>";
echo "<li><strong>Issue 3:</strong> Frontend caching old amount_detail data</li>";
echo "<li><strong>Issue 4:</strong> Payment mode affecting amount display</li>";
echo "</ul>";

echo "<h2>💡 Recommended Actions</h2>";
echo "<div style='background-color: #ffe4e1; padding: 15px; border-left: 5px solid #ff6b6b;'>";
echo "<h3>Immediate Actions:</h3>";
echo "<ol>";
echo "<li>Test fee collection with advance payment</li>";
echo "<li>Check database record for amount_detail content</li>";
echo "<li>Verify frontend amount display after page refresh</li>";
echo "<li>Compare with normal cash payment amount display</li>";
echo "</ol>";
echo "</div>";

echo "<p style='margin-top: 30px;'><strong>📝 Note:</strong> The implementation should correctly store and display the full amount paid from advance payment, not zero. If the amount is showing as 0, the issue is likely in the amount_detail JSON structure or the display calculation logic.</p>";
?>
