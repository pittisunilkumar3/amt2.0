<?php
/**
 * Progressive Filter Testing
 * Tests the API with increasingly specific filters to identify where data exists
 */

$base_url = 'http://localhost/amt/api/collection-report/filter';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

function runTest($test_name, $data, $url, $headers) {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "TEST: {$test_name}\n";
    echo str_repeat("=", 80) . "\n";
    echo "Request: " . json_encode($data) . "\n";
    
    $result = makeRequest($url, $data, $headers);
    
    if ($result['response'] && isset($result['response']['total_records'])) {
        $count = $result['response']['total_records'];
        echo "Result: {$count} records found\n";
        
        if ($count > 0) {
            echo "✓ DATA EXISTS!\n";
            $first = $result['response']['data'][0];
            echo "Sample Record:\n";
            echo "  - Student: {$first['firstname']} {$first['lastname']} ({$first['admission_no']})\n";
            echo "  - Class: {$first['class']} - {$first['section']}\n";
            echo "  - Fee Type: {$first['type']}\n";
            echo "  - Amount: {$first['amount']}\n";
            echo "  - Date: {$first['date']}\n";
            echo "  - Received By: {$first['received_by']}\n";
        } else {
            echo "✗ No data found\n";
        }
    } else {
        echo "✗ Error: " . ($result['response']['message'] ?? 'Unknown error') . "\n";
    }
    
    return $result['response']['total_records'] ?? 0;
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║              PROGRESSIVE FILTER TESTING                                    ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

// Test 1: Empty request (current month)
$count1 = runTest("1. Empty Request (Current Month)", [], $base_url, $headers);

// Test 2: This year
$count2 = runTest("2. This Year", ['search_type' => 'this_year'], $base_url, $headers);

// Test 3: Last 12 months
$count3 = runTest("3. Last 12 Months", ['search_type' => 'last_12_month'], $base_url, $headers);

// Test 4: Wide date range
$count4 = runTest("4. Wide Date Range (2024-01-01 to 2025-12-31)", [
    'from_date' => '2024-01-01',
    'to_date' => '2025-12-31'
], $base_url, $headers);

// Test 5: User's date range only
$count5 = runTest("5. User's Date Range Only (2025-09-01 to 2025-10-11)", [
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11'
], $base_url, $headers);

// Test 6: Add session filter
$count6 = runTest("6. Date Range + Session 21", [
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11',
    'session_id' => '21'
], $base_url, $headers);

// Test 7: Add class filter
$count7 = runTest("7. Date Range + Session 21 + Class 19", [
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11',
    'session_id' => '21',
    'class_id' => '19'
], $base_url, $headers);

// Test 8: Add section filter
$count8 = runTest("8. Date Range + Session 21 + Class 19 + Section 36", [
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11',
    'session_id' => '21',
    'class_id' => '19',
    'section_id' => '36'
], $base_url, $headers);

// Test 9: Add fee type filter
$count9 = runTest("9. Date Range + Session 21 + Class 19 + Section 36 + Fee Type 33", [
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11',
    'session_id' => '21',
    'class_id' => '19',
    'section_id' => '36',
    'fee_type_id' => '33'
], $base_url, $headers);

// Test 10: Full filters (user's request)
$count10 = runTest("10. All Filters (User's Request)", [
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11',
    'session_id' => '21',
    'class_id' => '19',
    'section_id' => '36',
    'fee_type_id' => '33',
    'collect_by_id' => '6'
], $base_url, $headers);

// Summary
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           SUMMARY                                          ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Test 1 (Empty/Current Month):     {$count1} records\n";
echo "Test 2 (This Year):                {$count2} records\n";
echo "Test 3 (Last 12 Months):           {$count3} records\n";
echo "Test 4 (Wide Date Range):          {$count4} records\n";
echo "Test 5 (User's Date Range):        {$count5} records\n";
echo "Test 6 (+ Session 21):             {$count6} records\n";
echo "Test 7 (+ Class 19):               {$count7} records\n";
echo "Test 8 (+ Section 36):             {$count8} records\n";
echo "Test 9 (+ Fee Type 33):            {$count9} records\n";
echo "Test 10 (+ Collector 6):           {$count10} records\n";
echo "\n";

// Analysis
echo "ANALYSIS:\n";
if ($count1 > 0) {
    echo "✓ Database has collection data\n";
} else {
    echo "✗ No collection data in current month\n";
}

if ($count4 > 0) {
    echo "✓ Database has collection data in wide date range\n";
} else {
    echo "✗ No collection data at all in database\n";
}

if ($count5 == 0 && $count4 > 0) {
    echo "⚠ Data exists but not in user's date range (2025-09-01 to 2025-10-11)\n";
}

if ($count5 > 0 && $count6 == 0) {
    echo "⚠ Session 21 filter eliminates all records\n";
}

if ($count6 > 0 && $count7 == 0) {
    echo "⚠ Class 19 filter eliminates all records\n";
}

if ($count7 > 0 && $count8 == 0) {
    echo "⚠ Section 36 filter eliminates all records\n";
}

if ($count8 > 0 && $count9 == 0) {
    echo "⚠ Fee Type 33 filter eliminates all records\n";
}

if ($count9 > 0 && $count10 == 0) {
    echo "⚠ Collector 6 filter eliminates all records\n";
}

echo "\n";

