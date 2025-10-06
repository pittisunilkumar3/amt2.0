<?php
/**
 * End-to-End Menu API Test for Postman
 * This script tests the menu API thoroughly and provides Postman examples
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🧪 Menu API End-to-End Test</h1>";
echo "<p><strong>Testing URL:</strong> <code>POST http://localhost/amt/api/teacher/menu</code></p>";

// Database connection to get test data
try {
    $pdo = new PDO("mysql:host=localhost;dbname=amt", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}

// Get some staff IDs for testing
$stmt = $pdo->query("
    SELECT s.id, s.name, s.surname, s.employee_id, s.is_active,
           r.name as role_name, r.is_superadmin
    FROM staff s
    LEFT JOIN staff_roles sr ON sr.staff_id = s.id
    LEFT JOIN roles r ON r.id = sr.role_id
    WHERE s.is_active = 1
    ORDER BY r.is_superadmin DESC, s.id
    LIMIT 5
");
$test_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>📋 Available Test Staff IDs</h2>";
if ($test_staff) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Staff ID</th><th>Name</th><th>Employee ID</th><th>Role</th><th>Superadmin</th><th>Status</th></tr>";
    foreach ($test_staff as $staff) {
        $superadmin = $staff['is_superadmin'] ? '✅ Yes' : '❌ No';
        $bg = $staff['is_superadmin'] ? '#e8f5e8' : '#f8f9fa';
        echo "<tr style='background: $bg;'>";
        echo "<td><strong>{$staff['id']}</strong></td>";
        echo "<td>{$staff['name']} {$staff['surname']}</td>";
        echo "<td>{$staff['employee_id']}</td>";
        echo "<td>" . ($staff['role_name'] ?: 'No Role') . "</td>";
        echo "<td>$superadmin</td>";
        echo "<td>✅ Active</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No active staff found!</p>";
}

// Test each staff ID
echo "<h2>🔍 API Test Results</h2>";

foreach ($test_staff as $staff) {
    $staff_id = $staff['id'];
    echo "<div style='border: 2px solid #ddd; margin: 20px 0; padding: 15px; border-radius: 8px;'>";
    echo "<h3>Testing Staff ID: {$staff_id} ({$staff['name']} {$staff['surname']})</h3>";
    
    $url = "http://localhost/amt/api/teacher/menu";
    $post_data = json_encode(['staff_id' => (int)$staff_id]);
    
    echo "<div style='background: #f5f5f5; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>📤 Request Details:</strong><br>";
    echo "<code>POST $url</code><br>";
    echo "<code>Content-Type: application/json</code><br>";
    echo "<code>Body: $post_data</code>";
    echo "</div>";
    
    // Make the request
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post_data,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Client-Service: smartschool',
            'Auth-Key: schoolAdmin@'
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    
    echo "<div style='background: " . ($http_code == 200 ? '#e8f5e8' : '#ffebee') . "; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>📥 Response:</strong><br>";
    echo "<strong>HTTP Status:</strong> $http_code<br>";
    
    if ($curl_error) {
        echo "<strong style='color: red;'>❌ cURL Error:</strong> $curl_error<br>";
    } else {
        $result = json_decode($response, true);
        
        if ($result) {
            if ($result['status'] == 1) {
                echo "<strong style='color: green;'>✅ Success!</strong><br>";
                $data = $result['data'] ?? [];
                echo "<strong>Role:</strong> " . ($data['role']['name'] ?? 'Unknown') . "<br>";
                echo "<strong>Is Superadmin:</strong> " . ($data['role']['is_superadmin'] ? 'Yes' : 'No') . "<br>";
                echo "<strong>Total Menus:</strong> " . ($data['total_menus'] ?? 0) . "<br>";
            } else {
                echo "<strong style='color: red;'>❌ API Error:</strong> " . ($result['message'] ?? 'Unknown') . "<br>";
            }
        } else {
            echo "<strong style='color: red;'>❌ Invalid JSON Response</strong><br>";
        }
    }
    echo "</div>";
    
    // Show raw response in details
    echo "<details style='margin: 10px 0;'>";
    echo "<summary>🔍 View Raw Response</summary>";
    echo "<pre style='background: #f8f9fa; padding: 10px; border: 1px solid #dee2e6; border-radius: 5px; max-height: 400px; overflow-y: auto;'>";
    if ($result) {
        echo htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT));
    } else {
        echo htmlspecialchars($response);
    }
    echo "</pre>";
    echo "</details>";
    
    echo "</div>";
}

// Postman collection generation
echo "<h2>📮 Postman Collection</h2>";
echo "<p>Here's a ready-to-use Postman collection for testing:</p>";

$postman_collection = [
    "info" => [
        "name" => "AMT Menu API Tests",
        "description" => "Test collection for the Teacher Menu API",
        "schema" => "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    ],
    "item" => []
];

foreach ($test_staff as $staff) {
    $postman_collection["item"][] = [
        "name" => "Get Menu - Staff {$staff['id']} ({$staff['name']})",
        "request" => [
            "method" => "POST",
            "header" => [
                [
                    "key" => "Content-Type",
                    "value" => "application/json"
                ],
                [
                    "key" => "Client-Service",
                    "value" => "smartschool"
                ],
                [
                    "key" => "Auth-Key",
                    "value" => "schoolAdmin@"
                ]
            ],
            "body" => [
                "mode" => "raw",
                "raw" => json_encode(["staff_id" => (int)$staff['id']], JSON_PRETTY_PRINT)
            ],
            "url" => [
                "raw" => "http://localhost/amt/api/teacher/menu",
                "protocol" => "http",
                "host" => ["localhost"],
                "path" => ["amt", "api", "teacher", "menu"]
            ]
        ]
    ];
}

echo "<textarea rows='20' cols='100' style='width: 100%; font-family: monospace;'>";
echo json_encode($postman_collection, JSON_PRETTY_PRINT);
echo "</textarea>";

echo "<h2>📝 Manual Postman Setup Instructions</h2>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 8px; border-left: 4px solid #2196f3;'>";
echo "<h3>Step-by-step Postman setup:</h3>";
echo "<ol>";
echo "<li><strong>Create new request in Postman</strong></li>";
echo "<li><strong>Set method to:</strong> <code>POST</code></li>";
echo "<li><strong>Set URL to:</strong> <code>http://localhost/amt/api/teacher/menu</code></li>";
echo "<li><strong>Add Headers:</strong>";
echo "<ul>";
echo "<li><code>Content-Type: application/json</code></li>";
echo "<li><code>Client-Service: smartschool</code></li>";
echo "<li><code>Auth-Key: schoolAdmin@</code></li>";
echo "</ul>";
echo "</li>";
echo "<li><strong>Set Body to:</strong> <code>raw</code> and <code>JSON</code></li>";
echo "<li><strong>Add JSON body:</strong>";
if (!empty($test_staff)) {
    echo "<pre><code>";
    echo json_encode(["staff_id" => (int)$test_staff[0]['id']], JSON_PRETTY_PRINT);
    echo "</code></pre>";
}
echo "</li>";
echo "<li><strong>Click Send</strong></li>";
echo "</ol>";
echo "</div>";

// Check for common issues
echo "<h2>🔧 Troubleshooting</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107;'>";
echo "<h3>Common Issues & Solutions:</h3>";
echo "<ul>";
echo "<li><strong>❌ URL typo:</strong> Make sure you use <code>localhost</code> not <code>loaclhost</code></li>";
echo "<li><strong>❌ XAMPP not running:</strong> Ensure Apache and MySQL are started</li>";
echo "<li><strong>❌ Wrong port:</strong> If XAMPP uses port 8080, use <code>http://localhost:8080/amt/api/teacher/menu</code></li>";
echo "<li><strong>❌ Headers missing:</strong> Ensure Content-Type is set to application/json</li>";
echo "<li><strong>❌ Invalid JSON:</strong> Validate your JSON body syntax</li>";
echo "</ul>";
echo "</div>";

// Quick status check
echo "<h2>⚡ Quick System Status</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px;'>";

// Check if the API file exists
$api_file = __DIR__ . '/api/application/controllers/Teacher_webservice.php';
if (file_exists($api_file)) {
    echo "✅ API Controller file exists<br>";
} else {
    echo "❌ API Controller file missing<br>";
}

// Check database connection
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM staff WHERE is_active = 1");
    $count = $stmt->fetch()['count'];
    echo "✅ Database connected - $count active staff members<br>";
} catch (Exception $e) {
    echo "❌ Database connection issue: " . $e->getMessage() . "<br>";
}

// Check if Apache is running (by testing a simple PHP file)
$test_url = "http://localhost/amt/test_menu_api_postman.php";
$test_response = @file_get_contents($test_url . "?ping=1");
if ($test_response) {
    echo "✅ Apache is running and serving PHP<br>";
} else {
    echo "❌ Apache/PHP issue - check XAMPP<br>";
}

echo "</div>";

// Handle ping request
if (isset($_GET['ping'])) {
    die("pong");
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    margin: 20px;
    line-height: 1.6;
}
table {
    border-collapse: collapse;
    width: 100%;
    margin: 10px 0;
}
th, td {
    padding: 12px;
    text-align: left;
    border: 1px solid #ddd;
}
th {
    background-color: #f8f9fa;
    font-weight: 600;
}
code {
    background: #f1f3f4;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
}
pre {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 6px;
    overflow-x: auto;
}
</style>