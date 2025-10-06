<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit(0);
}

// Function to output JSON response
function json_response($status, $data, $http_code = 200) {
    http_response_code($http_code);
    echo json_encode($data);
    exit;
}

try {
    // Check request method
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        json_response("error", array(
            "status" => 0,
            "message" => "Only POST method allowed",
            "timestamp" => date('Y-m-d H:i:s')
        ), 405);
    }
    
    // Get and validate JSON input
    $input_raw = file_get_contents("php://input");
    
    if (empty($input_raw)) {
        json_response("error", array(
            "status" => 0,
            "message" => "Request body is empty",
            "timestamp" => date('Y-m-d H:i:s')
        ), 400);
    }
    
    $input = json_decode($input_raw, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        json_response("error", array(
            "status" => 0,
            "message" => "Invalid JSON format",
            "error" => json_last_error_msg(),
            "timestamp" => date('Y-m-d H:i:s')
        ), 400);
    }
    
    $staff_id = isset($input["staff_id"]) ? intval($input["staff_id"]) : 0;
    
    if (!$staff_id || $staff_id <= 0) {
        json_response("error", array(
            "status" => 0,
            "message" => "staff_id is required and must be a positive integer",
            "provided" => isset($input["staff_id"]) ? $input["staff_id"] : null,
            "timestamp" => date('Y-m-d H:i:s')
        ), 400);
    }
    
    // Database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "amt";
    
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get staff info
    $stmt = $pdo->prepare("
        SELECT s.*, r.name as role_name, r.is_superadmin, r.id as role_id 
        FROM staff s 
        LEFT JOIN staff_roles sr ON sr.staff_id = s.id 
        LEFT JOIN roles r ON r.id = sr.role_id 
        WHERE s.id = ? AND s.is_active = 1
    ");
    $stmt->execute([$staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$staff) {
        json_response("error", array(
            "status" => 0,
            "message" => "Staff member not found or inactive",
            "staff_id" => $staff_id,
            "timestamp" => date('Y-m-d H:i:s')
        ), 404);
    }
    
    // Check if superadmin
    $is_superadmin = ($staff["role_id"] == 7 || $staff["is_superadmin"] == 1);
    
    // Get menus
    if ($is_superadmin) {
        $stmt = $pdo->prepare("SELECT * FROM sidebar_menus WHERE is_active = 1 ORDER BY level");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT DISTINCT sm.* 
            FROM sidebar_menus sm
            JOIN permission_category pc ON sm.permission_group_id = pc.perm_group_id
            JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
            WHERE rp.role_id = ? AND rp.can_view = 1 AND sm.is_active = 1
            ORDER BY sm.level
        ");
        $stmt->execute([$staff["role_id"]]);
    }
    
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get submenus for each menu
    foreach ($menus as &$menu) {
        if ($is_superadmin) {
            $stmt = $pdo->prepare("SELECT * FROM sidebar_sub_menus WHERE sidebar_menu_id = ? AND is_active = 1 ORDER BY level");
            $stmt->execute([$menu["id"]]);
        } else {
            $stmt = $pdo->prepare("
                SELECT DISTINCT ssm.* 
                FROM sidebar_sub_menus ssm
                JOIN permission_category pc ON ssm.permission_group_id = pc.perm_group_id
                JOIN roles_permissions rp ON pc.id = rp.perm_cat_id
                WHERE rp.role_id = ? AND rp.can_view = 1 AND ssm.sidebar_menu_id = ? AND ssm.is_active = 1
                ORDER BY ssm.level
            ");
            $stmt->execute([$staff["role_id"], $menu["id"]]);
        }
        
        $menu["submenus"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Success response
    json_response("success", array(
        "status" => 1,
        "message" => "Menu items retrieved successfully",
        "data" => array(
            "staff_id" => $staff_id,
            "staff_info" => array(
                "id" => (int)$staff["id"],
                "name" => $staff["name"],
                "surname" => $staff["surname"],
                "employee_id" => $staff["employee_id"],
                "full_name" => trim($staff["name"] . " " . $staff["surname"])
            ),
            "role" => array(
                "id" => $staff["role_id"] ? (int)$staff["role_id"] : null,
                "name" => $staff["role_name"] ? $staff["role_name"] : "No Role Assigned",
                "is_superadmin" => $is_superadmin
            ),
            "menus" => $menus,
            "total_menus" => count($menus),
            "timestamp" => date('Y-m-d H:i:s')
        )
    ), 200);
    
} catch (PDOException $e) {
    json_response("error", array(
        "status" => 0,
        "message" => "Database error occurred",
        "error" => array(
            "type" => "Database Error",
            "message" => $e->getMessage()
        ),
        "timestamp" => date('Y-m-d H:i:s')
    ), 500);
    
} catch (Exception $e) {
    json_response("error", array(
        "status" => 0,
        "message" => "An unexpected error occurred",
        "error" => array(
            "type" => get_class($e),
            "message" => $e->getMessage()
        ),
        "timestamp" => date('Y-m-d H:i:s')
    ), 500);
}
?>