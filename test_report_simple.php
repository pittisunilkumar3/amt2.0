<?php
$url = 'http://localhost/amt/api/report-by-name/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test with student_id filter
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
echo "Response Length: " . strlen($response) . " bytes\n\n";

if ($http_code == 200) {
    $json_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "Valid JSON: YES\n";
        echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
        echo "Records: " . ($json_data['total_records'] ?? 'N/A') . "\n\n";
        
        if (isset($json_data['data'][0])) {
            $student = $json_data['data'][0];
            echo "Student: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
            echo "Keys: " . implode(", ", array_keys($student)) . "\n\n";
            
            // Check if fees array exists
            if (isset($student['fees'])) {
                echo "✓ Fees array exists with " . count($student['fees']) . " groups\n";
            } else {
                echo "✗ No fees array\n";
            }
        }
    } else {
        echo "JSON Error: " . json_last_error_msg() . "\n";
    }
} else {
    echo "Error: HTTP " . $http_code . "\n";
    echo "Response: " . substr($response, 0, 500) . "\n";
}

