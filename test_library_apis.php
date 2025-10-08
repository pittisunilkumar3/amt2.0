<?php
/**
 * Test script for Library/Book Report APIs
 * Tests all four library-related APIs
 */

// API Configuration
$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Function to make API request
function makeRequest($url, $data = []) {
    global $headers;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

echo "==============================================\n";
echo "LIBRARY/BOOK REPORT APIs TESTING\n";
echo "==============================================\n\n";

// Test 1: Issue Return Report API - Empty Request
echo "Test 1: Issue Return Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/issue-return-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    $firstRecord = $result['response']['data'][0];
    echo "First Record ID: " . $firstRecord['id'] . "\n";
    echo "Book Title: " . $firstRecord['book_title'] . "\n";
}
echo "\n";

// Test 2: Issue Return Report API - List
echo "Test 2: Issue Return Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/issue-return-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Search Types: " . count($result['response']['search_types'] ?? []) . "\n";
echo "\n";

// Test 3: Student Book Issue Report API - Empty Request
echo "Test 3: Student Book Issue Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/student-book-issue-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    $firstRecord = $result['response']['data'][0];
    echo "First Record ID: " . $firstRecord['id'] . "\n";
    echo "Book Title: " . $firstRecord['book_title'] . "\n";
    echo "Member Type: " . $firstRecord['member_type'] . "\n";
}
echo "\n";

// Test 4: Student Book Issue Report API - List
echo "Test 4: Student Book Issue Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/student-book-issue-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Search Types: " . count($result['response']['search_types'] ?? []) . "\n";
echo "Member Types: " . count($result['response']['member_types'] ?? []) . "\n";
echo "\n";

// Test 5: Book Due Report API - Empty Request
echo "Test 5: Book Due Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/book-due-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    $firstRecord = $result['response']['data'][0];
    echo "First Record ID: " . $firstRecord['id'] . "\n";
    echo "Book Title: " . $firstRecord['book_title'] . "\n";
    echo "Due Return Date: " . $firstRecord['duereturn_date'] . "\n";
}
echo "\n";

// Test 6: Book Due Report API - List
echo "Test 6: Book Due Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/book-due-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Search Types: " . count($result['response']['search_types'] ?? []) . "\n";
echo "Member Types: " . count($result['response']['member_types'] ?? []) . "\n";
echo "\n";

// Test 7: Book Inventory Report API - Empty Request
echo "Test 7: Book Inventory Report API - Empty Request\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/book-inventory-report/filter', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
    $firstRecord = $result['response']['data'][0];
    echo "First Record ID: " . $firstRecord['id'] . "\n";
    echo "Book Title: " . $firstRecord['book_title'] . "\n";
    echo "Total Quantity: " . $firstRecord['qty'] . "\n";
    echo "Available Quantity: " . $firstRecord['available_qty'] . "\n";
}
echo "\n";

// Test 8: Book Inventory Report API - List
echo "Test 8: Book Inventory Report API - List\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/book-inventory-report/list', []);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Search Types: " . count($result['response']['search_types'] ?? []) . "\n";
echo "\n";

// Test 9: Issue Return Report API - Filter by Search Type
echo "Test 9: Issue Return Report API - Filter by Search Type (this_month)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/issue-return-report/filter', ['search_type' => 'this_month']);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
echo "Search Type Applied: " . ($result['response']['filters_applied']['search_type'] ?? 'N/A') . "\n";
echo "\n";

// Test 10: Student Book Issue Report API - Filter by Member Type
echo "Test 10: Student Book Issue Report API - Filter by Member Type (student)\n";
echo "--------------------------------------------\n";
$result = makeRequest($base_url . '/student-book-issue-report/filter', ['member_type' => 'student']);
echo "HTTP Code: " . $result['http_code'] . "\n";
echo "Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Total Records: " . ($result['response']['total_records'] ?? 0) . "\n";
echo "Member Type Applied: " . ($result['response']['filters_applied']['member_type'] ?? 'N/A') . "\n";
echo "\n";

echo "==============================================\n";
echo "TESTING COMPLETE\n";
echo "==============================================\n";
echo "\nAll four Library/Book Report APIs have been tested.\n";
echo "Check the results above to verify functionality.\n";
?>

