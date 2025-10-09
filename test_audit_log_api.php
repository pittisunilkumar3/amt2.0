<?php
/**
 * Test Script for Audit Log API
 * 
 * Tests the audit log API to ensure it works correctly
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_audit_log_api.php
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
echo "║          AUDIT LOG API - COMPREHENSIVE TEST               ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

$total_tests = 0;
$passed_tests = 0;

// ============================================================================
// TEST SUITE 1: LIST ENDPOINT
// ============================================================================

echo "========================================\n";
echo "TEST SUITE 1: LIST ENDPOINT\n";
echo "========================================\n\n";

// Test 1.1: List endpoint
echo "Test 1.1: List Endpoint - Get Filter Options\n";
$result = call_api("{$base_url}/audit-log/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
    
    $data = $result['response']['data'];
    echo "  Actions Available: " . count($data['actions']) . "\n";
    echo "  Platforms Available: " . count($data['platforms']) . "\n";
    echo "  Users Available: " . count($data['users']) . "\n";
    
    // Show sample data
    if (count($data['actions']) > 0) {
        echo "\n  Sample Actions:\n";
        for ($i = 0; $i < min(5, count($data['actions'])); $i++) {
            echo "    - {$data['actions'][$i]}\n";
        }
    }
    
    if (count($data['platforms']) > 0) {
        echo "\n  Sample Platforms:\n";
        for ($i = 0; $i < min(3, count($data['platforms'])); $i++) {
            echo "    - {$data['platforms'][$i]}\n";
        }
    }
    
    if (count($data['users']) > 0) {
        echo "\n  Sample User:\n";
        $sample = $data['users'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Name: {$sample['staff_name']}\n";
    }
} else {
    print_test_result("List endpoint works", false, "HTTP {$result['http_code']}");
}
echo "\n";

// ============================================================================
// TEST SUITE 2: FILTER ENDPOINT
// ============================================================================

echo "========================================\n";
echo "TEST SUITE 2: FILTER ENDPOINT\n";
echo "========================================\n\n";

// Test 2.1: Empty request (recent logs with default limit)
echo "Test 2.1: Empty Request (Recent Audit Logs)\n";
$result = call_api("{$base_url}/audit-log/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Logs: {$summary['total_logs']}\n";
    echo "  Total Actions: {$summary['total_actions']}\n";
    echo "  Total Users: {$summary['total_users']}\n";
    
    if (isset($summary['action_distribution']) && count($summary['action_distribution']) > 0) {
        echo "\n  Action Distribution:\n";
        $count = 0;
        foreach ($summary['action_distribution'] as $action => $num) {
            echo "    {$action}: {$num} logs\n";
            if (++$count >= 5) break;
        }
    }
    
    // Show sample log
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        echo "\n  Sample Audit Log:\n";
        $sample = $result['response']['data'][0];
        echo "    User: {$sample['staff_name']}\n";
        echo "    Action: {$sample['action']}\n";
        echo "    Message: " . substr($sample['message'], 0, 50) . "...\n";
        echo "    IP Address: {$sample['ip_address']}\n";
        echo "    Platform: {$sample['platform']}\n";
        echo "    Time: {$sample['formatted_time']}\n";
    }
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 2.2: Filter by action (if actions exist)
echo "Test 2.2: Filter by Action\n";
$list_result = call_api("{$base_url}/audit-log/list", array(), $headers);
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['actions']) > 0) {
    $action = $list_result['response']['data']['actions'][0];
    $result = call_api("{$base_url}/audit-log/filter", array('action' => $action), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by action works", true);
        echo "  Action: {$action}\n";
        echo "  Logs Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by action works", false);
    }
} else {
    echo "⚠️  SKIP - No actions available for testing\n";
}
echo "\n";

// Test 2.3: Filter by platform (if platforms exist)
echo "Test 2.3: Filter by Platform\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['platforms']) > 0) {
    $platform = $list_result['response']['data']['platforms'][0];
    $result = call_api("{$base_url}/audit-log/filter", array('platform' => $platform), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by platform works", true);
        echo "  Platform: {$platform}\n";
        echo "  Logs Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by platform works", false);
    }
} else {
    echo "⚠️  SKIP - No platforms available for testing\n";
}
echo "\n";

// Test 2.4: Filter by user (if users exist)
echo "Test 2.4: Filter by User\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['users']) > 0) {
    $user_id = $list_result['response']['data']['users'][0]['id'];
    $result = call_api("{$base_url}/audit-log/filter", array('user_id' => $user_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by user works", true);
        echo "  User ID: {$user_id}\n";
        echo "  Logs Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by user works", false);
    }
} else {
    echo "⚠️  SKIP - No users available for testing\n";
}
echo "\n";

// Test 2.5: Filter by date range
echo "Test 2.5: Filter by Date Range\n";
$from_date = date('Y-m-d', strtotime('-7 days'));
$to_date = date('Y-m-d');
$result = call_api("{$base_url}/audit-log/filter", array(
    'from_date' => $from_date,
    'to_date' => $to_date
), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("Filter by date range works", true);
    echo "  From: {$from_date}\n";
    echo "  To: {$to_date}\n";
    echo "  Logs Found: {$result['response']['total_records']}\n";
} else {
    print_test_result("Filter by date range works", false);
}
echo "\n";

// Test 2.6: Filter with custom limit
echo "Test 2.6: Filter with Custom Limit\n";
$result = call_api("{$base_url}/audit-log/filter", array('limit' => 10), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $actual_count = count($result['response']['data']);
    print_test_result("Filter with custom limit works", true);
    echo "  Requested Limit: 10\n";
    echo "  Records Returned: {$actual_count}\n";
} else {
    print_test_result("Filter with custom limit works", false);
}
echo "\n";

// ============================================================================
// TEST SUITE 3: ERROR HANDLING
// ============================================================================

echo "========================================\n";
echo "TEST SUITE 3: ERROR HANDLING\n";
echo "========================================\n\n";

// Test 3.1: Unauthorized access
echo "Test 3.1: Unauthorized Access\n";
$bad_headers = array(
    'Content-Type: application/json',
    'Client-Service: wrong',
    'Auth-Key: wrong'
);
$result = call_api("{$base_url}/audit-log/filter", array(), $bad_headers);
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
    echo "The Audit Log API is working correctly!\n";
} else {
    echo "⚠️  SOME TESTS FAILED ⚠️\n";
    echo "\n";
    echo "Please review the failed tests above and check:\n";
    echo "  1. MySQL is running in XAMPP\n";
    echo "  2. Database has audit log data\n";
    echo "  3. API routes are configured correctly\n";
}

echo "\n";
echo "Next Steps:\n";
echo "  1. Test in Postman with your actual data\n";
echo "  2. Compare API responses with web page results\n";
echo "  3. Verify all data fields are correct\n";
echo "  4. Test with different filter combinations\n";
echo "\n";
?>

