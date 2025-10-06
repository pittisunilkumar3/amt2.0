<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h3>roles_permissions table structure:</h3>";
    $stmt = $pdo->query("DESCRIBE roles_permissions");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
    }
    
    echo "<h3>sidebar_sub_menus table structure:</h3>";
    $stmt = $pdo->query("DESCRIBE sidebar_sub_menus");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")<br>";
    }
    
    echo "<h3>Sample roles_permissions data:</h3>";
    $stmt = $pdo->query("SELECT * FROM roles_permissions WHERE role_id = 2 LIMIT 5");
    $perms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($perms as $perm) {
        echo "Role ID: " . $perm['role_id'] . " - ";
        foreach ($perm as $key => $value) {
            if ($key != 'role_id') echo "$key: $value, ";
        }
        echo "<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>