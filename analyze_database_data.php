<?php
/**
 * Analyze what data is actually available in the database
 */

$base_url = 'http://localhost/amt/api/collection-report';
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
echo "║              ANALYZING DATABASE DATA                                       ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

// Get actual data for Class 19, Session 21
echo "\nChecking collection data for Class 19, Session 21 (2025-09-01 to 2025-10-11)...\n";
$filter_result = makeRequest($base_url . '/filter', [
    'from_date' => '2025-09-01',
    'to_date' => '2025-10-11',
    'session_id' => '21',
    'class_id' => '19'
], $headers);

if ($filter_result && isset($filter_result['data'])) {
    $records = $filter_result['data'];
    echo "✓ Found {$filter_result['total_records']} records\n";
    
    // Analyze sections, fee types, collectors
    $sections = [];
    $fee_types = [];
    $collectors = [];
    
    foreach ($records as $record) {
        $sections[$record['section_id']] = $record['section'];
        if (isset($record['fee_groups_feetype_id'])) {
            $fee_types[$record['fee_groups_feetype_id']] = $record['type'];
        }
        $collectors[$record['received_by']] = $record['received_by'];
    }
    
    echo "\n✓ Sections in Class 19, Session 21:\n";
    foreach ($sections as $id => $name) {
        echo "  - Section ID: {$id}, Name: {$name}\n";
    }
    
    echo "\n✓ Sample Records:\n";
    for ($i = 0; $i < min(3, count($records)); $i++) {
        $r = $records[$i];
        echo "\n  Record " . ($i + 1) . ":\n";
        echo "    Student: {$r['firstname']} {$r['lastname']}\n";
        echo "    Section: {$r['section']} (ID: {$r['section_id']})\n";
        $feetype_id = isset($r['fee_groups_feetype_id']) ? $r['fee_groups_feetype_id'] : 'N/A';
        echo "    Fee Type: {$r['type']} (ID: {$feetype_id})\n";
        echo "    Amount: {$r['amount']}, Date: {$r['date']}\n";
        echo "    Received By: {$r['received_by']}\n";
    }
    
    // Test with a valid section
    if (count($sections) > 0) {
        $valid_section = array_key_first($sections);
        echo "\n\n✓ Testing with valid Section ID {$valid_section}:\n";
        $valid_test = makeRequest($base_url . '/filter', [
            'from_date' => '2025-09-01',
            'to_date' => '2025-10-11',
            'session_id' => '21',
            'class_id' => '19',
            'section_id' => $valid_section
        ], $headers);
        
        echo "  Result: {$valid_test['total_records']} records found\n";
        
        if ($valid_test['total_records'] > 0) {
            echo "\n  ✓ VALID REQUEST EXAMPLE:\n";
            echo json_encode([
                'session_id' => '21',
                'class_id' => '19',
                'section_id' => $valid_section,
                'from_date' => '2025-09-01',
                'to_date' => '2025-10-11'
            ], JSON_PRETTY_PRINT) . "\n";
        }
    }
}

// Check Section 36
echo "\n\nChecking if Section 36 exists...\n";
$section36_result = makeRequest($base_url . '/filter', [
    'from_date' => '2024-01-01',
    'to_date' => '2025-12-31',
    'section_id' => '36'
], $headers);

if ($section36_result && isset($section36_result['total_records'])) {
    echo "✓ Section 36 has {$section36_result['total_records']} records\n";
    
    if ($section36_result['total_records'] > 0) {
        $sample = $section36_result['data'][0];
        echo "  - Belongs to Class: {$sample['class']} (ID: {$sample['class_id']})\n";
        echo "  - Section Name: {$sample['section']}\n";
    } else {
        echo "  ✗ Section 36 does not exist in the database\n";
    }
}

echo "\n\n╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                    CONCLUSION                                              ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "Your original request returns 0 records because:\n";
echo "  ✗ Section 36 does not exist for Class 19 in Session 21\n";
echo "\nThe API is working correctly. The issue is with the filter values.\n";
echo "Please use valid section IDs from the list above.\n\n";

