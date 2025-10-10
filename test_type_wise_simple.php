<?php
/**
 * Simple test for Type-wise Balance Report API
 */

echo "Testing Type-wise Balance Report API...\n\n";

$url = 'http://localhost/amt/api/type-wise-balance-report/list';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "HTTP Code: {$http_code}\n";
if ($error) {
    echo "cURL Error: {$error}\n";
}

echo "\nResponse:\n";
echo $response;
echo "\n\n";

if ($http_code == 200) {
    $data = json_decode($response, true);
    if ($data) {
        echo "Sessions: " . count($data['sessions']) . "\n";
        echo "Fee Types: " . count($data['feetypes']) . "\n";
        echo "Fee Groups: " . count($data['feegroups']) . "\n";
        echo "Classes: " . count($data['classes']) . "\n";
        
        // Get active session
        $active_session = null;
        foreach ($data['sessions'] as $session) {
            if ($session['is_active'] === 'yes') {
                $active_session = $session;
                break;
            }
        }
        
        if ($active_session) {
            echo "\nActive Session: {$active_session['session']} (ID: {$active_session['id']})\n";
            
            // Now test filter endpoint
            echo "\n--- Testing Filter Endpoint ---\n";
            
            $filter_url = 'http://localhost/amt/api/type-wise-balance-report/filter';
            $filter_data = [
                'session_id' => $active_session['id'],
                'feetype_ids' => []
            ];
            
            $ch = curl_init($filter_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($filter_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Client-Service: smartschool',
                'Auth-Key: schoolAdmin@'
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $filter_response = curl_exec($ch);
            $filter_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "Filter HTTP Code: {$filter_http_code}\n";
            
            $filter_result = json_decode($filter_response, true);
            if ($filter_result) {
                echo "Total Records: {$filter_result['total_records']}\n";
                
                if ($filter_result['total_records'] > 0) {
                    echo "\nFirst Record:\n";
                    $record = $filter_result['data'][0];
                    echo "  Student: {$record['firstname']} {$record['lastname']}\n";
                    echo "  Admission No: {$record['admission_no']}\n";
                    echo "  Class: {$record['class']}\n";
                    echo "  Section: {$record['section']}\n";
                    echo "  Fee Type: {$record['type']}\n";
                    echo "  Fee Group: {$record['feegroupname']}\n";
                    echo "  Total: {$record['total']}\n";
                    echo "  Paid: {$record['total_amount']}\n";
                    echo "  Fine: {$record['total_fine']}\n";
                    echo "  Discount: {$record['total_discount']}\n";
                    
                    $balance = floatval($record['total']) - floatval($record['total_amount']) + floatval($record['total_fine']) - floatval($record['total_discount']);
                    echo "  Balance: {$balance}\n";
                    
                    echo "\n✓ API is working correctly!\n";
                } else {
                    echo "\n⚠ No data returned for this session\n";
                }
            } else {
                echo "Failed to parse filter response\n";
                echo $filter_response . "\n";
            }
        }
    }
}

