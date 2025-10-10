<?php
/**
 * Debug Script for Other Collection Report
 * 
 * This script checks:
 * 1. If additional fees data exists in the database
 * 2. If the model methods are working correctly
 * 3. What data is being returned by getFeeCollectionReport()
 */

// Bootstrap CodeIgniter
define('BASEPATH', true);
require_once 'index.php';

// Get CI instance
$CI =& get_instance();
$CI->load->model('studentfeemasteradding_model');
$CI->load->model('studentfeemaster_model');
$CI->load->database();

echo "=== Other Collection Report - Debug Analysis ===\n\n";

// Test 1: Check if additional fees exist in database
echo "Test 1: Check Database for Additional Fees\n";
echo "--------------------------------------------\n";

$CI->db->select('COUNT(*) as count');
$CI->db->from('student_fees_depositeadding');
$query = $CI->db->get();
$total_other_fees = $query->row()->count;

echo "Total records in student_fees_depositeadding: $total_other_fees\n";

if ($total_other_fees > 0) {
    echo "✅ Additional fees data EXISTS in database\n\n";
    
    // Get a sample record
    $CI->db->select('*');
    $CI->db->from('student_fees_depositeadding');
    $CI->db->limit(1);
    $query = $CI->db->get();
    $sample = $query->row();
    
    echo "Sample Record:\n";
    echo "  - ID: " . $sample->id . "\n";
    echo "  - Student Fees Master ID: " . $sample->student_fees_master_id . "\n";
    echo "  - Fee Groups Feetype ID: " . $sample->fee_groups_feetype_id . "\n";
    echo "  - Amount Detail (first 200 chars): " . substr($sample->amount_detail, 0, 200) . "...\n\n";
    
    // Parse the JSON to see payment dates
    $amount_detail = json_decode($sample->amount_detail);
    if (!empty($amount_detail)) {
        echo "Payment Details in Sample Record:\n";
        $count = 0;
        foreach ($amount_detail as $key => $payment) {
            if ($count < 3) { // Show first 3 payments
                echo "  - Payment $key: Date=" . $payment->date . ", Amount=" . $payment->amount . "\n";
                $count++;
            }
        }
        echo "\n";
    }
} else {
    echo "❌ NO additional fees data found in database\n";
    echo "   This is why the report shows no data.\n\n";
}

// Test 2: Check current session
echo "Test 2: Check Current Session\n";
echo "------------------------------\n";

$current_session = $CI->studentfeemasteradding_model->current_session;
echo "Current Session ID: $current_session\n\n";

// Test 3: Test getFeeCollectionReport() with wide date range
echo "Test 3: Test getFeeCollectionReport() Method\n";
echo "----------------------------------------------\n";

$start_date = '2020-01-01';
$end_date = '2025-12-31';

echo "Testing with date range: $start_date to $end_date\n";
echo "Parameters: All filters empty (should return all records)\n\n";

$start_time = microtime(true);

