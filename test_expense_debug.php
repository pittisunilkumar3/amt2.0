<?php
/**
 * Debug test for Expense Group Report API
 */

$url = 'http://localhost/amt/api/expense-group-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "Testing Expense Group Report API - Filter Endpoint (Debug)\n";
echo "URL: $url\n\n";

// Test with explicit date range to avoid customlib call
echo "Test: Explicit Date Range\n";
echo "-------------------------\n";
$data = [
    'date_from' => '2025-01-01',
    'date_to' => '2025-12-31'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
if ($error) {
    echo "cURL Error: " . $error . "\n";
}

if ($response) {
    $json_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
        echo "Message: " . ($json_data['message'] ?? 'N/A') . "\n";
        echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
        echo "\n✅ API WORKS WITH EXPLICIT DATE RANGE\n";
    } else {
        echo "JSON Error: " . json_last_error_msg() . "\n";
        echo "Response (first 500 chars): " . substr($response, 0, 500) . "\n";
    }
} else {
    echo "No response received\n";
}

