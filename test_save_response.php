<?php
$url = 'http://localhost/amt/api/report-by-name/filter';
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

// Save to file
file_put_contents('api_response.json', $response);

echo "HTTP Code: " . $http_code . "\n";
echo "Response saved to api_response.json\n";
echo "Response length: " . strlen($response) . " bytes\n";

// Pretty print JSON
$json_data = json_decode($response, true);
if (json_last_error() === JSON_ERROR_NONE) {
    file_put_contents('api_response_pretty.json', json_encode($json_data, JSON_PRETTY_PRINT));
    echo "Pretty JSON saved to api_response_pretty.json\n";
    
    if (isset($json_data['data'][0])) {
        $student = $json_data['data'][0];
        echo "\nStudent: " . ($student['firstname'] ?? '') . " " . ($student['lastname'] ?? '') . "\n";
        echo "Fee Groups: " . (isset($student['fees']) ? count($student['fees']) : 0) . "\n";
        
        if (isset($student['fees'][0][0])) {
            $first_fee = $student['fees'][0][0];
            echo "\nFirst Fee Type:\n";
            echo "  Type: " . ($first_fee['type'] ?? 'N/A') . "\n";
            echo "  Amount: " . ($first_fee['amount'] ?? 'N/A') . "\n";
            echo "  Has Payment History: " . (isset($first_fee['amount_detail']) && !empty($first_fee['amount_detail']) ? 'YES' : 'NO') . "\n";
        }
    }
}

