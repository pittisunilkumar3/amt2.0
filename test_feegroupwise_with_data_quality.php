<?php
/**
 * Test script for Fee Group-wise Collection Report API
 * This version focuses on data quality issues
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
$data = json_encode([
    'session_id' => '',
    'class_ids' => [],
    'section_ids' => [],
    'feegroup_ids' => [],
    'from_date' => '',
    'to_date' => ''
]);

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
echo "Fee Group-wise Collection Report API - Data Quality Analysis\n";
echo "=================================================================\n\n";

echo "HTTP Status Code: $http_code\n";
echo "Response Status: " . ($result['status'] ?? 'unknown') . "\n";
echo "Message: " . ($result['message'] ?? 'No message') . "\n\n";

if ($http_code == 200 && isset($result['data'])) {
    $data = $result['data'];
    
    // Summary
    echo "=================================================================\n";
    echo "SUMMARY\n";
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
    echo "=================================================================\n";
    echo "GRID DATA ANALYSIS (Fee Group-wise Summary)\n";
    echo "=================================================================\n";
    
    if (isset($data['grid_data']) && is_array($data['grid_data'])) {
        $total_groups = count($data['grid_data']);
        $groups_with_issues = 0;
        $groups_with_negative_balance = 0;
        $overpayment_groups = 0;
        $no_fee_assigned_groups = 0;
        
        echo "Total Fee Groups: $total_groups\n\n";
        
        // Categorize issues
        foreach ($data['grid_data'] as $group) {
            if (isset($group['data_issue']) && $group['data_issue'] !== null) {
                $groups_with_issues++;
                
                if ($group['data_issue'] === 'OVERPAYMENT') {
                    $overpayment_groups++;
                } elseif ($group['data_issue'] === 'NO_FEE_ASSIGNED') {
                    $no_fee_assigned_groups++;
                }
            }
            
            if ($group['balance_amount'] < 0) {
                $groups_with_negative_balance++;
            }
        }
        
        echo "Data Quality Summary:\n";
        echo "  ✓ Groups with no issues: " . ($total_groups - $groups_with_issues) . "\n";
        echo "  ⚠ Groups with data issues: $groups_with_issues\n";
        echo "    - Overpayment issues: $overpayment_groups\n";
        echo "    - No fee assigned: $no_fee_assigned_groups\n";
        echo "  ⚠ Groups with negative balance: $groups_with_negative_balance\n\n";
        
        // Show groups with issues
        if ($groups_with_issues > 0) {
            echo "Fee Groups with Data Issues:\n";
            echo str_repeat("-", 120) . "\n";
            printf("%-40s %15s %15s %15s %20s\n", 
                "Fee Group Name", "Total Amount", "Collected", "Balance", "Issue Type");
            echo str_repeat("-", 120) . "\n";
            
            foreach ($data['grid_data'] as $group) {
                if (isset($group['data_issue']) && $group['data_issue'] !== null) {
                    printf("%-40s %15s %15s %15s %20s\n",
                        substr($group['fee_group_name'], 0, 40),
                        "₹ " . number_format($group['total_amount'], 2),
                        "₹ " . number_format($group['amount_collected'], 2),
                        "₹ " . number_format($group['balance_amount'], 2),
                        $group['data_issue']
                    );
                }
            }
            echo str_repeat("-", 120) . "\n\n";
        }
        
        // Show top 10 normal groups
        echo "Sample of Fee Groups WITHOUT Issues (Top 10):\n";
        echo str_repeat("-", 120) . "\n";
        printf("%-40s %15s %15s %15s %10s\n", 
            "Fee Group Name", "Total Amount", "Collected", "Balance", "Students");
        echo str_repeat("-", 120) . "\n";
        
        $count = 0;
        foreach ($data['grid_data'] as $group) {
            if (!isset($group['data_issue']) || $group['data_issue'] === null) {
                printf("%-40s %15s %15s %15s %10d\n",
                    substr($group['fee_group_name'], 0, 40),
                    "₹ " . number_format($group['total_amount'], 2),
                    "₹ " . number_format($group['amount_collected'], 2),
                    "₹ " . number_format($group['balance_amount'], 2),
                    $group['total_students']
                );
                $count++;
                if ($count >= 10) break;
            }
        }
        echo str_repeat("-", 120) . "\n\n";
    }
    
    // Detailed Data Analysis
    echo "=================================================================\n";
    echo "DETAILED DATA ANALYSIS (Student-level Records)\n";
    echo "=================================================================\n";
    
    if (isset($data['detailed_data']) && is_array($data['detailed_data'])) {
        $total_records = count($data['detailed_data']);
        $records_with_issues = 0;
        $records_with_negative_balance = 0;
        $overpaid_records = 0;
        $paid_records = 0;
        $partial_records = 0;
        $pending_records = 0;
        
        echo "Total Student Records: $total_records\n\n";
        
        // Categorize records
        foreach ($data['detailed_data'] as $record) {
            if (isset($record['data_issue']) && $record['data_issue'] !== null) {
                $records_with_issues++;
            }
            
            if ($record['balance_amount'] < 0) {
                $records_with_negative_balance++;
            }
            
            // Count by payment status
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
        
        echo "Data Quality Summary:\n";
        echo "  ✓ Records with no issues: " . ($total_records - $records_with_issues) . "\n";
        echo "  ⚠ Records with data issues: $records_with_issues\n";
        echo "  ⚠ Records with negative balance: $records_with_negative_balance\n\n";
        
        echo "Payment Status Distribution:\n";
        echo "  ✓ Paid: $paid_records\n";
        echo "  ⚠ Overpaid: $overpaid_records\n";
        echo "  ⚠ Partial: $partial_records\n";
        echo "  ⚠ Pending: $pending_records\n\n";
        
        // Show sample records with issues
        if ($records_with_issues > 0) {
            echo "Sample Student Records with Data Issues (First 10):\n";
            echo str_repeat("-", 150) . "\n";
            printf("%-15s %-25s %-20s %-30s %12s %12s %12s %15s\n", 
                "Admission No", "Student Name", "Class", "Fee Group", "Total", "Collected", "Balance", "Issue");
            echo str_repeat("-", 150) . "\n";
            
            $count = 0;
            foreach ($data['detailed_data'] as $record) {
                if (isset($record['data_issue']) && $record['data_issue'] !== null) {
                    printf("%-15s %-25s %-20s %-30s %12s %12s %12s %15s\n",
                        $record['admission_no'],
                        substr($record['student_name'], 0, 25),
                        substr($record['class_name'], 0, 20),
                        substr($record['fee_group_name'], 0, 30),
                        "₹" . number_format($record['total_amount'], 2),
                        "₹" . number_format($record['amount_collected'], 2),
                        "₹" . number_format($record['balance_amount'], 2),
                        $record['data_issue']
                    );
                    $count++;
                    if ($count >= 10) break;
                }
            }
            echo str_repeat("-", 150) . "\n\n";
        }
        
        // Show sample normal records
        echo "Sample Student Records WITHOUT Issues (First 10):\n";
        echo str_repeat("-", 150) . "\n";
        printf("%-15s %-25s %-20s %-30s %12s %12s %12s %10s\n", 
            "Admission No", "Student Name", "Class", "Fee Group", "Total", "Collected", "Balance", "Status");
        echo str_repeat("-", 150) . "\n";
        
        $count = 0;
        foreach ($data['detailed_data'] as $record) {
            if (!isset($record['data_issue']) || $record['data_issue'] === null) {
                printf("%-15s %-25s %-20s %-30s %12s %12s %12s %10s\n",
                    $record['admission_no'],
                    substr($record['student_name'], 0, 25),
                    substr($record['class_name'], 0, 20),
                    substr($record['fee_group_name'], 0, 30),
                    "₹" . number_format($record['total_amount'], 2),
                    "₹" . number_format($record['amount_collected'], 2),
                    "₹" . number_format($record['balance_amount'], 2),
                    $record['payment_status']
                );
                $count++;
                if ($count >= 10) break;
            }
        }
        echo str_repeat("-", 150) . "\n\n";
    }
    
    // Verification Results
    echo "=================================================================\n";
    echo "VERIFICATION RESULTS\n";
    echo "=================================================================\n";
    
    $all_checks_passed = true;
    
    // Check 1: Detailed data exists
    echo "✓ Check 1: Detailed data array exists and contains records\n";
    if (!isset($data['detailed_data']) || !is_array($data['detailed_data']) || count($data['detailed_data']) == 0) {
        echo "  ✗ FAILED: No detailed data found\n";
        $all_checks_passed = false;
    } else {
        echo "  ✓ PASSED: Found " . count($data['detailed_data']) . " student records\n";
    }
    
    // Check 2: Required fields present
    echo "\n✓ Check 2: Required fields present in detailed data\n";
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
            echo "  ✓ PASSED: All required fields present\n";
        } else {
            echo "  ✗ FAILED: Missing fields: " . implode(', ', $missing_fields) . "\n";
            $all_checks_passed = false;
        }
    }
    
    // Check 3: Data quality flags present
    echo "\n✓ Check 3: Data quality flags present\n";
    if (isset($data['detailed_data'][0]['data_issue'])) {
        echo "  ✓ PASSED: Data quality flags (data_issue, data_issue_description) are present\n";
    } else {
        echo "  ✗ FAILED: Data quality flags not found\n";
        $all_checks_passed = false;
    }
    
    // Check 4: Calculation accuracy
    echo "\n✓ Check 4: Balance calculation accuracy\n";
    $calculation_errors = 0;
    foreach ($data['detailed_data'] as $record) {
        $expected_balance = $record['total_amount'] - $record['amount_collected'];
        $actual_balance = $record['balance_amount'];
        
        // Allow for small floating point differences
        if (abs($expected_balance - $actual_balance) > 0.01) {
            $calculation_errors++;
        }
    }
    
    if ($calculation_errors == 0) {
        echo "  ✓ PASSED: All balance calculations are correct\n";
    } else {
        echo "  ✗ FAILED: Found $calculation_errors records with incorrect balance calculations\n";
        $all_checks_passed = false;
    }
    
    echo "\n=================================================================\n";
    if ($all_checks_passed) {
        echo "✓ ALL CHECKS PASSED - API is working correctly\n";
    } else {
        echo "✗ SOME CHECKS FAILED - Please review the issues above\n";
    }
    echo "=================================================================\n";
    
} else {
    echo "ERROR: Failed to retrieve data from API\n";
    echo "Response: " . print_r($result, true) . "\n";
}

