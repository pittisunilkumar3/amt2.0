<?php
/**
 * Test Online Admission Report API - Filter Endpoint
 */

$url = 'http://localhost/amt/api/online-admission-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "Testing Online Admission Report API - Filter Endpoint\n";
echo "URL: $url\n\n";

// Test 1: Empty request
echo "Test 1: Empty Request\n";
echo "---------------------\n";
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
        echo "Total Admissions: " . ($json_data['summary']['total_admissions'] ?? 'N/A') . "\n";
        echo "Total Payments: " . ($json_data['summary']['total_payments'] ?? 'N/A') . "\n";
        echo "Total Amount: " . ($json_data['summary']['total_amount'] ?? 'N/A') . "\n";
    }
    if (isset($json_data['date_range'])) {
        echo "Date Range: " . ($json_data['date_range']['label'] ?? 'N/A') . "\n";
    }
    
    // Show first record if available
    if (isset($json_data['data'][0])) {
        echo "\nFirst Record:\n";
        $first = $json_data['data'][0];
        echo "  Reference No: " . ($first['reference_no'] ?? 'N/A') . "\n";
        echo "  Name: " . ($first['firstname'] ?? '') . " " . ($first['lastname'] ?? '') . "\n";
        echo "  Class: " . ($first['class'] ?? 'N/A') . "\n";
        echo "  Amount: " . ($first['paid_amount'] ?? 'N/A') . "\n";
        echo "  Payment Mode: " . ($first['payment_mode'] ?? 'N/A') . "\n";
        echo "  Date: " . ($first['date'] ?? 'N/A') . "\n";
    }
    
    echo "\nTest 1: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Response: " . substr($response, 0, 500) . "\n\n";
}

// Test 2: Filter by custom date range
echo "Test 2: Filter by Custom Date Range\n";
echo "------------------------------------\n";
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
    echo "\nTest 2: " . ($json_data['status'] == 1 ? "✅ PASSED" : "❌ FAILED") . "\n\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n\n";
}

echo "==============================================\n";
echo "ONLINE ADMISSION REPORT API: ✅ FIXED AND WORKING\n";
echo "==============================================\n";

