<?php
// Direct database test for menu functionality

$mysqli = new mysqli("localhost", "root", "", "school");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get a staff member to test with
$staff_id = $_GET['staff_id'] ?? 1;

echo "<h2>Direct Database Menu Test for Staff ID: {$staff_id}</h2>";

// Check staff info
echo "<h3>1. Staff Information:</h3>";
$staff_query = "SELECT s.*, r.name as role_name, r.id as role_id, r.is_superadmin 
                FROM staff s 
                LEFT JOIN staff_roles sr ON sr.staff_id = s.id 
                LEFT JOIN roles r ON r.id = sr.role_id 
                WHERE s.id = ?";
$stmt = $mysqli->prepare($staff_query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$staff_result = $stmt->get_result()->fetch_assoc();

if ($staff_result) {
    echo "<table border='1'>";
    foreach ($staff_result as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td>$value</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Staff not found!</p>";
    exit;
}

// Check menu permissions
echo "<h3>2. Available Sidebar Menus:</h3>";
$menu_query = "SELECT sm.*, pg.short_code as permission_group 
               FROM sidebar_menus sm 
               LEFT JOIN permission_group pg ON pg.id = sm.permission_group_id 
               WHERE sm.is_active = 1 AND sm.sidebar_display = 1 
               ORDER BY sm.level";
$menu_result = $mysqli->query($menu_query);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Menu</th><th>Icon</th><th>Level</th><th>Permission Group</th><th>Access</th></tr>";

while ($menu = $menu_result->fetch_assoc()) {
    $access = "Unknown";
    
    // Check if this is super admin
    if ($staff_result['is_superadmin'] == 1) {
        $access = "Super Admin - Full Access";
    } else if (empty($menu['permission_group'])) {
        $access = "Public Access";
    } else {
        // Check permissions for this group
        $perm_query = "SELECT COUNT(*) as count 
                       FROM roles_permissions rp 
                       JOIN permission_category pc ON pc.id = rp.perm_cat_id 
                       JOIN permission_group pg ON pg.id = pc.perm_group_id 
                       WHERE rp.role_id = ? AND pg.short_code = ? 
                       AND (rp.can_view = 1 OR rp.can_add = 1 OR rp.can_edit = 1 OR rp.can_delete = 1)";
        $perm_stmt = $mysqli->prepare($perm_query);
        $perm_stmt->bind_param("is", $staff_result['role_id'], $menu['permission_group']);
        $perm_stmt->execute();
        $perm_result = $perm_stmt->get_result()->fetch_assoc();
        
        $access = $perm_result['count'] > 0 ? "Has Access" : "No Access";
    }
    
    echo "<tr>";
    echo "<td>" . $menu['id'] . "</td>";
    echo "<td>" . $menu['menu'] . "</td>";
    echo "<td>" . $menu['icon'] . "</td>";
    echo "<td>" . $menu['level'] . "</td>";
    echo "<td>" . ($menu['permission_group'] ?? 'None') . "</td>";
    echo "<td style='color: " . ($access == "No Access" ? "red" : "green") . "'>" . $access . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check submenus
echo "<h3>3. Available Sub-menus:</h3>";
$submenu_query = "SELECT ssm.*, sm.menu as parent_menu, pg.short_code as permission_group 
                  FROM sidebar_sub_menus ssm 
                  JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id 
                  LEFT JOIN permission_group pg ON pg.id = ssm.permission_group_id 
                  WHERE ssm.is_active = 1 
                  ORDER BY sm.level, ssm.level";
$submenu_result = $mysqli->query($submenu_query);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Parent</th><th>Menu</th><th>URL</th><th>Permission Group</th><th>Access</th></tr>";

while ($submenu = $submenu_result->fetch_assoc()) {
    $access = "Unknown";
    
    // Check if this is super admin
    if ($staff_result['is_superadmin'] == 1) {
        $access = "Super Admin - Full Access";
    } else if (empty($submenu['permission_group'])) {
        $access = "Public Access";
    } else {
        // Check permissions for this group
        $perm_query = "SELECT COUNT(*) as count 
                       FROM roles_permissions rp 
                       JOIN permission_category pc ON pc.id = rp.perm_cat_id 
                       JOIN permission_group pg ON pg.id = pc.perm_group_id 
                       WHERE rp.role_id = ? AND pg.short_code = ? 
                       AND (rp.can_view = 1 OR rp.can_add = 1 OR rp.can_edit = 1 OR rp.can_delete = 1)";
        $perm_stmt = $mysqli->prepare($perm_query);
        $perm_stmt->bind_param("is", $staff_result['role_id'], $submenu['permission_group']);
        $perm_stmt->execute();
        $perm_result = $perm_stmt->get_result()->fetch_assoc();
        
        $access = $perm_result['count'] > 0 ? "Has Access" : "No Access";
    }
    
    echo "<tr>";
    echo "<td>" . $submenu['id'] . "</td>";
    echo "<td>" . $submenu['parent_menu'] . "</td>";
    echo "<td>" . $submenu['menu'] . "</td>";
    echo "<td>" . $submenu['url'] . "</td>";
    echo "<td>" . ($submenu['permission_group'] ?? 'None') . "</td>";
    echo "<td style='color: " . ($access == "No Access" ? "red" : "green") . "'>" . $access . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check roles and permissions
echo "<h3>4. Role Permissions:</h3>";
if ($staff_result['role_id']) {
    $role_perm_query = "SELECT pg.name as group_name, pg.short_code, pc.name as permission_name, 
                               rp.can_view, rp.can_add, rp.can_edit, rp.can_delete
                        FROM roles_permissions rp 
                        JOIN permission_category pc ON pc.id = rp.perm_cat_id 
                        JOIN permission_group pg ON pg.id = pc.perm_group_id 
                        WHERE rp.role_id = ? 
                        ORDER BY pg.name, pc.name";
    $role_stmt = $mysqli->prepare($role_perm_query);
    $role_stmt->bind_param("i", $staff_result['role_id']);
    $role_stmt->execute();
    $role_perm_result = $role_stmt->get_result();
    
    echo "<table border='1'>";
    echo "<tr><th>Group</th><th>Permission</th><th>View</th><th>Add</th><th>Edit</th><th>Delete</th></tr>";
    
    while ($perm = $role_perm_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $perm['group_name'] . "</td>";
        echo "<td>" . $perm['permission_name'] . "</td>";
        echo "<td>" . ($perm['can_view'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . ($perm['can_add'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . ($perm['can_edit'] ? 'Yes' : 'No') . "</td>";
        echo "<td>" . ($perm['can_delete'] ? 'Yes' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No role assigned to this staff member!</p>";
}

$mysqli->close();

echo "<p><a href='test_menu_api.php'>← Back to Staff List</a></p>";
?>