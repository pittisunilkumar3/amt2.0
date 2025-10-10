<?php
/**
 * Test script for Session Fee Structure API
 * 
 * This script tests the Session Fee Structure API endpoints to verify:
 * 1. Authentication works correctly
 * 2. Filter endpoint returns data with various filters
 * 3. List endpoint returns filter options
 * 4. Response structure is correct
 */

// API base URL
$base_url = 'http://localhost/amt/api';

// Authentication headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

/**
 * Make API request
 */
function makeRequest($url, $data = [], $headers = []) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

/**
 * Print test result
 */
function printResult($test_name, $result, $expected_status = 200) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST: {$test_name}\n";
    echo str_repeat("=", 80) . "\n";
    
    echo "HTTP Status: {$result['http_code']} ";
    if ($result['http_code'] == $expected_status) {
        echo "✓ PASS\n";
    } else {
        echo "✗ FAIL (Expected: {$expected_status})\n";
    }
    
    echo "\nResponse:\n";
    echo json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         SESSION FEE STRUCTURE API - COMPREHENSIVE TEST SUITE              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

// Test 1: Authentication failure (missing headers)
echo "\n[1/7] Testing authentication failure (missing headers)...\n";
$result1 = makeRequest("{$base_url}/session-fee-structure/filter", [], [
    'Content-Type: application/json'
]);
printResult("Authentication Failure Test", $result1, 401);

// Test 2: Wrong HTTP method (should fail)
echo "\n[2/7] Testing wrong HTTP method...\n";
$ch = curl_init("{$base_url}/session-fee-structure/filter");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
printResult("Wrong HTTP Method Test", [
    'http_code' => $http_code,
    'response' => json_decode($response, true)
], 400);

// Test 3: List endpoint - Get filter options
echo "\n[3/7] Testing list endpoint (filter options)...\n";
$result3 = makeRequest("{$base_url}/session-fee-structure/list", [], $headers);
printResult("List Filter Options", $result3, 200);

if ($result3['http_code'] == 200 && isset($result3['response']['sessions'])) {
    echo "\n✓ Filter options retrieved successfully:\n";
    echo "  - Sessions: " . count($result3['response']['sessions']) . "\n";
    echo "  - Classes: " . count($result3['response']['classes']) . "\n";
    echo "  - Fee Groups: " . count($result3['response']['fee_groups']) . "\n";
    echo "  - Fee Types: " . count($result3['response']['fee_types']) . "\n";
}

// Test 4: Filter endpoint - Empty request (all data)
echo "\n[4/7] Testing filter endpoint with empty request (all data)...\n";
$result4 = makeRequest("{$base_url}/session-fee-structure/filter", [], $headers);
printResult("Empty Filter (All Data)", $result4, 200);

if ($result4['http_code'] == 200 && isset($result4['response']['data'])) {
    echo "\n✓ Data structure validation:\n";
    echo "  - Total sessions: " . $result4['response']['total_sessions'] . "\n";
    
    if (count($result4['response']['data']) > 0) {
        $first_session = $result4['response']['data'][0];
        echo "  - First session ID: " . $first_session['session_id'] . "\n";
        echo "  - First session name: " . $first_session['session_name'] . "\n";
        echo "  - Classes in first session: " . count($first_session['classes']) . "\n";
        echo "  - Fee groups in first session: " . count($first_session['fee_groups']) . "\n";
        
        if (count($first_session['classes']) > 0) {
            $first_class = $first_session['classes'][0];
            echo "  - Sections in first class: " . count($first_class['sections']) . "\n";
        }
        
        if (count($first_session['fee_groups']) > 0) {
            $first_fee_group = $first_session['fee_groups'][0];
            echo "  - Fee types in first fee group: " . count($first_fee_group['fee_types']) . "\n";
            
            if (count($first_fee_group['fee_types']) > 0) {
                $first_fee_type = $first_fee_group['fee_types'][0];
                echo "  - First fee type amount: " . $first_fee_type['amount'] . "\n";
            }
        }
    }
}

