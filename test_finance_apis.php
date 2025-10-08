<?php
/**
 * Test script for Finance Report APIs
 * Tests: Due Fees Report, Daily Collection Report, Year Report Due Fees, Type Wise Balance Report
 */

// API base URL
$base_url = 'http://localhost/amt/api';

// Headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Function to make API request
function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['response' => json_decode($response, true), 'http_code' => $http_code];
}

echo "==============================================\n";
echo "FINANCE REPORT APIS TESTING\n";
echo "==============================================\n\n";

// Test 1: Due Fees Report API - Empty Request
echo "Test 1: Due Fees Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/due-fees-report/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
echo "Message: " . $result['response']['message'] . "\n";
echo "Total Records: " . $result['response']['total_records'] . "\n\n";

// Test 2: Due Fees Report API - List
echo "Test 2: Due Fees Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/due-fees-report/list', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
if (isset($result['response']['classes'])) {
    echo "Classes: " . count($result['response']['classes']) . "\n\n";
}

// Test 3: Due Fees Report API - Filter by Class
echo "Test 3: Due Fees Report API - Filter by Class (class_id=1)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/due-fees-report/filter', ['class_id' => '1'], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
echo "Total Records: " . $result['response']['total_records'] . "\n";
if (isset($result['response']['filters_applied']['class_id'])) {
    echo "Class ID Applied: " . $result['response']['filters_applied']['class_id'] . "\n\n";
}

// Test 4: Daily Collection Report API - Empty Request (Current Month)
echo "Test 4: Daily Collection Report API - Empty Request (Current Month)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/daily-collection-report/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
echo "Message: " . $result['response']['message'] . "\n";
echo "Total Records: " . $result['response']['total_records'] . "\n";
if (isset($result['response']['filters_applied'])) {
    echo "Date From: " . $result['response']['filters_applied']['date_from'] . "\n";
    echo "Date To: " . $result['response']['filters_applied']['date_to'] . "\n\n";
}

// Test 5: Daily Collection Report API - List
echo "Test 5: Daily Collection Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/daily-collection-report/list', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
if (isset($result['response']['date_ranges'])) {
    echo "Date Ranges: " . count($result['response']['date_ranges']) . "\n\n";
}

// Test 6: Daily Collection Report API - Custom Date Range
echo "Test 6: Daily Collection Report API - Custom Date Range\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/daily-collection-report/filter', [
    'date_from' => '2025-01-01',
    'date_to' => '2025-01-31'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
echo "Total Records: " . $result['response']['total_records'] . "\n";
if (isset($result['response']['filters_applied'])) {
    echo "Date From Applied: " . $result['response']['filters_applied']['date_from'] . "\n";
    echo "Date To Applied: " . $result['response']['filters_applied']['date_to'] . "\n\n";
}

// Test 7: Year Report Due Fees API - Empty Request
echo "Test 7: Year Report Due Fees API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
echo "Message: " . $result['response']['message'] . "\n";
echo "Total Records: " . $result['response']['total_records'] . "\n\n";

// Test 8: Year Report Due Fees API - List
echo "Test 8: Year Report Due Fees API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/list', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
if (isset($result['response']['classes'])) {
    echo "Classes: " . count($result['response']['classes']) . "\n\n";
}

// Test 9: Year Report Due Fees API - Filter by Class
echo "Test 9: Year Report Due Fees API - Filter by Class (class_id=1)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', ['class_id' => '1'], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
echo "Total Records: " . $result['response']['total_records'] . "\n";
if (isset($result['response']['filters_applied']['class_id'])) {
    echo "Class ID Applied: " . $result['response']['filters_applied']['class_id'] . "\n\n";
}

// Test 10: Type Wise Balance Report API - List
echo "Test 10: Type Wise Balance Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/type-wise-balance-report/list', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
if (isset($result['response']['sessions'])) {
    echo "Sessions: " . count($result['response']['sessions']) . "\n";
}
if (isset($result['response']['feegroups'])) {
    echo "Fee Groups: " . count($result['response']['feegroups']) . "\n";
}
if (isset($result['response']['feetypes'])) {
    echo "Fee Types: " . count($result['response']['feetypes']) . "\n\n";
}

// Test 11: Type Wise Balance Report API - Filter with Session and Fee Type
echo "Test 11: Type Wise Balance Report API - Filter (session_id=1, feetype_ids=[1])\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/type-wise-balance-report/filter', [
    'session_id' => '1',
    'feetype_ids' => ['1']
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
if ($result['response']['status'] == 1) {
    echo "Message: " . $result['response']['message'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    if (isset($result['response']['filters_applied'])) {
        echo "Session ID Applied: " . $result['response']['filters_applied']['session_id'] . "\n";
        echo "Fee Type IDs Applied: " . json_encode($result['response']['filters_applied']['feetype_ids']) . "\n\n";
    }
} else {
    echo "Message: " . $result['response']['message'] . "\n\n";
}

// Test 12: Type Wise Balance Report API - Filter with All Parameters
echo "Test 12: Type Wise Balance Report API - Filter with All Parameters\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/type-wise-balance-report/filter', [
    'session_id' => '1',
    'feetype_ids' => ['1'],
    'feegroup_ids' => ['1'],
    'class_id' => '1',
    'section_id' => '1'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . $result['response']['status'] . "\n";
if ($result['response']['status'] == 1) {
    echo "Message: " . $result['response']['message'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n\n";
} else {
    echo "Message: " . $result['response']['message'] . "\n\n";
}

echo "==============================================\n";
echo "TESTING COMPLETE\n";
echo "==============================================\n\n";
echo "All four Finance Report APIs have been tested.\n";
echo "Check the results above to verify functionality.\n\n";

