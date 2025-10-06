<?php
// Test script to simulate hostel fee collection and check debug logs
require_once 'application/config/database.php';

$host = $db['default']['hostname'];
$username = $db['default']['username'];
$password = $db['default']['password'];
$database = $db['default']['database'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Hostel Fee Collection Test & Debug</h2>";
    
    // Check error log file
    $error_log_path = ini_get('error_log');
    if (empty($error_log_path)) {
        $error_log_path = '/xampp/apache/logs/error.log'; // Default XAMPP path
    }
    
    echo "<h3>1. Debug Log Location:</h3>";
    echo "<p>Error log path: <code>$error_log_path</code></p>";
    
    if (file_exists($error_log_path)) {
        echo "<p style='color: green;'>✓ Error log file exists</p>";
        
        // Read last 50 lines of error log
        $lines = file($error_log_path);
        $recent_lines = array_slice($lines, -50);
        
        echo "<h3>2. Recent Debug Logs (Last 50 lines):</h3>";
        echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 400px; overflow-y: auto;'>";
        echo "<pre>";
        foreach ($recent_lines as $line) {
            if (strpos($line, 'HOSTEL FEE DEBUG') !== false) {
                echo "<span style='background: yellow;'>" . htmlspecialchars($line) . "</span>";
            } else {
                echo htmlspecialchars($line);
            }
        }
        echo "</pre>";
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>✗ Error log file not found</p>";
        echo "<p>Try these common paths:</p>";
        echo "<ul>";
        echo "<li>/xampp/apache/logs/error.log</li>";
        echo "<li>/var/log/apache2/error.log</li>";
        echo "<li>C:\\xampp\\apache\\logs\\error.log</li>";
        echo "</ul>";
    }
    
    // Check recent database records
    echo "<h3>3. Recent Database Records:</h3>";
    $stmt = $pdo->prepare("
        SELECT id, student_session_id, student_fees_master_id, fee_groups_feetype_id, 
               student_transport_fee_id, student_hostel_fee_id, created_at,
               CASE 
                   WHEN student_hostel_fee_id IS NOT NULL AND student_hostel_fee_id > 0 THEN 'HOSTEL'
                   WHEN student_transport_fee_id IS NOT NULL AND student_transport_fee_id > 0 THEN 'TRANSPORT'
                   WHEN student_fees_master_id IS NOT NULL AND student_fees_master_id > 0 THEN 'REGULAR'
                   ELSE 'UNKNOWN'
               END as fee_type
        FROM student_fees_deposite 
        ORDER BY id DESC 
        LIMIT 10
    ");
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Type</th><th>Session ID</th><th>Master ID</th><th>Fee Type ID</th><th>Transport ID</th><th>Hostel ID</th><th>Created</th><th>Status</th></tr>";
    
    foreach ($records as $record) {
        $row_color = "";
        $status = "";
        
        if ($record['fee_type'] == 'HOSTEL') {
            if ($record['student_session_id'] > 0 && $record['student_hostel_fee_id'] > 0) {
                $row_color = "style='background-color: #d4edda;'"; // Green - correct
                $status = "✓ CORRECT";
            } else {
                $row_color = "style='background-color: #f8d7da;'"; // Red - incorrect
                $status = "✗ INCORRECT";
            }
        } elseif ($record['fee_type'] == 'TRANSPORT') {
            $row_color = "style='background-color: #d1ecf1;'"; // Blue
            $status = "TRANSPORT";
        } else {
            $status = "REGULAR";
        }
        
        echo "<tr $row_color>";
        echo "<td>" . $record['id'] . "</td>";
        echo "<td>" . $record['fee_type'] . "</td>";
        echo "<td>" . ($record['student_session_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['student_fees_master_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['fee_groups_feetype_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['student_transport_fee_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['student_hostel_fee_id'] ?: 'NULL') . "</td>";
        echo "<td>" . substr($record['created_at'], 0, 16) . "</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Instructions for testing
    echo "<h3>4. Testing Instructions:</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
    echo "<h4>To Test Hostel Fee Collection:</h4>";
    echo "<ol>";
    echo "<li><strong>Go to:</strong> <a href='http://localhost/amt/studentfee/addfee/8' target='_blank'>Student Fee Collection Page</a></li>";
    echo "<li><strong>Click:</strong> Any hostel fee 'Collect Fees' button (+ icon)</li>";
    echo "<li><strong>Fill:</strong> Payment details (amount, payment mode, account)</li>";
    echo "<li><strong>Click:</strong> 'Collect Fees' button</li>";
    echo "<li><strong>Refresh:</strong> This page to see debug logs and new database record</li>";
    echo "</ol>";
    
    echo "<h4>What to Look For:</h4>";
    echo "<ul>";
    echo "<li><strong>Debug Logs:</strong> Should show 'HOSTEL FEE DEBUG' messages with data values</li>";
    echo "<li><strong>Database Record:</strong> New row with correct student_hostel_fee_id and student_session_id</li>";
    echo "<li><strong>Green Row:</strong> Indicates successful hostel fee collection</li>";
    echo "</ul>";
    echo "</div>";
    
    // Clear debug logs button
    echo "<h3>5. Clear Debug Logs:</h3>";
    echo "<form method='post'>";
    echo "<button type='submit' name='clear_logs' style='background: #dc3545; color: white; padding: 10px; border: none; border-radius: 5px;'>Clear Debug Logs</button>";
    echo "</form>";
    
    if (isset($_POST['clear_logs'])) {
        if (file_exists($error_log_path)) {
            file_put_contents($error_log_path, '');
            echo "<p style='color: green;'>✓ Debug logs cleared</p>";
            echo "<script>setTimeout(function(){ location.reload(); }, 1000);</script>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>