// Test 5: Filter by session (if we have session data)
if ($result3['http_code'] == 200 && isset($result3['response']['sessions']) && count($result3['response']['sessions']) > 0) {
    $first_session_id = $result3['response']['sessions'][0]['id'];
    
    echo "\n[5/7] Testing filter by session (session_id: {$first_session_id})...\n";
    $result5 = makeRequest("{$base_url}/session-fee-structure/filter", [
        'session_id' => $first_session_id
    ], $headers);
    printResult("Filter by Session", $result5, 200);
    
    if ($result5['http_code'] == 200) {
        echo "\n✓ Session filter applied:\n";
        echo "  - Filtered session ID: " . $result5['response']['filters_applied']['session_id'] . "\n";
        echo "  - Total sessions in response: " . $result5['response']['total_sessions'] . "\n";
    }
} else {
    echo "\n[5/7] Skipping session filter test (no sessions available)\n";
}

// Test 6: Filter by class (if we have class data)
if ($result3['http_code'] == 200 && isset($result3['response']['classes']) && count($result3['response']['classes']) > 0) {
    $first_class_id = $result3['response']['classes'][0]['id'];
    
    echo "\n[6/7] Testing filter by class (class_id: {$first_class_id})...\n";
    $result6 = makeRequest("{$base_url}/session-fee-structure/filter", [
        'class_id' => $first_class_id
    ], $headers);
    printResult("Filter by Class", $result6, 200);
    
    if ($result6['http_code'] == 200) {
        echo "\n✓ Class filter applied:\n";
        echo "  - Filtered class ID: " . $result6['response']['filters_applied']['class_id'] . "\n";
        echo "  - Total sessions in response: " . $result6['response']['total_sessions'] . "\n";
    }
} else {
    echo "\n[6/7] Skipping class filter test (no classes available)\n";
}

// Test 7: Filter by fee group (if we have fee group data)
if ($result3['http_code'] == 200 && isset($result3['response']['fee_groups']) && count($result3['response']['fee_groups']) > 0) {
    $first_fee_group_id = $result3['response']['fee_groups'][0]['id'];
    
    echo "\n[7/7] Testing filter by fee group (fee_group_id: {$first_fee_group_id})...\n";
    $result7 = makeRequest("{$base_url}/session-fee-structure/filter", [
        'fee_group_id' => $first_fee_group_id
    ], $headers);
    printResult("Filter by Fee Group", $result7, 200);
    
    if ($result7['http_code'] == 200) {
        echo "\n✓ Fee group filter applied:\n";
        echo "  - Filtered fee group ID: " . $result7['response']['filters_applied']['fee_group_id'] . "\n";
        echo "  - Total sessions in response: " . $result7['response']['total_sessions'] . "\n";
    }
} else {
    echo "\n[7/7] Skipping fee group filter test (no fee groups available)\n";
}

// Summary
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           TEST SUITE COMPLETED                             ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

echo "\nSUMMARY:\n";
echo "--------\n";
echo "✓ Authentication test completed\n";
echo "✓ HTTP method validation test completed\n";
echo "✓ List endpoint test completed\n";
echo "✓ Filter endpoint tests completed\n";
echo "✓ Response structure validation completed\n";

echo "\nNEXT STEPS:\n";
echo "-----------\n";
echo "1. Review the test results above\n";
echo "2. Verify the response structure matches your requirements\n";
echo "3. Check that nested data (sessions → classes → sections, fee_groups → fee_types) is correct\n";
echo "4. Test with your frontend application\n";
echo "5. Refer to api/documentation/SESSION_FEE_STRUCTURE_API_README.md for detailed documentation\n";

echo "\nAPI ENDPOINTS:\n";
echo "--------------\n";
echo "Filter: POST {$base_url}/session-fee-structure/filter\n";
echo "List:   POST {$base_url}/session-fee-structure/list\n";

echo "\n";

