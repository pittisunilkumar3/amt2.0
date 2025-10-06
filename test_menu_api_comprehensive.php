<?php
/**
 * Comprehensive Menu API Test
 * Tests the menu API functionality thoroughly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🧪 Comprehensive Menu API Test</h2>";

// Test different staff IDs
$test_cases = [
    ['staff_id' => 1, 'description' => 'Staff ID 1'],
    ['staff_id' => 2, 'description' => 'Staff ID 2'], 
    ['staff_id' => 10, 'description' => 'Staff ID 10'],
    ['staff_id' => 999, 'description' => 'Non-existent Staff ID'],
];

// Add any superadmin IDs we can find
$pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
$stmt = $pdo->query("
    SELECT DISTINCT s.id 
    FROM staff s
    JOIN staff_roles sr ON sr.staff_id = s.id
    JOIN roles r ON r.id = sr.role_id
    WHERE r.is_superadmin = 1 AND s.is_active = 1
    LIMIT 3
");
$superadmins = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($superadmins as $admin_id) {
    $test_cases[] = ['staff_id' => $admin_id, 'description' => "Superadmin Staff ID $admin_id"];
}

foreach ($test_cases as $test) {
    echo "<h3>🔍 Testing: {$test['description']}</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    
    $url = "http://localhost/amt/api/teacher/menu";
    $data = json_encode(['staff_id' => $test['staff_id']]);
    
    echo "<p><strong>Request:</strong> POST $url</p>";
    echo "<p><strong>Body:</strong> $data</p>";
    
    // Make the API call
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    echo "<p><strong>HTTP Code:</strong> $http_code</p>";
    
    if ($curl_error) {
        echo "<p style='color: red;'><strong>cURL Error:</strong> $curl_error</p>";
    } else {
        $result = json_decode($response, true);
        
        if ($result) {
            if ($result['status'] == 1) {
                echo "<p style='color: green;'><strong>✅ Success!</strong></p>";
                
                if (isset($result['data'])) {
                    $data = $result['data'];
                    echo "<p><strong>Staff:</strong> " . ($data['staff_info']['full_name'] ?? 'Unknown') . "</p>";
                    echo "<p><strong>Role:</strong> " . ($data['role']['name'] ?? 'No Role') . " (ID: " . ($data['role']['id'] ?? 'N/A') . ")</p>";
                    echo "<p><strong>Is Superadmin:</strong> " . ($data['role']['is_superadmin'] ? '✅ Yes' : '❌ No') . "</p>";
                    echo "<p><strong>Total Menus:</strong> " . ($data['total_menus'] ?? 0) . "</p>";
                    
                    if (isset($data['menus']) && is_array($data['menus'])) {
                        echo "<p><strong>Menu List:</strong></p>";
                        echo "<ul>";
                        foreach ($data['menus'] as $menu) {
                            $submenu_count = isset($menu['submenus']) ? count($menu['submenus']) : 0;
                            echo "<li>{$menu['menu']} (ID: {$menu['id']}) - $submenu_count submenus</li>";
                        }
                        echo "</ul>";
                    }
                }
            } else {
                echo "<p style='color: red;'><strong>❌ API Error:</strong> " . ($result['message'] ?? 'Unknown error') . "</p>";
            }
            
            echo "<details style='margin-top: 10px;'>";
            echo "<summary>🔍 View Full Response</summary>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow-x: auto; max-height: 300px;'>";
            echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT));
            echo "</pre>";
            echo "</details>";
        } else {
            echo "<p style='color: red;'><strong>❌ Invalid JSON Response:</strong></p>";
            echo "<pre style='background: #ffebee; padding: 10px; border: 1px solid #f44336;'>";
            echo htmlspecialchars($response);
            echo "</pre>";
        }
    }
    
    echo "</div>";
}

// Show database info for context
echo "<h3>📋 Database Context</h3>";
echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";

// Show roles
echo "<h4>Roles:</h4>";
$stmt = $pdo->query("SELECT id, name, slug, is_superadmin, is_active FROM roles ORDER BY id");
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Superadmin</th><th>Active</th></tr>";
foreach ($roles as $role) {
    $highlight = $role['is_superadmin'] ? 'style="background-color: #ffeb3b;"' : '';
    echo "<tr $highlight>";
    echo "<td>{$role['id']}</td>";
    echo "<td>{$role['name']}</td>";
    echo "<td>{$role['slug']}</td>";
    echo "<td>" . ($role['is_superadmin'] ? 'Yes' : 'No') . "</td>";
    echo "<td>" . ($role['is_active'] ? 'Yes' : 'No') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Show staff with roles
echo "<h4>Staff with Roles (first 10):</h4>";
$stmt = $pdo->query("
    SELECT s.id, s.name, s.surname, s.employee_id, r.name as role_name, r.is_superadmin
    FROM staff s
    LEFT JOIN staff_roles sr ON sr.staff_id = s.id
    LEFT JOIN roles r ON r.id = sr.role_id
    WHERE s.is_active = 1
    ORDER BY r.is_superadmin DESC, s.id
    LIMIT 10
");
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Name</th><th>Employee ID</th><th>Role</th><th>Superadmin</th></tr>";
foreach ($staff as $member) {
    $highlight = $member['is_superadmin'] ? 'style="background-color: #e8f5e8;"' : '';
    echo "<tr $highlight>";
    echo "<td>{$member['id']}</td>";
    echo "<td>{$member['name']} {$member['surname']}</td>";
    echo "<td>{$member['employee_id']}</td>";
    echo "<td>" . ($member['role_name'] ?: 'No Role') . "</td>";
    echo "<td>" . ($member['is_superadmin'] ? 'Yes' : 'No') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background-color: #f0f0f0; }
</style>