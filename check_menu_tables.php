<?php
// Check database tables and data for menu system

echo "<h1>Database Tables Check for Menu System</h1>";

$mysqli = new mysqli("localhost", "root", "", "school");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$required_tables = [
    'staff',
    'staff_roles', 
    'roles',
    'sidebar_menus',
    'sidebar_sub_menus',
    'permission_group',
    'permission_category', 
    'roles_permissions'
];

echo "<h2>Table Existence and Row Counts:</h2>";
echo "<table border='1'>";
echo "<tr><th>Table</th><th>Exists</th><th>Row Count</th><th>Sample Data</th></tr>";

foreach ($required_tables as $table) {
    echo "<tr>";
    echo "<td><strong>{$table}</strong></td>";
    
    // Check if table exists
    $check_query = "SHOW TABLES LIKE '{$table}'";
    $check_result = $mysqli->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        echo "<td style='color: green;'>✓ Yes</td>";
        
        // Get row count
        $count_query = "SELECT COUNT(*) as count FROM {$table}";
        $count_result = $mysqli->query($count_query);
        $count = $count_result->fetch_assoc()['count'];
        
        echo "<td>{$count}</td>";
        
        // Get sample data
        if ($count > 0) {
            $sample_query = "SELECT * FROM {$table} LIMIT 3";
            $sample_result = $mysqli->query($sample_query);
            
            echo "<td>";
            echo "<details><summary>View Sample</summary>";
            echo "<table border='1' style='font-size: 12px;'>";
            
            // Headers
            if ($sample_result->num_rows > 0) {
                $first_row = true;
                while ($row = $sample_result->fetch_assoc()) {
                    if ($first_row) {
                        echo "<tr>";
                        foreach (array_keys($row) as $column) {
                            echo "<th>{$column}</th>";
                        }
                        echo "</tr>";
                        $first_row = false;
                    }
                    
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>" . htmlspecialchars(substr($value, 0, 50)) . "</td>";
                    }
                    echo "</tr>";
                }
            }
            echo "</table></details>";
            echo "</td>";
        } else {
            echo "<td style='color: orange;'>Empty</td>";
        }
    } else {
        echo "<td style='color: red;'>✗ No</td>";
        echo "<td>-</td>";
        echo "<td>Table missing</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Check for sample menu data specifically
echo "<h2>Menu System Data Check:</h2>";

// Check sidebar_menus
$menu_exists = $mysqli->query("SHOW TABLES LIKE 'sidebar_menus'")->num_rows > 0;
if ($menu_exists) {
    echo "<h3>Sidebar Menus:</h3>";
    $menu_query = "SELECT id, menu, icon, level, is_active, sidebar_display, permission_group_id FROM sidebar_menus LIMIT 10";
    $menu_result = $mysqli->query($menu_query);
    
    if ($menu_result && $menu_result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Menu</th><th>Icon</th><th>Level</th><th>Active</th><th>Display</th><th>Permission Group</th></tr>";
        while ($row = $menu_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['menu']) . "</td>";
            echo "<td>" . htmlspecialchars($row['icon']) . "</td>";
            echo "<td>" . $row['level'] . "</td>";
            echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . ($row['sidebar_display'] ? 'Yes' : 'No') . "</td>";
            echo "<td>" . $row['permission_group_id'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No sidebar menus found</p>";
    }
} else {
    echo "<p style='color: red;'>sidebar_menus table does not exist</p>";
}

// Check staff roles
$staff_roles_exists = $mysqli->query("SHOW TABLES LIKE 'staff_roles'")->num_rows > 0;
if ($staff_roles_exists) {
    echo "<h3>Staff Roles (Sample):</h3>";
    $roles_query = "SELECT sr.staff_id, sr.role_id, r.name as role_name, s.name as staff_name 
                    FROM staff_roles sr 
                    JOIN roles r ON r.id = sr.role_id 
                    JOIN staff s ON s.id = sr.staff_id 
                    LIMIT 10";
    $roles_result = $mysqli->query($roles_query);
    
    if ($roles_result && $roles_result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>Staff ID</th><th>Staff Name</th><th>Role ID</th><th>Role Name</th></tr>";
        while ($row = $roles_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['staff_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['staff_name']) . "</td>";
            echo "<td>" . $row['role_id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['role_name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No staff roles assigned</p>";
    }
}

// Check permission groups
$perm_groups_exists = $mysqli->query("SHOW TABLES LIKE 'permission_group'")->num_rows > 0;
if ($perm_groups_exists) {
    echo "<h3>Permission Groups:</h3>";
    $groups_query = "SELECT * FROM permission_group LIMIT 10";
    $groups_result = $mysqli->query($groups_query);
    
    if ($groups_result && $groups_result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Short Code</th><th>Active</th></tr>";
        while ($row = $groups_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['short_code']) . "</td>";
            echo "<td>" . ($row['is_active'] ? 'Yes' : 'No') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No permission groups found</p>";
    }
}

$mysqli->close();

echo "<hr>";
echo "<h2>Recommendations:</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border: 1px solid #ccc;'>";
echo "<p>If tables are missing or empty, you may need to:</p>";
echo "<ul>";
echo "<li>1. Import the proper database schema</li>";
echo "<li>2. Run database migrations</li>";
echo "<li>3. Insert default menu data</li>";
echo "<li>4. Assign roles to staff members</li>";
echo "<li>5. Configure permission groups and role permissions</li>";
echo "</ul>";
echo "<p>The menu API requires these tables to have proper data to function correctly.</p>";
echo "</div>";

echo "<p><a href='comprehensive_menu_test.php'>← Back to Menu Test</a></p>";
?>