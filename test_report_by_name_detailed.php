<?php
/**
 * Test Report By Name API to verify it returns detailed fee information
 */

$url = 'http://localhost/amt/api/report-by-name/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Search for a specific student
$data = ['search_text' => 'NANDHINI'];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "==============================================\n";
echo "REPORT BY NAME API - DETAILED FEE TEST\n";
echo "==============================================\n\n";

echo "HTTP Code: " . $http_code . "\n";
echo "Response Length: " . number_format(strlen($response)) . " bytes\n\n";

// Check if it's valid JSON
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    echo "Valid JSON: YES\n";
    echo "Status: " . ($json_data['status'] ?? 'N/A') . "\n";
    echo "Total Records: " . ($json_data['total_records'] ?? 'N/A') . "\n\n";
    
    if (isset($json_data['data']) && is_array($json_data['data']) && count($json_data['data']) > 0) {
        $student = $json_data['data'][0];
        
        echo "Student Information:\n";
        echo "====================\n";
        echo "Name: " . ($student['firstname'] ?? '') . " " . ($student['middlename'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
        echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
        echo "Class: " . ($student['class'] ?? '') . "\n";
        echo "Section: " . ($student['section'] ?? '') . "\n";
        echo "Father Name: " . ($student['father_name'] ?? '') . "\n\n";
        
        // Check if detailed fee structure is present
        echo "Fee Structure Check:\n";
        echo "====================\n";
        
        if (isset($student['fees']) && is_array($student['fees'])) {
            echo "✓ Fee Groups: " . count($student['fees']) . " groups found\n";
            
            // Show first fee group details
            if (count($student['fees']) > 0) {
                $fee_group = $student['fees'][0];
                echo "\nFirst Fee Group Details:\n";
                echo "------------------------\n";
                
                if (is_array($fee_group) && count($fee_group) > 0) {
                    $first_fee = $fee_group[0];
                    echo "Fee Type: " . ($first_fee['type'] ?? 'N/A') . "\n";
                    echo "Fee Code: " . ($first_fee['code'] ?? 'N/A') . "\n";
                    echo "Amount: " . ($first_fee['amount'] ?? 'N/A') . "\n";
                    echo "Due Date: " . ($first_fee['due_date'] ?? 'N/A') . "\n";
                    
                    // Check for payment details
                    if (isset($first_fee['amount_detail']) && !empty($first_fee['amount_detail'])) {
                        echo "\n✓ Payment History Found:\n";
                        $amount_detail = json_decode($first_fee['amount_detail'], true);
                        if (is_array($amount_detail)) {
                            echo "  Number of Payments: " . count($amount_detail) . "\n";
                            
                            // Show first payment
                            if (count($amount_detail) > 0) {
                                $payment = $amount_detail[0];
                                echo "\n  First Payment:\n";
                                echo "  - Amount: " . ($payment['amount'] ?? 'N/A') . "\n";
                                echo "  - Date: " . ($payment['date'] ?? 'N/A') . "\n";
                                echo "  - Payment Mode: " . ($payment['payment_mode'] ?? 'N/A') . "\n";
                                echo "  - Discount: " . ($payment['amount_discount'] ?? '0') . "\n";
                                echo "  - Fine: " . ($payment['amount_fine'] ?? '0') . "\n";
                            }
                        }
                    } else {
                        echo "\n⚠ No payment history found\n";
                    }
                }
            }
        } else {
            echo "✗ No fee groups found\n";
        }
        
        // Check for transport fees
        if (isset($student['transport_fees']) && is_array($student['transport_fees'])) {
            echo "\n✓ Transport Fees: " . count($student['transport_fees']) . " records found\n";
        } else {
            echo "\n⚠ No transport fees found\n";
        }
        
        // Check for student discount
        if (isset($student['student_discount_fee'])) {
            echo "✓ Student Discount Info: Present\n";
        } else {
            echo "⚠ Student Discount Info: Not found\n";
        }
        
        echo "\n\nFull Student Data Structure:\n";
        echo "============================\n";
        echo "Keys present: " . implode(", ", array_keys($student)) . "\n";
        
        echo "\n\nFirst 2000 characters of response:\n";
        echo "===================================\n";
        echo substr($response, 0, 2000) . "\n";
        
    } else {
        echo "No students found\n";
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

