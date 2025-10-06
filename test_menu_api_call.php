<?php
// Test API call for teacher menu

if (!isset($_GET['staff_id'])) {
    die("Please provide staff_id parameter");
}

$staff_id = $_GET['staff_id'];

echo "<h2>Testing Menu API for Staff ID: {$staff_id}</h2>";

// Database connection to get staff credentials
$mysqli = new mysqli("localhost", "root", "", "school");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get staff credentials
$staff_query = "SELECT s.*, u.email, u.password 
                FROM staff s 
                LEFT JOIN users u ON u.user_id = s.id AND u.role = 'Staff'
                WHERE s.id = ?";
$stmt = $mysqli->prepare($staff_query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$staff_result = $stmt->get_result()->fetch_assoc();

if (!$staff_result) {
    die("Staff member not found");
}

echo "<p><strong>Staff Info:</strong> " . $staff_result['name'] . " " . $staff_result['surname'] . " (" . $staff_result['employee_id'] . ")</p>";

// Simulate API call
$base_url = "http://localhost/amt/api/teacher/menu";
$login_url = "http://localhost/amt/api/teacher/login";

// Test login first to get token
$login_data = array(
    'email' => $staff_result['email'] ?? 'admin@admin.com',
    'password' => 'admin123', // You may need to adjust this
    'app_key' => 'demo_app'
);

echo "<h3>Step 1: Login to get token</h3>";
echo "<p>Login data: " . print_r($login_data, true) . "</p>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $login_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($login_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$login_response = curl_exec($ch);
$login_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p>Login Response (HTTP {$login_http_code}):</p>";
echo "<pre>" . htmlspecialchars($login_response) . "</pre>";

$login_result = json_decode($login_response, true);

if ($login_result && isset($login_result['record']['token'])) {
    $token = $login_result['record']['token'];
    $user_id = $login_result['record']['id'];
    
    echo "<h3>Step 2: Get Menu with Token</h3>";
    echo "<p>Using Token: " . substr($token, 0, 20) . "...</p>";
    echo "<p>Using User ID: " . $user_id . "</p>";
    
    // Now call menu API with token and proper headers
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $base_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@',
        'Authorization: ' . $token,
        'User-ID: ' . $user_id
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $menu_response = curl_exec($ch);
    $menu_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "<p>Menu Response (HTTP {$menu_http_code}):</p>";
    echo "<pre>" . htmlspecialchars($menu_response) . "</pre>";
    
    // Format JSON for better readability
    $menu_result = json_decode($menu_response, true);
    if ($menu_result) {
        echo "<h3>Formatted Menu Data:</h3>";
        echo "<pre>" . print_r($menu_result, true) . "</pre>";
        
        // Show menu count
        if (isset($menu_result['data']['menus'])) {
            echo "<h4>Menu Summary:</h4>";
            echo "<p>Total Menus: " . count($menu_result['data']['menus']) . "</p>";
            echo "<p>Role: " . ($menu_result['data']['role']['name'] ?? 'Unknown') . "</p>";
        }
    }
    
} else {
    echo "<p style='color: red;'>Failed to get token from login. Response:</p>";
    if ($login_result) {
        echo "<pre>" . print_r($login_result, true) . "</pre>";
    }
}

$mysqli->close();
echo "<p><a href='test_menu_api.php'>← Back to Staff List</a></p>";
?>