<?php
/**
 * Overpayment Validation Enhancement
 * Add this validation logic to the payment entry system
 */

class PaymentValidation {
    
    /**
     * Validate payment amount against fee amount
     */
    public function validatePaymentAmount($student_fees_master_id, $new_payment_amount) {
        // Get current fee amount and existing payments
        $fee_info = $this->getFeeInfo($student_fees_master_id);
        $existing_payments = $this->getExistingPayments($student_fees_master_id);
        
        $total_after_payment = $existing_payments + $new_payment_amount;
        $overpayment_threshold = $fee_info['amount'] * 1.1; // Allow 10% overpayment
        
        $validation_result = [
            'is_valid' => true,
            'warnings' => [],
            'errors' => [],
            'fee_amount' => $fee_info['amount'],
            'existing_payments' => $existing_payments,
            'new_payment' => $new_payment_amount,
            'total_after_payment' => $total_after_payment,
            'remaining_balance' => $fee_info['amount'] - $total_after_payment
        ];
        
        // Check for overpayment
        if ($total_after_payment > $overpayment_threshold) {
            $validation_result['is_valid'] = false;
            $validation_result['errors'][] = "Payment would result in overpayment. Fee: ₹{$fee_info['amount']}, Total after payment: ₹{$total_after_payment}";
        }
        
        // Check for large payment amounts (potential data entry error)
        if ($new_payment_amount > $fee_info['amount'] * 2) {
            $validation_result['warnings'][] = "Large payment amount detected. Please verify: ₹{$new_payment_amount} for fee of ₹{$fee_info['amount']}";
        }
        
        // Check if payment exceeds remaining balance significantly
        $remaining_balance = $fee_info['amount'] - $existing_payments;
        if ($new_payment_amount > $remaining_balance * 1.5 && $remaining_balance > 0) {
            $validation_result['warnings'][] = "Payment exceeds remaining balance by 50%. Remaining: ₹{$remaining_balance}, Payment: ₹{$new_payment_amount}";
        }
        
        return $validation_result;
    }
    
    /**
     * Get fee information for a student
     */
    private function getFeeInfo($student_fees_master_id) {
        $this->db->select('sfm.amount, fg.name as fee_group_name, s.admission_no, CONCAT(s.firstname, " ", s.lastname) as student_name');
        $this->db->from('student_fees_master sfm');
        $this->db->join('fee_groups fg', 'fg.id = sfm.fee_session_group_id');
        $this->db->join('student_session ss', 'ss.id = sfm.student_session_id');
        $this->db->join('students s', 's.id = ss.student_id');
        $this->db->where('sfm.id', $student_fees_master_id);
        
        $query = $this->db->get();
        return $query->row_array();
    }
    
    /**
     * Calculate existing payments for a student
     */
    private function getExistingPayments($student_fees_master_id) {
        $this->db->select('amount_detail');
        $this->db->from('student_fees_deposite');
        $this->db->where('student_fees_master_id', $student_fees_master_id);
        
        $query = $this->db->get();
        $total_paid = 0;
        
        foreach ($query->result() as $row) {
            if (!empty($row->amount_detail)) {
                $amount_detail = json_decode($row->amount_detail);
                if (is_object($amount_detail) || is_array($amount_detail)) {
                    foreach ($amount_detail as $detail) {
                        if (is_object($detail) && isset($detail->amount)) {
                            $total_paid += floatval($detail->amount);
                        }
                    }
                }
            }
        }
        
        return $total_paid;
    }
    
    /**
     * Generate overpayment report
     */
    public function generateOverpaymentReport($session_id = null) {
        if (!$session_id) {
            $session_id = $this->current_session;
        }
        
        $sql = "
            SELECT 
                s.admission_no,
                CONCAT(s.firstname, ' ', s.lastname) as student_name,
                fg.name as fee_group_name,
                sfm.amount as fee_amount,
                sfm.id as master_id
            FROM students s
            INNER JOIN student_session ss ON ss.student_id = s.id AND ss.session_id = ?
            INNER JOIN student_fees_master sfm ON sfm.student_session_id = ss.id
            INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
            WHERE fg.is_system = 0
            ORDER BY fg.name, s.admission_no
        ";
        
        $query = $this->db->query($sql, [$session_id]);
        $students = $query->result_array();
        
        $overpayments = [];
        
        foreach ($students as $student) {
            $total_paid = $this->getExistingPayments($student['master_id']);
            $balance = $student['fee_amount'] - $total_paid;
            
            if ($balance < 0) {
                $overpayments[] = [
                    'admission_no' => $student['admission_no'],
                    'student_name' => $student['student_name'],
                    'fee_group_name' => $student['fee_group_name'],
                    'fee_amount' => $student['fee_amount'],
                    'amount_paid' => $total_paid,
                    'overpayment' => abs($balance),
                    'overpayment_ratio' => $total_paid / $student['fee_amount']
                ];
            }
        }
        
        // Sort by overpayment amount (highest first)
        usort($overpayments, function($a, $b) {
            return $b['overpayment'] <=> $a['overpayment'];
        });
        
        return $overpayments;
    }
}

/**
 * JavaScript validation for frontend
 */
?>
<script>
function validatePaymentAmount(feeAmount, existingPayments, newPayment) {
    const totalAfterPayment = existingPayments + newPayment;
    const overpaymentThreshold = feeAmount * 1.1; // 10% tolerance
    
    const validation = {
        isValid: true,
        warnings: [],
        errors: []
    };
    
    // Check for overpayment
    if (totalAfterPayment > overpaymentThreshold) {
        validation.isValid = false;
        validation.errors.push(`Payment would result in overpayment. Fee: ₹${feeAmount}, Total after payment: ₹${totalAfterPayment}`);
    }
    
    // Check for large payment amounts
    if (newPayment > feeAmount * 2) {
        validation.warnings.push(`Large payment amount detected. Please verify: ₹${newPayment} for fee of ₹${feeAmount}`);
    }
    
    return validation;
}

// Add to payment form submission
function onPaymentSubmit() {
    const feeAmount = parseFloat(document.getElementById('fee_amount').value);
    const existingPayments = parseFloat(document.getElementById('existing_payments').value);
    const newPayment = parseFloat(document.getElementById('payment_amount').value);
    
    const validation = validatePaymentAmount(feeAmount, existingPayments, newPayment);
    
    if (!validation.isValid) {
        alert('Payment Error:\n' + validation.errors.join('\n'));
        return false;
    }
    
    if (validation.warnings.length > 0) {
        if (!confirm('Payment Warning:\n' + validation.warnings.join('\n') + '\n\nDo you want to continue?')) {
            return false;
        }
    }
    
    return true;
}
</script>
