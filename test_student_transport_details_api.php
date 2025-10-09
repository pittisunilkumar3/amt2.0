<?php
/**
 * Test Script for Student Transport Details API
 * 
 * Tests the student transport details API to ensure it works correctly
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_student_transport_details_api.php
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
echo "║     STUDENT TRANSPORT DETAILS API - COMPREHENSIVE TEST    ║\n";
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
$result = call_api("{$base_url}/student-transport-details/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
    
    $data = $result['response']['data'];
    echo "  Classes Available: " . count($data['classes']) . "\n";
    echo "  Routes Available: " . count($data['routes']) . "\n";
    echo "  Vehicles Available: " . count($data['vehicles']) . "\n";
    
    // Show sample data
    if (count($data['classes']) > 0) {
        echo "\n  Sample Class:\n";
        $sample = $data['classes'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Name: {$sample['class']}\n";
    }
    
    if (count($data['routes']) > 0) {
        echo "\n  Sample Route:\n";
        $sample = $data['routes'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Title: {$sample['route_title']}\n";
    }
    
    if (count($data['vehicles']) > 0) {
        echo "\n  Sample Vehicle:\n";
        $sample = $data['vehicles'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Number: {$sample['vehicle_no']}\n";
        echo "    Model: {$sample['vehicle_model']}\n";
        echo "    Driver: {$sample['driver_name']}\n";
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

// Test 2.1: Empty request (all students)
echo "Test 2.1: Empty Request (All Transport Students)\n";
$result = call_api("{$base_url}/student-transport-details/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Students: {$summary['total_students']}\n";
    echo "  Total Routes: {$summary['total_routes']}\n";
    echo "  Total Vehicles: {$summary['total_vehicles']}\n";
    echo "  Total Transport Fees: {$summary['total_transport_fees']}\n";
    
    // Show sample student data
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        echo "\n  Sample Student:\n";
        $sample = $result['response']['data'][0];
        echo "    Name: {$sample['student_name']}\n";
        echo "    Admission No: {$sample['admission_no']}\n";
        echo "    Class: {$sample['class_section']}\n";
        echo "    Route: {$sample['route_title']}\n";
        echo "    Vehicle: {$sample['vehicle_no']}\n";
        echo "    Pickup Point: {$sample['pickup_name']}\n";
        echo "    Pickup Time: {$sample['pickup_time']}\n";
        echo "    Driver: {$sample['driver_name']}\n";
        echo "    Fees: {$sample['fees']}\n";
    }
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 2.2: Filter by class (if classes exist)
echo "Test 2.2: Filter by Class\n";
$list_result = call_api("{$base_url}/student-transport-details/list", array(), $headers);
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['classes']) > 0) {
    $class_id = $list_result['response']['data']['classes'][0]['id'];
    $result = call_api("{$base_url}/student-transport-details/filter", array('class_id' => $class_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by class works", true);
        echo "  Class ID: {$class_id}\n";
        echo "  Students Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by class works", false);
    }
} else {
    echo "⚠️  SKIP - No classes available for testing\n";
}
echo "\n";

// Test 2.3: Filter by route (if routes exist)
echo "Test 2.3: Filter by Route\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['routes']) > 0) {
    $route_id = $list_result['response']['data']['routes'][0]['id'];
    $result = call_api("{$base_url}/student-transport-details/filter", array('transport_route_id' => $route_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by route works", true);
        echo "  Route ID: {$route_id}\n";
        echo "  Students Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by route works", false);
    }
} else {
    echo "⚠️  SKIP - No routes available for testing\n";
}
echo "\n";

// Test 2.4: Filter by vehicle (if vehicles exist)
echo "Test 2.4: Filter by Vehicle\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['vehicles']) > 0) {
    $vehicle_id = $list_result['response']['data']['vehicles'][0]['id'];
    $result = call_api("{$base_url}/student-transport-details/filter", array('vehicle_id' => $vehicle_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by vehicle works", true);
        echo "  Vehicle ID: {$vehicle_id}\n";
        echo "  Students Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by vehicle works", false);
    }
} else {
    echo "⚠️  SKIP - No vehicles available for testing\n";
}
echo "\n";

// Test 2.5: Multiple filters
echo "Test 2.5: Multiple Filters (Class + Route)\n";
if ($list_result['response']['status'] == 1 && 
    count($list_result['response']['data']['classes']) > 0 && 
    count($list_result['response']['data']['routes']) > 0) {
    $class_id = $list_result['response']['data']['classes'][0]['id'];
    $route_id = $list_result['response']['data']['routes'][0]['id'];
    $result = call_api("{$base_url}/student-transport-details/filter", array(
        'class_id' => $class_id,
        'transport_route_id' => $route_id
    ), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Multiple filters work", true);
        echo "  Class ID: {$class_id}\n";
        echo "  Route ID: {$route_id}\n";
        echo "  Students Found: {$result['response']['total_records']}\n";
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
$result = call_api("{$base_url}/student-transport-details/filter", array(), $bad_headers);
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
    echo "The Student Transport Details API is working correctly!\n";
} else {
    echo "⚠️  SOME TESTS FAILED ⚠️\n";
    echo "\n";
    echo "Please review the failed tests above and check:\n";
    echo "  1. MySQL is running in XAMPP\n";
    echo "  2. Database has transport data (students, routes, vehicles)\n";
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

