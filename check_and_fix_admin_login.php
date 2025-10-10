<?php
/**
 * Script to Check and Fix Admin Login Credentials
 * 
 * This script will:
 * 1. Check if the admin user exists in the database
 * 2. Verify the password hash
 * 3. Fix any issues found
 * 4. Provide detailed status information
 */

// Database configuration
$host = 'localhost';
$dbname = 'amt';
$username = 'root';
$password = '';

// Admin credentials to check/fix
$admin_email = 'amaravatijuniorcollege@gmail.com';
$admin_password = '2017@amaravathi';

echo "=======================================================\n";
echo "Admin Login Credentials Check and Fix Script\n";
echo "=======================================================\n\n";

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Database connection successful\n\n";

    // Step 1: Check if user exists
    echo "Step 1: Checking if user exists...\n";
    echo "-------------------------------------------------------\n";
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE email = ?");
    $stmt->execute([$admin_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "❌ ERROR: User with email '$admin_email' not found in database!\n\n";
        echo "Available admin users in database:\n";
        $stmt = $pdo->query("SELECT id, name, surname, email, is_active FROM staff ORDER BY id LIMIT 10");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $u) {
            echo "  - ID: {$u['id']}, Name: {$u['name']} {$u['surname']}, Email: {$u['email']}, Active: {$u['is_active']}\n";
        }
        exit(1);
    }

    echo "✅ User found in database!\n\n";

    // Step 2: Display current user information
    echo "Step 2: Current User Information\n";
    echo "-------------------------------------------------------\n";
    echo "ID: " . $user['id'] . "\n";
    echo "Name: " . $user['name'] . " " . $user['surname'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Is Active: " . ($user['is_active'] == 1 ? 'Yes' : 'No') . "\n";
    echo "Employee ID: " . $user['employee_id'] . "\n";
    echo "Current Password Hash: " . substr($user['password'], 0, 50) . "...\n";
    echo "Password Hash Length: " . strlen($user['password']) . " characters\n\n";

    // Step 3: Check if account is active
    echo "Step 3: Checking account status...\n";
    echo "-------------------------------------------------------\n";
    if ($user['is_active'] != 1) {
        echo "⚠️  WARNING: Account is DISABLED!\n";
        echo "Enabling account...\n";
        $stmt = $pdo->prepare("UPDATE staff SET is_active = 1 WHERE id = ?");
        $stmt->execute([$user['id']]);
        echo "✅ Account enabled successfully!\n\n";
    } else {
        echo "✅ Account is active\n\n";
    }

    // Step 4: Check staff roles
    echo "Step 4: Checking user roles...\n";
    echo "-------------------------------------------------------\n";
    $stmt = $pdo->prepare("SELECT sr.*, r.name as role_name FROM staff_roles sr 
                           LEFT JOIN roles r ON sr.role_id = r.id 
                           WHERE sr.staff_id = ?");
    $stmt->execute([$user['id']]);
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($roles)) {
        echo "⚠️  WARNING: User has no roles assigned!\n";
        echo "Assigning Super Admin role (role_id = 7)...\n";
        
        // Check if role 7 exists
        $stmt = $pdo->query("SELECT * FROM roles WHERE id = 7");
        $superadmin_role = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($superadmin_role) {
            $stmt = $pdo->prepare("INSERT INTO staff_roles (staff_id, role_id) VALUES (?, 7)");
            $stmt->execute([$user['id']]);
            echo "✅ Super Admin role assigned successfully!\n\n";
        } else {
            echo "❌ ERROR: Super Admin role (ID 7) not found in roles table!\n";
            echo "Available roles:\n";
            $stmt = $pdo->query("SELECT * FROM roles");
            $all_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($all_roles as $role) {
                echo "  - ID: {$role['id']}, Name: {$role['name']}\n";
            }
            echo "\n";
        }
    } else {
        echo "✅ User has the following roles:\n";
        foreach ($roles as $role) {
            echo "  - Role ID: {$role['role_id']}, Role Name: {$role['role_name']}\n";
        }
        echo "\n";
    }

    // Step 5: Verify current password
    echo "Step 5: Verifying password...\n";
    echo "-------------------------------------------------------\n";
    $password_valid = password_verify($admin_password, $user['password']);
    
    if ($password_valid) {
        echo "✅ Password is CORRECT! No need to update.\n\n";
        echo "=======================================================\n";
        echo "RESULT: Login should work with these credentials:\n";
        echo "=======================================================\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n";
        echo "Login URL: http://localhost/amt/site/login\n\n";
        echo "✅ All checks passed! You should be able to login now.\n";
        exit(0);
    } else {
        echo "❌ Password verification FAILED!\n";
        echo "Current password hash does not match the provided password.\n\n";
    }

    // Step 6: Update password
    echo "Step 6: Updating password...\n";
    echo "-------------------------------------------------------\n";
    $new_password_hash = password_hash($admin_password, PASSWORD_DEFAULT);
    echo "New password hash: " . substr($new_password_hash, 0, 50) . "...\n";
    echo "New hash length: " . strlen($new_password_hash) . " characters\n\n";

    $stmt = $pdo->prepare("UPDATE staff SET password = ? WHERE id = ?");
    $result = $stmt->execute([$new_password_hash, $user['id']]);

    if ($result) {
        echo "✅ Password updated successfully!\n\n";

        // Verify the new password
        echo "Step 7: Verifying new password...\n";
        echo "-------------------------------------------------------\n";
        $stmt = $pdo->prepare("SELECT password FROM staff WHERE id = ?");
        $stmt->execute([$user['id']]);
        $updated_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $verify_new = password_verify($admin_password, $updated_user['password']);
        if ($verify_new) {
            echo "✅ New password verified successfully!\n\n";
        } else {
            echo "❌ ERROR: New password verification failed!\n\n";
            exit(1);
        }

        echo "=======================================================\n";
        echo "SUCCESS! Login credentials have been fixed:\n";
        echo "=======================================================\n";
        echo "Email: $admin_email\n";
        echo "Password: $admin_password\n";
        echo "Login URL: http://localhost/amt/site/login\n\n";
        echo "✅ You can now login with these credentials!\n";
    } else {
        echo "❌ ERROR: Failed to update password!\n";
        exit(1);
    }

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=======================================================\n";
echo "Script completed successfully!\n";
echo "=======================================================\n";
?>

