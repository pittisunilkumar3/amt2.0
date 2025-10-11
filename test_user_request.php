<?php
/**
 * Test script for the exact user request
 * Tests the Collection Report API with the user's specific parameters
 */

// Configuration
$base_url = 'http://localhost/amt/api/collection-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// User's exact request
$user_request = [
    'session_id' => '21',
    'class_id' => '19',
    'section_id' => '36',
    'fee_type_id' => '33',
    'collect_by_id' => '6',
    'search_type' => 'all',
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11'
];

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║              TESTING USER'S EXACT REQUEST                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "Request URL: {$base_url}\n";
echo "\nRequest Headers:\n";
foreach ($headers as $header) {
    echo "  - {$header}\n";
}

echo "\nRequest Body:\n";
echo json_encode($user_request, JSON_PRETTY_PRINT) . "\n";

// Make the request
$ch = curl_init($base_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($user_request));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "\n" . str_repeat("=", 80) . "\n";
echo "RESPONSE\n";
echo str_repeat("=", 80) . "\n";

if ($curl_error) {
    echo "CURL Error: {$curl_error}\n";
} else {
    echo "HTTP Status Code: {$http_code}\n\n";
    
    $response_data = json_decode($response, true);
    
    if ($response_data) {
        echo "Response Body:\n";
        echo json_encode($response_data, JSON_PRETTY_PRINT) . "\n";
        
        // Analyze the response
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "ANALYSIS\n";
        echo str_repeat("=", 80) . "\n";
        
        if (isset($response_data['status']) && $response_data['status'] == 1) {
            echo "✓ Status: SUCCESS\n";
            echo "✓ Message: {$response_data['message']}\n";
            
            if (isset($response_data['total_records'])) {
                echo "✓ Total Records: {$response_data['total_records']}\n";
            }
            
            if (isset($response_data['filters_applied'])) {
                echo "\n✓ Filters Applied:\n";
                foreach ($response_data['filters_applied'] as $key => $value) {
                    $display_value = is_null($value) ? 'null' : $value;
                    echo "  - {$key}: {$display_value}\n";
                }
            }
            
            if (isset($response_data['data']) && is_array($response_data['data'])) {
                $record_count = count($response_data['data']);
                echo "\n✓ Data Records: {$record_count}\n";
                
                if ($record_count > 0) {
                    echo "\nFirst Record Sample:\n";
                    $first_record = $response_data['data'][0];
                    echo "  - Student: {$first_record['firstname']} {$first_record['lastname']}\n";
                    echo "  - Admission No: {$first_record['admission_no']}\n";
                    echo "  - Class: {$first_record['class']}\n";
                    echo "  - Section: {$first_record['section']}\n";
                    echo "  - Fee Type: {$first_record['type']}\n";
                    echo "  - Amount: {$first_record['amount']}\n";
                    echo "  - Date: {$first_record['date']}\n";
                    echo "  - Payment Mode: {$first_record['payment_mode']}\n";
                    echo "  - Received By: {$first_record['received_by']}\n";
                }
            }
            
            echo "\n╔════════════════════════════════════════════════════════════════════════════╗\n";
            echo "║                    ✓ TEST PASSED SUCCESSFULLY ✓                            ║\n";
            echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
            
        } else {
            echo "✗ Status: FAILED\n";
            if (isset($response_data['message'])) {
                echo "✗ Error Message: {$response_data['message']}\n";
            }
            
            echo "\n╔════════════════════════════════════════════════════════════════════════════╗\n";
            echo "║                        ✗ TEST FAILED ✗                                     ║\n";
            echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
        }
    } else {
        echo "✗ Failed to parse JSON response\n";
        echo "Raw Response:\n{$response}\n";
    }
}

echo "\n";

// Additional test with standard parameter names for comparison
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         TESTING WITH STANDARD PARAMETER NAMES (FOR COMPARISON)             ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$standard_request = [
    'session_id' => '21',
    'class_id' => '19',
    'section_id' => '36',
    'feetype_id' => '33',
    'received_by' => '6',
    'date_from' => '2025-09-01',
    'date_to' => '2025-10-11'
];

echo "Request Body:\n";
echo json_encode($standard_request, JSON_PRETTY_PRINT) . "\n";

$ch = curl_init($base_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($standard_request));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "\nHTTP Status Code: {$http_code}\n";

$response_data = json_decode($response, true);
if ($response_data && isset($response_data['status']) && $response_data['status'] == 1) {
    echo "✓ Status: SUCCESS\n";
    echo "✓ Total Records: {$response_data['total_records']}\n";
    echo "\n✓ Both parameter naming conventions work correctly!\n";
} else {
    echo "✗ Status: FAILED\n";
    if (isset($response_data['message'])) {
        echo "✗ Error: {$response_data['message']}\n";
    }
}

echo "\n";

