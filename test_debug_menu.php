<?php
// Simple test for the debug menu API

$staff_id = $_GET['staff_id'] ?? 1;

echo "<h2>Testing Debug Menu API for Staff ID: {$staff_id}</h2>";

$debug_url = "http://localhost/amt/api/teacher/debug-menu?staff_id=" . $staff_id;

echo "<h3>Testing URL: <a href='{$debug_url}' target='_blank'>{$debug_url}</a></h3>";

// Make the API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $debug_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>Response (HTTP {$http_code}):</h3>";

if ($error) {
    echo "<p style='color: red;'>cURL Error: {$error}</p>";
} else {
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    
    // Try to decode and format JSON
    $result = json_decode($response, true);
    if ($result) {
        echo "<h3>Formatted Response:</h3>";
        echo "<pre>" . print_r($result, true) . "</pre>";
        
        // Show specific menu information
        if (isset($result['data']['menus'])) {
            echo "<h3>Menu Summary:</h3>";
            echo "<p>Total Menus: " . count($result['data']['menus']) . "</p>";
            
            if (isset($result['data']['role'])) {
                echo "<p>Staff Role: " . ($result['data']['role']->name ?? 'Unknown') . "</p>";
                echo "<p>Is Super Admin: " . ($result['data']['role']->is_superadmin ? 'Yes' : 'No') . "</p>";
            }
            
            echo "<h4>Available Menus:</h4>";
            echo "<ul>";
            foreach ($result['data']['menus'] as $menu) {
                echo "<li>";
                echo "<strong>" . $menu['menu'] . "</strong>";
                echo " (Level: " . $menu['level'] . ")";
                if (!empty($menu['submenus'])) {
                    echo "<ul>";
                    foreach ($menu['submenus'] as $submenu) {
                        echo "<li>" . $submenu['menu'] . " - " . $submenu['url'] . "</li>";
                    }
                    echo "</ul>";
                }
                echo "</li>";
            }
            echo "</ul>";
        }
    }
}

// Test with different staff IDs
echo "<h3>Test with different Staff IDs:</h3>";
for ($i = 1; $i <= 5; $i++) {
    echo "<a href='?staff_id={$i}' style='margin-right: 10px;'>Staff {$i}</a>";
}

echo "<p><a href='test_menu_api.php'>← Back to Staff List</a></p>";
echo "<p><a href='debug_menu_permissions.php?staff_id={$staff_id}'>← View Database Debug for this Staff</a></p>";
?>