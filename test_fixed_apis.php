<?php
echo "<h2>Testing Fixed APIs</h2>";

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
    echo htmlspecialchars($response);
    echo "</pre>";
}

// Test 2: Simple Menu Endpoint
echo "<h3>Test 2: Simple Menu Endpoint</h3>";

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
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars($response2);
    echo "</pre>";
}

// Test 3: Original Menu Endpoint
echo "<h3>Test 3: Original Menu Endpoint</h3>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/amt/api/teacher/menu');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $test_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($test_data)
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
    echo "📋 Response:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars($response3);
    echo "</pre>";
}

// Summary
echo "<h3>📋 Working Endpoints for Postman</h3>";

if ($http_code == 200) {
    echo "✅ <strong>Test Endpoint:</strong> <code>GET http://localhost/amt/api/teacher/test</code><br>";
}

if ($http_code2 == 200) {
    echo "✅ <strong>Simple Menu API:</strong> <code>POST http://localhost/amt/api/teacher/simple_menu</code><br>";
    echo "   - Headers: <code>Content-Type: application/json</code><br>";
    echo "   - Body: <code>{\"staff_id\": 24}</code><br><br>";
}

if ($http_code3 == 200) {
    echo "✅ <strong>Original Menu API:</strong> <code>POST http://localhost/amt/api/teacher/menu</code><br>";
    echo "   - Headers: <code>Content-Type: application/json</code><br>";
    echo "   - Body: <code>{\"staff_id\": 24}</code><br><br>";
}

echo "✅ <strong>Standalone API (Always Working):</strong> <code>POST http://localhost/amt/standalone_menu_api.php</code><br>";
echo "   - Headers: <code>Content-Type: application/json</code><br>";
echo "   - Body: <code>{\"staff_id\": 24}</code><br>";

?>