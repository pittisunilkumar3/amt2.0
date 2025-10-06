<?php
// Check error logs for hostel fee debug information
$possible_log_paths = [
    'C:\xampp\apache\logs\error.log',
    'C:\xampp\php\logs\php_error_log',
    ini_get('error_log'),
    dirname(__FILE__) . '/error.log',
    '/tmp/php_error.log'
];

echo "<h2>Error Log Analysis</h2>";

foreach ($possible_log_paths as $log_path) {
    if (empty($log_path)) continue;
    
    echo "<h3>Checking: $log_path</h3>";
    
    if (file_exists($log_path)) {
        echo "<p style='color: green;'>✓ File exists</p>";
        
        $lines = file($log_path);
        if ($lines === false) {
            echo "<p style='color: red;'>✗ Cannot read file</p>";
            continue;
        }
        
        $hostel_debug_lines = [];
        $recent_lines = array_slice($lines, -200); // Last 200 lines
        
        foreach ($recent_lines as $line_num => $line) {
            if (stripos($line, 'HOSTEL FEE DEBUG') !== false) {
                $hostel_debug_lines[] = $line;
            }
        }
        
        if (!empty($hostel_debug_lines)) {
            echo "<h4>Hostel Fee Debug Messages Found:</h4>";
            echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
            echo "<pre>";
            foreach ($hostel_debug_lines as $line) {
                echo htmlspecialchars($line);
            }
            echo "</pre>";
            echo "</div>";
        } else {
            echo "<p>No hostel fee debug messages found in recent logs</p>";
        }
        
        // Show last 10 lines for context
        echo "<h4>Last 10 Log Lines:</h4>";
        echo "<div style='background: #f1f1f1; padding: 10px; border-radius: 5px;'>";
        echo "<pre>";
        $last_lines = array_slice($lines, -10);
        foreach ($last_lines as $line) {
            echo htmlspecialchars($line);
        }
        echo "</pre>";
        echo "</div>";
        
        break; // Found a working log file
        
    } else {
        echo "<p style='color: red;'>✗ File not found</p>";
    }
}

// Check PHP error reporting settings
echo "<h3>PHP Error Settings:</h3>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>error_reporting</td><td>" . error_reporting() . "</td></tr>";
echo "<tr><td>log_errors</td><td>" . (ini_get('log_errors') ? 'On' : 'Off') . "</td></tr>";
echo "<tr><td>error_log</td><td>" . (ini_get('error_log') ?: 'Not set') . "</td></tr>";
echo "<tr><td>display_errors</td><td>" . (ini_get('display_errors') ? 'On' : 'Off') . "</td></tr>";
echo "</table>";

// Test error logging
echo "<h3>Test Error Logging:</h3>";
$test_message = "HOSTEL FEE DEBUG TEST - " . date('Y-m-d H:i:s');
if (error_log($test_message)) {
    echo "<p style='color: green;'>✓ Error logging is working</p>";
} else {
    echo "<p style='color: red;'>✗ Error logging failed</p>";
}

// Manual debug test
echo "<h3>Manual Debug Test:</h3>";
echo "<p>Click this button to test hostel fee collection manually:</p>";
echo "<form method='post'>";
echo "<button type='submit' name='test_hostel' style='background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px;'>Test Hostel Fee Collection</button>";
echo "</form>";

if (isset($_POST['test_hostel'])) {
    echo "<h4>Simulating Hostel Fee Collection:</h4>";
    
    // Simulate the data that should be sent
    $test_data = [
        'hostel_fees_id' => '5',
        'student_session_id' => '8', 
        'fee_category' => 'hostel',
        'amount' => '100',
        'date' => date('Y-m-d'),
        'payment_mode' => 'Cash',
        'accountname' => '1'
    ];
    
    echo "<p>Test data:</p>";
    echo "<pre>" . print_r($test_data, true) . "</pre>";
    
    // Log the test
    error_log("HOSTEL FEE DEBUG - Manual test data: " . print_r($test_data, true));
    
    echo "<p style='color: green;'>Test data logged. Check error logs above for the debug message.</p>";
}
?>
