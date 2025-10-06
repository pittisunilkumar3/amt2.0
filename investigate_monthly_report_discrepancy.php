<?php
/**
 * Investigate discrepancy between Dashboard and Monthly Collection Report
 */

echo "=== MONTHLY COLLECTION REPORT DISCREPANCY INVESTIGATION ===\n\n";

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
    $formated_date_from = strtotime($start_date);
    $formated_date_to = strtotime($end_date);
    
    echo "=== COMPARISON FOR SEPTEMBER 2025 ===\n";
    echo "Date range: $start_date to $end_date\n";
    echo "Dashboard shows: ₹645,000.00\n";
    echo "Monthly Report shows: ₹642,000.00\n";
    echo "Discrepancy: ₹3,000.00\n\n";
    
    // Method 1: Dashboard Calculation (Current Implementation)
    echo "1. DASHBOARD CALCULATION:\n";
    
    $dashboard_total = 0;
    $dashboard_entries = 0;
    $st_date = strtotime($start_date);
    $ed_date = strtotime($end_date);
    
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
                            $dashboard_entries++;
                        }
                    }
                }
            }
        }
    }
    
    echo "   Dashboard Total: ₹" . number_format($dashboard_total, 2) . " ({$dashboard_entries} entries)\n\n";
    
    // Method 2: Monthly Report Calculation (Daily Collection Report Logic)
    echo "2. MONTHLY REPORT CALCULATION (Daily Collection Report Logic):\n";
    
    // Get current session student fees (regular fees)
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
    $monthly_report_entries = 0;
    
    // Process regular fees (same logic as reportdailycollection)
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
                        $monthly_report_entries++;
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
                        $monthly_report_entries++;
                    }
                }
            }
        }
    }
    
    echo "   Monthly Report Total: ₹" . number_format($monthly_report_total, 2) . " ({$monthly_report_entries} entries)\n\n";
    
    // Analysis
    echo "=== DISCREPANCY ANALYSIS ===\n";
    $difference = $dashboard_total - $monthly_report_total;
    echo "Dashboard Total:      ₹" . number_format($dashboard_total, 2) . " ({$dashboard_entries} entries)\n";
    echo "Monthly Report Total: ₹" . number_format($monthly_report_total, 2) . " ({$monthly_report_entries} entries)\n";
    echo "Difference:           ₹" . number_format($difference, 2) . "\n";
    echo "Entry Difference:     " . ($dashboard_entries - $monthly_report_entries) . " entries\n\n";
    
    // Root cause analysis
    echo "=== ROOT CAUSE ANALYSIS ===\n";
    
    if (abs($difference) > 0.01) {
        echo "✗ DISCREPANCY CONFIRMED\n\n";
        
        echo "Potential causes:\n";
        echo "1. SESSION FILTERING: Monthly report only includes current session data\n";
        echo "2. JOIN CONDITIONS: Monthly report uses JOINs that may filter out some records\n";
        echo "3. DATA SCOPE: Dashboard includes all records, Monthly report includes session-specific records\n\n";
        
        // Check session filtering impact
        echo "INVESTIGATING SESSION FILTERING:\n";
        
        // Get all records count (dashboard approach)
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM student_fees_deposite WHERE amount_detail IS NOT NULL AND amount_detail != ''");
        $stmt->execute();
        $all_regular_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM student_fees_depositeadding WHERE amount_detail IS NOT NULL AND amount_detail != ''");
        $stmt->execute();
        $all_other_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Get session-filtered count (monthly report approach)
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM student_fees_deposite sfd
            JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
            JOIN student_session ss ON ss.id = sfm.student_session_id
            WHERE sfd.amount_detail IS NOT NULL AND sfd.amount_detail != ''
            AND ss.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
        ");
        $stmt->execute();
        $session_regular_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total 
            FROM student_fees_depositeadding sfda
            JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
            JOIN student_session ss ON ss.id = sfma.student_session_id
            WHERE sfda.amount_detail IS NOT NULL AND sfda.amount_detail != ''
            AND ss.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
        ");
        $stmt->execute();
        $session_other_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo "Regular fees - All records: {$all_regular_count}, Session-filtered: {$session_regular_count}\n";
        echo "Other fees - All records: {$all_other_count}, Session-filtered: {$session_other_count}\n";
        
        $regular_diff = $all_regular_count - $session_regular_count;
        $other_diff = $all_other_count - $session_other_count;
        
        if ($regular_diff > 0 || $other_diff > 0) {
            echo "⚠ SESSION FILTERING EXCLUDES: {$regular_diff} regular + {$other_diff} other records\n";
            echo "This explains the discrepancy!\n\n";
        }
        
        echo "SOLUTION NEEDED:\n";
        echo "- Dashboard should use session filtering to match Monthly Report\n";
        echo "- OR Monthly Report should include all records to match Dashboard\n";
        echo "- Recommend: Update Dashboard to use session filtering for consistency\n";
        
    } else {
        echo "✓ NO DISCREPANCY - Both calculations match\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== INVESTIGATION COMPLETE ===\n";
?>
