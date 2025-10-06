<?php
// Simple test script to verify the fix
require_once 'index.php';

// Simulate POST data for testing
$_POST = array(
    'search_type' => 'this_year',
    'class_id' => array('1', '2'),
    'section_id' => array('1', '2'),
    'sch_session_id' => array('1')
);

// Test the controller method
$CI =& get_instance();
$CI->load->controller('financereports');

try {
    echo "Testing fee collection report with array inputs...\n";
    $CI->financereports->fee_collection_report_columnwise();
    echo "Test completed successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
