<?php
$mysqli = new mysqli('localhost', 'root', '', 'amt');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo 'Connected to database successfully' . PHP_EOL;

// Check for existing student_session records
$result = $mysqli->query('SELECT student_session.id as student_session_id, students.firstname, students.lastname, students.admission_no FROM student_session JOIN students ON student_session.student_id = students.id LIMIT 5');
if ($result) {
    echo 'Available student sessions:' . PHP_EOL;
    while ($row = $result->fetch_assoc()) {
        echo 'Student Session ID: ' . $row['student_session_id'] . ' - ' . $row['firstname'] . ' ' . $row['lastname'] . ' (' . $row['admission_no'] . ')' . PHP_EOL;
    }
} else {
    echo 'Error: ' . $mysqli->error . PHP_EOL;
}

echo PHP_EOL;

// Check for existing student_fees_master records
$result = $mysqli->query('SELECT id, student_session_id FROM student_fees_master LIMIT 5');
if ($result) {
    echo 'Available student fees master records:' . PHP_EOL;
    while ($row = $result->fetch_assoc()) {
        echo 'Fees Master ID: ' . $row['id'] . ' - Student Session ID: ' . $row['student_session_id'] . PHP_EOL;
    }
} else {
    echo 'Error: ' . $mysqli->error . PHP_EOL;
}

echo PHP_EOL;

// Check for existing fee_groups_feetype records
$result = $mysqli->query('SELECT id, feetype_id FROM fee_groups_feetype LIMIT 5');
if ($result) {
    echo 'Available fee groups feetype records:' . PHP_EOL;
    while ($row = $result->fetch_assoc()) {
        echo 'Fee Groups Feetype ID: ' . $row['id'] . ' - Feetype ID: ' . $row['feetype_id'] . PHP_EOL;
    }
} else {
    echo 'Error: ' . $mysqli->error . PHP_EOL;
}

$mysqli->close();
?>
