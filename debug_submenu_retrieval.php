<?php
/**
 * Debug Submenu Retrieval
 * Tests the exact queries used in the API to see why submenus aren't appearing
 */

echo "<h1>Debug Submenu Retrieval for Staff ID 6 (Accountant Role)</h1>";
echo "<hr>";

try {
    $db = new PDO('mysql:host=localhost;dbname=school6', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $staff_id = 6;
    $role_id = 3; // Accountant
    
    // Get one menu with known submenus (Transport - ID 21)
    echo "<h2>Test 1: Transport Menu (ID 21) - Known to have submenus</h2>";
    
    $stmt = $db->prepare("
        SELECT DISTINCT ssm.*
        FROM sidebar_sub_menus ssm
        JOIN permission_category pc ON ssm.permission_group_id = pc.perm_group_id
        JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
        WHERE rp.role_id = ?
        AND rp.can_view = 1
        AND ssm.sidebar_menu_id = 21
        AND ssm.is_active = 1
        ORDER BY ssm.level
    ");
    $stmt->execute([$role_id]);
    $transport_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<strong>Query Result:</strong> " . count($transport_submenus) . " submenus found<br>";
    foreach ($transport_submenus as $submenu) {
        echo "- " . $submenu['menu'] . " (Perm Group: " . $submenu['permission_group_id'] . ")<br>";
    }
    echo "<hr>";
    
    // Test Student Information menu (ID 2)
    echo "<h2>Test 2: Student Information Menu (ID 2)</h2>";
    
    // First check if submenus exist
    $stmt = $db->query("SELECT COUNT(*) as count FROM sidebar_sub_menus WHERE sidebar_menu_id = 2 AND is_active = 1");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<strong>Total submenus in DB:</strong> " . $count['count'] . "<br>";
    
    // Try the permission-based query
    $stmt = $db->prepare("
        SELECT DISTINCT ssm.*
        FROM sidebar_sub_menus ssm
        JOIN permission_category pc ON ssm.permission_group_id = pc.perm_group_id
        JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
        WHERE rp.role_id = ?
        AND rp.can_view = 1
        AND ssm.sidebar_menu_id = 2
        AND ssm.is_active = 1
        ORDER BY ssm.level
    ");
    $stmt->execute([$role_id]);
    $student_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<strong>Permission-based query result:</strong> " . count($student_submenus) . " submenus found<br>";
    if (count($student_submenus) > 0) {
        foreach ($student_submenus as $submenu) {
            echo "- " . $submenu['menu'] . " (Perm Group: " . $submenu['permission_group_id'] . ")<br>";
        }
    } else {
        echo "<em style='color:red'>No submenus found with permission query!</em><br>";
        
        // Debug: Check what permission groups the submenus have
        echo "<br><strong>Debug: What permission groups do these submenus use?</strong><br>";
        $stmt = $db->query("
            SELECT id, menu, permission_group_id 
            FROM sidebar_sub_menus 
            WHERE sidebar_menu_id = 2 AND is_active = 1
        ");
        $all_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($all_submenus as $submenu) {
            // Check if role has access to this permission group
            $check_stmt = $db->prepare("
                SELECT COUNT(*) as has_access
                FROM permission_category pc
                JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
                WHERE pc.perm_group_id = ?
                AND rp.role_id = ?
                AND rp.can_view = 1
            ");
            $check_stmt->execute([$submenu['permission_group_id'], $role_id]);
            $access = $check_stmt->fetch(PDO::FETCH_ASSOC);
            
            $color = $access['has_access'] > 0 ? 'green' : 'red';
            $status = $access['has_access'] > 0 ? '✅' : '❌';
            echo "<span style='color:$color'>$status " . $submenu['menu'] . " (Perm Group: " . $submenu['permission_group_id'] . ")</span><br>";
        }
    }
    echo "<hr>";
    
    // Test all menus that staff has access to
    echo "<h2>Test 3: Check all menus for submenus</h2>";
    
    // Get all accessible menus
    $stmt = $db->prepare("
        SELECT DISTINCT sm.*
        FROM sidebar_menus sm
        JOIN permission_category pc ON sm.permission_group_id = pc.perm_group_id
        JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
        WHERE rp.role_id = ?
        AND rp.can_view = 1
        AND sm.is_active = 1
        ORDER BY sm.level
        LIMIT 5
    ");
    $stmt->execute([$role_id]);
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Menu ID</th><th>Menu Name</th><th>Submenus (Permission-Based)</th><th>Submenus (All Active)</th></tr>";
    
    foreach ($menus as $menu) {
        // Permission-based submenus
        $stmt = $db->prepare("
            SELECT DISTINCT ssm.*
            FROM sidebar_sub_menus ssm
            JOIN permission_category pc ON ssm.permission_group_id = pc.perm_group_id
            JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
            WHERE rp.role_id = ?
            AND rp.can_view = 1
            AND ssm.sidebar_menu_id = ?
            AND ssm.is_active = 1
            ORDER BY ssm.level
        ");
        $stmt->execute([$role_id, $menu['id']]);
        $perm_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // All active submenus
        $stmt = $db->prepare("
            SELECT * FROM sidebar_sub_menus 
            WHERE sidebar_menu_id = ? AND is_active = 1
        ");
        $stmt->execute([$menu['id']]);
        $all_submenus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<tr>";
        echo "<td>" . $menu['id'] . "</td>";
        echo "<td>" . $menu['menu'] . "</td>";
        echo "<td><strong>" . count($perm_submenus) . "</strong></td>";
        echo "<td>" . count($all_submenus) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<hr>";
    
    // Final recommendation
    echo "<h2>Analysis & Recommendation</h2>";
    echo "<div style='background:#fff3cd; padding:15px; border-left:4px solid #ffc107;'>";
    echo "<strong>Issue Identified:</strong><br>";
    echo "The submenu query requires matching permission_group_id between sidebar_sub_menus and permission_category.<br>";
    echo "However, if the submenu's permission_group_id doesn't match any permission_category that the role has access to, it won't appear.<br><br>";
    echo "<strong>Possible Solutions:</strong><br>";
    echo "1. <strong>Use parent menu permissions:</strong> If user has access to parent menu, show all its submenus<br>";
    echo "2. <strong>Check permission data:</strong> Ensure permission_category and roles_permissions tables are properly populated<br>";
    echo "3. <strong>Alternative approach:</strong> Check submenu permissions separately, not through JOIN<br>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color:red'><strong>Error:</strong> " . $e->getMessage() . "</div>";
}
?>
