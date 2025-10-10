<?php
/**
 * Comprehensive Test Suite for Type-wise Balance Report API
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║      TYPE-WISE BALANCE REPORT API - COMPREHENSIVE TEST SUITE              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$test_results = [
    'passed' => 0,
    'failed' => 0,
    'total' => 0
];

function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['http_code' => $http_code, 'data' => json_decode($response, true)];
}

function testCase($name, $condition, &$results) {
    $results['total']++;
    if ($condition) {
        echo "✓ {$name}\n";
        $results['passed']++;
        return true;
    } else {
        echo "✗ {$name}\n";
        $results['failed']++;
        return false;
    }
}

// Test 1: List Endpoint
echo "[TEST 1] List Endpoint - Get Filter Options\n";
echo str_repeat("-", 80) . "\n";

$result = makeRequest("{$base_url}/type-wise-balance-report/list", [], $headers);

testCase("HTTP 200 response", $result['http_code'] == 200, $test_results);
testCase("Status is 1", $result['data']['status'] == 1, $test_results);
testCase("Has sessions array", isset($result['data']['sessions']) && is_array($result['data']['sessions']), $test_results);
testCase("Has feetypes array", isset($result['data']['feetypes']) && is_array($result['data']['feetypes']), $test_results);
testCase("Has feegroups array", isset($result['data']['feegroups']) && is_array($result['data']['feegroups']), $test_results);
testCase("Has classes array", isset($result['data']['classes']) && is_array($result['data']['classes']), $test_results);

$sessions = $result['data']['sessions'];
$feetypes = $result['data']['feetypes'];
$classes = $result['data']['classes'];

echo "\nFilter Options Available:\n";
echo "  Sessions: " . count($sessions) . "\n";
echo "  Fee Types: " . count($feetypes) . "\n";
echo "  Fee Groups: " . count($result['data']['feegroups']) . "\n";
echo "  Classes: " . count($classes) . "\n";

// Find active session
$active_session = null;
foreach ($sessions as $session) {
    if ($session['is_active'] === 'yes') {
        $active_session = $session;
        break;
    }
}

// Find TUITION FEE
$tuition_fee = null;
foreach ($feetypes as $feetype) {
    if (stripos($feetype['type'], 'TUITION') !== false) {
        $tuition_fee = $feetype;
        break;
    }
}

echo "\nTest Data:\n";
echo "  Session: {$active_session['session']} (ID: {$active_session['id']})\n";
echo "  Fee Type: {$tuition_fee['type']} (ID: {$tuition_fee['id']})\n";

echo "\n";

// Test 2: Missing Required Parameter
echo "[TEST 2] Validation - Missing session_id\n";
echo str_repeat("-", 80) . "\n";

$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [], $headers);

testCase("HTTP 400 response", $result['http_code'] == 400, $test_results);
testCase("Status is 0", $result['data']['status'] == 0, $test_results);
testCase("Error message present", isset($result['data']['message']), $test_results);

echo "\n";

// Test 3: Filter with Empty Fee Types
echo "[TEST 3] Filter - Empty feetype_ids (All Fee Types)\n";
echo str_repeat("-", 80) . "\n";

$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => []
], $headers);

testCase("HTTP 200 response", $result['http_code'] == 200, $test_results);
testCase("Status is 1", $result['data']['status'] == 1, $test_results);
testCase("Has data array", isset($result['data']['data']) && is_array($result['data']['data']), $test_results);
testCase("Has total_records", isset($result['data']['total_records']), $test_results);
testCase("Data is not empty", $result['data']['total_records'] > 0, $test_results);

echo "\nTotal Records: {$result['data']['total_records']}\n";

echo "\n";

// Test 4: Filter with Specific Fee Type
echo "[TEST 4] Filter - Specific Fee Type (TUITION FEE)\n";
echo str_repeat("-", 80) . "\n";

$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => [$tuition_fee['id']]
], $headers);

testCase("HTTP 200 response", $result['http_code'] == 200, $test_results);
testCase("Status is 1", $result['data']['status'] == 1, $test_results);
testCase("Has data array", isset($result['data']['data']) && is_array($result['data']['data']), $test_results);
testCase("Data is not empty", $result['data']['total_records'] > 0, $test_results);

if ($result['data']['total_records'] > 0) {
    $first_record = $result['data']['data'][0];
    testCase("Fee type matches filter", $first_record['type'] == $tuition_fee['type'], $test_results);
    testCase("Has required fields", 
        isset($first_record['admission_no']) && 
        isset($first_record['firstname']) && 
        isset($first_record['total']) && 
        isset($first_record['balance']), 
        $test_results
    );
}

echo "\nTotal Records: {$result['data']['total_records']}\n";

// Calculate statistics
$total_balance = 0;
$total_paid = 0;
$total_due = 0;

foreach ($result['data']['data'] as $record) {
    $total = floatval($record['total']);
    $paid = floatval($record['total_amount']);
    $fine = floatval($record['total_fine']);
    $discount = floatval($record['total_discount']);
    
    $balance = $total - $paid + $fine - $discount;
    
    $total_due += $total;
    $total_paid += $paid;
    $total_balance += $balance;
}

echo "\nStatistics:\n";
echo "  Total Due: ₹" . number_format($total_due, 2) . "\n";
echo "  Total Paid: ₹" . number_format($total_paid, 2) . "\n";
echo "  Total Balance: ₹" . number_format($total_balance, 2) . "\n";

echo "\n";

// Test 5: Filter with Class
echo "[TEST 5] Filter - With Class Filter\n";
echo str_repeat("-", 80) . "\n";

$first_class = $classes[0];

$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => [$tuition_fee['id']],
    'class_id' => $first_class['id']
], $headers);

testCase("HTTP 200 response", $result['http_code'] == 200, $test_results);
testCase("Status is 1", $result['data']['status'] == 1, $test_results);
testCase("Filters applied correctly", 
    $result['data']['filters_applied']['class_id'] == $first_class['id'], 
    $test_results
);

echo "\nClass: {$first_class['class']} (ID: {$first_class['id']})\n";
echo "Total Records: {$result['data']['total_records']}\n";

if ($result['data']['total_records'] > 0) {
    $all_match = true;
    foreach ($result['data']['data'] as $record) {
        if ($record['class'] !== $first_class['class']) {
            $all_match = false;
            break;
        }
    }
    testCase("All records match class filter", $all_match, $test_results);
}

echo "\n";

// Test 6: Data Structure Validation
echo "[TEST 6] Data Structure Validation\n";
echo str_repeat("-", 80) . "\n";

$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => [$tuition_fee['id']]
], $headers);

if ($result['data']['total_records'] > 0) {
    $record = $result['data']['data'][0];
    
    $required_fields = [
        'feegroupname', 'stfeemasid', 'total', 'fgtid', 'fine', 'type',
        'section', 'class', 'admission_no', 'mobileno', 'firstname',
        'lastname', 'total_amount', 'total_fine', 'total_discount', 'balance'
    ];
    
    foreach ($required_fields as $field) {
        testCase("Has field: {$field}", isset($record[$field]), $test_results);
    }
    
    // Data type checks
    testCase("total is string", is_string($record['total']), $test_results);
    testCase("total_amount is integer", is_int($record['total_amount']), $test_results);
    testCase("fine is string", is_string($record['fine']), $test_results);
    testCase("type is string", is_string($record['type']), $test_results);
}

echo "\n";

// Test 7: Response Time
echo "[TEST 7] Performance Test\n";
echo str_repeat("-", 80) . "\n";

$start_time = microtime(true);

$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => []
], $headers);

$end_time = microtime(true);
$response_time = ($end_time - $start_time) * 1000; // Convert to milliseconds

testCase("Response time < 5 seconds", $response_time < 5000, $test_results);

echo "\nResponse Time: " . number_format($response_time, 2) . " ms\n";
echo "Total Records: {$result['data']['total_records']}\n";

echo "\n";

// Final Summary
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           TEST SUMMARY                                     ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "Total Tests: {$test_results['total']}\n";
echo "Passed: {$test_results['passed']} ✓\n";
echo "Failed: {$test_results['failed']} ✗\n";

$pass_rate = ($test_results['passed'] / $test_results['total']) * 100;
echo "Pass Rate: " . number_format($pass_rate, 2) . "%\n";

echo "\n";

if ($test_results['failed'] == 0) {
    echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    ✓✓✓ ALL TESTS PASSED! ✓✓✓                             ║\n";
    echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
} else {
    echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
    echo "║                    ⚠ SOME TESTS FAILED ⚠                                  ║\n";
    echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
}

echo "\n";

echo "API ENDPOINTS:\n";
echo "--------------\n";
echo "Filter: POST {$base_url}/type-wise-balance-report/filter\n";
echo "List:   POST {$base_url}/type-wise-balance-report/list\n";
echo "\n";

echo "DOCUMENTATION:\n";
echo "--------------\n";
echo "api/documentation/TYPE_WISE_BALANCE_REPORT_API_README.md\n";
echo "\n";

echo "SUMMARY:\n";
echo "--------\n";
echo "TYPE_WISE_BALANCE_REPORT_API_SUMMARY.md\n";
echo "\n";

