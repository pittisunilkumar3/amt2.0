<?php
echo "<h2>Testing APIs</h2>";

// Test 1: Standalone API
echo "<h3>Test 1: Standalone API</h3>";

$test_data = json_encode(['staff_id' => 24]);

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

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error<br>";
} else {
    echo "✅ HTTP Code: $http_code<br>";
    echo "📋 Response:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars($response);
    echo "</pre>";
}

// Test 2: CodeIgniter API (now with development mode enabled)
echo "<h3>Test 2: CodeIgniter API (Development Mode)</h3>";

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

// Summary
echo "<h3>Summary</h3>";
if ($http_code == 200) {
    echo "✅ Standalone API is working perfectly!<br>";
    echo "🔗 Use this URL for testing: <strong>http://localhost/amt/standalone_menu_api.php</strong><br>";
}

if ($http_code2 == 200) {
    echo "✅ CodeIgniter API is also working!<br>";
    echo "🔗 Use this URL for testing: <strong>http://localhost/amt/api/teacher/menu</strong><br>";
} else {
    echo "❌ CodeIgniter API has issues - use the standalone version for testing<br>";
}

echo "<br><strong>📋 Test with Postman:</strong><br>";
echo "URL: <code>http://localhost/amt/standalone_menu_api.php</code><br>";
echo "Method: <code>POST</code><br>";
echo "Headers: <code>Content-Type: application/json</code><br>";
echo "Body: <code>{\"staff_id\": 24}</code><br>";

?>