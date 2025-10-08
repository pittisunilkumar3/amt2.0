<?php
/**
 * Test script for Homework Report APIs
 * Tests all three homework-related APIs
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
echo "HOMEWORK REPORT APIs TESTING\n";
echo "==============================================\n\n";

// Test 1: Daily Assignment Report API - Empty Request
echo "Test 1: Daily Assignment Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/daily-assignment-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    echo "First Record ID: " . $result['response']['data'][0]['id'] . "\n";
    echo "Student Name: " . $result['response']['data'][0]['firstname'] . " " . $result['response']['data'][0]['lastname'] . "\n";
}
echo "\n";

// Test 2: Daily Assignment Report API - List
echo "Test 2: Daily Assignment Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/daily-assignment-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Classes: " . ($result['response']['total_classes'] ?? 0) . "\n";
echo "Current Session ID: " . ($result['response']['current_session_id'] ?? 'N/A') . "\n";
echo "\n";

// Test 3: Evaluation Report API - Empty Request
echo "Test 3: Evaluation Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/evaluation-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    echo "First Homework ID: " . $result['response']['data'][0]['id'] . "\n";
    echo "Subject: " . $result['response']['data'][0]['subject_name'] . "\n";
    if (isset($result['response']['data'][0]['evaluation_report'])) {
        $eval = $result['response']['data'][0]['evaluation_report'];
        echo "Total Students: " . $eval['total_students'] . "\n";
        echo "Evaluated Count: " . $eval['evaluated_count'] . "\n";
        echo "Evaluated Percentage: " . $eval['evaluated_percentage'] . "%\n";
    }
}
echo "\n";

// Test 4: Evaluation Report API - List
echo "Test 4: Evaluation Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/evaluation-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Classes: " . ($result['response']['total_classes'] ?? 0) . "\n";
echo "\n";

// Test 5: Homework Report API - Empty Request
echo "Test 5: Homework Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/homework-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    echo "First Homework ID: " . $result['response']['data'][0]['id'] . "\n";
    echo "Subject: " . $result['response']['data'][0]['subject_name'] . "\n";
    echo "Student Count: " . $result['response']['data'][0]['student_count'] . "\n";
    echo "Assignments: " . $result['response']['data'][0]['assignments'] . "\n";
    echo "Staff: " . $result['response']['data'][0]['staff_name'] . " " . $result['response']['data'][0]['staff_surname'] . "\n";
}
echo "\n";

// Test 6: Homework Report API - List
echo "Test 6: Homework Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/homework-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Classes: " . ($result['response']['total_classes'] ?? 0) . "\n";
echo "\n";

// Test 7: Daily Assignment Report API - Filter by Search Type
echo "Test 7: Daily Assignment Report API - Filter by Search Type (this_month)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/daily-assignment-report/filter', ['search_type' => 'this_month']);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
echo "Search Type Applied: " . ($result['response']['filters_applied']['search_type'] ?? 'N/A') . "\n";
echo "\n";

echo "==============================================\n";
echo "TESTING COMPLETE\n";
echo "==============================================\n";
echo "\nAll three Homework Report APIs have been tested.\n";
echo "Check the results above to verify functionality.\n";
?>

