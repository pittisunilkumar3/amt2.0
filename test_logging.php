<?php
// Test if PHP error logging is working
ini_set('log_errors', 1);
ini_set('error_log', 'c:\xampp\htdocs\amt\test_error.log');

error_log("=== PHP ERROR LOG TEST ===");
error_log("Current time: " . date('Y-m-d H:i:s'));
error_log("Test message: PHP error logging is working!");

echo "Test completed. Check for 'test_error.log' file in the amt directory.";

// Also try to log to the application logs
$app_log_path = __DIR__ . '/application/logs/fee_debug.log';
error_log("=== APPLICATION LOG TEST ===", 3, $app_log_path);
error_log("Current time: " . date('Y-m-d H:i:s'), 3, $app_log_path);
error_log("Test message: Application logging is working!", 3, $app_log_path);

echo "<br>Application log path: " . $app_log_path;
echo "<br>Application log test completed.";
?>
