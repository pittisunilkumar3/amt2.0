<?php
/**
 * Debug script to identify negative balance issues in fee group-wise collection
 * 
 * This script will:
 * 1. Check for fee groups with negative balances
 * 2. Analyze the payment records causing the issue
 * 3. Identify data inconsistencies
 */

// Load CodeIgniter
require_once('index.php');

// Get CI instance
$CI =& get_instance();
$CI->load->database();
$CI->load->model('feegroupwise_model');

echo "<h1>Fee Group-wise Collection Debug Report</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .negative { background-color: #ffcccc !important; font-weight: bold; }
    .warning { background-color: #fff3cd; }
    h2 { color: #333; margin-top: 30px; }
    .info { background-color: #d1ecf1; padding: 10px; margin: 10px 0; border-left: 4px solid #0c5460; }
</style>";

// Get current session
$current_session = $CI->setting_model->getCurrentSession();
echo "<div class='info'><strong>Current Session:</strong> $current_session</div>";

// Test 1: Get all fee groups with their collection data
echo "<h2>1. Fee Groups with Negative Balances</h2>";

$data = $CI->feegroupwise_model->getFeeGroupwiseCollection($current_session);

$negative_balance_groups = array();
foreach ($data as $row) {
    if ($row->balance_amount < 0) {
        $negative_balance_groups[] = $row;
    }
}

if (count($negative_balance_groups) > 0) {
    echo "<p style='color: red;'><strong>Found " . count($negative_balance_groups) . " fee group(s) with negative balances!</strong></p>";
    echo "<table>";
    echo "<tr><th>Fee Group</th><th>Total Amount</th><th>Amount Collected</th><th>Balance</th><th>Students</th><th>Issue</th></tr>";
    
    foreach ($negative_balance_groups as $group) {
        echo "<tr class='negative'>";
        echo "<td>" . $group->fee_group_name . "</td>";
        echo "<td>₹ " . number_format($group->total_amount, 2) . "</td>";
        echo "<td>₹ " . number_format($group->amount_collected, 2) . "</td>";
        echo "<td>₹ " . number_format($group->balance_amount, 2) . "</td>";
        echo "<td>" . $group->total_students . "</td>";
        echo "<td>" . ($group->data_issue ?? 'NEGATIVE_BALANCE') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Detailed analysis for each negative balance group
    foreach ($negative_balance_groups as $group) {
        echo "<h3>Detailed Analysis: " . $group->fee_group_name . "</h3>";
        
        // Check if it's a regular or additional fee group
        $is_regular = $CI->db->where('id', $group->fee_group_id)
                              ->where('is_system', 0)
                              ->get('fee_groups')
                              ->num_rows() > 0;
        
        $is_additional = $CI->db->where('id', $group->fee_group_id)
                                 ->where('is_system', 0)
                                 ->get('fee_groupsadding')
                                 ->num_rows() > 0;
        
        echo "<p><strong>Fee Group Type:</strong> " . ($is_regular ? 'Regular' : ($is_additional ? 'Additional (Other Fees)' : 'Unknown')) . "</p>";
        
        // Get student-level details
        $detailed_data = $CI->feegroupwise_model->getFeeGroupwiseDetailedData(
            $current_session,
            array(),
            array(),
            array($group->fee_group_id)
        );
        
        if (count($detailed_data) > 0) {
            echo "<table>";
            echo "<tr><th>Student</th><th>Admission No</th><th>Class</th><th>Total Amount</th><th>Collected</th><th>Balance</th><th>Status</th></tr>";
            
            foreach ($detailed_data as $student) {
                $row_class = $student->balance_amount < 0 ? 'negative' : '';
                echo "<tr class='$row_class'>";
                echo "<td>" . $student->student_name . "</td>";
                echo "<td>" . $student->admission_no . "</td>";
                echo "<td>" . $student->class_name . " - " . $student->section_name . "</td>";
                echo "<td>₹ " . number_format($student->total_amount, 2) . "</td>";
                echo "<td>₹ " . number_format($student->amount_collected, 2) . "</td>";
                echo "<td>₹ " . number_format($student->balance_amount, 2) . "</td>";
                echo "<td>" . $student->payment_status . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // Analyze payment records for students with negative balance
            foreach ($detailed_data as $student) {
                if ($student->balance_amount < 0) {
                    echo "<h4>Payment Records for " . $student->student_name . " (Admission: " . $student->admission_no . ")</h4>";
                    
                    // Get payment records
                    if ($is_regular) {
                        $payments = $CI->db->select('id, amount_detail, date')
                                          ->from('student_fees_deposite')
                                          ->where('student_fees_master_id', $student->student_fees_master_id)
                                          ->get()
                                          ->result();
                    } else {
                        $payments = $CI->db->select('id, amount_detail, date')
                                          ->from('student_fees_depositeadding')
                                          ->where('student_fees_master_id', $student->student_fees_master_id)
                                          ->get()
                                          ->result();
                    }
                    
                    if (count($payments) > 0) {
                        echo "<table>";
                        echo "<tr><th>Payment ID</th><th>Date</th><th>Amount Detail (JSON)</th><th>Parsed Amounts</th></tr>";
                        
                        foreach ($payments as $payment) {
                            echo "<tr>";
                            echo "<td>" . $payment->id . "</td>";
                            echo "<td>" . $payment->date . "</td>";
                            echo "<td><pre>" . htmlspecialchars($payment->amount_detail) . "</pre></td>";
                            
                            // Parse JSON
                            $amount_detail = json_decode($payment->amount_detail);
                            $parsed_amounts = array();
                            if (is_array($amount_detail) || is_object($amount_detail)) {
                                foreach ($amount_detail as $detail) {
                                    if (is_object($detail) && isset($detail->amount)) {
                                        $parsed_amounts[] = "₹" . $detail->amount . " (Fee Type: " . ($detail->fee_type_id ?? 'N/A') . ")";
                                    }
                                }
                            }
                            echo "<td>" . implode("<br>", $parsed_amounts) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                    } else {
                        echo "<p>No payment records found.</p>";
                    }
                }
            }
        }
    }
} else {
    echo "<p style='color: green;'><strong>No fee groups with negative balances found!</strong></p>";
}

// Test 2: Check for data quality issues
echo "<h2>2. Data Quality Issues</h2>";

$all_groups = $data;
$quality_issues = array();

foreach ($all_groups as $group) {
    if ($group->data_issue) {
        $quality_issues[] = $group;
    }
}

if (count($quality_issues) > 0) {
    echo "<table>";
    echo "<tr><th>Fee Group</th><th>Issue Type</th><th>Description</th><th>Total Amount</th><th>Collected</th></tr>";
    
    foreach ($quality_issues as $group) {
        echo "<tr class='warning'>";
        echo "<td>" . $group->fee_group_name . "</td>";
        echo "<td>" . $group->data_issue . "</td>";
        echo "<td>" . $group->data_issue_description . "</td>";
        echo "<td>₹ " . number_format($group->total_amount, 2) . "</td>";
        echo "<td>₹ " . number_format($group->amount_collected, 2) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: green;'>No data quality issues found.</p>";
}

// Test 3: Summary statistics
echo "<h2>3. Summary Statistics</h2>";
echo "<table>";
echo "<tr><th>Metric</th><th>Value</th></tr>";
echo "<tr><td>Total Fee Groups</td><td>" . count($all_groups) . "</td></tr>";
echo "<tr><td>Fee Groups with Negative Balance</td><td>" . count($negative_balance_groups) . "</td></tr>";
echo "<tr><td>Fee Groups with Data Issues</td><td>" . count($quality_issues) . "</td></tr>";

$total_amount = 0;
$total_collected = 0;
foreach ($all_groups as $group) {
    $total_amount += $group->total_amount;
    $total_collected += $group->amount_collected;
}

echo "<tr><td>Total Amount (All Groups)</td><td>₹ " . number_format($total_amount, 2) . "</td></tr>";
echo "<tr><td>Total Collected (All Groups)</td><td>₹ " . number_format($total_collected, 2) . "</td></tr>";
echo "<tr><td>Total Balance (All Groups)</td><td>₹ " . number_format($total_amount - $total_collected, 2) . "</td></tr>";
echo "</table>";

echo "<h2>4. Recommendations</h2>";
echo "<div class='info'>";
if (count($negative_balance_groups) > 0) {
    echo "<p><strong>Action Required:</strong></p>";
    echo "<ul>";
    echo "<li>Review the payment records for students with negative balances</li>";
    echo "<li>Check if payments are being recorded against the correct fee groups</li>";
    echo "<li>Verify the JSON structure in amount_detail column</li>";
    echo "<li>Ensure fee_type_id in payment records matches the fee group</li>";
    echo "</ul>";
} else {
    echo "<p><strong>All Clear:</strong> No negative balances detected in the current session.</p>";
}
echo "</div>";

echo "<p><em>Debug report generated on " . date('Y-m-d H:i:s') . "</em></p>";

