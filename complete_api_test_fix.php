<?php
/**
 * Complete API Testing and Fix
 */

echo "<h2>Complete API Testing and Fix</h2>";

// Test 1: Direct file access
echo "<h3>Test 1: Direct File Access</h3>";
$teacher_webservice_path = __DIR__ . '/api/application/controllers/Teacher_webservice.php';
if (file_exists($teacher_webservice_path)) {
    echo "✅ Teacher_webservice.php exists<br>";
} else {
    echo "❌ Teacher_webservice.php missing<br>";
    exit;
}

// Test 2: Database connection without CodeIgniter
echo "<h3>Test 2: Direct Database Connection</h3>";
$db_config_path = __DIR__ . '/api/application/config/database.php';
if (file_exists($db_config_path)) {
    include $db_config_path;
    
    $hostname = $db['default']['hostname'];
    $username = $db['default']['username'];
    $password = $db['default']['password'];
    $database = $db['default']['database'];
    
    try {
        $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Database connection successful<br>";
        
        // Test staff table
        $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ? LIMIT 1");
        $stmt->execute([24]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($staff) {
            echo "✅ Staff ID 24 found: " . $staff['name'] . "<br>";
        } else {
            echo "❌ Staff ID 24 not found<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Database config not found<br>";
}

// Test 3: Create standalone API endpoint
echo "<h3>Test 3: Create Standalone API Endpoint</h3>";

$standalone_api = '<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    exit(0);
}

try {
    // Database connection
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "amt";
    
    $pdo = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        throw new Exception("Only POST method allowed");
    }
    
    $input = json_decode(file_get_contents("php://input"), true);
    $staff_id = isset($input["staff_id"]) ? intval($input["staff_id"]) : 0;
    
    if (!$staff_id) {
        throw new Exception("staff_id is required");
    }
    
    // Get staff info
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
    $stmt->execute([$staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$staff) {
        throw new Exception("Staff not found");
    }
    
    // Get role info
    $stmt = $pdo->prepare("
        SELECT r.* 
        FROM roles r 
        JOIN staff_roles sr ON r.id = sr.role_id 
        WHERE sr.staff_id = ?
    ");
    $stmt->execute([$staff_id]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if superadmin
    $is_superadmin = false;
    if ($role) {
        $is_superadmin = ($role["id"] == 7 || $role["is_superadmin"] == 1);
    }
    
    // Get menus
    if ($is_superadmin) {
        $stmt = $pdo->prepare("SELECT * FROM sidebar_menus WHERE status = 1 ORDER BY sort_order");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT DISTINCT sm.* 
            FROM sidebar_menus sm
            JOIN permission_category_mapping pcm ON sm.id = pcm.sidebar_menu_id
            JOIN staff_designation_permissions sdp ON pcm.permission_category_id = sdp.permission_category_id
            JOIN staff s ON s.designation = sdp.designation
            WHERE s.id = ? AND sm.status = 1
            ORDER BY sm.sort_order
        ");
        $stmt->execute([$staff_id]);
    }
    
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get submenus for each menu
    foreach ($menus as &$menu) {
        if ($is_superadmin) {
            $stmt = $pdo->prepare("SELECT * FROM sidebar_sub_menus WHERE sidebar_menu_id = ? AND status = 1 ORDER BY sort_order");
            $stmt->execute([$menu["id"]]);
        } else {
            $stmt = $pdo->prepare("
                SELECT DISTINCT ssm.* 
                FROM sidebar_sub_menus ssm
                JOIN permission_category_mapping pcm ON ssm.id = pcm.sidebar_sub_menu_id
                JOIN staff_designation_permissions sdp ON pcm.permission_category_id = sdp.permission_category_id
                JOIN staff s ON s.designation = sdp.designation
                WHERE s.id = ? AND ssm.sidebar_menu_id = ? AND ssm.status = 1
                ORDER BY ssm.sort_order
            ");
            $stmt->execute([$staff_id, $menu["id"]]);
        }
        
        $menu["submenus"] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        "status" => "success",
        "data" => [
            "staff" => $staff,
            "role" => $role,
            "is_superadmin" => $is_superadmin,
            "menus" => $menus
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>';

file_put_contents(__DIR__ . '/standalone_menu_api.php', $standalone_api);
echo "✅ Created standalone API at: http://localhost/amt/standalone_menu_api.php<br>";

// Test 4: Test the standalone API
echo "<h3>Test 4: Testing Standalone API</h3>";

$test_data = json_encode(['staff_id' => 24]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/amt/standalone_menu_api.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $test_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($test_data)
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error<br>";
} else {
    echo "✅ HTTP Code: $http_code<br>";
    echo "📋 Response:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars($response);
    echo "</pre>";
}

// Test 5: Check CodeIgniter bootstrap
echo "<h3>Test 5: CodeIgniter Bootstrap Test</h3>";

$simple_ci_test = '<?php
try {
    define("BASEPATH", "");
    require_once("' . __DIR__ . '/api/system/core/Common.php");
    echo "✅ CodeIgniter Common.php loaded successfully<br>";
} catch (Exception $e) {
    echo "❌ CodeIgniter bootstrap failed: " . $e->getMessage() . "<br>";
}
?>';

file_put_contents(__DIR__ . '/test_ci_bootstrap.php', $simple_ci_test);
echo "✅ Created CI bootstrap test<br>";

echo "<h3>Summary</h3>";
echo "1. Use the standalone API for immediate testing: <strong>http://localhost/amt/standalone_menu_api.php</strong><br>";
echo "2. Check the CI bootstrap test for framework issues<br>";
echo "3. If standalone works, the issue is with CodeIgniter configuration<br>";
echo "4. If standalone fails, the issue is with database or server setup<br>";

?>