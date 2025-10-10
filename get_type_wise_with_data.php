<?php
/**
 * Get Type-wise Balance Report data with TUITION FEE
 */

echo "Getting Type-wise Balance Report with TUITION FEE...\n\n";

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

// Get list to find TUITION FEE
$list_result = makeRequest("{$base_url}/type-wise-balance-report/list", [], $headers);

// Find TUITION FEE
$tuition_fee = null;
foreach ($list_result['feetypes'] as $feetype) {
    if (stripos($feetype['type'], 'TUITION') !== false) {
        $tuition_fee = $feetype;
        break;
    }
}

if (!$tuition_fee) {
    echo "TUITION FEE not found, using first fee type\n";
    $tuition_fee = $list_result['feetypes'][0];
}

echo "Using Fee Type: {$tuition_fee['type']} (ID: {$tuition_fee['id']})\n\n";

// Find active session
$active_session = null;
foreach ($list_result['sessions'] as $session) {
    if ($session['is_active'] === 'yes') {
        $active_session = $session;
        break;
    }
}

echo "Using Session: {$active_session['session']} (ID: {$active_session['id']})\n\n";

// Get filter response with TUITION FEE
$filter_result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
    'session_id' => $active_session['id'],
    'feetype_ids' => [$tuition_fee['id']]
], $headers);

echo "=== FILTER RESPONSE ===\n\n";
echo "Status: {$filter_result['status']}\n";
echo "Message: {$filter_result['message']}\n";
echo "Total Records: {$filter_result['total_records']}\n";
echo "Timestamp: {$filter_result['timestamp']}\n\n";

echo "Filters Applied:\n";
echo json_encode($filter_result['filters_applied'], JSON_PRETTY_PRINT);
echo "\n\n";

if ($filter_result['total_records'] > 0) {
    echo "=== SAMPLE DATA (First 3 Records) ===\n\n";
    echo json_encode(array_slice($filter_result['data'], 0, 3), JSON_PRETTY_PRINT);
    echo "\n\n";
    
    // Show field structure
    echo "=== FIELD STRUCTURE ===\n\n";
    $first_record = $filter_result['data'][0];
    foreach ($first_record as $key => $value) {
        $type = gettype($value);
        $sample = is_string($value) ? "\"$value\"" : $value;
        echo "  {$key}: {$type} = {$sample}\n";
    }
    echo "\n";
    
    // Calculate statistics
    echo "=== STATISTICS ===\n\n";
    $total_balance = 0;
    $total_paid = 0;
    $total_due = 0;
    $total_fine = 0;
    $total_discount = 0;
    
    foreach ($filter_result['data'] as $record) {
        $total = floatval($record['total']);
        $paid = floatval($record['total_amount']);
        $fine = floatval($record['total_fine']);
        $discount = floatval($record['total_discount']);
        
        $balance = $total - $paid + $fine - $discount;
        
        $total_due += $total;
        $total_paid += $paid;
        $total_balance += $balance;
        $total_fine += $fine;
        $total_discount += $discount;
    }
    
    echo "Total Students: {$filter_result['total_records']}\n";
    echo "Total Due Amount: ₹" . number_format($total_due, 2) . "\n";
    echo "Total Paid Amount: ₹" . number_format($total_paid, 2) . "\n";
    echo "Total Fine: ₹" . number_format($total_fine, 2) . "\n";
    echo "Total Discount: ₹" . number_format($total_discount, 2) . "\n";
    echo "Total Balance: ₹" . number_format($total_balance, 2) . "\n";
    echo "\n";
    
    // Test with class filter
    echo "=== TESTING WITH CLASS FILTER ===\n\n";
    $first_class = $list_result['classes'][0];
    
    $class_filter_result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
        'session_id' => $active_session['id'],
        'feetype_ids' => [$tuition_fee['id']],
        'class_id' => $first_class['id']
    ], $headers);
    
    echo "Class: {$first_class['class']} (ID: {$first_class['id']})\n";
    echo "Total Records: {$class_filter_result['total_records']}\n";
    
    if ($class_filter_result['total_records'] > 0) {
        echo "\nFirst Record:\n";
        echo json_encode($class_filter_result['data'][0], JSON_PRETTY_PRINT);
    }
    
} else {
    echo "No data found for this fee type. Trying with empty feetype_ids...\n\n";
    
    // Try with empty feetype_ids
    $filter_result = makeRequest("{$base_url}/type-wise-balance-report/filter", [
        'session_id' => $active_session['id'],
        'feetype_ids' => []
    ], $headers);
    
    echo "Total Records: {$filter_result['total_records']}\n\n";
    
    if ($filter_result['total_records'] > 0) {
        echo "First 3 Records:\n";
        echo json_encode(array_slice($filter_result['data'], 0, 3), JSON_PRETTY_PRINT);
    }
}

echo "\n=== COMPLETE ===\n";

