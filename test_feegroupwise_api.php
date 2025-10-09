<?php
/**
 * Test Script for Fee Group-wise Collection Report API
 * 
 * This script tests the API endpoints to verify:
 * 1. Correct calculation of amounts (no negative values)
 * 2. Detailed fee collection records are returned
 */

// API Configuration
$base_url = 'http://localhost/amt/api/';
$headers = [
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
];

// Test function
function testAPI($endpoint, $data, $test_name) {
    global $base_url, $headers;
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST: $test_name\n";
    echo str_repeat("=", 80) . "\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "HTTP Status Code: $http_code\n";
    echo "Request Data: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
    echo "\nResponse:\n";
    
    $result = json_decode($response, true);
    if ($result) {
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        
        // Validate response
        if ($http_code == 200 && $result['status'] == 1) {
            echo "\n✅ TEST PASSED: API returned success\n";
            
            // Check for negative values
            if (isset($result['grid_data'])) {
                $has_negative = false;
                foreach ($result['grid_data'] as $row) {
                    if (floatval($row['total_amount']) < 0 || 
                        floatval($row['amount_collected']) < 0 || 
                        floatval($row['balance_amount']) < 0) {
                        $has_negative = true;
                        echo "❌ ISSUE FOUND: Negative value in fee group: " . $row['fee_group_name'] . "\n";
                        echo "   Total: " . $row['total_amount'] . ", Collected: " . $row['amount_collected'] . ", Balance: " . $row['balance_amount'] . "\n";
                    }
                }
                if (!$has_negative) {
                    echo "✅ VALIDATION PASSED: No negative values found in grid_data\n";
                }
                echo "   Total Fee Groups: " . count($result['grid_data']) . "\n";
            }
            
            // Check for detailed data
            if (isset($result['detailed_data'])) {
                if (count($result['detailed_data']) > 0) {
                    echo "✅ VALIDATION PASSED: Detailed fee collection records are present\n";
                    echo "   Total Detailed Records: " . count($result['detailed_data']) . "\n";
                    
                    // Check for negative values in detailed data
                    $has_negative = false;
                    foreach ($result['detailed_data'] as $row) {
                        if (floatval($row['total_amount']) < 0 || 
                            floatval($row['amount_collected']) < 0 || 
                            floatval($row['balance_amount']) < 0) {
                            $has_negative = true;
                            echo "❌ ISSUE FOUND: Negative value for student: " . $row['student_name'] . "\n";
                            echo "   Total: " . $row['total_amount'] . ", Collected: " . $row['amount_collected'] . ", Balance: " . $row['balance_amount'] . "\n";
                        }
                    }
                    if (!$has_negative) {
                        echo "✅ VALIDATION PASSED: No negative values found in detailed_data\n";
                    }
                    
                    // Show sample records
                    echo "\n   Sample Detailed Records (first 3):\n";
                    for ($i = 0; $i < min(3, count($result['detailed_data'])); $i++) {
                        $record = $result['detailed_data'][$i];
                        echo "   - " . $record['student_name'] . " (" . $record['admission_no'] . ")\n";
                        echo "     Fee Group: " . $record['fee_group_name'] . "\n";
                        echo "     Total: " . $record['total_amount'] . ", Collected: " . $record['amount_collected'] . ", Balance: " . $record['balance_amount'] . "\n";
                        echo "     Status: " . $record['payment_status'] . "\n";
                    }
                } else {
                    echo "⚠️  WARNING: No detailed records found (might be expected if no data exists)\n";
                }
            } else {
                echo "❌ VALIDATION FAILED: Detailed fee collection records are missing\n";
            }
            
            // Check summary
            if (isset($result['summary'])) {
                echo "\n   Summary:\n";
                echo "   - Total Amount: " . $result['summary']['total_amount'] . "\n";
                echo "   - Total Collected: " . $result['summary']['total_collected'] . "\n";
                echo "   - Total Balance: " . $result['summary']['total_balance'] . "\n";
                echo "   - Collection %: " . $result['summary']['collection_percentage'] . "%\n";
                
                // Validate summary calculations
                $total_amt = floatval($result['summary']['total_amount']);
                $total_coll = floatval($result['summary']['total_collected']);
                $total_bal = floatval($result['summary']['total_balance']);
                
                if ($total_amt < 0 || $total_coll < 0 || $total_bal < 0) {
                    echo "❌ VALIDATION FAILED: Negative values in summary\n";
                } else {
                    echo "✅ VALIDATION PASSED: No negative values in summary\n";
                }
                
                // Check if balance = total - collected
                $expected_balance = $total_amt - $total_coll;
                if (abs($expected_balance - $total_bal) < 0.01) {
                    echo "✅ VALIDATION PASSED: Balance calculation is correct\n";
                } else {
                    echo "❌ VALIDATION FAILED: Balance calculation mismatch (Expected: $expected_balance, Got: $total_bal)\n";
                }
            }
            
        } else {
            echo "\n❌ TEST FAILED: API returned error\n";
        }
    } else {
        echo "❌ TEST FAILED: Invalid JSON response\n";
        echo "Raw Response: $response\n";
    }
    
    echo "\n";
}

// Run Tests
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         Fee Group-wise Collection Report API - Test Suite                 ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

// Test 1: Get all records (empty filter)
testAPI(
    'feegroupwise-collection-report/filter',
    [],
    'Get All Records (Empty Filter)'
);

// Test 2: Filter by session only
testAPI(
    'feegroupwise-collection-report/filter',
    ['session_id' => '25'],
    'Filter by Session Only'
);

// Test 3: Filter by date range
testAPI(
    'feegroupwise-collection-report/filter',
    [
        'from_date' => '2024-01-01',
        'to_date' => '2024-12-31'
    ],
    'Filter by Date Range'
);

// Test 4: Filter by class
testAPI(
    'feegroupwise-collection-report/filter',
    [
        'class_ids' => ['1', '2']
    ],
    'Filter by Classes'
);

// Test 5: Get filter options
testAPI(
    'feegroupwise-collection-report/list',
    [],
    'Get Filter Options (List Endpoint)'
);

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           Test Suite Completed                             ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Summary of Issues to Check:\n";
echo "1. ✅ Negative values should NOT appear in any amount fields\n";
echo "2. ✅ Detailed fee collection records should be present in the response\n";
echo "3. ✅ Balance should equal (Total Amount - Amount Collected)\n";
echo "4. ✅ All amounts should be formatted to 2 decimal places\n";
echo "\n";
?>

