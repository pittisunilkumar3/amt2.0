<?php
/**
 * Test Script for User Log API
 * 
 * Tests the user log API to ensure it works correctly
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_user_log_api.php
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
echo "║          USER LOG API - COMPREHENSIVE TEST                ║\n";
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
$result = call_api("{$base_url}/user-log/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
    
    $data = $result['response']['data'];
    echo "  Roles Available: " . count($data['roles']) . "\n";
    echo "  Classes Available: " . count($data['classes']) . "\n";
    
    // Show sample data
    if (count($data['roles']) > 0) {
        echo "\n  Available Roles:\n";
        foreach ($data['roles'] as $role) {
            echo "    - {$role}\n";
        }
    }
    
    if (count($data['classes']) > 0) {
        echo "\n  Sample Classes:\n";
        for ($i = 0; $i < min(3, count($data['classes'])); $i++) {
            $class = $data['classes'][$i];
            echo "    ID: {$class['id']}, Name: {$class['class']}\n";
        }
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
echo "Test 2.1: Empty Request (Recent User Logs)\n";
$result = call_api("{$base_url}/user-log/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Logs: {$summary['total_logs']}\n";
    echo "  Total Roles: {$summary['total_roles']}\n";
    echo "  Total Users: {$summary['total_users']}\n";
    
    if (isset($summary['role_distribution']) && count($summary['role_distribution']) > 0) {
        echo "\n  Role Distribution:\n";
        foreach ($summary['role_distribution'] as $role => $num) {
            echo "    {$role}: {$num} logins\n";
        }
    }
    
    // Show sample log
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        echo "\n  Sample User Log:\n";
        $sample = $result['response']['data'][0];
        echo "    User: {$sample['user']}\n";
        echo "    Role: {$sample['role_formatted']}\n";
        echo "    Class/Section: {$sample['class_section']}\n";
        echo "    IP Address: {$sample['ipaddress']}\n";
        echo "    Login Time: {$sample['formatted_datetime']}\n";
        echo "    User Agent: " . substr($sample['user_agent'], 0, 50) . "...\n";
    }
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 2.2: Filter by role (if roles exist)
echo "Test 2.2: Filter by Role\n";
$list_result = call_api("{$base_url}/user-log/list", array(), $headers);
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['roles']) > 0) {
    $role = $list_result['response']['data']['roles'][0];
    $result = call_api("{$base_url}/user-log/filter", array('role' => $role), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by role works", true);
        echo "  Role: {$role}\n";
        echo "  Logs Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by role works", false);
    }
} else {
    echo "⚠️  SKIP - No roles available for testing\n";
}
echo "\n";

// Test 2.3: Filter by student role
echo "Test 2.3: Filter by Student Role\n";
$result = call_api("{$base_url}/user-log/filter", array('role' => 'student'), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("Filter by student role works", true);
    echo "  Role: student\n";
    echo "  Logs Found: {$result['response']['total_records']}\n";
    
    // Show sample student log
    if (count($result['response']['data']) > 0) {
        $sample = $result['response']['data'][0];
        echo "\n  Sample Student Login:\n";
        echo "    User: {$sample['user']}\n";
        echo "    Class/Section: {$sample['class_section']}\n";
        echo "    Login Time: {$sample['formatted_datetime']}\n";
    }
} else {
    print_test_result("Filter by student role works", false);
}
echo "\n";

// Test 2.4: Filter by class (if classes exist)
echo "Test 2.4: Filter by Class\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['classes']) > 0) {
    $class_id = $list_result['response']['data']['classes'][0]['id'];
    $result = call_api("{$base_url}/user-log/filter", array('class_id' => $class_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by class works", true);
        echo "  Class ID: {$class_id}\n";
        echo "  Logs Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by class works", false);
    }
} else {
    echo "⚠️  SKIP - No classes available for testing\n";
}
echo "\n";

// Test 2.5: Filter by date range
echo "Test 2.5: Filter by Date Range\n";
$from_date = date('Y-m-d', strtotime('-7 days'));
$to_date = date('Y-m-d');
$result = call_api("{$base_url}/user-log/filter", array(
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
$result = call_api("{$base_url}/user-log/filter", array('limit' => 10), $headers);
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

// Test 2.7: Combined filters (role + date range)
echo "Test 2.7: Combined Filters (Role + Date Range)\n";
$result = call_api("{$base_url}/user-log/filter", array(
    'role' => 'student',
    'from_date' => date('Y-m-d', strtotime('-30 days')),
    'to_date' => date('Y-m-d')
), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("Combined filters work", true);
    echo "  Logs Found: {$result['response']['total_records']}\n";
} else {
    print_test_result("Combined filters work", false);
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
$result = call_api("{$base_url}/user-log/filter", array(), $bad_headers);
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
    echo "The User Log API is working correctly!\n";
} else {
    echo "⚠️  SOME TESTS FAILED ⚠️\n";
    echo "\n";
    echo "Please review the failed tests above and check:\n";
    echo "  1. MySQL is running in XAMPP\n";
    echo "  2. Database has user log data\n";
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

