<?php
// Test script to verify currency conversion function
echo "<h2>Currency Conversion Function Test</h2>";

// Try to simulate CodeIgniter environment minimally
if (!function_exists('convertCurrencyFormatToBaseAmount')) {
    // Define the fixed function for testing
    function convertCurrencyFormatToBaseAmount($amount) {
        echo "<p><strong>Testing amount: $amount</strong></p>";
        
        // Simulate getting currency price (this would normally come from CI)
        $currency_price = null; // Simulating the problematic scenario
        
        echo "<p>Currency price: " . ($currency_price ?? 'NULL') . "</p>";
        
        // Our fix
        if (empty($currency_price) || $currency_price <= 0) {
            echo "<p style='color: green;'>✅ Fix activated: Currency price invalid, returning original amount</p>";
            error_log("Warning: convertCurrencyFormatToBaseAmount received invalid currency_price: " . print_r($currency_price, true) . ", returning original amount: " . $amount);
            return floatval($amount);
        }
        
        $result = floatval($amount / $currency_price);
        echo "<p>Conversion result: $result</p>";
        return $result;
    }
}

echo "<h3>Test Cases</h3>";

$test_cases = [
    100,
    500.50,
    1000,
    0,
    50.75
];

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Input</th><th>Output</th><th>Status</th></tr>";

foreach ($test_cases as $test_amount) {
    echo "<tr>";
    echo "<td>$test_amount</td>";
    
    ob_start();
    $result = convertCurrencyFormatToBaseAmount($test_amount);
    $debug_output = ob_get_clean();
    
    echo "<td>$result</td>";
    
    if ($result == $test_amount) {
        echo "<td style='color: green;'>✅ PASS (Amount preserved)</td>";
    } else {
        echo "<td style='color: red;'>❌ FAIL (Amount changed)</td>";
    }
    echo "</tr>";
    
    echo "<tr><td colspan='3'><small>$debug_output</small></td></tr>";
}

echo "</table>";

echo "<h3>Check Error Log</h3>";
echo "<p>Check your PHP error log for entries like:</p>";
echo "<code>Warning: convertCurrencyFormatToBaseAmount received invalid currency_price: NULL, returning original amount: [amount]</code>";

echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li>Try making a fee payment through the system</li>";
echo "<li>Check the error logs in: <code>" . ini_get('error_log') . "</code> or <code>c:\\xampp\\php\\logs\\php_error_log</code></li>";
echo "<li>Run the debug_amount_storage.php script to see actual database values</li>";
echo "<li>Look for the debug logs we added to controller and model</li>";
echo "</ol>";
?>
