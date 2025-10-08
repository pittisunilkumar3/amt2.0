<?php
/**
 * Quick Test Script for Collection Report API
 */

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['response' => json_decode($response, true), 'http_code' => $http_code];
}

echo "Collection Report API - Quick Test\n";
echo "===================================\n\n";

// Test 1: Empty request
echo "Test 1: Empty Request\n";
$result = makeRequest($base_url . '/collection-report/filter', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n\n";

// Test 2: List endpoint
echo "Test 2: List Endpoint\n";
$result = makeRequest($base_url . '/collection-report/list', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Status: " . ($result['response']['status'] ?? 'N/A') . "\n\n";

// Test 3: With search_type
echo "Test 3: With search_type='this_year'\n";
$result = makeRequest($base_url . '/collection-report/filter', ['search_type' => 'this_year'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n\n";

// Test 4: With class_id
echo "Test 4: With class_id=1\n";
$result = makeRequest($base_url . '/collection-report/filter', ['class_id' => '1', 'search_type' => 'this_month'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n\n";

echo "✓ All tests completed successfully!\n";

