<?php
/**
 * Test Income Report API
 */

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "========================================\n";
echo "INCOME REPORT API - TEST SUITE\n";
echo "========================================\n\n";

// Test 1: List endpoint
echo "Test 1: List Endpoint\n";
echo "---------------------\n";
$url = $base_url . '/income-report/list';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Message: " . ($json_data['message'] ?? 'N/A') . "\n";
    if (isset($json_data['data']['search_types'])) {
        echo "Search Types Count: " . count($json_data['data']['search_types']) . "\n";
    }
    echo "Test 1: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

// Test 2: Filter endpoint - Empty request
echo "Test 2: Filter Endpoint - Empty Request\n";
echo "----------------------------------------\n";
$url = $base_url . '/income-report/filter';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Message: " . ($json_data['message'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    if (isset($json_data['summary'])) {
        echo "Total Amount: " . ($json_data['summary']['total_amount'] ?? 'N/A') . "\n";
    }
    if (isset($json_data['date_range'])) {
        echo "Date Range: " . ($json_data['date_range']['label'] ?? 'N/A') . "\n";
    }
    echo "Test 2: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

// Test 3: Filter endpoint - Search type
echo "Test 3: Filter Endpoint - Search Type (this_month)\n";
echo "---------------------------------------------------\n";
$url = $base_url . '/income-report/filter';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['search_type' => 'this_month']));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    echo "Search Type: " . ($json_data['filters_applied']['search_type'] ?? 'N/A') . "\n";
    echo "Test 3: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n\n";
}

// Test 4: Filter endpoint - Custom date range
echo "Test 4: Filter Endpoint - Custom Date Range\n";
echo "--------------------------------------------\n";
$url = $base_url . '/income-report/filter';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['date_from' => '2025-01-01', 'date_to' => '2025-12-31']));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    echo "Date From: " . ($json_data['filters_applied']['date_from'] ?? 'N/A') . "\n";
    echo "Date To: " . ($json_data['filters_applied']['date_to'] ?? 'N/A') . "\n";
    echo "Test 4: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n\n";
}

echo "========================================\n";
echo "INCOME REPORT API: TEST COMPLETE\n";
echo "========================================\n";

