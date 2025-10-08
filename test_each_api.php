<?php
function testAPI($name, $url, $data) {
    $headers = [
        'Content-Type: application/json',
        'Client-Service: smartschool',
        'Auth-Key: schoolAdmin@'
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $json_data = json_decode($response, true);
    $is_valid_json = (json_last_error() === JSON_ERROR_NONE);
    $has_html_errors = (strpos($response, '<div style="border:1px solid #990000') !== false);
    
    echo "$name:\n";
    echo "  HTTP: $http_code | JSON: " . ($is_valid_json ? "✓" : "✗") . " | HTML Errors: " . ($has_html_errors ? "✗" : "✓");
    if ($is_valid_json && isset($json_data['total_records'])) {
        echo " | Records: " . $json_data['total_records'];
    }
    echo " | " . (($is_valid_json && !$has_html_errors && $http_code == 200) ? "✓ PASS" : "✗ FAIL") . "\n";
}

echo "Quick API Test\n";
echo "==============\n";

testAPI("API #2: Total Student Academic", "http://localhost/amt/api/total-student-academic-report/filter", []);
testAPI("API #3: Student Academic", "http://localhost/amt/api/student-academic-report/filter", ['class_id' => '1']);
testAPI("API #4: Report By Name", "http://localhost/amt/api/report-by-name/filter", []);
testAPI("API #1: Collection Report", "http://localhost/amt/api/collection-report/list", []);

echo "\nAll tests complete!\n";

