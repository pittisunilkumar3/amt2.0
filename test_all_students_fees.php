<?php
$url = 'http://localhost/amt/api/total-student-academic-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test with empty filter to get all students
$data = [];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
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
    
    // Show first 5 students with their fee details
    if (isset($json_data['data']) && is_array($json_data['data'])) {
        echo "Sample Student Fee Data:\n";
        echo "========================\n\n";
        
        $count = 0;
        foreach ($json_data['data'] as $student) {
            if ($count >= 5) break;
            
            echo "Student #" . ($count + 1) . ": " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
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
        $total_students = count($json_data['data']);
        foreach ($json_data['data'] as $student) {
            if (isset($student['total_fee']) && floatval($student['total_fee']) > 0) {
                $students_with_fees++;
            }
        }
        
        echo "Summary:\n";
        echo "--------\n";
        echo "Total Students: $total_students\n";
        echo "Students with fees > 0: $students_with_fees\n";
        echo "Students with zero fees: " . ($total_students - $students_with_fees) . "\n";
        
        if ($students_with_fees > 0) {
            echo "\n✓ Fee calculation is working!\n";
        } else {
            echo "\n⚠️ WARNING: All students have zero fees!\n";
        }
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

