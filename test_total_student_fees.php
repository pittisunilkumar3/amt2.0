<?php
$url = 'http://localhost/amt/api/total-student-academic-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test with specific class to get fewer records
$data = ['class_id' => '1', 'section_id' => '1'];

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

// Check if it's valid JSON
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Valid JSON: YES\n";
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n\n";
    
    // Show first 3 students with their fee details
    if (isset($json_data['data']) && is_array($json_data['data'])) {
        echo "Sample Student Fee Data:\n";
        echo "========================\n\n";
        
        $count = 0;
        foreach ($json_data['data'] as $student) {
            if ($count >= 3) break;
            
            echo "Student: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
            echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
            echo "Class: " . ($student['class'] ?? '') . " - " . ($student['section'] ?? '') . "\n";
            echo "Total Fee: " . ($student['total_fee'] ?? '0.00') . "\n";
            echo "Deposit: " . ($student['deposit'] ?? '0.00') . "\n";
            echo "Discount: " . ($student['discount'] ?? '0.00') . "\n";
            echo "Fine: " . ($student['fine'] ?? '0.00') . "\n";
            echo "Balance: " . ($student['balance'] ?? '0.00') . "\n";
            echo "---\n\n";
            
            $count++;
        }
        
        // Count students with fees > 0
        $students_with_fees = 0;
        foreach ($json_data['data'] as $student) {
            if (isset($student['total_fee']) && floatval($student['total_fee']) > 0) {
                $students_with_fees++;
            }
        }
        
        echo "Students with fees > 0: $students_with_fees / " . count($json_data['data']) . "\n";
    }
} else {
    echo "Valid JSON: NO\n";
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "\nFirst 1000 characters of response:\n";
    echo substr($response, 0, 1000) . "\n";
}

// Check for HTML errors
if (strpos($response, '<div style="border:1px solid #990000') !== false) {
    echo "\n⚠️ WARNING: Response contains HTML errors!\n";
} else {
    echo "\n✓ No HTML errors in response\n";
}

