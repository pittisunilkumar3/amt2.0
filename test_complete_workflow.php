<?php
$mysqli = new mysqli('localhost', 'root', '', 'amt');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo 'Testing complete discount workflow...' . PHP_EOL;

// First, clear any existing test records
$mysqli->query("DELETE FROM fees_discount_approval WHERE description LIKE 'Test discount%'");

// Create a new test discount request
$stmt = $mysqli->prepare("INSERT INTO fees_discount_approval (student_session_id, student_fees_master_id, fee_groups_feetype_id, amount, date, description, approval_status, is_active, session_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$student_session_id = 1;
$student_fees_master_id = 1;
$fee_groups_feetype_id = 29;
$amount = 300;
$date = date('Y-m-d');
$description = 'Test discount workflow - Complete test';
$approval_status = 0; // Pending
$is_active = 1;
$session_id = 1;

$stmt->bind_param("iiidssiis", $student_session_id, $student_fees_master_id, $fee_groups_feetype_id, $amount, $date, $description, $approval_status, $is_active, $session_id);

if ($stmt->execute()) {
    $discount_id = $mysqli->insert_id;
    echo "✅ Test discount request created successfully!" . PHP_EOL;
    echo "Discount ID: " . $discount_id . PHP_EOL;
    
    // Check current status
    $result = $mysqli->query("SELECT * FROM fees_discount_approval WHERE id = " . $discount_id);
    if ($result && $row = $result->fetch_assoc()) {
        echo "Current status:" . PHP_EOL;
        echo "- Approval Status: " . $row['approval_status'] . " (0=pending, 1=approved, 2=rejected)" . PHP_EOL;
        echo "- Payment ID: " . ($row['payment_id'] ? $row['payment_id'] : 'NULL') . PHP_EOL;
        echo "- Amount: " . $row['amount'] . PHP_EOL;
    }
    
    echo PHP_EOL . "You can now test the following workflow:" . PHP_EOL;
    echo "1. Go to http://localhost/amt/admin/feesdiscountapproval" . PHP_EOL;
    echo "2. Search for the discount request" . PHP_EOL;
    echo "3. Approve it and check if payment_id is set" . PHP_EOL;
    echo "4. Revert it and check if payment_id is cleared and status is back to pending" . PHP_EOL;
    echo "5. Check the student fee page to see if discount is properly applied/removed" . PHP_EOL;
    
} else {
    echo "❌ Error creating test discount request: " . $stmt->error . PHP_EOL;
}

$stmt->close();
$mysqli->close();
?>
