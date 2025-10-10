<?php
/**
 * Quick test to verify fee groups are populated
 */

$ch = curl_init('http://localhost/amt/api/session-fee-structure/filter');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['session_id' => 21]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
]);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: {$http_code}\n\n";

$data = json_decode($response, true);

if ($data && isset($data['data']) && count($data['data']) > 0) {
    $session = $data['data'][0];
    
    echo "Session: {$session['session_name']}\n";
    echo "Classes: " . count($session['classes']) . "\n";
    echo "Fee Groups: " . count($session['fee_groups']) . "\n\n";
    
    if (count($session['fee_groups']) > 0) {
        echo "✓ Fee groups are populated!\n\n";
        
        foreach ($session['fee_groups'] as $idx => $fg) {
            echo "Fee Group " . ($idx + 1) . ": {$fg['fee_group_name']}\n";
            echo "  - Fee Types: " . count($fg['fee_types']) . "\n";
            
            if (count($fg['fee_types']) > 0) {
                foreach ($fg['fee_types'] as $ft_idx => $ft) {
                    echo "    " . ($ft_idx + 1) . ". {$ft['fee_type_name']} - Amount: {$ft['amount']}\n";
                    if ($ft_idx >= 2) {
                        echo "    ... and " . (count($fg['fee_types']) - 3) . " more\n";
                        break;
                    }
                }
            }
            echo "\n";
            
            if ($idx >= 2) {
                echo "... and " . (count($session['fee_groups']) - 3) . " more fee groups\n";
                break;
            }
        }
    } else {
        echo "✗ No fee groups found for this session\n";
    }
} else {
    echo "Error or no data returned\n";
    echo json_encode($data, JSON_PRETTY_PRINT);
}

