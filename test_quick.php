<?php
$url = 'http://localhost/amt/api/student-academic-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$data = ['student_id' => '2481'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "CURL Error: " . ($error ? $error : 'None') . "\n";
echo "Response Length: " . strlen($response) . " bytes\n";

if ($response) {
    $json_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
        echo "Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    } else {
        echo "JSON Error: " . json_last_error_msg() . "\n";
        echo "First 200 chars: " . substr($response, 0, 200) . "\n";
    }
}

