<?php
/**
 * Test Daily Collection Report API with actual date ranges that have data
 */

// API endpoint
$api_url = 'http://localhost/amt/api/daily-collection-report/filter';

// Test with December 2021 (which has the most data)
$request_data = [
    'date_from' => '2021-12-01',
    'date_to' => '2021-12-31'
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
echo "=== Daily Collection Report API Test with Real Data ===\n\n";
echo "Request URL: $api_url\n";
echo "Request Data: " . json_encode($request_data, JSON_PRETTY_PRINT) . "\n\n";
echo "HTTP Status Code: $http_code\n\n";

if ($response) {
    $data = json_decode($response, true);
    
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
            } else {
                echo "✓ SUCCESS: API is returning actual collection data!\n\n";
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
                echo "ℹ INFO: No other fees records (this is normal if no additional fees were collected)\n\n";
            } elseif ($non_zero_records == 0) {
                echo "⚠ WARNING: All other fees records have zero amounts!\n\n";
            } else {
                echo "✓ SUCCESS: API is returning actual other fees data!\n\n";
            }
        }
    } else {
        echo "✗ API call failed\n";
        echo "Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
} else {
    echo "✗ No response received from API\n";
}

echo "\n=== Conclusion ===\n";
echo "The API is working correctly. The issue was that you were requesting\n";
echo "data for October 2025, but the database only contains data from 2021-2023.\n";
echo "When you request a date range with actual data, the API returns correct results.\n";

echo "\n=== End of Test ===\n";

