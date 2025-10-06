<?php
// Debug script to check what data is being received by the controller
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>POST Data Received:</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>Specific Values:</h2>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Value</th><th>Status</th></tr>";
    
    $fields = [
        'hostel_fees_id',
        'student_session_id', 
        'fee_category',
        'amount',
        'date',
        'payment_mode',
        'accountname'
    ];
    
    foreach ($fields as $field) {
        $value = isset($_POST[$field]) ? $_POST[$field] : 'NOT SET';
        $status = ($value !== 'NOT SET' && $value !== '' && $value !== '0') ? '✓ OK' : '✗ MISSING/EMPTY';
        $color = ($status === '✓ OK') ? 'green' : 'red';
        
        echo "<tr>";
        echo "<td>$field</td>";
        echo "<td>$value</td>";
        echo "<td style='color: $color;'>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if this looks like a hostel fee request
    $is_hostel = (isset($_POST['fee_category']) && $_POST['fee_category'] === 'hostel');
    $has_hostel_id = (isset($_POST['hostel_fees_id']) && $_POST['hostel_fees_id'] > 0);
    
    echo "<h2>Hostel Fee Analysis:</h2>";
    echo "<p>Is hostel fee request: " . ($is_hostel ? 'YES' : 'NO') . "</p>";
    echo "<p>Has hostel fee ID: " . ($has_hostel_id ? 'YES' : 'NO') . "</p>";
    
    if ($is_hostel && $has_hostel_id) {
        echo "<p style='color: green;'>✓ This appears to be a valid hostel fee request</p>";
    } else {
        echo "<p style='color: red;'>✗ This does NOT appear to be a valid hostel fee request</p>";
    }
    
} else {
    echo "<h2>Debug Controller Input</h2>";
    echo "<p>This script will show POST data when the form is submitted.</p>";
    echo "<p>To test:</p>";
    echo "<ol>";
    echo "<li>Go to student fee collection page</li>";
    echo "<li>Click on a hostel fee collection button</li>";
    echo "<li>Fill in payment details</li>";
    echo "<li>Before clicking 'Collect Fees', open browser console (F12)</li>";
    echo "<li>In console, run: <code>$('form').attr('action', 'http://localhost/amt/debug_controller_input.php');</code></li>";
    echo "<li>Then click 'Collect Fees' button</li>";
    echo "</ol>";
}
?>
