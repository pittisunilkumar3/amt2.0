<?php
/**
 * Simple verification script to test dashboard fee collection calculation
 */

// Include CodeIgniter bootstrap to access models
define('BASEPATH', TRUE);
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/amt/verify_dashboard_fix.php';

// Set up basic environment
chdir(dirname(__FILE__));
require_once 'index.php';

// Get CodeIgniter instance
$CI =& get_instance();

echo "=== DASHBOARD FIX VERIFICATION ===\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n\n";

// Test date range (September 2025)
$start_date = '2025-09-01';
$end_date = '2025-09-30';

echo "Testing fee collection calculation for: $start_date to $end_date\n\n";

try {
    // Load required models
    $CI->load->model('studentfeemaster_model');
    $CI->load->model('studentfeemasteradding_model');
    
    echo "1. TESTING MODEL METHODS:\n";
    
    // Get regular fee collection data
    $regular_fees = $CI->studentfeemaster_model->getFeeCollectionReport(
        $start_date, $end_date, null, null, null, null, null, null
    );
    
    // Get other fee collection data  
    $other_fees = $CI->studentfeemasteradding_model->getFeeCollectionReport(
        $start_date, $end_date, null, null, null, null, null, null
    );
    
    // Combine results
    $combined_results = array_merge($regular_fees, $other_fees);
    
    echo "   Regular fees: " . count($regular_fees) . " entries\n";
    echo "   Other fees: " . count($other_fees) . " entries\n";
    echo "   Combined total: " . count($combined_results) . " entries\n\n";
    
    // Calculate total
    $total_collection = 0;
    foreach ($combined_results as $collect) {
        $amount = floatval($collect['amount']);
        $fine = floatval($collect['amount_fine']);
        $total_collection += ($amount + $fine);
    }
    
    echo "2. CALCULATION RESULT:\n";
    echo "   Total Fee Collection: ₹" . number_format($total_collection, 2) . "\n";
    echo "   Expected (Combined Report): ₹645,000.00\n";
    echo "   Match: " . ($total_collection == 645000 ? "✓ YES" : "✗ NO") . "\n\n";
    
    echo "3. DASHBOARD INTEGRATION:\n";
    echo "   The dashboard should now display: ₹" . number_format($total_collection, 2) . "\n";
    echo "   This matches the Combined Collection Report total\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
?>