try {
    $results = $CI->studentfeemasteradding_model->getFeeCollectionReport(
        $start_date, 
        $end_date, 
        null,  // feetype_id
        null,  // received_by
        null,  // group
        null,  // class_id
        null,  // section_id
        null   // session_id (will use current session)
    );
    
    $end_time = microtime(true);
    $execution_time = ($end_time - $start_time) * 1000;
    
    echo "Execution Time: " . number_format($execution_time, 2) . " ms\n";
    echo "Records Returned: " . count($results) . "\n\n";
    
    if (count($results) > 0) {
        echo "✅ getFeeCollectionReport() is returning data!\n\n";
        
        echo "Sample Result Record:\n";
        $sample = $results[0];
        echo "  - Payment ID: " . $sample['id'] . "/" . $sample['inv_no'] . "\n";
        echo "  - Student: " . $sample['firstname'] . " " . $sample['lastname'] . "\n";
        echo "  - Admission No: " . $sample['admission_no'] . "\n";
        echo "  - Class: " . $sample['class'] . " - " . $sample['section'] . "\n";
        echo "  - Fee Type: " . $sample['type'] . " (Code: " . $sample['code'] . ")\n";
        echo "  - Amount: " . $sample['amount'] . "\n";
        echo "  - Date: " . $sample['date'] . "\n";
        echo "  - Payment Mode: " . $sample['payment_mode'] . "\n";
        echo "  - Received By: " . $sample['received_by'] . "\n\n";
        
        // Show date distribution
        echo "Date Distribution of Results:\n";
        $date_counts = array();
        foreach ($results as $result) {
            $date = $result['date'];
            if (!isset($date_counts[$date])) {
                $date_counts[$date] = 0;
            }
            $date_counts[$date]++;
        }
        
        // Sort by date
        ksort($date_counts);
        
        // Show first 10 dates
        $count = 0;
        foreach ($date_counts as $date => $count_val) {
            if ($count < 10) {
                echo "  - $date: $count_val payments\n";
                $count++;
            }
        }
        
        if (count($date_counts) > 10) {
            echo "  ... and " . (count($date_counts) - 10) . " more dates\n";
        }
        echo "\n";
        
    } else {
        echo "❌ getFeeCollectionReport() returned NO data\n";
        echo "   This means the issue is in the model logic or filters.\n\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 4: Check if session filter is the issue
echo "Test 4: Check Session Filter Impact\n";
echo "------------------------------------\n";

// Get all sessions
$CI->load->model('session_model');
$sessions = $CI->session_model->get();

echo "Available Sessions:\n";
foreach ($sessions as $session) {
    echo "  - ID: " . $session['id'] . ", Name: " . $session['session'] . "\n";
}
echo "\n";

// Check which sessions have additional fees
echo "Additional Fees by Session:\n";
foreach ($sessions as $session) {
    $CI->db->select('COUNT(*) as count');
    $CI->db->from('student_fees_depositeadding');
    $CI->db->join('student_fees_masteradding', 'student_fees_masteradding.id = student_fees_depositeadding.student_fees_master_id');
    $CI->db->join('student_session', 'student_session.id = student_fees_masteradding.student_session_id');
    $CI->db->where('student_session.session_id', $session['id']);
    $query = $CI->db->get();
    $count = $query->row()->count;
    
    $marker = ($session['id'] == $current_session) ? " ← CURRENT SESSION" : "";
    echo "  - Session " . $session['session'] . " (ID: " . $session['id'] . "): $count records$marker\n";
}
echo "\n";

// Test 5: Test with specific session
echo "Test 5: Test with Each Session\n";
echo "--------------------------------\n";

foreach ($sessions as $session) {
    $results = $CI->studentfeemasteradding_model->getFeeCollectionReport(
        $start_date, 
        $end_date, 
        null, null, null, null, null,
        $session['id']  // Specific session
    );
    
    echo "Session " . $session['session'] . " (ID: " . $session['id'] . "): " . count($results) . " records\n";
}
echo "\n";

// Test 6: Check fee_groups_feetypeadding table
echo "Test 6: Check fee_groups_feetypeadding Table\n";
echo "---------------------------------------------\n";

$CI->db->select('COUNT(*) as count');
$CI->db->from('fee_groups_feetypeadding');
$CI->db->where('session_id', $current_session);
$query = $CI->db->get();
$count = $query->row()->count;

echo "fee_groups_feetypeadding records for current session: $count\n";

if ($count == 0) {
    echo "⚠️ WARNING: No fee_groups_feetypeadding records for current session!\n";
    echo "   This could be why the report shows no data.\n";
    echo "   The JOIN in getFeeCollectionReport() requires matching records.\n\n";
    
    // Check all sessions
    $CI->db->select('session_id, COUNT(*) as count');
    $CI->db->from('fee_groups_feetypeadding');
    $CI->db->group_by('session_id');
    $query = $CI->db->get();
    $results = $query->result();
    
    echo "fee_groups_feetypeadding records by session:\n";
    foreach ($results as $result) {
        echo "  - Session ID " . $result->session_id . ": " . $result->count . " records\n";
    }
    echo "\n";
} else {
    echo "✅ fee_groups_feetypeadding has records for current session\n\n";
}

// Test 7: Direct SQL query to bypass model
echo "Test 7: Direct SQL Query (Bypass Model)\n";
echo "----------------------------------------\n";

$sql = "
SELECT 
    sfd.*,
    s.firstname, s.lastname, s.admission_no,
    c.class, sec.section,
    ft.type, ft.code
FROM student_fees_depositeadding sfd
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN students s ON s.id = ss.student_id
JOIN classes c ON c.id = ss.class_id
JOIN sections sec ON sec.id = ss.section_id
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
WHERE ss.session_id = ?
LIMIT 5
";

$query = $CI->db->query($sql, array($current_session));
$results = $query->result();

echo "Direct SQL query returned: " . count($results) . " records\n";

if (count($results) > 0) {
    echo "✅ Direct query works! Data exists.\n\n";
    
    foreach ($results as $result) {
        echo "Record:\n";
        echo "  - Student: " . $result->firstname . " " . $result->lastname . "\n";
        echo "  - Fee Type: " . $result->type . "\n";
        echo "  - Amount Detail (first 100 chars): " . substr($result->amount_detail, 0, 100) . "...\n\n";
    }
} else {
    echo "❌ Direct query returned no data\n";
    echo "   This means there's no data for the current session.\n\n";
}

// Summary
echo "=== SUMMARY ===\n\n";

if ($total_other_fees > 0) {
    echo "✅ Additional fees data EXISTS in database ($total_other_fees records)\n";
} else {
    echo "❌ NO additional fees data in database\n";
}

echo "\n";
echo "📝 Recommendations:\n";
echo "1. Check if the current session has additional fees assigned\n";
echo "2. Verify fee_groups_feetypeadding has records for current session\n";
echo "3. Try selecting a different session in the report filter\n";
echo "4. Check if additional fees are properly assigned to students\n";
echo "\n";

echo "=== End of Debug Analysis ===\n";

