<?php
// Debug file to test fee collection issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Fee Collection Debug Check</h2>";

// Check if required models exist
$models_to_check = [
    'AdvancePayment_model',
    'studentfeemaster_model',
    'studentsession_model'
];

echo "<h3>Model Files Check:</h3>";
foreach ($models_to_check as $model) {
    $file_path = "application/models/" . $model . ".php";
    if (file_exists($file_path)) {
        echo "✅ $model - EXISTS<br>";
    } else {
        echo "❌ $model - MISSING<br>";
    }
}

// Check database tables
echo "<h3>Database Tables Check:</h3>";
try {
    $db_config = include('application/config/database.php');
    $db_settings = $db_config['default'];
    
    $mysqli = new mysqli(
        $db_settings['hostname'], 
        $db_settings['username'], 
        $db_settings['password'], 
        $db_settings['database']
    );

    if ($mysqli->connect_error) {
        echo "❌ Database connection failed: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ Database connection successful<br>";
        
        $tables_to_check = [
            'student_advance_payments',
            'advance_payment_usage', 
            'advance_payment_transfers',
            'student_fees_deposite'
        ];
        
        foreach ($tables_to_check as $table) {
            $result = $mysqli->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                echo "✅ Table $table - EXISTS<br>";
            } else {
                echo "❌ Table $table - MISSING<br>";
            }
        }
    }
    
    $mysqli->close();
} catch (Exception $e) {
    echo "❌ Database check error: " . $e->getMessage() . "<br>";
}

// Check PHP error log
echo "<h3>Recent PHP Errors:</h3>";
$error_log_file = ini_get('error_log');
if (file_exists($error_log_file)) {
    $errors = file_get_contents($error_log_file);
    $recent_errors = array_slice(explode("\n", $errors), -10);
    foreach ($recent_errors as $error) {
        if (!empty(trim($error))) {
            echo "<div style='color: red; font-family: monospace;'>$error</div>";
        }
    }
} else {
    echo "No error log file found or accessible.<br>";
}

echo "<h3>Session and POST Debug:</h3>";
echo "Session ID: " . session_id() . "<br>";
echo "POST data available: " . (empty($_POST) ? "NO" : "YES") . "<br>";

if (!empty($_POST)) {
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
}
?>
