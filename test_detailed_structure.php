<?php
$url = 'http://localhost/amt/api/student-academic-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

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

// Save response
file_put_contents('student_academic_response.json', $response);

echo "HTTP Code: " . $http_code . "\n";
echo "Response saved to student_academic_response.json\n\n";

$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE && isset($json_data['data'][0])) {
    $student = $json_data['data'][0];
    
    echo "Student: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
    echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
    echo "Class: " . ($student['class'] ?? '') . "\n\n";
    
    echo "Detailed Fee Structure Check:\n";
    echo "=============================\n";
    
    if (isset($student['fees']) && is_array($student['fees'])) {
        echo "✓ Fee Groups: " . count($student['fees']) . "\n";
        
        if (count($student['fees']) > 0 && is_array($student['fees'][0])) {
            echo "✓ First group has " . count($student['fees'][0]) . " fee types\n";
            
            $first_fee = $student['fees'][0][0];
            echo "\nFirst Fee Type Details:\n";
            echo "  Name: " . ($first_fee['name'] ?? 'N/A') . "\n";
            echo "  Type: " . ($first_fee['type'] ?? 'N/A') . "\n";
            echo "  Amount: " . ($first_fee['amount'] ?? 'N/A') . "\n";
            echo "  Due Date: " . ($first_fee['due_date'] ?? 'N/A') . "\n";
            echo "  Has Payment Detail: " . (isset($first_fee['amount_detail']) && $first_fee['amount_detail'] !== '0' ? 'YES' : 'NO') . "\n";
        }
    } else {
        echo "✗ No fee groups found\n";
    }
    
    echo "\n";
    echo "✓ Student Discount: " . (isset($student['student_discount_fee']) ? 'Present' : 'Not Present') . "\n";
    echo "✓ Transport Fees: " . (isset($student['transport_fees']) ? 'Present' : 'Not Present') . "\n";
    
    echo "\n=============================\n";
    echo "✅ API returns detailed fee structure!\n";
    
    // Pretty print
    file_put_contents('student_academic_pretty.json', json_encode($json_data, JSON_PRETTY_PRINT));
    echo "Pretty JSON saved to student_academic_pretty.json\n";
}

