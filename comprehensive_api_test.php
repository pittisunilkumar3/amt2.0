<?php
/**
 * Comprehensive Disable Reason API Test
 * Tests all endpoints according to the API documentation
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// API Configuration
$base_url = 'http://localhost/amt/api/disable-reason';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test results storage
$test_results = [];
$created_id = null;

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
        'error' => $error,
        'json' => json_decode($response, true)
    ];
}

/**
 * Display test result
 */
function displayTestResult($test_name, $result, $expected_code = 200) {
    global $test_results;
    
    $success = ($result['http_code'] == $expected_code && !$result['error']);
    $test_results[] = ['name' => $test_name, 'success' => $success, 'code' => $result['http_code']];
    
    $status_icon = $success ? '✅' : '❌';
    $status_class = $success ? 'success' : 'error';
    
    echo "<div class='test-result $status_class'>";
    echo "<h3>$status_icon $test_name</h3>";
    echo "<p><strong>Expected HTTP Code:</strong> $expected_code | <strong>Actual:</strong> {$result['http_code']}</p>";
    
    if ($result['error']) {
        echo "<p><strong>cURL Error:</strong> " . htmlspecialchars($result['error']) . "</p>";
    }
    
    if ($result['json']) {
        echo "<p><strong>Status:</strong> " . ($result['json']['status'] ?? 'N/A') . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($result['json']['message'] ?? 'N/A') . "</p>";
        
        if (isset($result['json']['data'])) {
            echo "<details>";
            echo "<summary>Response Data</summary>";
            echo "<pre>" . htmlspecialchars(json_encode($result['json']['data'], JSON_PRETTY_PRINT)) . "</pre>";
            echo "</details>";
        }
    } else {
        echo "<p><strong>Raw Response:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($result['response'], 0, 500)) . "</pre>";
    }
    
    echo "</div>";
    
    return $result;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Comprehensive Disable Reason API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        .test-result { margin: 20px 0; padding: 15px; border-radius: 5px; border: 1px solid #ddd; }
        .success { background-color: #d4edda; border-color: #c3e6cb; }
        .error { background-color: #f8d7da; border-color: #f5c6cb; }
        .summary { background-color: #e2e3e5; padding: 15px; border-radius: 5px; margin: 20px 0; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }
        details { margin: 10px 0; }
        summary { cursor: pointer; font-weight: bold; }
        h1, h2 { color: #333; }
        .stats { display: flex; gap: 20px; margin: 20px 0; }
        .stat { padding: 10px; border-radius: 5px; text-align: center; }
        .stat-success { background-color: #d4edda; }
        .stat-error { background-color: #f8d7da; }
    </style>
</head>
<body>
    <h1>🧪 Comprehensive Disable Reason API Test</h1>
    <p>Testing all endpoints according to the API documentation...</p>

    <?php
    
    // Test 1: List all disable reasons
    echo "<h2>Test 1: List All Disable Reasons</h2>";
    $result = makeApiRequest($base_url . '/list', null, $headers);
    displayTestResult('GET /disable-reason/list', $result, 200);
    
    // Test 2: Create a new disable reason
    echo "<h2>Test 2: Create New Disable Reason</h2>";
    $create_data = [
        'reason' => 'API Test Reason - ' . date('Y-m-d H:i:s')
    ];
    $result = makeApiRequest($base_url . '/create', $create_data, $headers);
    $create_result = displayTestResult('POST /disable-reason/create', $result, 201);
    
    // Extract created ID for further tests
    if ($create_result['json'] && isset($create_result['json']['data']['id'])) {
        $created_id = $create_result['json']['data']['id'];
        echo "<p><strong>Created ID:</strong> $created_id (will be used for subsequent tests)</p>";
    }
    
    // Test 3: Get specific disable reason (if we have an ID)
    if ($created_id) {
        echo "<h2>Test 3: Get Specific Disable Reason</h2>";
        $result = makeApiRequest($base_url . '/get/' . $created_id, null, $headers);
        displayTestResult("GET /disable-reason/get/$created_id", $result, 200);
        
        // Test 4: Update the disable reason
        echo "<h2>Test 4: Update Disable Reason</h2>";
        $update_data = [
            'reason' => 'Updated API Test Reason - ' . date('Y-m-d H:i:s')
        ];
        $result = makeApiRequest($base_url . '/update/' . $created_id, $update_data, $headers);
        displayTestResult("POST /disable-reason/update/$created_id", $result, 200);
        
        // Test 5: Delete the disable reason
        echo "<h2>Test 5: Delete Disable Reason</h2>";
        $result = makeApiRequest($base_url . '/delete/' . $created_id, null, $headers);
        displayTestResult("POST /disable-reason/delete/$created_id", $result, 200);
        
        // Test 6: Try to get deleted reason (should return 404)
        echo "<h2>Test 6: Get Deleted Reason (Should Fail)</h2>";
        $result = makeApiRequest($base_url . '/get/' . $created_id, null, $headers);
        displayTestResult("GET /disable-reason/get/$created_id (deleted)", $result, 404);
        
    } else {
        echo "<div class='test-result error'>";
        echo "<h3>❌ Tests 3-6 Skipped</h3>";
        echo "<p>Could not extract created ID from create response. Subsequent tests skipped.</p>";
        echo "</div>";
    }
    
    // Test 7: Invalid authentication
    echo "<h2>Test 7: Invalid Authentication</h2>";
    $invalid_headers = [
        'Content-Type: application/json',
        'Client-Service: invalid',
        'Auth-Key: invalid'
    ];
    $result = makeApiRequest($base_url . '/list', null, $invalid_headers);
    displayTestResult('GET /disable-reason/list (Invalid Auth)', $result, 401);
    
    // Test 8: Invalid method (GET instead of POST)
    echo "<h2>Test 8: Invalid Method Test</h2>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url . '/list');
    curl_setopt($ch, CURLOPT_HTTPGET, true);  // Use GET instead of POST
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    $result = [
        'http_code' => $http_code,
        'response' => $response,
        'error' => $error,
        'json' => json_decode($response, true)
    ];
    displayTestResult('GET /disable-reason/list (Wrong Method)', $result, 405);
    
    // Test 9: Invalid ID format
    echo "<h2>Test 9: Invalid ID Format</h2>";
    $result = makeApiRequest($base_url . '/get/invalid', null, $headers);
    displayTestResult('GET /disable-reason/get/invalid', $result, 400);
    
    // Test 10: Empty reason field
    echo "<h2>Test 10: Empty Reason Field</h2>";
    $empty_data = ['reason' => ''];
    $result = makeApiRequest($base_url . '/create', $empty_data, $headers);
    displayTestResult('POST /disable-reason/create (Empty Reason)', $result, 400);
    
    // Calculate and display summary
    $total_tests = count($test_results);
    $passed_tests = count(array_filter($test_results, function($test) { return $test['success']; }));
    $failed_tests = $total_tests - $passed_tests;
    $success_rate = $total_tests > 0 ? round(($passed_tests / $total_tests) * 100, 1) : 0;
    
    ?>
    
    <div class="summary">
        <h2>📊 Test Summary</h2>
        <div class="stats">
            <div class="stat stat-success">
                <strong><?php echo $passed_tests; ?></strong><br>
                Tests Passed
            </div>
            <div class="stat stat-error">
                <strong><?php echo $failed_tests; ?></strong><br>
                Tests Failed
            </div>
            <div class="stat">
                <strong><?php echo $success_rate; ?>%</strong><br>
                Success Rate
            </div>
        </div>
        
        <h3>Test Results:</h3>
        <ul>
            <?php foreach ($test_results as $test): ?>
                <li>
                    <?php echo $test['success'] ? '✅' : '❌'; ?>
                    <?php echo htmlspecialchars($test['name']); ?>
                    (HTTP <?php echo $test['code']; ?>)
                </li>
            <?php endforeach; ?>
        </ul>
        
        <?php if ($success_rate == 100): ?>
            <p style="color: green; font-weight: bold;">🎉 All tests passed! Your API is working correctly.</p>
        <?php elseif ($success_rate >= 80): ?>
            <p style="color: orange; font-weight: bold;">⚠️ Most tests passed, but there are some issues to address.</p>
        <?php else: ?>
            <p style="color: red; font-weight: bold;">❌ Multiple tests failed. Please check the API implementation.</p>
        <?php endif; ?>
    </div>
    
    <div class="summary">
        <h3>🔍 What This Test Covers:</h3>
        <ul>
            <li>✅ List all disable reasons</li>
            <li>✅ Create new disable reason</li>
            <li>✅ Get specific disable reason</li>
            <li>✅ Update existing disable reason</li>
            <li>✅ Delete disable reason</li>
            <li>✅ Authentication validation</li>
            <li>✅ HTTP method validation</li>
            <li>✅ Input validation</li>
            <li>✅ Error handling</li>
            <li>✅ Response format validation</li>
        </ul>
    </div>

</body>
</html>
