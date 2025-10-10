<?php
/**
 * Direct database query test to check what data exists
 */

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

// Connect to database
$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "=== Direct Database Query Test ===\n\n";

// Test 1: Check if student_fees_deposite table has data
echo "=== Test 1: Check student_fees_deposite table ===\n";
$sql = "SELECT COUNT(*) as total FROM student_fees_deposite";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Total records in student_fees_deposite: " . $row['total'] . "\n\n";
} else {
    echo "Error: " . $conn->error . "\n\n";
}

// Test 2: Check records with amount_detail
echo "=== Test 2: Check records with amount_detail ===\n";
$sql = "SELECT COUNT(*) as total FROM student_fees_deposite WHERE amount_detail IS NOT NULL AND amount_detail != ''";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo "Records with amount_detail: " . $row['total'] . "\n\n";
} else {
    echo "Error: " . $conn->error . "\n\n";
}

// Test 3: Get sample records
echo "=== Test 3: Sample records from student_fees_deposite ===\n";
$sql = "SELECT id, student_fees_master_id, fee_groups_feetype_id, amount_detail, created_at 
        FROM student_fees_deposite 
        WHERE amount_detail IS NOT NULL AND amount_detail != ''
        LIMIT 5";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . "\n";
        echo "  created_at: " . $row['created_at'] . "\n";
        echo "  amount_detail: " . substr($row['amount_detail'], 0, 200) . "...\n";
        
        // Parse JSON
        $details = json_decode($row['amount_detail'], true);
        if ($details && is_array($details)) {
            echo "  Payment count: " . count($details) . "\n";
            if (isset($details[0])) {
                echo "  First payment date: " . ($details[0]['date'] ?? 'N/A') . "\n";
                echo "  First payment amount: " . ($details[0]['amount'] ?? 0) . "\n";
            }
        }
        echo "\n";
    }
} else {
    echo "Error: " . $conn->error . "\n\n";
}

// Test 4: Check for October 2025 payments
echo "=== Test 4: Check for October 2025 payments ===\n";
$sql = "SELECT id, amount_detail 
        FROM student_fees_deposite 
        WHERE amount_detail LIKE '%2025-10-%'
        LIMIT 10";
$result = $conn->query($sql);
if ($result) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $count++;
        echo "ID: " . $row['id'] . "\n";
        $details = json_decode($row['amount_detail'], true);
        if ($details && is_array($details)) {
            foreach ($details as $detail) {
                if (isset($detail['date']) && strpos($detail['date'], '2025-10-') === 0) {
                    echo "  Date: " . $detail['date'] . ", Amount: " . ($detail['amount'] ?? 0) . ", Fine: " . ($detail['amount_fine'] ?? 0) . "\n";
                }
            }
        }
    }
    echo "\nTotal records with October 2025 payments: $count\n\n";
} else {
    echo "Error: " . $conn->error . "\n\n";
}

// Test 5: Check current session
echo "=== Test 5: Check current session ===\n";
$sql = "SELECT id, session FROM sch_session WHERE is_active = 1 LIMIT 1";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    if ($row) {
        echo "Current session ID: " . $row['id'] . "\n";
        echo "Current session: " . $row['session'] . "\n\n";
        $current_session_id = $row['id'];
        
        // Test 6: Check if fee_groups_feetype has session_id filter
        echo "=== Test 6: Check fee_groups_feetype with current session ===\n";
        $sql = "SELECT COUNT(*) as total FROM fee_groups_feetype WHERE session_id = " . $current_session_id;
        $result2 = $conn->query($sql);
        if ($result2) {
            $row2 = $result2->fetch_assoc();
            echo "fee_groups_feetype records for current session: " . $row2['total'] . "\n\n";
        }
    } else {
        echo "No active session found!\n\n";
    }
} else {
    echo "Error: " . $conn->error . "\n\n";
}

// Test 7: Run the actual query from the model
echo "=== Test 7: Run actual model query (simplified) ===\n";
$sql = "SELECT 
    student_fees_deposite.id as student_fees_deposite_id,
    student_fees_deposite.amount_detail
FROM student_fees_deposite
INNER JOIN fee_groups_feetype ON fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id
WHERE student_fees_deposite.amount_detail IS NOT NULL 
  AND student_fees_deposite.amount_detail != ''
LIMIT 10";

$result = $conn->query($sql);
if ($result) {
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $count++;
        echo "Deposit ID: " . $row['student_fees_deposite_id'] . "\n";
        $details = json_decode($row['amount_detail'], true);
        if ($details && is_array($details)) {
            echo "  Payment records: " . count($details) . "\n";
            if (isset($details[0])) {
                echo "  First payment: " . ($details[0]['date'] ?? 'N/A') . " - " . ($details[0]['amount'] ?? 0) . "\n";
            }
        }
    }
    echo "\nTotal records returned: $count\n\n";
} else {
    echo "Error: " . $conn->error . "\n\n";
}

$conn->close();

echo "=== End of Database Query Test ===\n";

