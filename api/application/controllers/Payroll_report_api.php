<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Payroll Report API Controller
 * 
 * This controller handles API requests for payroll reports
 * showing staff payroll information with filtering by month, year, and role.
 * 
 * @package    CodeIgniter
 * @subpackage Controllers
 * @category   API
 */
class Payroll_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: setting_model MUST be loaded FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('payroll_model');
        $this->load->model('staff_model');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->database();
    }

    /**
     * Filter payroll report
     * 
     * POST /api/payroll-report/filter
     * 
     * Request body (all parameters optional):
     * {
     *   "month": "January",
     *   "year": 2025,
     *   "role": "Teacher",
     *   "from_date": "2025-01-01",
     *   "to_date": "2025-12-31"
     * }
     * 
     * Empty request body {} returns all payroll data
     */
    public function filter()
    {
        try {
            // Check request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            // Check authentication
            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters (all optional)
            $month = isset($json_input['month']) ? $json_input['month'] : null;
            $year = isset($json_input['year']) ? $json_input['year'] : null;
            $role = isset($json_input['role']) ? $json_input['role'] : null;
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;

            $result = array();
            
            // If date range is provided, use getbetweenpayrollReport
            if (!empty($from_date) && !empty($to_date)) {
                $result = $this->payroll_model->getbetweenpayrollReport($from_date, $to_date);
            }
            // If no filters provided or only year provided, get all payroll data
            else if (empty($month) && empty($role)) {
                // Default to current year if not provided
                if (empty($year)) {
                    $year = date('Y');
                }
                
                // Get all payroll records for the year
                $this->db->select('staff.id,staff.employee_id,staff.name,roles.name as user_type,staff.surname,staff_designation.designation,department.department_name as department,staff_payslip.*');
                $this->db->from('staff');
                $this->db->join('staff_payslip', 'staff_payslip.staff_id = staff.id', 'inner');
                $this->db->join('staff_designation', 'staff.designation = staff_designation.id', 'left');
                $this->db->join('department', 'staff.department = department.id', 'left');
                $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
                $this->db->join('roles', 'staff_roles.role_id = roles.id', 'left');
                $this->db->where('staff_payslip.year', $year);
                $this->db->where('staff.is_active', 1);
                $query = $this->db->get();
                $result = $query->result_array();
            }
            // Use getpayrollReport with filters
            else {
                // Default to current year if not provided
                if (empty($year)) {
                    $year = date('Y');
                }
                
                // Convert month name to number if provided
                $month_number = '';
                if (!empty($month)) {
                    $month_number = date('m', strtotime($month . ' 1'));
                }
                
                // Set role to "select" if not provided
                if (empty($role)) {
                    $role = "select";
                }
                
                $result = $this->payroll_model->getpayrollReport($month_number, $year, $role);
            }

            $response = [
                'status' => 1,
                'message' => 'Payroll report retrieved successfully',
                'filters_applied' => [
                    'month' => $month,
                    'year' => $year,
                    'role' => $role,
                    'from_date' => $from_date,
                    'to_date' => $to_date
                ],
                'total_records' => count($result),
                'data' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Payroll Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }

    /**
     * List payroll years and roles
     * 
     * POST /api/payroll-report/list
     * 
     * Returns available years and staff roles for filtering
     */
    public function list()
    {
        try {
            // Check request method
            if ($this->input->method() !== 'post') {
                $this->output
                    ->set_status_header(400)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Bad request. Only POST method allowed.'
                    ]));
                return;
            }

            // Check authentication
            if (!$this->auth_model->check_auth_client()) {
                $this->output
                    ->set_status_header(401)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'status' => 0,
                        'message' => 'Unauthorized access'
                    ]));
                return;
            }

            // Get available years
            $years = $this->payroll_model->payrollYearCount();
            
            // Get staff roles
            $roles = $this->staff_model->getStaffRole();
            
            // Get months
            $months = array(
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            );

            $response = [
                'status' => 1,
                'message' => 'Payroll filter options retrieved successfully',
                'total_years' => count($years),
                'years' => $years,
                'total_roles' => count($roles),
                'roles' => $roles,
                'months' => $months,
                'note' => 'Use the filter endpoint with month, year, role, or date range to get payroll report',
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Payroll Report API Error: ' . $e->getMessage());
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error',
                    'error' => $e->getMessage()
                ]));
        }
    }
}

