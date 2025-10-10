<?php
/**
 * Test script for Fee Collection Filters API
 * 
 * This script tests the fee collection filters API endpoint to ensure it's working correctly.
 */

// API base URL
$base_url = 'http://localhost/amt/api/fee-collection-filters';

// Required headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

/**
 * Make API request
 */
function makeApiRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

/**
 * Display test result
 */
function displayResult($test_name, $result) {
    echo "<div style='margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px;'>";
    echo "<h3>$test_name</h3>";
    echo "<p><strong>HTTP Code:</strong> <span class='" . ($result['http_code'] == 200 ? 'success' : 'error') . "'>" . $result['http_code'] . "</span></p>";
    echo "<p><strong>Response:</strong></p>";
    echo "<pre>" . json_encode($result['response'], JSON_PRETTY_PRINT) . "</pre>";
    echo "</div>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fee Collection Filters API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        h3 { color: #444; margin: 0; }
        pre { background: #fff; padding: 10px; border-radius: 4px; overflow-x: auto; border: 1px solid #e0e0e0; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196F3; }
        .summary { background: #fff; padding: 20px; border-radius: 5px; margin: 20px 0; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <h1>Fee Collection Filters API Test Results</h1>
    
    <div class="info">
        <strong>ℹ️ Test Information:</strong><br>
        This test suite validates the Fee Collection Filters API endpoint.<br>
        <strong>Base URL:</strong> <?php echo $base_url; ?><br>
        <strong>Endpoint:</strong> POST /fee-collection-filters/get
    </div>
    
    <?php
    
    // Test 1: Get all filter options (empty request)
    echo "<h2>Test 1: Get All Filter Options (Empty Request)</h2>";
    echo "<p>Testing with empty request body {} to get all available filter options.</p>";
    $result = makeApiRequest($base_url . '/get', [], $headers);
    displayResult('GET /api/fee-collection-filters/get (Empty Request)', $result);
    
    // Store session_id and class_id for subsequent tests
    $session_id = null;
    $class_id = null;
    
    if (isset($result['response']['data']['sessions']) && count($result['response']['data']['sessions']) > 0) {
        $session_id = $result['response']['data']['sessions'][0]['id'];
        echo "<div class='info'>✅ Found session ID: $session_id for next test</div>";
    }
    
    if (isset($result['response']['data']['classes']) && count($result['response']['data']['classes']) > 0) {
        $class_id = $result['response']['data']['classes'][0]['id'];
        echo "<div class='info'>✅ Found class ID: $class_id for next test</div>";
    }
    
    // Test 2: Filter classes by session
    if ($session_id) {
        echo "<h2>Test 2: Filter Classes by Session</h2>";
        echo "<p>Testing with session_id = $session_id to get classes for that session.</p>";
        $filter_data = ['session_id' => $session_id];
        $result = makeApiRequest($base_url . '/get', $filter_data, $headers);
        displayResult('GET /api/fee-collection-filters/get (With session_id)', $result);
    } else {
        echo "<h2>Test 2: Filter Classes by Session</h2>";
        echo "<div class='warning'>⚠️ Skipped - No session ID available from Test 1</div>";
    }
    
    // Test 3: Filter sections by class
    if ($session_id && $class_id) {
        echo "<h2>Test 3: Filter Sections by Class</h2>";
        echo "<p>Testing with session_id = $session_id and class_id = $class_id to get sections for that class.</p>";
        $filter_data = [
            'session_id' => $session_id,
            'class_id' => $class_id
        ];
        $result = makeApiRequest($base_url . '/get', $filter_data, $headers);
        displayResult('GET /api/fee-collection-filters/get (With session_id and class_id)', $result);
    } else {
        echo "<h2>Test 3: Filter Sections by Class</h2>";
        echo "<div class='warning'>⚠️ Skipped - No session ID or class ID available from previous tests</div>";
    }
    
    // Test 4: Test with invalid headers
    echo "<h2>Test 4: Test with Invalid Headers</h2>";
    echo "<p>Testing with invalid authentication headers to verify security.</p>";
    $invalid_headers = [
        'Content-Type: application/json',
        'Client-Service: invalid',
        'Auth-Key: invalid'
    ];
    $result = makeApiRequest($base_url . '/get', [], $invalid_headers);
    displayResult('GET /api/fee-collection-filters/get (Invalid Headers)', $result);
    
    // Test 5: Test with GET method (should fail)
    echo "<h2>Test 5: Test with GET Method</h2>";
    echo "<p>Testing with GET method instead of POST to verify method validation.</p>";
    $ch = curl_init($base_url . '/get');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $result = [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
    displayResult('GET /api/fee-collection-filters/get (GET Method)', $result);
    
    ?>
    
    <div class="summary">
        <h2>📋 Test Summary</h2>
        <p><strong>Total Tests:</strong> 5</p>
        <p><strong>Expected Results:</strong></p>
        <ul>
            <li>✅ Test 1: Should return HTTP 200 with all filter options</li>
            <li>✅ Test 2: Should return HTTP 200 with filtered classes</li>
            <li>✅ Test 3: Should return HTTP 200 with filtered sections</li>
            <li>✅ Test 4: Should return HTTP 401 (Unauthorized)</li>
            <li>✅ Test 5: Should return HTTP 405 (Method Not Allowed)</li>
        </ul>
    </div>
    
    <div class="info">
        <strong>📚 API Documentation:</strong><br>
        For complete API documentation, see: <code>api/documentation/FEE_COLLECTION_FILTERS_API_DOCUMENTATION.md</code>
    </div>
    
</body>
</html>

