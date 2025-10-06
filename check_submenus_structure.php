<?php
/**
 * Diagnostic Script: Check Submenu Structure
 * Analyzes which menus should have submenus and why they're not appearing
 */

echo "<h1>Submenu Structure Analysis</h1>";
echo "<hr>";

try {
    $db = new PDO('mysql:host=localhost;dbname=school6', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Check total submenus
    echo "<h2>1. Total Active Submenus</h2>";
    $stmt = $db->query("SELECT COUNT(*) as total FROM sidebar_sub_menus WHERE is_active = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<strong>Total Active Submenus:</strong> " . $result['total'] . "<br><br>";
    
    // 2. Check submenus per menu
    echo "<h2>2. Submenus Per Menu</h2>";
    $stmt = $db->query("
        SELECT 
            sm.id as menu_id,
            sm.menu as menu_name,
            sm.permission_group_id as menu_perm_group,
            COUNT(ssm.id) as submenu_count
        FROM sidebar_menus sm
        LEFT JOIN sidebar_sub_menus ssm ON ssm.sidebar_menu_id = sm.id AND ssm.is_active = 1
        WHERE sm.is_active = 1
        GROUP BY sm.id
        HAVING submenu_count > 0
        ORDER BY submenu_count DESC
    ");
    
    $menus_with_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Menu ID</th><th>Menu Name</th><th>Permission Group</th><th>Submenu Count</th></tr>";
    foreach ($menus_with_submenus as $menu) {
        echo "<tr>";
        echo "<td>" . $menu['menu_id'] . "</td>";
        echo "<td>" . $menu['menu_name'] . "</td>";
        echo "<td>" . $menu['menu_perm_group'] . "</td>";
        echo "<td><strong>" . $menu['submenu_count'] . "</strong></td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // 3. Check specific menu submenus (Student Information - ID 2)
    echo "<h2>3. Student Information Menu (ID 2) Submenus</h2>";
    $stmt = $db->query("
        SELECT * 
        FROM sidebar_sub_menus 
        WHERE sidebar_menu_id = 2 
        AND is_active = 1
        ORDER BY level
    ");
    $student_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($student_submenus) > 0) {
        echo "<strong>Found " . count($student_submenus) . " submenus:</strong><br>";
        foreach ($student_submenus as $submenu) {
            echo "- " . $submenu['menu'] . " (ID: " . $submenu['id'] . ", Perm Group: " . $submenu['permission_group_id'] . ")<br>";
        }
    } else {
        echo "<em>No submenus found for Student Information</em><br>";
    }
    echo "<br>";
    
    // 4. Check Fees Collection menu submenus (ID 3)
    echo "<h2>4. Fees Collection Menu (ID 3) Submenus</h2>";
    $stmt = $db->query("
        SELECT * 
        FROM sidebar_sub_menus 
        WHERE sidebar_menu_id = 3 
        AND is_active = 1
        ORDER BY level
    ");
    $fees_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($fees_submenus) > 0) {
        echo "<strong>Found " . count($fees_submenus) . " submenus:</strong><br>";
        foreach ($fees_submenus as $submenu) {
            echo "- " . $submenu['menu'] . " (ID: " . $submenu['id'] . ", Perm Group: " . $submenu['permission_group_id'] . ")<br>";
        }
    } else {
        echo "<em>No submenus found for Fees Collection</em><br>";
    }
    echo "<br>";
    
    // 5. Test role permissions for staff_id 6 (Accountant role_id 3)
    echo "<h2>5. Role Permissions Analysis for Staff ID 6 (Accountant, Role ID 3)</h2>";
    
    // Check what permission categories the role has access to
    $stmt = $db->query("
        SELECT DISTINCT pc.id, pc.perm_group_id, pc.name
        FROM roles_permissions rp
        JOIN permission_category pc ON rp.perm_cat_id = pc.id
        WHERE rp.role_id = 3
        AND rp.can_view = 1
        ORDER BY pc.perm_group_id
    ");
    $role_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<strong>Permission Categories accessible by Accountant role:</strong><br>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Category ID</th><th>Permission Group ID</th><th>Category Name</th></tr>";
    foreach ($role_permissions as $perm) {
        echo "<tr>";
        echo "<td>" . $perm['id'] . "</td>";
        echo "<td>" . $perm['perm_group_id'] . "</td>";
        echo "<td>" . $perm['name'] . "</td>";
        echo "</tr>";
    }
    echo "</table><br>";
    
    // 6. Check submenu access for specific menu (e.g., menu_id 2)
    echo "<h2>6. Submenu Access Test for Student Information (Menu ID 2)</h2>";
    $stmt = $db->query("
        SELECT 
            ssm.*,
            pc.perm_group_id,
            pc.name as permission_name
        FROM sidebar_sub_menus ssm
        LEFT JOIN permission_category pc ON ssm.permission_group_id = pc.perm_group_id
        LEFT JOIN roles_permissions rp ON pc.id = rp.perm_cat_id AND rp.role_id = 3 AND rp.can_view = 1
        WHERE ssm.sidebar_menu_id = 2
        AND ssm.is_active = 1
    ");
    
    $submenus_check = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($submenus_check) > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Submenu</th><th>Submenu Perm Group</th><th>Has Access?</th></tr>";
        
        foreach ($submenus_check as $submenu) {
            // Check if role has access
            $check_stmt = $db->prepare("
                SELECT COUNT(*) as has_access
                FROM permission_category pc
                JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
                WHERE pc.perm_group_id = ?
                AND rp.role_id = 3
                AND rp.can_view = 1
            ");
            $check_stmt->execute([$submenu['permission_group_id']]);
            $access = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<tr>";
            echo "<td>" . $submenu['menu'] . "</td>";
            echo "<td>" . $submenu['permission_group_id'] . "</td>";
            echo "<td>" . ($access['has_access'] > 0 ? '<span style="color:green">✅ YES</span>' : '<span style="color:red">❌ NO</span>') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // 7. Show the correct query for getting submenus
    echo "<h2>7. Recommended Submenu Query</h2>";
    echo "<pre>";
    echo "For Superadmin:
SELECT * 
FROM sidebar_sub_menus
WHERE sidebar_menu_id = ?
AND is_active = 1
ORDER BY level

For Regular Role:
SELECT DISTINCT ssm.*
FROM sidebar_sub_menus ssm
JOIN permission_category pc ON ssm.permission_group_id = pc.perm_group_id
JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
WHERE rp.role_id = ?
AND rp.can_view = 1
AND ssm.sidebar_menu_id = ?
AND ssm.is_active = 1
ORDER BY ssm.level";
    echo "</pre>";
    
} catch (Exception $e) {
    echo "<div style='color:red'>";
    echo "<strong>Error:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $e->getLine();
    echo "</div>";
}
?>
