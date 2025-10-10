<?php
/**
 * Get sample data from Type-wise Balance Report API for documentation
 */

echo "Getting sample data from Type-wise Balance Report API...\n\n";

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// Get list response
echo "=== LIST ENDPOINT RESPONSE ===\n\n";
$list_result = makeRequest("{$base_url}/type-wise-balance-report/list", [], $headers);

// Show first 2 sessions
echo "Sessions (first 2):\n";
echo json_encode(array_slice($list_result['sessions'], 0, 2), JSON_PRETTY_PRINT);
echo "\n\n";

// Show first 2 fee types
echo "Fee Types (first 2):\n";
echo json_encode(array_slice($list_result['feetypes'], 0, 2), JSON_PRETTY_PRINT);
echo "\n\n";

// Show first 2 fee groups
echo "Fee Groups (first 2):\n";
echo json_encode(array_slice($list_result['feegroups'], 0, 2), JSON_PRETTY_PRINT);
echo "\n\n";

// Show first 2 classes
echo "Classes (first 2):\n";
echo json_encode(array_slice($list_result['classes'], 0, 2), JSON_PRETTY_PRINT);
echo "\n\n";

// Get filter response
echo "=== FILTER ENDPOINT RESPONSE ===\n\n";

// Find active session
$active_session = null;
foreach ($list_result['sessions'] as $session) {
    if ($session['is_active'] === 'yes') {
        $active_session = $session;
        break;
    }
}

// Get first fee type
$first_feetype = $list_result['feetypes'][0];

$filter_result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => [$first_feetype['id']]
], $headers);

echo "Filter Request:\n";
echo json_encode([
    'session_id' => $active_session['id'],
    'feetype_ids' => [$first_feetype['id']]
], JSON_PRETTY_PRINT);
echo "\n\n";

echo "Response Summary:\n";
echo "  Status: {$filter_result['status']}\n";
echo "  Message: {$filter_result['message']}\n";
echo "  Total Records: {$filter_result['total_records']}\n";
echo "  Timestamp: {$filter_result['timestamp']}\n";
echo "\n";

echo "Filters Applied:\n";
echo json_encode($filter_result['filters_applied'], JSON_PRETTY_PRINT);
echo "\n\n";

if ($filter_result['total_records'] > 0) {
    echo "Data Records (first 3):\n";
    echo json_encode(array_slice($filter_result['data'], 0, 3), JSON_PRETTY_PRINT);
    echo "\n\n";
    
    // Calculate some statistics
    $total_balance = 0;
    $total_paid = 0;
    $total_due = 0;
    
    foreach ($filter_result['data'] as $record) {
        $total = floatval($record['total']);
        $paid = floatval($record['total_amount']);
        $fine = floatval($record['total_fine']);
        $discount = floatval($record['total_discount']);
        
        $balance = $total - $paid + $fine - $discount;
        
        $total_due += $total;
        $total_paid += $paid;
        $total_balance += $balance;
    }
    
    echo "Statistics:\n";
    echo "  Total Due: " . number_format($total_due, 2) . "\n";
    echo "  Total Paid: " . number_format($total_paid, 2) . "\n";
    echo "  Total Balance: " . number_format($total_balance, 2) . "\n";
    echo "\n";
}

// Test with multiple filters
echo "=== FILTER WITH MULTIPLE PARAMETERS ===\n\n";

$first_class = $list_result['classes'][0];

$multi_filter_result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => [$first_feetype['id']],
    'class_id' => $first_class['id']
], $headers);

echo "Filter Request:\n";
echo json_encode([
    'session_id' => $active_session['id'],
    'feetype_ids' => [$first_feetype['id']],
    'class_id' => $first_class['id']
], JSON_PRETTY_PRINT);
echo "\n\n";

echo "Total Records: {$multi_filter_result['total_records']}\n";

if ($multi_filter_result['total_records'] > 0) {
    echo "\nFirst Record:\n";
    echo json_encode($multi_filter_result['data'][0], JSON_PRETTY_PRINT);
}

echo "\n\n=== COMPLETE ===\n";

