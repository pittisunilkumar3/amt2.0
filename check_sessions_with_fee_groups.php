<?php
/**
 * Check which sessions have fee groups
 */

// Database connection
$host = 'localhost';
$dbname = 'amt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking sessions with fee groups...\n\n";
    
    // Get sessions with fee group counts
    $sql = "
        SELECT 
            s.id,
            s.session,
            s.is_active,
            COUNT(DISTINCT fsg.id) as fee_group_count
        FROM sessions s
        LEFT JOIN fee_session_groups fsg ON fsg.session_id = s.id
        GROUP BY s.id
        ORDER BY s.id DESC
        LIMIT 10
    ";
    
    $stmt = $pdo->query($sql);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Recent Sessions:\n";
    echo str_repeat("-", 80) . "\n";
    printf("%-5s %-20s %-10s %-15s\n", "ID", "Session", "Active", "Fee Groups");
    echo str_repeat("-", 80) . "\n";
    
    $session_with_groups = null;
    
    foreach ($sessions as $session) {
        printf("%-5s %-20s %-10s %-15s\n", 
            $session['id'], 
            $session['session'], 
            $session['is_active'],
            $session['fee_group_count']
        );
        
        if ($session['fee_group_count'] > 0 && $session_with_groups === null) {
            $session_with_groups = $session['id'];
        }
    }
    
    echo "\n";
    
    if ($session_with_groups) {
        echo "✓ Found session with fee groups: Session ID {$session_with_groups}\n\n";
        
        // Get details of fee groups for this session
        $sql = "
            SELECT 
                fsg.id as fee_session_group_id,
                fg.id as fee_group_id,
                fg.name as fee_group_name,
                COUNT(DISTINCT fgf.id) as fee_type_count
            FROM fee_session_groups fsg
            JOIN fee_groups fg ON fg.id = fsg.fee_groups_id
            LEFT JOIN fee_groups_feetype fgf ON fgf.fee_session_group_id = fsg.id
            WHERE fsg.session_id = ?
            GROUP BY fsg.id
            LIMIT 5
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$session_with_groups]);
        $fee_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Fee Groups in Session {$session_with_groups}:\n";
        echo str_repeat("-", 80) . "\n";
        
        foreach ($fee_groups as $fg) {
            echo "- {$fg['fee_group_name']} (ID: {$fg['fee_group_id']}) - {$fg['fee_type_count']} fee types\n";
        }
        
        echo "\n\nTest the API with this session:\n";
        echo "curl -X POST \"http://localhost/amt/api/session-fee-structure/filter\" \\\n";
        echo "  -H \"Content-Type: application/json\" \\\n";
        echo "  -H \"Client-Service: smartschool\" \\\n";
        echo "  -H \"Auth-Key: schoolAdmin@\" \\\n";
        echo "  -d '{\"session_id\": {$session_with_groups}}'\n";
    } else {
        echo "✗ No sessions with fee groups found in the database\n";
    }
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}

