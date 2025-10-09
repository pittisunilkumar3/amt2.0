<?php
/**
 * Test script for Fee Group-wise Collection Report API - Summary Only
 */

// API endpoint
$url = 'http://localhost/amt/api/feegroupwise-collection-report/filter';

// Request headers
$headers = [
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
];

// Request body - empty to get all records
$data = json_encode([]);

// Initialize cURL
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$response_body = substr($response, $header_size);

curl_close($ch);

// Parse response
$result = json_decode($response_body, true);

echo "=================================================================\n";
echo "Fee Group-wise Collection Report API - Summary Report\n";
echo "=================================================================\n\n";

echo "HTTP Status Code: $http_code\n";
echo "Response Status: " . ($result['status'] ?? 'unknown') . "\n\n";

if ($http_code == 200 && isset($result['status']) && $result['status'] == 1) {
    // API returns data directly, not nested in 'data' key
    $data = $result;
    
    // Overall Summary
    echo "=================================================================\n";
    echo "OVERALL SUMMARY\n";
    echo "=================================================================\n";
    if (isset($data['summary'])) {
        echo "Total Fee Groups: " . $data['summary']['total_fee_groups'] . "\n";
        echo "Total Students: " . $data['summary']['total_students'] . "\n";
        echo "Total Amount: ₹ " . number_format($data['summary']['total_amount'], 2) . "\n";
        echo "Total Collected: ₹ " . number_format($data['summary']['total_collected'], 2) . "\n";
        echo "Total Balance: ₹ " . number_format($data['summary']['total_balance'], 2) . "\n";
    }
    echo "\n";
    
    // Grid Data Analysis
    if (isset($data['grid_data']) && is_array($data['grid_data'])) {
        $total_groups = count($data['grid_data']);
        $groups_with_issues = 0;
        $overpayment_groups = 0;
        $no_fee_assigned_groups = 0;
        
        foreach ($data['grid_data'] as $group) {
            if (isset($group['data_issue']) && $group['data_issue'] !== null) {
                $groups_with_issues++;
                
                if ($group['data_issue'] === 'OVERPAYMENT') {
                    $overpayment_groups++;
                } elseif ($group['data_issue'] === 'NO_FEE_ASSIGNED') {
                    $no_fee_assigned_groups++;
                }
            }
        }
        
        echo "=================================================================\n";
        echo "FEE GROUPS DATA QUALITY\n";
        echo "=================================================================\n";
        echo "Total Fee Groups: $total_groups\n";
        echo "  ✓ Groups with no issues: " . ($total_groups - $groups_with_issues) . "\n";
        echo "  ⚠ Groups with data issues: $groups_with_issues\n";
        echo "    - Overpayment issues: $overpayment_groups\n";
        echo "    - No fee assigned: $no_fee_assigned_groups\n\n";
    }
    
    // Detailed Data Analysis
    if (isset($data['detailed_data']) && is_array($data['detailed_data'])) {
        $total_records = count($data['detailed_data']);
        $records_with_issues = 0;
        $overpaid_records = 0;
        $paid_records = 0;
        $partial_records = 0;
        $pending_records = 0;
        
        foreach ($data['detailed_data'] as $record) {
            if (isset($record['data_issue']) && $record['data_issue'] !== null) {
                $records_with_issues++;
            }
            
            if (isset($record['payment_status'])) {
                switch ($record['payment_status']) {
                    case 'Paid':
                        $paid_records++;
                        break;
                    case 'Overpaid':
                        $overpaid_records++;
                        break;
                    case 'Partial':
                        $partial_records++;
                        break;
                    case 'Pending':
                        $pending_records++;
                        break;
                }
            }
        }
        
        echo "=================================================================\n";
        echo "STUDENT RECORDS DATA QUALITY\n";
        echo "=================================================================\n";
        echo "Total Student Records: $total_records\n";
        echo "  ✓ Records with no issues: " . ($total_records - $records_with_issues) . "\n";
        echo "  ⚠ Records with data issues: $records_with_issues\n\n";
        
        echo "Payment Status Distribution:\n";
        echo "  ✓ Paid: $paid_records (" . round(($paid_records/$total_records)*100, 1) . "%)\n";
        echo "  ⚠ Overpaid: $overpaid_records (" . round(($overpaid_records/$total_records)*100, 1) . "%)\n";
        echo "  ⚠ Partial: $partial_records (" . round(($partial_records/$total_records)*100, 1) . "%)\n";
        echo "  ⚠ Pending: $pending_records (" . round(($pending_records/$total_records)*100, 1) . "%)\n\n";
    }
    
    // Verification Results
    echo "=================================================================\n";
    echo "API VERIFICATION RESULTS\n";
    echo "=================================================================\n";
    
    $all_checks_passed = true;
    
    // Check 1: Detailed data exists
    if (!isset($data['detailed_data']) || !is_array($data['detailed_data']) || count($data['detailed_data']) == 0) {
        echo "✗ Check 1: FAILED - No detailed data found\n";
        $all_checks_passed = false;
    } else {
        echo "✓ Check 1: PASSED - Found " . count($data['detailed_data']) . " student records\n";
    }
    
    // Check 2: Required fields present
    $required_fields = ['student_id', 'admission_no', 'student_name', 'class_name', 
                       'section_name', 'fee_group_name', 'total_amount', 'amount_collected', 
                       'balance_amount', 'payment_status'];
    
    if (isset($data['detailed_data'][0])) {
        $missing_fields = [];
        foreach ($required_fields as $field) {
            if (!isset($data['detailed_data'][0][$field])) {
                $missing_fields[] = $field;
            }
        }
        
        if (empty($missing_fields)) {
            echo "✓ Check 2: PASSED - All required fields present\n";
        } else {
            echo "✗ Check 2: FAILED - Missing fields: " . implode(', ', $missing_fields) . "\n";
            $all_checks_passed = false;
        }
    }
    
    // Check 3: Data quality flags present
    if (isset($data['detailed_data'][0]['data_issue'])) {
        echo "✓ Check 3: PASSED - Data quality flags present\n";
    } else {
        echo "✗ Check 3: FAILED - Data quality flags not found\n";
        $all_checks_passed = false;
    }
    
    // Check 4: Calculation accuracy
    $calculation_errors = 0;
    foreach ($data['detailed_data'] as $record) {
        $expected_balance = $record['total_amount'] - $record['amount_collected'];
        $actual_balance = $record['balance_amount'];
        
        if (abs($expected_balance - $actual_balance) > 0.01) {
            $calculation_errors++;
        }
    }
    
    if ($calculation_errors == 0) {
        echo "✓ Check 4: PASSED - All balance calculations are correct\n";
    } else {
        echo "✗ Check 4: FAILED - Found $calculation_errors records with incorrect calculations\n";
        $all_checks_passed = false;
    }
    
    echo "\n=================================================================\n";
    if ($all_checks_passed) {
        echo "✓✓✓ ALL CHECKS PASSED - API IS WORKING CORRECTLY ✓✓✓\n";
    } else {
        echo "✗✗✗ SOME CHECKS FAILED - PLEASE REVIEW ISSUES ABOVE ✗✗✗\n";
    }
    echo "=================================================================\n\n";
    
    // Issue Explanation
    if ($records_with_issues > 0) {
        echo "=================================================================\n";
        echo "UNDERSTANDING THE DATA ISSUES\n";
        echo "=================================================================\n";
        echo "The negative balances you're seeing are due to DATA ISSUES, not\n";
        echo "calculation errors. Here's what's happening:\n\n";
        echo "OVERPAYMENT Issue:\n";
        echo "  - Student has NO fee assigned (total_amount = 0)\n";
        echo "  - But payment was collected (amount_collected > 0)\n";
        echo "  - Result: Balance = 0 - collected = NEGATIVE\n\n";
        echo "This indicates:\n";
        echo "  1. Fees were never assigned to these students, OR\n";
        echo "  2. Fees were waived/discounted to zero after payment, OR\n";
        echo "  3. Data migration issue - payments imported without fee amounts\n\n";
        echo "RECOMMENDATION:\n";
        echo "  - Review the fee groups with OVERPAYMENT issues\n";
        echo "  - Update student_fees_master records with correct fee amounts\n";
        echo "  - Or mark these as special cases in your system\n";
        echo "=================================================================\n";
    }
    
} else {
    echo "ERROR: Failed to retrieve data from API\n";
    if (isset($result['message'])) {
        echo "Message: " . $result['message'] . "\n";
    }
}

