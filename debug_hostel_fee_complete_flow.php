<?php
// Complete debug script to trace hostel fee data flow
require_once 'application/config/database.php';

$host = $db['default']['hostname'];
$username = $db['default']['username'];
$password = $db['default']['password'];
$database = $db['default']['database'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Complete Hostel Fee Data Flow Debug</h2>";
    
    // 1. Check if student has hostel fees assigned
    echo "<h3>1. Student Hostel Fee Assignment:</h3>";
    $stmt = $pdo->prepare("
        SELECT shf.id, shf.student_session_id, shf.month, shf.fees, 
               s.firstname, s.lastname, s.admission_no
        FROM student_hostel_fees shf
        JOIN students s ON shf.student_session_id = s.id
        WHERE s.id = 8
        ORDER BY shf.id DESC
        LIMIT 5
    ");
    $stmt->execute();
    $hostel_fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($hostel_fees)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Hostel Fee ID</th><th>Student</th><th>Student Session ID</th><th>Month</th><th>Amount</th></tr>";
        foreach ($hostel_fees as $fee) {
            echo "<tr>";
            echo "<td>" . $fee['id'] . "</td>";
            echo "<td>" . $fee['firstname'] . " " . $fee['lastname'] . "</td>";
            echo "<td>" . $fee['student_session_id'] . "</td>";
            echo "<td>" . $fee['month'] . "</td>";
            echo "<td>" . $fee['fees'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        $sample_hostel_fee_id = $hostel_fees[0]['id'];
        $sample_student_session_id = $hostel_fees[0]['student_session_id'];
        
        echo "<p><strong>Sample Data for Testing:</strong></p>";
        echo "<ul>";
        echo "<li>Hostel Fee ID: $sample_hostel_fee_id</li>";
        echo "<li>Student Session ID: $sample_student_session_id</li>";
        echo "</ul>";
        
    } else {
        echo "<p style='color: red;'>No hostel fees assigned to student ID 8</p>";
        $sample_hostel_fee_id = "N/A";
        $sample_student_session_id = "N/A";
    }
    
    // 2. Check JavaScript data attributes
    echo "<h3>2. JavaScript Data Flow Analysis:</h3>";
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
    echo "<h4>Button HTML (should have):</h4>";
    echo "<pre>";
    echo htmlspecialchars('data-hostel_fee_id="' . $sample_hostel_fee_id . '"
data-student_session_id="' . $sample_student_session_id . '"
data-fee-category="hostel"');
    echo "</pre>";
    
    echo "<h4>JavaScript Capture (line 4927):</h4>";
    echo "<pre>";
    echo "var hostelFeeId = \$(this).data('hostel_fee_id');        // Should get: $sample_hostel_fee_id
var studentSessionId = \$(this).data('student_session_id');  // Should get: $sample_student_session_id";
    echo "</pre>";
    
    echo "<h4>Modal Field Population (line 4952):</h4>";
    echo "<pre>";
    echo "\$('#hostel_fees_id').val(hostelFeeId);                 // Should set: $sample_hostel_fee_id
\$('#std_id').val(studentSessionId);                       // Should set: $sample_student_session_id
\$('#fee_category').val('hostel');                         // Should set: 'hostel'";
    echo "</pre>";
    echo "</div>";
    
    // 3. Check AJAX data transmission
    echo "<h3>3. AJAX Data Transmission (line 2933):</h3>";
    echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px;'>";
    echo "<h4>Save Button JavaScript Capture:</h4>";
    echo "<pre>";
    echo "var hostel_fees_id = \$('#hostel_fees_id').val();        // Should get: $sample_hostel_fee_id
var student_session_id = \$('#std_id').val();             // Should get: $sample_student_session_id
var fee_category = \$('#fee_category').val();             // Should get: 'hostel'";
    echo "</pre>";
    
    echo "<h4>AJAX POST Data:</h4>";
    echo "<pre>";
    echo "data: { 
    hostel_fees_id: hostel_fees_id,                       // Should send: $sample_hostel_fee_id
    student_session_id: student_session_id,               // Should send: $sample_student_session_id
    fee_category: fee_category,                           // Should send: 'hostel'
    // ... other fields
}";
    echo "</pre>";
    echo "</div>";
    
    // 4. Check controller processing
    echo "<h3>4. Controller Processing (Studentfee.php line 794):</h3>";
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px;'>";
    echo "<h4>Controller Input Processing:</h4>";
    echo "<pre>";
    echo "\$hostel_fees_id = \$this->input->post('hostel_fees_id');     // Should receive: $sample_hostel_fee_id
\$student_session_id = \$this->input->post('student_session_id'); // Should receive: $sample_student_session_id
\$fee_category = \$this->input->post('fee_category');             // Should receive: 'hostel'";
    echo "</pre>";
    
    echo "<h4>Data Array Processing (line 815-822):</h4>";
    echo "<pre>";
    echo "if (\$hostel_fees_id != 0 && \$fee_category == \"hostel\") {
    unset(\$data['student_fees_master_id']);              // Remove generic fields
    unset(\$data['fee_groups_feetype_id']);               // Remove generic fields
    \$data['student_hostel_fee_id'] = \$hostel_fees_id;    // Set: $sample_hostel_fee_id
    \$data['student_session_id'] = \$student_session_id;   // Set: $sample_student_session_id
}";
    echo "</pre>";
    echo "</div>";
    
    // 5. Check recent database records
    echo "<h3>5. Recent Database Records:</h3>";
    $stmt = $pdo->prepare("
        SELECT id, student_session_id, student_fees_master_id, fee_groups_feetype_id, 
               student_transport_fee_id, student_hostel_fee_id, created_at,
               CASE 
                   WHEN student_hostel_fee_id IS NOT NULL AND student_hostel_fee_id > 0 THEN 'HOSTEL'
                   WHEN student_transport_fee_id IS NOT NULL AND student_transport_fee_id > 0 THEN 'TRANSPORT'
                   WHEN student_fees_master_id IS NOT NULL AND student_fees_master_id > 0 THEN 'REGULAR'
                   ELSE 'UNKNOWN'
               END as fee_type
        FROM student_fees_deposite 
        ORDER BY id DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Type</th><th>Session ID</th><th>Master ID</th><th>Fee Type ID</th><th>Transport ID</th><th>Hostel ID</th><th>Created</th><th>Status</th></tr>";
    
    foreach ($records as $record) {
        $row_color = "";
        $status = "";
        
        if ($record['fee_type'] == 'HOSTEL') {
            if ($record['student_session_id'] > 0 && $record['student_hostel_fee_id'] > 0) {
                $row_color = "style='background-color: #d4edda;'"; // Green - correct
                $status = "✓ CORRECT";
            } else {
                $row_color = "style='background-color: #f8d7da;'"; // Red - incorrect
                $status = "✗ INCORRECT";
            }
        } elseif ($record['fee_type'] == 'TRANSPORT') {
            $row_color = "style='background-color: #d1ecf1;'"; // Blue
            $status = "TRANSPORT";
        } else {
            $status = "REGULAR";
        }
        
        echo "<tr $row_color>";
        echo "<td>" . $record['id'] . "</td>";
        echo "<td>" . $record['fee_type'] . "</td>";
        echo "<td>" . ($record['student_session_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['student_fees_master_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['fee_groups_feetype_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['student_transport_fee_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($record['student_hostel_fee_id'] ?: 'NULL') . "</td>";
        echo "<td>" . substr($record['created_at'], 0, 16) . "</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 6. Debugging steps
    echo "<h3>6. Debugging Steps:</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px;'>";
    echo "<h4>To Debug the Issue:</h4>";
    echo "<ol>";
    echo "<li><strong>Open Browser Console:</strong> Press F12 and go to Console tab</li>";
    echo "<li><strong>Click Hostel Fee Button:</strong> Look for console.log output showing captured data</li>";
    echo "<li><strong>Check Modal Fields:</strong> Inspect the modal form fields after clicking</li>";
    echo "<li><strong>Monitor AJAX Request:</strong> Check Network tab for the POST request data</li>";
    echo "<li><strong>Test Payment:</strong> Submit a small test payment and check this page again</li>";
    echo "</ol>";
    
    echo "<h4>Expected Console Output:</h4>";
    echo "<pre>";
    echo "Hostel fee button clicked: {
    studentSessionId: $sample_student_session_id,
    hostelFeeId: $sample_hostel_fee_id,
    group: 'Hostel Fees',
    type: 'Month Name'
}";
    echo "</pre>";
    echo "</div>";
    
    // 7. Quick fix verification
    echo "<h3>7. Quick Fix Verification:</h3>";
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px;'>";
    echo "<h4>Potential Issues to Check:</h4>";
    echo "<ul>";
    echo "<li><strong>Missing student_session_id in button:</strong> Check if hostel fee buttons have data-student_session_id attribute</li>";
    echo "<li><strong>Wrong data attribute name:</strong> Verify data-hostel_fee_id vs data-hostel-fee-id</li>";
    echo "<li><strong>Modal not clearing properly:</strong> Check if previous values are interfering</li>";
    echo "<li><strong>AJAX parameter mismatch:</strong> Verify parameter names match between JS and PHP</li>";
    echo "<li><strong>Controller condition not met:</strong> Check if hostel_fees_id > 0 and fee_category == 'hostel'</li>";
    echo "</ul>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}
?>
