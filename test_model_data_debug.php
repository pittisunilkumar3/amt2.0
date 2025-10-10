<?php
/**
 * Debug script to test model methods directly
 * This will help identify if the issue is in the model or controller
 */

// Bootstrap CodeIgniter
define('BASEPATH', true);
$_SERVER['CI_ENV'] = 'development';

// Load CodeIgniter
require_once('api/index.php');

// Get CI instance
$CI =& get_instance();

// Load required models
$CI->load->model('setting_model');
$CI->load->model('studentfeemaster_model');
$CI->load->helper('custom');

echo "=== Model Data Debug Test ===\n\n";

// Test getCurrentSessionStudentFeess()
echo "=== Testing getCurrentSessionStudentFeess() ===\n";
$st_fees = $CI->studentfeemaster_model->getCurrentSessionStudentFeess();

if (empty($st_fees)) {
    echo "⚠ WARNING: No regular fees data returned from model!\n";
    echo "This means the database query returned no results.\n\n";
} else {
    echo "✓ Regular fees data found: " . count($st_fees) . " records\n\n";
    
    // Show first 5 records
    echo "First 5 records:\n";
    foreach (array_slice($st_fees, 0, 5) as $i => $fee) {
        echo "\nRecord " . ($i + 1) . ":\n";
        echo "  student_fees_deposite_id: " . ($fee->student_fees_deposite_id ?? 'NULL') . "\n";
        echo "  amount_detail: " . (isset($fee->amount_detail) ? substr($fee->amount_detail, 0, 100) . '...' : 'NULL') . "\n";
        echo "  student name: " . ($fee->firstname ?? '') . " " . ($fee->lastname ?? '') . "\n";
        echo "  class: " . ($fee->class ?? 'N/A') . "\n";
        
        // Check if amount_detail is valid JSON
        if (isset($fee->amount_detail) && isJSON($fee->amount_detail)) {
            $details = json_decode($fee->amount_detail);
            if (!empty($details)) {
                echo "  Payment details count: " . count($details) . "\n";
                if (isset($details[0])) {
                    echo "  First payment date: " . ($details[0]->date ?? 'N/A') . "\n";
                    echo "  First payment amount: " . ($details[0]->amount ?? 0) . "\n";
                    echo "  First payment fine: " . ($details[0]->amount_fine ?? 0) . "\n";
                }
            } else {
                echo "  ⚠ amount_detail is empty array\n";
            }
        } else {
            echo "  ⚠ amount_detail is not valid JSON or is NULL\n";
        }
    }
    
    // Count records with valid amount_detail
    $valid_count = 0;
    $null_count = 0;
    $empty_json_count = 0;
    $invalid_json_count = 0;
    
    foreach ($st_fees as $fee) {
        if (!isset($fee->amount_detail) || $fee->amount_detail === null) {
            $null_count++;
        } elseif (!isJSON($fee->amount_detail)) {
            $invalid_json_count++;
        } else {
            $details = json_decode($fee->amount_detail);
            if (empty($details)) {
                $empty_json_count++;
            } else {
                $valid_count++;
            }
        }
    }
    
    echo "\n\nData Quality Summary:\n";
    echo "  Total records: " . count($st_fees) . "\n";
    echo "  Valid amount_detail: $valid_count\n";
    echo "  NULL amount_detail: $null_count\n";
    echo "  Empty JSON amount_detail: $empty_json_count\n";
    echo "  Invalid JSON amount_detail: $invalid_json_count\n";
}

echo "\n\n=== Testing getOtherfeesCurrentSessionStudentFeess() ===\n";
$st_other_fees = $CI->studentfeemaster_model->getOtherfeesCurrentSessionStudentFeess();

