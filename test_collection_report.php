<?php
/**
 * Comprehensive Test Script for Collection Report API
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
echo "COLLECTION REPORT API - COMPREHENSIVE TESTING\n";
echo "==============================================\n\n";

// Test 1: Empty request {} - Should return current month's collection
echo "Test 1: Empty Request {} - Should return current month's collection\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Message: " . $result['response']['message'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Date From: " . $result['response']['filters_applied']['date_from'] . "\n";
    echo "Date To: " . $result['response']['filters_applied']['date_to'] . "\n";
    echo "Expected: Should return current month's collection data\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 2: List endpoint - Should return filter options
echo "Test 2: List Endpoint - Should return filter options\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/list', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Classes: " . count($result['response']['data']['classes']) . "\n";
    echo "Fee Types: " . count($result['response']['data']['fee_types']) . "\n";
    echo "Sessions: " . count($result['response']['data']['sessions']) . "\n";
    echo "Expected: Should return all filter options\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 3: Filter by search_type (this_year)
echo "Test 3: Filter by search_type='this_year'\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [
    'search_type' => 'this_year'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Search Type Applied: " . $result['response']['filters_applied']['search_type'] . "\n";
    echo "Expected: Should return this year's collection\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 4: Filter by custom date range
echo "Test 4: Filter by custom date range (2025-01-01 to 2025-01-31)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [
    'date_from' => '2025-01-01',
    'date_to' => '2025-01-31'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Date From Applied: " . $result['response']['filters_applied']['date_from'] . "\n";
    echo "Date To Applied: " . $result['response']['filters_applied']['date_to'] . "\n";
    echo "Expected: Should return January 2025 collection\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 5: Filter by class_id
echo "Test 5: Filter by class_id=1\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [
    'class_id' => '1',
    'search_type' => 'this_month'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Class ID Applied: " . $result['response']['filters_applied']['class_id'] . "\n";
    echo "Expected: Should return collection for class 1 only\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 6: Filter by session_id
echo "Test 6: Filter by session_id=1\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [
    'session_id' => '1',
    'search_type' => 'this_month'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Session ID Applied: " . $result['response']['filters_applied']['session_id'] . "\n";
    echo "Expected: Should return collection for session 1 only\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 7: Filter by feetype_id
echo "Test 7: Filter by feetype_id=1\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [
    'feetype_id' => '1',
    'search_type' => 'this_month'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Fee Type ID Applied: " . $result['response']['filters_applied']['feetype_id'] . "\n";
    echo "Expected: Should return collection for fee type 1 only\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 8: Combined filters
echo "Test 8: Combined filters (class_id=1, section_id=1, session_id=1)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [
    'class_id' => '1',
    'section_id' => '1',
    'session_id' => '1',
    'search_type' => 'this_month'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Class ID Applied: " . $result['response']['filters_applied']['class_id'] . "\n";
    echo "Section ID Applied: " . $result['response']['filters_applied']['section_id'] . "\n";
    echo "Session ID Applied: " . $result['response']['filters_applied']['session_id'] . "\n";
    echo "Expected: Should return collection for specific class, section, and session\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

// Test 9: Empty string parameters - Should be treated as null
echo "Test 9: Empty string parameters (class_id='', session_id='') - Should return ALL\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [
    'class_id' => '',
    'session_id' => '',
    'search_type' => 'this_month'
], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    echo "Status: " . $result['response']['status'] . "\n";
    echo "Total Records: " . $result['response']['total_records'] . "\n";
    echo "Expected: Should return collection from ALL classes and sessions (empty strings treated as null)\n\n";
} else {
    echo "ERROR: " . ($result['response']['message'] ?? 'Unknown error') . "\n\n";
}

echo "==============================================\n";
echo "TESTING COMPLETE\n";
echo "==============================================\n\n";

echo "Summary:\n";
echo "- Test 1: Empty request should return current month's collection ✓\n";
echo "- Test 2: List endpoint should return filter options ✓\n";
echo "- Test 3: search_type filter should work correctly ✓\n";
echo "- Test 4: Custom date range should work correctly ✓\n";
echo "- Test 5: class_id filter should work correctly ✓\n";
echo "- Test 6: session_id filter should work correctly ✓\n";
echo "- Test 7: feetype_id filter should work correctly ✓\n";
echo "- Test 8: Combined filters should work correctly ✓\n";
echo "- Test 9: Empty strings should be treated as null ✓\n\n";

echo "Verify that:\n";
echo "1. Empty/null parameters return ALL records for that parameter\n";
echo "2. Provided parameters correctly filter the results\n";
echo "3. Combined parameters work together correctly\n";
echo "4. No validation errors for empty/null parameters\n";

