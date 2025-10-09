<?php
/**
 * Test Script for Alumni Report API
 * 
 * Tests the alumni report API to ensure it works correctly
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_alumni_report_api.php
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
echo "║         ALUMNI REPORT API - COMPREHENSIVE TEST            ║\n";
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
$result = call_api("{$base_url}/alumni-report/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
    
    $data = $result['response']['data'];
    echo "  Classes Available: " . count($data['classes']) . "\n";
    echo "  Sessions Available: " . count($data['sessions']) . "\n";
    echo "  Categories Available: " . count($data['categories']) . "\n";
    
    // Show sample data
    if (count($data['classes']) > 0) {
        echo "\n  Sample Class:\n";
        $sample = $data['classes'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Name: {$sample['class']}\n";
    }
    
    if (count($data['sessions']) > 0) {
        echo "\n  Sample Session:\n";
        $sample = $data['sessions'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Session: {$sample['session']}\n";
    }
    
    if (count($data['categories']) > 0) {
        echo "\n  Sample Category:\n";
        $sample = $data['categories'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Category: {$sample['category']}\n";
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

// Test 2.1: Empty request (all alumni)
echo "Test 2.1: Empty Request (All Alumni Students)\n";
$result = call_api("{$base_url}/alumni-report/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Alumni: {$summary['total_alumni']}\n";
    echo "  Total Classes: {$summary['total_classes']}\n";
    echo "  Total Sessions: {$summary['total_sessions']}\n";
    
    if (isset($summary['session_distribution']) && count($summary['session_distribution']) > 0) {
        echo "\n  Session Distribution:\n";
        foreach ($summary['session_distribution'] as $session => $count) {
            echo "    {$session}: {$count} alumni\n";
        }
    }
    
    // Show sample alumni data
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        echo "\n  Sample Alumni Student:\n";
        $sample = $result['response']['data'][0];
        echo "    Name: {$sample['student_name']}\n";
        echo "    Admission No: {$sample['admission_no']}\n";
        echo "    Class: {$sample['class_section']}\n";
        echo "    Pass Out Year: {$sample['pass_out_year']}\n";
        if ($sample['current_email']) {
            echo "    Current Email: {$sample['current_email']}\n";
        }
        if ($sample['current_phone']) {
            echo "    Current Phone: {$sample['current_phone']}\n";
        }
        if ($sample['occupation']) {
            echo "    Occupation: {$sample['occupation']}\n";
        }
    }
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 2.2: Filter by class (if classes exist)
echo "Test 2.2: Filter by Class\n";
$list_result = call_api("{$base_url}/alumni-report/list", array(), $headers);
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['classes']) > 0) {
    $class_id = $list_result['response']['data']['classes'][0]['id'];
    $result = call_api("{$base_url}/alumni-report/filter", array('class_id' => $class_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by class works", true);
        echo "  Class ID: {$class_id}\n";
        echo "  Alumni Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by class works", false);
    }
} else {
    echo "⚠️  SKIP - No classes available for testing\n";
}
echo "\n";

// Test 2.3: Filter by session (if sessions exist)
echo "Test 2.3: Filter by Pass-Out Session\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['sessions']) > 0) {
    $session_id = $list_result['response']['data']['sessions'][0]['id'];
    $result = call_api("{$base_url}/alumni-report/filter", array('session_id' => $session_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by session works", true);
        echo "  Session ID: {$session_id}\n";
        echo "  Alumni Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by session works", false);
    }
} else {
    echo "⚠️  SKIP - No sessions available for testing\n";
}
echo "\n";

// Test 2.4: Filter by category (if categories exist)
echo "Test 2.4: Filter by Category\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['categories']) > 0) {
    $category_id = $list_result['response']['data']['categories'][0]['id'];
    $result = call_api("{$base_url}/alumni-report/filter", array('category_id' => $category_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by category works", true);
        echo "  Category ID: {$category_id}\n";
        echo "  Alumni Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by category works", false);
    }
} else {
    echo "⚠️  SKIP - No categories available for testing\n";
}
echo "\n";

// Test 2.5: Search by admission number
echo "Test 2.5: Search by Admission Number\n";
$result = call_api("{$base_url}/alumni-report/filter", array('admission_no' => 'ADM'), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("Search by admission number works", true);
    echo "  Search Term: ADM\n";
    echo "  Alumni Found: {$result['response']['total_records']}\n";
} else {
    print_test_result("Search by admission number works", false);
}
echo "\n";

// Test 2.6: Multiple filters
echo "Test 2.6: Multiple Filters (Class + Session)\n";
if ($list_result['response']['status'] == 1 && 
    count($list_result['response']['data']['classes']) > 0 && 
    count($list_result['response']['data']['sessions']) > 0) {
    $class_id = $list_result['response']['data']['classes'][0]['id'];
    $session_id = $list_result['response']['data']['sessions'][0]['id'];
    $result = call_api("{$base_url}/alumni-report/filter", array(
        'class_id' => $class_id,
        'session_id' => $session_id
    ), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Multiple filters work", true);
        echo "  Class ID: {$class_id}\n";
        echo "  Session ID: {$session_id}\n";
        echo "  Alumni Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Multiple filters work", false);
    }
} else {
    echo "⚠️  SKIP - Not enough data for testing multiple filters\n";
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
$result = call_api("{$base_url}/alumni-report/filter", array(), $bad_headers);
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
    echo "The Alumni Report API is working correctly!\n";
} else {
    echo "⚠️  SOME TESTS FAILED ⚠️\n";
    echo "\n";
    echo "Please review the failed tests above and check:\n";
    echo "  1. MySQL is running in XAMPP\n";
    echo "  2. Database has alumni data (students with is_alumni = 1)\n";
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

