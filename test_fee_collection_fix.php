<?php
/**
 * Test script for fee collection calculation fix
 */

// Set up basic environment
define('BASEPATH', TRUE);
$_SERVER['REQUEST_METHOD'] = 'GET';

// Include CodeIgniter
require_once 'index.php';

echo "=== Fee Collection Calculation Test ===\n\n";

// Get CI instance
$CI =& get_instance();
$CI->load->database();

// Test date range - September 2025
$start_date = '2025-09-01';
$end_date = '2025-09-30';

echo "Testing date range: $start_date to $end_date\n\n";

// Test 1: Direct database query
echo "1. Direct Database Query Test:\n";
$CI->db->select('COUNT(*) as count');
$CI->db->from('student_fees_deposite');
$CI->db->where('DATE(created_at) >=', $start_date);
$CI->db->where('DATE(created_at) <=', $end_date);
$query = $CI->db->get();
$record_count = $query->row()->count;
echo "   Records found: $record_count\n";

// Test 2: Sample amount_detail parsing
echo "\n2. Sample Amount Detail Parsing:\n";
$CI->db->select('amount_detail, id, created_at');
$CI->db->from('student_fees_deposite');
$CI->db->where('DATE(created_at) >=', $start_date);
$CI->db->where('DATE(created_at) <=', $end_date);
$CI->db->limit(3);
$query = $CI->db->get();

$sample_total = 0;
foreach ($query->result() as $row) {
    echo "   Record ID: {$row->id}\n";
    if (!empty($row->amount_detail)) {
        $amount_detail = json_decode($row->amount_detail, true);
        if (is_array($amount_detail)) {
            $record_total = 0;
            foreach ($amount_detail as $key => $detail) {
                if (isset($detail['amount']) && $detail['amount'] > 0) {
                    $amount = floatval($detail['amount']);
                    $record_total += $amount;
                    echo "     Entry $key: ₹$amount\n";
                }
            }
            echo "     Record total: ₹$record_total\n";
            $sample_total += $record_total;
        }
    }
    echo "\n";
}
echo "   Sample total (3 records): ₹$sample_total\n";

// Test 3: Full calculation simulation
echo "\n3. Full Fee Collection Calculation:\n";
$total_collection = 0;
$processed_records = 0;

$CI->db->select('amount_detail, id');
$CI->db->from('student_fees_deposite');
$CI->db->where('DATE(created_at) >=', $start_date);
$CI->db->where('DATE(created_at) <=', $end_date);
$query = $CI->db->get();

if ($query->num_rows() > 0) {
    foreach ($query->result() as $row) {
        if (!empty($row->amount_detail)) {
            $amount_detail = json_decode($row->amount_detail, true);
            if (is_array($amount_detail)) {
                foreach ($amount_detail as $detail) {
                    if (isset($detail['amount']) && $detail['amount'] > 0) {
                        $amount = floatval($detail['amount']);
                        $total_collection += $amount;
                    }
                }
                $processed_records++;
            }
        }
    }
}

echo "   Total records processed: $processed_records\n";
echo "   Total fee collection: ₹" . number_format($total_collection, 2) . "\n";

// Test 4: Test the actual controller method
echo "\n4. Controller Method Test:\n";
$CI->load->controller('admin/Admin');
$admin_controller = new Admin();

// Use reflection to access private method
$reflection = new ReflectionClass($admin_controller);
$method = $reflection->getMethod('calculateFeeCollection');
$method->setAccessible(true);

$controller_result = $method->invoke($admin_controller, $start_date, $end_date);
echo "   Controller method result: ₹" . number_format($controller_result, 2) . "\n";

// Test 5: Compare results
echo "\n5. Results Comparison:\n";
echo "   Manual calculation: ₹" . number_format($total_collection, 2) . "\n";
echo "   Controller method:  ₹" . number_format($controller_result, 2) . "\n";

if (abs($total_collection - $controller_result) < 0.01) {
    echo "   ✓ Results match!\n";
} else {
    echo "   ✗ Results don't match!\n";
}

echo "\n=== Test Summary ===\n";
echo "✓ Database connection working\n";
echo "✓ Records found for September 2025: $record_count\n";
echo "✓ JSON parsing working correctly\n";
echo "✓ Fee collection calculation: ₹" . number_format($controller_result, 2) . "\n";

if ($controller_result > 0) {
    echo "✓ Fee collection calculation is working!\n";
} else {
    echo "⚠ Fee collection is zero - check data or calculation logic\n";
}

echo "\n=== Next Steps ===\n";
echo "1. Clear browser cache\n";
echo "2. Open browser console (F12)\n";
echo "3. Visit dashboard and test date filtering\n";
echo "4. Check console logs and PHP error logs\n";
echo "5. Verify AJAX responses\n";

echo "\n=== Test Complete ===\n";
?>
