<?php
/**
 * Quick Test for Fee Collection Filters API
 */

// API URL
$url = 'http://localhost/amt/api/fee-collection-filters/get';

// Headers
$headers = [
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
];

// Test data
$data = [];

// Make request
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

// Display results
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fee Collection Filters API - Quick Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        .status { padding: 15px; border-radius: 5px; margin: 20px 0; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #dee2e6; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .badge-success { background: #28a745; color: white; }
        .badge-error { background: #dc3545; color: white; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Fee Collection Filters API - Quick Test</h1>
        
        <div class="info">
            <strong>📍 Endpoint:</strong> POST <?php echo $url; ?><br>
            <strong>🔑 Headers:</strong> Client-Service: smartschool, Auth-Key: schoolAdmin@<br>
            <strong>📦 Request Body:</strong> {} (empty)
        </div>
        
        <h2>Test Results</h2>
        
        <table>
            <tr>
                <th>Property</th>
                <th>Value</th>
            </tr>
            <tr>
                <td><strong>HTTP Status Code</strong></td>
                <td>
                    <?php if ($http_code == 200): ?>
                        <span class="badge badge-success"><?php echo $http_code; ?> OK</span>
                    <?php else: ?>
                        <span class="badge badge-error"><?php echo $http_code; ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong>cURL Error</strong></td>
                <td><?php echo $error ? $error : '✅ None'; ?></td>
            </tr>
        </table>
        
        <?php if ($http_code == 200): ?>
            <div class="status success">
                <strong>✅ SUCCESS!</strong> The API endpoint is working correctly.
            </div>
        <?php else: ?>
            <div class="status error">
                <strong>❌ ERROR!</strong> The API endpoint returned an error.
            </div>
        <?php endif; ?>
        
        <h2>Response Data</h2>
        <pre><?php echo htmlspecialchars(json_encode(json_decode($response), JSON_PRETTY_PRINT)); ?></pre>
        
        <?php if ($http_code == 200): ?>
            <?php 
            $response_data = json_decode($response, true);
            if (isset($response_data['data'])) {
                $data = $response_data['data'];
            ?>
            <h2>📊 Filter Options Summary</h2>
            <table>
                <tr>
                    <th>Filter Type</th>
                    <th>Count</th>
                    <th>Status</th>
                </tr>
                <tr>
                    <td>Sessions</td>
                    <td><?php echo count($data['sessions'] ?? []); ?></td>
                    <td><?php echo count($data['sessions'] ?? []) > 0 ? '✅' : '⚠️'; ?></td>
                </tr>
                <tr>
                    <td>Classes</td>
                    <td><?php echo count($data['classes'] ?? []); ?></td>
                    <td><?php echo count($data['classes'] ?? []) > 0 ? '✅' : '⚠️'; ?></td>
                </tr>
                <tr>
                    <td>Sections</td>
                    <td><?php echo count($data['sections'] ?? []); ?></td>
                    <td><?php echo count($data['sections'] ?? []) > 0 ? '✅' : '⚠️'; ?></td>
                </tr>
                <tr>
                    <td>Fee Groups</td>
                    <td><?php echo count($data['fee_groups'] ?? []); ?></td>
                    <td><?php echo count($data['fee_groups'] ?? []) > 0 ? '✅' : '⚠️'; ?></td>
                </tr>
                <tr>
                    <td>Fee Types</td>
                    <td><?php echo count($data['fee_types'] ?? []); ?></td>
                    <td><?php echo count($data['fee_types'] ?? []) > 0 ? '✅' : '⚠️'; ?></td>
                </tr>
                <tr>
                    <td>Collect By (Staff)</td>
                    <td><?php echo count($data['collect_by'] ?? []); ?></td>
                    <td><?php echo count($data['collect_by'] ?? []) > 0 ? '✅' : '⚠️'; ?></td>
                </tr>
                <tr>
                    <td>Group By Options</td>
                    <td><?php echo count($data['group_by_options'] ?? []); ?></td>
                    <td><?php echo count($data['group_by_options'] ?? []) == 3 ? '✅' : '⚠️'; ?></td>
                </tr>
            </table>
            <?php } ?>
        <?php endif; ?>
        
        <div class="info" style="margin-top: 30px;">
            <strong>📚 Documentation:</strong><br>
            - Full Documentation: <code>api/documentation/FEE_COLLECTION_FILTERS_API_DOCUMENTATION.md</code><br>
            - Quick Reference: <code>api/documentation/FEE_COLLECTION_FILTERS_API_QUICK_REFERENCE.md</code><br>
            - Implementation Summary: <code>FEE_COLLECTION_FILTERS_API_IMPLEMENTATION_SUMMARY.md</code>
        </div>
    </div>
</body>
</html>

