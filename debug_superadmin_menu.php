<?php
/**
 * Debug Superadmin Menu Access
 * This script tests superadmin role and menu access
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amt";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2>🔍 Superadmin Menu Debug Tool</h2>";
} catch(PDOException $e) {
    die("<h2>❌ Connection failed: " . $e->getMessage() . "</h2>");
}

// Test specific staff ID
$test_staff_id = isset($_GET['staff_id']) ? (int)$_GET['staff_id'] : null;

if ($test_staff_id) {
    echo "<h3>Testing Staff ID: $test_staff_id</h3>";
    
    // 1. Check if staff exists and is active
    echo "<h4>1. Staff Information:</h4>";
    $stmt = $pdo->prepare("SELECT id, name, surname, employee_id, is_active FROM staff WHERE id = ?");
    $stmt->execute([$test_staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($staff) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($staff as $key => $value) {
            echo "<tr><td>$key</td><td>" . ($value ? $value : 'NULL') . "</td></tr>";
        }
        echo "</table>";
        
        if ($staff['is_active'] != 1) {
            echo "<p style='color: red;'>⚠️ Staff is not active!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Staff not found!</p>";
        exit;
    }
    
    // 2. Check staff role assignment
    echo "<h4>2. Role Assignment:</h4>";
    $stmt = $pdo->prepare("
        SELECT sr.staff_id, sr.role_id, sr.is_active as role_active, 
               r.id, r.name, r.slug, r.is_superadmin, r.is_active as role_table_active
        FROM staff_roles sr 
        JOIN roles r ON r.id = sr.role_id 
        WHERE sr.staff_id = ?
    ");
    $stmt->execute([$test_staff_id]);
    $role_assignment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($role_assignment) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($role_assignment as $key => $value) {
            $display_value = $value;
            if ($key == 'is_superadmin' || $key == 'role_active' || $key == 'role_table_active') {
                $display_value = $value ? '✅ Yes' : '❌ No';
            }
            echo "<tr><td>$key</td><td>$display_value</td></tr>";
        }
        echo "</table>";
        
        // Highlight superadmin status
        if ($role_assignment['is_superadmin'] == 1) {
            echo "<p style='color: green; font-weight: bold;'>🔐 This user IS a superadmin!</p>";
        } else {
            echo "<p style='color: orange;'>👤 This user is NOT a superadmin (role_id: {$role_assignment['role_id']})</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ No role assigned to this staff member!</p>";
    }
    
    // 3. Check all roles in the system (to see role ID 7)
    echo "<h4>3. All Roles in System:</h4>";
    $stmt = $pdo->query("SELECT id, name, slug, is_superadmin, is_active FROM roles ORDER BY id");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($roles) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Slug</th><th>Is Superadmin</th><th>Is Active</th></tr>";
        foreach ($roles as $role) {
            $superadmin_display = $role['is_superadmin'] ? '✅ Yes' : '❌ No';
            $active_display = $role['is_active'] ? '✅ Yes' : '❌ No';
            $highlight = ($role['id'] == 7 || $role['is_superadmin'] == 1) ? 'style="background-color: #ffeb3b;"' : '';
            echo "<tr $highlight>";
            echo "<td>{$role['id']}</td>";
            echo "<td>{$role['name']}</td>";
            echo "<td>{$role['slug']}</td>";
            echo "<td>$superadmin_display</td>";
            echo "<td>$active_display</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 4. Check menu access using the Teacher_permission_model logic
    echo "<h4>4. Menu Access Test:</h4>";
    
    if ($role_assignment) {
        // Simulate the Teacher_permission_model getTeacherMenus logic
        $stmt = $pdo->query("
            SELECT sm.id, sm.menu, sm.icon, sm.activate_menu, sm.lang_key, 
                   sm.level, sm.access_permissions, sm.is_active, sm.sidebar_display,
                   pg.short_code as permission_group, pg.name as permission_group_name
            FROM sidebar_menus sm
            LEFT JOIN permission_group pg ON pg.id = sm.permission_group_id
            WHERE sm.is_active = 1 AND sm.sidebar_display = 1
            ORDER BY sm.level
        ");
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($menus) {
            echo "<p><strong>Total available menus:</strong> " . count($menus) . "</p>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Menu</th><th>Permission Group</th><th>Access for Superadmin</th><th>Access Logic</th></tr>";
            
            foreach ($menus as $menu) {
                $access_logic = "";
                $has_access = false;
                
                // Simulate hasMenuAccess logic
                if ($role_assignment['is_superadmin'] == 1) {
                    $has_access = true;
                    $access_logic = "✅ Superadmin (has access to everything)";
                } else if (empty($menu['permission_group'])) {
                    $has_access = true;
                    $access_logic = "✅ No permission group required";
                } else {
                    // Would need to check hasPermissionInGroup
                    $access_logic = "❓ Need to check permission group: " . $menu['permission_group'];
                }
                
                $access_display = $has_access ? '✅ Yes' : '❌ No';
                $highlight = $has_access ? 'style="background-color: #c8e6c9;"' : 'style="background-color: #ffcdd2;"';
                
                echo "<tr $highlight>";
                echo "<td>{$menu['id']}</td>";
                echo "<td>{$menu['menu']}</td>";
                echo "<td>" . ($menu['permission_group'] ?: 'None') . "</td>";
                echo "<td>$access_display</td>";
                echo "<td>$access_logic</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p style='color: red;'>❌ No menus found in sidebar_menus table!</p>";
        }
    }
    
    // 5. Test the actual API endpoint
    echo "<h4>5. Test Menu API Endpoint:</h4>";
    echo "<p><strong>API Test URL:</strong></p>";
    echo "<div style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd;'>";
    echo "<code>POST http://localhost/amt/api/teacher/menu</code><br>";
    echo "<code>Body: {\"staff_id\": $test_staff_id}</code>";
    echo "</div>";
    echo "<button onclick=\"testApi($test_staff_id)\">🧪 Test API Now</button>";
    echo "<div id='api-result' style='margin-top: 10px;'></div>";
    
} else {
    // Show form to enter staff ID
    echo "<form method='GET'>";
    echo "<label>Enter Staff ID to test: </label>";
    echo "<input type='number' name='staff_id' placeholder='e.g., 123' required>";
    echo "<button type='submit'>🔍 Debug</button>";
    echo "</form>";
    
    // Show some staff IDs for reference
    echo "<h4>Available Staff IDs:</h4>";
    $stmt = $pdo->query("
        SELECT s.id, s.name, s.surname, s.employee_id, r.name as role_name, r.is_superadmin
        FROM staff s
        LEFT JOIN staff_roles sr ON sr.staff_id = s.id
        LEFT JOIN roles r ON r.id = sr.role_id
        WHERE s.is_active = 1
        ORDER BY r.is_superadmin DESC, s.id
        LIMIT 10
    ");
    $staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($staff_list) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Staff ID</th><th>Name</th><th>Employee ID</th><th>Role</th><th>Superadmin</th><th>Action</th></tr>";
        foreach ($staff_list as $staff) {
            $superadmin = $staff['is_superadmin'] ? '✅ Yes' : '❌ No';
            $highlight = $staff['is_superadmin'] ? 'style="background-color: #ffeb3b;"' : '';
            echo "<tr $highlight>";
            echo "<td>{$staff['id']}</td>";
            echo "<td>{$staff['name']} {$staff['surname']}</td>";
            echo "<td>{$staff['employee_id']}</td>";
            echo "<td>" . ($staff['role_name'] ?: 'No Role') . "</td>";
            echo "<td>$superadmin</td>";
            echo "<td><a href='?staff_id={$staff['id']}'>🔍 Debug</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
?>

<script>
function testApi(staffId) {
    const resultDiv = document.getElementById('api-result');
    resultDiv.innerHTML = '<p>⏳ Testing API...</p>';
    
    fetch('/amt/api/teacher/menu', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({staff_id: staffId})
    })
    .then(response => response.json())
    .then(data => {
        resultDiv.innerHTML = '<h5>API Response:</h5><pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow-x: auto;">' + JSON.stringify(data, null, 2) + '</pre>';
    })
    .catch(error => {
        resultDiv.innerHTML = '<p style="color: red;">❌ API Error: ' + error.message + '</p>';
    });
}
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
th, td { padding: 8px; text-align: left; }
th { background-color: #f0f0f0; }
.highlight { background-color: #ffffcc; }
</style>