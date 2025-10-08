<?php
$url = 'http://localhost/amt/api/report-by-name/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response Length: " . strlen($response) . " bytes\n";

// Check if it's valid JSON
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Valid JSON: YES\n";
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
} else {
    echo "Valid JSON: NO\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
}

// Check for HTML errors
if (strpos($response, '<div style="border:1px solid #990000') !== false) {
    echo "Contains HTML Errors: YES (PROBLEM!)\n";
} else {
    echo "Contains HTML Errors: NO (GOOD!)\n";
}

echo "\nFirst 500 characters of response:\n";
echo substr($response, 0, 500) . "\n";

