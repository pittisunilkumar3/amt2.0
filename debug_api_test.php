<?php
/**
 * Debug API Test
 * This will help identify what's causing the 500 error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h2>🔍 API Debug Test</h2>";

// Check if CodeIgniter is loading properly
try {
    echo "<h3>1. Basic PHP Check</h3>";
    echo "✅ PHP is working<br>";
    echo "PHP Version: " . phpversion() . "<br>";
    
    echo "<h3>2. Database Connection Test</h3>";
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
        echo "✅ Database connection works<br>";
        
        // Test staff table
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM staff LIMIT 1");
        $count = $stmt->fetch()['count'];
        echo "✅ Staff table accessible ($count records)<br>";
        
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
    }
    
    echo "<h3>3. CodeIgniter Path Test</h3>";
    $ci_path = __DIR__ . '/api/index.php';
    if (file_exists($ci_path)) {
        echo "✅ CodeIgniter index.php exists<br>";
    } else {
        echo "❌ CodeIgniter index.php missing<br>";
    }
    
    $controller_path = __DIR__ . '/api/application/controllers/Teacher_webservice.php';
    if (file_exists($controller_path)) {
        echo "✅ Teacher_webservice.php exists<br>";
    } else {
        echo "❌ Teacher_webservice.php missing<br>";
    }
    
    echo "<h3>4. Direct API Test</h3>";
    
    // Test the API endpoint with different methods
    $test_urls = [
        "http://localhost/amt/api/index.php/teacher/menu",
        "http://localhost/amt/api/teacher/menu",
        "http://localhost/amt/api/index.php/teacher_webservice/menu"
    ];
    
    foreach ($test_urls as $url) {
        echo "<h4>Testing: $url</h4>";
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['staff_id' => 1]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "❌ cURL Error: $error<br>";
        } else {
            echo "HTTP Code: $http_code<br>";
            if ($http_code == 200) {
                echo "✅ Success!<br>";
                $result = json_decode($response, true);
                if ($result && isset($result['status'])) {
                    echo "API Status: " . $result['status'] . "<br>";
                    echo "Message: " . ($result['message'] ?? 'No message') . "<br>";
                }
            } else {
                echo "❌ HTTP Error<br>";
                echo "Response: " . substr($response, 0, 200) . "...<br>";
            }
        }
        echo "<hr>";
    }
    
    echo "<h3>5. Manual Controller Test</h3>";
    echo "Let's try to load the controller manually...<br>";
    
    // Try to access CodeIgniter directly
    $_POST = json_decode('{"staff_id": 1}', true);
    $_SERVER['REQUEST_METHOD'] = 'POST';
    
    // Basic test without CodeIgniter framework
    echo "<h4>Testing API Logic Manually:</h4>";
    
    try {
        // Test database query directly
        $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
        $stmt = $pdo->prepare("
            SELECT s.id, s.name, s.surname, s.employee_id, s.is_active
            FROM staff s 
            WHERE s.id = ? AND s.is_active = 1
        ");
        $stmt->execute([1]);
        $staff = $stmt->fetch(PDO::FETCH_OBJ);
        
        if ($staff) {
            echo "✅ Staff ID 1 found: {$staff->name} {$staff->surname}<br>";
            
            // Test role query
            $stmt = $pdo->prepare("
                SELECT r.id, r.name, r.slug, r.is_superadmin
                FROM staff_roles sr
                JOIN roles r ON r.id = sr.role_id
                WHERE sr.staff_id = ? AND sr.is_active = 1
            ");
            $stmt->execute([1]);
            $role = $stmt->fetch(PDO::FETCH_OBJ);
            
            if ($role) {
                echo "✅ Role found: {$role->name} (Superadmin: " . ($role->is_superadmin ? 'Yes' : 'No') . ")<br>";
            } else {
                echo "⚠️ No role assigned to staff ID 1<br>";
            }
            
            // Test menu query
            $stmt = $pdo->query("
                SELECT COUNT(*) as count
                FROM sidebar_menus sm
                WHERE sm.is_active = 1 AND sm.sidebar_display = 1
            ");
            $menu_count = $stmt->fetch()['count'];
            echo "✅ Available menus: $menu_count<br>";
            
        } else {
            echo "❌ Staff ID 1 not found or inactive<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Manual test error: " . $e->getMessage() . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Critical error: " . $e->getMessage();
}

echo "<h3>6. Error Log Check</h3>";
$error_log_path = __DIR__ . '/api/application/logs/';
if (is_dir($error_log_path)) {
    echo "✅ Logs directory exists<br>";
    $log_files = glob($error_log_path . '*.php');
    if ($log_files) {
        echo "Log files found:<br>";
        foreach ($log_files as $log_file) {
            echo "- " . basename($log_file) . "<br>";
        }
    } else {
        echo "No log files found<br>";
    }
} else {
    echo "❌ Logs directory not found<br>";
}

// Check PHP error log
$php_error_log = ini_get('error_log');
if ($php_error_log && file_exists($php_error_log)) {
    echo "PHP error log: $php_error_log<br>";
} else {
    echo "PHP error log not configured<br>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h3, h4 { color: #333; }
hr { margin: 20px 0; }
</style>