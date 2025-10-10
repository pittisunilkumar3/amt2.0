<?php
/**
 * Debug script to test Daily Collection Report API
 * This script will help identify why the API is returning zero values
 */

// API endpoint
$api_url = 'http://localhost/amt/api/daily-collection-report/filter';

// Test with October 2025 date range
$request_data = [
    'date_from' => '2025-10-01',
    'date_to' => '2025-10-31'
];

// Initialize cURL
$ch = curl_init($api_url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
]);

// Execute request
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Display results
echo "=== Daily Collection Report API Debug Test ===\n\n";
echo "Request URL: $api_url\n";
echo "Request Data: " . json_encode($request_data, JSON_PRETTY_PRINT) . "\n\n";
echo "HTTP Status Code: $http_code\n\n";

if ($response) {
    $data = json_decode($response, true);
    echo "Response:\n";
    echo json_encode($data, JSON_PRETTY_PRINT) . "\n\n";
    
    if (isset($data['status']) && $data['status'] == 1) {
        echo "✓ API call successful\n\n";
        
        // Analyze fees_data
        echo "=== Regular Fees Data Analysis ===\n";
        if (isset($data['fees_data']) && is_array($data['fees_data'])) {
            $total_records = count($data['fees_data']);
            $non_zero_records = 0;
            $total_amount = 0;
            $total_count = 0;
            
            foreach ($data['fees_data'] as $record) {
                if ($record['amt'] > 0) {
                    $non_zero_records++;
                    $total_amount += $record['amt'];
                    $total_count += $record['count'];
                    echo "Date: {$record['date']}, Amount: {$record['amt']}, Count: {$record['count']}, IDs: " . implode(',', $record['student_fees_deposite_ids']) . "\n";
                }
            }
            
            echo "\nTotal Records: $total_records\n";
            echo "Non-Zero Records: $non_zero_records\n";
            echo "Total Amount: $total_amount\n";
            echo "Total Transactions: $total_count\n\n";
            
            if ($non_zero_records == 0) {
                echo "⚠ WARNING: All regular fees records have zero amounts!\n\n";
            }
        }
        
        // Analyze other_fees_data
        echo "=== Other Fees Data Analysis ===\n";
        if (isset($data['other_fees_data']) && is_array($data['other_fees_data'])) {
            $total_records = count($data['other_fees_data']);
            $non_zero_records = 0;
            $total_amount = 0;
            $total_count = 0;
            
            foreach ($data['other_fees_data'] as $record) {
                if ($record['amt'] > 0) {
                    $non_zero_records++;
                    $total_amount += $record['amt'];
                    $total_count += $record['count'];
                    echo "Date: {$record['date']}, Amount: {$record['amt']}, Count: {$record['count']}, IDs: " . implode(',', $record['student_fees_deposite_ids']) . "\n";
                }
            }
            
            echo "\nTotal Records: $total_records\n";
            echo "Non-Zero Records: $non_zero_records\n";
            echo "Total Amount: $total_amount\n";
            echo "Total Transactions: $total_count\n\n";
            
            if ($total_records == 0) {
                echo "⚠ WARNING: No other fees records found!\n\n";
            } elseif ($non_zero_records == 0) {
                echo "⚠ WARNING: All other fees records have zero amounts!\n\n";
            }
        }
    } else {
        echo "✗ API call failed\n";
        echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "✗ No response received from API\n";
}

echo "\n=== End of Debug Test ===\n";

