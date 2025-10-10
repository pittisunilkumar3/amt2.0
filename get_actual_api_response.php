<?php
/**
 * Get actual API response for documentation
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
curl_close($ch);

$data = json_decode($response, true);

// Show only first session with limited data for documentation
if (isset($data['data'][0])) {
    $session = $data['data'][0];
    
    // Limit to first 2 classes
    if (count($session['classes']) > 2) {
        $session['classes'] = array_slice($session['classes'], 0, 2);
    }
    
    // Limit sections in each class
    foreach ($session['classes'] as &$class) {
        if (count($class['sections']) > 2) {
            $class['sections'] = array_slice($class['sections'], 0, 2);
        }
    }
    
    // Limit to first 2 fee groups
    if (count($session['fee_groups']) > 2) {
        $session['fee_groups'] = array_slice($session['fee_groups'], 0, 2);
    }
    
    // Limit fee types in each group
    foreach ($session['fee_groups'] as &$fg) {
        if (count($fg['fee_types']) > 2) {
            $fg['fee_types'] = array_slice($fg['fee_types'], 0, 2);
        }
    }
    
    $data['data'] = [$session];
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

