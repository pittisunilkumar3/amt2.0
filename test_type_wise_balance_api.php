<?php
/**
 * Test Type-wise Balance Report API
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║         TYPE-WISE BALANCE REPORT API - TEST SUITE                         ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data = [], $headers = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['http_code' => $http_code, 'response' => json_decode($response, true)];
}

// Test 1: Get filter options
echo "[1/5] Testing list endpoint (filter options)...\n";
echo str_repeat("-", 80) . "\n";
$result = makeRequest("{$base_url}/type-wise-balance-report/list", [], $headers);

if ($result['http_code'] == 200) {
    echo "✓ Success! HTTP 200\n\n";
    echo "Available Sessions: " . count($result['response']['sessions']) . "\n";
    echo "Available Fee Groups: " . count($result['response']['feegroups']) . "\n";
    echo "Available Fee Types: " . count($result['response']['feetypes']) . "\n";
    echo "Available Classes: " . count($result['response']['classes']) . "\n";
    
    // Find active session
    $active_session = null;
    foreach ($result['response']['sessions'] as $session) {
        if ($session['is_active'] === 'yes') {
            $active_session = $session;
            break;
        }
    }
    
    if (!$active_session && count($result['response']['sessions']) > 0) {
        $active_session = $result['response']['sessions'][0];
    }
    
    echo "\nTest Session: {$active_session['session']} (ID: {$active_session['id']})\n";
    
    // Get first fee type
    $first_feetype = null;
    if (count($result['response']['feetypes']) > 0) {
        $first_feetype = $result['response']['feetypes'][0];
        echo "Test Fee Type: {$first_feetype['type']} (ID: {$first_feetype['id']})\n";
    }
} else {
    echo "✗ Failed with HTTP {$result['http_code']}\n";
    echo json_encode($result['response'], JSON_PRETTY_PRINT);
    exit(1);
}

echo "\n";

// Test 2: Test with missing session_id (should fail)
echo "[2/5] Testing filter endpoint without session_id (should fail)...\n";
echo str_repeat("-", 80) . "\n";
$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [], $headers);

if ($result['http_code'] == 400) {
    echo "✓ Correctly rejected! HTTP 400\n";
    echo "Message: {$result['response']['message']}\n";
} else {
    echo "✗ Unexpected response: HTTP {$result['http_code']}\n";
}

echo "\n";

// Test 3: Test with session_id and empty feetype_ids (should return all fee types)
echo "[3/5] Testing filter endpoint with session_id and empty feetype_ids...\n";
echo str_repeat("-", 80) . "\n";
$result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => []
], $headers);

if ($result['http_code'] == 200) {
    echo "✓ Success! HTTP 200\n\n";
    echo "Total Records: {$result['response']['total_records']}\n";
    echo "Filters Applied:\n";
    echo "  - Session ID: {$result['response']['filters_applied']['session_id']}\n";
    echo "  - Fee Type IDs: " . json_encode($result['response']['filters_applied']['feetype_ids']) . "\n";
    
    if ($result['response']['total_records'] > 0) {
        echo "\n✓ Data returned successfully!\n";
        $first_record = $result['response']['data'][0];
        echo "\nFirst Record Sample:\n";
        echo "  - Student: {$first_record['firstname']} {$first_record['lastname']}\n";
        echo "  - Admission No: {$first_record['admission_no']}\n";
        echo "  - Class: {$first_record['class']}\n";
        echo "  - Section: {$first_record['section']}\n";
        echo "  - Fee Type: {$first_record['type']}\n";
        echo "  - Fee Group: {$first_record['feegroupname']}\n";
        echo "  - Total Amount: {$first_record['total']}\n";
        echo "  - Paid Amount: {$first_record['total_amount']}\n";
        echo "  - Fine: {$first_record['total_fine']}\n";
        echo "  - Discount: {$first_record['total_discount']}\n";
        
        // Calculate balance
        $balance = floatval($first_record['total']) - floatval($first_record['total_amount']) + floatval($first_record['total_fine']) - floatval($first_record['total_discount']);
        echo "  - Balance: {$balance}\n";
    } else {
        echo "\n⚠ No data returned for this session\n";
    }
} else {
    echo "✗ Failed with HTTP {$result['http_code']}\n";
    echo json_encode($result['response'], JSON_PRETTY_PRINT);
}

echo "\n";

// Test 4: Test with specific fee type
if ($first_feetype) {
    echo "[4/5] Testing filter endpoint with specific fee type...\n";
    echo str_repeat("-", 80) . "\n";
    $result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
        'session_id' => $active_session['id'],
        'feetype_ids' => [$first_feetype['id']]
    ], $headers);
    
    if ($result['http_code'] == 200) {
        echo "✓ Success! HTTP 200\n\n";
        echo "Total Records: {$result['response']['total_records']}\n";
        echo "Fee Type Filter: {$first_feetype['type']} (ID: {$first_feetype['id']})\n";
        
        if ($result['response']['total_records'] > 0) {
            echo "\n✓ Filtered data returned successfully!\n";
            
            // Verify all records have the correct fee type
            $all_correct = true;
            foreach ($result['response']['data'] as $record) {
                if ($record['type'] !== $first_feetype['type']) {
                    $all_correct = false;
                    break;
                }
            }
            
            if ($all_correct) {
                echo "✓ All records match the fee type filter\n";
            } else {
                echo "✗ Some records don't match the fee type filter\n";
            }
        } else {
            echo "\n⚠ No data returned for this fee type\n";
        }
    } else {
        echo "✗ Failed with HTTP {$result['http_code']}\n";
    }
} else {
    echo "[4/5] Skipping fee type filter test (no fee types available)\n";
}

echo "\n";

// Test 5: Test with class filter
echo "[5/5] Testing filter endpoint with class filter...\n";
echo str_repeat("-", 80) . "\n";

// Get first class
$first_class = null;
if (isset($result['response']['classes']) && count($result['response']['classes']) > 0) {
    $first_class = $result['response']['classes'][0];
}

if ($first_class) {
    $result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
        'session_id' => $active_session['id'],
        'feetype_ids' => [],
        'class_id' => $first_class['id']
    ], $headers);
    
    if ($result['http_code'] == 200) {
        echo "✓ Success! HTTP 200\n\n";
        echo "Total Records: {$result['response']['total_records']}\n";
        echo "Class Filter: {$first_class['class']} (ID: {$first_class['id']})\n";
        
        if ($result['response']['total_records'] > 0) {
            echo "\n✓ Class-filtered data returned successfully!\n";
        } else {
            echo "\n⚠ No data returned for this class\n";
        }
    } else {
        echo "✗ Failed with HTTP {$result['http_code']}\n";
    }
} else {
    echo "⚠ Skipping class filter test (no classes available)\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                           TEST SUITE COMPLETED                             ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "API ENDPOINTS:\n";
echo "--------------\n";
echo "Filter: POST {$base_url}/type-wise-balance-report/filter\n";
echo "List:   POST {$base_url}/type-wise-balance-report/list\n";
echo "\n";

echo "NEXT STEPS:\n";
echo "-----------\n";
echo "1. Review the test results above\n";
echo "2. Create comprehensive API documentation\n";
echo "3. Test with your frontend application\n";
echo "\n";

