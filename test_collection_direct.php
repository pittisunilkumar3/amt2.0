<?php
// Direct test without curl
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['HTTP_CLIENT_SERVICE'] = 'smartschool';
$_SERVER['HTTP_AUTH_KEY'] = 'schoolAdmin@';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Set the input stream
$input = json_encode([]);
file_put_contents('php://input', $input);

// Include CodeIgniter
require_once 'api/index.php';

