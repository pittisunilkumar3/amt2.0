<?php
$mysqli = new mysqli('localhost', 'root', '', 'amt');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo 'Connected to database successfully' . PHP_EOL;

// Get current session
$session_result = $mysqli->query('SELECT id FROM sessions WHERE is_active = "yes" LIMIT 1');
$session_id = 1; // Default
if ($session_result && $row = $session_result->fetch_assoc()) {
    $session_id = $row['id'];
}

// Create a test discount request
$stmt = $mysqli->prepare("INSERT INTO fees_discount_approval (student_session_id, student_fees_master_id, fee_groups_feetype_id, amount, date, description, approval_status, is_active, session_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$student_session_id = 1;
$student_fees_master_id = 1;
$fee_groups_feetype_id = 29;
$amount = 500;
$date = date('Y-m-d');
$description = 'Test discount request - Manual creation';
$approval_status = 0; // Pending
$is_active = 1;

$stmt->bind_param("iiidssiis", $student_session_id, $student_fees_master_id, $fee_groups_feetype_id, $amount, $date, $description, $approval_status, $is_active, $session_id);

if ($stmt->execute()) {
    echo "✅ Test discount request created successfully!" . PHP_EOL;
    echo "Inserted ID: " . $mysqli->insert_id . PHP_EOL;
    
    // Verify the insertion
    $verify_result = $mysqli->query("SELECT * FROM fees_discount_approval WHERE id = " . $mysqli->insert_id);
    if ($verify_result && $row = $verify_result->fetch_assoc()) {
        echo "✅ Verification successful:" . PHP_EOL;
        echo "ID: " . $row['id'] . PHP_EOL;
        echo "Student Session ID: " . $row['student_session_id'] . PHP_EOL;
        echo "Amount: " . $row['amount'] . PHP_EOL;
        echo "Status: " . $row['approval_status'] . PHP_EOL;
        echo "Description: " . $row['description'] . PHP_EOL;
        echo "Created: " . $row['created_at'] . PHP_EOL;
    }
} else {
    echo "❌ Error creating discount request: " . $stmt->error . PHP_EOL;
}

$stmt->close();
$mysqli->close();
?>
