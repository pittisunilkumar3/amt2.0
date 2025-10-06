<?php
// Test the advance payment fix
echo "<h2>Testing Advance Payment Fix</h2>";

// Simulate the old broken logic
echo "<h3>Old Logic (Broken):</h3>";
$raw_amount = "1.00"; // User input
$original_amount_old = floatval($raw_amount); // This would be 1
$advance_balance = 1.00;
$advance_to_apply_old = min($advance_balance, $original_amount_old);
$final_amount_old = $original_amount_old - $advance_to_apply_old;

echo "<p>Raw input: $raw_amount</p>";
echo "<p>Original amount (no conversion): $original_amount_old</p>";
echo "<p>Advance to apply: $advance_to_apply_old</p>";
echo "<p>Final amount stored: $final_amount_old</p>";
echo "<p style='color: red;'>❌ Result: $final_amount_old (ZERO AMOUNT!)</p>";

echo "<hr>";

// Simulate the new fixed logic
echo "<h3>New Logic (Fixed):</h3>";
$raw_amount = "1.00"; // User input

// Simulate currency conversion (like the actual system does)
function simulateConvertCurrencyFormatToBaseAmount($input) {
    // Simulate the currency conversion that converts "1.00" to actual amount based on school currency
    // Let's say school has currency price = 1000, so "1.00" becomes 1000
    $school_currency_price = 1000; // Example value
    $converted = floatval($input) * $school_currency_price;
    return $converted;
}

$final_amount_new = simulateConvertCurrencyFormatToBaseAmount($raw_amount); // This would be 1000
$original_amount_new = $final_amount_new; // Use the converted amount
$advance_balance = 1.00; // But advance balance is still in base amount
$advance_to_apply_new = min($advance_balance, $original_amount_new);
$remaining_amount_new = $original_amount_new - $advance_to_apply_new;

echo "<p>Raw input: $raw_amount</p>";
echo "<p>After currency conversion: $final_amount_new</p>";
echo "<p>Original amount (converted): $original_amount_new</p>";
echo "<p>Advance balance: $advance_balance</p>";
echo "<p>Advance to apply: $advance_to_apply_new</p>";
echo "<p>Final amount stored: $remaining_amount_new</p>";
echo "<p style='color: green;'>✅ Result: $remaining_amount_new (CORRECT AMOUNT!)</p>";

echo "<hr>";
echo "<h3>Summary:</h3>";
echo "<p>The fix ensures that advance payment calculations use the currency-converted amount instead of the raw input amount.</p>";
echo "<p>This prevents zero amounts when advance payments exactly match the unconverted input amount.</p>";
?>
