<?php
/**
 * Simple API Test
 * Quick test to verify the menu API is working
 */

// Test the API directly
$api_url = "http://localhost/amt/api/teacher/menu";

// Test with a simple staff_id
$test_data = json_encode(['staff_id' => 1]);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $test_data,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json'
    ],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');

if ($error) {
    echo json_encode([
        'test_result' => 'error',
        'curl_error' => $error,
        'http_code' => $http_code
    ]);
} else {
    $result = json_decode($response, true);
    echo json_encode([
        'test_result' => 'success',
        'http_code' => $http_code,
        'api_response' => $result,
        'test_url' => $api_url,
        'test_data' => $test_data
    ], JSON_PRETTY_PRINT);
}
?>