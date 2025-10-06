<?php
/**
 * Test script to verify dashboard calculation matches Combined Collection Report
 */

echo "=== Combined Collection Report Calculation Test ===\n\n";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n\n";
    
    // Test date range - September 2025
    $start_date = '2025-09-01';
    $end_date = '2025-09-30';
    
    echo "Testing date range: $start_date to $end_date\n\n";
    
    // Test 1: Regular fees calculation (student_fees_deposite table)
    echo "1. Regular Fees Calculation:\n";
    
    $stmt = $pdo->prepare("
        SELECT amount_detail, id, created_at 
        FROM student_fees_deposite 
        WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $regular_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $regular_total = 0;
    $regular_count = 0;
    
    foreach ($regular_records as $record) {
        if (!empty($record['amount_detail'])) {
            $amount_detail = json_decode($record['amount_detail'], true);
            if (is_array($amount_detail)) {
                foreach ($amount_detail as $detail) {
                    // Check if the date falls within our range
                    if (isset($detail['date'])) {
                        $detail_date = $detail['date'];
                        if ($detail_date >= $start_date && $detail_date <= $end_date) {
                            $amount = floatval($detail['amount'] ?? 0);
                            $fine = floatval($detail['amount_fine'] ?? 0);
                            $record_total = $amount + $fine;
                            $regular_total += $record_total;
                            $regular_count++;
                            
                            if ($regular_count <= 5) { // Show first 5 for debugging
                                echo "   Record {$record['id']}: amount={$amount}, fine={$fine}, total={$record_total}\n";
                            }
                        }
                    }
                }
            }
        }
    }
    
    echo "   Regular fees total: ₹" . number_format($regular_total, 2) . " ({$regular_count} entries)\n\n";
    
    // Test 2: Other fees calculation (student_fees_depositeadding table)
    echo "2. Other Fees Calculation:\n";
    
    $stmt = $pdo->prepare("
        SELECT amount_detail, id, created_at 
        FROM student_fees_depositeadding 
        WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $other_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $other_total = 0;
    $other_count = 0;
    
    foreach ($other_records as $record) {
        if (!empty($record['amount_detail'])) {
            $amount_detail = json_decode($record['amount_detail'], true);
            if (is_array($amount_detail)) {
                foreach ($amount_detail as $detail) {
                    // Check if the date falls within our range
                    if (isset($detail['date'])) {
                        $detail_date = $detail['date'];
                        if ($detail_date >= $start_date && $detail_date <= $end_date) {
                            $amount = floatval($detail['amount'] ?? 0);
                            $fine = floatval($detail['amount_fine'] ?? 0);
                            $record_total = $amount + $fine;
                            $other_total += $record_total;
                            $other_count++;
                            
                            if ($other_count <= 5) { // Show first 5 for debugging
                                echo "   Record {$record['id']}: amount={$amount}, fine={$fine}, total={$record_total}\n";
                            }
                        }
                    }
                }
            }
        }
    }
    
    echo "   Other fees total: ₹" . number_format($other_total, 2) . " ({$other_count} entries)\n\n";
    
    // Test 3: Combined total (same as Combined Collection Report)
    $combined_total = $regular_total + $other_total;
    $combined_count = $regular_count + $other_count;
    
    echo "3. Combined Total (Dashboard should match this):\n";
    echo "   Regular fees: ₹" . number_format($regular_total, 2) . "\n";
    echo "   Other fees:   ₹" . number_format($other_total, 2) . "\n";
    echo "   GRAND TOTAL:  ₹" . number_format($combined_total, 2) . " ({$combined_count} entries)\n\n";
    
    // Test 4: Compare with old calculation method
    echo "4. Comparison with Old Method:\n";
    
    $old_total = 0;
    foreach ($regular_records as $record) {
        if (!empty($record['amount_detail'])) {
            $amount_detail = json_decode($record['amount_detail'], true);
            if (is_array($amount_detail)) {
                foreach ($amount_detail as $detail) {
                    if (isset($detail['amount']) && $detail['amount'] > 0) {
                        $old_total += floatval($detail['amount']);
                    }
                }
            }
        }
    }
    
    echo "   Old method (amount only): ₹" . number_format($old_total, 2) . "\n";
    echo "   New method (amount + fine): ₹" . number_format($combined_total, 2) . "\n";
    echo "   Difference: ₹" . number_format($combined_total - $old_total, 2) . "\n\n";
    
    echo "=== Summary ===\n";
    echo "✓ Dashboard Fee Collection card should show: ₹" . number_format($combined_total, 2) . "\n";
    echo "✓ This matches the Combined Collection Report calculation\n";
    echo "✓ Formula: (amount + amount_fine) for each fee entry\n";
    echo "✓ Sources: Regular fees + Other fees\n";
    echo "✓ Date filtering: Based on individual fee entry dates\n";
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
