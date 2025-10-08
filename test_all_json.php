<?php
$apis = [
    'collection-report' => [],
    'total-student-academic-report' => [],
    'student-academic-report' => ['class_id' => '1'],
    'report-by-name' => []
];

$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

echo "Testing All 4 APIs for JSON-Only Output\n";
echo "========================================\n\n";

foreach ($apis as $api => $data) {
    $url = "http://localhost/amt/api/$api/filter";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $json_data = json_decode($response, true);
    $is_valid_json = (json_last_error() === JSON_ERROR_NONE);
    $has_html_errors = (strpos($response, '<div style="border:1px solid #990000') !== false);
    
    echo "$api:\n";
    echo "  HTTP: $http_code\n";
    echo "  Valid JSON: " . ($is_valid_json ? "✓ YES" : "✗ NO") . "\n";
    echo "  HTML Errors: " . ($has_html_errors ? "✗ YES" : "✓ NO") . "\n";
    if ($is_valid_json) {
        echo "  Status: " . ($json_data['status'] ?? 'N/A') . "\n";
        echo "  Records: " . ($json_data['total_records'] ?? 'N/A') . "\n";
    }
    echo "  Result: " . (($is_valid_json && !$has_html_errors && $http_code == 200) ? "✓ PASS" : "✗ FAIL") . "\n\n";
}

echo "All APIs tested!\n";

