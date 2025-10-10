<?php
// Analyze negative balance issues in fee collection data
require_once 'application/config/database.php';

try {
    $pdo = new PDO('mysql:host=' . $db['default']['hostname'] . ';dbname=' . $db['default']['database'], 
                   $db['default']['username'], $db['default']['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== NEGATIVE BALANCE ANALYSIS FOR SESSION 2025-26 ===\n\n";
    
    // 1. Get fee groups with negative balances
    echo "1. FEE GROUPS WITH NEGATIVE BALANCES:\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            fg.name as fee_group_name,
            COUNT(DISTINCT sfm.student_session_id) as student_count,
            SUM(sfm.amount) as total_fee_amount,
            fg.id as fee_group_id
        FROM fee_groups fg
        INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id AND fsg.session_id = 21
        INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
        INNER JOIN student_session ss ON ss.id = sfm.student_session_id AND ss.session_id = 21
        WHERE fg.is_system = 0
        GROUP BY fg.id, fg.name
        ORDER BY fg.name
    ");
    $stmt->execute();
    $fee_groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $problematic_groups = [];
    
    foreach ($fee_groups as $group) {
        // Calculate collected amount for this fee group
        $stmt2 = $pdo->prepare("
            SELECT sfd.amount_detail
            FROM student_fees_deposite sfd
            INNER JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
            WHERE sfm.fee_session_group_id = ?
        ");
        $stmt2->execute([$group['fee_group_id']]);
        $deposits = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        $total_collected = 0;
        foreach ($deposits as $deposit) {
            if (!empty($deposit['amount_detail'])) {
                $amount_detail = json_decode($deposit['amount_detail'], true);
                if (is_array($amount_detail)) {
                    foreach ($amount_detail as $detail) {
                        if (isset($detail['amount'])) {
                            $total_collected += floatval($detail['amount']);
                        }
                    }
                }
            }
        }
        
        $balance = $group['total_fee_amount'] - $total_collected;
        
        if ($balance < 0) {
            $problematic_groups[] = [
                'name' => $group['fee_group_name'],
                'students' => $group['student_count'],
                'total_fee' => $group['total_fee_amount'],
                'collected' => $total_collected,
                'balance' => $balance,
                'overpayment_ratio' => $total_collected / $group['total_fee_amount']
            ];
        }
        
        printf("%-35s | Students: %3d | Fee: %10.2f | Collected: %10.2f | Balance: %10.2f\n",
               substr($group['fee_group_name'], 0, 34),
               $group['student_count'],
               $group['total_fee_amount'],
               $total_collected,
               $balance);
    }
    
    echo "\n\n2. TOP PROBLEMATIC FEE GROUPS (Negative Balances):\n";
    echo str_repeat("-", 80) . "\n";
    
    // Sort by overpayment ratio (highest first)
    usort($problematic_groups, function($a, $b) {
        return $b['overpayment_ratio'] <=> $a['overpayment_ratio'];
    });
    
    foreach (array_slice($problematic_groups, 0, 10) as $group) {
        printf("%-35s | Overpaid: %6.1fx | Balance: %10.2f\n",
               substr($group['name'], 0, 34),
               $group['overpayment_ratio'],
               $group['balance']);
    }
    
    // 3. Analyze individual student overpayments
    echo "\n\n3. SAMPLE STUDENT OVERPAYMENTS:\n";
    echo str_repeat("-", 80) . "\n";
    
    $stmt = $pdo->prepare("
        SELECT 
            s.admission_no,
            CONCAT(s.firstname, ' ', IFNULL(s.lastname, '')) as student_name,
            fg.name as fee_group_name,
            sfm.amount as fee_amount,
            sfm.id as master_id
        FROM students s
        INNER JOIN student_session ss ON ss.student_id = s.id AND ss.session_id = 21
        INNER JOIN student_fees_master sfm ON sfm.student_session_id = ss.id
        INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
        WHERE fg.name IN ('2025-2026 JR-BIPC(BOOKS FEE)', '2025-2026 TC FEE', '2025-2026 JR-MPC(BOOKS FEE)')
        LIMIT 10
    ");
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($students as $student) {
        // Get payment details for this student
        $stmt2 = $pdo->prepare("
            SELECT amount_detail 
            FROM student_fees_deposite 
            WHERE student_fees_master_id = ?
        ");
        $stmt2->execute([$student['master_id']]);
        $deposits = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        
        $total_paid = 0;
        $payment_count = 0;
        
        foreach ($deposits as $deposit) {
            if (!empty($deposit['amount_detail'])) {
                $amount_detail = json_decode($deposit['amount_detail'], true);
                if (is_array($amount_detail)) {
                    foreach ($amount_detail as $detail) {
                        if (isset($detail['amount'])) {
                            $total_paid += floatval($detail['amount']);
                            $payment_count++;
                        }
                    }
                }
            }
        }
        
        $balance = $student['fee_amount'] - $total_paid;
        
        if ($balance < 0) {
            printf("%-12s | %-25s | %-30s | Fee: %8.2f | Paid: %8.2f | Balance: %8.2f | Payments: %d\n",
                   $student['admission_no'],
                   substr($student['student_name'], 0, 24),
                   substr($student['fee_group_name'], 0, 29),
                   $student['fee_amount'],
                   $total_paid,
                   $balance,
                   $payment_count);
        }
    }
    
    // 4. Summary and recommendations
    echo "\n\n4. ANALYSIS SUMMARY:\n";
    echo str_repeat("=", 80) . "\n";
    
    $total_overpayment = array_sum(array_column($problematic_groups, 'balance'));
    $total_overpayment = abs($total_overpayment);
    
    echo "Total Overpayment Amount: ₹" . number_format($total_overpayment, 2) . "\n";
    echo "Number of Fee Groups with Overpayments: " . count($problematic_groups) . "\n";
    
    echo "\nPOSSIBLE CAUSES:\n";
    echo "1. Students paying for multiple installments/terms in advance\n";
    echo "2. Payments being recorded against wrong fee groups\n";
    echo "3. Fee amounts not properly updated after payments\n";
    echo "4. Bulk payments being split incorrectly across fee groups\n";
    echo "5. Data entry errors in payment amounts\n";
    
    echo "\nRECOMMENDATIONS:\n";
    echo "1. Review payment entry process to ensure accuracy\n";
    echo "2. Implement validation to prevent overpayments\n";
    echo "3. Add alerts when payment exceeds fee amount\n";
    echo "4. Create a reconciliation report to identify discrepancies\n";
    echo "5. Consider implementing payment allocation logic\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
