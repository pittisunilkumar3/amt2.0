<?php
/**
 * Test Script for Three Inventory Report APIs
 * 
 * Tests all three inventory report APIs to ensure they work correctly
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_three_inventory_apis.php
 */

// Configuration
$base_url = 'http://localhost/amt/api';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

// Helper function to make API calls
function call_api($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return array(
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    );
}

// Helper function to print test result
function print_test_result($test_name, $passed, $message = '') {
    $status = $passed ? '✅ PASS' : '❌ FAIL';
    echo "{$status} - {$test_name}";
    if ($message) {
        echo " ({$message})";
    }
    echo "\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║     THREE INVENTORY REPORT APIS - COMPREHENSIVE TEST      ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$total_tests = 0;
$passed_tests = 0;

// ============================================================================
// TEST SUITE 1: INVENTORY STOCK REPORT API
// ============================================================================

echo "========================================\n";
echo "TEST SUITE 1: INVENTORY STOCK REPORT API\n";
echo "========================================\n\n";

// Test 1.1: List endpoint
echo "Test 1.1: List Endpoint\n";
$result = call_api("{$base_url}/inventory-stock-report/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
} else {
    print_test_result("List endpoint works", false, "HTTP {$result['http_code']}");
}
echo "\n";

// Test 1.2: Empty request
echo "Test 1.2: Empty Request (All Records)\n";
$result = call_api("{$base_url}/inventory-stock-report/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Items: {$summary['total_items']}\n";
    echo "  Total Stock: {$summary['total_stock_quantity']}\n";
    echo "  Available: {$summary['total_available_quantity']}\n";
    echo "  Issued: {$summary['total_issued_quantity']}\n";
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 1.3: This month filter
echo "Test 1.3: This Month Filter\n";
$result = call_api("{$base_url}/inventory-stock-report/filter", array('search_type' => 'this_month'), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("This month filter works", true);
    echo "  Records: {$result['response']['total_records']}\n";
} else {
    print_test_result("This month filter works", false);
}
echo "\n";

// ============================================================================
// TEST SUITE 2: ADD ITEM REPORT API
// ============================================================================

echo "========================================\n";
echo "TEST SUITE 2: ADD ITEM REPORT API\n";
echo "========================================\n\n";

// Test 2.1: List endpoint
echo "Test 2.1: List Endpoint\n";
$result = call_api("{$base_url}/add-item-report/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
} else {
    print_test_result("List endpoint works", false, "HTTP {$result['http_code']}");
}
echo "\n";

// Test 2.2: Empty request
echo "Test 2.2: Empty Request (All Records)\n";
$result = call_api("{$base_url}/add-item-report/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Items: {$summary['total_items']}\n";
    echo "  Total Quantity: {$summary['total_quantity']}\n";
    echo "  Total Purchase Price: {$summary['total_purchase_price']}\n";
    
    // Show sample data
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        echo "\n  Sample Item:\n";
        $sample = $result['response']['data'][0];
        echo "    Name: {$sample['name']}\n";
        echo "    Category: {$sample['item_category']}\n";
        echo "    Quantity: {$sample['quantity']}\n";
        echo "    Price: {$sample['purchase_price']}\n";
        echo "    Date: {$sample['date']}\n";
    }
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 2.3: This week filter
echo "Test 2.3: This Week Filter\n";
$result = call_api("{$base_url}/add-item-report/filter", array('search_type' => 'this_week'), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("This week filter works", true);
    echo "  Records: {$result['response']['total_records']}\n";
} else {
    print_test_result("This week filter works", false);
}
echo "\n";

// Test 2.4: Custom date range
echo "Test 2.4: Custom Date Range\n";
$result = call_api("{$base_url}/add-item-report/filter", array(
    'search_type' => 'period',
    'date_from' => '2025-01-01',
    'date_to' => '2025-12-31'
), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("Custom date range works", true);
    echo "  Records: {$result['response']['total_records']}\n";
} else {
    print_test_result("Custom date range works", false);
}
echo "\n";

// ============================================================================
// TEST SUITE 3: ISSUE INVENTORY REPORT API
// ============================================================================

echo "========================================\n";
echo "TEST SUITE 3: ISSUE INVENTORY REPORT API\n";
echo "========================================\n\n";

// Test 3.1: List endpoint
echo "Test 3.1: List Endpoint\n";
$result = call_api("{$base_url}/issue-inventory-report/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
} else {
    print_test_result("List endpoint works", false, "HTTP {$result['http_code']}");
}
echo "\n";

// Test 3.2: Empty request
echo "Test 3.2: Empty Request (All Records)\n";
$result = call_api("{$base_url}/issue-inventory-report/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Issues: {$summary['total_issues']}\n";
    echo "  Total Quantity: {$summary['total_quantity']}\n";
    echo "  Returned: {$summary['total_returned']}\n";
    echo "  Not Returned: {$summary['total_not_returned']}\n";
    
    // Show sample data
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        echo "\n  Sample Issue:\n";
        $sample = $result['response']['data'][0];
        echo "    Item: {$sample['item_name']}\n";
        echo "    Category: {$sample['item_category']}\n";
        echo "    Quantity: {$sample['quantity']}\n";
        echo "    Issue Date: {$sample['issue_date']}\n";
        echo "    Issued To: {$sample['issue_to_info']['name']}\n";
        echo "    Issued By: {$sample['issued_by_info']['name']}\n";
        echo "    Status: {$sample['return_status']}\n";
    }
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 3.3: Today filter
echo "Test 3.3: Today Filter\n";
$result = call_api("{$base_url}/issue-inventory-report/filter", array('search_type' => 'today'), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("Today filter works", true);
    echo "  Records: {$result['response']['total_records']}\n";
} else {
    print_test_result("Today filter works", false);
}
echo "\n";

// ============================================================================
// TEST SUITE 4: ERROR HANDLING
// ============================================================================

echo "========================================\n";
echo "TEST SUITE 4: ERROR HANDLING\n";
echo "========================================\n\n";

// Test 4.1: Unauthorized access
echo "Test 4.1: Unauthorized Access\n";
$bad_headers = array(
    'Content-Type: application/json',
    'Client-Service: wrong',
    'Auth-Key: wrong'
);
$result = call_api("{$base_url}/inventory-stock-report/filter", array(), $bad_headers);
$total_tests++;
if ($result['http_code'] == 401) {
    $passed_tests++;
    print_test_result("Unauthorized access blocked", true);
} else {
    print_test_result("Unauthorized access blocked", false, "Expected 401, got {$result['http_code']}");
}
echo "\n";

// ============================================================================
// FINAL SUMMARY
// ============================================================================

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Total Tests: {$total_tests}\n";
echo "Passed: {$passed_tests}\n";
echo "Failed: " . ($total_tests - $passed_tests) . "\n";
echo "Success Rate: " . round(($passed_tests / $total_tests) * 100, 2) . "%\n";
echo "\n";

if ($passed_tests == $total_tests) {
    echo "🎉 ALL TESTS PASSED! 🎉\n";
    echo "\n";
    echo "All three inventory report APIs are working correctly!\n";
} else {
    echo "⚠️  SOME TESTS FAILED ⚠️\n";
    echo "\n";
    echo "Please review the failed tests above and check:\n";
    echo "  1. MySQL is running in XAMPP\n";
    echo "  2. Database has inventory data\n";
    echo "  3. API routes are configured correctly\n";
}

echo "\n";
echo "Next Steps:\n";
echo "  1. Test in Postman with your actual data\n";
echo "  2. Compare API responses with web page results\n";
echo "  3. Verify all data fields are correct\n";
echo "\n";
?>

