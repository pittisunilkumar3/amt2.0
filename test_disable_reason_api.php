<?php
/**
 * Test script for Disable Reason API
 * 
 * This script tests the disable reason API endpoints to ensure they're working correctly.
 */

// API base URL
$base_url = 'http://localhost/amt/api/disable-reason';

// Required headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

/**
 * Make API request
 */
function makeApiRequest($url, $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => $response,
        'error' => $error
    ];
}

/**
 * Display test result
 */
function displayResult($test_name, $result) {
    echo "<h3>$test_name</h3>";
    echo "<p><strong>HTTP Code:</strong> " . $result['http_code'] . "</p>";
    
    if ($result['error']) {
        echo "<p><strong>Error:</strong> " . $result['error'] . "</p>";
    }
    
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . htmlspecialchars($result['response']) . "</pre>";
    
    // Try to decode JSON for better display
    $json = json_decode($result['response'], true);
    if ($json) {
        echo "<p><strong>Formatted JSON:</strong></p>";
        echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
    }
    
    echo "<hr>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Disable Reason API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
    </style>
</head>
<body>
    <h1>Disable Reason API Test Results</h1>
    
    <?php
    
    // Test 1: List all disable reasons
    echo "<h2>Test 1: List All Disable Reasons</h2>";
    $result = makeApiRequest($base_url . '/list', null, $headers);
    displayResult('GET /api/disable-reason/list', $result);
    
    // Test 2: Create a new disable reason
    echo "<h2>Test 2: Create New Disable Reason</h2>";
    $create_data = [
        'reason' => 'Test Disable Reason - ' . date('Y-m-d H:i:s')
    ];
    $result = makeApiRequest($base_url . '/create', $create_data, $headers);
    displayResult('POST /api/disable-reason/create', $result);
    
    // Extract created ID for further tests
    $created_id = null;
    if ($result['response']) {
        $json = json_decode($result['response'], true);
        if ($json && isset($json['data']['id'])) {
            $created_id = $json['data']['id'];
        }
    }
    
    // Test 3: Get specific disable reason (if we have an ID)
    if ($created_id) {
        echo "<h2>Test 3: Get Specific Disable Reason (ID: $created_id)</h2>";
        $result = makeApiRequest($base_url . '/get/' . $created_id, null, $headers);
        displayResult("GET /api/disable-reason/get/$created_id", $result);
        
        // Test 4: Update the disable reason
        echo "<h2>Test 4: Update Disable Reason (ID: $created_id)</h2>";
        $update_data = [
            'reason' => 'Updated Test Disable Reason - ' . date('Y-m-d H:i:s')
        ];
        $result = makeApiRequest($base_url . '/update/' . $created_id, $update_data, $headers);
        displayResult("POST /api/disable-reason/update/$created_id", $result);
        
        // Test 5: Delete the disable reason
        echo "<h2>Test 5: Delete Disable Reason (ID: $created_id)</h2>";
        $result = makeApiRequest($base_url . '/delete/' . $created_id, null, $headers);
        displayResult("POST /api/disable-reason/delete/$created_id", $result);
    } else {
        echo "<h2>Tests 3-5 Skipped</h2>";
        echo "<p class='warning'>Could not extract created ID from create response, skipping get/update/delete tests.</p>";
    }
    
    // Test 6: Test with invalid headers
    echo "<h2>Test 6: Test with Invalid Headers</h2>";
    $invalid_headers = [
        'Content-Type: application/json',
        'Client-Service: invalid',
        'Auth-Key: invalid'
    ];
    $result = makeApiRequest($base_url . '/list', null, $invalid_headers);
    displayResult('GET /api/disable-reason/list (Invalid Headers)', $result);
    
    ?>
    
    <h2>Test Summary</h2>
    <p>The tests above should help identify any issues with the Disable Reason API endpoints.</p>
    <p><strong>Expected Results:</strong></p>
    <ul>
        <li>Test 1: Should return HTTP 200 with list of disable reasons</li>
        <li>Test 2: Should return HTTP 201 with created disable reason data</li>
        <li>Test 3: Should return HTTP 200 with specific disable reason data</li>
        <li>Test 4: Should return HTTP 200 with updated disable reason data</li>
        <li>Test 5: Should return HTTP 200 with deleted disable reason confirmation</li>
        <li>Test 6: Should return HTTP 401 with unauthorized error</li>
    </ul>
</body>
</html>
