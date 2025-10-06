<?php
/**
 * Fix Base URL Configuration for Localhost Testing
 * This will temporarily set the correct base URL for localhost testing
 */

// Read the current config file
$config_file = __DIR__ . '/api/application/config/config.php';
$config_content = file_get_contents($config_file);

// Check current base URL
if (strpos($config_content, "https://school.cyberdetox.in/api/") !== false) {
    echo "<h2>🔧 Base URL Configuration Fix</h2>";
    echo "<p>Current base URL is set to production server. We need to fix this for localhost testing.</p>";
    
    // Create a backup
    $backup_file = $config_file . '.backup.' . date('Y-m-d-H-i-s');
    copy($config_file, $backup_file);
    echo "<p>✅ Backup created: " . basename($backup_file) . "</p>";
    
    // Replace the base URL
    $new_config_content = str_replace(
        "\$config['base_url'] = 'https://school.cyberdetox.in/api/';",
        "\$config['base_url'] = 'http://localhost/amt/api/';",
        $config_content
    );
    
    if ($new_config_content !== $config_content) {
        file_put_contents($config_file, $new_config_content);
        echo "<p>✅ Base URL updated to: <code>http://localhost/amt/api/</code></p>";
        echo "<p>🧪 Now test your API again in Postman!</p>";
        
        // Test the API immediately
        echo "<h3>🧪 Testing API Now...</h3>";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => "http://localhost/amt/api/teacher/menu",
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['staff_id' => 1]),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Client-Service: smartschool',
                'Auth-Key: schoolAdmin@'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "<p style='color: red;'>❌ cURL Error: $error</p>";
        } else {
            echo "<p><strong>HTTP Status:</strong> $http_code</p>";
            
            if ($http_code == 200) {
                echo "<p style='color: green;'>✅ <strong>SUCCESS!</strong> API is now working!</p>";
                
                $result = json_decode($response, true);
                if ($result && isset($result['data'])) {
                    echo "<p><strong>Response Summary:</strong></p>";
                    echo "<ul>";
                    echo "<li>Status: " . ($result['status'] ?? 'Unknown') . "</li>";
                    echo "<li>Message: " . ($result['message'] ?? 'No message') . "</li>";
                    if (isset($result['data']['total_menus'])) {
                        echo "<li>Total Menus: " . $result['data']['total_menus'] . "</li>";
                    }
                    if (isset($result['data']['role']['name'])) {
                        echo "<li>Role: " . $result['data']['role']['name'] . "</li>";
                        echo "<li>Is Superadmin: " . ($result['data']['role']['is_superadmin'] ? 'Yes' : 'No') . "</li>";
                    }
                    echo "</ul>";
                }
                
                echo "<details>";
                echo "<summary>🔍 View Full Response</summary>";
                echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; max-height: 400px; overflow-y: auto;'>";
                echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT));
                echo "</pre>";
                echo "</details>";
                
            } else {
                echo "<p style='color: red;'>❌ API Error (HTTP $http_code)</p>";
                echo "<pre style='background: #ffebee; padding: 10px; border: 1px solid #f44336;'>";
                echo htmlspecialchars($response);
                echo "</pre>";
            }
        }
        
    } else {
        echo "<p style='color: orange;'>⚠️ No changes needed - base URL not found or already correct</p>";
    }
    
    echo "<hr>";
    echo "<h3>📮 Updated Postman Settings</h3>";
    echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 8px;'>";
    echo "<p><strong>Use these settings in Postman:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Method:</strong> POST</li>";
    echo "<li><strong>URL:</strong> <code>http://localhost/amt/api/teacher/menu</code></li>";
    echo "<li><strong>Headers:</strong>";
    echo "<ul>";
    echo "<li><code>Content-Type: application/json</code></li>";
    echo "<li><code>Client-Service: smartschool</code> (optional)</li>";
    echo "<li><code>Auth-Key: schoolAdmin@</code> (optional)</li>";
    echo "</ul>";
    echo "</li>";
    echo "<li><strong>Body (raw JSON):</strong>";
    echo "<pre><code>{\"staff_id\": 1}</code></pre>";
    echo "</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>🔄 Restore Original Configuration</h3>";
    echo "<p>When you're done testing, you can restore the original configuration:</p>";
    echo "<button onclick=\"restoreConfig()\">🔄 Restore Production Config</button>";
    echo "<script>";
    echo "function restoreConfig() {";
    echo "  if (confirm('Restore original production configuration?')) {";
    echo "    fetch('restore_config.php', {method: 'POST'})";
    echo "    .then(response => response.text())";
    echo "    .then(data => { alert(data); location.reload(); });";
    echo "  }";
    echo "}";
    echo "</script>";
    
} else {
    echo "<h2>✅ Configuration Check</h2>";
    echo "<p>Base URL appears to be correctly configured for localhost.</p>";
    
    // Test the API
    echo "<h3>🧪 Testing API...</h3>";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "http://localhost/amt/api/teacher/menu",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode(['staff_id' => 1]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<p><strong>HTTP Status:</strong> $http_code</p>";
    
    if ($error) {
        echo "<p style='color: red;'>❌ Error: $error</p>";
    } else if ($http_code == 200) {
        echo "<p style='color: green;'>✅ API is working!</p>";
    } else {
        echo "<p style='color: red;'>❌ API returned HTTP $http_code</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
button { padding: 10px 15px; background: #007cba; color: white; border: none; border-radius: 4px; cursor: pointer; }
button:hover { background: #005a87; }
</style>