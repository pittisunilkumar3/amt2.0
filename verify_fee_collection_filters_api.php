<?php
/**
 * Comprehensive Verification for Fee Collection Filters API
 */

function makeApiRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true),
        'error' => $error
    ];
}

$base_url = 'http://localhost/amt/api';
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

$tests = [];

// Test 1: Empty request
$tests[] = [
    'name' => 'Test 1: Get All Filter Options (Empty Request)',
    'url' => $base_url . '/fee-collection-filters/get',
    'data' => [],
    'expected_code' => 200
];

// Test 2: With session_id
$tests[] = [
    'name' => 'Test 2: Filter Classes by Session',
    'url' => $base_url . '/fee-collection-filters/get',
    'data' => ['session_id' => 21],
    'expected_code' => 200
];

// Test 3: With session_id and class_id
$tests[] = [
    'name' => 'Test 3: Filter Sections by Class',
    'url' => $base_url . '/fee-collection-filters/get',
    'data' => ['session_id' => 21, 'class_id' => 19],
    'expected_code' => 200
];

// Test 4: Invalid headers
$tests[] = [
    'name' => 'Test 4: Invalid Authentication Headers',
    'url' => $base_url . '/fee-collection-filters/get',
    'data' => [],
    'expected_code' => 401,
    'headers' => [
        'Content-Type: application/json',
        'Client-Service: invalid',
        'Auth-Key: invalid'
    ]
];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fee Collection Filters API - Verification</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .header h1 { margin: 0; color: #333; font-size: 32px; }
        .header p { margin: 10px 0 0 0; color: #666; }
        .test-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .test-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
        .test-title { font-size: 20px; font-weight: bold; color: #333; }
        .badge { padding: 6px 12px; border-radius: 20px; font-size: 14px; font-weight: bold; }
        .badge-success { background: #10b981; color: white; }
        .badge-error { background: #ef4444; color: white; }
        .badge-warning { background: #f59e0b; color: white; }
        .test-details { background: #f9fafb; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .test-details table { width: 100%; border-collapse: collapse; }
        .test-details td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .test-details td:first-child { font-weight: bold; width: 150px; color: #6b7280; }
        pre { background: #1f2937; color: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 13px; }
        .summary { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 20px; }
        .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px; }
        .summary-item { background: #f9fafb; padding: 15px; border-radius: 8px; text-align: center; }
        .summary-number { font-size: 32px; font-weight: bold; color: #667eea; }
        .summary-label { color: #6b7280; margin-top: 5px; }
        .filter-summary { background: #f0f9ff; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #3b82f6; }
        .filter-summary h4 { margin: 0 0 10px 0; color: #1e40af; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; }
        .filter-item { background: white; padding: 10px; border-radius: 5px; text-align: center; }
        .filter-count { font-size: 24px; font-weight: bold; color: #3b82f6; }
        .filter-label { font-size: 12px; color: #6b7280; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Fee Collection Filters API - Comprehensive Verification</h1>
            <p>Testing all endpoints and scenarios to ensure the API is working correctly</p>
        </div>
        
        <?php
        $total_tests = count($tests);
        $passed_tests = 0;
        $failed_tests = 0;
        $first_success_data = null;
        
        foreach ($tests as $index => $test) {
            $test_headers = isset($test['headers']) ? $test['headers'] : $headers;
            $result = makeApiRequest($test['url'], $test['data'], $test_headers);
            $passed = ($result['http_code'] == $test['expected_code']);
            
            if ($passed) {
                $passed_tests++;
                if ($first_success_data === null && isset($result['response']['data'])) {
                    $first_success_data = $result['response']['data'];
                }
            } else {
                $failed_tests++;
            }
            ?>
            
            <div class="test-card">
                <div class="test-header">
                    <div class="test-title"><?php echo $test['name']; ?></div>
                    <span class="badge <?php echo $passed ? 'badge-success' : 'badge-error'; ?>">
                        <?php echo $passed ? '✅ PASSED' : '❌ FAILED'; ?>
                    </span>
                </div>
                
                <div class="test-details">
                    <table>
                        <tr>
                            <td>Endpoint</td>
                            <td><code><?php echo $test['url']; ?></code></td>
                        </tr>
                        <tr>
                            <td>Request Body</td>
                            <td><code><?php echo json_encode($test['data']); ?></code></td>
                        </tr>
                        <tr>
                            <td>Expected Code</td>
                            <td><span class="badge badge-warning"><?php echo $test['expected_code']; ?></span></td>
                        </tr>
                        <tr>
                            <td>Actual Code</td>
                            <td>
                                <span class="badge <?php echo $passed ? 'badge-success' : 'badge-error'; ?>">
                                    <?php echo $result['http_code']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php if ($result['error']): ?>
                        <tr>
                            <td>cURL Error</td>
                            <td style="color: #ef4444;"><?php echo $result['error']; ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                
                <?php if ($passed && isset($result['response']['data'])): ?>
                <div class="filter-summary">
                    <h4>📊 Filter Options Returned</h4>
                    <div class="filter-grid">
                        <div class="filter-item">
                            <div class="filter-count"><?php echo count($result['response']['data']['sessions'] ?? []); ?></div>
                            <div class="filter-label">Sessions</div>
                        </div>
                        <div class="filter-item">
                            <div class="filter-count"><?php echo count($result['response']['data']['classes'] ?? []); ?></div>
                            <div class="filter-label">Classes</div>
                        </div>
                        <div class="filter-item">
                            <div class="filter-count"><?php echo count($result['response']['data']['sections'] ?? []); ?></div>
                            <div class="filter-label">Sections</div>
                        </div>
                        <div class="filter-item">
                            <div class="filter-count"><?php echo count($result['response']['data']['fee_groups'] ?? []); ?></div>
                            <div class="filter-label">Fee Groups</div>
                        </div>
                        <div class="filter-item">
                            <div class="filter-count"><?php echo count($result['response']['data']['fee_types'] ?? []); ?></div>
                            <div class="filter-label">Fee Types</div>
                        </div>
                        <div class="filter-item">
                            <div class="filter-count"><?php echo count($result['response']['data']['collect_by'] ?? []); ?></div>
                            <div class="filter-label">Staff</div>
                        </div>
                        <div class="filter-item">
                            <div class="filter-count"><?php echo count($result['response']['data']['group_by_options'] ?? []); ?></div>
                            <div class="filter-label">Group Options</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <details style="margin-top: 15px;">
                    <summary style="cursor: pointer; font-weight: bold; color: #667eea;">View Full Response</summary>
                    <pre><?php echo json_encode($result['response'], JSON_PRETTY_PRINT); ?></pre>
                </details>
            </div>
            
        <?php } ?>
        
        <div class="summary">
            <h2 style="margin: 0 0 20px 0; color: #333;">📈 Test Summary</h2>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="summary-number"><?php echo $total_tests; ?></div>
                    <div class="summary-label">Total Tests</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number" style="color: #10b981;"><?php echo $passed_tests; ?></div>
                    <div class="summary-label">Passed</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number" style="color: #ef4444;"><?php echo $failed_tests; ?></div>
                    <div class="summary-label">Failed</div>
                </div>
                <div class="summary-item">
                    <div class="summary-number" style="color: #667eea;">
                        <?php echo $total_tests > 0 ? round(($passed_tests / $total_tests) * 100) : 0; ?>%
                    </div>
                    <div class="summary-label">Success Rate</div>
                </div>
            </div>
            
            <?php if ($passed_tests == $total_tests): ?>
            <div style="background: #d1fae5; border: 2px solid #10b981; padding: 20px; border-radius: 8px; margin-top: 20px; text-align: center;">
                <h3 style="margin: 0; color: #065f46;">🎉 All Tests Passed!</h3>
                <p style="margin: 10px 0 0 0; color: #047857;">The Fee Collection Filters API is working perfectly!</p>
            </div>
            <?php else: ?>
            <div style="background: #fee2e2; border: 2px solid #ef4444; padding: 20px; border-radius: 8px; margin-top: 20px; text-align: center;">
                <h3 style="margin: 0; color: #991b1b;">⚠️ Some Tests Failed</h3>
                <p style="margin: 10px 0 0 0; color: #b91c1c;">Please review the failed tests above and fix the issues.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 20px;">
            <h3 style="margin: 0 0 15px 0; color: #333;">📚 Documentation</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                <li style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <strong>Full Documentation:</strong> <code>api/documentation/FEE_COLLECTION_FILTERS_API_DOCUMENTATION.md</code>
                </li>
                <li style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                    <strong>Quick Reference:</strong> <code>api/documentation/FEE_COLLECTION_FILTERS_API_QUICK_REFERENCE.md</code>
                </li>
                <li style="padding: 8px 0;">
                    <strong>Implementation Summary:</strong> <code>FEE_COLLECTION_FILTERS_API_IMPLEMENTATION_SUMMARY.md</code>
                </li>
            </ul>
        </div>
    </div>
</body>
</html>

