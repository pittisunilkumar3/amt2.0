<?php
// Test what data is actually being sent via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>AJAX POST Data Received:</h2>";
    
    echo "<h3>Raw POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>Critical Fields Analysis:</h3>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Value</th><th>Type</th><th>Status</th></tr>";
    
    $critical_fields = [
        'hostel_fees_id',
        'student_session_id',
        'fee_category',
        'amount',
        'payment_mode',
        'accountname'
    ];
    
    foreach ($critical_fields as $field) {
        $value = isset($_POST[$field]) ? $_POST[$field] : 'NOT SET';
        $type = gettype($value);
        $status = '';
        
        if ($field === 'hostel_fees_id') {
            $status = ($value !== 'NOT SET' && $value !== '' && $value !== '0' && $value > 0) ? '✓ VALID' : '✗ INVALID';
        } elseif ($field === 'fee_category') {
            $status = ($value === 'hostel') ? '✓ VALID' : '✗ INVALID';
        } else {
            $status = ($value !== 'NOT SET' && $value !== '') ? '✓ VALID' : '✗ INVALID';
        }
        
        $color = ($status === '✓ VALID') ? 'green' : 'red';
        
        echo "<tr>";
        echo "<td>$field</td>";
        echo "<td>$value</td>";
        echo "<td>$type</td>";
        echo "<td style='color: $color;'>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if this would pass the controller condition
    $hostel_fees_id = $_POST['hostel_fees_id'] ?? '';
    $fee_category = $_POST['fee_category'] ?? '';
    
    echo "<h3>Controller Condition Check:</h3>";
    $condition = ($hostel_fees_id != 0 && $fee_category == "hostel");
    echo "<p>Condition: (hostel_fees_id != 0 && fee_category == 'hostel')</p>";
    echo "<p>Result: " . ($condition ? '<span style="color: green;">TRUE - Would process as hostel fee</span>' : '<span style="color: red;">FALSE - Would NOT process as hostel fee</span>') . "</p>";
    
    if (!$condition) {
        echo "<h4>Issues Found:</h4>";
        echo "<ul>";
        if ($hostel_fees_id == 0 || empty($hostel_fees_id)) {
            echo "<li style='color: red;'>hostel_fees_id is 0 or empty</li>";
        }
        if ($fee_category !== 'hostel') {
            echo "<li style='color: red;'>fee_category is not 'hostel' (got: '$fee_category')</li>";
        }
        echo "</ul>";
    }
    
    exit;
}
?>

<h2>AJAX Data Test</h2>
<p>This page will capture and analyze the AJAX data sent when you click "Collect Fees"</p>

<h3>Instructions:</h3>
<ol>
    <li>Go to the student fee page: <a href="http://localhost/amt/studentfee/addfee/8" target="_blank">http://localhost/amt/studentfee/addfee/8</a></li>
    <li>Click on a hostel fee "Collect Fees" button (+ icon)</li>
    <li>Fill in the payment details</li>
    <li>Open browser console (F12)</li>
    <li>In console, run: <code>$('form').attr('action', 'http://localhost/amt/test_ajax_data.php');</code></li>
    <li>Click "Collect Fees" button</li>
    <li>Check the data that appears on this page</li>
</ol>

<h3>Alternative Method:</h3>
<p>If the above doesn't work, you can also intercept the AJAX call:</p>
<ol>
    <li>Open browser console (F12)</li>
    <li>Go to Network tab</li>
    <li>Click hostel fee "Collect Fees" button and submit</li>
    <li>Look for the POST request to "addstudentfee"</li>
    <li>Check the Form Data section to see what's being sent</li>
</ol>
