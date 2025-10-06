<?php
require_once 'index.php';

// Test discount creation directly
$CI =& get_instance();
$CI->load->model('Studentfeemaster_model');

echo "Testing discount creation..." . PHP_EOL;

// Sample data for discount request
$test_data = array(
    'is_active' => 1,
    'approval_status' => 0,
    'student_session_id' => 1, // Use an existing student session ID
    'amount' => 100,
    'date' => date('Y-m-d'),
    'description' => 'Test discount request',
    'student_fees_master_id' => 1, // Use an existing fees master ID
    'fee_groups_feetype_id' => 1, // Use an existing fee type ID
    'session_id' => 1, // Use current session
);

try {
    $CI->Studentfeemaster_model->adddiscountstudentfee($test_data);
    echo "✅ Discount request created successfully!" . PHP_EOL;
    
    // Check if it was actually inserted
    $CI->load->database();
    $query = $CI->db->get_where('fees_discount_approval', array('student_session_id' => 1, 'amount' => 100));
    $result = $query->result_array();
    
    if (!empty($result)) {
        echo "✅ Discount request found in database:" . PHP_EOL;
        foreach ($result as $row) {
            echo "ID: " . $row['id'] . ", Amount: " . $row['amount'] . ", Status: " . $row['approval_status'] . PHP_EOL;
        }
    } else {
        echo "❌ Discount request not found in database" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "❌ Error creating discount request: " . $e->getMessage() . PHP_EOL;
}
?>
