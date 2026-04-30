<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Daysheet_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ========================================================================
    // ACCOUNTS
    // ========================================================================

    /**
     * Get all active accounts grouped into Cash and Bank categories.
     * Uses the accounttype table to determine the category:
     *   - accounttype.code containing 'cash' → Cash Account
     *   - Everything else → Bank/Other Account
     */
    public function getAllAccounts()
    {
        $this->db->select('addaccount.*, accounttype.type as account_type_name, accounttype.code as account_type_code, accountcategory.name as account_category_name');
        $this->db->from('addaccount');
        $this->db->join('accounttype', 'accounttype.id = addaccount.account_type', 'left');
        $this->db->join('accountcategory', 'accountcategory.id = addaccount.account_category', 'left');
        $this->db->order_by('addaccount.id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Classify accounts into Cash and Bank based on accounttype.code
     */
    public function getAccountsClassified()
    {
        $accounts = $this->getAllAccounts();
        $cash_accounts = array();
        $bank_accounts = array();

        foreach ($accounts as $acc) {
            $code = strtolower($acc['account_type_code'] ?? '');
            // If the account type code contains 'cash' or the account name contains 'cash'
            if (strpos($code, 'cash') !== false || strpos(strtolower($acc['name']), 'cash') !== false) {
                $cash_accounts[] = $acc;
            } else {
                $bank_accounts[] = $acc;
            }
        }

        return array(
            'cash' => $cash_accounts,
            'bank' => $bank_accounts
        );
    }

    // ========================================================================
    // COLLECTION GRID (Day-wise per account)
    // ========================================================================

    /**
     * Get day-wise fee collections per account for a date range.
     * Returns: array[ accountid ][ date ] = total_amount
     */
    public function getDayWiseCollections($from_date, $to_date)
    {
        $this->db->select('accountreceipts.accountid, accountreceipts.date, SUM(accountreceipts.amount) as total_amount');
        $this->db->from('accountreceipts');
        $this->db->where('accountreceipts.status', 'credit');
        $this->db->where('accountreceipts.type', 'fees');
        $this->db->where('accountreceipts.date >=', $from_date);
        $this->db->where('accountreceipts.date <=', $to_date);
        $this->db->group_by(array('accountreceipts.accountid', 'accountreceipts.date'));
        $this->db->order_by('accountreceipts.date', 'asc');
        $query = $this->db->get();
        $results = $query->result_array();

        $grid = array();
        foreach ($results as $row) {
            $grid[$row['accountid']][$row['date']] = floatval($row['total_amount']);
        }
        return $grid;
    }

    /**
     * Get all unique dates with collections in the range
     */
    public function getCollectionDates($from_date, $to_date)
    {
        $dates = array();
        $current = strtotime($from_date);
        $end = strtotime($to_date);
        while ($current <= $end) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }
        return $dates;
    }

    // ========================================================================
    // QUICK STATS
    // ========================================================================

    /**
     * Get total fee collection for a specific date range
     */
    public function getTotalCollection($from_date, $to_date)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from('accountreceipts');
        $this->db->where('status', 'credit');
        $this->db->where('type', 'fees');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $row = $this->db->get()->row_array();
        return floatval($row['total'] ?? 0);
    }

    /**
     * Get total expenses for a specific date range
     */
    public function getTotalExpenses($from_date, $to_date)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from('expenses');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $row = $this->db->get()->row_array();
        return floatval($row['total'] ?? 0);
    }

    /**
     * Get total income for a specific date range
     */
    public function getTotalIncome($from_date, $to_date)
    {
        $this->db->select('SUM(amount) as total');
        $this->db->from('income');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $row = $this->db->get()->row_array();
        return floatval($row['total'] ?? 0);
    }

    /**
     * Get transaction count for a date range
     */
    public function getTransactionCount($from_date, $to_date)
    {
        $this->db->select('COUNT(*) as cnt');
        $this->db->from('accountreceipts');
        $this->db->where('status', 'credit');
        $this->db->where('type', 'fees');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $row = $this->db->get()->row_array();
        return intval($row['cnt'] ?? 0);
    }

    // ========================================================================
    // ACCOUNT BALANCE CALCULATIONS
    // ========================================================================

    /**
     * Calculate opening balance for an account on a given date.
     * Opening balance = all credits before that date - all debits before that date.
     * This is fully calculated from historical transaction data.
     */
    public function getOpeningBalance($account_id, $date)
    {
        // Credits before this date
        $this->db->select('SUM(amount) as total');
        $this->db->from('accountreceipts');
        $this->db->where('accountid', $account_id);
        $this->db->where('status', 'credit');
        $this->db->where('date <', $date);
        $credit = $this->db->get()->row_array();
        $total_credit = floatval($credit['total'] ?? 0);

        // Debits before this date
        $this->db->select('SUM(amount) as total');
        $this->db->from('accountreceipts');
        $this->db->where('accountid', $account_id);
        $this->db->where('status', 'debit');
        $this->db->where('date <', $date);
        $debit = $this->db->get()->row_array();
        $total_debit = floatval($debit['total'] ?? 0);

        // Transfers IN before this date
        $this->db->select('SUM(amount) as total');
        $this->db->from('accounttranscations');
        $this->db->where('toaccountid', $account_id);
        $this->db->where('date <', $date);
        $transfer_in = $this->db->get()->row_array();
        $total_transfer_in = floatval($transfer_in['total'] ?? 0);

        // Transfers OUT before this date
        $this->db->select('SUM(amount) as total');
        $this->db->from('accounttranscations');
        $this->db->where('fromaccountid', $account_id);
        $this->db->where('date <', $date);
        $transfer_out = $this->db->get()->row_array();
        $total_transfer_out = floatval($transfer_out['total'] ?? 0);

        return $total_credit - $total_debit + $total_transfer_in - $total_transfer_out;
    }

    /**
     * Get account summary for the daysheet (for a date range)
     * Returns opening balance, collections, debits, transfers, closing balance
     */
    public function getAccountSummary($account_id, $from_date, $to_date)
    {
        $opening_balance = $this->getOpeningBalance($account_id, $from_date);

        // Credits (collections) in the date range
        $this->db->select('SUM(amount) as total');
        $this->db->from('accountreceipts');
        $this->db->where('accountid', $account_id);
        $this->db->where('status', 'credit');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $credit = $this->db->get()->row_array();
        $collections = floatval($credit['total'] ?? 0);

        // Debits (withdrawals/expenses) in the date range
        $this->db->select('SUM(amount) as total');
        $this->db->from('accountreceipts');
        $this->db->where('accountid', $account_id);
        $this->db->where('status', 'debit');
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $debit = $this->db->get()->row_array();
        $withdrawals = floatval($debit['total'] ?? 0);

        // Transfers IN
        $this->db->select('SUM(amount) as total');
        $this->db->from('accounttranscations');
        $this->db->where('toaccountid', $account_id);
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $tin = $this->db->get()->row_array();
        $transfers_in = floatval($tin['total'] ?? 0);

        // Transfers OUT
        $this->db->select('SUM(amount) as total');
        $this->db->from('accounttranscations');
        $this->db->where('fromaccountid', $account_id);
        $this->db->where('date >=', $from_date);
        $this->db->where('date <=', $to_date);
        $tout = $this->db->get()->row_array();
        $transfers_out = floatval($tout['total'] ?? 0);

        $closing_balance = $opening_balance + $collections - $withdrawals + $transfers_in - $transfers_out;

        return array(
            'opening_balance' => $opening_balance,
            'collections'     => $collections,
            'withdrawals'     => $withdrawals,
            'transfers_in'    => $transfers_in,
            'transfers_out'   => $transfers_out,
            'closing_balance' => $closing_balance
        );
    }

    // ========================================================================
    // DETAILED RECEIPTS
    // ========================================================================

    /**
     * Get detailed receipt-level data for a date range
     */
    public function getDetailedReceipts($from_date, $to_date, $account_id = null)
    {
        $this->db->select('
            ar.id, ar.receiptid, ar.date, ar.amount, ar.type, ar.status, ar.description,
            aa.name as account_name,
            sfd.amount_detail, sfd.fee_groups_feetype_id,
            sfm.id as fee_master_id,
            ss.student_id, ss.class_id, ss.section_id,
            s.firstname, s.lastname, s.admission_no,
            c.class, sec.section
        ');
        $this->db->from('accountreceipts ar');
        $this->db->join('addaccount aa', 'aa.id = ar.accountid', 'left');
        $this->db->join('student_fees_deposite sfd', 'sfd.id = SUBSTRING_INDEX(ar.receiptid, "/", 1)', 'left');
        $this->db->join('student_fees_master sfm', 'sfm.id = sfd.student_fees_master_id', 'left');
        $this->db->join('student_session ss', 'ss.id = sfm.student_session_id', 'left');
        $this->db->join('students s', 's.id = ss.student_id', 'left');
        $this->db->join('classes c', 'c.id = ss.class_id', 'left');
        $this->db->join('sections sec', 'sec.id = ss.section_id', 'left');
        $this->db->where('ar.status', 'credit');
        $this->db->where('ar.type', 'fees');
        $this->db->where('ar.date >=', $from_date);
        $this->db->where('ar.date <=', $to_date);

        if ($account_id) {
            $this->db->where('ar.accountid', $account_id);
        }

        $this->db->order_by('ar.date', 'desc');
        $this->db->order_by('ar.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================================================
    // CONSOLIDATED VIEWS
    // ========================================================================

    /**
     * Fee Type-wise consolidated totals for a date range
     */
    public function getFeeTypeConsolidated($from_date, $to_date)
    {
        // We'll aggregate from accountreceipts which has the type field
        // But we need to parse description or join with fee tables
        // Simpler approach: use student_fees_deposite amount_detail JSON
        $this->db->select('
            fgft.id as fgft_id,
            fg.name as fee_group_name,
            ft.type as fee_type_name,
            COUNT(DISTINCT ar.receiptid) as receipt_count,
            SUM(ar.amount) as total_amount
        ');
        $this->db->from('accountreceipts ar');
        $this->db->join('student_fees_deposite sfd', 'sfd.id = SUBSTRING_INDEX(ar.receiptid, "/", 1)', 'left');
        $this->db->join('fee_groups_feetype fgft', 'fgft.id = sfd.fee_groups_feetype_id', 'left');
        $this->db->join('fee_session_groups fsg', 'fsg.id = fgft.fee_session_group_id', 'left');
        $this->db->join('fee_groups fg', 'fg.id = fsg.fee_groups_id', 'left');
        $this->db->join('feetype ft', 'ft.id = fgft.feetype_id', 'left');
        $this->db->where('ar.status', 'credit');
        $this->db->where('ar.type', 'fees');
        $this->db->where('ar.date >=', $from_date);
        $this->db->where('ar.date <=', $to_date);
        $this->db->group_by(array('fgft.id', 'fg.name', 'ft.type'));
        $this->db->order_by('total_amount', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Account-wise consolidated totals for a date range
     */
    public function getAccountConsolidated($from_date, $to_date)
    {
        $this->db->select('
            ar.accountid,
            aa.name as account_name,
            SUM(CASE WHEN ar.status = "credit" THEN ar.amount ELSE 0 END) as total_credit,
            SUM(CASE WHEN ar.status = "debit" THEN ar.amount ELSE 0 END) as total_debit,
            COUNT(*) as transaction_count
        ');
        $this->db->from('accountreceipts ar');
        $this->db->join('addaccount aa', 'aa.id = ar.accountid', 'left');
        $this->db->where('ar.date >=', $from_date);
        $this->db->where('ar.date <=', $to_date);
        $this->db->group_by('ar.accountid');
        $this->db->order_by('total_credit', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================================================
    // MONTHLY TREND
    // ========================================================================

    /**
     * Get monthly collection trend, grouped by fee type if possible
     */
    public function getMonthlyTrend($from_date, $to_date)
    {
        $this->db->select("
            DATE_FORMAT(ar.date, '%Y-%m') as month_key,
            DATE_FORMAT(ar.date, '%b %Y') as month_label,
            SUM(ar.amount) as total_amount,
            COUNT(*) as transaction_count
        ");
        $this->db->from('accountreceipts ar');
        $this->db->where('ar.status', 'credit');
        $this->db->where('ar.type', 'fees');
        $this->db->where('ar.date >=', $from_date);
        $this->db->where('ar.date <=', $to_date);
        $this->db->group_by("DATE_FORMAT(ar.date, '%Y-%m')");
        $this->db->order_by('month_key', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================================================
    // EXPENSE DETAILS FOR DAYSHEET
    // ========================================================================

    /**
     * Get expenses grouped by head for a date range
     */
    public function getExpensesByHead($from_date, $to_date)
    {
        $this->db->select('eh.exp_category, SUM(e.amount) as total_amount, COUNT(*) as count');
        $this->db->from('expenses e');
        $this->db->join('expense_head eh', 'eh.id = e.exp_head_id', 'left');
        $this->db->where('e.date >=', $from_date);
        $this->db->where('e.date <=', $to_date);
        $this->db->group_by('eh.id');
        $this->db->order_by('total_amount', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get income grouped by head for a date range
     */
    public function getIncomeByHead($from_date, $to_date)
    {
        $this->db->select('ih.income_category, SUM(i.amount) as total_amount, COUNT(*) as count');
        $this->db->from('income i');
        $this->db->join('income_head ih', 'ih.id = i.income_head_id', 'left');
        $this->db->where('i.date >=', $from_date);
        $this->db->where('i.date <=', $to_date);
        $this->db->group_by('ih.id');
        $this->db->order_by('total_amount', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================================================
    // FINANCIAL YEAR
    // ========================================================================

    /**
     * Get active financial year
     */
    public function getActiveFinancialYear()
    {
        $this->db->select('*');
        $this->db->where('is_active', 1);
        $query = $this->db->get('financialyear');
        return $query->row_array();
    }
}
