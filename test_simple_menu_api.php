<?php
// Test the simplified menu API with POST staff_id

$staff_id = $_GET['staff_id'] ?? 1;

echo "<h1>Simplified Menu API Test</h1>";
echo "<h2>Testing for Staff ID: {$staff_id}</h2>";

// API endpoint
$api_url = "http://localhost/amt/api/teacher/menu";

// Request data
$request_data = array(
    'staff_id' => (int)$staff_id
);

echo "<h3>Request Details:</h3>";
echo "<p><strong>URL:</strong> {$api_url}</p>";
echo "<p><strong>Method:</strong> POST</p>";
echo "<p><strong>Content-Type:</strong> application/json</p>";
echo "<p><strong>Request Body:</strong></p>";
echo "<pre>" . json_encode($request_data, JSON_PRETTY_PRINT) . "</pre>";

// Make the API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "<h3>Response:</h3>";
echo "<p><strong>HTTP Status:</strong> {$http_code}</p>";

if ($error) {
    echo "<p style='color: red;'><strong>cURL Error:</strong> {$error}</p>";
} else {
    echo "<h4>Raw Response:</h4>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 3px;'>" . htmlspecialchars($response) . "</pre>";
    
    // Try to decode and format JSON
    $result = json_decode($response, true);
    if ($result) {
        echo "<h4>Formatted Response:</h4>";
        
        if ($result['status'] == 1) {
            echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 3px; color: #155724;'>";
            echo "<h5>✅ Success!</h5>";
            echo "<p><strong>Staff:</strong> " . $result['data']['staff_info']['full_name'] . " (ID: " . $result['data']['staff_info']['id'] . ")</p>";
            echo "<p><strong>Employee ID:</strong> " . $result['data']['staff_info']['employee_id'] . "</p>";
            echo "<p><strong>Role:</strong> " . $result['data']['role']['name'] . "</p>";
            echo "<p><strong>Total Menus:</strong> " . $result['data']['total_menus'] . "</p>";
            echo "</div>";
            
            if (!empty($result['data']['menus'])) {
                echo "<h5>Available Menus:</h5>";
                echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
                echo "<tr style='background: #f8f9fa;'><th>Menu</th><th>Icon</th><th>Level</th><th>Submenus</th></tr>";
                
                foreach ($result['data']['menus'] as $menu) {
                    echo "<tr>";
                    echo "<td><strong>" . htmlspecialchars($menu['menu']) . "</strong></td>";
                    echo "<td>" . htmlspecialchars($menu['icon']) . "</td>";
                    echo "<td>" . $menu['level'] . "</td>";
                    echo "<td>";
                    if (!empty($menu['submenus'])) {
                        echo "<ul style='margin: 0; padding-left: 15px;'>";
                        foreach ($menu['submenus'] as $submenu) {
                            echo "<li>" . htmlspecialchars($submenu['menu']) . " <em>(" . htmlspecialchars($submenu['url']) . ")</em></li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<em>No submenus</em>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 3px; color: #856404; margin-top: 10px;'>";
                echo "<p><strong>⚠️ No menus found for this staff member.</strong></p>";
                echo "<p>This could be due to:</p>";
                echo "<ul>";
                echo "<li>Staff member has no role assigned</li>";
                echo "<li>Role has no permissions</li>";
                echo "<li>Menu system not configured</li>";
                echo "</ul>";
                echo "</div>";
            }
        } else {
            echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 3px; color: #721c24;'>";
            echo "<h5>❌ Error</h5>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($result['message']) . "</p>";
            if (isset($result['error'])) {
                echo "<p><strong>Error:</strong> " . htmlspecialchars($result['error']) . "</p>";
            }
            echo "</div>";
        }
        
        echo "<h4>Complete JSON Response:</h4>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 3px; max-height: 400px; overflow: auto;'>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<p style='color: red;'>Invalid JSON response</p>";
    }
}

// Test with different staff IDs
echo "<hr>";
echo "<h3>Test with Different Staff IDs:</h3>";
for ($i = 1; $i <= 10; $i++) {
    $current = ($i == $staff_id) ? " <strong>(current)</strong>" : "";
    echo "<a href='?staff_id={$i}' style='display: inline-block; margin: 5px; padding: 5px 10px; background: " . ($i == $staff_id ? "#007cba" : "#6c757d") . "; color: white; text-decoration: none; border-radius: 3px;'>Staff {$i}{$current}</a>";
}

echo "<hr>";
echo "<h3>cURL Command Example:</h3>";
echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 3px;'>";
echo "curl -X POST http://localhost/amt/api/teacher/menu \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\"staff_id\": " . $staff_id . "}'";
echo "</pre>";

echo "<hr>";
echo "<h3>JavaScript Fetch Example:</h3>";
echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #ddd; border-radius: 3px;'>";
echo "fetch('http://localhost/amt/api/teacher/menu', {\n";
echo "  method: 'POST',\n";
echo "  headers: {\n";
echo "    'Content-Type': 'application/json'\n";
echo "  },\n";
echo "  body: JSON.stringify({staff_id: " . $staff_id . "})\n";
echo "})\n";
echo ".then(response => response.json())\n";
echo ".then(data => console.log(data));";
echo "</pre>";

echo "<p style='margin-top: 20px;'>";
echo "<a href='check_menu_tables.php' style='margin-right: 10px;'>Check Database Tables</a>";
echo "<a href='debug_menu_permissions.php?staff_id={$staff_id}' style='margin-right: 10px;'>Debug Permissions</a>";
echo "<a href='menu_api_dashboard.html'>Back to Dashboard</a>";
echo "</p>";
?>