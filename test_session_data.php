<?php
// Test the detailed data for session 21 (2025-26)
require_once 'application/config/database.php';

try {
    $pdo = new PDO('mysql:host=' . $db['default']['hostname'] . ';dbname=' . $db['default']['database'], $db['default']['username'], $db['default']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if there are any student_session records for session 21
    $stmt = $pdo->prepare('SELECT COUNT(*) as count FROM student_session WHERE session_id = 21');
    $stmt->execute();
    $sessionCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Student sessions for 2025-26: ' . $sessionCount['count'] . PHP_EOL;
    
    // Check if there are any fee records for session 21
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as count 
        FROM student_fees_master sfm 
        INNER JOIN student_session ss ON ss.id = sfm.student_session_id 
        WHERE ss.session_id = 21
    ');
    $stmt->execute();
    $feeCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Fee records for 2025-26: ' . $feeCount['count'] . PHP_EOL;
    
    // Check if there are any fee collection records for session 21
    $stmt = $pdo->prepare('
        SELECT COUNT(*) as count 
        FROM student_fees_deposite sfd
        INNER JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
        INNER JOIN student_session ss ON ss.id = sfm.student_session_id 
        WHERE ss.session_id = 21
    ');
    $stmt->execute();
    $collectionCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'Collection records for 2025-26: ' . $collectionCount['count'] . PHP_EOL;
    
    // Let's also check what sessions exist
    $stmt = $pdo->prepare('SELECT id, session FROM sessions ORDER BY id');
    $stmt->execute();
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo PHP_EOL . 'Available sessions:' . PHP_EOL;
    foreach ($sessions as $session) {
        echo "ID: {$session['id']}, Session: {$session['session']}" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>
