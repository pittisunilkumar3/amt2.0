<?php
/**
 * Simple Test Script for Fee Group-wise Collection Report API
 */

// API Configuration
$base_url = 'http://localhost/amt/api/';
$endpoint = 'feegroupwise-collection-report/filter';

$headers = [
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@',
    'Content-Type: application/json'
];

$data = [];

echo "Testing API Endpoint: $base_url$endpoint\n";
echo "Request Data: " . json_encode($data) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $base_url . $endpoint);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_HEADER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

curl_close($ch);

echo "HTTP Status Code: $http_code\n";
echo "\nResponse Headers:\n";
echo $header;
echo "\nResponse Body:\n";
echo $body;
echo "\n";

// Try to decode JSON
$result = json_decode($body, true);
if ($result) {
    echo "\nParsed JSON:\n";
    echo json_encode($result, JSON_PRETTY_PRINT);
} else {
    echo "\nFailed to parse JSON. JSON Error: " . json_last_error_msg();
}
?>