if (empty($st_other_fees)) {
    echo "⚠ WARNING: No other fees data returned from model!\n";
    echo "This means the database query returned no results.\n\n";
} else {
    echo "✓ Other fees data found: " . count($st_other_fees) . " records\n\n";
    
    // Show first 5 records
    echo "First 5 records:\n";
    foreach (array_slice($st_other_fees, 0, 5) as $i => $fee) {
        echo "\nRecord " . ($i + 1) . ":\n";
        echo "  student_fees_deposite_id: " . ($fee->student_fees_deposite_id ?? 'NULL') . "\n";
        echo "  amount_detail: " . (isset($fee->amount_detail) ? substr($fee->amount_detail, 0, 100) . '...' : 'NULL') . "\n";
        echo "  student name: " . ($fee->firstname ?? '') . " " . ($fee->lastname ?? '') . "\n";
        echo "  class: " . ($fee->class ?? 'N/A') . "\n";
        
        // Check if amount_detail is valid JSON
        if (isset($fee->amount_detail) && isJSON($fee->amount_detail)) {
            $details = json_decode($fee->amount_detail);
            if (!empty($details)) {
                echo "  Payment details count: " . count($details) . "\n";
                if (isset($details[0])) {
                    echo "  First payment date: " . ($details[0]->date ?? 'N/A') . "\n";
                    echo "  First payment amount: " . ($details[0]->amount ?? 0) . "\n";
                    echo "  First payment fine: " . ($details[0]->amount_fine ?? 0) . "\n";
                }
            } else {
                echo "  ⚠ amount_detail is empty array\n";
            }
        } else {
            echo "  ⚠ amount_detail is not valid JSON or is NULL\n";
        }
    }
    
    // Count records with valid amount_detail
    $valid_count = 0;
    $null_count = 0;
    $empty_json_count = 0;
    $invalid_json_count = 0;
    
    foreach ($st_other_fees as $fee) {
        if (!isset($fee->amount_detail) || $fee->amount_detail === null) {
            $null_count++;
        } elseif (!isJSON($fee->amount_detail)) {
            $invalid_json_count++;
        } else {
            $details = json_decode($fee->amount_detail);
            if (empty($details)) {
                $empty_json_count++;
            } else {
                $valid_count++;
            }
        }
    }
    
    echo "\n\nData Quality Summary:\n";
    echo "  Total records: " . count($st_other_fees) . "\n";
    echo "  Valid amount_detail: $valid_count\n";
    echo "  NULL amount_detail: $null_count\n";
    echo "  Empty JSON amount_detail: $empty_json_count\n";
    echo "  Invalid JSON amount_detail: $invalid_json_count\n";
}

echo "\n\n=== Testing Date Filtering Logic ===\n";
$date_from = '2025-10-01';
$date_to = '2025-10-31';
$formated_date_from = strtotime($date_from);
$formated_date_to = strtotime($date_to);

echo "Date range: $date_from to $date_to\n";
echo "Timestamp range: $formated_date_from to $formated_date_to\n\n";

if (!empty($st_fees)) {
    $matching_records = 0;
    $sample_dates = [];
    
    foreach ($st_fees as $fee) {
        if (isset($fee->amount_detail) && isJSON($fee->amount_detail)) {
            $details = json_decode($fee->amount_detail);
            if (!empty($details)) {
                foreach ($details as $detail) {
                    if (isset($detail->date)) {
                        $date_timestamp = strtotime($detail->date);
                        if ($date_timestamp >= $formated_date_from && $date_timestamp <= $formated_date_to) {
                            $matching_records++;
                            if (count($sample_dates) < 10) {
                                $sample_dates[] = $detail->date . " (amt: " . ($detail->amount ?? 0) . ", fine: " . ($detail->amount_fine ?? 0) . ")";
                            }
                        }
                    }
                }
            }
        }
    }
    
    echo "Regular fees matching date range: $matching_records payment records\n";
    if (!empty($sample_dates)) {
        echo "Sample matching dates:\n";
        foreach ($sample_dates as $sample) {
            echo "  - $sample\n";
        }
    } else {
        echo "⚠ No regular fees found in the specified date range!\n";
    }
}

echo "\n=== End of Model Debug Test ===\n";

