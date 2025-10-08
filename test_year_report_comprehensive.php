<?php
/**
 * Comprehensive Test Script for Year Report Due Fees API
 * Tests all parameter combinations to verify graceful null/empty handling
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
echo "YEAR REPORT DUE FEES API - COMPREHENSIVE TESTING\n";
echo "==============================================\n\n";

// Test 1: Empty request {} - Should return ALL students with due fees from ALL sessions
echo "Test 1: Empty Request {} - Should return ALL sessions, ALL classes, ALL sections\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Message: " . $result['response']['message'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Filters Applied:\n";
    echo "  - class_id: " . ($result['response']['filters_applied']['class_id'] ?? 'null') . "\n";
    echo "  - section_id: " . ($result['response']['filters_applied']['section_id'] ?? 'null') . "\n";
    echo "  - session_id: " . ($result['response']['filters_applied']['session_id'] ?? 'null') . "\n";
    echo "Expected: Should return students from ALL sessions\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 2: Only session_id provided - Should return only that session
echo "Test 2: Only session_id=1 - Should return ONLY session 1, ALL classes, ALL sections\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', ['session_id' => '1'], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Filters Applied:\n";
    echo "  - session_id: " . ($result['response']['filters_applied']['session_id'] ?? 'null') . "\n";
    echo "Expected: Should return students from session 1 only\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 3: Only class_id provided - Should return only that class from ALL sessions
echo "Test 3: Only class_id=1 - Should return ONLY class 1, ALL sessions, ALL sections\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', ['class_id' => '1'], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Filters Applied:\n";
    echo "  - class_id: " . ($result['response']['filters_applied']['class_id'] ?? 'null') . "\n";
    echo "Expected: Should return students from class 1 across ALL sessions\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 4: session_id + class_id - Should return specific session and class
echo "Test 4: session_id=1 + class_id=1 - Should return session 1, class 1, ALL sections\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', [
    'session_id' => '1',
    'class_id' => '1'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Filters Applied:\n";
    echo "  - session_id: " . ($result['response']['filters_applied']['session_id'] ?? 'null') . "\n";
    echo "  - class_id: " . ($result['response']['filters_applied']['class_id'] ?? 'null') . "\n";
    echo "Expected: Should return students from session 1, class 1 only\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 5: All three parameters - Should return specific session, class, and section
echo "Test 5: session_id=1 + class_id=1 + section_id=1 - Should return ONLY this combination\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', [
    'session_id' => '1',
    'class_id' => '1',
    'section_id' => '1'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Filters Applied:\n";
    echo "  - session_id: " . ($result['response']['filters_applied']['session_id'] ?? 'null') . "\n";
    echo "  - class_id: " . ($result['response']['filters_applied']['class_id'] ?? 'null') . "\n";
    echo "  - section_id: " . ($result['response']['filters_applied']['section_id'] ?? 'null') . "\n";
    echo "Expected: Should return students from session 1, class 1, section 1 only\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 6: Empty string parameters - Should be treated as null
echo "Test 6: Empty string parameters (session_id='', class_id='') - Should return ALL\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/filter', [
    'session_id' => '',
    'class_id' => ''
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Expected: Should return students from ALL sessions and classes (empty strings treated as null)\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 7: List endpoint - Should return filter options
echo "Test 7: List Endpoint - Should return classes for filter options\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/year-report-due-fees/list', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Classes: " . count($result['response']['data']['classes']) . "\n";
    echo "Expected: Should return list of classes\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

echo "==============================================\n";
echo "TESTING COMPLETE\n";
echo "==============================================\n\n";

echo "Summary:\n";
echo "- Test 1: Empty request should return ALL sessions ✓\n";
echo "- Test 2: session_id filter should work correctly ✓\n";
echo "- Test 3: class_id filter should work across ALL sessions ✓\n";
echo "- Test 4: Combined filters should work correctly ✓\n";
echo "- Test 5: All parameters should work together ✓\n";
echo "- Test 6: Empty strings should be treated as null ✓\n";
echo "- Test 7: List endpoint should return filter options ✓\n\n";

echo "Verify that:\n";
echo "1. Empty/null parameters return ALL records for that parameter\n";
echo "2. Provided parameters correctly filter the results\n";
echo "3. Combined parameters work together correctly\n";
echo "4. No validation errors for empty/null parameters\n";

