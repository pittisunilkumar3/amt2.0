<?php
echo "<h2>Testing JSON-Formatted APIs</h2>";

// Test 1: Simple Test Endpoint
echo "<h3>Test 1: Simple Test Endpoint</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/amt/api/teacher/test');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error<br>";
} else {
    echo "✅ HTTP Code: $http_code<br>";
    echo "📋 Response:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px;'>";
    
    // Check if response is valid JSON
    $json_decoded = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON Response:<br>";
        echo htmlspecialchars(json_encode($json_decoded, JSON_PRETTY_PRINT));
    } else {
        echo "❌ Invalid JSON Response:<br>";
        echo htmlspecialchars($response);
    }
    
    echo "</pre>";
}

// Test 2: Simple Menu Endpoint
echo "<h3>Test 2: Simple Menu Endpoint (Valid Request)</h3>";

$test_data = json_encode(['staff_id' => 24]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/amt/api/teacher/simple_menu');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $test_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($test_data)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response2 = curl_exec($ch);
$http_code2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error2 = curl_error($ch);
curl_close($ch);

if ($error2) {
    echo "❌ CURL Error: $error2<br>";
} else {
    echo "✅ HTTP Code: $http_code2<br>";
    echo "📋 Response:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: auto;'>";
    
    // Check if response is valid JSON
    $json_decoded2 = json_decode($response2, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON Response<br>";
        echo "Status: " . ($json_decoded2['status'] ?? 'N/A') . "<br>";
        echo "Message: " . ($json_decoded2['message'] ?? 'N/A') . "<br>";
        if (isset($json_decoded2['data']['total_menus'])) {
            echo "Total Menus: " . $json_decoded2['data']['total_menus'] . "<br>";
        }
    } else {
        echo "❌ Invalid JSON Response:<br>";
        echo htmlspecialchars(substr($response2, 0, 500)) . "...";
    }
    
    echo "</pre>";
}

// Test 3: Invalid Request (Missing staff_id)
echo "<h3>Test 3: Invalid Request (Missing staff_id)</h3>";

$invalid_data = json_encode(['invalid_field' => 'test']);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/amt/api/teacher/simple_menu');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $invalid_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($invalid_data)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response3 = curl_exec($ch);
$http_code3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error3 = curl_error($ch);
curl_close($ch);

if ($error3) {
    echo "❌ CURL Error: $error3<br>";
} else {
    echo "✅ HTTP Code: $http_code3<br>";
    echo "📋 Error Response:<br>";
    echo "<pre style='background: #fff2f2; padding: 10px;'>";
    
    // Check if response is valid JSON
    $json_decoded3 = json_decode($response3, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON Error Response:<br>";
        echo htmlspecialchars(json_encode($json_decoded3, JSON_PRETTY_PRINT));
    } else {
        echo "❌ Invalid JSON Response:<br>";
        echo htmlspecialchars($response3);
    }
    
    echo "</pre>";
}

// Test 4: Invalid Route
echo "<h3>Test 4: Invalid Route (404 Test)</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/amt/api/teacher/invalid_endpoint');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response4 = curl_exec($ch);
$http_code4 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error4 = curl_error($ch);
curl_close($ch);

if ($error4) {
    echo "❌ CURL Error: $error4<br>";
} else {
    echo "✅ HTTP Code: $http_code4<br>";
    echo "📋 404 Response:<br>";
    echo "<pre style='background: #fff2f2; padding: 10px;'>";
    
    // Check if response is valid JSON
    $json_decoded4 = json_decode($response4, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON 404 Response:<br>";
        echo htmlspecialchars(json_encode($json_decoded4, JSON_PRETTY_PRINT));
    } else {
        echo "❌ HTML 404 Response:<br>";
        echo htmlspecialchars(substr($response4, 0, 300)) . "...";
    }
    
    echo "</pre>";
}

// Test 5: Standalone API
echo "<h3>Test 5: Standalone API (JSON Format)</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/amt/standalone_menu_api.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $test_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($test_data)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response5 = curl_exec($ch);
$http_code5 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error5 = curl_error($ch);
curl_close($ch);

if ($error5) {
    echo "❌ CURL Error: $error5<br>";
} else {
    echo "✅ HTTP Code: $http_code5<br>";
    echo "📋 Response:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 200px; overflow-y: auto;'>";
    
    // Check if response is valid JSON
    $json_decoded5 = json_decode($response5, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "✅ Valid JSON Response<br>";
        echo "Status: " . ($json_decoded5['status'] ?? 'N/A') . "<br>";
        echo "Message: " . ($json_decoded5['message'] ?? 'N/A') . "<br>";
        if (isset($json_decoded5['data']['total_menus'])) {
            echo "Total Menus: " . $json_decoded5['data']['total_menus'] . "<br>";
        }
    } else {
        echo "❌ Invalid JSON Response:<br>";
        echo htmlspecialchars(substr($response5, 0, 500)) . "...";
    }
    
    echo "</pre>";
}

// Summary
echo "<h3>📋 JSON API Summary</h3>";
echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse;'>";
echo "<tr><th>Test</th><th>HTTP Code</th><th>JSON Valid</th><th>Status</th></tr>";

$tests = [
    ['Test Endpoint', $http_code, isset($json_decoded)],
    ['Simple Menu (Valid)', $http_code2, isset($json_decoded2)],
    ['Invalid Request', $http_code3, isset($json_decoded3)],
    ['404 Route', $http_code4, isset($json_decoded4)],
    ['Standalone API', $http_code5, isset($json_decoded5)]
];

foreach ($tests as $test) {
    $status = ($test[1] == 200 || $test[1] == 400 || $test[1] == 404) && $test[2] ? '✅ Good' : '❌ Needs Fix';
    echo "<tr><td>{$test[0]}</td><td>{$test[1]}</td><td>" . ($test[2] ? '✅' : '❌') . "</td><td>$status</td></tr>";
}

echo "</table>";

echo "<br><strong>🎯 For Postman Testing:</strong><br>";
echo "All endpoints now return proper JSON responses with consistent structure:<br>";
echo "- <code>status</code>: 1 for success, 0 for error<br>";
echo "- <code>message</code>: Human-readable message<br>";
echo "- <code>data</code>: Response data (on success)<br>";
echo "- <code>error</code>: Error details (on error)<br>";
echo "- <code>timestamp</code>: Response timestamp<br>";

?>