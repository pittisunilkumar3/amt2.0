<?php
/**
 * Test script for Due Fees Remark Report API - Graceful Null Handling
 * Tests all scenarios with null/empty parameters and session_id support
 */

// Color codes for terminal output
define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_RESET', "\033[0m");

// API configuration
$base_url = 'http://localhost/amt/api';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

/**
 * Make API request
 */
function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return array(
        'http_code' => $http_code,
        'response' => $response,
        'data' => json_decode($response, true)
    );
}

/**
 * Print test result
 */
function printResult($test_name, $result) {
    echo "\n=== " . COLOR_BLUE . $test_name . COLOR_RESET . " ===\n";
    echo "HTTP Code: " . $result['http_code'] . "\n";
    
    if ($result['http_code'] == 200 && isset($result['data']['status']) && $result['data']['status'] == 1) {
        echo COLOR_GREEN . "✓ PASSED" . COLOR_RESET . "\n";
        echo "Message: " . $result['data']['message'] . "\n";
        
        if (isset($result['data']['filters_applied'])) {
            echo "Filters Applied:\n";
            echo "  - class_id: " . ($result['data']['filters_applied']['class_id'] ?? 'null') . "\n";
            echo "  - section_id: " . ($result['data']['filters_applied']['section_id'] ?? 'null') . "\n";
            echo "  - session_id: " . ($result['data']['filters_applied']['session_id'] ?? 'null') . "\n";
        }
        
        if (isset($result['data']['total_records'])) {
            echo "Total Records: " . $result['data']['total_records'] . "\n";
        }
        
        if (isset($result['data']['summary'])) {
            echo "Summary: " . json_encode($result['data']['summary']) . "\n";
        }
    } else {
        echo COLOR_RED . "✗ FAILED" . COLOR_RESET . "\n";
        if (isset($result['data']['message'])) {
            echo "Message: " . $result['data']['message'] . "\n";
        } else {
            echo "Response: " . substr($result['response'], 0, 500) . "...\n";
        }
    }
}

// Print header
echo "\n" . str_repeat("=", 60) . "\n";
echo "  Testing Due Fees Remark Report API - Graceful Handling\n";
echo str_repeat("=", 60) . "\n";

// Test 1: Empty request - should return ALL due fees for current session
echo "\n" . COLOR_YELLOW . "Test 1: Empty Request (Get All Due Fees)" . COLOR_RESET . "\n";
$result1 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array(),
    $headers
);
printResult("Empty Request {}", $result1);

// Test 2: Only class_id provided - should return all sections in that class
echo "\n" . COLOR_YELLOW . "Test 2: Only class_id Provided" . COLOR_RESET . "\n";
$result2 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array('class_id' => '1'),
    $headers
);
printResult("Filter by Class Only", $result2);

// Test 3: Both class_id and section_id provided
echo "\n" . COLOR_YELLOW . "Test 3: Both class_id and section_id Provided" . COLOR_RESET . "\n";
$result3 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array(
        'class_id' => '1',
        'section_id' => '1'
    ),
    $headers
);
printResult("Filter by Class and Section", $result3);

// Test 4: Only session_id provided - should return all classes/sections for that session
echo "\n" . COLOR_YELLOW . "Test 4: Only session_id Provided" . COLOR_RESET . "\n";
$result4 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array('session_id' => '25'),
    $headers
);
printResult("Filter by Session Only", $result4);

// Test 5: class_id and session_id provided
echo "\n" . COLOR_YELLOW . "Test 5: class_id and session_id Provided" . COLOR_RESET . "\n";
$result5 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array(
        'class_id' => '1',
        'session_id' => '25'
    ),
    $headers
);
printResult("Filter by Class and Session", $result5);

// Test 6: All parameters provided
echo "\n" . COLOR_YELLOW . "Test 6: All Parameters Provided" . COLOR_RESET . "\n";
$result6 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array(
        'class_id' => '1',
        'section_id' => '1',
        'session_id' => '25'
    ),
    $headers
);
printResult("Filter by Class, Section, and Session", $result6);

// Test 7: Null values explicitly provided
echo "\n" . COLOR_YELLOW . "Test 7: Null Values Explicitly Provided" . COLOR_RESET . "\n";
$result7 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array(
        'class_id' => null,
        'section_id' => null
    ),
    $headers
);
printResult("Explicit Null Values", $result7);

// Test 8: Empty string values
echo "\n" . COLOR_YELLOW . "Test 8: Empty String Values" . COLOR_RESET . "\n";
$result8 = makeRequest(
    $base_url . '/due-fees-remark-report/filter',
    array(
        'class_id' => '',
        'section_id' => ''
    ),
    $headers
);
printResult("Empty String Values", $result8);

// Print summary
echo "\n" . str_repeat("=", 60) . "\n";
echo "  Test Summary\n";
echo str_repeat("=", 60) . "\n";

$tests = array(
    'Test 1: Empty Request' => $result1,
    'Test 2: Only class_id' => $result2,
    'Test 3: class_id + section_id' => $result3,
    'Test 4: Only session_id' => $result4,
    'Test 5: class_id + session_id' => $result5,
    'Test 6: All parameters' => $result6,
    'Test 7: Null values' => $result7,
    'Test 8: Empty strings' => $result8
);

$passed = 0;
$failed = 0;

foreach ($tests as $name => $result) {
    if ($result['http_code'] == 200 && isset($result['data']['status']) && $result['data']['status'] == 1) {
        $passed++;
        echo COLOR_GREEN . "✓ " . COLOR_RESET . $name . "\n";
    } else {
        $failed++;
        echo COLOR_RED . "✗ " . COLOR_RESET . $name . "\n";
    }
}

echo "\n" . str_repeat("-", 60) . "\n";
echo "Total Tests: " . count($tests) . "\n";
echo COLOR_GREEN . "Passed: " . $passed . COLOR_RESET . "\n";
echo COLOR_RED . "Failed: " . $failed . COLOR_RESET . "\n";
echo str_repeat("=", 60) . "\n\n";

// Expected behavior summary
echo COLOR_BLUE . "Expected Behavior:" . COLOR_RESET . "\n";
echo "1. Empty request {} should return ALL due fees for current session\n";
echo "2. Only class_id should return all sections in that class\n";
echo "3. class_id + section_id should return specific class/section\n";
echo "4. session_id should filter by that session\n";
echo "5. All combinations should work together\n";
echo "6. Null and empty string values should be treated the same\n";
echo "\n";

?>

