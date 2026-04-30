<?php

class Payroll_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date    = $this->setting_model->getDateYmd();
    }

    public function searchEmployee($month, $year, $emp_name, $role)
    {
        $condition = "";
        if ($this->session->has_userdata('admin')) {
            $getStaffRole     = $this->customlib->getStaffRole();
            $staffrole   =   json_decode($getStaffRole);       
            $superadmin_visible = $this->customlib->superadmin_visible(); 
            if ($superadmin_visible == 'disabled' && $staffrole->id != 7) {                 
                $condition = " and roles.id != 7";
            } 
        }
        
        $date_month = date("m", strtotime($year));
        if (!empty($role) && !empty($emp_name)) {

            $query = $this->db->query("select staff_payslip.status,
        IFNULL(staff_payslip.id, 0) as payslip_id ,staff.* ,roles.name as user_type ,staff_designation.designation as designation,department.department_name as department from staff left join staff_payslip on staff.id = staff_payslip.staff_id and month = " . $this->db->escape($month) . " and year = " . $this->db->escape($year) . " left join department on department.id = staff.department left join staff_designation on staff_designation.id = staff.designation left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id where roles.name = " . $this->db->escape($role) . " and name = " . $this->db->escape($emp_name) . " and staff.is_active = 1 $condition");
        } else if (!empty($role)) {

            $query = $this->db->query("select staff_payslip.status,
        IFNULL(staff_payslip.id, 0) as payslip_id ,staff.*,staff_designation.designation as designation,department.department_name as department ,roles.name as user_type from staff left join staff_payslip on staff.id = staff_payslip.staff_id and month = " . $this->db->escape($month) . " and year = " . $this->db->escape($year) . " left join department on department.id = staff.department left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_designation on staff_designation.id = staff.designation where roles.name = " . $this->db->escape($role) . " and staff.is_active = 1 $condition");
        } else {

            $query = $this->db->query("select staff_payslip.status,
        IFNULL(staff_payslip.id, 0) as payslip_id ,staff.* ,roles.name as user_type ,staff_designation.designation as designation,department.department_name as department  from staff left join staff_payslip on staff.id = staff_payslip.staff_id and month = " . $this->db->escape($month) . " and year = " . $this->db->escape($year) . " left join department on department.id = staff.department left join staff_roles on staff_roles.staff_id = staff.id left join roles on staff_roles.role_id = roles.id left join staff_designation on staff_designation.id = staff.designation where staff.is_active = 1 $condition");
        }

        return $query->result_array();
    }

     public function update_allowance($insert_data, $update_data, $delete_data,$payslipid,$type)
    {
        $this->db->trans_begin();
        
        if (!empty($delete_data)) {
            $this->db->where('cal_type', $type);
            $this->db->where('payslip_id', $payslipid);
            $this->db->where_not_in('id', $delete_data);
            $this->db->delete('payslip_allowance');
        }

        if (!empty($insert_data)) {
            $this->db->insert_batch('payslip_allowance', $insert_data);
        }
        if (!empty($update_data)) {
            $this->db->update_batch('payslip_allowance', $update_data, 'id');
        }
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

   public function createPayslip($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            $this->db->update('staff_payslip', $data);
            $message = UPDATE_RECORD_CONSTANT . " On Staff Payslip id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================
            $this->db->trans_complete(); # Completing transaction
            /* Optional */
            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                return $record_id;
            }
        } else {
            $this->db->insert('staff_payslip', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On Staff Payslip id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);
            //======================Code End==============================
            $this->db->trans_complete(); # Completing transaction
            /* Optional */
            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $insert_id;
        }
    }

    public function checkPayslip($month, $year, $staff_id)
    {

        $query = $this->db->where(array('month' => $month, 'year' => $year, 'staff_id' => $staff_id))->get("staff_payslip");

        if ($query->num_rows() > 0) {
            return false;
        } else {

            return true;
        }
    }

    public function add_allowance($data)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('payslip_allowance', $data);
            $message   = UPDATE_RECORD_CONSTANT . " On payslip allowance id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
        } else {
            $this->db->insert('payslip_allowance', $data);
            $id = $this->db->insert_id();

            $message   = INSERT_RECORD_CONSTANT . " On payslip allowance id " . $id;
            $action    = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    } 

    public function searchPaylist($name, $month, $year)
    {
        $query = $this->db->select('staff.*,staff_designation.designation as desg,department.department_name as department')->where(array('staff.name' => $name, 'staff_payslip.month' => $month, 'staff_payslip.year' => $year))->join("staff_payslip", "staff.id = staff_payslip.staff_id")->join("staff_designation", "staff.designation = staff_designation.id")->join("department", "staff.department = department.id")->get("staff");

        return $query->result_array();
    }

    public function count_attendance($month, $year, $staff_id, $attendance_type = 1)
    {
        $date_month = date("m", strtotime($month));
        $query      = $this->db->select('count(*) as att')->where(array('staff_id' => $staff_id, 'month(date)' => $month, 'year(date)' => $year, 'staff_attendance_type_id' => $attendance_type))->get("staff_attendance");
        return $query->result_array();
    }

    public function count_attendance_obj($month, $year, $staff_id, $attendance_type = 1)
    {
        $query = $this->db->select('count(*) as attendence')->where(array('staff_id' => $staff_id, 'month(date)' => $month, 'year(date)' => $year, 'staff_attendance_type_id' => $attendance_type))->get("staff_attendance");

        return $query->row()->attendence;
    }

    public function updatePaymentStatus($status, $id)
    {
        $data = array('status' => $status);
        $this->db->where("id", $id)->update("staff_payslip", $data);
    }

    public function searchEmployeeById($id)
    {
        $query = $this->db->select('staff.*,roles.name as user_type ,staff_designation.designation,department.department_name as department')->join("staff_designation", "staff_designation.id = staff.designation", "left")->join("department", "department.id = staff.department", "left")->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->where("staff.id", $id)->get("staff");

        return $query->row_array();
    }

    public function searchPayment($id, $month, $year)
    {
        $query = $this->db->select('staff.name,staff.surname,staff.employee_id,staff.basic_salary,staff_payslip.*')->where(array('staff_payslip.month' => $month, 'staff_payslip.year' => $year, 'staff_payslip.staff_id' => $id))->join("staff_payslip", "staff.id = staff_payslip.staff_id")->get("staff");
        return $query->row_array();
    }

    public function paymentSuccess($data, $payslipid)
    {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where("id", $payslipid)->update("staff_payslip", $data);
        $message   = UPDATE_RECORD_CONSTANT . " On staff payslip id " . $payslipid;
        $action    = "Update";
        $record_id = $payslipid;
        $this->log($message, $record_id, $action);
        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function getPayslip($id)
    {
        $query = $this->db->select("staff.name,staff.surname,department.department_name as department,staff_designation.designation,staff.employee_id,staff_payslip.*")->join("staff", "staff.id = staff_payslip.staff_id")->join("staff_designation", "staff.designation = staff_designation.id", "left")->join("department", "staff.department = department.id", "left")->where("staff_payslip.id", $id)->get("staff_payslip");

        return $query->row_array();
    }

    public function getAllowance($id, $type = null)
    {
        if (!empty($type)) {

            $query = $this->db->select("id,allowance_type,amount,cal_type")->where(array('payslip_id' => $id, 'cal_type' => $type))->get("payslip_allowance");
        } else {

            $query = $this->db->select("id,allowance_type,amount,cal_type")->where("payslip_id", $id)->get("payslip_allowance");
        }

        return $query->result_array();
    }     

    public function getSalaryDetails($id)
    {
        $query = $this->db->select("sum(net_salary) as net_salary, sum(total_allowance) as earnings, sum(total_deduction) as deduction, sum(basic) as basic_salary, sum(tax) as tax")->where(array('staff_id' => $id, 'status' => 'paid'))->get("staff_payslip");
        return $query->row_array();
    }

    public function getpayrollReport($month, $year, $role)
    {
        if ($this->session->has_userdata('admin')) {
            $getStaffRole     = $this->customlib->getStaffRole();
            $staffrole   =   json_decode($getStaffRole);  
            $superadmin_visible = $this->customlib->superadmin_visible(); 
            if ($superadmin_visible == 'disabled' && $staffrole->id != 7) {
                $this->db->where("roles.id !=", 7);                 
            } 
        }
        
        if ($role == "select" && $month != "") {
            $data = array('staff_payslip.month' => $month, 'staff_payslip.year' => $year, 'staff_payslip.status' => 'paid');
        } else if ($role == "select" && $month == "") {

            $data = array('staff_payslip.year' => $year, 'staff_payslip.status' => 'paid');
        } else if ($role != "select" && $month == "") {

            $data = array('staff_payslip.year' => $year, 'roles.name' => $role, 'staff_payslip.status' => 'paid');
        } else {

            $data = array('staff_payslip.month' => $month, 'staff_payslip.year' => $year, 'roles.name' => $role, 'staff_payslip.status' => 'paid');
        }
        $data['staff.is_active'] = 1;

        $query = $this->db->select('staff.id,staff.employee_id,staff.name,roles.name as user_type,staff.surname,staff_designation.designation,department.department_name as department,staff_payslip.*')->join("staff_payslip", "staff_payslip.staff_id = staff.id", "inner")->join("staff_designation", "staff.designation = staff_designation.id", "left")->join("department", "staff.department = department.id", "left")->join("staff_roles", "staff_roles.staff_id = staff.id", "left")->join("roles", "staff_roles.role_id = roles.id", "left")->where($data)->get("staff");

        return $query->result_array();
    }

    public function deletePayslip($payslipid)
    {
        $this->db->where("id", $payslipid)->delete("staff_payslip");
        $this->db->where("payslip_id", $payslipid)->delete("payslip_allowance");
    }

    public function revertPayslipStatus($payslipid)
    {
        $data = array('status' => "generated");
        $this->db->where("id", $payslipid)->update("staff_payslip", $data);
    }

    public function payrollYearCount()
    {
        $query = $this->db->select("distinct(year) as year")->get("staff_payslip");
        return $query->result_array();
    }

    public function getbetweenpayrollReport($start_date, $end_date)
    {      
        
        $condition = "date_format(staff_payslip.payment_date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";       
       
        $this->db->select('staff.id,staff.employee_id,staff.name,roles.name as user_type,staff.surname,staff_designation.designation,department.department_name as department,staff_payslip.*');
        $this->db->join("staff_payslip", "staff_payslip.staff_id = staff.id", "inner");
        $this->db->join("staff_designation", "staff.designation = staff_designation.id", "left");
        $this->db->join("department", "staff.department = department.id", "left");
        $this->db->join("staff_roles", "staff_roles.staff_id = staff.id", "left");
        $this->db->join("roles", "staff_roles.role_id = roles.id", "left");        
        $this->db->where($condition); 
        if ($this->session->has_userdata('admin')) {
            $getStaffRole     = $this->customlib->getStaffRole();
            $staffrole   =   json_decode($getStaffRole);       
            
            $superadmin_rest = $this->customlib->superadmin_visible(); 
            if ($superadmin_rest == 'disabled' && $staffrole->id != 7) {
                $this->db->where("roles.id !=", 7)  ;          
            } 
        }
        
        $query = $this->db->get("staff");         
        return $query->result_array(); 
    }

    // ============================================
    // AUTO PAYROLL GENERATION METHODS
    // ============================================

    /**
     * Get payroll auto-generation settings
     */
    public function getPayrollSettings()
    {
        if (!$this->db->table_exists('payroll_settings')) {
            return null;
        }
        $query = $this->db->get('payroll_settings');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return null;
    }

    /**
     * Save payroll auto-generation settings
     */
    public function savePayrollSettings($data)
    {
        $this->db->where('id', 1);
        $this->db->update('payroll_settings', $data);
        return $this->db->affected_rows();
    }

    /**
     * Get staff with salary details (including salary_type and hourly_rate)
     */
    public function getActiveStaffForPayroll()
    {
        $this->db->select('staff.id, staff.name, staff.surname, staff.employee_id, staff.basic_salary, 
                          staff.salary_type, staff.hourly_rate, staff.is_active');
        $this->db->from('staff');
        $this->db->where('is_active', 1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Convert month name (e.g. 'March') to numeric month (e.g. 3)
     * Handles both full names and numeric strings
     */
    private function monthToNumeric($month)
    {
        // If already numeric
        if (is_numeric($month)) {
            return (int) $month;
        }
        // Try date parsing for month names
        $timestamp = strtotime("1 $month");
        if ($timestamp === false) {
            return 0;
        }
        return (int) date('m', $timestamp);
    }

    /**
     * Calculate working days in a month
     * Returns array: ['count' => int, 'dates' => array of date strings]
     * Supports optional cutoff date to limit the period
     */
    public function calculateWorkingDays($month, $year, $method = null, $cutoffDay = 0)
    {
        $settings = $this->getPayrollSettings();
        if ($method === null && !empty($settings)) {
            $method = $settings['working_days_method'];
        }
        if ($method === null) {
            $method = 'exclude_sundays';
        }

        // Determine cutoff date for the payroll period
        $numericMonth = $this->monthToNumeric($month);
        $numericYear = (int) $year;
        $daysInMonth = (int) cal_days_in_month(CAL_GREGORIAN, $numericMonth, $numericYear);
        $endDate = $daysInMonth;

        if ($cutoffDay > 0 && $cutoffDay <= $daysInMonth) {
            $endDate = $cutoffDay;
        }

        $workingDays = array();

        for ($d = 1; $d <= $endDate; $d++) {
            $date = sprintf('%04d-%02d-%02d', $numericYear, $numericMonth, $d);
            $dow = (int) date('N', strtotime($date)); // 1=Mon ... 7=Sun

            if ($method == 'exclude_sundays' && $dow == 7) {
                continue;
            }
            if ($method == 'exclude_sundays_saturdays' && ($dow == 7 || $dow == 6)) {
                continue;
            }

            $workingDays[] = $date;
        }

        return array('count' => count($workingDays), 'dates' => $workingDays);
    }

    /**
     * Get attendance breakdown for a staff member for a specific month
     * Returns: ['present' => N, 'late' => N, 'absent' => N, 'half_day' => N, 'holiday' => N, 'total_hours_worked' => N]
     * For each working day with no attendance record, counts as absent
     * Supports cutoff date to limit the period
     */
    public function getAttendanceDaysByType($staff_id, $month, $year, $workingDaysList = null, $cutoffDay = 0)
    {
        $numericMonth = $this->monthToNumeric($month);
        $numericYear = (int) $year;

        if ($workingDaysList === null) {
            $wd = $this->calculateWorkingDays($numericMonth, $numericYear, null, $cutoffDay);
            $workingDaysList = $wd['dates'];
        }

        // Build date range for attendance query
        if (!empty($workingDaysList)) {
            $minDate = min($workingDaysList);
            $maxDate = max($workingDaysList);
        } else {
            $minDate = sprintf('%04d-%02d-01', $numericYear, $numericMonth);
            $maxDate = $minDate;
        }

        // Get all attendance records for this staff for this period
        $this->db->select('date, staff_attendance_type_id, check_in_time, check_out_time, created_at');
        $this->db->from('staff_attendance');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('date >=', $minDate);
        $this->db->where('date <=', $maxDate);
        $query = $this->db->get();
        $records = $query->result_array();

        // Build map: date => worst attendance type
        // Severity: Absent(3)=4, HalfDay(4)=3, Late(2)=2, Present(1)=1, Holiday(5)=0
        $severity = array(3 => 4, 4 => 3, 2 => 2, 1 => 1, 5 => 0);
        $dateTypes = array();

        foreach ($records as $r) {
            $d = $r['date'];
            $type = (int) $r['staff_attendance_type_id'];
            $s = isset($severity[$type]) ? $severity[$type] : 0;

            if (!isset($dateTypes[$d]) || $s > $dateTypes[$d]['severity']) {
                $dateTypes[$d] = array('type' => $type, 'severity' => $s);
            }
        }

        // Calculate total hours worked from biometric check-in/check-out times
        $totalHoursWorked = $this->calculateTotalHoursWorked($staff_id, $minDate, $maxDate, $records);

        // For each working day, determine attendance status
        $counts = array('present' => 0, 'late' => 0, 'absent' => 0, 'half_day' => 0, 'holiday' => 0, 'total_hours_worked' => 0);

        foreach ($workingDaysList as $wdate) {
            if (isset($dateTypes[$wdate])) {
                $type = $dateTypes[$wdate]['type'];
            } else {
                $type = 3; // No record = Absent
            }

            switch ($type) {
                case 1: $counts['present']++; break;
                case 2: $counts['late']++; break;
                case 3: $counts['absent']++; break;
                case 4: $counts['half_day']++; break;
                case 5: $counts['holiday']++; break;
            }
        }

        $counts['total_hours_worked'] = $totalHoursWorked;
        $counts['records'] = $records;

        return $counts;
    }

    /**
     * Calculate total hours worked by a staff member between two dates
     * Uses first check-in to last check-out for each day
     */
    private function calculateTotalHoursWorked($staff_id, $startDate, $endDate, $records = null)
    {
        if ($records === null) {
            $this->db->select('date, check_in_time, check_out_time, created_at');
            $this->db->from('staff_attendance');
            $this->db->where('staff_id', $staff_id);
            $this->db->where('date >=', $startDate);
            $this->db->where('date <=', $endDate);
            $query = $this->db->get();
            $records = $query->result_array();
        }

        if (empty($records)) {
            return 0;
        }

        // Group records by date
        $byDate = array();
        foreach ($records as $r) {
            $d = $r['date'];
            if (!isset($byDate[$d])) {
                $byDate[$d] = array();
            }
            $byDate[$d][] = $r;
        }

        $totalSeconds = 0;

        foreach ($byDate as $date => $dayRecords) {
            // Find the earliest punch-in and latest punch-out for this day
            $earliestTime = null;
            $latestTime = null;

            foreach ($dayRecords as $rec) {
                // Use created_at as punch timestamp (biometric records set this)
                $punchTime = !empty($rec['created_at']) ? $rec['created_at'] : null;
                
                if ($punchTime) {
                    if ($earliestTime === null || $punchTime < $earliestTime) {
                        $earliestTime = $punchTime;
                    }
                    if ($latestTime === null || $punchTime > $latestTime) {
                        $latestTime = $punchTime;
                    }
                }
            }

            if ($earliestTime !== null && $latestTime !== null && $earliestTime !== $latestTime) {
                $duration = strtotime($latestTime) - strtotime($earliestTime);
                if ($duration > 0) {
                    $totalSeconds += $duration;
                }
            }
        }

        return round($totalSeconds / 3600, 2); // Convert to hours
    }

    /**
     * Calculate deduction amount for given days
     */
    private function calculateDeduction($days, $type, $value, $perDaySalary)
    {
        if ($days <= 0) {
            return 0;
        }
        if ($type == 'per_day') {
            return $days * $perDaySalary * $value;
        } else {
            return $days * $value;
        }
    }

    /**
     * Calculate short-hour deduction (when staff worked less than required hours in a day)
     * @param array $records - staff_attendance records for the period
     * @param float $requiredHours - required hours per day from settings
     * @param string $deductionType - 'per_hour', 'fixed', or 'disabled'
     * @param float $deductionValue - amount per hour or fixed amount
     * @param float $threshold - minimum shortage to trigger deduction (in hours)
     * @param float $perDaySalary - per day salary (for per_hour calculation base)
     * @return array ('short_days' => count, 'short_hours_total' => total, 'deduction' => amount)
     */
    public function calculateShortHourDeduction($records, $requiredHours, $deductionType, $deductionValue, $threshold, $perDaySalary)
    {
        $result = array('short_days' => 0, 'short_hours_total' => 0, 'deduction' => 0);

        if ($deductionType === 'disabled' || $requiredHours <= 0 || empty($records)) {
            return $result;
        }

        // Group records by date and calculate hours per day
        $byDate = array();
        foreach ($records as $r) {
            $d = $r['date'];
            if (!isset($byDate[$d])) {
                $byDate[$d] = array('earliest' => null, 'latest' => null);
            }
            $punchTime = !empty($r['created_at']) ? $r['created_at'] : null;
            if ($punchTime) {
                if ($byDate[$d]['earliest'] === null || $punchTime < $byDate[$d]['earliest']) {
                    $byDate[$d]['earliest'] = $punchTime;
                }
                if ($byDate[$d]['latest'] === null || $punchTime > $byDate[$d]['latest']) {
                    $byDate[$d]['latest'] = $punchTime;
                }
            }
        }

        foreach ($byDate as $date => $times) {
            if ($times['earliest'] !== null && $times['latest'] !== null && $times['earliest'] !== $times['latest']) {
                $workedHours = (strtotime($times['latest']) - strtotime($times['earliest'])) / 3600;
            } else {
                $workedHours = 0;
            }

            $shortage = round($requiredHours - $workedHours, 2);

            if ($shortage >= $threshold && $shortage > 0) {
                $result['short_days']++;
                $result['short_hours_total'] += $shortage;

                if ($deductionType === 'per_hour') {
                    $result['deduction'] += $shortage * $deductionValue;
                } else { // fixed
                    $result['deduction'] += $deductionValue;
                }
            }
        }

        $result['short_hours_total'] = round($result['short_hours_total'], 2);
        $result['deduction'] = round($result['deduction'], 2);
        return $result;
    }

    /**
     * Get daily hours breakdown for a staff member in a date range
     * Returns array of {date, hours_worked, required_hours, shortage, status}
     */
    public function getDailyHoursBreakdown($staff_id, $startDate, $endDate, $requiredHours = 8)
    {
        $this->db->select('date, check_in_time, check_out_time, created_at, staff_attendance_type_id');
        $this->db->from('staff_attendance');
        $this->db->where('staff_id', $staff_id);
        $this->db->where('date >=', $startDate);
        $this->db->where('date <=', $endDate);
        $this->db->order_by('date', 'ASC');
        $query = $this->db->get();
        $records = $query->result_array();

        // Group by date
        $byDate = array();
        foreach ($records as $r) {
            $d = $r['date'];
            if (!isset($byDate[$d])) {
                $byDate[$d] = array('earliest' => null, 'latest' => null, 'type' => $r['staff_attendance_type_id']);
            }
            $punchTime = !empty($r['created_at']) ? $r['created_at'] : null;
            if ($punchTime) {
                if ($byDate[$d]['earliest'] === null || $punchTime < $byDate[$d]['earliest']) {
                    $byDate[$d]['earliest'] = $punchTime;
                }
                if ($byDate[$d]['latest'] === null || $punchTime > $byDate[$d]['latest']) {
                    $byDate[$d]['latest'] = $punchTime;
                }
            }
            // Track worst attendance type
            $severity = array(3 => 4, 4 => 3, 2 => 2, 1 => 1, 5 => 0);
            $s = isset($severity[(int)$r['staff_attendance_type_id']]) ? $severity[(int)$r['staff_attendance_type_id']] : 0;
            if (!isset($byDate[$d]['severity']) || $s > $byDate[$d]['severity']) {
                $byDate[$d]['severity'] = $s;
                $byDate[$d]['type'] = (int)$r['staff_attendance_type_id'];
            }
        }

        $typeNames = array(1 => 'Present', 2 => 'Late', 3 => 'Absent', 4 => 'Half Day', 5 => 'Holiday');
        $breakdown = array();

        foreach ($byDate as $date => $info) {
            if ($info['earliest'] !== null && $info['latest'] !== null && $info['earliest'] !== $info['latest']) {
                $hoursWorked = round((strtotime($info['latest']) - strtotime($info['earliest'])) / 3600, 2);
            } else {
                $hoursWorked = 0;
            }

            $shortage = round($requiredHours - $hoursWorked, 2);
            $status = 'ok';
            if ($hoursWorked < $requiredHours && $hoursWorked > 0) {
                $status = 'short';
            } elseif ($hoursWorked == 0) {
                $status = 'absent';
            }

            $breakdown[] = array(
                'date'           => $date,
                'hours_worked'   => $hoursWorked,
                'required_hours' => $requiredHours,
                'shortage'       => max(0, $shortage),
                'status'         => $status,
                'attendance_type' => isset($typeNames[$info['type']]) ? $typeNames[$info['type']] : 'Unknown',
                'punch_in'       => $info['earliest'],
                'punch_out'      => $info['latest'],
            );
        }

        return $breakdown;
    }

    /**
     * Calculate auto salary for a single staff member
     * Returns calculation array or null if basic_salary <= 0
     * Supports both monthly and hourly salary types
     * Supports payroll cutoff date
     */
    public function calculateAutoSalary($staff, $settings, $month, $year)
    {
        $basic = (float) $staff['basic_salary'];
        $salaryType = isset($staff['salary_type']) ? $staff['salary_type'] : 'monthly';
        $hourlyRate = isset($staff['hourly_rate']) ? (float) $staff['hourly_rate'] : 0;

        // Get cutoff day from settings
        $cutoffDay = isset($settings['payroll_cutoff_day']) ? (int) $settings['payroll_cutoff_day'] : 0;

        // Parse month correctly
        $numericMonth = $this->monthToNumeric($month);
        $numericYear = (int) $year;

        // ============ HOURLY STAFF ============
        if ($salaryType === 'hourly' && $hourlyRate > 0) {
            // For hourly staff, we need hours worked in the period
            $wd = $this->calculateWorkingDays($numericMonth, $numericYear, $settings['working_days_method'], $cutoffDay);
            $attendance = $this->getAttendanceDaysByType($staff['id'], $month, $numericYear, $wd['dates'], $cutoffDay);

            $totalHours = $attendance['total_hours_worked'];
            $grossEarnings = round($totalHours * $hourlyRate, 2);

            // For hourly staff, late/absent deductions still apply based on settings
            $requiredHours = isset($settings['required_hours_per_day']) ? (float)$settings['required_hours_per_day'] : 8;
            $perDaySalary = ($wd['count'] > 0) ? ($hourlyRate * $requiredHours) : $hourlyRate;

            $lateDeduction = $this->calculateDeduction(
                $attendance['late'], $settings['late_deduction_type'],
                $settings['late_deduction_value'], $perDaySalary
            );

            $absentDeduction = $this->calculateDeduction(
                $attendance['absent'], $settings['absent_deduction_type'],
                $settings['absent_deduction_value'], $perDaySalary
            );

            $halfDayDeduction = $this->calculateDeduction(
                $attendance['half_day'], $settings['half_day_deduction_type'],
                $settings['half_day_deduction_value'], $perDaySalary
            );

            // Short-hour deduction: when staff worked less than required hours per day
            $shortHourDeduction = $this->calculateShortHourDeduction(
                $attendance['records'] ?? array(),
                $requiredHours,
                $settings['short_hour_deduction_type'] ?? 'disabled',
                $settings['short_hour_deduction_value'] ?? 0,
                $settings['short_hour_threshold'] ?? 1,
                $perDaySalary
            );

            $totalDeduction = $absentDeduction + $lateDeduction + $halfDayDeduction + $shortHourDeduction['deduction'];
            $netSalary = $grossEarnings - $totalDeduction;

            if ($netSalary < 0) {
                $netSalary = 0;
            }

            return array(
                'basic'              => 0,
                'hourly_rate'        => $hourlyRate,
                'total_hours_worked' => $totalHours,
                'per_day_salary'     => round($perDaySalary, 2),
                'working_days'       => $wd['count'],
                'present_days'       => $attendance['present'],
                'late_days'          => $attendance['late'],
                'absent_days'        => $attendance['absent'],
                'half_day_days'      => $attendance['half_day'],
                'holiday_days'       => $attendance['holiday'],
                'absent_deduction'   => round($absentDeduction, 2),
                'late_deduction'     => round($lateDeduction, 2),
                'half_day_deduction' => round($halfDayDeduction, 2),
                'short_hour_deduction' => round($shortHourDeduction['deduction'], 2),
                'short_hour_days'    => $shortHourDeduction['short_days'],
                'short_hours_total'  => $shortHourDeduction['short_hours_total'],
                'total_deduction'    => round($totalDeduction, 2),
                'net_salary'         => round($netSalary, 2),
                'salary_type'        => 'hourly',
            );
        }

        // ============ MONTHLY STAFF ============
        if ($basic <= 0) {
            return null;
        }

        $wd = $this->calculateWorkingDays($numericMonth, $numericYear, $settings['working_days_method'], $cutoffDay);
        $workingDays = $wd['count'];

        if ($workingDays <= 0) {
            return null;
        }

        $perDaySalary = $basic / $workingDays;
        $attendance = $this->getAttendanceDaysByType($staff['id'], $month, $numericYear, $wd['dates'], $cutoffDay);

        $absentDeduction = $this->calculateDeduction(
            $attendance['absent'], $settings['absent_deduction_type'],
            $settings['absent_deduction_value'], $perDaySalary
        );

        $lateDeduction = $this->calculateDeduction(
            $attendance['late'], $settings['late_deduction_type'],
            $settings['late_deduction_value'], $perDaySalary
        );

        $halfDayDeduction = $this->calculateDeduction(
            $attendance['half_day'], $settings['half_day_deduction_type'],
            $settings['half_day_deduction_value'], $perDaySalary
        );

        // Short-hour deduction: when staff worked less than required hours per day
        $requiredHours = isset($settings['required_hours_per_day']) ? (float)$settings['required_hours_per_day'] : 8;
        $shortHourDeduction = $this->calculateShortHourDeduction(
            $attendance['records'] ?? array(),
            $requiredHours,
            $settings['short_hour_deduction_type'] ?? 'disabled',
            $settings['short_hour_deduction_value'] ?? 0,
            $settings['short_hour_threshold'] ?? 1,
            $perDaySalary
        );

        $totalDeduction = $absentDeduction + $lateDeduction + $halfDayDeduction + $shortHourDeduction['deduction'];
        $netSalary = $basic - $totalDeduction;

        if ($netSalary < 0) {
            $netSalary = 0;
        }

        // Also calculate hours worked for display purposes
        $totalHours = $attendance['total_hours_worked'];

        return array(
            'basic'              => $basic,
            'per_day_salary'     => round($perDaySalary, 2),
            'working_days'       => $workingDays,
            'present_days'       => $attendance['present'],
            'late_days'          => $attendance['late'],
            'absent_days'        => $attendance['absent'],
            'half_day_days'      => $attendance['half_day'],
            'holiday_days'       => $attendance['holiday'],
            'absent_deduction'   => round($absentDeduction, 2),
            'late_deduction'     => round($lateDeduction, 2),
            'half_day_deduction' => round($halfDayDeduction, 2),
            'short_hour_deduction' => round($shortHourDeduction['deduction'], 2),
            'short_hour_days'    => $shortHourDeduction['short_days'],
            'short_hours_total'  => $shortHourDeduction['short_hours_total'],
            'total_deduction'    => round($totalDeduction, 2),
            'net_salary'         => round($netSalary, 2),
            'total_hours_worked' => $totalHours,
            'salary_type'        => 'monthly',
        );
    }

    /**
     * Auto-generate payroll for ALL active staff for a given month/year
     * Returns: ['generated' => N, 'skipped' => N, 'errors' => [...]]
     */
    public function autoGeneratePayroll($month, $year, $generatedBy = 0)
    {
        $settings = $this->getPayrollSettings();
        if (empty($settings)) {
            return array('generated' => 0, 'skipped' => 0, 'errors' => array('Payroll settings not found'));
        }

        // Get all active staff (with salary_type and hourly_rate)
        $staffList = $this->getActiveStaffForPayroll();

        $generated = 0;
        $skipped = 0;
        $errors = array();

        foreach ($staffList as $staff) {
            // Check if payslip already exists for this month/year/staff
            $exists = $this->db->where(array(
                'month'   => $month,
                'year'    => $year,
                'staff_id' => $staff['id'],
            ))->get('staff_payslip')->num_rows();

            if ($exists > 0) {
                $skipped++;
                continue;
            }

            // Calculate salary based on attendance
            $calc = $this->calculateAutoSalary($staff, $settings, $month, (int) $year);

            if ($calc === null) {
                $skipped++;
                continue;
            }

            // Create payslip
            $salaryType = isset($calc['salary_type']) ? $calc['salary_type'] : 'monthly';
            $hourlyRate = isset($calc['hourly_rate']) ? $calc['hourly_rate'] : 0;
            $totalHours = isset($calc['total_hours_worked']) ? $calc['total_hours_worked'] : 0;
            
            $data = array(
                'staff_id'           => $staff['id'],
                'basic'              => $calc['basic'],
                'total_allowance'    => 0,
                'total_deduction'    => $calc['total_deduction'],
                'net_salary'         => $calc['net_salary'],
                'payment_date'       => date('Y-m-01'), // Will be updated when actually paid
                'status'             => 'generated',
                'month'              => $month,
                'year'               => $year,
                'tax'                => 0,
                'leave_deduction'    => $calc['total_deduction'],
                'generated_by'       => $generatedBy,
                'salary_type'        => $salaryType,
                'hourly_rate'        => $hourlyRate,
                'total_hours_worked' => $totalHours,
            );

            $insertId = $this->createPayslip($data);

            if ($insertId) {
                // Add attendance deduction line in payslip_allowance
                if ($calc['total_deduction'] > 0) {
                    $deductionLines = array();
                    if ($calc['absent_deduction'] > 0) {
                        $deductionLines[] = array(
                            'payslip_id'     => $insertId,
                            'allowance_type' => 'Absent Deduction (' . $calc['absent_days'] . ' days)',
                            'amount'         => $calc['absent_deduction'],
                            'staff_id'       => $staff['id'],
                            'cal_type'       => 'negative',
                        );
                    }
                    if ($calc['late_deduction'] > 0) {
                        $deductionLines[] = array(
                            'payslip_id'     => $insertId,
                            'allowance_type' => 'Late Deduction (' . $calc['late_days'] . ' days)',
                            'amount'         => $calc['late_deduction'],
                            'staff_id'       => $staff['id'],
                            'cal_type'       => 'negative',
                        );
                    }
                    if ($calc['half_day_deduction'] > 0) {
                        $deductionLines[] = array(
                            'payslip_id'     => $insertId,
                            'allowance_type' => 'Half Day Deduction (' . $calc['half_day_days'] . ' days)',
                            'amount'         => $calc['half_day_deduction'],
                            'staff_id'       => $staff['id'],
                            'cal_type'       => 'negative',
                        );
                    }
                    if (isset($calc['short_hour_deduction']) && $calc['short_hour_deduction'] > 0) {
                        $deductionLines[] = array(
                            'payslip_id'     => $insertId,
                            'allowance_type' => 'Short Hour Deduction (' . $calc['short_hour_days'] . ' days)',
                            'amount'         => $calc['short_hour_deduction'],
                            'staff_id'       => $staff['id'],
                            'cal_type'       => 'negative',
                        );
                    }
                    if (!empty($deductionLines)) {
                        $this->db->insert_batch('payslip_allowance', $deductionLines);
                    }
                }

                // Store breakdown in remark
                $remark = 'Auto: ' . $salaryType . ' WDays=' . $calc['working_days'] . ' P=' . $calc['present_days'] .
                    ' L=' . $calc['late_days'] . ' A=' . $calc['absent_days'] .
                    ' HD=' . $calc['half_day_days'] . ' H=' . $calc['holiday_days'];
                if ($salaryType === 'hourly') {
                    $remark .= ' Hours=' . $totalHours . ' Rate=' . $hourlyRate;
                }
                $this->db->where('id', $insertId)->update('staff_payslip', array('remark' => $remark));

                $generated++;
            } else {
                $errors[] = 'Failed to create payslip for ' . $staff['name'] . ' (' . $staff['employee_id'] . ')';
            }
        }

        return array('generated' => $generated, 'skipped' => $skipped, 'errors' => $errors);
    }

}
