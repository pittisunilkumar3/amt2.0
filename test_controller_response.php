<?php
// Test the controller response directly
$url = 'http://localhost/amt/financereports/getFeeGroupwiseData';
$data = array(
    'session_id' => '21',
    'class_ids' => '',
    'section_ids' => '',
    'feegroup_ids' => '',
    'from_date' => '',
    'to_date' => '',
    'date_grouping' => 'None'
);

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Use cookies to maintain session
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response Length: " . strlen($response) . "\n";
echo "Response:\n";
echo $response . "\n";

// Try to decode JSON
$decoded = json_decode($response, true);
if ($decoded) {
    echo "\nDecoded JSON:\n";
    echo "Status: " . ($decoded['status'] ?? 'not set') . "\n";
    echo "Message: " . ($decoded['message'] ?? 'not set') . "\n";
    echo "Grid Data Count: " . (isset($decoded['grid_data']) ? count($decoded['grid_data']) : 'not set') . "\n";
    echo "Detailed Data Count: " . (isset($decoded['detailed_data']) ? count($decoded['detailed_data']) : 'not set') . "\n";
    
    if (isset($decoded['summary'])) {
        echo "Summary:\n";
        foreach ($decoded['summary'] as $key => $value) {
            echo "  $key: $value\n";
        }
    }
} else {
    echo "\nFailed to decode JSON. Raw response:\n";
    echo $response;
}
?>
