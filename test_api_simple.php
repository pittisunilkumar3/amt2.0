<?php
// Simple test for each API
$apis = [
    'collection-report',
    'total-student-academic-report',
    'student-academic-report',
    'report-by-name'
];

$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

foreach ($apis as $api) {
    $url = "http://localhost/amt/api/$api/list";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "$api: HTTP $http_code\n";
    if ($http_code == 200) {
        $data = json_decode($response, true);
        echo "  Status: " . ($data['status'] ?? 'N/A') . "\n";
    }
    echo "\n";
}

