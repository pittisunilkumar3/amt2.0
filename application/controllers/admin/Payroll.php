<?php

class Payroll extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("mailsms");
        $this->config->load("payroll");
        $this->load->library('mailsmsconf');
        $this->load->library('media_storage');
        $this->config_attendance = $this->config->item('attendence');
        $this->staff_attendance  = $this->config->item('staffattendance');
        $this->payment_mode      = $this->config->item('payment_mode');
        $this->load->model("payroll_model");
        $this->load->model("staff_model");
        $this->load->model('staffattendancemodel');
        $this->payroll_status     = $this->config->item('payroll_status');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {

        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/payroll');
        $data["staff_id"]            = "";
        $data["name"]                = "";
        $data["month"]               = date("F", strtotime("-1 month"));
        $data["year"]                = date("Y");
        $data["present"]             = 0;
        $data["absent"]              = 0;
        $data["late"]                = 0;
        $data["half_day"]            = 0;
        $data["holiday"]             = 0;
        $data["leave_count"]         = 0;
        $data["alloted_leave"]       = 0;
        $data["basic"]               = 0;
        $data["payment_mode"]        = $this->payment_mode;
        $user_type                   = $this->staff_model->getStaffRole();
        $data['classlist']           = $user_type;
        $data['monthlist']           = $this->customlib->getMonthDropdown();
        $data['sch_setting']         = $this->sch_setting_detail;
        $data['staffid_auto_insert'] = $this->sch_setting_detail->staffid_auto_insert;
        $submit                      = $this->input->post("search");
        if (isset($submit) && $submit == "search") {

            $month    = $this->input->post("month");
            $year     = $this->input->post("year");
            $emp_name = $this->input->post("name");
            $role     = $this->input->post("role");

            $searchEmployee = $this->payroll_model->searchEmployee($month, $year, $emp_name, $role);

            $data["resultlist"] = $searchEmployee;
            $data["name"]       = $emp_name;
            $data["month"]      = $month;
            $data["year"]       = $year;
        }

        $data["payroll_status"] = $this->payroll_status;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/stafflist", $data);
        $this->load->view("layout/footer", $data);
    }

    public function create($month, $year, $id)
    {       
        
        $data["staff_id"]            = "";
        $data["basic"]               = "";
        $data["name"]                = "";
        $data["month"]               = "";
        $data["year"]                = "";
        $data["present"]             = 0;
        $data["absent"]              = 0;
        $data["late"]                = 0;
        $data["half_day"]            = 0;
        $data["holiday"]             = 0;
        $data["leave_count"]         = 0;
        $data["alloted_leave"]       = 0;
        $data['sch_setting']         = $this->sch_setting_detail;
        $data['staffid_auto_insert'] = $this->sch_setting_detail->staffid_auto_insert;
        $user_type                   = $this->staff_model->getStaffRole();
        $data['classlist']           = $user_type;

        $date = $year . "-" . $month;

        $searchEmployee = $this->payroll_model->searchEmployeeById($id);

        $data['result'] = $searchEmployee;
        $data["month"]  = $month;
        $data["year"]   = $year;

        $alloted_leave = $this->staff_model->alloted_leave($id);

        $newdate = date('Y-m-d', strtotime($date . " +1 month"));

        $data['monthAttendance'] = $this->monthAttendance($newdate, 3, $id);
        $data['monthLeaves']     = $this->monthLeaves($newdate, 3, $id);
        $data["attendanceType"]  = $this->staffattendancemodel->getStaffAttendanceType();
        $data["alloted_leave"]   = $alloted_leave[0]["alloted_leave"];

        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/create", $data);
        $this->load->view("layout/footer", $data);
    }

    public function edit($id)
    {

        $data["staff_id"]         = "";
        $data["basic"]            = "";
        $data["name"]             = "";
        $data["month"]            = "";
        $data["year"]             = "";
        $data["present"]          = 0;
        $data["absent"]           = 0;
        $data["late"]             = 0;
        $data["half_day"]         = 0;
        $data["holiday"]          = 0;
        $data["leave_count"]      = 0;
        $data["alloted_leave"]    = 0;
        $user_type                = $this->staff_model->getStaffRole();
        $employee_payroll         = $this->payroll_model->getPayslip($id);
        if (!$employee_payroll) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Payslip not found</div>');
            redirect('admin/payroll');
        }
        $data['employee_payroll'] = $employee_payroll;
        $data['classlist']        = $user_type;
        $data['sch_setting']      = $this->sch_setting_detail;
        $searchEmployee           = $this->payroll_model->searchEmployeeById($employee_payroll['staff_id']);
        $date                     = $employee_payroll['year'] . "-" . $employee_payroll['month'];
        $data['result']           = $searchEmployee;
        $data["month"]            = $employee_payroll['month'];
        $data["year"]             = $employee_payroll['year'];

        $data["earnings"]   = $this->payroll_model->getAllowance($id, 'positive');
        $data["deductions"] = $this->payroll_model->getAllowance($id, 'negative');

        $alloted_leave           = $this->staff_model->alloted_leave($employee_payroll['staff_id']);
        $newdate                 = date('Y-m-d', strtotime($date . " +1 month"));
        $data['monthAttendance'] = $this->monthAttendance($newdate, 3, $employee_payroll['staff_id']);
        $data['monthLeaves']     = $this->monthLeaves($newdate, 3, $employee_payroll['staff_id']);
        $data["attendanceType"]  = $this->staffattendancemodel->getStaffAttendanceType();
        $data["alloted_leave"]   = $alloted_leave[0]["alloted_leave"];
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/edit", $data);
        $this->load->view("layout/footer", $data);
    }

    public function editpayroll()
    {
        $id              = $this->input->post("id");
        $basic           = $this->input->post("basic");
        $total_allowance = $this->input->post("total_allowance");
        $total_deduction = $this->input->post("total_deduction");
        $net_salary      = $this->input->post("net_salary");
        $status          = $this->input->post("status");
        $staff_id        = $this->input->post("staff_id");
        $month           = $this->input->post("month");
        $name            = $this->input->post("name");
        $year            = $this->input->post("year");
        $tax             = $this->input->post("tax_percent");
        $leave_deduction = $this->input->post("leave_deduction");
        $this->form_validation->set_rules('net_salary', $this->lang->line('net_salary'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->create($month, $year, $staff_id);
        } else {

            $data = array(
                'id'              => $id,
                'staff_id'        => $staff_id,
                'basic'           => convertCurrencyFormatToBaseAmount($basic),
                'total_allowance' => convertCurrencyFormatToBaseAmount($total_allowance),
                'total_deduction' => convertCurrencyFormatToBaseAmount($total_deduction),
                'net_salary'      => convertCurrencyFormatToBaseAmount($net_salary),
                'payment_date'    => date("Y-m-d"),
                'status'          => $status,
                'month'           => $month,
                'year'            => $year,
                'tax'             => convertCurrencyFormatToBaseAmount($tax),
                'leave_deduction' => '0',
                'generated_by'    => $this->customlib->getStaffID(),
            );

            $checkForUpdate = $this->payroll_model->checkPayslip($month, $year, $staff_id);
            if (!$checkForUpdate) {
                $insert_id         = $this->payroll_model->createPayslip($data);
                $payslipid         = $insert_id;
                $allowance_type    = $this->input->post("allowance_type");
                $deduction_type    = $this->input->post("deduction_type");
                $allowance_prev_id = $this->input->post("allowance_prev_id");
                $deduction_prev_id = $this->input->post("deduction_prev_id");
                $allowance_amount  = $this->input->post("allowance_amount");
                $deduction_amount  = $this->input->post("deduction_amount");

                if (!empty($allowance_type)) {

                    $i                        = 0;
                    $insert_payslip_allowance = array();
                    $update_payslip_allowance = array();
                    foreach ($allowance_type as $key => $all) {
                        if ($allowance_prev_id[$i] != 0) {
                            $update_payslip_allowance[] = array(
                                'id'             => $allowance_prev_id[$i],
                                'payslip_id'     => $payslipid,
                                'allowance_type' => $allowance_type[$i],
                                'amount'         => convertCurrencyFormatToBaseAmount($allowance_amount[$i]),
                                'staff_id'       => $staff_id,
                                'cal_type'       => "positive",
                            );
                        } else {
                            $insert_payslip_allowance[] = array(
                                'payslip_id'     => $payslipid,
                                'allowance_type' => $allowance_type[$i],
                                'amount'         => convertCurrencyFormatToBaseAmount($allowance_amount[$i]),
                                'staff_id'       => $staff_id,
                                'cal_type'       => "positive",
                            );
                        }

                        $i++;
                    }

                    $insert_payslip_allowance = $this->payroll_model->update_allowance($insert_payslip_allowance, $update_payslip_allowance, $allowance_prev_id, $payslipid, 'positive');
                } else {

                    $insert_payslip_allowance = $this->payroll_model->update_allowance([], [], [0], $payslipid, 'positive');
                }

                if (!empty($deduction_type)) {
                    $j                        = 0;
                    $insert_payslip_allowance = array();
                    $update_payslip_allowance = array();

                    foreach ($deduction_type as $key => $type) {
                        if ($deduction_prev_id[$j] != 0) {
                            $update_payslip_allowance[] = array(
                                'id'             => $deduction_prev_id[$j],
                                'payslip_id'     => $payslipid,
                                'allowance_type' => $deduction_type[$j],
                                'amount'         => convertCurrencyFormatToBaseAmount($deduction_amount[$j]),
                                'staff_id'       => $staff_id,
                                'cal_type'       => "negative",
                            );
                        } else {
                            $insert_payslip_allowance[] = array(
                                'payslip_id'     => $payslipid,
                                'allowance_type' => $deduction_type[$j],
                                'amount'         => convertCurrencyFormatToBaseAmount($deduction_amount[$j]),
                                'staff_id'       => $staff_id,
                                'cal_type'       => "negative",
                            );
                        }
                        $j++;
                    }

                    $insert_payslip_allowance = $this->payroll_model->update_allowance($insert_payslip_allowance, $update_payslip_allowance, $deduction_prev_id, $payslipid, 'negative');
                } else {
                    $insert_payslip_allowance = $this->payroll_model->update_allowance([], [], [0], $payslipid, 'negative');
                }

                redirect('admin/payroll');
            } else {

                $this->session->set_flashdata("msg", "<div class='alert alert-warning'>" . $this->lang->line('payslip_not_generated') . "</div>");

                redirect('admin/payroll');
            }
        }
    }

    public function monthAttendance($st_month, $no_of_months, $emp)
    {
        $record = array();
        for ($i = 1; $i <= $no_of_months; $i++) {

            $r     = array();
            $month = date('m', strtotime($st_month . " -$i month"));
            $year  = date('Y', strtotime($st_month . " -$i month"));

            foreach ($this->staff_attendance as $att_key => $att_value) {

                $s = $this->payroll_model->count_attendance_obj($month, $year, $emp, $att_value);

                $r[$att_key] = $s;
            }

            $record['01-' . $month . '-' . $year] = $r;
        }
        return $record;
    }

    public function monthLeaves($st_month, $no_of_months, $emp)
    {
        $record = array();
        for ($i = 1; $i <= $no_of_months; $i++) {

            $r           = array();
            $month       = date('m', strtotime($st_month . " -$i month"));
            $year        = date('Y', strtotime($st_month . " -$i month"));
            $leave_count = $this->staff_model->count_leave($month, $year, $emp);
            if (!empty($leave_count["tl"])) {
                $l = $leave_count["tl"];
            } else {
                $l = "0";
            }

            $record[$month] = $l;
        }

        return $record;
    }

    public function payslip()
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_add')) {
            access_denied();
        }

        $basic           = convertCurrencyFormatToBaseAmount($this->input->post("basic"));
        $total_allowance = convertCurrencyFormatToBaseAmount($this->input->post("total_allowance"));
        $total_deduction = convertCurrencyFormatToBaseAmount($this->input->post("total_deduction"));
        $net_salary      = convertCurrencyFormatToBaseAmount($this->input->post("net_salary"));
        $status          = $this->input->post("status");
        $staff_id        = $this->input->post("staff_id");
        $month           = $this->input->post("month");
        $name            = $this->input->post("name");
        $year            = $this->input->post("year");
        $tax             = convertCurrencyFormatToBaseAmount($this->input->post("tax"));
        $leave_deduction = $this->input->post("leave_deduction");
        $this->form_validation->set_rules('net_salary', $this->lang->line('net_salary'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->create($month, $year, $staff_id);
        } else {

            $data = array('staff_id' => $staff_id,
                'basic'                  => $basic,
                'total_allowance'        => $total_allowance,
                'total_deduction'        => $total_deduction,
                'net_salary'             => $net_salary,
                'payment_date'           => date("Y-m-d"),
                'status'                 => $status,
                'month'                  => $month,
                'year'                   => $year,
                'tax'                    => $tax,
                'leave_deduction'        => '0',
            );

            $checkForUpdate = $this->payroll_model->checkPayslip($month, $year, $staff_id);
 
            if ($checkForUpdate == true) {

                $insert_id        = $this->payroll_model->createPayslip($data);
                $payslipid        = $insert_id;
                $allowance_type   = $this->input->post("allowance_type");
                $deduction_type   = $this->input->post("deduction_type");
                $allowance_amount = $this->input->post("allowance_amount");
                $deduction_amount = $this->input->post("deduction_amount");
                if (!empty($allowance_type)) {

                    $i = 0;
                    foreach ($allowance_type as $key => $all) {

                        $all_data = array(
                            'payslip_id'     => $payslipid,
                            'allowance_type' => $allowance_type[$i],
                            'amount'         => convertCurrencyFormatToBaseAmount($allowance_amount[$i]),
                            'staff_id'       => $staff_id,
                            'cal_type'       => "positive",
                        );

                        $insert_payslip_allowance = $this->payroll_model->add_allowance($all_data);

                        $i++;
                    }
                }

                if (!empty($deduction_type)) {
                    $j = 0;
                    foreach ($deduction_type as $key => $type) {

                        $type_data = array('payslip_id' => $payslipid,
                            'allowance_type'                => $deduction_type[$j],
                            'amount'                        => convertCurrencyFormatToBaseAmount($deduction_amount[$j]),
                            'staff_id'                      => $staff_id,
                            'cal_type'                      => "negative",
                        );

                        $insert_payslip_allowance = $this->payroll_model->add_allowance($type_data);

                        $j++;
                    }
                }

                redirect('admin/payroll');
            } else {

                $this->session->set_flashdata("msg", $this->lang->line('payslip_already_generated'));
                redirect('admin/payroll');
            }
        }
    }

    public function search($month, $year, $role = '')
    {
        $user_type              = $this->staff_model->getStaffRole();
        $data['classlist']      = $user_type;
        $data['monthlist']      = $this->customlib->getMonthDropdown();
        $searchEmployee         = $this->payroll_model->searchEmployee($month, $year, $emp_name = '', $role);
        $data["resultlist"]     = $searchEmployee;
        $data["name"]           = $emp_name;
        $data["month"]          = $month;
        $data["year"]           = $year;
        $data['sch_setting']    = $this->sch_setting_detail;
        $data["payroll_status"] = $this->payroll_status;
        $data["resultlist"]     = $searchEmployee;
        $data["payment_mode"]   = $this->payment_mode;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/stafflist", $data);
        $this->load->view("layout/footer", $data);
    }

    public function paymentRecord()
    {
        $month              = $this->input->get_post("month");
        $year               = $this->input->get_post("year");
        $id                 = $this->input->get_post("staffid");
        $searchEmployee     = $this->payroll_model->searchPayment($id, $month, $year);
        $data['result']     = $searchEmployee;
        $data['net_salary'] = amountFormat($searchEmployee['net_salary']);
          $data['monthlist']           = $this->customlib->getMonthDropdown();

        $data["month"]      = $data['monthlist'][$month];

           


        $data["year"]       = $year;
        echo json_encode($data);
    }

    public function paymentStatus($status)
    {
        $id          = $this->input->get('id');
        $updateStaus = $this->payroll_model->updatePaymentStatus($status, $id);
        redirect("admin/payroll");
    }

    public function paymentSuccess()
    {
        $payment_mode = $this->input->post("payment_mode");
        $date         = $this->input->post("payment_date");
        $payment_date = date('Y-m-d', strtotime($date));
        $remark       = $this->input->post("remarks");
        $status       = 'paid';
        $payslipid    = $this->input->post("paymentid");
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment_mode'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('payment_date', $this->lang->line('payment_date'), 'trim|required|xss_clean');
        
        if ($this->form_validation->run() == false) {

            $msg = array(
                'payment_mode' => form_error('payment_mode'),
                'payment_date' => form_error('payment_date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array('payment_mode' => $payment_mode, 'payment_date' => $this->customlib->dateFormatToYYYYMMDD($date), 'remark' => $remark, 'status' => $status);
            $this->payroll_model->paymentSuccess($data, $payslipid);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function payslipView()
    {
        $data["payment_mode"] = $this->payment_mode;
        $this->load->model("setting_model");
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result[0];
        $id                  = $this->input->post("payslipid");
        $result              = $this->payroll_model->getPayslip($id);
        $data['sch_setting'] = $this->sch_setting_detail;

        $data['staffid_auto_insert'] = $this->sch_setting_detail->staffid_auto_insert;
        if (!empty($result)) {
            $allowance                  = $this->payroll_model->getAllowance($result["id"]);
            $data["allowance"]          = $allowance;
            $positive_allowance         = $this->payroll_model->getAllowance($result["id"], "positive");
            $data["positive_allowance"] = $positive_allowance;
            $negative_allowance         = $this->payroll_model->getAllowance($result["id"], "negative");
            $data["negative_allowance"] = $negative_allowance;
            $data["result"]             = $result;
            $this->load->view("admin/payroll/payslipview", $data);
        } else {
            echo "<div class='alert alert-info'>" . $this->lang->line('no_record_found') . "</div>";
        }
    }

    public function payslippdf()
    {
        $this->load->model("setting_model");
        $setting_result             = $this->setting_model->get();
        $data['settinglist']        = $setting_result[0];
        $id                         = 15;
        $result                     = $this->payroll_model->getPayslip($id);
        $allowance                  = $this->payroll_model->getAllowance($result["id"]);
        $data["allowance"]          = $allowance;
        $positive_allowance         = $this->payroll_model->getAllowance($result["id"], "positive");
        $data["positive_allowance"] = $positive_allowance;
        $negative_allowance         = $this->payroll_model->getAllowance($result["id"], "negative");
        $data["negative_allowance"] = $negative_allowance;
        $data["result"]             = $result;
        $this->load->view("admin/payroll/payslippdf", $data);
    }

    public function payrollreport()
    {
        if (!$this->rbac->hasPrivilege('payroll_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/human_resource');
        $this->session->set_userdata('subsub_menu', 'Reports/attendance/attendance_report');
        $month                = $this->input->post("month");
        $year                 = $this->input->post("year");
        $role                 = $this->input->post("role");
        $data["month"]        = $month;
        $data["year"]         = $year;
        $data["role_select"]  = $role;
        $data['monthlist']    = $this->customlib->getMonthDropdown();
        $data['yearlist']     = $this->payroll_model->payrollYearCount();
        $staffRole            = $this->staff_model->getStaffRole();
        $data["role"]         = $staffRole;
        $data["payment_mode"] = $this->payment_mode;

        $this->form_validation->set_rules('year', $this->lang->line('year'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->load->view("layout/header", $data);
            $this->load->view("admin/payroll/payrollreport", $data);
            $this->load->view("layout/footer", $data);
        } else {

            $result = $this->payroll_model->getpayrollReport($month, $year, $role);

            $data["result"] = $result;
            $this->load->view("layout/header", $data);
            $this->load->view("admin/payroll/payrollreport", $data);
            $this->load->view("layout/footer", $data);
        }
    }

    public function deletepayroll($payslipid, $month, $year, $role = '')
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_delete')) {
            access_denied();
        }
        if (!empty($payslipid)) {
            $this->payroll_model->deletePayslip($payslipid);
        }

        redirect('admin/payroll/search/' . $month . "/" . $year . "/" . $role);
    }

    public function revertpayroll($payslipid, $month, $year, $role = '')
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_delete')) {
            access_denied();
        }
        if (!empty($payslipid)) {
            $this->payroll_model->revertPayslipStatus($payslipid);
        }
        redirect('admin/payroll/search/' . $month . "/" . $year . "/" . $role);

    }

    // ============================================
    // MY PAYROLL — Staff self-service view
    // ============================================

    /**
     * My Payroll — Shows the logged-in staff member their own payslips,
     * attendance breakdown, hours worked, cutoff info, and payroll settings.
     * Every role with 'my_payroll' permission can access this.
     */
    public function myPayroll()
    {
        if (!$this->rbac->hasPrivilege('my_payroll', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/payroll/myPayroll');

        $data['title']           = $this->lang->line('my_payroll');
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['payment_mode']    = $this->payment_mode;
        $data['payroll_status']  = $this->payroll_status;

        // Get current logged-in staff id
        $logged_in_staff = $this->customlib->getStaffID();
        $data['staff_id'] = $logged_in_staff;

        // Get staff details
        $data['staff'] = $this->payroll_model->searchEmployeeById($logged_in_staff);

        // Get payroll settings (cutoff, working days, deductions)
        $data['payrollSettings'] = $this->payroll_model->getPayrollSettings();

        // Get current month/year for the summary
        $currentMonth = date('F');
        $currentYear  = date('Y');
        $data['current_month'] = $currentMonth;
        $data['current_year']  = $currentYear;

        // Calculate current month attendance for this staff
        $settings      = $data['payrollSettings'];
        $cutoffDay     = (!empty($settings)) ? (int)$settings['payroll_cutoff_day'] : 0;
        $workingMethod = (!empty($settings)) ? $settings['working_days_method'] : 'exclude_sundays';

        $wd = $this->payroll_model->calculateWorkingDays($currentMonth, (int)$currentYear, $workingMethod, $cutoffDay);
        $attendance = $this->payroll_model->getAttendanceDaysByType($logged_in_staff, $currentMonth, (int)$currentYear, $wd['dates'], $cutoffDay);

        $data['working_days']    = $wd['count'];
        $data['working_dates']   = $wd['dates'];
        $data['present_days']    = $attendance['present'];
        $data['late_days']       = $attendance['late'];
        $data['absent_days']     = $attendance['absent'];
        $data['half_day_days']   = $attendance['half_day'];
        $data['holiday_days']    = $attendance['holiday'];
        $data['total_hours_worked'] = $attendance['total_hours_worked'];

        // Per day salary for display
        $basic = (float)$data['staff']['basic_salary'];
        $data['basic_salary'] = $basic;
        $data['per_day_salary']  = ($wd['count'] > 0 && $basic > 0) ? round($basic / $wd['count'], 2) : 0;

        // Salary type info
        $data['salary_type'] = isset($data['staff']['salary_type']) ? $data['staff']['salary_type'] : 'monthly';
        $data['hourly_rate'] = isset($data['staff']['hourly_rate']) ? (float)$data['staff']['hourly_rate'] : 0;

        // Estimated current month salary
        if ($data['salary_type'] === 'hourly' && $data['hourly_rate'] > 0) {
            $data['estimated_earnings'] = round($attendance['total_hours_worked'] * $data['hourly_rate'], 2);
            $data['estimated_basic'] = 0;
        } else {
            $data['estimated_earnings'] = $basic;
            $data['estimated_basic'] = $basic;
        }

        // Get all payslips for this staff (last 12 months)
        $this->db->select('*');
        $this->db->from('staff_payslip');
        $this->db->where('staff_id', $logged_in_staff);
        $this->db->order_by('year DESC, FIELD(month,"January","February","March","April","May","June","July","August","September","October","November","December") DESC');
        $payslipQuery = $this->db->get();
        $data['payslips'] = $payslipQuery->result_array();

        // Get total salary paid
        $this->db->select('SUM(net_salary) as total_paid');
        $this->db->from('staff_payslip');
        $this->db->where('staff_id', $logged_in_staff);
        $this->db->where('status', 'paid');
        $totalPaid = $this->db->get()->row();
        $data['total_paid'] = ($totalPaid && $totalPaid->total_paid) ? (float)$totalPaid->total_paid : 0;

        // Get total pending
        $this->db->select('SUM(net_salary) as total_pending');
        $this->db->from('staff_payslip');
        $this->db->where('staff_id', $logged_in_staff);
        $this->db->where('status', 'generated');
        $totalPending = $this->db->get()->row();
        $data['total_pending'] = ($totalPending && $totalPending->total_pending) ? (float)$totalPending->total_pending : 0;

        // Month list for dropdown
        $data['monthlist'] = $this->customlib->getMonthDropdown();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/payroll/my_payroll', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * AJAX: Get payslip detail for My Payroll
     */
    public function getMyPayslipDetail()
    {
        if (!$this->rbac->hasPrivilege('my_payroll', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $payslipId = $this->input->post('payslip_id');
        $staffId   = $this->customlib->getStaffID();

        // Security: only allow viewing own payslips (unless admin)
        $isAdmin = ($this->rbac->hasPrivilege('staff_payroll', 'can_view'));

        $this->db->select('*');
        $this->db->from('staff_payslip');
        $this->db->where('id', $payslipId);
        if (!$isAdmin) {
            $this->db->where('staff_id', $staffId);
        }
        $query = $this->db->get();
        $payslip = $query->row_array();

        if (empty($payslip)) {
            echo json_encode(array('status' => 'error', 'message' => 'Payslip not found'));
            return;
        }

        // Get allowances
        $allowance = $this->payroll_model->getAllowance($payslipId);

        // Get attendance breakdown for this payslip's month/year
        $psMonth = $payslip['month'];
        $psYear  = $payslip['year'];
        $psStaffId = $payslip['staff_id'];

        $settings      = $this->payroll_model->getPayrollSettings();
        $cutoffDay     = (!empty($settings)) ? (int)$settings['payroll_cutoff_day'] : 0;
        $workingMethod = (!empty($settings)) ? $settings['working_days_method'] : 'exclude_sundays';

        $wd = $this->payroll_model->calculateWorkingDays($psMonth, (int)$psYear, $workingMethod, $cutoffDay);
        $attendance = $this->payroll_model->getAttendanceDaysByType($psStaffId, $psMonth, (int)$psYear, $wd['dates'], $cutoffDay);

        echo json_encode(array(
            'status'     => 'success',
            'payslip'    => $payslip,
            'allowance'  => $allowance,
            'attendance' => $attendance,
            'working_days' => $wd['count'],
        ));
    }

    // ============================================
    // AUTO PAYROLL SETTINGS
    // ============================================

    public function payrollSettings()
    {
        // Admin with staff_payroll can edit; staff with view_payroll_settings can only view
        $canEdit = $this->rbac->hasPrivilege('staff_payroll', 'can_view');
        $canViewOnly = $this->rbac->hasPrivilege('view_payroll_settings', 'can_view');

        if (!$canEdit && !$canViewOnly) {
            access_denied();
        }

        $data['title'] = $this->lang->line('payroll_settings');
        $data['payrollSettings'] = $this->payroll_model->getPayrollSettings();
        $data['readonly'] = (!$canEdit && $canViewOnly) ? true : false;

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/payroll');

        $this->load->view('layout/header', $data);
        $this->load->view('admin/payroll/payrollsettings', $data);
        $this->load->view('layout/footer', $data);
    }

    public function savepayrollSettings()
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_edit')) {
            access_denied();
        }

        $data = array(
            'auto_payroll_enabled'    => $this->input->post('auto_payroll_enabled'),
            'auto_payroll_day'        => $this->input->post('auto_payroll_day'),
            'payroll_cutoff_day'      => ($this->input->post('payroll_cutoff_day') !== null) ? (int)$this->input->post('payroll_cutoff_day') : 0,
            'required_hours_per_day'  => ($this->input->post('required_hours_per_day') !== null) ? (float)$this->input->post('required_hours_per_day') : 8.00,
            'late_grace_minutes'      => ($this->input->post('late_grace_minutes') !== null) ? (int)$this->input->post('late_grace_minutes') : 15,
            'working_days_method'     => $this->input->post('working_days_method'),
            'absent_deduction_type'   => $this->input->post('absent_deduction_type'),
            'absent_deduction_value'  => convertCurrencyFormatToBaseAmount($this->input->post('absent_deduction_value')),
            'late_deduction_type'     => $this->input->post('late_deduction_type'),
            'late_deduction_value'    => convertCurrencyFormatToBaseAmount($this->input->post('late_deduction_value')),
            'half_day_deduction_type' => $this->input->post('half_day_deduction_type'),
            'half_day_deduction_value'=> convertCurrencyFormatToBaseAmount($this->input->post('half_day_deduction_value')),
            'short_hour_deduction_type'  => $this->input->post('short_hour_deduction_type'),
            'short_hour_deduction_value' => convertCurrencyFormatToBaseAmount($this->input->post('short_hour_deduction_value')),
            'short_hour_threshold'      => ($this->input->post('short_hour_threshold') !== null) ? (float)$this->input->post('short_hour_threshold') : 1.00,
        );

        $this->payroll_model->savePayrollSettings($data);

        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('update_message') . '</div>');
        redirect('admin/payroll/payrollSettings');
    }

    // ============================================
    // AUTO GENERATE PAYROLL
    // ============================================

    public function autoGenerate()
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_add')) {
            $array = array('status' => 'fail', 'error' => '', 'message' => 'Permission denied');
            echo json_encode($array);
            return;
        }

        $month = $this->input->post('month');
        $year  = $this->input->post('year');

        if (empty($month) || empty($year)) {
            $array = array('status' => 'fail', 'error' => '', 'message' => 'Month and Year are required');
            echo json_encode($array);
            return;
        }

        $generated_by = $this->customlib->getStaffID();
        $result = $this->payroll_model->autoGeneratePayroll($month, $year, $generated_by);

        $message = $result['generated'] . ' payslips generated';
        if ($result['skipped'] > 0) {
            $message .= ', ' . $result['skipped'] . ' skipped (already exist or no salary)';
        }
        if (!empty($result['errors'])) {
            $message .= '. Errors: ' . implode('; ', $result['errors']);
        }

        $array = array('status' => 'success', 'error' => '', 'message' => $message);
        echo json_encode($array);
    }

}
