<?php
/**
 * Get actual list endpoint response
 */

$ch = curl_init('http://localhost/amt/api/session-fee-structure/list');
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
curl_close($ch);

$data = json_decode($response, true);

// Limit arrays for documentation
if (isset($data['sessions']) && count($data['sessions']) > 2) {
    $data['sessions'] = array_slice($data['sessions'], 0, 2);
}
if (isset($data['classes']) && count($data['classes']) > 2) {
    $data['classes'] = array_slice($data['classes'], 0, 2);
}
if (isset($data['fee_groups']) && count($data['fee_groups']) > 2) {
    $data['fee_groups'] = array_slice($data['fee_groups'], 0, 2);
}
if (isset($data['fee_types']) && count($data['fee_types']) > 2) {
    $data['fee_types'] = array_slice($data['fee_types'], 0, 2);
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

