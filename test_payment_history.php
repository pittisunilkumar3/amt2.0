<?php
$url = 'http://localhost/amt/api/report-by-name/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test with NAVANEETH who has payment history
$data = ['search_text' => 'NAVANEETH'];

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
echo "PAYMENT HISTORY TEST\n";
echo "==============================================\n\n";

$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE && isset($json_data['data'][0])) {
    $student = $json_data['data'][0];
    
    echo "Student: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
    echo "Admission No: " . ($student['admission_no'] ?? '') . "\n";
    echo "Class: " . ($student['class'] ?? '') . "\n\n";
    
    $total_payments = 0;
    $total_amount_paid = 0;
    
    if (isset($student['fees']) && is_array($student['fees'])) {
        foreach ($student['fees'] as $group_index => $fee_group) {
            if (is_array($fee_group)) {
                foreach ($fee_group as $fee) {
                    if (isset($fee['amount_detail']) && !empty($fee['amount_detail']) && $fee['amount_detail'] !== '0') {
                        $amount_detail = json_decode($fee['amount_detail'], true);
                        if (is_array($amount_detail) && count($amount_detail) > 0) {
                            echo "Fee Type: " . ($fee['type'] ?? 'N/A') . "\n";
                            echo "Fee Amount: " . ($fee['amount'] ?? 'N/A') . "\n";
                            echo "Payments: " . count($amount_detail) . "\n";
                            
                            foreach ($amount_detail as $payment) {
                                $total_payments++;
                                $amount = isset($payment['amount']) ? floatval($payment['amount']) : 0;
                                $total_amount_paid += $amount;
                                
                                echo "  - Date: " . ($payment['date'] ?? 'N/A');
                                echo " | Amount: " . $amount;
                                echo " | Mode: " . ($payment['payment_mode'] ?? 'N/A');
                                echo " | Discount: " . ($payment['amount_discount'] ?? '0');
                                echo " | Fine: " . ($payment['amount_fine'] ?? '0');
                                echo "\n";
                            }
                            echo "\n";
                        }
                    }
                }
            }
        }
    }
    
    echo "==============================================\n";
    echo "SUMMARY:\n";
    echo "==============================================\n";
    echo "Total Payments: " . $total_payments . "\n";
    echo "Total Amount Paid: ₹" . number_format($total_amount_paid, 2) . "\n";
    
    if ($total_payments > 0) {
        echo "\n✅ SUCCESS: Payment history is showing correctly!\n";
    } else {
        echo "\n⚠️ No payment history found for this student\n";
    }
} else {
    echo "Error: Invalid response or no data\n";
}

