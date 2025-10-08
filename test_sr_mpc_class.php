<?php
// First, let's get the list of classes to find SR-MPC class ID
$url = 'http://localhost/amt/api/total-student-academic-report/list';
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
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$response = curl_exec($ch);
curl_close($ch);

$json_data = json_decode($response, true);

echo "Available Classes:\n";
echo "==================\n";
if (isset($json_data['classes']) && is_array($json_data['classes'])) {
    foreach ($json_data['classes'] as $class) {
        if (isset($class['class']) && strpos($class['class'], 'SR') !== false) {
            echo "ID: " . ($class['id'] ?? 'N/A') . " - " . ($class['class'] ?? 'N/A') . "\n";
        }
    }
}

echo "\n\nNow testing with a sample class...\n";
echo "===================================\n\n";

// Test with empty filter to see all students
$url2 = 'http://localhost/amt/api/total-student-academic-report/filter';
$ch2 = curl_init($url2);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_POST, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch2, CURLOPT_TIMEOUT, 60);
$response2 = curl_exec($ch2);
curl_close($ch2);

$json_data2 = json_decode($response2, true);

if (isset($json_data2['data']) && is_array($json_data2['data'])) {
    // Find students in SR-MPC class
    $sr_mpc_students = [];
    foreach ($json_data2['data'] as $student) {
        if (isset($student['class']) && strpos($student['class'], 'SR-MPC') !== false) {
            $sr_mpc_students[] = $student;
        }
    }
    
    echo "Found " . count($sr_mpc_students) . " students in SR-MPC class\n\n";
    
    // Show first 3 SR-MPC students
    $count = 0;
    foreach ($sr_mpc_students as $student) {
        if ($count >= 3) break;
        
        echo "Student: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
        echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
        echo "Total Fee: " . ($student['total_fee'] ?? '0.00') . "\n";
        echo "Balance: " . ($student['balance'] ?? '0.00') . "\n";
        echo "---\n\n";
        
        $count++;
    }
    
    // Check if NAVANEETH is in the list
    foreach ($sr_mpc_students as $student) {
        if (strpos($student['firstname'] ?? '', 'NAVANEETH') !== false || 
            strpos($student['lastname'] ?? '', 'NAVANEETH') !== false) {
            echo "\n✓ Found NAVANEETH in SR-MPC class:\n";
            echo "Name: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
            echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
            echo "Total Fee: " . ($student['total_fee'] ?? '0.00') . "\n";
            echo "Deposit: " . ($student['deposit'] ?? '0.00') . "\n";
            echo "Balance: " . ($student['balance'] ?? '0.00') . "\n";
            break;
        }
    }
}

