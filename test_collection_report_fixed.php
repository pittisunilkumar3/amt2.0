<?php
/**
 * Test script for Collection Report API - Fixed Version
 * Tests all parameter variations and edge cases
 */

// Configuration
$base_url = 'http://localhost/amt/api/collection-report';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test counter
$test_number = 0;
$passed = 0;
$failed = 0;

/**
 * Make API request
 */
function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

/**
 * Run a test
 */
function runTest($test_name, $url, $data, $headers, $expected_status = 200) {
    global $test_number, $passed, $failed;
    $test_number++;
    
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST #{$test_number}: {$test_name}\n";
    echo str_repeat("=", 80) . "\n";
    
    echo "Request Data:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
    
    $result = makeRequest($url, $data, $headers);
    
    echo "HTTP Status: {$result['http_code']}\n";
    echo "Response:\n";
    echo json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    
    // Check if test passed
    $test_passed = ($result['http_code'] == $expected_status && 
                    isset($result['response']['status']) && 
                    $result['response']['status'] == 1);
    
    if ($test_passed) {
        echo "\n✓ TEST PASSED\n";
        $passed++;
    } else {
        echo "\n✗ TEST FAILED\n";
        $failed++;
    }
    
    return $result;
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         COLLECTION REPORT API - COMPREHENSIVE TEST SUITE                  ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

// Test 1: Empty request (should return current month)
runTest(
    "Empty Request - Current Month Default",
    $base_url . '/filter',
    [],
    $headers
);

// Test 2: List endpoint
runTest(
    "List Endpoint - Get Filter Options",
    $base_url . '/list',
    [],
    $headers
);

// Test 3: Using standard parameter names
runTest(
    "Standard Parameter Names",
    $base_url . '/filter',
    [
        'session_id' => '21',
        'class_id' => '19',
        'section_id' => '36',
        'feetype_id' => '33',
        'received_by' => '6',
        'date_from' => '2025-09-01',
        'date_to' => '2025-10-11'
    ],
    $headers
);

// Test 4: Using alternative parameter names (user's original request)
runTest(
    "Alternative Parameter Names (User's Request)",
    $base_url . '/filter',
    [
        'session_id' => '21',
        'class_id' => '19',
        'section_id' => '36',
        'fee_type_id' => '33',
        'collect_by_id' => '6',
        'search_type' => 'all',
        'from_date' => '2025-09-01',
        'to_date' => '2025-10-11'
    ],
    $headers
);

// Test 5: Using search_type with predefined range
runTest(
    "Search Type - This Month",
    $base_url . '/filter',
    [
        'search_type' => 'this_month',
        'class_id' => '19'
    ],
    $headers
);

// Test 6: Using search_type - This Year
runTest(
    "Search Type - This Year",
    $base_url . '/filter',
    [
        'search_type' => 'this_year'
    ],
    $headers
);

// Test 7: Using search_type - Last Week
runTest(
    "Search Type - Last Week",
    $base_url . '/filter',
    [
        'search_type' => 'last_week',
        'section_id' => '36'
    ],
    $headers
);

// Test 8: Using period search_type with custom dates
runTest(
    "Search Type - Period with Custom Dates",
    $base_url . '/filter',
    [
        'search_type' => 'period',
        'date_from' => '2025-09-01',
        'date_to' => '2025-10-11'
    ],
    $headers
);

// Test 9: Filter by fee type only
runTest(
    "Filter by Fee Type Only",
    $base_url . '/filter',
    [
        'feetype_id' => '33',
        'search_type' => 'this_month'
    ],
    $headers
);

// Test 10: Filter by collector only
runTest(
    "Filter by Collector Only",
    $base_url . '/filter',
    [
        'received_by' => '6',
        'search_type' => 'this_month'
    ],
    $headers
);

// Test 11: Filter by class and section
runTest(
    "Filter by Class and Section",
    $base_url . '/filter',
    [
        'class_id' => '19',
        'section_id' => '36',
        'search_type' => 'this_month'
    ],
    $headers
);

// Test 12: Filter by session only
runTest(
    "Filter by Session Only",
    $base_url . '/filter',
    [
        'session_id' => '21',
        'search_type' => 'this_year'
    ],
    $headers
);

// Test 13: Using sch_session_id (alternative name)
runTest(
    "Using sch_session_id (Alternative Name)",
    $base_url . '/filter',
    [
        'sch_session_id' => '21',
        'search_type' => 'this_month'
    ],
    $headers
);

// Test 14: All filters combined with standard names
runTest(
    "All Filters Combined - Standard Names",
    $base_url . '/filter',
    [
        'session_id' => '21',
        'class_id' => '19',
        'section_id' => '36',
        'feetype_id' => '33',
        'received_by' => '6',
        'search_type' => 'this_month',
        'group' => 'class'
    ],
    $headers
);

// Test 15: All filters combined with alternative names
runTest(
    "All Filters Combined - Alternative Names",
    $base_url . '/filter',
    [
        'sch_session_id' => '21',
        'class_id' => '19',
        'section_id' => '36',
        'fee_type_id' => '33',
        'collect_by_id' => '6',
        'from_date' => '2025-09-01',
        'to_date' => '2025-10-11',
        'group' => 'collection'
    ],
    $headers
);

// Test 16: Empty string parameters (should be treated as null)
runTest(
    "Empty String Parameters",
    $base_url . '/filter',
    [
        'session_id' => '',
        'class_id' => '',
        'feetype_id' => '',
        'search_type' => 'this_month'
    ],
    $headers
);

// Test 17: Transport fees
runTest(
    "Filter by Transport Fees",
    $base_url . '/filter',
    [
        'feetype_id' => 'transport_fees',
        'search_type' => 'this_month'
    ],
    $headers
);

// Test 18: Group by class
runTest(
    "Group by Class",
    $base_url . '/filter',
    [
        'search_type' => 'this_month',
        'group' => 'class'
    ],
    $headers
);

// Test 19: Group by collection
runTest(
    "Group by Collection",
    $base_url . '/filter',
    [
        'search_type' => 'this_month',
        'group' => 'collection'
    ],
    $headers
);

// Test 20: Group by mode
runTest(
    "Group by Payment Mode",
    $base_url . '/filter',
    [
        'search_type' => 'this_month',
        'group' => 'mode'
    ],
    $headers
);

// Summary
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           TEST SUMMARY                                     ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Total Tests: {$test_number}\n";
echo "Passed: {$passed} ✓\n";
echo "Failed: {$failed} ✗\n";
echo "\n";

if ($failed == 0) {
    echo "🎉 ALL TESTS PASSED! 🎉\n";
} else {
    echo "⚠️  SOME TESTS FAILED - Please review the output above\n";
}

echo "\n";

