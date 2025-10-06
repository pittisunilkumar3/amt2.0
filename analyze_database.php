<?php
echo "<h2>Database Table Analysis</h2>";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>Available Tables</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $relevant_tables = [];
    foreach ($tables as $table) {
        if (stripos($table, 'menu') !== false || 
            stripos($table, 'permission') !== false || 
            stripos($table, 'role') !== false || 
            stripos($table, 'staff') !== false) {
            $relevant_tables[] = $table;
        }
    }
    
    echo "<strong>Relevant Tables Found:</strong><br>";
    foreach ($relevant_tables as $table) {
        echo "✅ " . $table . "<br>";
    }
    
    echo "<h3>Table Structure Analysis</h3>";
    
    // Check sidebar_menus structure
    if (in_array('sidebar_menus', $tables)) {
        echo "<h4>sidebar_menus structure:</h4>";
        $stmt = $pdo->query("DESCRIBE sidebar_menus");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
        }
    }
    
    // Check staff_roles structure
    if (in_array('staff_roles', $tables)) {
        echo "<h4>staff_roles structure:</h4>";
        $stmt = $pdo->query("DESCRIBE staff_roles");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
        }
    }
    
    // Check roles structure
    if (in_array('roles', $tables)) {
        echo "<h4>roles structure:</h4>";
        $stmt = $pdo->query("DESCRIBE roles");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
        }
    }
    
    // Check staff structure
    if (in_array('staff', $tables)) {
        echo "<h4>staff structure:</h4>";
        $stmt = $pdo->query("DESCRIBE staff");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
        }
    }
    
    echo "<h3>Test Staff ID 24</h3>";
    $stmt = $pdo->prepare("
        SELECT s.*, r.name as role_name, r.is_superadmin, r.id as role_id 
        FROM staff s 
        LEFT JOIN staff_roles sr ON sr.staff_id = s.id 
        LEFT JOIN roles r ON r.id = sr.role_id 
        WHERE s.id = ?
    ");
    $stmt->execute([24]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($staff) {
        echo "✅ Staff found:<br>";
        echo "- Name: " . $staff['name'] . " " . $staff['surname'] . "<br>";
        echo "- Role: " . ($staff['role_name'] ?: 'No Role') . "<br>";
        echo "- Is Superadmin: " . ($staff['is_superadmin'] ? 'Yes' : 'No') . "<br>";
        echo "- Role ID: " . ($staff['role_id'] ?: 'None') . "<br>";
    } else {
        echo "❌ Staff ID 24 not found<br>";
    }
    
    echo "<h3>Available Menus</h3>";
    $stmt = $pdo->query("SELECT * FROM sidebar_menus WHERE status = 1 ORDER BY sort_order LIMIT 5");
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Found " . count($menus) . " active menus:<br>";
    foreach ($menus as $menu) {
        echo "- " . $menu['menu'] . " (ID: " . $menu['id'] . ")<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>