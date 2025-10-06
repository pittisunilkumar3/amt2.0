<?php
$mysqli = new mysqli('localhost', 'root', '', 'amt');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}
echo 'Connected to database successfully' . PHP_EOL;

// Check table structure
$result = $mysqli->query('DESCRIBE fees_discount_approval');
if ($result) {
    echo 'fees_discount_approval table structure:' . PHP_EOL;
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . PHP_EOL;
    }
} else {
    echo 'Error: ' . $mysqli->error . PHP_EOL;
}

echo PHP_EOL;

// Check existing discount requests
$result = $mysqli->query('SELECT * FROM fees_discount_approval ORDER BY created_at DESC LIMIT 10');
if ($result) {
    echo 'Recent discount approval records:' . PHP_EOL;
    while ($row = $result->fetch_assoc()) {
        echo 'ID: ' . $row['id'] . ' - Student Session ID: ' . $row['student_session_id'] . ' - Amount: ' . $row['amount'] . ' - Status: ' . $row['approval_status'] . ' - Created: ' . $row['created_at'] . PHP_EOL;
    }
} else {
    echo 'Error: ' . $mysqli->error . PHP_EOL;
}

$mysqli->close();
?>
