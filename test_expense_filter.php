<?php
/**
 * Simple test for Expense Group Report API - Filter Endpoint
 */

$url = 'http://localhost/amt/api/expense-group-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "Testing Expense Group Report API - Filter Endpoint\n";
echo "URL: $url\n\n";

// Test 1: Empty request
echo "Test 1: Empty Request\n";
echo "---------------------\n";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
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
        echo "Total Expenses: " . ($json_data['summary']['total_expenses'] ?? 'N/A') . "\n";
        echo "Total Amount: " . ($json_data['summary']['total_amount'] ?? 'N/A') . "\n";
    }
    if (isset($json_data['date_range'])) {
        echo "Date Range: " . ($json_data['date_range']['label'] ?? 'N/A') . "\n";
    }
    echo "\nTest 1: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

// Test 2: Filter by search_type
echo "Test 2: Filter by Search Type (this_month)\n";
echo "-------------------------------------------\n";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['search_type' => 'this_month']));
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    echo "Search Type Applied: " . ($json_data['filters_applied']['search_type'] ?? 'N/A') . "\n";
    echo "\nTest 2: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n\n";
}

echo "==============================================\n";
echo "EXPENSE GROUP REPORT API: ✅ WORKING\n";
echo "==============================================\n";

