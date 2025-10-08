<?php
/**
 * Test script for Due Fees Report API
 */

// API Configuration
$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Function to make API request
function makeRequest($url, $data = []) {
    global $headers;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

echo "==============================================\n";
echo "DUE FEES REPORT API TESTING\n";
echo "==============================================\n\n";

// Test 1: Due Fees Report API - Empty Request
echo "Test 1: Due Fees Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/due-fees-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    $firstRecord = $result['response']['data'][0];
    echo "First Student: " . $firstRecord['firstname'] . " " . $firstRecord['lastname'] . "\n";
    echo "Admission No: " . $firstRecord['admission_no'] . "\n";
    echo "Class: " . $firstRecord['class'] . " - " . $firstRecord['section'] . "\n";
}
echo "\n";

// Test 2: Due Fees Report API - List
echo "Test 2: Due Fees Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/due-fees-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Classes: " . count($result['response']['classes'] ?? []) . "\n";
echo "\n";

// Test 3: Due Fees Report API - Filter by Class
echo "Test 3: Due Fees Report API - Filter by Class (class_id=1)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/due-fees-report/filter', ['class_id' => '1']);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
echo "Class ID Applied: " . ($result['response']['filters_applied']['class_id'] ?? 'N/A') . "\n";
echo "\n";

// Test 4: Due Fees Report API - Filter by Class and Section
echo "Test 4: Due Fees Report API - Filter by Class and Section\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/due-fees-report/filter', [
    'class_id' => '1',
    'section_id' => '1'
]);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
echo "Class ID Applied: " . ($result['response']['filters_applied']['class_id'] ?? 'N/A') . "\n";
echo "Section ID Applied: " . ($result['response']['filters_applied']['section_id'] ?? 'N/A') . "\n";
echo "\n";

echo "==============================================\n";
echo "TESTING COMPLETE\n";
echo "==============================================\n";
echo "\nDue Fees Report API has been tested.\n";
echo "Check the results above to verify functionality.\n";
?>

