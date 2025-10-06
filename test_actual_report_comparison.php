<?php
/**
 * Test actual Combined Collection Report vs Dashboard comparison
 */

echo "=== ACTUAL REPORT VS DASHBOARD COMPARISON ===\n\n";

// Test by making HTTP requests to both interfaces
$base_url = 'http://localhost/amt';

echo "1. Testing Dashboard Fee Collection Card:\n";

// Get dashboard data via AJAX (simulate the AJAX call)
$dashboard_url = $base_url . '/admin/admin/getDashboardSummary';

$post_data = array(
    'filter_type' => 'current',
    'start_date' => '2025-09-01',
    'end_date' => '2025-09-30'
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dashboard_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'X-Requested-With: XMLHttpRequest',
    'Content-Type: application/x-www-form-urlencoded'
));

$dashboard_response = curl_exec($ch);
$dashboard_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Status: $dashboard_http_code\n";

if ($dashboard_http_code == 200 && $dashboard_response) {
    $dashboard_data = json_decode($dashboard_response, true);
    if ($dashboard_data && isset($dashboard_data['data']['total_fee_collection'])) {
        $dashboard_amount = $dashboard_data['data']['total_fee_collection'];
        echo "   Dashboard Fee Collection: ₹" . number_format($dashboard_amount, 2) . "\n";
    } else {
        echo "   Error: Could not parse dashboard response\n";
        echo "   Response: " . substr($dashboard_response, 0, 200) . "...\n";
    }
} else {
    echo "   Error: Dashboard AJAX request failed\n";
    echo "   Response: " . substr($dashboard_response, 0, 200) . "...\n";
}

echo "\n2. Testing Combined Collection Report:\n";

// For the Combined Collection Report, we need to simulate the form submission
$report_url = $base_url . '/financereports/combined_collection_report';

$report_post_data = array(
    'search_type' => 'period',
    'date_from' => '09/01/2025',
    'date_to' => '09/30/2025',
    'collect_by' => '',
    'feetype_id' => '',
    'group' => '',
    'class_id' => '',
    'section_id' => '',
    'sch_session_id' => ''
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $report_url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($report_post_data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$report_response = curl_exec($ch);
$report_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "   HTTP Status: $report_http_code\n";

if ($report_http_code == 200 && $report_response) {
    // Parse the HTML response to extract the grand total
    if (preg_match('/grand_total.*?text-right.*?>([\d,]+\.?\d*)</', $report_response, $matches)) {
        $report_amount_str = str_replace(',', '', $matches[1]);
        $report_amount = floatval($report_amount_str);
        echo "   Report Grand Total: ₹" . number_format($report_amount, 2) . "\n";
        
        // Compare the amounts
        if (isset($dashboard_amount)) {
            echo "\n3. COMPARISON RESULTS:\n";
            echo "   Dashboard Amount: ₹" . number_format($dashboard_amount, 2) . "\n";
            echo "   Report Amount:    ₹" . number_format($report_amount, 2) . "\n";
            
            $difference = $dashboard_amount - $report_amount;
            echo "   Difference:       ₹" . number_format($difference, 2) . "\n";
            
            if (abs($difference) < 0.01) {
                echo "   Status: ✓ AMOUNTS MATCH PERFECTLY\n";
            } else {
                echo "   Status: ✗ DISCREPANCY DETECTED\n";
                echo "   Percentage Diff: " . round(($difference / max($dashboard_amount, $report_amount)) * 100, 2) . "%\n";
            }
        }
    } else {
        echo "   Error: Could not extract grand total from report HTML\n";
        // Try alternative parsing methods
        if (preg_match('/Grand Total.*?(\d+[\d,]*\.?\d*)/', $report_response, $matches)) {
            echo "   Alternative match found: " . $matches[1] . "\n";
        }
    }
} else {
    echo "   Error: Combined Collection Report request failed\n";
}

// Additional test: Direct database comparison for verification
echo "\n4. DIRECT DATABASE VERIFICATION:\n";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=amt", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $start_date = '2025-09-01';
    $end_date = '2025-09-30';
    $st_date = strtotime($start_date);
    $ed_date = strtotime($end_date);
    
    $db_total = 0;
    $db_entries = 0;
    
    // Regular fees
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL AND amount_detail != ''");
    $stmt->execute();
    
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $db_total += ($amount + $fine);
                            $db_entries++;
                        }
                    }
                }
            }
        }
    }
    
    // Other fees
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_depositeadding WHERE amount_detail IS NOT NULL AND amount_detail != ''");
    $stmt->execute();
    
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $db_total += ($amount + $fine);
                            $db_entries++;
                        }
                    }
                }
            }
        }
    }
    
    echo "   Database Direct Total: ₹" . number_format($db_total, 2) . " ({$db_entries} entries)\n";
    
} catch (PDOException $e) {
    echo "   Database error: " . $e->getMessage() . "\n";
}

echo "\n=== SUMMARY ===\n";
if (isset($dashboard_amount) && isset($report_amount)) {
    if (abs($dashboard_amount - $report_amount) < 0.01) {
        echo "✓ No discrepancy found - both interfaces show the same amount\n";
        echo "✓ Data consistency is maintained across the application\n";
    } else {
        echo "✗ Discrepancy confirmed - amounts differ between interfaces\n";
        echo "✗ Investigation and fix required\n";
    }
} else {
    echo "⚠ Could not complete comparison due to request failures\n";
    echo "⚠ Manual verification recommended\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
