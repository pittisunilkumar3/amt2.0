<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Online Admission Report API Controller
 * 
 * Provides API endpoints for retrieving online admission data
 * with flexible filtering capabilities by class, section, and admission status.
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Report APIs
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Online_admission_report_api extends CI_Controller
{
    /**
     * Constructor
     * 
     * Loads required models and helpers for the API
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load required models - IMPORTANT: Load setting_model FIRST
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('student_model');
        
        // Load helpers
        $this->load->helper('url');
        $this->load->helper('security');
    }

    /**
     * Filter Online Admission Report
     * 
     * Retrieves online admission data based on optional filter parameters.
     * Handles null/empty parameters gracefully by returning all records when no filters are provided.
     * Supports both single values and arrays for multi-select functionality.
     * 
     * @method POST
     * @route  /api/online-admission-report/filter
     * 
     * @param  int|array  $class_id          Optional. Class ID(s) to filter by
     * @param  int|array  $section_id        Optional. Section ID(s) to filter by
     * @param  int|array  $admission_status  Optional. Admission status (0=pending, 1=admitted)
     * @param  string     $from_date         Optional. Start date for date range filter
     * @param  string     $to_date           Optional. End date for date range filter
     * 
     * @return JSON Response with status, message, filters_applied, total_records, data, and timestamp
     * 
     * @example
     * Request Body:
     * {
     *   "class_id": 1,
     *   "section_id": 2,
     *   "admission_status": 1
     * }
     * 
     * Response:
     * {
     *   "status": 1,
     *   "message": "Online admission report retrieved successfully",
     *   "filters_applied": {
     *     "class_id": [1],
     *     "section_id": [2],
     *     "admission_status": [1]
     *   },
     *   "total_records": 25,
     *   "data": [...],
     *   "timestamp": "2025-10-07 10:30:45"
     * }
     */
    public function filter()
    {
        try {
            // Validate request method
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

            // Validate authentication
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
            $json_input = json_decode(file_get_contents('php://input'), true);
            
            // Extract filter parameters
            $class_id = isset($json_input['class_id']) ? $json_input['class_id'] : null;
            $section_id = isset($json_input['section_id']) ? $json_input['section_id'] : null;
            $admission_status = isset($json_input['admission_status']) ? $json_input['admission_status'] : null;
            $from_date = isset($json_input['from_date']) ? $json_input['from_date'] : null;
            $to_date = isset($json_input['to_date']) ? $json_input['to_date'] : null;

            // Convert single values to arrays for consistent handling
            if (!is_array($class_id)) {
                $class_id = !empty($class_id) ? array($class_id) : array();
            }
            if (!is_array($section_id)) {
                $section_id = !empty($section_id) ? array($section_id) : array();
            }
            if (!is_array($admission_status)) {
                $admission_status = !empty($admission_status) && $admission_status !== '' ? array($admission_status) : array();
            }

            // Filter out empty values from arrays
            $class_id = array_filter($class_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });
            $section_id = array_filter($section_id, function($value) { 
                return !empty($value) && $value !== null && $value !== ''; 
            });
            $admission_status = array_filter($admission_status, function($value) { 
                return $value !== null && $value !== ''; 
            });

            // Convert empty arrays to null for graceful handling
            $class_id = !empty($class_id) ? $class_id : null;
            $section_id = !empty($section_id) ? $section_id : null;
            $admission_status = !empty($admission_status) ? $admission_status : null;

            // Get online admission report data from model
            $data = $this->student_model->getOnlineAdmissionReportByFilters(
                $class_id, 
                $section_id, 
                $admission_status,
                $from_date,
                $to_date
            );

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Online admission report retrieved successfully',
                'filters_applied' => [
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'admission_status' => $admission_status,
                    'from_date' => $from_date,
                    'to_date' => $to_date
                ],
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Online Admission Report API Filter Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }

    /**
     * List All Online Admissions
     * 
     * Retrieves all online admission data.
     * No filter parameters required - returns all online admission records.
     * 
     * @method POST
     * @route  /api/online-admission-report/list
     * 
     * @return JSON Response with status, message, total_records, data, and timestamp
     * 
     * @example
     * Request Body:
     * {}
     * 
     * Response:
     * {
     *   "status": 1,
     *   "message": "Online admission report retrieved successfully",
     *   "total_records": 150,
     *   "data": [...],
     *   "timestamp": "2025-10-07 10:30:45"
     * }
     */
    public function list()
    {
        try {
            // Validate request method
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

            // Validate authentication
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

            // Get all online admissions (no filters)
            $data = $this->student_model->getOnlineAdmissionReportByFilters(null, null, null, null, null);

            // Prepare response
            $response = [
                'status' => 1,
                'message' => 'Online admission report retrieved successfully',
                'total_records' => count($data),
                'data' => $data,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            log_message('error', 'Online Admission Report API List Error: ' . $e->getMessage());
            
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Internal server error occurred',
                    'error' => $e->getMessage(),
                    'data' => null
                ]));
        }
    }
}

