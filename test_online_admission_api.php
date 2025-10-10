<?php
/**
 * Test Script for Online Admission API
 * 
 * This script tests the Online Admission API filter endpoint
 * Run from command line: php test_online_admission_api.php
 * Or access via browser: http://localhost/amt/test_online_admission_api.php
 */

// API endpoint
$api_url = 'http://localhost/amt/api/online-admission/filter';

// Test 1: Filter with empty body (should return all records)
echo "=================================================\n";
echo "Test 1: Filter with empty request body\n";
echo "=================================================\n";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response:\n";
$data = json_decode($response, true);
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Filter by gender
echo "=================================================\n";
echo "Test 2: Filter by gender (Male)\n";
echo "=================================================\n";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'gender' => 'Male'
)));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response:\n";
$data = json_decode($response, true);
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Filter by enrollment status
echo "=================================================\n";
echo "Test 3: Filter by enrollment status (is_enroll = 1)\n";
echo "=================================================\n";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'is_enroll' => '1'
)));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response:\n";
$data = json_decode($response, true);
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Test 4: Filter by date range
echo "=================================================\n";
echo "Test 4: Filter by date range\n";
echo "=================================================\n";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
    'date_from' => '2024-01-01',
    'date_to' => '2024-12-31'
)));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response:\n";
$data = json_decode($response, true);
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

// Test 5: Test list endpoint
echo "=================================================\n";
echo "Test 5: List endpoint (get filter options)\n";
echo "=================================================\n";

$list_url = 'http://localhost/amt/api/online-admission/list';
$ch = curl_init($list_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array()));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "Response:\n";
$data = json_decode($response, true);
echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";

echo "=================================================\n";
echo "All tests completed!\n";
echo "=================================================\n";
?>

