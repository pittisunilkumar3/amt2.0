<?php
/**
 * Test Script for Fee Collection Columnwise Report API Fix
 * 
 * Tests the fixed API to ensure amounts are now showing correctly
 * 
 * Run from command line:
 * C:\xampp\php\php.exe test_columnwise_fix.php
 */

// Configuration
$base_url = 'http://localhost/amt/api';
$headers = array(
    'Content-Type: application/json',
    'Client-Service: smartschool',
    'Auth-Key: schoolAdmin@'
);

// Helper function to make API calls
function call_api($url, $data, $headers) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return array(
        'http_code' => $http_code,
        'response' => json_decode($response, true)
    );
}

echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║   FEE COLLECTION COLUMNWISE REPORT API - FIX TEST         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Test 1: Empty Request
echo "========================================\n";
echo "Test #1: Empty Request (All Records)\n";
echo "========================================\n";
echo "URL: {$base_url}/fee-collection-columnwise-report/filter\n";
echo "Request: {}\n";

$result = call_api("{$base_url}/fee-collection-columnwise-report/filter", array(), $headers);

echo "HTTP Code: {$result['http_code']}\n";

if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    if ($result['response']['status'] == 1) {
        echo "✅ API Call Successful\n";
        
        $summary = $result['response']['summary'];
        $total_amount = floatval($summary['total_amount']);
        
        echo "\n--- Summary ---\n";
        echo "Total Students: {$summary['total_students']}\n";
        echo "Total Records: {$summary['total_records']}\n";
        echo "Total Amount: {$summary['total_amount']}\n";
        
        if ($total_amount > 0) {
            echo "\n✅ ✅ ✅ SUCCESS! Amounts are now showing correctly! ✅ ✅ ✅\n";
            
            echo "\n--- Fee Type Totals ---\n";
            if (isset($summary['fee_type_totals'])) {
                foreach ($summary['fee_type_totals'] as $fee_type => $total) {
                    echo "  {$fee_type}: {$total}\n";
                }
            }
            
            // Show sample student data
            if (isset($result['response']['data']) && count($result['response']['data']) > 0) {
                echo "\n--- Sample Student Data (First 3 Students) ---\n";
                $sample_count = min(3, count($result['response']['data']));
                for ($i = 0; $i < $sample_count; $i++) {
                    $student = $result['response']['data'][$i];
                    $student_num = $i + 1;
                    echo "\nStudent #{$student_num}:\n";
                    echo "  Admission No: {$student['admission_no']}\n";
                    echo "  Name: {$student['student_name']}\n";
                    echo "  Class: {$student['class']} - {$student['section']}\n";
                    echo "  Total Paid: {$student['total']}\n";
                    echo "  Fee Payments:\n";
                    foreach ($student['fee_payments'] as $fee_type => $amount) {
                        if ($amount > 0) {
                            echo "    - {$fee_type}: {$amount}\n";
                        }
                    }
                }
            }
            
        } else {
            echo "\n❌ ISSUE: Total amount is still 0\n";
            echo "This might mean:\n";
            echo "  1. No fee collection data exists in the database\n";
            echo "  2. The amount_detail field is empty or null\n";
            echo "  3. Date range filtering is excluding all records\n";
        }
        
    } else {
        echo "❌ API returned status 0\n";
        echo "Message: {$result['response']['message']}\n";
    }
} else {
    echo "❌ Invalid response\n";
    echo "Response: " . json_encode($result['response']) . "\n";
}

// Test 2: This Month
echo "\n\n========================================\n";
echo "Test #2: This Month's Records\n";
echo "========================================\n";
echo "URL: {$base_url}/fee-collection-columnwise-report/filter\n";
echo "Request: {\"search_type\": \"this_month\"}\n";

$result = call_api("{$base_url}/fee-collection-columnwise-report/filter", array('search_type' => 'this_month'), $headers);

echo "HTTP Code: {$result['http_code']}\n";

if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    if ($result['response']['status'] == 1) {
        echo "✅ API Call Successful\n";
        
        $summary = $result['response']['summary'];
        $total_amount = floatval($summary['total_amount']);
        
        echo "\n--- Summary ---\n";
        echo "Total Students: {$summary['total_students']}\n";
        echo "Total Records: {$summary['total_records']}\n";
        echo "Total Amount: {$summary['total_amount']}\n";
        
        if ($total_amount > 0) {
            echo "\n✅ This month's data showing correctly!\n";
        } else {
            echo "\n⚠️  No records for this month (this might be normal if no payments this month)\n";
        }
        
    } else {
        echo "❌ API returned status 0\n";
        echo "Message: {$result['response']['message']}\n";
    }
}

// Test 3: Filter by Class
echo "\n\n========================================\n";
echo "Test #3: Filter by Class 1\n";
echo "========================================\n";
echo "URL: {$base_url}/fee-collection-columnwise-report/filter\n";
echo "Request: {\"search_type\": \"this_year\", \"class_id\": 1}\n";

$result = call_api("{$base_url}/fee-collection-columnwise-report/filter", array('search_type' => 'this_year', 'class_id' => 1), $headers);

echo "HTTP Code: {$result['http_code']}\n";

if ($result['http_code'] == 200 && isset($result['response']['status'])) {
    if ($result['response']['status'] == 1) {
        echo "✅ API Call Successful\n";
        
        $summary = $result['response']['summary'];
        $total_amount = floatval($summary['total_amount']);
        
        echo "\n--- Summary ---\n";
        echo "Total Students: {$summary['total_students']}\n";
        echo "Total Records: {$summary['total_records']}\n";
        echo "Total Amount: {$summary['total_amount']}\n";
        
        if ($total_amount > 0) {
            echo "\n✅ Class filter working correctly!\n";
        } else {
            echo "\n⚠️  No records for class 1 (check if class 1 exists)\n";
        }
        
    } else {
        echo "❌ API returned status 0\n";
        echo "Message: {$result['response']['message']}\n";
    }
}

echo "\n\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║                      TEST COMPLETE                         ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";
echo "If amounts are showing correctly, the fix is successful!\n";
echo "\n";
echo "Next Steps:\n";
echo "  1. Test in Postman with your actual data\n";
echo "  2. Compare with web page results\n";
echo "  3. Verify all fee types are showing correct amounts\n";
echo "\n";
?>

