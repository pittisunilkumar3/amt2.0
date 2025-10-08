<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentfeemaster_model extends CI_Model
{

    protected $balance_group;
    protected $balance_type;

    public function __construct()
    {
        parent::__construct();
        $this->load->config('ci-blog');
        $this->load->model('setting_model');
        $this->balance_group   = $this->config->item('ci_balance_group');
        $this->balance_type    = $this->config->item('ci_balance_type');
        $this->current_session = $this->setting_model->getCurrentSession();
    }
    
    public function getStudentFees($student_session_id)
    {
        $sql    = "SELECT `student_fees_master`.*,fee_groups.name FROM `student_fees_master` INNER JOIN fee_session_groups on student_fees_master.fee_session_group_id=fee_session_groups.id INNER JOIN fee_groups on fee_groups.id=fee_session_groups.fee_groups_id  WHERE `student_session_id` = " . $student_session_id . " ORDER BY `student_fees_master`.`id`";
        $query  = $this->db->query($sql);
        $result = $query->result();
        if (!empty($result)) {
            foreach ($result as $result_key => $result_value) {
                $fee_session_group_id   = $result_value->fee_session_group_id;
                $student_fees_master_id = $result_value->id;
                $result_value->fees     = $this->getDueFeeByFeeSessionGroup($fee_session_group_id, $student_fees_master_id);

                if ($result_value->is_system != 0) {
                    $result_value->fees[0]->amount = $result_value->amount;
                }

                if ($result_value->fees[0]->due_date == 'null' || $result_value->fees[0]->due_date == '') {
                    $result_value->fees[0]->due_date = '';
                }
            }
        }

        return $result;
    }

    public function getStudentTransportFees($student_session_id, $route_pickup_point_id)
    {
        if($student_session_id != NULL && $route_pickup_point_id != NULL){

            $sql = "SELECT student_transport_fees.*,transport_feemaster.month,transport_feemaster.due_date ,route_pickup_point.fees,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id  where student_transport_fees.student_session_id=".$student_session_id." and student_transport_fees.route_pickup_point_id=".$route_pickup_point_id." ORDER BY student_transport_fees.id asc";       
            $query = $this->db->query($sql);
            return $query->result();

        }
        return false;
    }

    public function getDueFeeByFeeSessionGroup($fee_session_groups_id, $student_fees_master_id) 
    {
        $sql = "SELECT student_fees_master.*,fee_groups_feetype.id as `fee_groups_feetype_id`,`fee_groups_feetype`.`fine_amount`,IFNULL(fee_groups_feetype.amount,0) as `amount`
        ,IFNULL(fee_groups_feetype.due_date,'') as `due_date`,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_fees_master.fee_session_group_id =" . $fee_session_groups_id . " and student_fees_master.id=" . $student_fees_master_id . " order by fee_groups_feetype.due_date asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function studentDeposit($data)
    {
        $sql = "SELECT fee_groups.is_system,student_fees_master.amount as `student_fees_master_amount`, fee_groups.name as `fee_group_name`,feetype.code as `fee_type_code`,fee_groups_feetype.amount,fee_groups_feetype.due_date,`fee_groups_feetype`.`fine_amount`,IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` from student_fees_master
               INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id
              INNER JOIN fee_groups_feetype on fee_groups_feetype.fee_groups_id=fee_session_groups.fee_groups_id
              INNER JOIN fee_groups on fee_groups_feetype.fee_groups_id=fee_groups.id
              INNER JOIN feetype on fee_groups_feetype.feetype_id=feetype.id
         LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_fees_master.id =" . $data['student_fees_master_id'] . " and fee_groups_feetype.id =" . $data['fee_groups_feetype_id'];
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    public function studentTransportDeposit($student_transport_fee_id)
    {
        $sql = "SELECT student_transport_fees.*,transport_feemaster.month,transport_feemaster.due_date ,route_pickup_point.fees,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id  LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id  where student_transport_fees.id=".$this->db->escape($student_transport_fee_id);    
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    public function fee_deposit($data, $send_to, $student_fees_discount_id)
    {
        if(isset($data['student_transport_fee_id']) && !empty($data['student_transport_fee_id']) ){
            $this->db->where('student_transport_fee_id', $data['student_transport_fee_id']);
        
        }else{
            $this->db->where('student_fees_master_id', $data['student_fees_master_id']);
        $this->db->where('fee_groups_feetype_id', $data['fee_groups_feetype_id']);
        }
        unset($data['fee_category']);
        $q = $this->db->get('student_fees_deposite');
        if ($q->num_rows() > 0) {

            $desc = $data['amount_detail']['description'];
            $this->db->trans_start(); // Query will be rolled back
            $row = $q->row();
            $this->db->where('id', $row->id);
            $a                               = json_decode($row->amount_detail, true);
            $inv_no                          = max(array_keys($a)) + 1;
            $data['amount_detail']['inv_no'] = $inv_no;
            $a[$inv_no]                      = $data['amount_detail'];
            $data['amount_detail']           = json_encode($a);
            $this->db->update('student_fees_deposite', $data);

            if ($student_fees_discount_id != "") {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $row->id . "/" . $inv_no));
            }

            $this->db->trans_complete();
            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();

                return false;
            } else {
                $this->db->trans_commit();
                return json_encode(array('invoice_id' => $row->id, 'sub_invoice_id' => $inv_no));
            }
        } else {

            $this->db->trans_start(); // Query will be rolled back
            $data['amount_detail']['inv_no'] = 1;
            $desc                            = $data['amount_detail']['description'];
            $data['amount_detail']           = json_encode(array('1' => $data['amount_detail']));
            $this->db->insert('student_fees_deposite', $data);
            $inserted_id = $this->db->insert_id();
            if ($student_fees_discount_id != "") {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $inserted_id . "/" . "1"));
            }

            $this->db->trans_complete(); # Completing transaction

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                return false;
            } else {
                $this->db->trans_commit();
                return json_encode(array('invoice_id' => $inserted_id, 'sub_invoice_id' => 1));
            }
        }
    }

    public function getFeeByInvoice($invoice_id, $sub_invoice_id)
    {
        $this->db->select('`student_fees_deposite`.*,students.firstname,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,`fee_groups`.`name`, `feetype`.`type`, `feetype`.`code`,student_fees_master.student_session_id')->from('student_fees_deposite');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id= student_fees_master.student_session_id');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');
        $this->db->where('student_fees_deposite.id', $invoice_id);
        $q = $this->db->get();

        if ($q->num_rows() > 0) {
            $result = $q->row();
            $res    = json_decode($result->amount_detail);
            $a      = (array) $res;

            foreach ($a as $key => $value) {
                if ($key == $sub_invoice_id) {

                    return $result;
                }
            }
        }

        return false;
    }

    public function getDueFeesByStudent($student_session_id, $date)
    {
        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,fee_groups_feetype.fine_type,fee_groups_feetype.due_date,fee_groups_feetype.fine_percentage,fee_groups_feetype.fine_amount,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail`,students.is_active,classes.class,sections.section,feetype.type,feetype.code FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id  INNER JOIN fee_groups_feetype on student_fees_master.fee_session_group_id=fee_groups_feetype.fee_session_group_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id  INNER JOIN feetype on feetype.id= fee_groups_feetype.feetype_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_fees_master.student_session_id='" . $student_session_id . "' AND student_session.session_id='" . $this->current_session . "' and  fee_session_groups.session_id='" . $this->current_session . "'  and fee_groups_feetype.due_date <  '".$date."' ORDER BY `student_fees_master`.`id` DESC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getDueTransportFeeByStudent($student_session_id, $route_pickup_point_id, $date)
    {
        if($student_session_id != NULL && $route_pickup_point_id != NULL){

        $sql = "SELECT student_transport_fees.*,transport_feemaster.month,transport_feemaster.due_date ,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` ,route_pickup_point.fees FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id  INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id where student_transport_fees.student_session_id=".$student_session_id." and student_transport_fees.route_pickup_point_id=".$route_pickup_point_id." and transport_feemaster.due_date < '".$date."' ORDER BY student_transport_fees.id asc";
        
        $query = $this->db->query($sql);

        return $query->result();

        }
        return false;
    }

    public function getStudentProcessingFees($student_session_id)
    {
        $sql = "SELECT student_fees_processing.*,student_fees_master.student_session_id,fee_groups.id as fee_group_id,fee_groups.name,feetype.type,feetype.code,gateway_ins.unique_id,fee_groups_feetype.due_date FROM `student_fees_processing` inner join student_fees_master on student_fees_master.id=student_fees_processing.student_fees_master_id INNER JOIN fee_groups_feetype on fee_groups_feetype.id=student_fees_processing.fee_groups_feetype_id and fee_groups_feetype.fee_session_group_id=student_fees_master.fee_session_group_id INNER join feetype on feetype.id=fee_groups_feetype.feetype_id  inner join fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER join fee_groups on fee_groups.id =fee_session_groups.fee_groups_id  inner join gateway_ins on gateway_ins.id=student_fees_processing.gateway_ins_id where student_fees_master.student_session_id=" . $student_session_id. " order by student_fees_processing.id asc";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function getProcessingTransportFees($student_session_id, $route_pickup_point_id)
    {
        $sql = "SELECT student_transport_fees.*, 'Transport Fees' as `transport_fee` ,transport_feemaster.month,transport_feemaster.due_date ,route_pickup_point.fees,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,student_fees_processing.student_transport_fee_id,IFNULL(student_fees_processing.id,0) as `student_fees_processing_id`, IFNULL(student_fees_processing.amount_detail,0) as `amount_detail`,gateway_ins.unique_id
        FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id INNER JOIN student_fees_processing on student_fees_processing.student_transport_fee_id=student_transport_fees.id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id inner join gateway_ins on gateway_ins.id=student_fees_processing.gateway_ins_id where student_transport_fees.student_session_id=".$student_session_id." and student_transport_fees.route_pickup_point_id=".$route_pickup_point_id." ORDER BY student_transport_fees.id asc";

        $query = $this->db->query($sql);
        return $query->result();
    }
    
    public function getProcessingFeeByFeeSessionGroup1($fee_session_groups_id, $student_fees_master_id)
    {
        $sql = "SELECT student_fees_master.*,fee_groups_feetype.id as `fee_groups_feetype_id`,fee_groups_feetype.amount,fee_groups_feetype.due_date,fee_groups_feetype.fine_amount,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_processing.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_processing.amount_detail,0) as `amount_detail`,gateway_ins.unique_id FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_processing on student_fees_processing.student_fees_master_id=student_fees_master.id and student_fees_processing.fee_groups_feetype_id=fee_groups_feetype.id inner join gateway_ins on gateway_ins.id=student_fees_processing.gateway_ins_id WHERE student_fees_master.fee_session_group_id =" . $fee_session_groups_id . " and student_fees_master.id=" . $student_fees_master_id . " order by fee_groups_feetype.due_date ASC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get student due fee types by date
     * Simplified version for API with session support
     * Gracefully handles null parameters - null means return ALL records for that parameter
     */
    public function getStudentDueFeeTypesByDatee($date, $class_id = null, $section_id = null, $session_id = null)
    {
        // Build WHERE conditions dynamically based on provided parameters
        $where_condition = array();

        // Add session filter only if session_id is explicitly provided
        if ($session_id !== null && $session_id !== '') {
            $where_condition[] = "fee_session_groups.session_id = " . $this->db->escape($session_id);
            $where_condition[] = "student_session.session_id = " . $this->db->escape($session_id);
        }

        // Add class filter only if class_id is provided
        if ($class_id !== null && $class_id !== '') {
            $where_condition[] = "student_session.class_id = " . $this->db->escape($class_id);
        }

        // Add section filter only if section_id is provided
        if ($section_id !== null && $section_id !== '') {
            $where_condition[] = "student_session.section_id = " . $this->db->escape($section_id);
        }

        // Add due date filter (always required)
        $where_condition[] = "fee_groups_feetype.due_date <= " . $this->db->escape($date);

        // Build WHERE clause
        $where_clause = "WHERE " . implode(" AND ", $where_condition);

        $sql = "SELECT student_fees_master.*, fee_session_groups.fee_groups_id, fee_session_groups.session_id,
                fee_groups.name, fee_groups.is_system, fee_groups_feetype.amount as fee_amount,
                fee_groups_feetype.id as fee_groups_feetype_id, fee_groups_feetype.fine_type,
                fee_groups_feetype.due_date, fee_groups_feetype.fine_percentage, fee_groups_feetype.fine_amount,
                IFNULL(student_fees_deposite.id, 0) as student_fees_deposite_id,
                IFNULL(student_fees_deposite.amount_detail, 0) as amount_detail,
                students.is_active, students.admission_no, students.roll_no, students.admission_date,
                students.firstname, students.middlename, students.lastname, students.father_name,
                students.image, students.mobileno, students.email, students.state, students.city, students.pincode,
                classes.class, sections.section, feetype.type, feetype.code,
                student_session.class_id, student_session.section_id, student_session.student_id,
                student_session.id as student_session_id
                FROM student_fees_master
                INNER JOIN fee_session_groups ON fee_session_groups.id = student_fees_master.fee_session_group_id
                INNER JOIN student_session ON student_session.id = student_fees_master.student_session_id
                INNER JOIN students ON students.id = student_session.student_id
                INNER JOIN classes ON student_session.class_id = classes.id
                INNER JOIN sections ON sections.id = student_session.section_id
                INNER JOIN fee_groups_feetype ON student_fees_master.fee_session_group_id = fee_groups_feetype.fee_session_group_id
                INNER JOIN fee_groups ON fee_groups.id = fee_session_groups.fee_groups_id
                INNER JOIN feetype ON feetype.id = fee_groups_feetype.feetype_id
                LEFT JOIN student_fees_deposite ON student_fees_deposite.student_fees_master_id = student_fees_master.id
                    AND student_fees_deposite.fee_groups_feetype_id = fee_groups_feetype.id
                " . $where_clause . "
                ORDER BY students.admission_no ASC";

        $query = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    /**
     * Get student deposit by fee group fee type array
     * Simplified version for API
     */
    public function studentDepositByFeeGroupFeeTypeArray($student_session_id, $fee_type_array)
    {
        $fee_groups_feetype_ids = implode(', ', $fee_type_array);

        $sql = "SELECT student_fees_master.*, fee_session_groups.fee_groups_id, fee_session_groups.session_id,
                fee_groups.name, fee_groups.is_system, fee_groups_feetype.amount as fee_amount,
                fee_groups_feetype.id as fee_groups_feetype_id, fee_groups_feetype.fine_type,
                fee_groups_feetype.due_date, fee_groups_feetype.fine_percentage, fee_groups_feetype.fine_amount,
                IFNULL(student_fees_deposite.id, 0) as student_fees_deposite_id,
                IFNULL(student_fees_deposite.amount_detail, 0) as amount_detail,
                feetype.type, feetype.code
                FROM student_fees_master
                INNER JOIN fee_session_groups ON fee_session_groups.id = student_fees_master.fee_session_group_id
                INNER JOIN fee_groups_feetype ON student_fees_master.fee_session_group_id = fee_groups_feetype.fee_session_group_id
                INNER JOIN fee_groups ON fee_groups.id = fee_session_groups.fee_groups_id
                INNER JOIN feetype ON feetype.id = fee_groups_feetype.feetype_id
                LEFT JOIN student_fees_deposite ON student_fees_deposite.student_fees_master_id = student_fees_master.id
                    AND student_fees_deposite.fee_groups_feetype_id = fee_groups_feetype.id
                WHERE student_fees_master.student_session_id = " . $this->db->escape($student_session_id) . "
                AND fee_groups_feetype.id IN (" . $fee_groups_feetype_ids . ")
                ORDER BY fee_groups_feetype.due_date ASC";

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get current session student fees for daily collection
     * Simplified version for API
     */
    public function getCurrentSessionStudentFeess()
    {
        $sql = "SELECT student_fees_deposite.id as student_fees_deposite_id,
                student_fees_deposite.amount_detail
                FROM student_fees_deposite
                INNER JOIN fee_groups_feetype ON fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id
                WHERE fee_groups_feetype.session_id = " . $this->db->escape($this->current_session);

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get other fees for current session for daily collection
     * Simplified version for API
     */
    public function getOtherfeesCurrentSessionStudentFeess()
    {
        $sql = "SELECT student_fees_deposite.id as student_fees_deposite_id,
                student_fees_deposite.amount_detail
                FROM student_fees_deposite
                WHERE student_fees_deposite.student_transport_fee_id IS NOT NULL
                OR student_fees_deposite.fee_groups_feetype_id IS NULL";

        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get type wise balance report
     * Simplified version for API
     */
    public function gettypewisereportt($session = null, $feetype_id = null, $group_id = null, $class_id = null, $section_id = null)
    {
        $this->db->select('fee_groups.name as feegroupname, student_fees_master.id as stfeemasid,
                          fee_groups_feetype.amount as total, fee_groups_feetype.id as fgtid,
                          fee_groups_feetype.fine_amount as fine, feetype.type, sections.section,
                          classes.class, students.admission_no, students.mobileno, students.firstname,
                          students.middlename, students.lastname')
                 ->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id');
        $this->db->join('fee_session_groups', 'fee_session_groups.id = student_fees_master.fee_session_group_id');
        $this->db->join('fee_groups', 'fee_session_groups.fee_groups_id = fee_groups.id');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.fee_session_group_id = fee_session_groups.id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');

        // Apply filters
        if ($session != null) {
            $this->db->where('student_session.session_id', $session);
        }

        if ($feetype_id != null && !empty($feetype_id) && is_array($feetype_id)) {
            $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
        }

        if ($group_id != null && !empty($group_id) && is_array($group_id)) {
            $this->db->where_in('fee_groups.id', $group_id);
        }

        if ($class_id != null && !empty($class_id)) {
            if (is_array($class_id)) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        if ($section_id != null && !empty($section_id)) {
            if (is_array($section_id)) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        $this->db->order_by('students.id', 'desc');

        $query = $this->db->get();
        $results = $query->result_array();

        // Calculate paid amounts
        foreach ($results as &$result) {
            $amountres = $this->getdepositeamount($result['stfeemasid'], $result['fgtid']);
            if ($amountres) {
                $amount_detail = json_decode($amountres, true);
                $total_amount = 0;
                $total_fine = 0;
                $total_discount = 0;
                foreach ($amount_detail as $detail) {
                    $total_amount += $detail['amount'];
                    $total_discount += $detail['amount_discount'];
                    $total_fine += $detail['amount_fine'];
                }
                $result['total_amount'] = $total_amount;
                $result['total_fine'] = $total_fine;
                $result['total_discount'] = $total_discount;
                $result['balance'] = $result['total'] - $total_amount;
            } else {
                $result['total_amount'] = 0;
                $result['total_fine'] = 0;
                $result['total_discount'] = 0;
                $result['balance'] = $result['total'];
            }
        }

        return $results;
    }

    /**
     * Get deposited amount for a student fee master and fee group fee type
     * Helper method for type wise report
     */
    public function getdepositeamount($student_fees_master_id, $fee_groups_feetype_id)
    {
        $this->db->select('amount_detail');
        $this->db->from('student_fees_deposite');
        $this->db->where('student_fees_master_id', $student_fees_master_id);
        $this->db->where('fee_groups_feetype_id', $fee_groups_feetype_id);
        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->amount_detail : null;
    }

}

