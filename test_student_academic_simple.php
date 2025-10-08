<?php
$url = 'http://localhost/amt/api/student-academic-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "TEST 1: Empty Request\n";
echo "----------------------\n";
$data = [];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response Length: " . strlen($response) . " bytes\n";

$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Valid JSON: YES\n";
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    
    if ($json_data['status'] == 1) {
        echo "✅ Empty request works (no validation error)\n";
    } else {
        echo "❌ Empty request failed: " . ($json_data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Response: " . substr($response, 0, 200) . "\n";
}

echo "\n\nTEST 2: Filter by Student ID\n";
echo "-----------------------------\n";
$data = ['student_id' => '2481'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";

$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE && isset($json_data['data'][0])) {
    $student = $json_data['data'][0];
    echo "Student: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
    echo "Has Fees: " . (isset($student['fees']) ? 'YES (' . count($student['fees']) . ' groups)' : 'NO') . "\n";
    echo "✅ Student ID filter works\n";
} else {
    echo "❌ Student ID filter failed\n";
}

