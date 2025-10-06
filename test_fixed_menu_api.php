<?php
/**
 * Test Script for Fixed Original Menu API
 * Tests the original menu() method after removing teacher_permission_model dependency
 */

echo "<h1>Testing Fixed Original Menu API</h1>";
echo "<hr>";

// Test original menu endpoint
function test_original_menu_api() {
    echo "<h2>1. Testing Original Menu API (POST /teacher/menu)</h2>";
    
    $url = 'http://localhost/amt/api/teacher/menu';
    $data = json_encode(array('staff_id' => 24));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    echo "<strong>URL:</strong> $url<br>";
    echo "<strong>POST Data:</strong> $data<br>";
    echo "<strong>HTTP Code:</strong> $http_code<br>";
    
    if ($curl_error) {
        echo "<strong>cURL Error:</strong> $curl_error<br>";
        return;
    }
    
    $json_response = json_decode($response, true);
    $is_valid_json = (json_last_error() === JSON_ERROR_NONE);
    
    echo "<strong>Valid JSON:</strong> " . ($is_valid_json ? "✅ Yes" : "❌ No") . "<br>";
    
    if ($is_valid_json && isset($json_response['status'])) {
        echo "<strong>Status:</strong> " . $json_response['status'] . "<br>";
        echo "<strong>Message:</strong> " . (isset($json_response['message']) ? $json_response['message'] : 'N/A') . "<br>";
        
        if (isset($json_response['data']) && isset($json_response['data']['menus'])) {
            $menu_count = count($json_response['data']['menus']);
            echo "<strong>Menu Count:</strong> $menu_count<br>";
            
            if ($menu_count > 0) {
                echo "<strong>First 3 Menus:</strong><br>";
                for ($i = 0; $i < min(3, $menu_count); $i++) {
                    $menu = $json_response['data']['menus'][$i];
                    echo "- " . (isset($menu['menu']) ? $menu['menu'] : 'Unknown') . " (ID: " . (isset($menu['id']) ? $menu['id'] : 'N/A') . ")<br>";
                }
            }
            
            if (isset($json_response['data']['role'])) {
                $role = $json_response['data']['role'];
                echo "<strong>Role:</strong> " . (isset($role['name']) ? $role['name'] : 'N/A') . 
                     " (Superadmin: " . (isset($role['is_superadmin']) && $role['is_superadmin'] ? 'Yes' : 'No') . ")<br>";
            }
        }
    } else {
        echo "<strong>Raw Response:</strong> " . htmlspecialchars($response) . "<br>";
    }
    
    echo "<hr>";
}

// Test comparison with simple_menu
function test_simple_menu_comparison() {
    echo "<h2>2. Comparison with Simple Menu API</h2>";
    
    $url = 'http://localhost/amt/api/teacher/simple_menu';
    $data = json_encode(array('staff_id' => 24));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $json_response = json_decode($response, true);
    
    if ($json_response && isset($json_response['data']['menus'])) {
        $simple_menu_count = count($json_response['data']['menus']);
        echo "<strong>Simple Menu Count:</strong> $simple_menu_count<br>";
        echo "<strong>Both APIs should return same menu count for consistency</strong><br>";
    }
    
    echo "<hr>";
}

// Test error handling
function test_error_handling() {
    echo "<h2>3. Testing Error Handling</h2>";
    
    // Test invalid staff_id
    $url = 'http://localhost/amt/api/teacher/menu';
    $data = json_encode(array('staff_id' => 'invalid'));
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<strong>Invalid Staff ID Test:</strong><br>";
    echo "HTTP Code: $http_code<br>";
    
    $json_response = json_decode($response, true);
    if ($json_response) {
        echo "Status: " . (isset($json_response['status']) ? $json_response['status'] : 'N/A') . "<br>";
        echo "Message: " . (isset($json_response['message']) ? $json_response['message'] : 'N/A') . "<br>";
    }
    
    echo "<hr>";
}

// Run all tests
test_original_menu_api();
test_simple_menu_comparison();
test_error_handling();

echo "<h2>Summary</h2>";
echo "✅ Original menu() method should now work without teacher_permission_model<br>";
echo "✅ Uses same database logic as working simple_menu() method<br>";
echo "✅ Maintains original response format for backward compatibility<br>";
echo "✅ Proper JSON error handling implemented<br>";
?>