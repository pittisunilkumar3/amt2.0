<?php
/**
 * Test to verify all 4 APIs return ONLY JSON without PHP errors
 */

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Check if response is valid JSON
    $json_data = json_decode($response, true);
    $is_valid_json = (json_last_error() === JSON_ERROR_NONE);
    
    // Check if response contains HTML error tags
    $has_html_errors = (strpos($response, '<div style="border:1px solid #990000') !== false);
    
    return [
        'response' => $response,
        'json_data' => $json_data,
        'http_code' => $http_code,
        'is_valid_json' => $is_valid_json,
        'has_html_errors' => $has_html_errors
    ];
}

echo "==============================================\n";
echo "JSON-ONLY VERIFICATION TEST\n";
echo "==============================================\n\n";

$all_passed = true;

// Test 1: Collection Report API
echo "Test 1: Collection Report API\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/collection-report/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Valid JSON: " . ($result['is_valid_json'] ? "✓ YES" : "✗ NO") . "\n";
echo "Has HTML Errors: " . ($result['has_html_errors'] ? "✗ YES (FAIL)" : "✓ NO (PASS)") . "\n";
if ($result['is_valid_json']) {
    echo "Status: " . ($result['json_data']['status'] ?? 'N/A') . "\n";
    echo "Records: " . ($result['json_data']['total_records'] ?? 'N/A') . "\n";
}
$test1_pass = ($result['is_valid_json'] && !$result['has_html_errors'] && $result['http_code'] == 200);
echo "Result: " . ($test1_pass ? "✓ PASS" : "✗ FAIL") . "\n\n";
$all_passed = $all_passed && $test1_pass;

// Test 2: Total Student Academic Report API
echo "Test 2: Total Student Academic Report API\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/total-student-academic-report/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Valid JSON: " . ($result['is_valid_json'] ? "✓ YES" : "✗ NO") . "\n";
echo "Has HTML Errors: " . ($result['has_html_errors'] ? "✗ YES (FAIL)" : "✓ NO (PASS)") . "\n";
if ($result['is_valid_json']) {
    echo "Status: " . ($result['json_data']['status'] ?? 'N/A') . "\n";
    echo "Records: " . ($result['json_data']['total_records'] ?? 'N/A') . "\n";
}
$test2_pass = ($result['is_valid_json'] && !$result['has_html_errors'] && $result['http_code'] == 200);
echo "Result: " . ($test2_pass ? "✓ PASS" : "✗ FAIL") . "\n\n";
$all_passed = $all_passed && $test2_pass;

// Test 3: Student Academic Report API
echo "Test 3: Student Academic Report API\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/student-academic-report/filter', ['class_id' => '1'], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Valid JSON: " . ($result['is_valid_json'] ? "✓ YES" : "✗ NO") . "\n";
echo "Has HTML Errors: " . ($result['has_html_errors'] ? "✗ YES (FAIL)" : "✓ NO (PASS)") . "\n";
if ($result['is_valid_json']) {
    echo "Status: " . ($result['json_data']['status'] ?? 'N/A') . "\n";
    echo "Records: " . ($result['json_data']['total_records'] ?? 'N/A') . "\n";
}
$test3_pass = ($result['is_valid_json'] && !$result['has_html_errors'] && $result['http_code'] == 200);
echo "Result: " . ($test3_pass ? "✓ PASS" : "✗ FAIL") . "\n\n";
$all_passed = $all_passed && $test3_pass;

// Test 4: Report By Name API
echo "Test 4: Report By Name API\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/report-by-name/filter', [], $headers);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Valid JSON: " . ($result['is_valid_json'] ? "✓ YES" : "✗ NO") . "\n";
echo "Has HTML Errors: " . ($result['has_html_errors'] ? "✗ YES (FAIL)" : "✓ NO (PASS)") . "\n";
if ($result['is_valid_json']) {
    echo "Status: " . ($result['json_data']['status'] ?? 'N/A') . "\n";
    echo "Records: " . ($result['json_data']['total_records'] ?? 'N/A') . "\n";
}
$test4_pass = ($result['is_valid_json'] && !$result['has_html_errors'] && $result['http_code'] == 200);
echo "Result: " . ($test4_pass ? "✓ PASS" : "✗ FAIL") . "\n\n";
$all_passed = $all_passed && $test4_pass;

echo "==============================================\n";
echo "FINAL RESULT\n";
echo "==============================================\n";
if ($all_passed) {
    echo "✓ ALL TESTS PASSED!\n";
    echo "All 4 APIs return ONLY JSON without HTML errors.\n";
} else {
    echo "✗ SOME TESTS FAILED\n";
    echo "Please check the output above for details.\n";
}
echo "\n";

