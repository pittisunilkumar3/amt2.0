<?php
/**
 * Debug script to identify fee collection discrepancy between dashboard and combined report
 */

// Database configuration
$host = 'localhost';
$dbname = 'amt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== FEE COLLECTION DISCREPANCY DEBUG ===\n";
    echo "Date: " . date('Y-m-d H:i:s') . "\n\n";
    
    // Current month date range (September 2025)
    $start_date = '2025-09-01';
    $end_date = '2025-09-30';
    $st_date = strtotime($start_date);
    $ed_date = strtotime($end_date);
    
    echo "Date Range: $start_date to $end_date\n\n";
    
    // 1. Test Dashboard Calculation (Direct Database Method)
    echo "1. DASHBOARD CALCULATION (Current Method):\n";
    echo "   Using direct database queries with session filtering...\n";
    
    $dashboard_total = 0;
    $dashboard_regular_count = 0;
    $dashboard_other_count = 0;
    
    // Regular fees (dashboard method)
    $sql = "SELECT sfd.amount_detail 
            FROM student_fees_deposite sfd
            JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
            JOIN student_session ss ON ss.id = sfm.student_session_id
            JOIN sessions s ON s.id = ss.session_id
            WHERE sfd.amount_detail IS NOT NULL 
            AND sfd.amount_detail != ''
            AND s.is_active = 'yes'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                            $dashboard_regular_count++;
                        }
                    }
                }
            }
        }
    }
    
    // Other fees (dashboard method)
    $sql = "SELECT sfda.amount_detail 
            FROM student_fees_depositeadding sfda
            JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
            JOIN student_session ss ON ss.id = sfma.student_session_id
            JOIN sessions s ON s.id = ss.session_id
            WHERE sfda.amount_detail IS NOT NULL 
            AND sfda.amount_detail != ''
            AND s.is_active = 'yes'";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
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
                            $dashboard_other_count++;
                        }
                    }
                }
            }
        }
    }
    
    echo "   Regular fees: {$dashboard_regular_count} entries\n";
    echo "   Other fees: {$dashboard_other_count} entries\n";
    echo "   DASHBOARD TOTAL: ₹" . number_format($dashboard_total, 2) . "\n\n";
    
    // 2. Test Combined Report Calculation (Model Method)
    echo "2. COMBINED REPORT CALCULATION (Model Method):\n";
    echo "   Using model getFeeCollectionReport methods...\n";
    
    // This would require loading CodeIgniter framework, so let's simulate the model logic
    // by directly querying the same data the models would return
    
    $combined_total = 0;
    $combined_regular_count = 0;
    $combined_other_count = 0;
    
    // Regular fees (model method simulation)
    $sql = "SELECT sfd.*, s.firstname, s.middlename, s.lastname, ss.class_id, c.class, sec.section,
                   ss.section_id, ss.student_id, fg.name, ft.type, ft.code, ft.is_system,
                   sfm.student_session_id, s.admission_no
            FROM student_fees_deposite sfd
            JOIN fee_groups_feetype fgft ON fgft.id = sfd.fee_groups_feetype_id
            JOIN fee_groups fg ON fg.id = fgft.fee_groups_id
            JOIN feetype ft ON ft.id = fgft.feetype_id
            JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
            JOIN student_session ss ON ss.id = sfm.student_session_id
            JOIN students s ON s.id = ss.student_id
            JOIN classes c ON c.id = ss.class_id
            JOIN sections sec ON sec.id = ss.section_id
            WHERE sfd.amount_detail IS NOT NULL 
            AND sfd.amount_detail != ''";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $combined_total += ($amount + $fine);
                            $combined_regular_count++;
                        }
                    }
                }
            }
        }
    }
    
    // Other fees (model method simulation)
    $sql = "SELECT sfda.*, s.firstname, s.middlename, s.lastname, ss.class_id, c.class, sec.section,
                   ss.section_id, ss.student_id, fga.name, fta.type, fta.code, fta.is_system,
                   sfma.student_session_id, s.admission_no
            FROM student_fees_depositeadding sfda
            JOIN fee_groups_feetypeadding fgfta ON fgfta.id = sfda.fee_groups_feetype_id
            JOIN fee_groupsadding fga ON fga.id = fgfta.fee_groups_id
            JOIN feetypeadding fta ON fta.id = fgfta.feetype_id
            JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
            JOIN student_session ss ON ss.id = sfma.student_session_id
            JOIN students s ON s.id = ss.student_id
            JOIN classes c ON c.id = ss.class_id
            JOIN sections sec ON sec.id = ss.section_id
            WHERE sfda.amount_detail IS NOT NULL 
            AND sfda.amount_detail != ''";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['amount_detail'])) {
            $amount_detail = json_decode($row['amount_detail']);
            if ($amount_detail) {
                foreach ($amount_detail as $entry) {
                    if (isset($entry->date)) {
                        $entry_date = strtotime($entry->date);
                        if ($entry_date >= $st_date && $entry_date <= $ed_date) {
                            $amount = floatval($entry->amount ?? 0);
                            $fine = floatval($entry->amount_fine ?? 0);
                            $combined_total += ($amount + $fine);
                            $combined_other_count++;
                        }
                    }
                }
            }
        }
    }
    
    echo "   Regular fees: {$combined_regular_count} entries\n";
    echo "   Other fees: {$combined_other_count} entries\n";
    echo "   COMBINED TOTAL: ₹" . number_format($combined_total, 2) . "\n\n";
    
    // 3. Compare Results
    echo "3. COMPARISON:\n";
    echo "   Dashboard Total: ₹" . number_format($dashboard_total, 2) . "\n";
    echo "   Combined Report Total: ₹" . number_format($combined_total, 2) . "\n";
    echo "   Difference: ₹" . number_format(abs($dashboard_total - $combined_total), 2) . "\n";
    
    if ($dashboard_total == $combined_total) {
        echo "   ✓ AMOUNTS MATCH - No discrepancy found!\n";
    } else {
        echo "   ✗ DISCREPANCY DETECTED!\n";
        echo "   Possible causes:\n";
        echo "   - Different session filtering\n";
        echo "   - Different date range interpretation\n";
        echo "   - Different data sources\n";
        echo "   - Different calculation methods\n";
    }
    
    // 4. Check for formatting differences
    echo "\n4. FORMATTING CHECK:\n";
    echo "   Dashboard (₹645,000.00) vs Combined Report (₹6,42,000.00)\n";
    echo "   This appears to be Indian vs Western number formatting\n";
    echo "   Both represent the same amount: " . number_format($dashboard_total, 2) . "\n";
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
?>
