<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>Let's understand the permission system:</h3>";
    
    // Check permission_group table
    echo "<h4>permission_group structure:</h4>";
    $stmt = $pdo->query("DESCRIBE permission_group");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
    }
    
    // Check permission_category table
    echo "<h4>permission_category structure:</h4>";
    $stmt = $pdo->query("DESCRIBE permission_category");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
    }
    
    echo "<h3>Let's trace the permission flow for role_id 2:</h3>";
    
    // Get sidebar menus with permission_group_id
    echo "<h4>Sidebar menus for role 2:</h4>";
    $stmt = $pdo->query("
        SELECT DISTINCT sm.id, sm.menu, sm.permission_group_id 
        FROM sidebar_menus sm
        JOIN permission_category pc ON sm.permission_group_id = pc.perm_group_id
        JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
        WHERE rp.role_id = 2 AND rp.can_view = 1 AND sm.is_active = 1
        ORDER BY sm.level
        LIMIT 5
    ");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($menus as $menu) {
        echo "- " . $menu['menu'] . " (ID: " . $menu['id'] . ", Group: " . $menu['permission_group_id'] . ")<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    
    echo "<h4>Let's try simpler approach - just get all menus:</h4>";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
        $stmt = $pdo->query("SELECT id, menu, permission_group_id FROM sidebar_menus WHERE is_active = 1 ORDER BY level LIMIT 5");
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($menus as $menu) {
            echo "- " . $menu['menu'] . " (ID: " . $menu['id'] . ", Group: " . $menu['permission_group_id'] . ")<br>";
        }
    } catch (Exception $e2) {
        echo "Error 2: " . $e2->getMessage();
    }
}
?>