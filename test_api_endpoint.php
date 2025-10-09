<?php
// Simple test script to verify the Fee Group-wise Collection Report API
// This bypasses authentication to test if the data is correctly calculated

// Include CodeIgniter bootstrap
define('BASEPATH', TRUE);
$system_path = 'system';
$application_folder = 'application';

// Set the current directory correctly
chdir(__DIR__);

// Include the CodeIgniter bootstrap
require_once $system_path.'/core/CodeIgniter.php';

// Load the model directly
$CI =& get_instance();
$CI->load->model('feegroupwise_model');

// Test parameters for 2025-26 session
$test_params = array(
    'session_id' => 9, // Assuming 2025-26 session has ID 9
    'class_id' => '',
    'section_id' => '',
    'fee_group_id' => '',
    'from_date' => '',
    'to_date' => '',
    'date_grouping' => 'none'
);

echo "Testing Fee Group-wise Collection Report API...\n";
echo "==============================================\n\n";

try {
    // Call the model method
    $result = $CI->feegroupwise_model->getFeeGroupwiseCollection($test_params);
    
    echo "SUCCESS: API call completed\n";
    echo "Number of records returned: " . count($result) . "\n\n";
    
    // Show first 5 records to verify the fix
    echo "Sample Records (showing first 5):\n";
    echo "==================================\n";
    
    $count = 0;
    foreach ($result as $record) {
        if ($count >= 5) break;
        
        echo "Fee Group: " . $record->fee_group_name . "\n";
        echo "Total Amount: ₹" . number_format($record->total_amount, 2) . "\n";
        echo "Collected: ₹" . number_format($record->amount_collected, 2) . "\n";
        echo "Balance: ₹" . number_format($record->balance_amount, 2) . "\n";
        
        if ($record->balance_amount < 0) {
            echo "❌ NEGATIVE BALANCE DETECTED!\n";
        } else {
            echo "✅ Positive balance\n";
        }
        
        echo "---\n";
        $count++;
    }
    
    // Calculate summary statistics
    $total_amount = 0;
    $total_collected = 0;
    $negative_count = 0;
    
    foreach ($result as $record) {
        $total_amount += $record->total_amount;
        $total_collected += $record->amount_collected;
        if ($record->balance_amount < 0) {
            $negative_count++;
        }
    }
    
    $total_balance = $total_amount - $total_collected;
    
    echo "\nSUMMARY STATISTICS:\n";
    echo "==================\n";
    echo "Total Fee Groups: " . count($result) . "\n";
    echo "Total Amount: ₹" . number_format($total_amount, 2) . "\n";
    echo "Amount Collected: ₹" . number_format($total_collected, 2) . "\n";
    echo "Balance Amount: ₹" . number_format($total_balance, 2) . "\n";
    echo "Fee Groups with Negative Balance: " . $negative_count . "\n";
    
    if ($negative_count == 0) {
        echo "\n🎉 SUCCESS: No negative balances found! The fix worked!\n";
    } else {
        echo "\n⚠️  WARNING: " . $negative_count . " fee groups still have negative balances\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
?>
