<?php
/**
 * Test Script for Student Hostel Details API
 * 
 * Tests the student hostel details API to ensure it works correctly
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_student_hostel_details_api.php
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
echo "║      STUDENT HOSTEL DETAILS API - COMPREHENSIVE TEST      ║\n";
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
$result = call_api("{$base_url}/student-hostel-details/list", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    print_test_result("List endpoint works", true);
    
    $data = $result['response']['data'];
    echo "  Classes Available: " . count($data['classes']) . "\n";
    echo "  Hostels Available: " . count($data['hostels']) . "\n";
    echo "  Room Types Available: " . count($data['room_types']) . "\n";
    
    // Show sample data
    if (count($data['classes']) > 0) {
        echo "\n  Sample Class:\n";
        $sample = $data['classes'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Name: {$sample['class']}\n";
    }
    
    if (count($data['hostels']) > 0) {
        echo "\n  Sample Hostel:\n";
        $sample = $data['hostels'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Name: {$sample['hostel_name']}\n";
        echo "    Type: {$sample['type']}\n";
        echo "    Address: {$sample['address']}\n";
        echo "    Intake: {$sample['intake']}\n";
    }
    
    if (count($data['room_types']) > 0) {
        echo "\n  Sample Room Type:\n";
        $sample = $data['room_types'][0];
        echo "    ID: {$sample['id']}\n";
        echo "    Type: {$sample['room_type']}\n";
        echo "    Description: {$sample['description']}\n";
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
echo "Test 2.1: Empty Request (All Hostel Students)\n";
$result = call_api("{$base_url}/student-hostel-details/filter", array(), $headers);
$total_tests++;
if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
    $passed_tests++;
    $summary = $result['response']['summary'];
    print_test_result("Empty request works", true);
    echo "  Total Students: {$summary['total_students']}\n";
    echo "  Total Hostels: {$summary['total_hostels']}\n";
    echo "  Total Rooms: {$summary['total_rooms']}\n";
    echo "  Total Hostel Cost: {$summary['total_hostel_cost']}\n";
    
    // Show sample student data
    if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
        echo "\n  Sample Student:\n";
        $sample = $result['response']['data'][0];
        echo "    Name: {$sample['student_name']}\n";
        echo "    Admission No: {$sample['admission_no']}\n";
        echo "    Class: {$sample['class_section']}\n";
        echo "    Hostel: {$sample['hostel_name']}\n";
        echo "    Room No: {$sample['room_no']}\n";
        echo "    Room Type: {$sample['room_type']}\n";
        echo "    No of Beds: {$sample['no_of_bed']}\n";
        echo "    Cost per Bed: {$sample['cost_per_bed']}\n";
    }
} else {
    print_test_result("Empty request works", false);
}
echo "\n";

// Test 2.2: Filter by class (if classes exist)
echo "Test 2.2: Filter by Class\n";
$list_result = call_api("{$base_url}/student-hostel-details/list", array(), $headers);
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['classes']) > 0) {
    $class_id = $list_result['response']['data']['classes'][0]['id'];
    $result = call_api("{$base_url}/student-hostel-details/filter", array('class_id' => $class_id), $headers);
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

// Test 2.3: Filter by hostel (if hostels exist)
echo "Test 2.3: Filter by Hostel\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['hostels']) > 0) {
    $hostel_id = $list_result['response']['data']['hostels'][0]['id'];
    $result = call_api("{$base_url}/student-hostel-details/filter", array('hostel_id' => $hostel_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by hostel works", true);
        echo "  Hostel ID: {$hostel_id}\n";
        echo "  Students Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by hostel works", false);
    }
} else {
    echo "⚠️  SKIP - No hostels available for testing\n";
}
echo "\n";

// Test 2.4: Filter by hostel name (if hostels exist)
echo "Test 2.4: Filter by Hostel Name\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['hostels']) > 0) {
    $hostel_name = $list_result['response']['data']['hostels'][0]['hostel_name'];
    $result = call_api("{$base_url}/student-hostel-details/filter", array('hostel_name' => $hostel_name), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by hostel name works", true);
        echo "  Hostel Name: {$hostel_name}\n";
        echo "  Students Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by hostel name works", false);
    }
} else {
    echo "⚠️  SKIP - No hostels available for testing\n";
}
echo "\n";

// Test 2.5: Filter by room type (if room types exist)
echo "Test 2.5: Filter by Room Type\n";
if ($list_result['response']['status'] == 1 && count($list_result['response']['data']['room_types']) > 0) {
    $room_type_id = $list_result['response']['data']['room_types'][0]['id'];
    $result = call_api("{$base_url}/student-hostel-details/filter", array('room_type_id' => $room_type_id), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Filter by room type works", true);
        echo "  Room Type ID: {$room_type_id}\n";
        echo "  Students Found: {$result['response']['total_records']}\n";
    } else {
        print_test_result("Filter by room type works", false);
    }
} else {
    echo "⚠️  SKIP - No room types available for testing\n";
}
echo "\n";

// Test 2.6: Multiple filters
echo "Test 2.6: Multiple Filters (Class + Hostel)\n";
if ($list_result['response']['status'] == 1 && 
    count($list_result['response']['data']['classes']) > 0 && 
    count($list_result['response']['data']['hostels']) > 0) {
    $class_id = $list_result['response']['data']['classes'][0]['id'];
    $hostel_id = $list_result['response']['data']['hostels'][0]['id'];
    $result = call_api("{$base_url}/student-hostel-details/filter", array(
        'class_id' => $class_id,
        'hostel_id' => $hostel_id
    ), $headers);
    $total_tests++;
    if ($result['http_code'] == 200 && $result['response']['status'] == 1) {
        $passed_tests++;
        print_test_result("Multiple filters work", true);
        echo "  Class ID: {$class_id}\n";
        echo "  Hostel ID: {$hostel_id}\n";
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
$result = call_api("{$base_url}/student-hostel-details/filter", array(), $bad_headers);
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
    echo "The Student Hostel Details API is working correctly!\n";
} else {
    echo "⚠️  SOME TESTS FAILED ⚠️\n";
    echo "\n";
    echo "Please review the failed tests above and check:\n";
    echo "  1. MySQL is running in XAMPP\n";
    echo "  2. Database has hostel data (students, hostels, rooms)\n";
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

