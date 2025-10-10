<?php
/**
 * Test Hierarchical Filtering Logic
 */

// API URL
$base_url = 'http://localhost/amt/api/fee-collection-filters/get';

// Headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

function makeRequest($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

// Test 1: Get all options (no filters)
$test1 = makeRequest($base_url, [], $headers);

// Test 2: Filter by session_id
$test2 = makeRequest($base_url, ['session_id' => 21], $headers);

// Test 3: Filter by session_id and class_id
$test3 = makeRequest($base_url, ['session_id' => 21, 'class_id' => 19], $headers);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Hierarchical Filtering Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { margin: 0; color: #333; }
        .test-section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .test-title { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #f0f0f0; }
        .comparison { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin: 20px 0; }
        .comparison-item { background: #f8f9fa; padding: 15px; border-radius: 5px; }
        .comparison-item h3 { margin: 0 0 10px 0; color: #666; font-size: 16px; }
        .count { font-size: 32px; font-weight: bold; color: #007bff; }
        .label { color: #6c757d; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        .badge-info { background: #17a2b8; color: white; }
        pre { background: #1f2937; color: #f3f4f6; padding: 15px; border-radius: 5px; overflow-x: auto; font-size: 13px; }
        .alert { padding: 15px; border-radius: 5px; margin: 15px 0; }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .alert-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-warning { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Hierarchical Filtering Test</h1>
            <p>Testing if classes and sections are properly filtered based on session_id and class_id</p>
        </div>
        
        <!-- Test 1: No Filters -->
        <div class="test-section">
            <div class="test-title">Test 1: No Filters (Empty Request)</div>
            <p><strong>Request:</strong> <code>{}</code></p>
            <p><strong>Expected:</strong> All sessions, all classes, all sections</p>
            
            <?php if ($test1['http_code'] == 200 && isset($test1['response']['data'])): ?>
                <?php $data1 = $test1['response']['data']; ?>
                <div class="comparison">
                    <div class="comparison-item">
                        <h3>Sessions</h3>
                        <div class="count"><?php echo count($data1['sessions']); ?></div>
                        <div class="label">Total Sessions</div>
                    </div>
                    <div class="comparison-item">
                        <h3>Classes</h3>
                        <div class="count"><?php echo count($data1['classes']); ?></div>
                        <div class="label">Total Classes</div>
                    </div>
                    <div class="comparison-item">
                        <h3>Sections</h3>
                        <div class="count"><?php echo count($data1['sections']); ?></div>
                        <div class="label">Total Sections</div>
                    </div>
                </div>
                
                <details>
                    <summary style="cursor: pointer; font-weight: bold; color: #007bff;">View Classes</summary>
                    <table>
                        <tr><th>ID</th><th>Name</th></tr>
                        <?php foreach (array_slice($data1['classes'], 0, 10) as $class): ?>
                        <tr>
                            <td><?php echo $class['id']; ?></td>
                            <td><?php echo $class['name']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php if (count($data1['classes']) > 10): ?>
                    <p><em>Showing first 10 of <?php echo count($data1['classes']); ?> classes</em></p>
                    <?php endif; ?>
                </details>
                
                <details>
                    <summary style="cursor: pointer; font-weight: bold; color: #007bff;">View Sections</summary>
                    <table>
                        <tr><th>ID</th><th>Name</th></tr>
                        <?php foreach (array_slice($data1['sections'], 0, 10) as $section): ?>
                        <tr>
                            <td><?php echo $section['id']; ?></td>
                            <td><?php echo $section['name']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    <?php if (count($data1['sections']) > 10): ?>
                    <p><em>Showing first 10 of <?php echo count($data1['sections']); ?> sections</em></p>
                    <?php endif; ?>
                </details>
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>Error:</strong> Failed to get data. HTTP Code: <?php echo $test1['http_code']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Test 2: Filter by Session -->
        <div class="test-section">
            <div class="test-title">Test 2: Filter by Session ID</div>
            <p><strong>Request:</strong> <code>{"session_id": 21}</code></p>
            <p><strong>Expected:</strong> All sessions, ONLY classes for session 21, all sections</p>
            
            <?php if ($test2['http_code'] == 200 && isset($test2['response']['data'])): ?>
                <?php $data2 = $test2['response']['data']; ?>
                <div class="comparison">
                    <div class="comparison-item">
                        <h3>Sessions</h3>
                        <div class="count"><?php echo count($data2['sessions']); ?></div>
                        <div class="label">Total Sessions</div>
                    </div>
                    <div class="comparison-item">
                        <h3>Classes</h3>
                        <div class="count"><?php echo count($data2['classes']); ?></div>
                        <div class="label">Filtered Classes</div>
                    </div>
                    <div class="comparison-item">
                        <h3>Sections</h3>
                        <div class="count"><?php echo count($data2['sections']); ?></div>
                        <div class="label">Total Sections</div>
                    </div>
                </div>
                
                <?php
                $classes_filtered = count($data2['classes']) < count($data1['classes']);
                $sections_same = count($data2['sections']) == count($data1['sections']);
                ?>
                
                <?php if ($classes_filtered && $sections_same): ?>
                    <div class="alert alert-success">
                        <strong>✅ PASS:</strong> Classes are filtered (<?php echo count($data2['classes']); ?> vs <?php echo count($data1['classes']); ?>), sections remain the same (<?php echo count($data2['sections']); ?>)
                    </div>
                <?php elseif (!$classes_filtered): ?>
                    <div class="alert alert-error">
                        <strong>❌ FAIL:</strong> Classes are NOT filtered! Still showing all <?php echo count($data2['classes']); ?> classes.
                    </div>
                <?php elseif (!$sections_same): ?>
                    <div class="alert alert-warning">
                        <strong>⚠️ WARNING:</strong> Sections count changed (<?php echo count($data2['sections']); ?> vs <?php echo count($data1['sections']); ?>). Should remain the same.
                    </div>
                <?php endif; ?>
                
                <details>
                    <summary style="cursor: pointer; font-weight: bold; color: #007bff;">View Filtered Classes</summary>
                    <table>
                        <tr><th>ID</th><th>Name</th></tr>
                        <?php foreach ($data2['classes'] as $class): ?>
                        <tr>
                            <td><?php echo $class['id']; ?></td>
                            <td><?php echo $class['name']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </details>
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>Error:</strong> Failed to get data. HTTP Code: <?php echo $test2['http_code']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Test 3: Filter by Session and Class -->
        <div class="test-section">
            <div class="test-title">Test 3: Filter by Session ID and Class ID</div>
            <p><strong>Request:</strong> <code>{"session_id": 21, "class_id": 19}</code></p>
            <p><strong>Expected:</strong> All sessions, ONLY classes for session 21, ONLY sections for class 19</p>
            
            <?php if ($test3['http_code'] == 200 && isset($test3['response']['data'])): ?>
                <?php $data3 = $test3['response']['data']; ?>
                <div class="comparison">
                    <div class="comparison-item">
                        <h3>Sessions</h3>
                        <div class="count"><?php echo count($data3['sessions']); ?></div>
                        <div class="label">Total Sessions</div>
                    </div>
                    <div class="comparison-item">
                        <h3>Classes</h3>
                        <div class="count"><?php echo count($data3['classes']); ?></div>
                        <div class="label">Filtered Classes</div>
                    </div>
                    <div class="comparison-item">
                        <h3>Sections</h3>
                        <div class="count"><?php echo count($data3['sections']); ?></div>
                        <div class="label">Filtered Sections</div>
                    </div>
                </div>
                
                <?php
                $classes_filtered = count($data3['classes']) < count($data1['classes']);
                $sections_filtered = count($data3['sections']) < count($data1['sections']);
                ?>
                
                <?php if ($classes_filtered && $sections_filtered): ?>
                    <div class="alert alert-success">
                        <strong>✅ PASS:</strong> Both classes (<?php echo count($data3['classes']); ?> vs <?php echo count($data1['classes']); ?>) and sections (<?php echo count($data3['sections']); ?> vs <?php echo count($data1['sections']); ?>) are filtered correctly!
                    </div>
                <?php else: ?>
                    <div class="alert alert-error">
                        <strong>❌ FAIL:</strong> 
                        <?php if (!$classes_filtered): ?>
                            Classes are NOT filtered! Still showing all <?php echo count($data3['classes']); ?> classes.
                        <?php endif; ?>
                        <?php if (!$sections_filtered): ?>
                            Sections are NOT filtered! Still showing all <?php echo count($data3['sections']); ?> sections.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <details>
                    <summary style="cursor: pointer; font-weight: bold; color: #007bff;">View Filtered Sections</summary>
                    <table>
                        <tr><th>ID</th><th>Name</th></tr>
                        <?php foreach ($data3['sections'] as $section): ?>
                        <tr>
                            <td><?php echo $section['id']; ?></td>
                            <td><?php echo $section['name']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </details>
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>Error:</strong> Failed to get data. HTTP Code: <?php echo $test3['http_code']; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Summary -->
        <div class="test-section">
            <div class="test-title">📊 Test Summary</div>
            <?php
            $all_tests_passed = true;
            if (isset($data1, $data2, $data3)) {
                $test2_pass = count($data2['classes']) < count($data1['classes']) && count($data2['sections']) == count($data1['sections']);
                $test3_pass = count($data3['classes']) < count($data1['classes']) && count($data3['sections']) < count($data1['sections']);
                $all_tests_passed = $test2_pass && $test3_pass;
            } else {
                $all_tests_passed = false;
            }
            ?>
            
            <?php if ($all_tests_passed): ?>
                <div class="alert alert-success">
                    <h3 style="margin: 0 0 10px 0;">🎉 All Tests Passed!</h3>
                    <p style="margin: 0;">The hierarchical filtering is working correctly. Classes and sections are properly filtered based on the provided parameters.</p>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    <h3 style="margin: 0 0 10px 0;">❌ Tests Failed</h3>
                    <p style="margin: 0;">The hierarchical filtering is NOT working as expected. Please check the model implementation.</p>
                </div>
            <?php endif; ?>
            
            <table>
                <tr>
                    <th>Test</th>
                    <th>Classes Count</th>
                    <th>Sections Count</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>Test 1: No Filters</td>
                    <td><?php echo isset($data1) ? count($data1['classes']) : 'N/A'; ?></td>
                    <td><?php echo isset($data1) ? count($data1['sections']) : 'N/A'; ?></td>
                    <td><span class="badge badge-info">Baseline</span></td>
                </tr>
                <tr>
                    <td>Test 2: Session Filter</td>
                    <td><?php echo isset($data2) ? count($data2['classes']) : 'N/A'; ?></td>
                    <td><?php echo isset($data2) ? count($data2['sections']) : 'N/A'; ?></td>
                    <td>
                        <?php if (isset($data1, $data2)): ?>
                            <span class="badge badge-<?php echo (count($data2['classes']) < count($data1['classes'])) ? 'success' : 'error'; ?>">
                                <?php echo (count($data2['classes']) < count($data1['classes'])) ? '✓ Pass' : '✗ Fail'; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-warning">N/A</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>Test 3: Session + Class Filter</td>
                    <td><?php echo isset($data3) ? count($data3['classes']) : 'N/A'; ?></td>
                    <td><?php echo isset($data3) ? count($data3['sections']) : 'N/A'; ?></td>
                    <td>
                        <?php if (isset($data1, $data3)): ?>
                            <span class="badge badge-<?php echo (count($data3['sections']) < count($data1['sections'])) ? 'success' : 'error'; ?>">
                                <?php echo (count($data3['sections']) < count($data1['sections'])) ? '✓ Pass' : '✗ Fail'; ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-warning">N/A</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>

