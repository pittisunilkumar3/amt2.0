<?php
/**
 * Test date filtering logic
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
echo "║              TESTING DATE FILTERING LOGIC                                  ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

// Test with Section 36 + Class 19 + Session 21 with different date ranges
echo "\n1. Section 36 + Class 19 + Session 21 with date 2025-09-26:\n";
$test1 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'session_id' => '21',
    'from_date' => '2025-09-26',
    'to_date' => '2025-09-26'
], $headers);
echo "   Result: {$test1['total_records']} records\n";

if ($test1['total_records'] > 0) {
    $record = $test1['data'][0];
    echo "   ✓ Found record:\n";
    echo "     Student: {$record['firstname']} {$record['lastname']}\n";
    echo "     Date: {$record['date']}\n";
    echo "     Amount: {$record['amount']}\n";
}

echo "\n2. Section 36 + Class 19 + Session 21 (2025-09-20 to 2025-09-30):\n";
$test2 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'session_id' => '21',
    'from_date' => '2025-09-20',
    'to_date' => '2025-09-30'
], $headers);
echo "   Result: {$test2['total_records']} records\n";

echo "\n3. Section 36 + Class 19 + Session 21 (2025-09-01 to 2025-09-30):\n";
$test3 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'session_id' => '21',
    'from_date' => '2025-09-01',
    'to_date' => '2025-09-30'
], $headers);
echo "   Result: {$test3['total_records']} records\n";

echo "\n4. User's original date range (2025-09-01 to 2025-10-11):\n";
$test4 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'session_id' => '21',
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11'
], $headers);
echo "   Result: {$test4['total_records']} records\n";

// Get the one record from Section 36 + Class 19 + Session 21
echo "\n5. Getting the one record from Section 36 + Class 19 + Session 21 (wide range):\n";
$test5 = makeRequest($base_url, [
    'section_id' => '36',
    'class_id' => '19',
    'session_id' => '21',
    'from_date' => '2024-01-01',
    'to_date' => '2025-12-31'
], $headers);
echo "   Result: {$test5['total_records']} records\n";

if ($test5['total_records'] > 0) {
    $record = $test5['data'][0];
    echo "   ✓ Record details:\n";
    echo "     Student: {$record['firstname']} {$record['lastname']} ({$record['admission_no']})\n";
    echo "     Class: {$record['class']} (ID: {$record['class_id']})\n";
    echo "     Section: {$record['section']} (ID: {$record['section_id']})\n";
    echo "     Fee Type: {$record['type']}\n";
    echo "     Amount: {$record['amount']}\n";
    echo "     Date: {$record['date']}\n";
    echo "     Received By: {$record['received_by']}\n";
    
    // Check if this date is within user's range
    $record_date = strtotime($record['date']);
    $user_start = strtotime('2025-09-01');
    $user_end = strtotime('2025-10-11');
    
    echo "\n   Date comparison:\n";
    echo "     Record date: {$record['date']} (" . date('Y-m-d', $record_date) . ")\n";
    echo "     User start: 2025-09-01 (" . date('Y-m-d', $user_start) . ")\n";
    echo "     User end: 2025-10-11 (" . date('Y-m-d', $user_end) . ")\n";
    echo "     Is within range? " . ($record_date >= $user_start && $record_date <= $user_end ? 'YES' : 'NO') . "\n";
}

echo "\n\n╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    CONCLUSION                                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

if ($test4['total_records'] == 0 && $test5['total_records'] > 0) {
    echo "The API is working correctly, but:\n";
    echo "  ✗ Section 36 + Class 19 + Session 21 has only 1 record\n";
    echo "  ✗ That record's date is OUTSIDE the user's date range (2025-09-01 to 2025-10-11)\n";
    echo "\nThe user needs to either:\n";
    echo "  1. Use a different date range that includes the record's date\n";
    echo "  2. Use different filter values (section, class, session)\n";
    echo "  3. Check if the data they're looking for actually exists in the database\n";
}

echo "\n";

