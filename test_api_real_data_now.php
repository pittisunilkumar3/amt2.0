<?php
/**
 * Test Daily Collection Report API with December 2021 data
 */

echo "=== Testing Daily Collection Report API with Real Data ===\n\n";

// Test with December 2021
$api_url = 'http://localhost/amt/api/daily-collection-report/filter';
$request_data = [
    'date_from' => '2021-12-01',
    'date_to' => '2021-12-31'
];

echo "Request URL: $api_url\n";
echo "Request Body: " . json_encode($request_data, JSON_PRETTY_PRINT) . "\n\n";

$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $http_code\n";
if ($curl_error) {
    echo "cURL Error: $curl_error\n";
}
echo "\n";

if ($response) {
    $data = json_decode($response, true);
    
    if ($data) {
        echo "Response:\n";
        echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
        
        if (isset($data['status']) && $data['status'] == 1) {
            // Count non-zero records
            $non_zero = 0;
            $total_amt = 0;
            
            if (isset($data['fees_data'])) {
                foreach ($data['fees_data'] as $record) {
                    if ($record['amt'] > 0) {
                        $non_zero++;
                        $total_amt += $record['amt'];
                        echo "✓ {$record['date']}: Amount={$record['amt']}, Count={$record['count']}\n";
                    }
                }
            }
            
            echo "\n=== RESULT ===\n";
            echo "Non-zero records: $non_zero\n";
            echo "Total amount: $total_amt\n";
            
            if ($non_zero > 0) {
                echo "\n✓✓✓ SUCCESS: API is returning real data!\n";
            } else {
                echo "\n✗✗✗ FAILURE: API still returning zeros - THERE IS A BUG!\n";
            }
        }
    } else {
        echo "Failed to parse JSON response\n";
        echo "Raw response: $response\n";
    }
} else {
    echo "No response from API\n";
}

