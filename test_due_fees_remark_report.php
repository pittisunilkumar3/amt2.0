<?php
/**
 * Test Due Fees Remark Report API
 */

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "========================================\n";
echo "DUE FEES REMARK REPORT API - TEST SUITE\n";
echo "========================================\n\n";

// Test 1: List endpoint
echo "Test 1: List Endpoint\n";
echo "---------------------\n";
$url = $base_url . '/due-fees-remark-report/list';
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
    if (isset($json_data['data']['classes'])) {
        echo "Classes Count: " . count($json_data['data']['classes']) . "\n";
    }
    echo "Test 1: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

// Test 2: Filter endpoint - Empty request
echo "Test 2: Filter Endpoint - Empty Request\n";
echo "----------------------------------------\n";
$url = $base_url . '/due-fees-remark-report/filter';
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
    echo "Test 2: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

// Test 3: Filter endpoint - With class and section
echo "Test 3: Filter Endpoint - With Class and Section\n";
echo "-------------------------------------------------\n";
$url = $base_url . '/due-fees-remark-report/filter';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['class_id' => '1', 'section_id' => '1']));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    if (isset($json_data['summary'])) {
        echo "Total Students: " . ($json_data['summary']['total_students'] ?? 'N/A') . "\n";
        echo "Total Balance: " . ($json_data['summary']['total_balance'] ?? 'N/A') . "\n";
    }
    echo "Test 3: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n\n";
}

echo "========================================\n";
echo "DUE FEES REMARK REPORT API: TEST COMPLETE\n";
echo "========================================\n";

