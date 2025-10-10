<?php
/**
 * Temporary Admin Password Reset Script
 * This script resets the Super Admin password to 'admin123' for testing
 */

echo "=== Admin Password Reset Script ===\n\n";

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amt";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful\n\n";
} catch(PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage() . "\n");
}

// Check current admin users
echo "Current admin users:\n";
$stmt = $pdo->query("SELECT id, name, surname, email, employee_id FROM staff WHERE id IN (SELECT staff_id FROM staff_roles WHERE role_id = 7)");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($admins)) {
    echo "❌ No super admin users found!\n";
    exit;
}

foreach ($admins as $admin) {
    echo "- ID: {$admin['id']}, Name: {$admin['name']} {$admin['surname']}, Email: {$admin['email']}\n";
}

echo "\n";

// Reset password for Super Admin (ID = 1)
$new_password = 'admin123';
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

echo "Resetting password for Super Admin (ID: 1)...\n";

try {
    $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE id = 1");
    $result = $stmt->execute([$hashed_password]);
    
    if ($result) {
        echo "✅ Password reset successful!\n\n";
        echo "Login Credentials:\n";
        echo "Email: amaravatijuniorcollege@gmail.com\n";
        echo "Password: admin123\n\n";
        echo "You can now login at: http://localhost/amt/site/login\n";
    } else {
        echo "❌ Password reset failed!\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Script Complete ===\n";
?>
