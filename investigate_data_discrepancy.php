<?php
/**
 * Comprehensive investigation of data discrepancy between Dashboard and Combined Collection Report
 */

echo "=== DATA DISCREPANCY INVESTIGATION ===\n\n";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n\n";
    
    // Test current month (September 2025)
    $start_date = '2025-09-01';
    $end_date = '2025-09-30';
    
    echo "=== TESTING SEPTEMBER 2025 ($start_date to $end_date) ===\n\n";
    
    // Method 1: Dashboard Direct Calculation (Current Implementation)
    echo "1. DASHBOARD CALCULATION (Current Direct Method):\n";
    
    $dashboard_total = 0;
    $dashboard_entries = 0;
    $st_date = strtotime($start_date);
    $ed_date = strtotime($end_date);
    
    // Regular fees
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_deposite WHERE amount_detail IS NOT NULL AND amount_detail != ''");
    $stmt->execute();
    $regular_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($regular_records as $row) {
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
                        }
                    }
                }
            }
        }
    }
    
    // Other fees
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_depositeadding WHERE amount_detail IS NOT NULL AND amount_detail != ''");
    $stmt->execute();
    $other_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($other_records as $row) {
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
                        }
                    }
                }
            }
        }
    }
    
    echo "   Dashboard Total: ₹" . number_format($dashboard_total, 2) . " ({$dashboard_entries} entries)\n\n";
    
    // Method 2: Combined Collection Report Simulation (Model-based)
    echo "2. COMBINED COLLECTION REPORT SIMULATION:\n";
    
    // Simulate the model's getFeeCollectionReport logic
    $report_total = 0;
    $report_entries = 0;
    
    // Get data with joins (similar to model)
    $stmt = $pdo->prepare("
        SELECT sfd.amount_detail, sfd.id, sfd.created_at
        FROM student_fees_deposite sfd
        JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
        JOIN student_session ss ON ss.id = sfm.student_session_id
        JOIN students s ON s.id = ss.student_id
        WHERE sfd.amount_detail IS NOT NULL AND sfd.amount_detail != ''
    ");
    $stmt->execute();
    $regular_model_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process regular fees using model logic (findObjectById simulation)
    foreach ($regular_model_data as $record) {
        if (!empty($record['amount_detail'])) {
            $amount_detail = json_decode($record['amount_detail']);
            if ($amount_detail) {
                // Simulate findObjectById logic
                for ($i = $st_date; $i <= $ed_date; $i += 86400) {
                    $find = date('Y-m-d', $i);
                    foreach ($amount_detail as $row_key => $row_value) {
                        if (isset($row_value->date) && $row_value->date == $find) {
                            $amount = floatval($row_value->amount ?? 0);
                            $fine = floatval($row_value->amount_fine ?? 0);
                            $report_total += ($amount + $fine);
                            $report_entries++;
                        }
                    }
                }
            }
        }
    }
    
    // Get other fees with joins
    $stmt = $pdo->prepare("
        SELECT sfda.amount_detail, sfda.id, sfda.created_at
        FROM student_fees_depositeadding sfda
        JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
        JOIN student_session ss ON ss.id = sfma.student_session_id
        JOIN students s ON s.id = ss.student_id
        WHERE sfda.amount_detail IS NOT NULL AND sfda.amount_detail != ''
    ");
    $stmt->execute();
    $other_model_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process other fees using model logic
    foreach ($other_model_data as $record) {
        if (!empty($record['amount_detail'])) {
            $amount_detail = json_decode($record['amount_detail']);
            if ($amount_detail) {
                // Simulate findObjectById logic
                for ($i = $st_date; $i <= $ed_date; $i += 86400) {
                    $find = date('Y-m-d', $i);
                    foreach ($amount_detail as $row_key => $row_value) {
                        if (isset($row_value->date) && $row_value->date == $find) {
                            $amount = floatval($row_value->amount ?? 0);
                            $fine = floatval($row_value->amount_fine ?? 0);
                            $report_total += ($amount + $fine);
                            $report_entries++;
                        }
                    }
                }
            }
        }
    }
    
    echo "   Report Total: ₹" . number_format($report_total, 2) . " ({$report_entries} entries)\n\n";
    
    // Method 3: Alternative calculation methods to identify discrepancy
    echo "3. ALTERNATIVE CALCULATION METHODS:\n";
    
    // Method 3a: Amount only (no fines)
    $amount_only_total = 0;
    foreach ($regular_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount_only_total += floatval($entry->amount ?? 0);
                        }
                    }
                }
            }
        }
    }
    foreach ($other_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount_only_total += floatval($entry->amount ?? 0);
                        }
                    }
                }
            }
        }
    }
    
    echo "   Amount Only (no fines): ₹" . number_format($amount_only_total, 2) . "\n";
    
    // Method 3b: Amount minus discount
    $amount_minus_discount = 0;
    foreach ($regular_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $discount = floatval($entry->amount_discount ?? 0);
                            $amount_minus_discount += ($amount - $discount);
                        }
                    }
                }
            }
        }
    }
    foreach ($other_records as $row) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $discount = floatval($entry->amount_discount ?? 0);
                            $amount_minus_discount += ($amount - $discount);
                        }
                    }
                }
            }
        }
    }
    
    echo "   Amount - Discount: ₹" . number_format($amount_minus_discount, 2) . "\n\n";
    
    // Analysis
    echo "=== DISCREPANCY ANALYSIS ===\n";
    $difference = $dashboard_total - $report_total;
    echo "Dashboard Total:     ₹" . number_format($dashboard_total, 2) . "\n";
    echo "Report Total:        ₹" . number_format($report_total, 2) . "\n";
    echo "Difference:          ₹" . number_format($difference, 2) . "\n";
    echo "Percentage Diff:     " . round(($difference / max($dashboard_total, $report_total)) * 100, 2) . "%\n\n";
    
    if (abs($difference) < 0.01) {
        echo "✓ CALCULATIONS MATCH - No discrepancy found\n";
    } else {
        echo "✗ DISCREPANCY DETECTED - Amounts do not match\n";
        echo "Investigation needed to determine correct calculation\n";
    }
    
    // Data source analysis
    echo "\n=== DATA SOURCE ANALYSIS ===\n";
    echo "Regular records (direct): " . count($regular_records) . "\n";
    echo "Regular records (model):  " . count($regular_model_data) . "\n";
    echo "Other records (direct):   " . count($other_records) . "\n";
    echo "Other records (model):    " . count($other_model_data) . "\n";
    
    if (count($regular_records) != count($regular_model_data)) {
        echo "⚠ Regular records count mismatch - JOIN conditions may filter data\n";
    }
    if (count($other_records) != count($other_model_data)) {
        echo "⚠ Other records count mismatch - JOIN conditions may filter data\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== INVESTIGATION COMPLETE ===\n";
?>
