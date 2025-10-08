<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Collection Report API Controller
 * 
 * Provides API endpoints for fee collection reports with filtering by:
 * - Date range (search_type or date_from/date_to)
 * - Fee type
 * - Collected by (staff member)
 * - Class and Section
 * - Session
 * 
 * @package    School Management System
 * @subpackage API Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0.0
 */
class Collection_report_api extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Disable error display - API should only return JSON
        ini_set('display_errors', 0);
        error_reporting(0);

        // Load required models in correct order
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('studentfeemaster_model');
        $this->load->model('class_model');
        $this->load->model('feetype_model');
        $this->load->model('session_model');

        // Load library
        $this->load->library('customlib');

        // Load helper for JSON validation
        $this->load->helper('custom');
    }

    /**
     * Filter endpoint - Get collection report with filters
     * POST /api/collection-report/filter
     */
    public function filter()
    {
        // Authenticate request
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

        try {
            // Get JSON input
            $json_input = json_decode($this->input->raw_input_stream, true);
            
            // Get filter parameters (all optional)
            // Treat empty strings as null for graceful handling
            $search_type = (isset($json_input['search_type']) && $json_input['search_type'] !== '') ? $json_input['search_type'] : null;
            $date_from = (isset($json_input['date_from']) && $json_input['date_from'] !== '') ? $json_input['date_from'] : null;
            $date_to = (isset($json_input['date_to']) && $json_input['date_to'] !== '') ? $json_input['date_to'] : null;
            $feetype_id = (isset($json_input['feetype_id']) && $json_input['feetype_id'] !== '') ? $json_input['feetype_id'] : null;
            $received_by = (isset($json_input['received_by']) && $json_input['received_by'] !== '') ? $json_input['received_by'] : null;
            $group = (isset($json_input['group']) && $json_input['group'] !== '') ? $json_input['group'] : null;
            $class_id = (isset($json_input['class_id']) && $json_input['class_id'] !== '') ? $json_input['class_id'] : null;
            $section_id = (isset($json_input['section_id']) && $json_input['section_id'] !== '') ? $json_input['section_id'] : null;
            $session_id = (isset($json_input['session_id']) && $json_input['session_id'] !== '') ? $json_input['session_id'] : null;

            // Determine date range
            if ($search_type !== null) {
                $dates = $this->customlib->get_betweendate($search_type);
            } elseif ($date_from !== null && $date_to !== null) {
                $dates = [
                    'from_date' => $date_from,
                    'to_date' => $date_to
                ];
            } else {
                // Default to current month if no date parameters provided
                $dates = $this->customlib->get_betweendate('this_month');
            }

            $start_date = date('Y-m-d', strtotime($dates['from_date']));
            $end_date = date('Y-m-d', strtotime($dates['to_date']));

            // Get collection report data
            $results = $this->studentfeemaster_model->getFeeCollectionReport(
                $start_date,
                $end_date,
                $feetype_id,
                $received_by,
                $group,
                $class_id,
                $section_id,
                $session_id
            );

            $response = [
                'status' => 1,
                'message' => 'Collection report retrieved successfully',
                'filters_applied' => [
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date,
                    'feetype_id' => $feetype_id,
                    'received_by' => $received_by,
                    'group' => $group,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id
                ],
                'total_records' => count($results),
                'data' => $results,
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving collection report: ' . $e->getMessage()
                ]));
        }
    }

    /**
     * List endpoint - Get filter options
     * POST /api/collection-report/list
     */
    public function list()
    {
        // Authenticate request
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

        try {
            // Get classes
            $classes = $this->class_model->get();
            
            // Get fee types (including transport fees)
            $feetype = $this->feetype_model->get();
            $tnumber = count($feetype);
            $feetype[$tnumber] = array('id' => 'transport_fees', 'type' => 'Transport Fees');
            
            // Get staff members who can collect fees
            $collect_by = $this->studentfeemaster_model->get_feesreceived_by();
            
            // Get sessions
            $sessions = $this->session_model->get();
            
            // Get search types
            $searchlist = $this->customlib->get_searchtype();
            
            // Get group by options
            $group_by = $this->customlib->get_groupby();

            $response = [
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => [
                    'classes' => $classes,
                    'fee_types' => $feetype,
                    'collect_by' => $collect_by,
                    'sessions' => $sessions,
                    'search_types' => $searchlist,
                    'group_by' => $group_by
                ],
                'timestamp' => date('Y-m-d H:i:s')
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json')
                ->set_output(json_encode($response));

        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 0,
                    'message' => 'Error retrieving filter options: ' . $e->getMessage()
                ]));
        }
    }
}

