<?php
/**
 * Test Script for Due Fees Report API
 * 
 * This script tests the /api/due-fees-report/filter endpoint
 * to verify that session filtering is working correctly.
 * 
 * Usage: php test_due_fees_report_api.php
 */

// Configuration
$api_base_url = "http://localhost/amt/api";
$endpoint = "/due-fees-report/filter";
$api_url = $api_base_url . $endpoint;

// Test cases
$test_cases = [
    [
        'name' => 'Test 1: Empty filter (should return all students with due fees)',
        'description' => 'No filters applied - returns all active students with due fees across all sessions',
        'data' => []
    ],
    [
        'name' => 'Test 2: Filter by session ID only',
        'description' => 'Returns students enrolled in the specified session with their fees for that session',
        'data' => ['session_id' => '25']
    ],
    [
        'name' => 'Test 3: Filter by session and class',
        'description' => 'Returns students from specified session and class',
        'data' => ['session_id' => '25', 'class_id' => '1']
    ],
    [
        'name' => 'Test 4: Filter by session, class, and section',
        'description' => 'Returns students from specified session, class, and section',
        'data' => ['session_id' => '25', 'class_id' => '1', 'section_id' => '1']
    ],
    [
        'name' => 'Test 5: Filter by class only (no session)',
        'description' => 'Returns students from specified class across all sessions',
        'data' => ['class_id' => '1']
    ]
];

// Helper function to make API request
function makeApiRequest($url, $data) {
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_errno($ch) ? curl_error($ch) : null;
    
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => $response,
        'error' => $curl_error
    ];
}

// Helper function to display student info
function displayStudentInfo($student, $index) {
    echo "  " . ($index + 1) . ". ";
    echo $student['firstname'] . " " . $student['lastname'];
    echo " (Admission: " . $student['admission_no'] . ")";
    echo " - " . $student['class'] . " / " . $student['section'];
    
    if (isset($student['fees_list']) && !empty($student['fees_list'])) {
        echo "\n     Fees: ";
        $fee_info = [];
        foreach ($student['fees_list'] as $fee) {
            if (isset($fee->name)) {
                $fee_str = $fee->name;
                if (isset($fee->session_id)) {
                    $fee_str .= " (Session: " . $fee->session_id . ")";
                }
                if (isset($fee->fee_amount)) {
                    $fee_str .= " - Amount: " . $fee->fee_amount;
                }
                $fee_info[] = $fee_str;
            }
        }
        echo implode(", ", array_slice($fee_info, 0, 2));
        if (count($fee_info) > 2) {
            echo " ... (" . count($fee_info) . " total)";
        }
    }
    echo "\n";
}

// Main test execution
echo "===========================================\n";
echo "Due Fees Report API - Session Filter Test\n";
echo "===========================================\n";
echo "API Endpoint: " . $api_url . "\n";
echo "Date: " . date('Y-m-d H:i:s') . "\n";
echo "===========================================\n\n";

$test_results = [];

foreach ($test_cases as $index => $test) {
    echo "-------------------------------------------\n";
    echo "TEST " . ($index + 1) . ": " . $test['name'] . "\n";
    echo "-------------------------------------------\n";
    echo "Description: " . $test['description'] . "\n";
    echo "Request Body: " . json_encode($test['data']) . "\n\n";
    
    // Make API request
    $result = makeApiRequest($api_url, $test['data']);
    
    // Check for cURL errors
    if ($result['error']) {
        echo "❌ cURL Error: " . $result['error'] . "\n\n";
        $test_results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => 'cURL Error'];
        continue;
    }
    
    // Display HTTP status
    echo "HTTP Status: " . $result['http_code'] . " ";
    if ($result['http_code'] == 200) {
        echo "✅\n";
    } else {
        echo "❌\n";
    }
    
    // Parse response
    if ($result['http_code'] == 200) {
        $response = json_decode($result['response'], true);
        
        if ($response && isset($response['status'])) {
            // Display response details
            echo "API Status: " . ($response['status'] == 1 ? '✅ Success' : '❌ Failed') . "\n";
            echo "Message: " . $response['message'] . "\n\n";
            
            // Display filters applied
            if (isset($response['filters_applied'])) {
                echo "Filters Applied:\n";
                foreach ($response['filters_applied'] as $key => $value) {
                    echo "  - " . ucfirst(str_replace('_', ' ', $key)) . ": " . ($value ?? 'null') . "\n";
                }
                echo "\n";
            }
            
            // Display filter info
            if (isset($response['filter_info'])) {
                echo "Filter Information:\n";
                foreach ($response['filter_info'] as $key => $value) {
                    echo "  - " . ucfirst(str_replace('_', ' ', $key)) . ": " . $value . "\n";
                }
                echo "\n";
            }
            
            // Display record count
            if (isset($response['total_records'])) {
                echo "Total Records: " . $response['total_records'] . "\n";
            }
            
            // Display sample students
            if (isset($response['data']) && !empty($response['data'])) {
                echo "\nSample Students (first 3):\n";
                $count = 0;
                foreach ($response['data'] as $student) {
                    if ($count >= 3) break;
                    displayStudentInfo($student, $count);
                    $count++;
                }
                
                if ($response['total_records'] > 3) {
                    echo "  ... and " . ($response['total_records'] - 3) . " more students\n";
                }
            } else {
                echo "\n⚠️  No student records found.\n";
            }
            
            // Test result
            if ($response['status'] == 1) {
                $test_results[] = ['test' => $test['name'], 'status' => 'PASSED', 'records' => $response['total_records']];
            } else {
                $test_results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => $response['message']];
            }
            
        } else {
            echo "❌ Invalid JSON response\n";
            echo "Response: " . substr($result['response'], 0, 500) . "\n";
            $test_results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => 'Invalid JSON'];
        }
    } else {
        echo "Error Response:\n";
        echo substr($result['response'], 0, 500) . "\n";
        $test_results[] = ['test' => $test['name'], 'status' => 'FAILED', 'reason' => 'HTTP ' . $result['http_code']];
    }
    
    echo "\n";
}

// Summary
echo "===========================================\n";
echo "TEST SUMMARY\n";
echo "===========================================\n";

$passed = 0;
$failed = 0;

foreach ($test_results as $result) {
    $status_icon = $result['status'] == 'PASSED' ? '✅' : '❌';
    echo $status_icon . " " . $result['test'] . " - " . $result['status'];
    
    if ($result['status'] == 'PASSED') {
        echo " (" . $result['records'] . " records)";
        $passed++;
    } else {
        echo " (" . $result['reason'] . ")";
        $failed++;
    }
    echo "\n";
}

echo "\n";
echo "Total Tests: " . count($test_results) . "\n";
echo "Passed: " . $passed . " ✅\n";
echo "Failed: " . $failed . " ❌\n";
echo "===========================================\n";

// Exit code
exit($failed > 0 ? 1 : 0);

