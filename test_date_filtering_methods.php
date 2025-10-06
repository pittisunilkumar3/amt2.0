<?php
/**
 * Test different date filtering methods to identify discrepancy
 */

echo "=== DATE FILTERING METHODS COMPARISON ===\n\n";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n\n";
    
    $start_date = '2025-09-01';
    $end_date = '2025-09-30';
    
    echo "Testing date range: $start_date to $end_date\n\n";
    
    // Method 1: Current Dashboard Method (timestamp comparison)
    echo "1. DASHBOARD METHOD (Timestamp Comparison):\n";
    
    $dashboard_total = 0;
    $dashboard_entries = 0;
    $st_date = strtotime($start_date);
    $ed_date = strtotime($end_date);
    
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL AND amount_detail != '' LIMIT 5");
    $stmt->execute();
    $sample_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($sample_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $dashboard_total += ($amount + $fine);
                            $dashboard_entries++;
                            echo "   Entry: date={$entry->date}, timestamp={$entry_date}, amount={$amount}, fine={$fine}\n";
                        }
                    }
                }
            }
        }
    }
    
    echo "   Dashboard method total: ₹" . number_format($dashboard_total, 2) . " ({$dashboard_entries} entries)\n\n";
    
    // Method 2: Model Method (exact date string matching)
    echo "2. MODEL METHOD (Exact Date String Matching):\n";
    
    $model_total = 0;
    $model_entries = 0;
    
    // Simulate findObjectById logic
    foreach ($sample_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                // Iterate through each day in the range (model approach)
                for ($i = $st_date; $i <= $ed_date; $i += 86400) {
                    $find = date('Y-m-d', $i);
                    foreach ($amount_detail as $entry) {
                        if (isset($entry->date) && $entry->date == $find) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $model_total += ($amount + $fine);
                            $model_entries++;
                            echo "   Entry: date={$entry->date}, find={$find}, amount={$amount}, fine={$fine}\n";
                        }
                    }
                }
            }
        }
    }
    
    echo "   Model method total: ₹" . number_format($model_total, 2) . " ({$model_entries} entries)\n\n";
    
    // Method 3: Check for date format issues
    echo "3. DATE FORMAT ANALYSIS:\n";
    
    $date_formats = array();
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL AND amount_detail != '' LIMIT 10");
    $stmt->execute();
    
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $date_format = $entry->date;
                        if (!in_array($date_format, $date_formats)) {
                            $date_formats[] = $date_format;
                            if (count($date_formats) <= 10) {
                                echo "   Date format example: {$date_format}\n";
                            }
                        }
                    }
                }
            }
        }
    }
    
    echo "   Total unique date formats found: " . count($date_formats) . "\n\n";
    
    // Method 4: Full comparison with all data
    echo "4. FULL DATA COMPARISON:\n";
    
    $full_dashboard_total = 0;
    $full_model_total = 0;
    $full_dashboard_entries = 0;
    $full_model_entries = 0;
    
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL AND amount_detail != ''");
    $stmt->execute();
    $all_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($all_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                // Dashboard method
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $full_dashboard_total += ($amount + $fine);
                            $full_dashboard_entries++;
                        }
                    }
                }
                
                // Model method
                for ($i = $st_date; $i <= $ed_date; $i += 86400) {
                    $find = date('Y-m-d', $i);
                    foreach ($amount_detail as $entry) {
                        if (isset($entry->date) && $entry->date == $find) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $full_model_total += ($amount + $fine);
                            $full_model_entries++;
                        }
                    }
                }
            }
        }
    }
    
    // Add other fees
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_depositeadding WHERE amount_detail IS NOT NULL AND amount_detail != ''");
    $stmt->execute();
    $other_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($other_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                // Dashboard method
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $full_dashboard_total += ($amount + $fine);
                            $full_dashboard_entries++;
                        }
                    }
                }
                
                // Model method
                for ($i = $st_date; $i <= $ed_date; $i += 86400) {
                    $find = date('Y-m-d', $i);
                    foreach ($amount_detail as $entry) {
                        if (isset($entry->date) && $entry->date == $find) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $full_model_total += ($amount + $fine);
                            $full_model_entries++;
                        }
                    }
                }
            }
        }
    }
    
    echo "   Dashboard method (full): ₹" . number_format($full_dashboard_total, 2) . " ({$full_dashboard_entries} entries)\n";
    echo "   Model method (full):     ₹" . number_format($full_model_total, 2) . " ({$full_model_entries} entries)\n";
    
    $difference = $full_dashboard_total - $full_model_total;
    echo "   Difference:              ₹" . number_format($difference, 2) . "\n\n";
    
    if (abs($difference) < 0.01) {
        echo "✓ METHODS MATCH - No discrepancy in date filtering\n";
    } else {
        echo "✗ METHODS DIFFER - Date filtering approach causes discrepancy\n";
        echo "  The model's exact date string matching produces different results\n";
        echo "  Dashboard should be updated to use the model's approach\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
?>
