<?php
/**
 * Demo script for Session Fee Structure API
 * 
 * This script demonstrates the complete functionality of the API
 */

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║           SESSION FEE STRUCTURE API - DEMONSTRATION                        ║\n";
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

// Demo 1: Get filter options
echo "DEMO 1: Get Available Filter Options\n";
echo str_repeat("-", 80) . "\n";
$result = makeRequest("{$base_url}/session-fee-structure/list", [], $headers);

if ($result['http_code'] == 200) {
    echo "✓ Success!\n\n";
    echo "Available Sessions: " . count($result['response']['sessions']) . "\n";
    echo "Available Classes: " . count($result['response']['classes']) . "\n";
    echo "Available Fee Groups: " . count($result['response']['fee_groups']) . "\n";
    echo "Available Fee Types: " . count($result['response']['fee_types']) . "\n";
    
    // Find a session with data
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
    
    echo "\nActive Session: {$active_session['session']} (ID: {$active_session['id']})\n";
} else {
    echo "✗ Failed with HTTP {$result['http_code']}\n";
    exit(1);
}

echo "\n";

// Demo 2: Get complete fee structure for active session
echo "DEMO 2: Get Complete Fee Structure for Active Session\n";
echo str_repeat("-", 80) . "\n";
$result = makeRequest("{$base_url}/session-fee-structure/filter", [
    'session_id' => $active_session['id']
], $headers);

if ($result['http_code'] == 200 && isset($result['response']['data'][0])) {
    $session_data = $result['response']['data'][0];
    
    echo "✓ Success!\n\n";
    echo "Session: {$session_data['session_name']}\n";
    echo "Status: " . ($session_data['session_is_active'] === 'yes' ? 'Active' : 'Inactive') . "\n";
    echo "Classes: " . count($session_data['classes']) . "\n";
    echo "Fee Groups: " . count($session_data['fee_groups']) . "\n\n";
    
    // Display classes and sections
    if (count($session_data['classes']) > 0) {
        echo "Classes and Sections:\n";
        foreach ($session_data['classes'] as $idx => $class) {
            echo "  " . ($idx + 1) . ". {$class['class_name']} - " . count($class['sections']) . " sections\n";
            if ($idx >= 2) {
                echo "  ... and " . (count($session_data['classes']) - 3) . " more classes\n";
                break;
            }
        }
        echo "\n";
    }
    
    // Display fee groups and fee types
    if (count($session_data['fee_groups']) > 0) {
        echo "Fee Groups and Fee Types:\n";
        $total_amount = 0;
        
        foreach ($session_data['fee_groups'] as $idx => $fg) {
            echo "  " . ($idx + 1) . ". {$fg['fee_group_name']}\n";
            
            foreach ($fg['fee_types'] as $ft_idx => $ft) {
                echo "     - {$ft['fee_type_name']}: ₹{$ft['amount']}";
                
                if ($ft['fine_type'] !== 'none' && $ft['fine_type'] !== null) {
                    if ($ft['fine_type'] === 'percentage') {
                        echo " (Fine: {$ft['fine_percentage']}%)";
                    } elseif ($ft['fine_type'] === 'fixed') {
                        echo " (Fine: ₹{$ft['fine_amount']})";
                    }
                }
                
                echo "\n";
                $total_amount += floatval($ft['amount']);
                
                if ($ft_idx >= 1) {
                    $remaining = count($fg['fee_types']) - 2;
                    if ($remaining > 0) {
                        echo "     ... and {$remaining} more fee type(s)\n";
                    }
                    break;
                }
            }
            
            if ($idx >= 2) {
                echo "  ... and " . (count($session_data['fee_groups']) - 3) . " more fee groups\n";
                break;
            }
        }
        
        echo "\nTotal Amount (from displayed fee types): ₹" . number_format($total_amount, 2) . "\n";
    }
} else {
    echo "✗ Failed or no data available\n";
}

echo "\n";

