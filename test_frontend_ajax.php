<?php
/**
 * Test Frontend AJAX Endpoint
 * This script tests the frontend AJAX endpoint directly
 */

echo "=== Testing Frontend AJAX Endpoint ===\n\n";

// Test the frontend AJAX endpoint
echo "Testing: http://localhost/amt/financereports/getFeeGroupwiseData\n\n";

$frontend_data = [
    'session_id' => '',
    'class_ids' => [],
    'section_ids' => [],
    'feegroup_ids' => [],
    'from_date' => '',
    'to_date' => '',
    'date_grouping' => 'none'
];

$frontend_context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($frontend_data),
        'timeout' => 30,
        'ignore_errors' => true
    ]
]);

$result = @file_get_contents('http://localhost/amt/financereports/getFeeGroupwiseData', false, $frontend_context);

if ($result === false) {
    echo "❌ Request failed\n";
    echo "Error: " . error_get_last()['message'] . "\n\n";
} else {
    echo "✅ Response received:\n";
    echo "Response Length: " . strlen($result) . " characters\n\n";
    
    // Check if it's HTML (login page) or JSON
    if (strpos($result, '<!DOCTYPE html>') !== false || strpos($result, '<html') !== false) {
        echo "❌ ISSUE: Response is HTML (likely redirected to login page)\n";
        echo "First 500 characters of response:\n";
        echo substr($result, 0, 500) . "\n\n";
        
        // Check if it's the login page
        if (strpos($result, 'Sign In to your Account') !== false) {
            echo "🔍 DIAGNOSIS: User is not authenticated - redirected to login page\n";
            echo "SOLUTION: Need to login first or bypass authentication for testing\n";
        }
    } else {
        // Try to decode as JSON
        $response = json_decode($result, true);
        if ($response) {
            echo "✅ Valid JSON response received\n";
            echo "Status: " . ($response['status'] ?? 'unknown') . "\n";
            echo "Message: " . ($response['message'] ?? 'no message') . "\n";
            
            if (isset($response['data'])) {
                echo "Data Count: " . count($response['data']) . "\n";
            }
            if (isset($response['detailed_data'])) {
                echo "Detailed Data Count: " . count($response['detailed_data']) . "\n";
            }
            if (isset($response['summary'])) {
                echo "Summary Present: Yes\n";
                echo "Total Amount: " . ($response['summary']['total_amount'] ?? 'N/A') . "\n";
            }
        } else {
            echo "❌ Invalid JSON response\n";
            echo "Raw Response (first 1000 chars):\n";
            echo substr($result, 0, 1000) . "\n";
        }
    }
}

echo "\n=== Test Complete ===\n";
?>
