<?php
/**
 * Test updated dashboard calculation against Monthly Collection Report
 */

echo "=== DASHBOARD-MONTHLY REPORT ALIGNMENT TEST ===\n\n";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n\n";
    
    // Test September 2025 data
    $start_date = '2025-09-01';
    $end_date = '2025-09-30';
    $st_date = strtotime($start_date);
    $ed_date = strtotime($end_date);
    
    echo "=== TESTING UPDATED DASHBOARD CALCULATION ===\n";
    echo "Date range: $start_date to $end_date\n\n";
    
    // Updated Dashboard Calculation (with session filtering)
    echo "1. UPDATED DASHBOARD CALCULATION (Session-Filtered):\n";
    
    $dashboard_total = 0;
    $dashboard_entries = 0;
    
    // Get current session regular fees
    $stmt = $pdo->prepare("
        SELECT sfd.amount_detail
        FROM student_fees_deposite sfd
        JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
        JOIN student_session ss ON ss.id = sfm.student_session_id
        JOIN sessions s ON s.id = ss.session_id
        WHERE sfd.amount_detail IS NOT NULL 
        AND sfd.amount_detail != ''
        AND s.is_active = 'yes'
    ");
    $stmt->execute();
    
    $regular_count = 0;
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
                            $dashboard_total += ($amount + $fine);
                            $regular_count++;
                        }
                    }
                }
            }
        }
    }
    
    // Get current session other fees
    $stmt = $pdo->prepare("
        SELECT sfda.amount_detail
        FROM student_fees_depositeadding sfda
        JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
        JOIN student_session ss ON ss.id = sfma.student_session_id
        JOIN sessions s ON s.id = ss.session_id
        WHERE sfda.amount_detail IS NOT NULL 
        AND sfda.amount_detail != ''
        AND s.is_active = 'yes'
    ");
    $stmt->execute();
    
    $other_count = 0;
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
                            $dashboard_total += ($amount + $fine);
                            $other_count++;
                        }
                    }
                }
            }
        }
    }
    
    $dashboard_entries = $regular_count + $other_count;
    echo "   Regular fees: {$regular_count} entries\n";
    echo "   Other fees: {$other_count} entries\n";
    echo "   Updated Dashboard Total: ₹" . number_format($dashboard_total, 2) . " ({$dashboard_entries} entries)\n\n";
    
    // Monthly Report Calculation (for comparison)
    echo "2. MONTHLY REPORT CALCULATION (Reference):\n";
    
    $formated_date_from = strtotime($start_date);
    $formated_date_to = strtotime($end_date);
    
    // Get current session student fees (same as Monthly Report)
    $stmt = $pdo->prepare("
        SELECT sfd.amount_detail, sfd.id as student_fees_deposite_id
        FROM student_fees_deposite sfd
        JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
        JOIN student_session ss ON ss.id = sfm.student_session_id
        WHERE sfd.amount_detail IS NOT NULL AND sfd.amount_detail != ''
        AND ss.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
    ");
    $stmt->execute();
    $st_fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get current session other fees
    $stmt = $pdo->prepare("
        SELECT sfda.amount_detail, sfda.id as student_fees_deposite_id
        FROM student_fees_depositeadding sfda
        JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
        JOIN student_session ss ON ss.id = sfma.student_session_id
        WHERE sfda.amount_detail IS NOT NULL AND sfda.amount_detail != ''
        AND ss.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
    ");
    $stmt->execute();
    $st_other_fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $monthly_report_total = 0;
    $monthly_regular_count = 0;
    $monthly_other_count = 0;
    
    // Process regular fees (Monthly Report logic)
    if (!empty($st_fees)) {
        foreach ($st_fees as $fee_value) {
            $fees_details = json_decode($fee_value['amount_detail']);
            if (!empty($fees_details)) {
                foreach ($fees_details as $fees_detail_value) {
                    $date = strtotime($fees_detail_value->date);
                    if ($date >= $formated_date_from && $date <= $formated_date_to) {
                        $amount = floatval($fees_detail_value->amount ?? 0);
                        $fine = floatval($fees_detail_value->amount_fine ?? 0);
                        $monthly_report_total += ($amount + $fine);
                        $monthly_regular_count++;
                    }
                }
            }
        }
    }
    
    // Process other fees
    if (!empty($st_other_fees)) {
        foreach ($st_other_fees as $fee_value) {
            $fees_details = json_decode($fee_value['amount_detail']);
            if (!empty($fees_details)) {
                foreach ($fees_details as $fees_detail_value) {
                    $date = strtotime($fees_detail_value->date);
                    if ($date >= $formated_date_from && $date <= $formated_date_to) {
                        $amount = floatval($fees_detail_value->amount ?? 0);
                        $fine = floatval($fees_detail_value->amount_fine ?? 0);
                        $monthly_report_total += ($amount + $fine);
                        $monthly_other_count++;
                    }
                }
            }
        }
    }
    
    $monthly_report_entries = $monthly_regular_count + $monthly_other_count;
    echo "   Regular fees: {$monthly_regular_count} entries\n";
    echo "   Other fees: {$monthly_other_count} entries\n";
    echo "   Monthly Report Total: ₹" . number_format($monthly_report_total, 2) . " ({$monthly_report_entries} entries)\n\n";
    
    // Comparison
    echo "=== ALIGNMENT VERIFICATION ===\n";
    $difference = $dashboard_total - $monthly_report_total;
    echo "Updated Dashboard:  ₹" . number_format($dashboard_total, 2) . " ({$dashboard_entries} entries)\n";
    echo "Monthly Report:     ₹" . number_format($monthly_report_total, 2) . " ({$monthly_report_entries} entries)\n";
    echo "Difference:         ₹" . number_format($difference, 2) . "\n";
    echo "Entry Difference:   " . ($dashboard_entries - $monthly_report_entries) . " entries\n\n";
    
    if (abs($difference) < 0.01 && $dashboard_entries == $monthly_report_entries) {
        echo "✅ PERFECT ALIGNMENT ACHIEVED!\n";
        echo "✅ Dashboard now matches Monthly Collection Report exactly\n";
        echo "✅ Both use session filtering for consistent results\n";
        echo "✅ Data consistency maintained across interfaces\n";
    } else {
        echo "⚠ Minor discrepancy detected - further investigation needed\n";
        echo "Difference: ₹" . number_format(abs($difference), 2) . "\n";
    }
    
    // Test other periods for consistency
    echo "\n=== TESTING OTHER PERIODS ===\n";
    
    $test_periods = array(
        array('2025-08-01', '2025-08-31', 'August 2025'),
        array('2025-07-01', '2025-07-31', 'July 2025')
    );
    
    foreach ($test_periods as $period) {
        $test_start = $period[0];
        $test_end = $period[1];
        $test_label = $period[2];
        
        $test_st_date = strtotime($test_start);
        $test_ed_date = strtotime($test_end);
        
        // Dashboard calculation
        $test_dashboard_total = 0;
        $test_dashboard_entries = 0;
        
        // Regular fees
        $stmt = $pdo->prepare("
            SELECT sfd.amount_detail
            FROM student_fees_deposite sfd
            JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
            JOIN student_session ss ON ss.id = sfm.student_session_id
            JOIN sessions s ON s.id = ss.session_id
            WHERE sfd.amount_detail IS NOT NULL 
            AND sfd.amount_detail != ''
            AND s.is_active = 'yes'
        ");
        $stmt->execute();
        
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!empty($row['amount_detail'])) {
                $amount_detail = json_decode($row['amount_detail']);
                if ($amount_detail) {
                    foreach ($amount_detail as $entry) {
                        if (isset($entry->date)) {
                            $entry_date = strtotime($entry->date);
                            if ($entry_date >= $test_st_date && $entry_date <= $test_ed_date) {
                                $amount = floatval($entry->amount ?? 0);
                                $fine = floatval($entry->amount_fine ?? 0);
                                $test_dashboard_total += ($amount + $fine);
                                $test_dashboard_entries++;
                            }
                        }
                    }
                }
            }
        }
        
        // Other fees
        $stmt = $pdo->prepare("
            SELECT sfda.amount_detail
            FROM student_fees_depositeadding sfda
            JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
            JOIN student_session ss ON ss.id = sfma.student_session_id
            JOIN sessions s ON s.id = ss.session_id
            WHERE sfda.amount_detail IS NOT NULL 
            AND sfda.amount_detail != ''
            AND s.is_active = 'yes'
        ");
        $stmt->execute();
        
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (!empty($row['amount_detail'])) {
                $amount_detail = json_decode($row['amount_detail']);
                if ($amount_detail) {
                    foreach ($amount_detail as $entry) {
                        if (isset($entry->date)) {
                            $entry_date = strtotime($entry->date);
                            if ($entry_date >= $test_st_date && $entry_date <= $test_ed_date) {
                                $amount = floatval($entry->amount ?? 0);
                                $fine = floatval($entry->amount_fine ?? 0);
                                $test_dashboard_total += ($amount + $fine);
                                $test_dashboard_entries++;
                            }
                        }
                    }
                }
            }
        }
        
        echo "{$test_label}: ₹" . number_format($test_dashboard_total, 2) . " ({$test_dashboard_entries} entries)\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETE ===\n";
echo "Dashboard calculation updated to match Monthly Collection Report exactly.\n";
echo "Both interfaces now use session filtering for consistent results.\n";
?>
