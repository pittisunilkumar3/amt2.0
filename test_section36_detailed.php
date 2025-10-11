<?php
/**
 * Detailed testing for Section 36
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
    curl_close($ch);
    
    return json_decode($response, true);
}

echo "\n╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║              DETAILED TESTING FOR SECTION 36                               ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

// Test 1: Section 36 in wide date range
echo "\n1. Section 36 in wide date range (2024-01-01 to 2025-12-31):\n";
$test1 = makeRequest($base_url, [
    'section_id' => '36',
    'from_date' => '2024-01-01',
    'to_date' => '2025-12-31'
], $headers);
echo "   Result: {$test1['total_records']} records\n";

if ($test1['total_records'] > 0) {
    $sample = $test1['data'][0];
    echo "   Sample: {$sample['firstname']} {$sample['lastname']}, Date: {$sample['date']}\n";
    echo "   Class: {$sample['class']} (ID: {$sample['class_id']})\n";
    echo "   Session: (ID: {$sample['student_session_id']})\n";
}

// Test 2: Section 36 in user's date range
echo "\n2. Section 36 in user's date range (2025-09-01 to 2025-10-11):\n";
$test2 = makeRequest($base_url, [
    'section_id' => '36',
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11'
], $headers);
echo "   Result: {$test2['total_records']} records\n";

// Test 3: Section 36 + Session 21
echo "\n3. Section 36 + Session 21 (wide date range):\n";
$test3 = makeRequest($base_url, [
    'section_id' => '36',
    'session_id' => '21',
    'from_date' => '2024-01-01',
    'to_date' => '2025-12-31'
], $headers);
echo "   Result: {$test3['total_records']} records\n";

// Test 4: Section 36 + Session 21 + user's date range
echo "\n4. Section 36 + Session 21 + user's date range:\n";
$test4 = makeRequest($base_url, [
    'section_id' => '36',
    'session_id' => '21',
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11'
], $headers);
echo "   Result: {$test4['total_records']} records\n";

// Test 5: Section 36 + Class 19
echo "\n5. Section 36 + Class 19 (wide date range):\n";
$test5 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'from_date' => '2024-01-01',
    'to_date' => '2025-12-31'
], $headers);
echo "   Result: {$test5['total_records']} records\n";

// Test 6: Section 36 + Class 19 + user's date range
echo "\n6. Section 36 + Class 19 + user's date range:\n";
$test6 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11'
], $headers);
echo "   Result: {$test6['total_records']} records\n";

// Test 7: Section 36 + Class 19 + Session 21
echo "\n7. Section 36 + Class 19 + Session 21 (wide date range):\n";
$test7 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'session_id' => '21',
    'from_date' => '2024-01-01',
    'to_date' => '2025-12-31'
], $headers);
echo "   Result: {$test7['total_records']} records\n";

// Test 8: Section 36 + Class 19 + Session 21 + user's date range
echo "\n8. Section 36 + Class 19 + Session 21 + user's date range:\n";
$test8 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'session_id' => '21',
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11'
], $headers);
echo "   Result: {$test8['total_records']} records\n";

// Analyze dates in Section 36 data
echo "\n\n9. Analyzing dates in Section 36 data:\n";
if ($test1['total_records'] > 0) {
    $dates = [];
    foreach ($test1['data'] as $record) {
        $dates[] = $record['date'];
    }
    $dates = array_unique($dates);
    sort($dates);
    
    echo "   Date range in data: " . min($dates) . " to " . max($dates) . "\n";
    echo "   Total unique dates: " . count($dates) . "\n";
    echo "   First 10 dates: " . implode(', ', array_slice($dates, 0, 10)) . "\n";
    echo "   Last 10 dates: " . implode(', ', array_slice($dates, -10)) . "\n";
}

echo "\n\n╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    ANALYSIS                                                ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if ($test2['total_records'] == 0) {
    echo "✗ Section 36 has NO data in the date range 2025-09-01 to 2025-10-11\n";
} else {
    echo "✓ Section 36 has {$test2['total_records']} records in the date range\n";
}

if ($test4['total_records'] == 0) {
    echo "✗ Section 36 + Session 21 has NO data in the date range\n";
} else {
    echo "✓ Section 36 + Session 21 has {$test4['total_records']} records\n";
}

if ($test8['total_records'] == 0) {
    echo "✗ Section 36 + Class 19 + Session 21 has NO data in the date range\n";
    echo "\nPossible reasons:\n";
    echo "  1. Section 36 data is outside the date range 2025-09-01 to 2025-10-11\n";
    echo "  2. Section 36 belongs to a different session\n";
    echo "  3. Section 36 data exists but with different class/session combination\n";
} else {
    echo "✓ Section 36 + Class 19 + Session 21 has {$test8['total_records']} records\n";
}

echo "\n";

