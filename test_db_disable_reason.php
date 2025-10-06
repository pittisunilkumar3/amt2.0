<?php
/**
 * Test Database Connection and Disable Reason Table
 */

// Database configuration (adjust as needed)
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Database Test Results</h1>";
    echo "<p><strong>Database Connection:</strong> ✅ Success</p>";
    
    // Check if disable_reason table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'disable_reason'");
    if ($stmt->rowCount() > 0) {
        echo "<p><strong>disable_reason table:</strong> ✅ Exists</p>";
        
        // Check table structure
        echo "<h3>Table Structure</h3>";
        $stmt = $pdo->query("DESCRIBE disable_reason");
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
        
        // Check existing data
        echo "<h3>Existing Data</h3>";
        $stmt = $pdo->query("SELECT * FROM disable_reason ORDER BY id");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($rows) > 0) {
            echo "<p><strong>Records found:</strong> " . count($rows) . "</p>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Reason</th><th>Created At</th></tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['reason']) . "</td>";
                echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p><strong>Records found:</strong> 0 (Table is empty)</p>";
            
            // Insert some test data
            echo "<h3>Inserting Test Data</h3>";
            $test_reasons = [
                'Medical Leave',
                'Personal Reasons',
                'Academic Issues',
                'Disciplinary Action',
                'Transfer to Another School'
            ];
            
            $insert_stmt = $pdo->prepare("INSERT INTO disable_reason (reason) VALUES (?)");
            foreach ($test_reasons as $reason) {
                $insert_stmt->execute([$reason]);
                echo "<p>✅ Inserted: " . htmlspecialchars($reason) . "</p>";
            }
            
            echo "<p><strong>Test data inserted successfully!</strong></p>";
        }
        
    } else {
        echo "<p><strong>disable_reason table:</strong> ❌ Does not exist</p>";
        
        // Create the table
        echo "<h3>Creating disable_reason Table</h3>";
        $create_sql = "
            CREATE TABLE `disable_reason` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `reason` varchar(255) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci
        ";
        
        $pdo->exec($create_sql);
        echo "<p>✅ Table created successfully!</p>";
        
        // Insert test data
        echo "<h3>Inserting Test Data</h3>";
        $test_reasons = [
            'Medical Leave',
            'Personal Reasons',
            'Academic Issues',
            'Disciplinary Action',
            'Transfer to Another School'
        ];
        
        $insert_stmt = $pdo->prepare("INSERT INTO disable_reason (reason) VALUES (?)");
        foreach ($test_reasons as $reason) {
            $insert_stmt->execute([$reason]);
            echo "<p>✅ Inserted: " . htmlspecialchars($reason) . "</p>";
        }
        
        echo "<p><strong>Test data inserted successfully!</strong></p>";
    }
    
    // Test sch_settings table
    echo "<h3>Testing sch_settings Table</h3>";
    $stmt = $pdo->query("SHOW TABLES LIKE 'sch_settings'");
    if ($stmt->rowCount() > 0) {
        echo "<p><strong>sch_settings table:</strong> ✅ Exists</p>";
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM sch_settings");
        $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p><strong>Records in sch_settings:</strong> $count</p>";
        
        if ($count == 0) {
            echo "<p><strong>Warning:</strong> sch_settings table is empty. This might cause issues with the API.</p>";
        }
    } else {
        echo "<p><strong>sch_settings table:</strong> ❌ Does not exist</p>";
    }
    
} catch (PDOException $e) {
    echo "<h1>Database Test Results</h1>";
    echo "<p><strong>Database Connection:</strong> ❌ Failed</p>";
    echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
