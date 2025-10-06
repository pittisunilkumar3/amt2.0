<?php
/**
 * Update Role 7 Utility
 * Helper script to fix role-related issues
 */

header('Content-Type: application/json');
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
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {
    case 'fix_role_7':
        try {
            // Update role ID 7 to be superadmin
            $stmt = $pdo->prepare("UPDATE roles SET is_superadmin = 1, is_active = 1 WHERE id = 7");
            $result = $stmt->execute();
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => '✅ Role ID 7 has been updated to superadmin!']);
            } else {
                echo json_encode(['success' => false, 'message' => '❌ Failed to update role ID 7']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;
        
    case 'create_role_7':
        try {
            // Create role ID 7 as superadmin
            $stmt = $pdo->prepare("
                INSERT INTO roles (id, name, slug, is_superadmin, is_active, created_at) 
                VALUES (7, 'Super Admin', 'super_admin', 1, 1, NOW())
            ");
            $result = $stmt->execute();
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => '✅ Role ID 7 (Super Admin) has been created!']);
            } else {
                echo json_encode(['success' => false, 'message' => '❌ Failed to create role ID 7']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;
        
    case 'assign_role':
        $staff_id = $input['staff_id'] ?? 0;
        $role_id = $input['role_id'] ?? 0;
        
        if (!$staff_id || !$role_id) {
            echo json_encode(['success' => false, 'message' => 'Staff ID and Role ID are required']);
            break;
        }
        
        try {
            // Check if staff exists
            $stmt = $pdo->prepare("SELECT id, name, surname FROM staff WHERE id = ? AND is_active = 1");
            $stmt->execute([$staff_id]);
            $staff = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$staff) {
                echo json_encode(['success' => false, 'message' => '❌ Staff member not found or inactive']);
                break;
            }
            
            // Check if role exists
            $stmt = $pdo->prepare("SELECT id, name, is_superadmin FROM roles WHERE id = ?");
            $stmt->execute([$role_id]);
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$role) {
                echo json_encode(['success' => false, 'message' => '❌ Role not found']);
                break;
            }
            
            // Remove existing role assignment
            $stmt = $pdo->prepare("DELETE FROM staff_roles WHERE staff_id = ?");
            $stmt->execute([$staff_id]);
            
            // Assign new role
            $stmt = $pdo->prepare("
                INSERT INTO staff_roles (staff_id, role_id, is_active, assigned_at) 
                VALUES (?, ?, 1, NOW())
            ");
            $result = $stmt->execute([$staff_id, $role_id]);
            
            if ($result) {
                $superadmin_text = $role['is_superadmin'] ? ' (SUPERADMIN)' : '';
                echo json_encode([
                    'success' => true, 
                    'message' => "✅ Role '{$role['name']}'{$superadmin_text} assigned to {$staff['name']} {$staff['surname']} (ID: $staff_id)!"
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => '❌ Failed to assign role']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;
        
    case 'create_test_superadmin':
        try {
            // Create a test superadmin role if it doesn't exist
            $stmt = $pdo->query("SELECT id FROM roles WHERE is_superadmin = 1 LIMIT 1");
            $existing_superadmin = $stmt->fetch();
            
            if (!$existing_superadmin) {
                // Create superadmin role
                $stmt = $pdo->prepare("
                    INSERT INTO roles (name, slug, is_superadmin, is_active, created_at) 
                    VALUES ('Super Administrator', 'super_admin', 1, 1, NOW())
                ");
                $stmt->execute();
                $role_id = $pdo->lastInsertId();
                
                echo json_encode([
                    'success' => true, 
                    'message' => "✅ Superadmin role created with ID: $role_id"
                ]);
            } else {
                echo json_encode([
                    'success' => true, 
                    'message' => "✅ Superadmin role already exists with ID: {$existing_superadmin['id']}"
                ]);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>