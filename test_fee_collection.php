<?php
// Simple test to check if the basic controller loading works
define('BASEPATH', TRUE);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Testing fee collection endpoint...\n";

// Test direct POST to the endpoint
$url = 'http://localhost/amt/studentfee/addstudentfee';
$data = array(
    'student_fees_master_id' => '1',
    'date' => '05/09/2025',
    'fee_groups_feetype_id' => '1',
    'amount' => '1',
    'amount_discount' => '0',
    'amount_fine' => '0',
    'payment_mode' => 'Cash',
    'student_session_id' => '8',
    'fee_category' => 'transport',
    'transport_fees_id' => '1',
    'action' => 'collect'
);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

echo "Response: " . $result . "\n";

if ($result === FALSE) {
    echo "Error occurred\n";
} else {
    echo "Success: " . $result . "\n";
}
?>