// Demo 3: Filter by specific class
if (count($session_data['classes']) > 0) {
    $first_class = $session_data['classes'][0];
    
    echo "DEMO 3: Filter by Specific Class\n";
    echo str_repeat("-", 80) . "\n";
    $result = makeRequest("{$base_url}/session-fee-structure/filter", [
        'session_id' => $active_session['id'],
        'class_id' => $first_class['class_id']
    ], $headers);
    
    if ($result['http_code'] == 200 && isset($result['response']['data'][0])) {
        $filtered_data = $result['response']['data'][0];
        
        echo "✓ Success!\n\n";
        echo "Filtered by Class: {$first_class['class_name']}\n";
        echo "Classes in response: " . count($filtered_data['classes']) . "\n";
        echo "Sections in class: " . count($filtered_data['classes'][0]['sections']) . "\n";
        echo "Fee Groups: " . count($filtered_data['fee_groups']) . "\n";
    } else {
        echo "✗ Failed or no data available\n";
    }
    
    echo "\n";
}

// Demo 4: Filter by specific fee group
if (count($session_data['fee_groups']) > 0) {
    $first_fee_group = $session_data['fee_groups'][0];
    
    echo "DEMO 4: Filter by Specific Fee Group\n";
    echo str_repeat("-", 80) . "\n";
    $result = makeRequest("{$base_url}/session-fee-structure/filter", [
        'session_id' => $active_session['id'],
        'fee_group_id' => $first_fee_group['fee_group_id']
    ], $headers);
    
    if ($result['http_code'] == 200 && isset($result['response']['data'][0])) {
        $filtered_data = $result['response']['data'][0];
        
        echo "✓ Success!\n\n";
        echo "Filtered by Fee Group: {$first_fee_group['fee_group_name']}\n";
        echo "Fee Groups in response: " . count($filtered_data['fee_groups']) . "\n";
        
        if (count($filtered_data['fee_groups']) > 0) {
            echo "Fee Types in group: " . count($filtered_data['fee_groups'][0]['fee_types']) . "\n";
            
            echo "\nFee Types:\n";
            foreach ($filtered_data['fee_groups'][0]['fee_types'] as $ft) {
                echo "  - {$ft['fee_type_name']}: ₹{$ft['amount']}\n";
            }
        }
    } else {
        echo "✗ Failed or no data available\n";
    }
    
    echo "\n";
}

// Demo 5: Empty request (get all data)
echo "DEMO 5: Empty Request (Get All Data)\n";
echo str_repeat("-", 80) . "\n";
$result = makeRequest("{$base_url}/session-fee-structure/filter", [], $headers);

if ($result['http_code'] == 200) {
    echo "✓ Success!\n\n";
    echo "Total Sessions: {$result['response']['total_sessions']}\n";
    
    $total_classes = 0;
    $total_fee_groups = 0;
    
    foreach ($result['response']['data'] as $session) {
        $total_classes += count($session['classes']);
        $total_fee_groups += count($session['fee_groups']);
    }
    
    echo "Total Classes: {$total_classes}\n";
    echo "Total Fee Groups: {$total_fee_groups}\n";
} else {
    echo "✗ Failed with HTTP {$result['http_code']}\n";
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                        DEMONSTRATION COMPLETED                             ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";
echo "\n";

echo "API ENDPOINTS:\n";
echo "--------------\n";
echo "Filter: POST {$base_url}/session-fee-structure/filter\n";
echo "List:   POST {$base_url}/session-fee-structure/list\n";
echo "\n";

echo "DOCUMENTATION:\n";
echo "--------------\n";
echo "Complete API documentation: api/documentation/SESSION_FEE_STRUCTURE_API_README.md\n";
echo "Implementation summary: SESSION_FEE_STRUCTURE_API_SUMMARY.md\n";
echo "\n";

echo "NEXT STEPS:\n";
echo "-----------\n";
echo "1. Review the API documentation\n";
echo "2. Test with your frontend application\n";
echo "3. Use the provided curl examples\n";
echo "4. Integrate into your school management system\n";
echo "\n";

