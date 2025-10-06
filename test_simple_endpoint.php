<?php
/**
 * Test Simple Endpoint
 * Creates a minimal test endpoint to verify CodeIgniter is working
 */

// Create a simple test controller
$test_controller_content = '<?php
defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');

class Test_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        header(\'Content-Type: application/json\');
    }

    public function index() {
        $response = array(
            "status" => "success",
            "message" => "Test API is working",
            "timestamp" => date("Y-m-d H:i:s"),
            "method" => $this->input->server("REQUEST_METHOD"),
            "codeigniter_version" => CI_VERSION,
            "php_version" => phpversion()
        );
        
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    public function post_test() {
        $input = json_decode($this->input->raw_input_stream, true);
        
        $response = array(
            "status" => "success",
            "message" => "POST test working",
            "received_data" => $input,
            "timestamp" => date("Y-m-d H:i:s")
        );
        
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
}';

$controller_path = __DIR__ . '/api/application/controllers/Test_api.php';
file_put_contents($controller_path, $test_controller_content);

echo "✅ Test controller created<br>";
echo "🧪 Testing endpoints:<br>";

// Test the simple endpoint
$test_urls = [
    'http://localhost/amt/api/test_api',
    'http://localhost/amt/api/index.php/test_api',
    'http://localhost/amt/api/test_api/post_test'
];

foreach ($test_urls as $url) {
    echo "<strong>Testing: $url</strong><br>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    
    if (strpos($url, 'post_test') !== false) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['test' => 'data']));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code == 200) {
        echo "✅ Status: $http_code - Working!<br>";
        $data = json_decode($response, true);
        if ($data) {
            echo "📋 Response: " . $data['message'] . "<br>";
        }
    } else {
        echo "❌ Status: $http_code - Error<br>";
    }
    echo "<br>";
}

echo "<hr>";
echo "If the test endpoints work, the issue is specifically with the Teacher_webservice controller.<br>";
echo "If they don't work, there's a fundamental CodeIgniter configuration issue.<br>";
?>