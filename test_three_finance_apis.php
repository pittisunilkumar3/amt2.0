<?php
/**
 * Test script for three finance report APIs
 * Tests: Income Report, Due Fees Remark Report, Online Fees Report
 */

// API base URL
$base_url = 'http://localhost/amt/api';

// Headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Color codes for output
$GREEN = "\033[32m";
$RED = "\033[31m";
$YELLOW = "\033[33m";
$BLUE = "\033[34m";
$RESET = "\033[0m";

/**
 * Make API request
 */
function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => $response,
        'error' => $error
    ];
}

/**
 * Print test result
 */
function printResult($test_name, $result, $expected_status = 1) {
    global $GREEN, $RED, $YELLOW, $BLUE, $RESET;
    
    echo "\n{$BLUE}=== $test_name ==={$RESET}\n";
    echo "HTTP Code: {$result['http_code']}\n";
    
    if ($result['error']) {
        echo "{$RED}cURL Error: {$result['error']}{$RESET}\n";
        return false;
    }
    
    $data = json_decode($result['response'], true);
    
    if ($result['http_code'] == 200 && $data && isset($data['status'])) {
        if ($data['status'] == $expected_status) {
            echo "{$GREEN}✓ PASSED{$RESET}\n";
            echo "Message: {$data['message']}\n";
            if (isset($data['total_records'])) {
                echo "Total Records: {$data['total_records']}\n";
            }
            if (isset($data['summary'])) {
                echo "Summary: " . json_encode($data['summary']) . "\n";
            }
            return true;
        } else {
            echo "{$RED}✗ FAILED - Unexpected status: {$data['status']}{$RESET}\n";
            echo "Message: {$data['message']}\n";
            if (isset($data['error'])) {
                echo "Error: {$data['error']}\n";
            }
            return false;
        }
    } else {
        echo "{$RED}✗ FAILED - Invalid response{$RESET}\n";
        echo "Response: " . substr($result['response'], 0, 1000) . "\n";
        // Try to decode and show any error
        $decoded = json_decode($result['response'], true);
        if ($decoded && isset($decoded['error'])) {
            echo "Error Details: {$decoded['error']}\n";
        }
        return false;
    }
}

echo "\n{$BLUE}========================================{$RESET}\n";
echo "{$BLUE}  Testing Three Finance Report APIs{$RESET}\n";
echo "{$BLUE}========================================{$RESET}\n";

// ============================================
// 1. INCOME REPORT API TESTS
// ============================================
echo "\n\n{$YELLOW}>>> 1. INCOME REPORT API TESTS{$RESET}\n";

// Test 1.1: List endpoint
$result = makeRequest("$base_url/income-report/list", [], $headers);
printResult("Income Report - List Endpoint", $result);

// Test 1.2: Filter with empty request
$result = makeRequest("$base_url/income-report/filter", [], $headers);
printResult("Income Report - Filter (Empty Request)", $result);

// Test 1.3: Filter with search_type
$result = makeRequest("$base_url/income-report/filter", ['search_type' => 'this_month'], $headers);
printResult("Income Report - Filter (This Month)", $result);

// Test 1.4: Filter with custom dates
$result = makeRequest("$base_url/income-report/filter", [
    'date_from' => '2025-01-01',
    'date_to' => '2025-12-31'
], $headers);
printResult("Income Report - Filter (Custom Dates)", $result);

// ============================================
// 2. DUE FEES REMARK REPORT API TESTS
// ============================================
echo "\n\n{$YELLOW}>>> 2. DUE FEES REMARK REPORT API TESTS{$RESET}\n";

// Test 2.1: List endpoint
$result = makeRequest("$base_url/due-fees-remark-report/list", [], $headers);
printResult("Due Fees Remark - List Endpoint", $result);

// Test 2.2: Filter with empty request
$result = makeRequest("$base_url/due-fees-remark-report/filter", [], $headers);
printResult("Due Fees Remark - Filter (Empty Request)", $result);

// Test 2.3: Filter with class and section
$result = makeRequest("$base_url/due-fees-remark-report/filter", [
    'class_id' => '1',
    'section_id' => '1'
], $headers);
printResult("Due Fees Remark - Filter (Class 1, Section 1)", $result);

// ============================================
// 3. ONLINE FEES REPORT API TESTS
// ============================================
echo "\n\n{$YELLOW}>>> 3. ONLINE FEES REPORT API TESTS{$RESET}\n";

// Test 3.1: List endpoint
$result = makeRequest("$base_url/online-fees-report/list", [], $headers);
printResult("Online Fees Report - List Endpoint", $result);

// Test 3.2: Filter with empty request
$result = makeRequest("$base_url/online-fees-report/filter", [], $headers);
printResult("Online Fees Report - Filter (Empty Request)", $result);

// Test 3.3: Filter with search_type
$result = makeRequest("$base_url/online-fees-report/filter", ['search_type' => 'this_month'], $headers);
printResult("Online Fees Report - Filter (This Month)", $result);

// Test 3.4: Filter with custom dates
$result = makeRequest("$base_url/online-fees-report/filter", [
    'date_from' => '2025-01-01',
    'date_to' => '2025-12-31'
], $headers);
printResult("Online Fees Report - Filter (Custom Dates)", $result);

echo "\n\n{$BLUE}========================================{$RESET}\n";
echo "{$BLUE}  Testing Complete{$RESET}\n";
echo "{$BLUE}========================================{$RESET}\n\n";
?>

