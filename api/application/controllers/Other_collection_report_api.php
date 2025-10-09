<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Other Collection Report API Controller
 * 
 * Provides API endpoints for other fee collection reports
 * 
 * @package    School Management System API
 * @subpackage Controllers
 * @category   Finance Reports
 * @author     SMS Development Team
 * @version    1.0
 */
class Other_collection_report_api extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        // Suppress errors for clean JSON output
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
        
        // Set JSON response header
        header('Content-Type: application/json');
        
        // Try to load database with error handling
        try {
            $this->load->database();
            
            // Test database connection
            if (!$this->db->conn_id) {
                throw new Exception('Database connection failed');
            }
            
            // Load required models
            $this->load->model('setting_model');
            $this->load->model('auth_model');
            $this->load->model('module_model');
            $this->load->model('class_model');
            $this->load->model('section_model');
            
        } catch (Exception $e) {
            // Return JSON error response
            echo json_encode(array(
                'status' => 0,
                'message' => 'Database connection error. Please ensure MySQL is running in XAMPP.',
                'error' => 'Unable to connect to database server',
                'timestamp' => date('Y-m-d H:i:s')
            ));
            exit;
        }
    }

    /**
     * List endpoint - Get filter options
     * 
     * POST /api/other-collection-report/list
     */
    public function list()
    {
        try {
            // Authenticate request
            if (!$this->auth_model->check_auth_client()) {
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get filter options
            $search_types = array(
                array('key' => 'today', 'label' => 'Today'),
                array('key' => 'this_week', 'label' => 'This Week'),
                array('key' => 'this_month', 'label' => 'This Month'),
                array('key' => 'last_month', 'label' => 'Last Month'),
                array('key' => 'this_year', 'label' => 'This Year'),
                array('key' => 'period', 'label' => 'Custom Period')
            );

            $group_by = array(
                array('key' => 'class', 'label' => 'Group By Class'),
                array('key' => 'collection', 'label' => 'Group By Collection'),
                array('key' => 'mode', 'label' => 'Group By Payment Mode')
            );

            // Get classes with sections
            $classes = $this->class_model->get();
            
            // Get fee types (other fees)
            $this->db->select('id, type');
            $this->db->from('feetypeadding');
            $this->db->order_by('type', 'asc');
            $fee_types = $this->db->get()->result_array();

            // Get received by list
            $this->db->select('DISTINCT received_by');
            $this->db->from('student_fees_depositeadding');
            $this->db->where('received_by IS NOT NULL');
            $this->db->where('received_by !=', '');
            $received_by_list = $this->db->get()->result_array();

            $response = array(
                'status' => 1,
                'message' => 'Filter options retrieved successfully',
                'data' => array(
                    'search_types' => $search_types,
                    'group_by' => $group_by,
                    'classes' => $classes,
                    'fee_types' => $fee_types,
                    'received_by' => $received_by_list
                ),
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'Error retrieving filter options',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Filter endpoint - Get other collection report with filters
     * 
     * POST /api/other-collection-report/filter
     */
    public function filter()
    {
        try {
            // Authenticate request
            if (!$this->auth_model->check_auth_client()) {
                echo json_encode(array(
                    'status' => 0,
                    'message' => 'Unauthorized access',
                    'timestamp' => date('Y-m-d H:i:s')
                ));
                return;
            }

            // Get input
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input === null) {
                $input = array();
            }

            // Extract parameters with graceful null handling
            $search_type = isset($input['search_type']) && $input['search_type'] !== '' ? $input['search_type'] : null;
            $date_from = isset($input['date_from']) && $input['date_from'] !== '' ? $input['date_from'] : null;
            $date_to = isset($input['date_to']) && $input['date_to'] !== '' ? $input['date_to'] : null;
            $class_id = isset($input['class_id']) && $input['class_id'] !== '' ? $input['class_id'] : null;
            $section_id = isset($input['section_id']) && $input['section_id'] !== '' ? $input['section_id'] : null;
            $session_id = isset($input['session_id']) && $input['session_id'] !== '' ? $input['session_id'] : null;
            $feetype_id = isset($input['feetype_id']) && $input['feetype_id'] !== '' ? $input['feetype_id'] : null;
            $received_by = isset($input['received_by']) && $input['received_by'] !== '' ? $input['received_by'] : null;
            $group = isset($input['group']) && $input['group'] !== '' ? $input['group'] : null;

            // Get date range
            if ($search_type) {
                $dates = $this->get_date_range($search_type);
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
            } elseif ($date_from && $date_to) {
                $start_date = $date_from;
                $end_date = $date_to;
            } else {
                // Default to current year
                $dates = $this->get_date_range('this_year');
                $start_date = $dates['from_date'];
                $end_date = $dates['to_date'];
            }

            // Get session ID - use provided or current
            if ($session_id === null) {
                $session_id = $this->setting_model->getCurrentSession();
            }

            // Build query for other fee collection
            $this->db->select('student_fees_depositeadding.*, students.firstname, students.middlename, students.lastname, 
                student_session.class_id, classes.class, sections.section, student_session.section_id, 
                student_session.student_id, fee_groupsadding.name, feetypeadding.type, feetypeadding.code, 
                feetypeadding.is_system, student_fees_masteradding.student_session_id, students.admission_no');
            $this->db->from('student_fees_depositeadding');
            $this->db->join('fee_groups_feetypeadding', 'fee_groups_feetypeadding.id = student_fees_depositeadding.fee_groups_feetype_id');
            $this->db->join('fee_groupsadding', 'fee_groupsadding.id = fee_groups_feetypeadding.fee_groups_id');
            $this->db->join('feetypeadding', 'feetypeadding.id = fee_groups_feetypeadding.feetype_id');
            $this->db->join('student_fees_masteradding', 'student_fees_masteradding.id = student_fees_depositeadding.student_fees_master_id');
            $this->db->join('student_session', 'student_session.id = student_fees_masteradding.student_session_id');
            $this->db->join('classes', 'classes.id = student_session.class_id');
            $this->db->join('sections', 'sections.id = student_session.section_id');
            $this->db->join('students', 'students.id = student_session.student_id');
            
            // Apply filters
            $this->db->where('student_fees_depositeadding.created_at >=', $start_date);
            $this->db->where('student_fees_depositeadding.created_at <=', $end_date . ' 23:59:59');
            $this->db->where('student_session.session_id', $session_id);
            
            if ($class_id !== null) {
                $this->db->where('student_session.class_id', $class_id);
            }
            
            if ($section_id !== null) {
                $this->db->where('student_session.section_id', $section_id);
            }
            
            if ($feetype_id !== null) {
                $this->db->where('feetypeadding.id', $feetype_id);
            }
            
            if ($received_by !== null) {
                $this->db->where('student_fees_depositeadding.received_by', $received_by);
            }
            
            $this->db->order_by('student_fees_depositeadding.created_at', 'desc');
            
            $query = $this->db->get();
            $results = $query->result_array();

            // Group results if grouping is specified
            $grouped_results = array();
            $total_amount = 0;
            
            if ($group && !empty($results)) {
                $group_by_field = $this->get_group_field($group);
                foreach ($results as $row) {
                    $key = $row[$group_by_field];
                    if (!isset($grouped_results[$key])) {
                        $grouped_results[$key] = array(
                            'group_name' => $key,
                            'records' => array(),
                            'subtotal' => 0
                        );
                    }
                    $grouped_results[$key]['records'][] = $row;
                    $grouped_results[$key]['subtotal'] += floatval($row['amount']);
                    $total_amount += floatval($row['amount']);
                }
                $grouped_results = array_values($grouped_results);
            } else {
                foreach ($results as $row) {
                    $total_amount += floatval($row['amount']);
                }
            }

            $response = array(
                'status' => 1,
                'message' => 'Other collection report retrieved successfully',
                'filters_applied' => array(
                    'search_type' => $search_type,
                    'date_from' => $start_date,
                    'date_to' => $end_date,
                    'class_id' => $class_id,
                    'section_id' => $section_id,
                    'session_id' => $session_id,
                    'feetype_id' => $feetype_id,
                    'received_by' => $received_by,
                    'group' => $group
                ),
                'summary' => array(
                    'total_records' => count($results),
                    'total_amount' => number_format($total_amount, 2, '.', '')
                ),
                'total_records' => count($results),
                'data' => $group ? $grouped_results : $results,
                'timestamp' => date('Y-m-d H:i:s')
            );

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(array(
                'status' => 0,
                'message' => 'Error retrieving other collection report',
                'error' => $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ));
        }
    }

    /**
     * Helper method to get date range based on search type
     */
    private function get_date_range($search_type)
    {
        $today = date('Y-m-d');
        
        switch ($search_type) {
            case 'today':
                return array('from_date' => $today, 'to_date' => $today);
            case 'this_week':
                return array('from_date' => date('Y-m-d', strtotime('monday this week')), 'to_date' => $today);
            case 'this_month':
                return array('from_date' => date('Y-m-01'), 'to_date' => $today);
            case 'last_month':
                return array('from_date' => date('Y-m-01', strtotime('last month')), 'to_date' => date('Y-m-t', strtotime('last month')));
            case 'this_year':
            default:
                return array('from_date' => date('Y-01-01'), 'to_date' => date('Y-12-31'));
        }
    }

    /**
     * Helper method to get group by field
     */
    private function get_group_field($group)
    {
        switch ($group) {
            case 'class':
                return 'class_id';
            case 'collection':
                return 'received_by';
            case 'mode':
                return 'payment_mode';
            default:
                return 'class_id';
        }
    }
}

