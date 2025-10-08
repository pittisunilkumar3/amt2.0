<?php
/**
 * Test script for Daily Collection Report API
 */

// API URL
$url = 'http://localhost/amt/api/daily-collection-report/filter';

// Headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Data
$data = [];

// Make request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response: " . $response . "\n";

$result = json_decode($response, true);
if ($result) {
    echo "\nParsed Response:\n";
    print_r($result);
} else {
    echo "\nFailed to parse JSON response\n";
}

