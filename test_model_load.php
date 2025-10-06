<?php
// Test model loading
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try to include the model file directly
echo "Testing Feediscount_model loading...\n";

try {
    include_once 'application/models/Feediscount_model.php';
    echo "Model loaded successfully!\n";
} catch (Exception $e) {
    echo "Error loading model: " . $e->getMessage() . "\n";
} catch (ParseError $e) {
    echo "Parse error in model: " . $e->getMessage() . "\n";
}

// Check for duplicate function declarations
$functions = get_defined_functions();
echo "User-defined functions: " . count($functions['user']) . "\n";

// Check if class exists
if (class_exists('Feediscount_model')) {
    echo "Feediscount_model class exists\n";
    $reflection = new ReflectionClass('Feediscount_model');
    $methods = $reflection->getMethods();
    echo "Methods in class: " . count($methods) . "\n";
    
    // Check for duplicate method names
    $methodNames = array();
    foreach ($methods as $method) {
        $methodName = strtolower($method->getName());
        if (isset($methodNames[$methodName])) {
            echo "DUPLICATE METHOD FOUND: " . $method->getName() . "\n";
        } else {
            $methodNames[$methodName] = true;
        }
    }
} else {
    echo "Feediscount_model class does not exist\n";
}
?>
