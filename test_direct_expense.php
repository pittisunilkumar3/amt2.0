<?php
/**
 * Direct test - bypass cURL
 */

// Set headers
$_SERVER['HTTP_CLIENT_SERVICE'] = 'smartschool';
$_SERVER['HTTP_AUTH_KEY'] = 'schoolAdmin@';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['CONTENT_TYPE'] = 'application/json';

// Set POST data
$_POST = [];
file_put_contents('php://input', json_encode([]));

// Include CodeIgniter
define('BASEPATH', true);
$_SERVER['CI_ENV'] = 'development';

// Change to API directory
chdir('C:/xampp/htdocs/amt/api');

// Include index.php
require_once 'C:/xampp/htdocs/amt/api/index.php';

