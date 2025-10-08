<?php
$url = 'http://localhost/amt/api/student-academic-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "Testing Empty Request...\n";

$data = [];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n";
echo "Response Length: " . strlen($response) . " bytes\n\n";

if ($response) {
    $json_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
        echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n\n";
        
        if ($json_data['status'] == 1) {
            echo "✅ Empty request works!\n";
        } else {
            echo "❌ Error: " . ($json_data['message'] ?? 'Unknown') . "\n";
        }
    }
}

