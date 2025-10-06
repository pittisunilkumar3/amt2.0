<?php
// Debug script to check hostel fee print data
require_once('application/config/database.php');

// Create database connection
$mysqli = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "<h2>Debug Hostel Fee Print Data</h2>";

// Get a sample hostel fee ID from the database
$hostel_fee_query = "SELECT id FROM student_hostel_fees LIMIT 1";
$result = $mysqli->query($hostel_fee_query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $hostel_fee_id = $row['id'];
    
    echo "<h3>Testing with Hostel Fee ID: $hostel_fee_id</h3>";
    
    // Execute the same query as getHostelFeeByID
    $sql = "SELECT student_hostel_fees.*,hostel_rooms.cost_per_bed as fees,hostel_feemaster.month,hostel_feemaster.due_date,hostel_feemaster.fine_amount, hostel_feemaster.fine_type,hostel_feemaster.fine_percentage,students.id as student_id,students.firstname,students.middlename,students.admission_no,students.lastname,student_session.class_id,classes.class,sections.section,students.guardian_name,students.guardian_phone,students.father_name,student_session.section_id,student_session.student_id, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_hostel_fees` INNER JOIN hostel_feemaster on hostel_feemaster.id =student_hostel_fees.hostel_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_hostel_fee_id=student_hostel_fees.id INNER JOIN student_session on student_session.id= student_hostel_fees.student_session_id INNER JOIN classes on classes.id= student_session.class_id INNER JOIN sections on sections.id= student_session.section_id INNER JOIN students on students.id=student_session.student_id INNER JOIN hostel_rooms on hostel_rooms.id = student_hostel_fees.hostel_room_id WHERE student_hostel_fees.id=" . $hostel_fee_id;
    
    echo "<h4>Hostel Fee Query:</h4>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    $result = $mysqli->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $hostel_data = $result->fetch_assoc();
        echo "<h4>Hostel Fee Data Retrieved:</h4>";
        echo "<pre>" . print_r($hostel_data, true) . "</pre>";
        
        // Check if amount_detail has data
        if (!empty($hostel_data['amount_detail']) && $hostel_data['amount_detail'] != '0') {
            echo "<h4>Payment Details (amount_detail):</h4>";
            $payment_details = json_decode($hostel_data['amount_detail'], true);
            echo "<pre>" . print_r($payment_details, true) . "</pre>";
        } else {
            echo "<h4 style='color: red;'>No Payment Details Found - amount_detail is empty or 0</h4>";
        }
    } else {
        echo "<h4 style='color: red;'>No data returned from hostel fee query</h4>";
        echo "MySQL Error: " . $mysqli->error;
    }
} else {
    echo "<h4 style='color: red;'>No hostel fees found in database</h4>";
}

// Now let's compare with transport fees
echo "<hr><h2>Compare with Transport Fee Data</h2>";

$transport_fee_query = "SELECT id FROM student_transport_fees LIMIT 1";
$result = $mysqli->query($transport_fee_query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $transport_fee_id = $row['id'];
    
    echo "<h3>Testing with Transport Fee ID: $transport_fee_id</h3>";
    
    // Execute the same query as getTransportFeeByID
    $sql = "SELECT student_transport_fees.*,route_pickup_point.fees,transport_feemaster.month,transport_feemaster.due_date ,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,students.id as student_id,students.firstname,students.middlename,students.admission_no,students.lastname,student_session.class_id,classes.class,sections.section,students.guardian_name,students.guardian_phone,students.father_name,student_session.section_id,student_session.student_id, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id   LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN student_session on student_session.id= student_transport_fees.student_session_id INNER JOIN classes on classes.id= student_session.class_id INNER JOIN sections on sections.id= student_session.section_id INNER JOIN students on students.id=student_session.student_id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id  WHERE student_transport_fees.id=" . $transport_fee_id;
    
    echo "<h4>Transport Fee Query:</h4>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    
    $result = $mysqli->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $transport_data = $result->fetch_assoc();
        echo "<h4>Transport Fee Data Retrieved:</h4>";
        echo "<pre>" . print_r($transport_data, true) . "</pre>";
        
        // Check if amount_detail has data
        if (!empty($transport_data['amount_detail']) && $transport_data['amount_detail'] != '0') {
            echo "<h4>Payment Details (amount_detail):</h4>";
            $payment_details = json_decode($transport_data['amount_detail'], true);
            echo "<pre>" . print_r($payment_details, true) . "</pre>";
        } else {
            echo "<h4 style='color: red;'>No Payment Details Found - amount_detail is empty or 0</h4>";
        }
    } else {
        echo "<h4 style='color: red;'>No data returned from transport fee query</h4>";
        echo "MySQL Error: " . $mysqli->error;
    }
} else {
    echo "<h4 style='color: red;'>No transport fees found in database</h4>";
}

$mysqli->close();
?>
