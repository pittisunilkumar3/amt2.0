<?php
/**
 * Simple test for Online Admission Report API
 */

$url = 'http://localhost/amt/api/online-admission-report/list';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "Testing Online Admission Report API - List Endpoint\n";
echo "URL: $url\n\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
if ($error) {
    echo "cURL Error: " . $error . "\n";
}
echo "Response:\n";
echo $response . "\n";

