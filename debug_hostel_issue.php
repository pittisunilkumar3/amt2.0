<?php
// Direct debug of hostel fee collection issue
require_once 'application/config/database.php';

$host = $db['default']['hostname'];
$username = $db['default']['username'];
$password = $db['default']['password'];
$database = $db['default']['database'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Hostel Fee Collection Debug</h2>";
    
    // Simulate the exact data flow
    if (isset($_POST['debug_hostel'])) {
        echo "<h3>Debugging Hostel Fee Collection Process:</h3>";
        
        // Step 1: Check what data is being sent
        echo "<h4>Step 1: POST Data Analysis</h4>";
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        
        // Step 2: Simulate controller processing
        $hostel_fees_id = $_POST['hostel_fees_id'] ?? '';
        $student_session_id = $_POST['student_session_id'] ?? '';
        $fee_category = $_POST['fee_category'] ?? '';
        
        echo "<h4>Step 2: Controller Variables</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Variable</th><th>Value</th><th>Type</th><th>Status</th></tr>";
        echo "<tr><td>hostel_fees_id</td><td>$hostel_fees_id</td><td>" . gettype($hostel_fees_id) . "</td><td>" . ($hostel_fees_id > 0 ? '✓' : '✗') . "</td></tr>";
        echo "<tr><td>student_session_id</td><td>$student_session_id</td><td>" . gettype($student_session_id) . "</td><td>" . ($student_session_id > 0 ? '✓' : '✗') . "</td></tr>";
        echo "<tr><td>fee_category</td><td>$fee_category</td><td>" . gettype($fee_category) . "</td><td>" . ($fee_category === 'hostel' ? '✓' : '✗') . "</td></tr>";
        echo "</table>";
        
        // Step 3: Check condition logic
        echo "<h4>Step 3: Condition Check</h4>";
        $condition1 = ($hostel_fees_id != 0);
        $condition2 = ($fee_category == "hostel");
        $overall_condition = ($condition1 && $condition2);
        
        echo "<p>Condition 1 (hostel_fees_id != 0): " . ($condition1 ? 'TRUE' : 'FALSE') . "</p>";
        echo "<p>Condition 2 (fee_category == 'hostel'): " . ($condition2 ? 'TRUE' : 'FALSE') . "</p>";
        echo "<p><strong>Overall Condition: " . ($overall_condition ? 'TRUE - Should process as hostel fee' : 'FALSE - Will NOT process as hostel fee') . "</strong></p>";
        
        // Step 4: Simulate data array creation
        if ($overall_condition) {
            echo "<h4>Step 4: Data Array Creation (Hostel Fee Path)</h4>";
            $data = [
                'fee_category' => $fee_category,
                'student_fees_master_id' => 0,
                'fee_groups_feetype_id' => 0,
                'student_session_id' => $student_session_id,
                'amount_detail' => '{"1":{"amount":"100","date":"2025-01-02","description":"Test"}}',
            ];
            
            // Remove generic fields for hostel
            unset($data['student_fees_master_id']);
            unset($data['fee_groups_feetype_id']);
            $data['student_hostel_fee_id'] = $hostel_fees_id;
            $data['student_session_id'] = $student_session_id;
            
            echo "<p>Data array after hostel fee processing:</p>";
            echo "<pre>";
            print_r($data);
            echo "</pre>";
            
            // Step 5: Test database insertion
            echo "<h4>Step 5: Database Insertion Test</h4>";
            unset($data['fee_category']); // This is what the model does
            
            try {
                $stmt = $pdo->prepare("INSERT INTO student_fees_deposite (student_session_id, student_hostel_fee_id, amount_detail) VALUES (?, ?, ?)");
                $result = $stmt->execute([$data['student_session_id'], $data['student_hostel_fee_id'], $data['amount_detail']]);
                
                if ($result) {
                    $insert_id = $pdo->lastInsertId();
                    echo "<p style='color: green;'>✓ Database insertion successful! Insert ID: $insert_id</p>";
                    
                    // Verify the inserted data
                    $verify_stmt = $pdo->prepare("SELECT * FROM student_fees_deposite WHERE id = ?");
                    $verify_stmt->execute([$insert_id]);
                    $inserted_record = $verify_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    echo "<p>Inserted record:</p>";
                    echo "<pre>";
                    print_r($inserted_record);
                    echo "</pre>";
                } else {
                    echo "<p style='color: red;'>✗ Database insertion failed</p>";
                }
            } catch (Exception $e) {
                echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<h4>Step 4: Issue Found!</h4>";
            echo "<p style='color: red; font-weight: bold;'>The hostel fee condition is not being met. This is why the hostel fee ID is not being stored.</p>";
            
            if (!$condition1) {
                echo "<p style='color: red;'>Problem: hostel_fees_id is 0 or empty</p>";
            }
            if (!$condition2) {
                echo "<p style='color: red;'>Problem: fee_category is not 'hostel'</p>";
            }
        }
    }
    
    // Test form
    echo "<h3>Test Hostel Fee Collection:</h3>";
    echo "<form method='post'>";
    echo "<input type='hidden' name='debug_hostel' value='1'>";
    echo "<table>";
    echo "<tr><td>Hostel Fee ID:</td><td><input type='number' name='hostel_fees_id' value='5' required></td></tr>";
    echo "<tr><td>Student Session ID:</td><td><input type='number' name='student_session_id' value='8' required></td></tr>";
    echo "<tr><td>Fee Category:</td><td><input type='text' name='fee_category' value='hostel' required></td></tr>";
    echo "<tr><td>Amount:</td><td><input type='number' name='amount' value='100' required></td></tr>";
    echo "<tr><td>Date:</td><td><input type='date' name='date' value='" . date('Y-m-d') . "' required></td></tr>";
    echo "<tr><td>Payment Mode:</td><td><input type='text' name='payment_mode' value='Cash' required></td></tr>";
    echo "<tr><td>Account Name:</td><td><input type='number' name='accountname' value='1' required></td></tr>";
    echo "</table>";
    echo "<br><button type='submit' style='background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px;'>Debug Hostel Fee Collection</button>";
    echo "</form>";
    
    // Check recent database records
    echo "<h3>Recent Database Records:</h3>";
    $stmt = $pdo->prepare("SELECT * FROM student_fees_deposite ORDER BY id DESC LIMIT 5");
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($records)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Session ID</th><th>Master ID</th><th>Fee Type ID</th><th>Transport ID</th><th>Hostel ID</th><th>Created</th></tr>";
        foreach ($records as $record) {
            $hostel_color = ($record['student_hostel_fee_id'] > 0) ? 'background: #d4edda;' : '';
            echo "<tr style='$hostel_color'>";
            echo "<td>" . $record['id'] . "</td>";
            echo "<td>" . ($record['student_session_id'] ?: 'NULL') . "</td>";
            echo "<td>" . ($record['student_fees_master_id'] ?: 'NULL') . "</td>";
            echo "<td>" . ($record['fee_groups_feetype_id'] ?: 'NULL') . "</td>";
            echo "<td>" . ($record['student_transport_fee_id'] ?: 'NULL') . "</td>";
            echo "<td>" . ($record['student_hostel_fee_id'] ?: 'NULL') . "</td>";
            echo "<td>" . $record['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>
