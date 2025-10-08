<?php
$url = 'http://localhost/amt/api/report-by-name/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Search for the specific student
$data = ['search_text' => 'NAVANEETH'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $http_code . "\n\n";

$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE && isset($json_data['data'])) {
    echo "Found " . count($json_data['data']) . " student(s) matching 'NAVANEETH'\n\n";
    
    foreach ($json_data['data'] as $student) {
        if (strpos($student['firstname'] ?? '', 'NAVANEETH') !== false || 
            strpos($student['lastname'] ?? '', 'NAVANEETH') !== false) {
            
            echo "Student Details:\n";
            echo "================\n";
            echo "Name: " . ($student['full_name'] ?? '') . "\n";
            echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
            echo "Class: " . ($student['class'] ?? '') . "\n";
            echo "Section: " . ($student['section'] ?? '') . "\n";
            echo "Father Name: " . ($student['father_name'] ?? '') . "\n";
            echo "\nFee Details:\n";
            echo "------------\n";
            echo "Total Fee: " . ($student['total_fee'] ?? '0.00') . "\n";
            echo "Deposit: " . ($student['deposit'] ?? '0.00') . "\n";
            echo "Discount: " . ($student['discount'] ?? '0.00') . "\n";
            echo "Fine: " . ($student['fine'] ?? '0.00') . "\n";
            echo "Balance: " . ($student['balance'] ?? '0.00') . "\n";
            echo "\n---\n\n";
        }
    }
} else {
    echo "Error or no data found\n";
}

