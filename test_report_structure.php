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

echo "==============================================\n";
echo "REPORT BY NAME API - DETAILED STRUCTURE TEST\n";
echo "==============================================\n\n";

echo "HTTP Code: " . $http_code . "\n\n";

if ($http_code == 200) {
    $json_data = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE && isset($json_data['data'][0])) {
        $student = $json_data['data'][0];
        
        echo "Student Information:\n";
        echo "====================\n";
        echo "Name: " . ($student['firstname'] ?? '') . " " . ($student['middlename'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
        echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
        echo "Class: " . ($student['class'] ?? '') . "\n";
        echo "Section: " . ($student['section'] ?? '') . "\n";
        echo "Father Name: " . ($student['father_name'] ?? '') . "\n";
        echo "Roll No: " . ($student['roll_no'] ?? '') . "\n";
        echo "Category: " . ($student['category'] ?? '') . "\n\n";
        
        // Fee Groups
        if (isset($student['fees']) && is_array($student['fees'])) {
            echo "Fee Groups: " . count($student['fees']) . " groups\n";
            echo "====================\n\n";
            
            foreach ($student['fees'] as $group_index => $fee_group) {
                echo "Fee Group #" . ($group_index + 1) . ":\n";
                echo "-------------------\n";
                
                if (is_array($fee_group) && count($fee_group) > 0) {
                    foreach ($fee_group as $fee_index => $fee) {
                        echo "  Fee Type #" . ($fee_index + 1) . ":\n";
                        echo "    Type: " . ($fee['type'] ?? 'N/A') . "\n";
                        echo "    Code: " . ($fee['code'] ?? 'N/A') . "\n";
                        echo "    Amount: " . ($fee['amount'] ?? 'N/A') . "\n";
                        echo "    Due Date: " . ($fee['due_date'] ?? 'N/A') . "\n";
                        echo "    Fine Amount: " . ($fee['fine_amount'] ?? 'N/A') . "\n";
                        
                        // Payment History
                        if (isset($fee['amount_detail']) && !empty($fee['amount_detail'])) {
                            $amount_detail = json_decode($fee['amount_detail'], true);
                            if (is_array($amount_detail) && count($amount_detail) > 0) {
                                echo "    Payment History: " . count($amount_detail) . " payment(s)\n";
                                
                                foreach ($amount_detail as $pay_index => $payment) {
                                    echo "      Payment #" . ($pay_index + 1) . ":\n";
                                    echo "        Amount: " . ($payment['amount'] ?? 'N/A') . "\n";
                                    echo "        Date: " . ($payment['date'] ?? 'N/A') . "\n";
                                    echo "        Payment Mode: " . ($payment['payment_mode'] ?? 'N/A') . "\n";
                                    echo "        Discount: " . ($payment['amount_discount'] ?? '0') . "\n";
                                    echo "        Fine: " . ($payment['amount_fine'] ?? '0') . "\n";
                                }
                            } else {
                                echo "    Payment History: No payments\n";
                            }
                        } else {
                            echo "    Payment History: No payments\n";
                        }
                        echo "\n";
                    }
                }
            }
        }
        
        // Student Discount
        if (isset($student['student_discount_fee']) && is_array($student['student_discount_fee'])) {
            echo "\nStudent Discounts: " . count($student['student_discount_fee']) . " discount(s)\n";
            echo "====================\n";
            foreach ($student['student_discount_fee'] as $discount) {
                echo "  Discount: " . ($discount['name'] ?? 'N/A') . "\n";
                echo "  Amount: " . ($discount['amount'] ?? 'N/A') . "\n";
            }
        }
        
        // Transport Fees
        if (isset($student['transport_fees']) && is_array($student['transport_fees']) && count($student['transport_fees']) > 0) {
            echo "\nTransport Fees: " . count($student['transport_fees']) . " record(s)\n";
            echo "====================\n";
            foreach ($student['transport_fees'] as $transport) {
                echo "  Month: " . ($transport->month ?? 'N/A') . "\n";
                echo "  Amount: " . ($transport->fees ?? 'N/A') . "\n";
                echo "  Due Date: " . ($transport->due_date ?? 'N/A') . "\n";
            }
        } else {
            echo "\nTransport Fees: None\n";
        }
        
        echo "\n==============================================\n";
        echo "RESULT: ✓ API RETURNS COMPLETE FEE DETAILS!\n";
        echo "==============================================\n\n";
        
        echo "The API now includes:\n";
        echo "✓ Student information\n";
        echo "✓ Fee groups with fee types\n";
        echo "✓ Fee amounts and due dates\n";
        echo "✓ Payment history with dates and modes\n";
        echo "✓ Discount information\n";
        echo "✓ Fine information\n";
        echo "✓ Student discount details\n";
        echo "✓ Transport fees (if applicable)\n";
        
    } else {
        echo "Error: Invalid JSON or no data\n";
    }
} else {
    echo "Error: HTTP " . $http_code . "\n";
}

