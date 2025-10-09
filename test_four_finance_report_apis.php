<?php
/**
 * Test Script for Four Finance Report APIs
 * 
 * Tests all four finance report APIs:
 * 1. Other Collection Report API
 * 2. Combined Collection Report API
 * 3. Total Fee Collection Report API
 * 4. Fee Collection Columnwise Report API
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_four_finance_report_apis.php
 */

// Configuration
$base_url = 'http://localhost/amt/api';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

// Test counter
$test_number = 0;
$passed = 0;
$failed = 0;

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

// Helper function to run a test
function run_test($test_name, $url, $data, $headers, &$test_number, &$passed, &$failed) {
    $test_number++;
    echo "\n";
    echo "========================================\n";
    echo "Test #{$test_number}: {$test_name}\n";
    echo "========================================\n";
    echo "URL: {$url}\n";
    echo "Request: " . json_encode($data) . "\n";
    
    $result = call_api($url, $data, $headers);
    
    echo "HTTP Code: {$result['http_code']}\n";
    
    if ($result['http_code'] == 200 && isset($result['response']['status'])) {
        if ($result['response']['status'] == 1) {
            echo "✅ PASSED\n";
            echo "Message: {$result['response']['message']}\n";
            if (isset($result['response']['total_records'])) {
                echo "Total Records: {$result['response']['total_records']}\n";
            }
            if (isset($result['response']['summary'])) {
                echo "Summary: " . json_encode($result['response']['summary']) . "\n";
            }
            $passed++;
        } else {
            echo "❌ FAILED - API returned status 0\n";
            echo "Message: {$result['response']['message']}\n";
            $failed++;
        }
    } else {
        echo "❌ FAILED - Invalid response\n";
        echo "Response: " . json_encode($result['response']) . "\n";
        $failed++;
    }
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   FOUR FINANCE REPORT APIs - COMPREHENSIVE TEST SUITE     ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";

// ============================================
// 1. OTHER COLLECTION REPORT API TESTS
// ============================================
echo "\n";
echo "┌────────────────────────────────────────────────────────────┐\n";
echo "│  1. OTHER COLLECTION REPORT API TESTS                     │\n";
echo "└────────────────────────────────────────────────────────────┘\n";

run_test(
    "Other Collection - List Endpoint",
    "{$base_url}/other-collection-report/list",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Other Collection - Empty Request (All Records)",
    "{$base_url}/other-collection-report/filter",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Other Collection - This Month",
    "{$base_url}/other-collection-report/filter",
    array('search_type' => 'this_month'),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Other Collection - With Grouping",
    "{$base_url}/other-collection-report/filter",
    array('search_type' => 'this_year', 'group' => 'class'),
    $headers,
    $test_number,
    $passed,
    $failed
);

// ============================================
// 2. COMBINED COLLECTION REPORT API TESTS
// ============================================
echo "\n";
echo "┌────────────────────────────────────────────────────────────┐\n";
echo "│  2. COMBINED COLLECTION REPORT API TESTS                  │\n";
echo "└────────────────────────────────────────────────────────────┘\n";

run_test(
    "Combined Collection - List Endpoint",
    "{$base_url}/combined-collection-report/list",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Combined Collection - Empty Request (All Records)",
    "{$base_url}/combined-collection-report/filter",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Combined Collection - This Month",
    "{$base_url}/combined-collection-report/filter",
    array('search_type' => 'this_month'),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Combined Collection - With Grouping",
    "{$base_url}/combined-collection-report/filter",
    array('search_type' => 'this_year', 'group' => 'collection'),
    $headers,
    $test_number,
    $passed,
    $failed
);

// ============================================
// 3. TOTAL FEE COLLECTION REPORT API TESTS
// ============================================
echo "\n";
echo "┌────────────────────────────────────────────────────────────┐\n";
echo "│  3. TOTAL FEE COLLECTION REPORT API TESTS                 │\n";
echo "└────────────────────────────────────────────────────────────┘\n";

run_test(
    "Total Fee Collection - List Endpoint",
    "{$base_url}/total-fee-collection-report/list",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Total Fee Collection - Empty Request (All Records)",
    "{$base_url}/total-fee-collection-report/filter",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Total Fee Collection - This Month",
    "{$base_url}/total-fee-collection-report/filter",
    array('search_type' => 'this_month'),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Total Fee Collection - With Grouping",
    "{$base_url}/total-fee-collection-report/filter",
    array('search_type' => 'this_year', 'group' => 'mode'),
    $headers,
    $test_number,
    $passed,
    $failed
);

// ============================================
// 4. FEE COLLECTION COLUMNWISE REPORT API TESTS
// ============================================
echo "\n";
echo "┌────────────────────────────────────────────────────────────┐\n";
echo "│  4. FEE COLLECTION COLUMNWISE REPORT API TESTS            │\n";
echo "└────────────────────────────────────────────────────────────┘\n";

run_test(
    "Fee Collection Columnwise - List Endpoint",
    "{$base_url}/fee-collection-columnwise-report/list",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Fee Collection Columnwise - Empty Request (All Records)",
    "{$base_url}/fee-collection-columnwise-report/filter",
    array(),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Fee Collection Columnwise - This Month",
    "{$base_url}/fee-collection-columnwise-report/filter",
    array('search_type' => 'this_month'),
    $headers,
    $test_number,
    $passed,
    $failed
);

run_test(
    "Fee Collection Columnwise - Filter by Class",
    "{$base_url}/fee-collection-columnwise-report/filter",
    array('search_type' => 'this_year', 'class_id' => 1),
    $headers,
    $test_number,
    $passed,
    $failed
);

// ============================================
// FINAL SUMMARY
// ============================================
echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST SUMMARY                          ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Total Tests: {$test_number}\n";
echo "✅ Passed: {$passed}\n";
echo "❌ Failed: {$failed}\n";
echo "\n";

if ($failed == 0) {
    echo "🎉 ALL TESTS PASSED! 🎉\n";
    echo "\n";
    echo "All four finance report APIs are working correctly!\n";
    echo "\n";
    echo "APIs Tested:\n";
    echo "  1. ✅ Other Collection Report API\n";
    echo "  2. ✅ Combined Collection Report API\n";
    echo "  3. ✅ Total Fee Collection Report API\n";
    echo "  4. ✅ Fee Collection Columnwise Report API\n";
} else {
    echo "⚠️  SOME TESTS FAILED ⚠️\n";
    echo "\n";
    echo "Please check the failed tests above and fix the issues.\n";
}

echo "\n";
echo "========================================\n";
echo "Test completed at: " . date('Y-m-d H:i:s') . "\n";
echo "========================================\n";
echo "\n";
?>

