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

        // Set JSON response header
        header('Content-Type: application/json');

        // Load database
        $this->load->database();

        // Add main application models path to search paths
        // This allows loading models from the main application directory
        $main_models_path = FCPATH . 'application/models/';
        $this->load->add_package_path($main_models_path);

        // Load required models
        $this->load->model('setting_model');
        $this->load->model('auth_model');
        $this->load->model('module_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->load->model('studentfeemaster_model');
        $this->load->model('staff_model'); // Needed for collector names

        // Load model from main application directory
        // The model exists in application/models/ not api/application/models/
        // FCPATH points to api/ directory, so we need to go up one level with ../
        require_once(FCPATH . '../application/models/Studentfeemasteradding_model.php');
        $this->studentfeemasteradding_model = new Studentfeemasteradding_model();
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

            // Get received by list from staff table (collectors)
            // Note: received_by is stored in JSON amount_detail field, not as a column
            // So we get the list of staff who can collect fees
            $collect_by_data = $this->studentfeemaster_model->get_feesreceived_by();

            // Convert to array format for API response
            $received_by_list = array();
            if (!empty($collect_by_data)) {
                foreach ($collect_by_data as $staff_id => $staff_name) {
                    $received_by_list[] = array(
                        'id' => $staff_id,
                        'name' => $staff_name
                    );
                }
            }

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
     *
     * This endpoint matches the behavior of the web interface at:
     * http://localhost/amt/financereports/other_collection_report
     *
     * Key differences from simple database query:
     * 1. Uses studentfeemasteradding_model->getFeeCollectionReport() method
     * 2. Parses amount_detail JSON field to extract individual payments
     * 3. Filters by date and received_by from JSON, not table columns
     * 4. Returns individual payment records, not just deposit records
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

            // Use the same model method as the web interface
            // This method:
            // 1. Queries student_fees_depositeadding with all joins
            // 2. Parses amount_detail JSON field to extract individual payments
            // 3. Filters payments by date range (from JSON)
            // 4. Filters payments by received_by (from JSON) if specified
            // 5. Returns individual payment records with full student/fee details
            $results = $this->studentfeemasteradding_model->getFeeCollectionReport(
                $start_date,
                $end_date,
                $feetype_id,
                $received_by,
                $group,
                $class_id,
                $section_id,
                $session_id
            );

            // Format results to match web interface table columns exactly
            $formatted_results = array();
            $total_amount = 0;
            $total_discount = 0;
            $total_fine = 0;
            $total_grand = 0;

            foreach ($results as $row) {
                // Calculate total for this payment (Paid + Fine - Discount)
                $payment_total = floatval($row['amount']) + floatval($row['amount_fine']) - floatval($row['amount_discount']);

                // Format collector name
                $collector_name = '';
                if (isset($row['received_byname']) && is_array($row['received_byname'])) {
                    $collector_name = $row['received_byname']['name'] . ' (' . $row['received_byname']['employee_id'] . ')';
                }

                // Build formatted record matching web interface table
                $formatted_record = array(
                    'payment_id' => $row['id'] . '/' . $row['inv_no'],
                    'date' => $row['date'],
                    'admission_no' => $row['admission_no'],
                    'student_name' => trim($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']),
                    'class' => $row['class'] . ' (' . $row['section'] . ')',
                    'fee_type' => $row['type'],
                    'collect_by' => $collector_name,
                    'mode' => $row['payment_mode'],
                    'paid' => number_format($row['amount'], 2, '.', ''),
                    'note' => isset($row['description']) ? $row['description'] : '',
                    'discount' => number_format($row['amount_discount'], 2, '.', ''),
                    'fine' => number_format($row['amount_fine'], 2, '.', ''),
                    'total' => number_format($payment_total, 2, '.', ''),
                    // Include original fields for reference
                    'raw_data' => array(
                        'id' => $row['id'],
                        'student_id' => $row['student_id'],
                        'class_id' => $row['class_id'],
                        'section_id' => $row['section_id'],
                        'received_by' => $row['received_by'],
                        'inv_no' => $row['inv_no']
                    )
                );

                $formatted_results[] = $formatted_record;

                // Calculate totals
                $total_amount += floatval($row['amount']);
                $total_discount += floatval($row['amount_discount']);
                $total_fine += floatval($row['amount_fine']);
                $total_grand += $payment_total;
            }

            // Group results if grouping is specified (matches web interface logic)
            $grouped_results = array();

            if ($group && !empty($formatted_results)) {
                // Determine group by field
                if ($group == 'class') {
                    $group_by_field = 'class_id';
                } elseif ($group == 'collection') {
                    $group_by_field = 'received_by';
                } elseif ($group == 'mode') {
                    $group_by_field = 'mode';
                } else {
                    $group_by_field = 'class_id';
                }

                // Group the results
                foreach ($formatted_results as $idx => $record) {
                    // Get grouping key
                    if ($group == 'class') {
                        $key = $results[$idx]['class_id'];
                        $key_label = $record['class'];
                    } elseif ($group == 'collection') {
                        $key = $results[$idx]['received_by'];
                        $key_label = $record['collect_by'];
                    } elseif ($group == 'mode') {
                        $key = $record['mode'];
                        $key_label = $record['mode'];
                    } else {
                        $key = 'all';
                        $key_label = 'All';
                    }

                    if (!isset($grouped_results[$key])) {
                        $grouped_results[$key] = array(
                            'group_name' => $key_label,
                            'records' => array(),
                            'subtotal_paid' => 0,
                            'subtotal_discount' => 0,
                            'subtotal_fine' => 0,
                            'subtotal_total' => 0
                        );
                    }

                    $grouped_results[$key]['records'][] = $record;
                    $grouped_results[$key]['subtotal_paid'] += floatval($record['paid']);
                    $grouped_results[$key]['subtotal_discount'] += floatval($record['discount']);
                    $grouped_results[$key]['subtotal_fine'] += floatval($record['fine']);
                    $grouped_results[$key]['subtotal_total'] += floatval($record['total']);
                }

                // Format subtotals
                foreach ($grouped_results as &$group_data) {
                    $group_data['subtotal_paid'] = number_format($group_data['subtotal_paid'], 2, '.', '');
                    $group_data['subtotal_discount'] = number_format($group_data['subtotal_discount'], 2, '.', '');
                    $group_data['subtotal_fine'] = number_format($group_data['subtotal_fine'], 2, '.', '');
                    $group_data['subtotal_total'] = number_format($group_data['subtotal_total'], 2, '.', '');
                }

                // Convert to indexed array
                $grouped_results = array_values($grouped_results);
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
                    'total_records' => count($formatted_results),
                    'total_paid' => number_format($total_amount, 2, '.', ''),
                    'total_discount' => number_format($total_discount, 2, '.', ''),
                    'total_fine' => number_format($total_fine, 2, '.', ''),
                    'grand_total' => number_format($total_grand, 2, '.', '')
                ),
                'total_records' => count($formatted_results),
                'data' => $group ? $grouped_results : $formatted_results,
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

