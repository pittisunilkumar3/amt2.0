<?php
/**
 * Comprehensive Test Script for All 4 New Finance Report APIs
 */

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['response' => json_decode($response, true), 'http_code' => $http_code];
}

echo "==============================================\n";
echo "ALL 4 NEW FINANCE REPORT APIs - TESTING\n";
echo "==============================================\n\n";

// ============================================
// API #1: COLLECTION REPORT API
// ============================================
echo "API #1: COLLECTION REPORT API\n";
echo "============================================\n\n";

echo "Test 1.1: Collection Report - Empty Request\n";
$result = makeRequest($base_url . '/collection-report/filter', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 1.2: Collection Report - List Endpoint\n";
$result = makeRequest($base_url . '/collection-report/list', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 1.3: Collection Report - With search_type\n";
$result = makeRequest($base_url . '/collection-report/filter', ['search_type' => 'this_year'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// ============================================
// API #2: TOTAL STUDENT ACADEMIC REPORT API
// ============================================
echo "API #2: TOTAL STUDENT ACADEMIC REPORT API\n";
echo "============================================\n\n";

echo "Test 2.1: Total Student Academic Report - Empty Request\n";
$result = makeRequest($base_url . '/total-student-academic-report/filter', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 2.2: Total Student Academic Report - List Endpoint\n";
$result = makeRequest($base_url . '/total-student-academic-report/list', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 2.3: Total Student Academic Report - With class_id\n";
$result = makeRequest($base_url . '/total-student-academic-report/filter', ['class_id' => '1'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 2.4: Total Student Academic Report - With session_id\n";
$result = makeRequest($base_url . '/total-student-academic-report/filter', ['session_id' => '1'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

// ============================================
// API #3: STUDENT ACADEMIC REPORT API
// ============================================
echo "API #3: STUDENT ACADEMIC REPORT API\n";
echo "============================================\n\n";

echo "Test 3.1: Student Academic Report - List Endpoint\n";
$result = makeRequest($base_url . '/student-academic-report/list', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 3.2: Student Academic Report - With class_id\n";
$result = makeRequest($base_url . '/student-academic-report/filter', ['class_id' => '1'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 3.3: Student Academic Report - No filter (should fail)\n";
$result = makeRequest($base_url . '/student-academic-report/filter', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Message: " . ($result['response']['message'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 400 ? "✓ PASS (Expected 400)" : "✗ FAIL") . "\n\n";

// ============================================
// API #4: REPORT BY NAME API
// ============================================
echo "API #4: REPORT BY NAME API\n";
echo "============================================\n\n";

echo "Test 4.1: Report By Name - Empty Request (returns all)\n";
$result = makeRequest($base_url . '/report-by-name/filter', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 4.2: Report By Name - List Endpoint\n";
$result = makeRequest($base_url . '/report-by-name/list', [], $headers);
echo "HTTP: " . $result['http_code'] . " | Status: " . ($result['response']['status'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 4.3: Report By Name - With search_text\n";
$result = makeRequest($base_url . '/report-by-name/filter', ['search_text' => 'John'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "Test 4.4: Report By Name - With class_id\n";
$result = makeRequest($base_url . '/report-by-name/filter', ['class_id' => '1'], $headers);
echo "HTTP: " . $result['http_code'] . " | Records: " . ($result['response']['total_records'] ?? 'N/A') . "\n";
echo "Status: " . ($result['http_code'] == 200 ? "✓ PASS" : "✗ FAIL") . "\n\n";

echo "==============================================\n";
echo "TESTING COMPLETE\n";
echo "==============================================\n\n";

echo "Summary:\n";
echo "- API #1 (Collection Report): 3 tests\n";
echo "- API #2 (Total Student Academic Report): 4 tests\n";
echo "- API #3 (Student Academic Report): 3 tests\n";
echo "- API #4 (Report By Name): 4 tests\n";
echo "- Total: 14 tests\n\n";

echo "All 4 Finance Report APIs have been tested!\n";
echo "Check the results above to verify all tests passed.\n";

