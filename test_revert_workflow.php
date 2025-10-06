<?php
$mysqli = new mysqli('localhost', 'root', '', 'amt');
if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo 'Testing comprehensive discount workflow with revert functionality...' . PHP_EOL;

// Function to get discount status
function getDiscountStatus($mysqli, $id) {
    $result = $mysqli->query("SELECT approval_status, payment_id FROM fees_discount_approval WHERE id = " . $id);
    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    }
    return null;
}

// Function to check if payment record exists
function checkPaymentRecord($mysqli, $payment_id) {
    if (empty($payment_id)) return false;
    
    $parts = explode('/', $payment_id);
    if (count($parts) < 2) return false;
    
    $result = $mysqli->query("SELECT * FROM student_fees_deposite WHERE id = " . $parts[0] . " AND inv_no = " . $parts[1]);
    return $result && $result->num_rows > 0;
}

// Clear previous test records
$mysqli->query("DELETE FROM fees_discount_approval WHERE description LIKE 'Test revert workflow%'");

// Create a test discount request
$stmt = $mysqli->prepare("INSERT INTO fees_discount_approval (student_session_id, student_fees_master_id, fee_groups_feetype_id, amount, date, description, approval_status, is_active, session_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");

$student_session_id = 1;
$student_fees_master_id = 1;
$fee_groups_feetype_id = 29;
$amount = 500;
$date = date('Y-m-d');
$description = 'Test revert workflow - Step by step test';
$approval_status = 0; // Pending
$is_active = 1;
$session_id = 1;

$stmt->bind_param("iiidssiis", $student_session_id, $student_fees_master_id, $fee_groups_feetype_id, $amount, $date, $description, $approval_status, $is_active, $session_id);

if ($stmt->execute()) {
    $discount_id = $mysqli->insert_id;
    echo "✅ Step 1: Created test discount request (ID: $discount_id)" . PHP_EOL;
    
    // Check initial status
    $status = getDiscountStatus($mysqli, $discount_id);
    echo "   - Initial status: " . $status['approval_status'] . " (0=pending)" . PHP_EOL;
    echo "   - Initial payment_id: " . ($status['payment_id'] ? $status['payment_id'] : 'NULL') . PHP_EOL;
    
    echo PHP_EOL . "📋 Test Plan:" . PHP_EOL;
    echo "1. ✅ Create discount request (DONE)" . PHP_EOL;
    echo "2. 🔄 Go to admin panel and approve it" . PHP_EOL;
    echo "3. 🔄 Check that payment_id is set and status = 1" . PHP_EOL;
    echo "4. 🔄 Revert the approval" . PHP_EOL;
    echo "5. 🔄 Check that payment_id is NULL and status = 0" . PHP_EOL;
    echo "6. 🔄 Verify payment record is deleted" . PHP_EOL;
    echo "7. 🔄 Check student fee page shows discount button enabled again" . PHP_EOL;
    
    echo PHP_EOL . "🌐 URLs to test:" . PHP_EOL;
    echo "- Approval page: http://localhost/amt/admin/feesdiscountapproval" . PHP_EOL;
    echo "- Student fee page: http://localhost/amt/studentfee/addfee/" . $student_session_id . PHP_EOL;
    
    echo PHP_EOL . "🔍 Query to check status:" . PHP_EOL;
    echo "SELECT id, approval_status, payment_id, amount, description FROM fees_discount_approval WHERE id = $discount_id;" . PHP_EOL;
    
} else {
    echo "❌ Error creating test discount request: " . $stmt->error . PHP_EOL;
}

$stmt->close();
$mysqli->close();
?>
