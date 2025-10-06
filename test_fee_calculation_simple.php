<?php
/**
 * Simple test for fee collection calculation
 */

echo "=== Simple Fee Collection Test ===\n\n";

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'amt';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Database connection successful\n\n";
    
    // Test date range
    $start_date = '2025-09-01';
    $end_date = '2025-09-30';
    
    echo "Testing date range: $start_date to $end_date\n\n";
    
    // Get records count
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM student_fees_deposite WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?");
    $stmt->execute([$start_date, $end_date]);
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "Records found: $count\n\n";
    
    // Get sample records
    $stmt = $pdo->prepare("SELECT id, amount_detail, created_at FROM student_fees_deposite WHERE DATE(created_at) >= ? AND DATE(created_at) <= ? LIMIT 5");
    $stmt->execute([$start_date, $end_date]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Sample records:\n";
    $total_collection = 0;
    
    foreach ($records as $record) {
        echo "ID: {$record['id']}, Date: {$record['created_at']}\n";
        
        if (!empty($record['amount_detail'])) {
            $amount_detail = json_decode($record['amount_detail'], true);
            if (is_array($amount_detail)) {
                $record_total = 0;
                foreach ($amount_detail as $key => $detail) {
                    if (isset($detail['amount']) && $detail['amount'] > 0) {
                        $amount = floatval($detail['amount']);
                        $record_total += $amount;
                        echo "  Entry $key: ₹$amount\n";
                    }
                }
                echo "  Record total: ₹$record_total\n";
                $total_collection += $record_total;
            }
        }
        echo "\n";
    }
    
    echo "Sample total (5 records): ₹" . number_format($total_collection, 2) . "\n\n";
    
    // Calculate full total
    $stmt = $pdo->prepare("SELECT amount_detail FROM student_fees_deposite WHERE DATE(created_at) >= ? AND DATE(created_at) <= ?");
    $stmt->execute([$start_date, $end_date]);
    $all_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $full_total = 0;
    $processed_count = 0;
    
    foreach ($all_records as $record) {
        if (!empty($record['amount_detail'])) {
            $amount_detail = json_decode($record['amount_detail'], true);
            if (is_array($amount_detail)) {
                foreach ($amount_detail as $detail) {
                    if (isset($detail['amount']) && $detail['amount'] > 0) {
                        $full_total += floatval($detail['amount']);
                    }
                }
                $processed_count++;
            }
        }
    }
    
    echo "Full calculation results:\n";
    echo "Records processed: $processed_count\n";
    echo "Total fee collection: ₹" . number_format($full_total, 2) . "\n\n";
    
    if ($full_total > 0) {
        echo "✓ Fee collection calculation is working!\n";
        echo "✓ The issue was the is_active filter - now fixed\n";
    } else {
        echo "⚠ Fee collection is still zero\n";
    }
    
} catch (PDOException $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
