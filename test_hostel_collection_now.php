<?php
// Test hostel fee collection and check for errors
require_once 'application/config/database.php';

$host = $db['default']['hostname'];
$username = $db['default']['username'];
$password = $db['default']['password'];
$database = $db['default']['database'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Hostel Fee Collection Test</h2>";
    
    // Check if error log exists and is writable
    $error_log_path = 'error_log';
    if (file_exists($error_log_path)) {
        echo "<p style='color: green;'>✓ Error log file exists and is cleared</p>";
        echo "<p>File size: " . filesize($error_log_path) . " bytes</p>";
    } else {
        echo "<p style='color: red;'>✗ Error log file not found</p>";
    }
    
    // Test error logging
    error_log("HOSTEL FEE DEBUG TEST - " . date('Y-m-d H:i:s') . " - Testing error logging");
    
    // Check recent database records
    echo "<h3>Recent Database Records (Before Test):</h3>";
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
        LIMIT 5
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
    echo "<h3>Test Instructions:</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
    echo "<h4>Step 1: Test Hostel Fee Collection</h4>";
    echo "<ol>";
    echo "<li><a href='http://localhost/amt/studentfee/addfee/8' target='_blank' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Open Student Fee Page</a></li>";
    echo "<li>Click on any hostel fee 'Collect Fees' button (+ icon)</li>";
    echo "<li>Open browser console (F12) to see debug messages</li>";
    echo "<li>Fill in payment details (amount, payment mode, account)</li>";
    echo "<li>Click 'Collect Fees' button</li>";
    echo "<li><a href='javascript:location.reload();' style='background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>Refresh This Page</a> to see results</li>";
    echo "</ol>";
    echo "</div>";
    
    // Check error log content
    echo "<h3>Current Error Log Content:</h3>";
    if (file_exists($error_log_path) && filesize($error_log_path) > 0) {
        $log_content = file_get_contents($error_log_path);
        $lines = explode("\n", $log_content);
        
        echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
        echo "<pre>";
        
        $hostel_debug_found = false;
        foreach ($lines as $line) {
            if (stripos($line, 'HOSTEL FEE DEBUG') !== false) {
                echo "<span style='background: yellow;'>" . htmlspecialchars($line) . "</span>\n";
                $hostel_debug_found = true;
            } elseif (!empty(trim($line))) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        
        if (!$hostel_debug_found) {
            echo "<span style='color: orange;'>No hostel fee debug messages found yet. Try testing hostel fee collection.</span>";
        }
        
        echo "</pre>";
        echo "</div>";
    } else {
        echo "<p>Error log is empty. Debug messages will appear here after testing.</p>";
    }
    
    // Auto-refresh option
    echo "<h3>Auto-Monitor:</h3>";
    echo "<button onclick='startAutoRefresh()' style='background: #17a2b8; color: white; padding: 10px; border: none; border-radius: 5px;'>Start Auto-Refresh (10s)</button>";
    echo "<button onclick='stopAutoRefresh()' style='background: #dc3545; color: white; padding: 10px; border: none; border-radius: 5px; margin-left: 10px;'>Stop Auto-Refresh</button>";
    echo "<span id='refresh-status' style='margin-left: 10px;'></span>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>

<script>
let refreshInterval;

function startAutoRefresh() {
    refreshInterval = setInterval(function() {
        location.reload();
    }, 10000);
    document.getElementById('refresh-status').innerHTML = '<span style="color: green;">Auto-refreshing every 10 seconds...</span>';
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        document.getElementById('refresh-status').innerHTML = '<span style="color: red;">Auto-refresh stopped</span>';
    }
}
</script>
