<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studentfeemaster_model extends MY_Model
{

    protected $balance_group;
    protected $balance_type;

    public function __construct()
    {
        parent::__construct();
        $this->load->config('ci-blog');
        $this->load->model('Module_model', 'module_model');
        $this->balance_group   = $this->config->item('ci_balance_group');
        $this->balance_type    = $this->config->item('ci_balance_type');
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    public function searchAssignFeeByClassSection($class_id = null, $section_id = null, $fee_session_group_id = null, $category = null, $gender = null, $rte = null)
    {
        $sql = "SELECT IFNULL(`student_fees_master`.`id`, '0') as `student_fees_master_id`,`classes`.`id` AS `class_id`,"
        . " `student_session`.`id` as `student_session_id`, `students`.`id`, "
        . "`classes`.`class`, `sections`.`id` AS `section_id`, `sections`.`section`, "
        . "`students`.`id`, `students`.`admission_no`, `students`.`roll_no`,"
        . " `students`.`admission_date`, `students`.`firstname`, `students`.`middlename`,`students`.`lastname`,"
        . " `students`.`image`, `students`.`mobileno`, `students`.`email`, `students`.`state`,"
        . " `students`.`city`, `students`.`pincode`, `students`.`religion`, `students`.`dob`, "
        . "`students`.`current_address`, `students`.`permanent_address`,"
        . " IFNULL(students.category_id, 0) as `category_id`,"
        . " IFNULL(categories.category, '') as `category`,"
        . " `students`.`adhar_no`, `students`.`samagra_id`,"
        . " `students`.`bank_account_no`, `students`.`bank_name`, `students`.`ifsc_code`,"
        . " `students`.`guardian_name`, `students`.`guardian_relation`, `students`.`guardian_phone`,"
        . " `students`.`guardian_address`, `students`.`is_active`, `students`.`created_at`,"
        . " `students`.`updated_at`, `students`.`father_name`, `students`.`rte`,"
        . " `students`.`gender` FROM `students` JOIN `student_session` "
        . "ON `student_session`.`student_id` = `students`.`id` JOIN `classes` "
        . "ON `student_session`.`class_id` = `classes`.`id` JOIN `sections` "
        . "ON `sections`.`id` = `student_session`.`section_id` LEFT JOIN `categories` "
        . "ON `students`.`category_id` = `categories`.`id` LEFT JOIN student_fees_master on"
        . " student_fees_master.student_session_id=student_session.id"
        . "  AND student_fees_master.fee_session_group_id=" . $this->db->escape($fee_session_group_id)
        . "WHERE `student_session`.`session_id` =  " . $this->current_session
            . " and `students`.`is_active` =  'yes'";

        if ($class_id != null) {
            $sql .= " AND `student_session`.`class_id` = " . $this->db->escape($class_id);
        }
        if ($section_id != null) {
            $sql .= " AND `student_session`.`section_id` =" . $this->db->escape($section_id);
        }
        if ($category != null) {
            $sql .= " AND `students`.`category_id` =" . $this->db->escape($category);
        }
        if ($gender != null) {
            $sql .= " AND `students`.`gender` =" . $this->db->escape($gender);
        }
        if ($rte != null) {
            $sql .= " AND `students`.`rte` =" . $this->db->escape($rte);
        }
        $sql .= " ORDER BY `students`.`id`";

        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function add($data)
    {
        $this->db->where('student_session_id', $data['student_session_id']);
        $this->db->where('fee_session_group_id', $data['fee_session_group_id']);
        $q = $this->db->get('student_fees_master');

        if ($q->num_rows() > 0) {
            return $q->row()->id;
        } else {
            $this->db->insert('student_fees_master', $data);
            $id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On student fees master id " . $id;
            $action    = "Insert";
            $record_id = $id;
            $this->log($message, $record_id, $action);
        }
    }

    public function assign_bulk_fees($fee_session_group,$student_session_id,$delete_fee_session_group)
    {
        if(!empty($fee_session_group)){
            $data_insert=array();
            foreach ($fee_session_group as $fee_session_key => $fee_session_value) {
                $array=array();
               $array['is_system']=0;
               $array['student_session_id']=$student_session_id;
               $array['fee_session_group_id']=$fee_session_value;
               $data_insert[]=$array;
            }
            $this->db->insert_batch('student_fees_master', $data_insert);
        }

        if(!empty($delete_fee_session_group)){
            $this->db->where('student_session_id', $student_session_id);
            $this->db->where_in('fee_session_group_id', $delete_fee_session_group);
            $this->db->delete('student_fees_master');
        }
    }

    public function addPreviousBal($student_data, $due_date)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $fee_group_exists = $this->feegroup_model->checkGroupExistsByName($this->balance_group);
        $fee_type_exists  = $this->feetype_model->checkFeetypeByName($this->balance_type);
        $fee_group_id     = 0;
        $fee_type_id      = 0;
        if (!$fee_group_exists) {
            $this->db->insert('fee_groups', array('name' => $this->balance_group, 'is_system' => 1));
            $fee_group_id = $this->db->insert_id();
        } else {
            $fee_group_id = $fee_group_exists->id;
        }

        if (!$fee_type_exists) {
            $this->db->insert('feetype', array('type' => $this->balance_type, 'code' => $this->balance_type, 'is_system' => 1));
            $fee_type_id = $this->db->insert_id();
        } else {
            $fee_type_id = $fee_type_exists->id;
        }
        $to_be_insert = array(
            'session_id'           => $this->current_session,
            'fee_groups_id'        => $fee_group_id,
            'feetype_id'           => $fee_type_id,
            'fee_session_group_id' => 0,
            'due_date'             => $due_date,
        );
        $parentid = $this->feesessiongroup_model->group_exists($to_be_insert['fee_groups_id']);

        $to_be_insert['fee_session_group_id'] = $parentid;

        $session_group_exists = $this->feesessiongroup_model->checkExists($to_be_insert);
        if (!$session_group_exists) {
            $this->db->insert('fee_groups_feetype', $to_be_insert);
        } else {
            $this->db->where('fee_groups_feetype.id', $session_group_exists);
            $this->db->update('fee_groups_feetype', $to_be_insert);
        }
        $student_list = array();
        if (isset($student_data) && !empty($student_data)) {

            $total_rec = count($student_data);
            for ($i = 0; $i < $total_rec; $i++) {
                $student_list[]                           = $student_data[$i]['student_session_id'];
                $student_data[$i]['id']                   = 0;
                $student_data[$i]['fee_session_group_id'] = $parentid;
            }
            $check_insert_feemaster = $this->selectInArray($parentid, $student_list);
            if (!empty($check_insert_feemaster)) {
                $insert_new_student = array();
                foreach ($student_data as $student_key => $student_value) {
                    $student_data[$student_key]['id'] = $this->findValueExists($check_insert_feemaster, $student_value['student_session_id']);
                    if ($student_data[$student_key]['id'] == 0) {
                        $insert_new_student[] = $student_data[$student_key];
                        unset($student_data[$student_key]);
                    }
                }

                if (!empty($insert_new_student)) {
                    $this->db->insert_batch('student_fees_master', $insert_new_student);
                }
                $this->db->update_batch('student_fees_master', $student_data, 'id');
            } else {
                $this->db->insert_batch('student_fees_master', $student_data);
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function findValueExists($array, $find)
    {
        $id = 0;
        foreach ($array as $x => $x_value) {
            if ($x_value->student_session_id == $find) {
                return $x_value->id;
            }
        }
        return $id;
    }

    public function selectInArray($fee_session_groups, $student_session_array)
    {
        $this->db->where('fee_session_group_id', $fee_session_groups);
        $this->db->where_in('student_session_id', $student_session_array);
        $q      = $this->db->get('student_fees_master');
        $result = $q->result();
        return $result;
    }

    public function delete($fee_session_groups, $array)
    {

        $this->db->where('fee_session_group_id', $fee_session_groups);
        $this->db->where_in('student_session_id', $array);
        $this->db->delete('student_fees_master');


    }



    public function getBalanceMasterRecord($group_name, $student_session_array)
    {
        $sql = "select * from student_fees_master where student_session_id in $student_session_array and fee_session_group_id=(SELECT id FROM `fee_session_groups` where fee_groups_id=(SELECT id FROM `fee_groups` WHERE name=" . "'" . $group_name . "'" . ") and session_id=$this->current_session)";

        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    public function getStudentFeesByClassSectionStudent($class_id=NULL,$section_id=NULL,$student_id=NULL)
    {
        $where_condition=array();
        if( $class_id !=NULL){
            $where_condition[]=" and student_session.class_id=".$class_id;
        }
        if ($section_id != NULL) {
             $where_condition[]=" and student_session.section_id=".$section_id;
        }
        if ($student_id != NULL) {
            $where_condition[]=" and student_session.student_id=".$student_id;
        }

        $where_condition_string = implode(" ", $where_condition);

        $sql = "SELECT student_fees_master.*,student_session.id as `student_session_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,students.category_id,students.image,students.id as student_id,students.father_name,students.admission_no,students.mobileno,students.roll_no,students.rte, IFNULL(categories.category, '') as `category` FROM `student_fees_master` INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id INNER JOIN classes on classes.id =student_session.class_id left join  categories on students.category_id = categories.id INNER join sections on sections.id=student_session.section_id  WHERE student_session.session_id=" . $this->db->escape($this->current_session). $where_condition_string;

        $query        = $this->db->query($sql);
        $result       = $query->result();
        $student_fees = array();
        if (!empty($result)) {

            foreach ($result as $result_key => $result_value) {
                $fee_session_group_id   = $result_value->fee_session_group_id;
                $student_fees_master_id = $result_value->id;
                $result_value->fees     = $this->getDueFeeByFeeSessionGroup($fee_session_group_id, $student_fees_master_id);

                if ($result_value->is_system != 0) {
                    $result_value->fees[0]->amount = $result_value->amount;
                }

                if (!array_key_exists($result_value->student_session_id, $student_fees)) {

                    $student_fees[$result_value->student_session_id] = array(
                           'student_session_id'=>$result_value->student_session_id,
                           'firstname'=>$result_value->firstname,
                           'student_id'=>$result_value->student_id,
                           'middlename'=>$result_value->middlename,
                           'lastname'=>$result_value->lastname,
                           'class_id'=>$result_value->class_id,
                           'class'=>$result_value->class,
                           'section'=>$result_value->section,
                           'father_name'=>$result_value->father_name,
                           'admission_no'=>$result_value->admission_no,
                           'mobileno'=>$result_value->mobileno,
                           'roll_no'=>$result_value->roll_no,
                           'category_id'=>$result_value->category_id,
                           'category'=>$result_value->category,
                           'rte'=>$result_value->rte,
                           'image'=>$result_value->image
                    ); //the magic

                $student_fees[$result_value->student_session_id]['student_discount_fee']=$this->feediscount_model->getStudentFeesDiscount($result_value->student_session_id);

                }

                $student_fees[$result_value->student_session_id]['fees'][] = $result_value->fees;

            }
        }

        return $student_fees;

    }

    public function getStudentFees($student_session_id)
    {
        $sql    = "SELECT `student_fees_master`.*, fee_groups.name, fee_session_groups.session_id, sessions.session
                  FROM `student_fees_master`
                  INNER JOIN fee_session_groups ON student_fees_master.fee_session_group_id = fee_session_groups.id
                  INNER JOIN fee_groups ON fee_groups.id = fee_session_groups.fee_groups_id
                  INNER JOIN sessions ON sessions.id = fee_session_groups.session_id
                  WHERE `student_session_id` = " . $student_session_id . "
                  ORDER BY `student_fees_master`.`id`";
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
            }
        }

        return $result;
    }

     public function getTransStudentFees($student_session_id)
    {
        $sql    = "SELECT `student_fees_master`.*,fee_groups.name FROM `student_fees_master` INNER JOIN fee_session_groups on student_fees_master.fee_session_group_id=fee_session_groups.id INNER JOIN fee_groups on fee_groups.id=fee_session_groups.fee_groups_id  WHERE `student_session_id` = " . $student_session_id . " ORDER BY `student_fees_master`.`id`";
        $query  = $this->db->query($sql);
        $result_value = $query->result();

        $class_id="";
        if(isset($_POST['class_id']) && !empty($_POST['class_id'])){
        $class_id=$_POST['class_id'];
        }
         $section_id="";
        if(isset($_POST['section_id']) && !empty($_POST['section_id'])){
        $section_id=$_POST['section_id'];
        }
        $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){
         $this->db->select('`student_fees_deposite`.*,0 as previous_balance_amount,route_pickup_point.fees as amount,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as fee_group,"Transport Fees" as name, "Transport Fees" as `fee_type`, "" as `fee_code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no, `student_session`.`id` as `student_session_id`,0 as is_system, "" as fee_session_group_id')->from('student_transport_fees');

        $this->db->join('student_fees_deposite', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`','left');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_transport_fees.route_pickup_point_id');

        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');
        $this->db->where('student_session.session_id',$this->current_session);
        $this->db->where('student_session.id',$student_session_id);
        $this->db->order_by('student_fees_deposite.id','desc');

        if($class_id!=null){
            $this->db->where('student_session.class_id',$class_id);
        }

        if($section_id!=null){
            $this->db->where('student_session.section_id',$section_id);
        }

        $query1        = $this->db->get();
        $result_value1 = $query1->result();
       }else{
        $result_value1=array();
       }
        if(empty($result_value)){
            $result_value2=$result_value1;
        }elseif(empty($result_value1)){
            $result_value2=$result_value;
        }else{
            $result_value2=array_merge($result_value,$result_value1);
        }

        if (!empty($result_value2)) {
            foreach ($result_value2 as $result_key => $result_value) {
$result_value->fees=array();
                $fee_session_group_id   = $result_value->fee_session_group_id;
                $student_fees_master_id = $result_value->id;
                if(empty($result_value->fee_session_group_id)){
$result_value->fees[0]     = (object)array('amount_detail'=>$result_value->amount_detail,'amount'=>$result_value->amount);
                }else{
$result_value->fees     = (object)$this->getDueFeeByFeeSessionGroup($fee_session_group_id, $student_fees_master_id);
                }


                if ($result_value->is_system != 0) {
                    // $result_value->fees[0]->amount = $result_value->amount;
					$result_value->fees->{"0"}->{'amount'} = $result_value->amount;
                }
            }
        }
        return $result_value2;
    }

    public function getStudentProcessingFees($student_session_id)
    {
        $sql    = "SELECT `student_fees_master`.*,fee_groups.name FROM `student_fees_master` INNER JOIN fee_session_groups on student_fees_master.fee_session_group_id=fee_session_groups.id INNER JOIN fee_groups on fee_groups.id=fee_session_groups.fee_groups_id  WHERE `student_session_id` = " . $student_session_id . " ORDER BY `student_fees_master`.`id`";
        $query  = $this->db->query($sql);
        $result = $query->result();
        if (!empty($result)) {
            foreach ($result as $result_key => $result_value) {

                $fee_session_group_id   = $result_value->fee_session_group_id;
                $student_fees_master_id = $result_value->id;
                $result_value->fees     = $this->getProcessingFeeByFeeSessionGroup($fee_session_group_id, $student_fees_master_id);
                if(!empty($result_value->fees)){
                     if ($result_value->is_system != 0) {
                    $result_value->fees[0]->amount = $result_value->amount;
                }
                }

            }
        }

        return $result;
    }
  public function getTransDueFeeByFeeSessionGroup($fee_session_groups_id, $student_fees_master_id)
    {
        $class_id="";
        if(isset($_POST['class_id']) && !empty($_POST['class_id'])){
        $class_id=$_POST['class_id'];
        }
         $section_id="";
        if(isset($_POST['section_id']) && !empty($_POST['section_id'])){
        $section_id=$_POST['section_id'];
        }

          $this->db->select('`student_fees_deposite`.*,0 as previous_balance_amount,route_pickup_point.fees as amount,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as fee_group,"Transport Fees" as name, "Transport Fees" as `fee_type`, "" as `fee_code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no, `student_session`.`id` as `student_session_id`,0 as is_system')->from('student_transport_fees');

        $this->db->join('student_fees_deposite', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`','left');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_transport_fees.route_pickup_point_id');

        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');


        $this->db->where('student_session.session_id',$this->current_session);
        $this->db->order_by('student_fees_deposite.id','desc');

        if($class_id!=null){
            $this->db->where('student_session.class_id',$class_id);
        }

        if($section_id!=null){
            $this->db->where('student_session.section_id',$section_id);
        }

        $query1        = $this->db->get();
        $result_value1 = $query1->result();

        return $result_value1;
    }
    public function getDueFeeByFeeSessionGroup($fee_session_groups_id, $student_fees_master_id)
    {

        $sql = "SELECT student_fees_master.*,fee_groups_feetype.id as `fee_groups_feetype_id`,fee_groups_feetype.amount,fee_groups_feetype.due_date,fee_groups_feetype.fine_amount,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_fees_master.fee_session_group_id =" . $fee_session_groups_id . " and student_fees_master.id=" . $student_fees_master_id . " order by fee_groups_feetype.due_date ASC";

        $query = $this->db->query($sql);

         $result_value=$query->result();
          return $result_value;
    }

    public function getProcessingFeeByFeeSessionGroup($fee_session_groups_id, $student_fees_master_id)
    {
        $sql = "SELECT student_fees_master.*,fee_groups_feetype.id as `fee_groups_feetype_id`,fee_groups_feetype.amount,fee_groups_feetype.due_date,fee_groups_feetype.fine_amount,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_processing.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_processing.amount_detail,0) as `amount_detail`,gateway_ins.unique_id FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_processing on student_fees_processing.student_fees_master_id=student_fees_master.id and student_fees_processing.fee_groups_feetype_id=fee_groups_feetype.id inner join gateway_ins on gateway_ins.id=student_fees_processing.gateway_ins_id WHERE student_fees_master.fee_session_group_id =" . $fee_session_groups_id . " and student_fees_master.id=" . $student_fees_master_id . " order by fee_groups_feetype.due_date ASC";

        $query = $this->db->query($sql);
        return $query->result();
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

    public function getTransportFeesByDueDate($start_date, $end_date)
    {
        $sql    = "SELECT student_transport_fees.*,route_pickup_point.fees,transport_feemaster.month,transport_feemaster.due_date ,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail`,students.id as `student_id`, students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,students.dob ,students.current_address,    students.permanent_address,students.category_id, IFNULL(categories.category, '') as `category`,   students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name, students.guardian_relation,students.guardian_phone,students.guardian_email,`classes`.`class`,students.guardian_address,students.is_active,`students`.`father_name`,`students`.`app_key`,`students`.`parent_app_key`,`students`.`gender`  FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id   LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN student_session on student_session.id= student_transport_fees.student_session_id INNER JOIN classes on classes.id= student_session.class_id INNER JOIN sections on sections.id= student_session.section_id INNER JOIN students on students.id=student_session.student_id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id LEFT JOIN `categories` ON `students`.`category_id` = `categories`.`id` WHERE transport_feemaster.due_date BETWEEN ". $this->db->escape($start_date) . " and " . $this->db->escape($end_date);
        $query  = $this->db->query($sql);
        $result = $query->result();

        return $result;
    }

    public function getFeesByStudentFeeMasterAndFeetype($student_fees_master_id, $fee_groups_feetype_id)
    {
        $sql = "SELECT student_fees_master.id,student_fees_master.is_system,student_fees_master.student_session_id,student_fees_master.fee_session_group_id,student_fees_master.amount as `student_fees_master_amount`,fee_groups_feetype.id as `fee_groups_feetype_id`,students.id as student_id,students.firstname,students.middlename,students.admission_no,students.lastname,student_session.class_id,classes.class,sections.section,students.guardian_name,students.guardian_phone,students.father_name,student_session.section_id,student_session.student_id,fee_groups_feetype.amount,fee_groups_feetype.due_date,fee_groups_feetype.fine_amount,fee_groups_feetype.fine_type,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id INNER JOIN student_session on student_session.id= student_fees_master.student_session_id INNER JOIN classes on classes.id= student_session.class_id INNER JOIN sections on sections.id= student_session.section_id INNER JOIN students on students.id=student_session.student_id  WHERE  student_fees_master.id=" . $student_fees_master_id . " and fee_groups_feetype.id= " . $fee_groups_feetype_id;

        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getDueFeeByFeeSessionGroupFeetype($fee_session_groups_id, $student_fees_master_id, $fee_groups_feetype_id)
    {
        $sql = "SELECT student_fees_master.id,student_fees_master.is_system,student_fees_master.student_session_id,student_fees_master.fee_session_group_id,student_fees_master.amount as `student_fees_master_amount`,fee_groups_feetype.id as `fee_groups_feetype_id`,students.id as student_id,students.firstname,students.middlename,students.admission_no,students.lastname,student_session.class_id,classes.class,sections.section,students.guardian_name,students.guardian_phone,students.father_name,student_session.section_id,student_session.student_id,fee_groups_feetype.amount,fee_groups_feetype.due_date,fee_groups_feetype.fine_amount,fee_groups_feetype.fee_groups_id,fee_groups.name,fee_groups_feetype.feetype_id,feetype.code,feetype.type, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id INNER JOIN fee_groups_feetype on  fee_groups_feetype.fee_session_group_id = fee_session_groups.id  INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id INNER JOIN student_session on student_session.id= student_fees_master.student_session_id INNER JOIN classes on classes.id= student_session.class_id INNER JOIN sections on sections.id= student_session.section_id INNER JOIN students on students.id=student_session.student_id  WHERE student_fees_master.fee_session_group_id =" . $fee_session_groups_id . " and student_fees_master.id=" . $student_fees_master_id . " and fee_groups_feetype.id= " . $fee_groups_feetype_id;

        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getTransportFeeByID($trans_fee_id)
    {
        $sql = "SELECT student_transport_fees.*,route_pickup_point.fees,transport_feemaster.month,transport_feemaster.due_date ,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,students.id as student_id,students.firstname,students.middlename,students.admission_no,students.lastname,student_session.class_id,classes.class,sections.section,students.guardian_name,students.guardian_phone,students.father_name,student_session.section_id,student_session.student_id, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id   LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN student_session on student_session.id= student_transport_fees.student_session_id INNER JOIN classes on classes.id= student_session.class_id INNER JOIN sections on sections.id= student_session.section_id INNER JOIN students on students.id=student_session.student_id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id  WHERE student_transport_fees.id=" . $trans_fee_id;
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function getHostelFeeByID($hostel_fee_id)
    {
        $sql = "SELECT student_hostel_fees.*,hostel_rooms.cost_per_bed as fees,hostel_feemaster.month,hostel_feemaster.due_date,hostel_feemaster.fine_amount, hostel_feemaster.fine_type,hostel_feemaster.fine_percentage,students.id as student_id,students.firstname,students.middlename,students.admission_no,students.lastname,student_session.class_id,classes.class,sections.section,students.guardian_name,students.guardian_phone,students.father_name,student_session.section_id,student_session.student_id, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_hostel_fees` INNER JOIN hostel_feemaster on hostel_feemaster.id =student_hostel_fees.hostel_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_hostel_fee_id=student_hostel_fees.id INNER JOIN student_session on student_session.id= student_hostel_fees.student_session_id INNER JOIN classes on classes.id= student_session.class_id INNER JOIN sections on sections.id= student_session.section_id INNER JOIN students on students.id=student_session.student_id INNER JOIN hostel_rooms on hostel_rooms.id = student_hostel_fees.hostel_room_id WHERE student_hostel_fees.id=" . $hostel_fee_id;
        $query = $this->db->query($sql);
        return $query->row();
    }

    public function fee_deposit_bulk($bulk_data, $student_fees_discount_id = null)
    {
        $this->db->trans_start();
        $fees_return=array();
        foreach ($bulk_data as $fee_key => $fee_data) {

            if ($fee_data['fee_category'] == "fees") {

                $fee_data['student_transport_fee_id']=NULL;
                $fee_data['student_hostel_fee_id']=NULL;
                $this->db->where('student_fees_master_id', $fee_data['student_fees_master_id']);
                $this->db->where('fee_groups_feetype_id', $fee_data['fee_groups_feetype_id']);

            }elseif($fee_data['student_transport_fee_id'] > 0 && $fee_data['fee_category'] == "transport"){

                $fee_data['student_fees_master_id'] =NULL;
                $fee_data['fee_groups_feetype_id'] =NULL;
                $fee_data['student_hostel_fee_id']=NULL;
                $this->db->where('student_transport_fee_id', $fee_data['student_transport_fee_id']);

            }elseif($fee_data['student_hostel_fee_id'] > 0 && $fee_data['fee_category'] == "hostel"){

                $fee_data['student_fees_master_id'] =NULL;
                $fee_data['fee_groups_feetype_id'] =NULL;
                $fee_data['student_transport_fee_id']=NULL;
                $this->db->where('student_hostel_fee_id', $fee_data['student_hostel_fee_id']);

            }

            $fee_category=$fee_data['fee_category'];
            unset($fee_data['fee_category']);

            $q = $this->db->get('student_fees_deposite');

            if ($q->num_rows() > 0) {
                $desc = $fee_data['amount_detail']['description'];
                $row  = $q->row();
                $this->db->where('id', $row->id);
                $a                                   = json_decode($row->amount_detail, true);
                $inv_no                              = max(array_keys($a)) + 1;
                $fee_data['amount_detail']['inv_no'] = $inv_no;
                $a[$inv_no]                          = $fee_data['amount_detail'];
                $fee_data['amount_detail']           = json_encode($a);
                $this->db->update('student_fees_deposite', $fee_data);

                $fees_return[]=array('invoice_id' => $row->id,
                                    'sub_invoice_id' => $inv_no,
                                    'fee_groups_feetype_id'=>$fee_data['fee_groups_feetype_id'],
                                    'student_transport_fee_id'=>$fee_data['student_transport_fee_id'],
                                    'student_hostel_fee_id'=>$fee_data['student_hostel_fee_id'],
                                    'fee_category'=>$fee_category
                                );

            }else{
                $fee_data['amount_detail']['inv_no'] = 1;
                $desc                                = $fee_data['amount_detail']['description'];
                $fee_data['amount_detail']           = json_encode(array('1' => $fee_data['amount_detail']));
                $this->db->insert('student_fees_deposite', $fee_data);
                $inserted_id = $this->db->insert_id();
				$message = INSERT_RECORD_CONSTANT . " On student fees deposite id " . $inserted_id;
				$action = "Insert";
				$record_id = $inserted_id;

                $fees_return[]=array('invoice_id' => $inserted_id,
                                        'sub_invoice_id' => 1,
                                        'fee_groups_feetype_id'=>$fee_data['fee_groups_feetype_id'],
                                        'student_transport_fee_id'=>$fee_data['student_transport_fee_id'],
                                        'student_hostel_fee_id'=>$fee_data['student_hostel_fee_id'],
                                        'fee_category'=>$fee_category
                                    );
            }
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $fees_return;
        }

    }

    public function fee_deposit($data, $send_to, $student_fees_discount_id = null)
    {
        // Enhanced debug logging for all fees
        error_log("=== FEE DEPOSIT DEBUG START ===");
        error_log("Input data to fee_deposit:");
        error_log(print_r($data, true));
        
        if (isset($data['amount_detail'])) {
            error_log("Amount detail structure:");
            error_log(print_r($data['amount_detail'], true));
            
            if (isset($data['amount_detail']['amount'])) {
                error_log("Amount value: " . $data['amount_detail']['amount']);
                error_log("Amount type: " . gettype($data['amount_detail']['amount']));
            }
        }
        
        // Debug logging for hostel fees
        if (isset($data['fee_category']) && $data['fee_category'] == "hostel") {
            error_log("HOSTEL FEE DEBUG - Model fee_deposit input:");
            error_log(print_r($data, true));
        }
        
        if ($data['fee_category'] == "fees") {
            # code...
            $this->db->where('student_fees_master_id', $data['student_fees_master_id']);
            $this->db->where('fee_groups_feetype_id', $data['fee_groups_feetype_id']);
        }elseif($data['student_transport_fee_id'] > 0 && $data['fee_category'] == "transport"){
            $this->db->where('student_transport_fee_id', $data['student_transport_fee_id']);
        }elseif($data['student_hostel_fee_id'] > 0 && $data['fee_category'] == "hostel"){
            $this->db->where('student_hostel_fee_id', $data['student_hostel_fee_id']);
            error_log("HOSTEL FEE DEBUG - Model WHERE clause set for student_hostel_fee_id: " . $data['student_hostel_fee_id']);
        }

        unset($data['fee_category']);
        
        // Debug logging after unsetting fee_category
        if (isset($data['student_hostel_fee_id'])) {
            error_log("HOSTEL FEE DEBUG - Data after unsetting fee_category:");
            error_log(print_r($data, true));
        }
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

            if ($student_fees_discount_id != null) {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $row->id . "//" . $inv_no));

				$message = UPDATE_RECORD_CONSTANT . " On  student fees discounts id " . $student_fees_discount_id;
				$action = "Update";
				$record_id = $student_fees_discount_id;
				$this->log($message, $record_id, $action);
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
            
            // Enhanced debug logging before JSON encoding
            error_log("=== BEFORE JSON ENCODING ===");
            error_log("Amount detail before encoding:");
            error_log(print_r($data['amount_detail'], true));
            
            $data['amount_detail']           = json_encode(array('1' => $data['amount_detail']));
            
            // Enhanced debug logging after JSON encoding
            error_log("=== AFTER JSON ENCODING ===");
            error_log("Final JSON string: " . $data['amount_detail']);
            
            // Debug logging before insert for hostel fees
            if (isset($data['student_hostel_fee_id'])) {
                error_log("HOSTEL FEE DEBUG - About to insert data:");
                error_log(print_r($data, true));
                error_log("HOSTEL FEE DEBUG - SQL Query: " . $this->db->get_compiled_insert('student_fees_deposite', $data));
            }
            
            $this->db->insert('student_fees_deposite', $data);
            $inserted_id = $this->db->insert_id();
            
            // Enhanced debug logging after insert
            error_log("=== AFTER INSERT ===");
            error_log("Inserted ID: " . $inserted_id);
            error_log("Last query: " . $this->db->last_query());
            
            // Debug logging after insert for hostel fees
            if (isset($data['student_hostel_fee_id'])) {
                error_log("HOSTEL FEE DEBUG - Insert completed. Inserted ID: " . $inserted_id);
                error_log("HOSTEL FEE DEBUG - Last query: " . $this->db->last_query());
            }
            if ($student_fees_discount_id != null) {
                $this->db->where('id', $student_fees_discount_id);
                $this->db->update('student_fees_discounts', array('status' => 'applied', 'description' => $desc, 'payment_id' => $inserted_id . "//" . "1"));
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

    public function get_feesreceived_by()
    {
        if ($this->session->has_userdata('admin')) {
            $getStaffRole     = $this->customlib->getStaffRole();
            $staffrole   =   json_decode($getStaffRole);

            $superadmin_visible = $this->customlib->superadmin_visible();
            if ($superadmin_visible == 'disabled' && $staffrole->id != 7) {
                $this->db->where("staff_roles.role_id !=", 7);
            }
        }

        $result = $this->db->select('CONCAT_WS(" ",staff.name,staff.surname) as name, staff.employee_id,staff.id')->from('staff')->join('staff_roles', 'staff.id=staff_roles.staff_id')->where('staff.is_active', '1')->get()->result_array();
        foreach ($result as $key => $value) {
            $data[$value['id']] = $value['name'] . " (" . $value['employee_id'] . ")";
        }
        return $data;
    }

    // public function getFeeCollectionReport($start_date, $end_date, $feetype_id= null,$received_by = null, $group = null, $class_id = null, $section_id = null)
    // {
    //     $this->db->select('`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,`fee_groups`.`name`, `feetype`.`type`, `feetype`.`code`,feetype.is_system,student_fees_master.student_session_id,students.admission_no')->from('student_fees_deposite');
    //     $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
    //     $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
    //     $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
    //     $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
    //     $this->db->join('student_session', 'student_session.id= student_fees_master.student_session_id', 'left');
    //     $this->db->join('classes', 'classes.id= student_session.class_id');
    //     $this->db->join('sections', 'sections.id= student_session.section_id');
    //     $this->db->join('students', 'students.id=student_session.student_id');
    //     if($feetype_id!=null){
    //         $this->db->where('fee_groups_feetype.feetype_id',$feetype_id);
    //     }
    //     $this->db->where('fee_groups_feetype.session_id',$this->current_session);
    //     $this->db->where('student_session.session_id',$this->current_session);
    //     // $this->db->order_by('student_fees_deposite.id','desc');

    //     if($class_id!=null){
    //         $this->db->where('student_session.class_id',$class_id);
    //     }

    //     if($section_id!=null){
    //         $this->db->where('student_session.section_id',$section_id);
    //     }

    //     $query        = $this->db->get();
    //     $result_value = $query->result();
    //     $module=$this->module_model->getPermissionByModulename('transport');
    //     if($module['is_active']){
    //       $this->db->select('`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_deposite');

    //     $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
    //     $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
    //     $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
    //     $this->db->join('classes', 'classes.id= student_session.class_id');
    //     $this->db->join('sections', 'sections.id= student_session.section_id');
    //     $this->db->join('students', 'students.id=student_session.student_id');


    //     $this->db->where('student_session.session_id',$this->current_session);
    //     // $this->db->order_by('student_fees_deposite.id','desc');

    //     if($class_id!=null){
    //         $this->db->where('student_session.class_id',$class_id);
    //     }

    //     if($section_id!=null){
    //         $this->db->where('student_session.section_id',$section_id);
    //     }

    //     $query1        = $this->db->get();
    //     $result_value1 = $query1->result();
    // }else{
    //     $result_value1=array();
    // }
    //     if($feetype_id!=null){
    //     if($feetype_id!='transport_fees'){
    //         $result_value1=array();
    //     }
    //   }
    //     if(empty($result_value)){
    //         $result_value2=$result_value1;
    //     }elseif(empty($result_value1)){
    //         $result_value2=$result_value;
    //     }else{
    //         $result_value2=array_merge($result_value,$result_value1);
    //     }


    //     $return_array = array();
    //     if (!empty($result_value2)) {
    //         $st_date = strtotime($start_date);
    //         $ed_date = strtotime($end_date);
    //         foreach ($result_value2 as $key => $value) {
    //             if ($received_by != null) {
    //                 $return = $this->findObjectByCollectId($value, $st_date, $ed_date, $received_by);
    //             } else {
    //                 $return = $this->findObjectById($value, $st_date, $ed_date);
    //             }

    //             if (!empty($return)) {
    //                 foreach ($return as $r_key => $r_value) {

    //                     $a['id']                     = $value->id;
    //                     $a['student_fees_master_id'] = $value->student_fees_master_id;
    //                     $a['fee_groups_feetype_id']  = $value->fee_groups_feetype_id;
    //                     $a['admission_no']           = $value->admission_no;
    //                     $a['firstname']              = $value->firstname;
    //                     $a['middlename']             = $value->middlename;
    //                     $a['lastname']               = $value->lastname;
    //                     $a['class_id']               = $value->class_id;
    //                     $a['class']                  = $value->class;
    //                     $a['section']                = $value->section;
    //                     $a['section_id']             = $value->section_id;
    //                     $a['student_id']             = $value->student_id;
    //                     $a['name']                   = $value->name;
    //                     $a['type']                   = $value->type;
    //                     $a['code']                   = $value->code;
    //                     $a['student_session_id']     = $value->student_session_id;
    //                     $a['is_system']              = $value->is_system;
    //                     $a['amount']                 = $r_value->amount;
    //                     $a['date']                   = $r_value->date;
    //                     $a['amount_discount']        = $r_value->amount_discount;
    //                     $a['amount_fine']            = $r_value->amount_fine;
    //                     $a['description']            = $r_value->description;
    //                     $a['payment_mode']           = $r_value->payment_mode;
    //                     $a['inv_no']                 = $r_value->inv_no;
    //                     $a['received_by']            = $r_value->received_by;
    //                     if (isset($r_value->received_by)) {

    //                         $a['received_by']     = $r_value->received_by;
    //                         $a['received_byname'] = $this->staff_model->get_StaffNameById($r_value->received_by);
    //                     } else {

    //                         $a['received_by']     = '';
    //                         $a['received_byname'] = array('name' => '', 'employee_id' => '', 'id' => '');
    //                     }

    //                     $return_array[] = $a;
    //                 }
    //             }
    //         }

    //     }

    //     return $return_array;
    // }

    public function getFeeCollectionReport($start_date, $end_date, $feetype_id= null,$received_by = null, $group = null, $class_id = null, $section_id = null,$session_id=null)
    {
        // Debug incoming parameters and types
        log_message('debug', '=== MODEL: getFeeCollectionReport called ===');
        log_message('debug', 'MODEL: Parameters received:');
        log_message('debug', '  - start_date: ' . $start_date);
        log_message('debug', '  - end_date: ' . $end_date);
        log_message('debug', '  - class_id: ' . (is_array($class_id) ? json_encode($class_id) : $class_id) . ' (type: ' . gettype($class_id) . ')');
        log_message('debug', '  - section_id: ' . (is_array($section_id) ? json_encode($section_id) : $section_id) . ' (type: ' . gettype($section_id) . ')');
        log_message('debug', '  - session_id: ' . (is_array($session_id) ? json_encode($session_id) : $session_id) . ' (type: ' . gettype($session_id) . ')');
        log_message('debug', '  - feetype_id: ' . (is_array($feetype_id) ? json_encode($feetype_id) : $feetype_id) . ' (type: ' . gettype($feetype_id) . ')');
        log_message('debug', '  - received_by: ' . (is_array($received_by) ? json_encode($received_by) : $received_by) . ' (type: ' . gettype($received_by) . ')');
        log_message('debug', '  - group: ' . $group);
        
        log_message('debug', 'MODEL: Building SELECT statement...');
        
        $this->db->select('student_fees_deposite.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,fee_groups.name, feetype.type, feetype.code,feetype.is_system,student_fees_master.student_session_id,students.admission_no')->from('student_fees_deposite');
        
        log_message('debug', 'MODEL: Adding JOINs...');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id= student_fees_master.student_session_id', 'left');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');
        
        log_message('debug', 'MODEL: Processing WHERE conditions...');
        // Handle both single values and arrays for multi-select functionality - feetype_id
        log_message('debug', 'MODEL: Processing feetype_id filter...');
        if($feetype_id != null && !empty($feetype_id)){
            if (is_array($feetype_id) && count($feetype_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_feetype_ids = array_filter($feetype_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_feetype_ids)) {
                    log_message('debug', 'MODEL: Adding feetype_id WHERE_IN condition: ' . json_encode($valid_feetype_ids));
                    $this->db->where_in('fee_groups_feetype.feetype_id', $valid_feetype_ids);
                }
            } elseif (!is_array($feetype_id) && !empty($feetype_id)) {
                log_message('debug', 'MODEL: Adding feetype_id WHERE condition: ' . $feetype_id);
                $this->db->where('fee_groups_feetype.feetype_id', $feetype_id);
            }
        } else {
            log_message('debug', 'MODEL: No feetype_id filter applied');
        }
        // $this->db->where('fee_groups_feetype.session_id',$this->current_session);

        $this->db->order_by('student_fees_deposite.id','desc');

        // Handle both single values and arrays for multi-select functionality - class_id
        log_message('debug', 'MODEL: Processing class_id filter...');
        if($class_id != null && !empty($class_id)){
            if (is_array($class_id) && count($class_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_class_ids = array_filter($class_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_class_ids)) {
                    log_message('debug', 'MODEL: Adding class_id WHERE_IN condition: ' . json_encode($valid_class_ids));
                    $this->db->where_in('student_session.class_id', $valid_class_ids);
                } else {
                    log_message('debug', 'MODEL: class_id array was empty after filtering');
                }
            } elseif (!is_array($class_id) && !empty($class_id)) {
                log_message('debug', 'MODEL: Adding class_id WHERE condition: ' . $class_id);
                $this->db->where('student_session.class_id', $class_id);
            }
        } else {
            log_message('debug', 'MODEL: No class_id filter applied (showing all classes)');
        }

        // Handle both single values and arrays for multi-select functionality - section_id
        log_message('debug', 'MODEL: Processing section_id filter...');
        if($section_id != null && !empty($section_id)){
            if (is_array($section_id) && count($section_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_section_ids = array_filter($section_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_section_ids)) {
                    log_message('debug', 'MODEL: Adding section_id WHERE_IN condition: ' . json_encode($valid_section_ids));
                    $this->db->where_in('student_session.section_id', $valid_section_ids);
                } else {
                    log_message('debug', 'MODEL: section_id array was empty after filtering');
                }
            } elseif (!is_array($section_id) && !empty($section_id)) {
                log_message('debug', 'MODEL: Adding section_id WHERE condition: ' . $section_id);
                $this->db->where('student_session.section_id', $section_id);
            }
        } else {
            log_message('debug', 'MODEL: No section_id filter applied (showing all sections)');
        }
        
        // Handle both single values and arrays for multi-select functionality - session_id
        log_message('debug', 'MODEL: Processing session_id filter...');
        if($session_id != null && !empty($session_id)){
            if (is_array($session_id) && count($session_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_session_ids = array_filter($session_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_session_ids)) {
                    log_message('debug', 'MODEL: Adding session_id WHERE_IN condition: ' . json_encode($valid_session_ids));
                    $this->db->where_in('student_session.session_id', $valid_session_ids);
                } else {
                    log_message('debug', 'MODEL: session_id array was empty after filtering');
                }
            } elseif (!is_array($session_id) && !empty($session_id)) {
                log_message('debug', 'MODEL: Adding session_id WHERE condition: ' . $session_id);
                $this->db->where('student_session.session_id', $session_id);
            }
        } else {
            log_message('debug', 'MODEL: No session_id filter applied (using current session)');
        }

        // CRITICAL FIX: Add date filtering that was missing (using created_at column)
        log_message('debug', 'MODEL: Adding date filter...');
        if (!empty($start_date) && !empty($end_date)) {
            log_message('debug', 'MODEL: Adding date WHERE conditions: ' . $start_date . ' to ' . $end_date);
            $this->db->where('DATE(student_fees_deposite.created_at) >=', $start_date);
            $this->db->where('DATE(student_fees_deposite.created_at) <=', $end_date);
        } else {
            log_message('debug', 'MODEL: No date filter applied');
        }

        // Handle received_by filter
        log_message('debug', 'MODEL: Processing received_by filter...');
        if ($received_by != null && !empty($received_by)) {
            if (is_array($received_by) && count($received_by) > 0) {
                // Ensure all values are numeric/valid
                $valid_received_by = array_filter($received_by, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_received_by)) {
                    log_message('debug', 'MODEL: Adding received_by WHERE_IN condition: ' . json_encode($valid_received_by));
                    $this->db->where_in('student_fees_deposite.received_by', $valid_received_by);
                } else {
                    log_message('debug', 'MODEL: received_by array was empty after filtering');
                }
            } elseif (!is_array($received_by) && !empty($received_by)) {
                log_message('debug', 'MODEL: Adding received_by WHERE condition: ' . $received_by);
                $this->db->where('student_fees_deposite.received_by', $received_by);
            }
        } else {
            log_message('debug', 'MODEL: No received_by filter applied');
        }

        log_message('debug', 'MODEL: Executing main query...');
        $query = $this->db->get();
        
        // Log the actual SQL query for debugging
        log_message('debug', 'MODEL: Generated SQL query: ' . $this->db->last_query());
        
        $result_value = $query->result();
        log_message('debug', 'MODEL: Main query returned ' . count($result_value) . ' results');
        $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){
          $this->db->select('`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_deposite');

        $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');


        // Handle session_id filter for transport fees (override current_session if session_id parameter provided)
        if($session_id != null && !empty($session_id)){
            if (is_array($session_id) && count($session_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_session_ids = array_filter($session_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_session_ids)) {
                    $this->db->where_in('student_session.session_id', $valid_session_ids);
                } else {
                    $this->db->where('student_session.session_id',$this->current_session);
                }
            } elseif (!is_array($session_id) && !empty($session_id)) {
                $this->db->where('student_session.session_id', $session_id);
            } else {
                $this->db->where('student_session.session_id',$this->current_session);
            }
        } else {
            $this->db->where('student_session.session_id',$this->current_session);
        }
        
        $this->db->order_by('student_fees_deposite.id','desc');

        // Handle both single values and arrays for multi-select functionality - class_id (transport fees)
        if($class_id != null && !empty($class_id)){
            if (is_array($class_id) && count($class_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_class_ids = array_filter($class_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_class_ids)) {
                    $this->db->where_in('student_session.class_id', $valid_class_ids);
                }
            } elseif (!is_array($class_id) && !empty($class_id)) {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Handle both single values and arrays for multi-select functionality - section_id (transport fees)
        if($section_id != null && !empty($section_id)){
            if (is_array($section_id) && count($section_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_section_ids = array_filter($section_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_section_ids)) {
                    $this->db->where_in('student_session.section_id', $valid_section_ids);
                }
            } elseif (!is_array($section_id) && !empty($section_id)) {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // CRITICAL FIX: Add date filtering for transport fees as well (using created_at column)
        if (!empty($start_date) && !empty($end_date)) {
            $this->db->where('DATE(student_fees_deposite.created_at) >=', $start_date);
            $this->db->where('DATE(student_fees_deposite.created_at) <=', $end_date);
        }

        // Handle received_by filter for transport fees
        if ($received_by != null && !empty($received_by)) {
            if (is_array($received_by) && count($received_by) > 0) {
                $this->db->where_in('student_fees_deposite.received_by', $received_by);
            } elseif (!is_array($received_by)) {
                $this->db->where('student_fees_deposite.received_by', $received_by);
            }
        }

        $query1        = $this->db->get();
        $result_value1 = $query1->result();
    }else{
        $result_value1=array();
    }
        // Handle both single values and arrays for multi-select functionality - feetype_id (transport fees)
        if($feetype_id != null && !empty($feetype_id)){
            $has_transport_fees = false;
            if (is_array($feetype_id)) {
                $has_transport_fees = in_array('transport_fees', $feetype_id);
            } else {
                $has_transport_fees = ($feetype_id == 'transport_fees');
            }

            if(!$has_transport_fees){
                $result_value1=array();
            }
        }
        if(empty($result_value)){
            $result_value2=$result_value1;
        }elseif(empty($result_value1)){
            $result_value2=$result_value;
        }else{
            $result_value2=array_merge($result_value,$result_value1);
        }


        $return_array = array();
        if (!empty($result_value2)) {
            $st_date = strtotime($start_date);
            $ed_date = strtotime($end_date);
            foreach ($result_value2 as $key => $value) {
                if ($received_by != null) {
                    $return = $this->findObjectByCollectId($value, $st_date, $ed_date, $received_by);
                } else {
                    $return = $this->findObjectById($value, $st_date, $ed_date);
                }

                if (!empty($return)) {
                    foreach ($return as $r_key => $r_value) {

                        $a['id']                     = $value->id;
                        $a['student_fees_master_id'] = $value->student_fees_master_id;
                        $a['fee_groups_feetype_id']  = $value->fee_groups_feetype_id;
                        $a['admission_no']           = $value->admission_no;
                        $a['firstname']              = $value->firstname;
                        $a['middlename']             = $value->middlename;
                        $a['lastname']               = $value->lastname;
                        $a['class_id']               = $value->class_id;
                        $a['class']                  = $value->class;
                        $a['section']                = $value->section;
                        $a['section_id']             = $value->section_id;
                        $a['student_id']             = $value->student_id;
                        $a['name']                   = $value->name;
                        $a['type']                   = $value->type;
                        $a['code']                   = $value->code;
                        $a['student_session_id']     = $value->student_session_id;
                        $a['is_system']              = $value->is_system;
                        $a['amount']                 = $r_value->amount;
                        $a['date']                   = $r_value->date;
                        $a['amount_discount']        = $r_value->amount_discount;
                        $a['amount_fine']            = $r_value->amount_fine;
                        $a['description']            = $r_value->description;
                        $a['payment_mode']           = $r_value->payment_mode;
                        $a['inv_no']                 = $r_value->inv_no;
                        $a['received_by']            = $r_value->received_by;
                        if (isset($r_value->received_by)) {

                            $a['received_by']     = $r_value->received_by;
                            $a['received_byname'] = $this->staff_model->get_StaffNameById($r_value->received_by);
                        } else {

                            $a['received_by']     = '';
                            $a['received_byname'] = array('name' => '', 'employee_id' => '', 'id' => '');
                        }

                        $return_array[] = $a;
                    }
                }
            }

        }

        log_message('debug', 'MODEL: Processing results and building return array...');
        log_message('debug', 'MODEL: Combined result count: ' . count($result_value2));
        log_message('debug', 'MODEL: Final return array count: ' . count($return_array));
        log_message('debug', '=== MODEL: getFeeCollectionReport completed ===');

        return $return_array;
    }

    public function getFeeBetweenDate($start_date, $end_date)
    {
        $this->db->select('`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,`fee_groups`.`name`, `feetype`.`type`, `feetype`.`code`,student_fees_master.student_session_id')->from('student_fees_deposite');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id= student_fees_master.student_session_id');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');
        $this->db->order_by('student_fees_deposite.id');
        $query        = $this->db->get();
        $result_value = $query->result();
        $return_array = array();
        if (!empty($result_value)) {
            $st_date = strtotime($start_date);
            $ed_date = strtotime($end_date);
            foreach ($result_value as $key => $value) {
                $return = $this->findObjectById($value, $st_date, $ed_date);
                if (!empty($return)) {
                    foreach ($return as $r_key => $r_value) {
                        $a['id']                     = $value->id;
                        $a['student_fees_master_id'] = $value->student_fees_master_id;
                        $a['fee_groups_feetype_id']  = $value->fee_groups_feetype_id;
                        $a['firstname']              = $value->firstname;
                        $a['lastname']               = $value->lastname;
                        $a['class_id']               = $value->class_id;
                        $a['class']                  = $value->class;
                        $a['section']                = $value->section;
                        $a['section_id']             = $value->section_id;
                        $a['student_id']             = $value->student_id;
                        $a['name']                   = $value->name;
                        $a['type']                   = $value->type;
                        $a['code']                   = $value->code;
                        $a['student_session_id']     = $value->student_session_id;
                        $a['amount']                 = $r_value->amount;
                        $a['date']                   = $r_value->date;
                        $a['amount_discount']        = $r_value->amount_discount;
                        $a['amount_fine']            = $r_value->amount_fine;
                        $a['description']            = $r_value->description;
                        $a['payment_mode']           = $r_value->payment_mode;
                        $a['inv_no']                 = $r_value->inv_no;

                        $return_array[] = $a;
                    }
                }
            }
        }

        return $return_array;
    }

    public function getDepositAmountBetweenDate($start_date, $end_date)
    {
        $this->db->select('`student_fees_deposite`.*')->from('student_fees_deposite')->join('fee_groups_feetype','fee_groups_feetype.id=student_fees_deposite.fee_groups_feetype_id')->where('fee_groups_feetype.session_id',$this->current_session);
        $this->db->order_by('student_fees_deposite.id');
        $query        = $this->db->get();
        $result_value = $query->result();
        $return_array = array();
        if (!empty($result_value)) {
            $st_date = strtotime($start_date);
            $ed_date = strtotime($end_date);
            foreach ($result_value as $key => $value) {
                $return = $this->findObjectById($value, $st_date, $ed_date);

                if (!empty($return)) {
                    foreach ($return as $r_key => $r_value) {
                        $a                    = array();
                        $a['amount']          = $r_value->amount;
                        $a['date']            = $r_value->date;
                        $a['amount_discount'] = $r_value->amount_discount;
                        $a['amount_fine']     = $r_value->amount_fine;
                        $a['description']     = $r_value->description;
                        $a['payment_mode']    = $r_value->payment_mode;
                        $a['inv_no']          = $r_value->inv_no;
                        $return_array[]       = $a;
                    }
                }
            }
        }

        return $return_array;
    }

    public function findObjectAmount($array, $st_date, $ed_date)
    {
        $ar     = json_decode($array->amount_detail);
        $array  = array();
        $amount = 0;
        for ($i = $st_date; $i <= $ed_date; $i += 86400) {
            $find = date('Y-m-d', $i);
            foreach ($ar as $row_key => $row_value) {
                if ($row_value->date == $find) {
                    $array[] = $row_value;
                }
            }
        }
        return $array;
    }

    public function findObjectById($array, $st_date, $ed_date)
    {
        $ar = json_decode($array->amount_detail);

        $array = array();
        for ($i = $st_date; $i <= $ed_date; $i += 86400) {
            $find = date('Y-m-d', $i);
            foreach ($ar as $row_key => $row_value) {
                if ($row_value->date == $find) {
                    $array[] = $row_value;
                }
            }
        }

        return $array;
    }

    public function findObjectByCollectId($array, $st_date, $ed_date, $receivedBy)
    {
        $ar = json_decode($array->amount_detail);

        $array = array();
        for ($i = $st_date; $i <= $ed_date; $i += 86400) {
            $find = date('Y-m-d', $i);
            foreach ($ar as $row_key => $row_value) {
                if (isset($row_value->received_by)) {
                    $match = false;

                    // Handle both single values and arrays for multi-select functionality - received_by
                    if (is_array($receivedBy)) {
                        $match = in_array($row_value->received_by, $receivedBy);
                    } else {
                        $match = ($row_value->received_by == $receivedBy);
                    }

                    if ($row_value->date == $find && $match) {
                        $array[] = $row_value;
                    }
                }
            }
        }

        return $array;
    }


    public function getTransportFeeByInvoice($invoice_id, $sub_invoice_id)
    {
        $this->db->select('`student_fees_deposite`.*,students.id as std_id,students.firstname,students.middlename,students.lastname,students.admission_no,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,pickup_point.name as `pickup_name`,transport_route.route_title,transport_route_id,pickup_point_id,transport_feemaster.month')->from('student_fees_deposite');
        $this->db->join('student_transport_fees', 'student_transport_fees.id = student_fees_deposite.student_transport_fee_id');
        $this->db->join('transport_feemaster', 'transport_feemaster.id = student_transport_fees.transport_feemaster_id');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_transport_fees.route_pickup_point_id');
        $this->db->join('pickup_point', 'route_pickup_point.pickup_point_id = pickup_point.id');
        $this->db->join('transport_route', 'route_pickup_point.transport_route_id = transport_route.id');
        $this->db->join('student_session', 'student_session.id= student_transport_fees.student_session_id');
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

    public function getHostelFeeByInvoice($invoice_id, $sub_invoice_id)
    {
        $this->db->select('`student_fees_deposite`.*,students.id as std_id,students.firstname,students.middlename,students.lastname,students.admission_no,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,hostel_rooms.room_no,hostel_rooms.cost_per_bed,hostel.hostel_name,hostel_feemaster.month')->from('student_fees_deposite');
        $this->db->join('student_hostel_fees', 'student_hostel_fees.id = student_fees_deposite.student_hostel_fee_id');
        $this->db->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id');
        $this->db->join('student_session', 'student_session.id= student_hostel_fees.student_session_id');
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

    public function getFeeByInvoice($invoice_id, $sub_invoice_id)
    {
        $type=$this->db->select('`student_fees_deposite`.*')->from('`student_fees_deposite`')->where('student_fees_deposite.id',$invoice_id)->get()->row_array();
        if(empty($type['student_transport_fee_id'])){
             $this->db->select('`student_fees_deposite`.*,students.id as std_id,students.firstname,students.middlename,students.lastname,students.admission_no,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,`fee_groups`.`name`, `feetype`.`type`, `feetype`.`code`,feetype.is_system,student_fees_master.student_session_id')->from('student_fees_deposite');
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
        }else{
$module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){
          $this->db->select('`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, transport_feemaster.month as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_deposite');

        $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');
        $this->db->order_by('student_fees_deposite.id','desc');
        $this->db->where('student_fees_deposite.id', $invoice_id);
        $q        = $this->db->get();
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
    }
        }






        return false;
    }

    public function studentDeposit($data)
    {
        $sql = "SELECT fee_groups.is_system,student_fees_master.amount as `student_fees_master_amount`, fee_groups.name as `fee_group_name`,feetype.code as `fee_type_code`,fee_groups_feetype.amount,fee_groups_feetype.fine_percentage,fee_groups_feetype.fine_amount,fee_groups_feetype.due_date,IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` from student_fees_master
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

    public function getStudentTransportFees($student_session_id, $route_pickup_point_id)
    {
        if($student_session_id != NULL && $route_pickup_point_id != NULL){

            $sql = "SELECT student_transport_fees.*,transport_feemaster.month,transport_feemaster.due_date ,route_pickup_point.fees,transport_feemaster.fine_amount, transport_feemaster.fine_type,transport_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_transport_fees` INNER JOIN transport_feemaster on transport_feemaster.id =student_transport_fees.transport_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_transport_fee_id=student_transport_fees.id INNER JOIN route_pickup_point on route_pickup_point.id = student_transport_fees.route_pickup_point_id  where student_transport_fees.student_session_id=".$student_session_id." and student_transport_fees.route_pickup_point_id=".$route_pickup_point_id." ORDER BY student_transport_fees.id asc";
            $query = $this->db->query($sql);
            return $query->result();

        }
        return false;
    }

    public function getStudentHostelFees($student_session_id, $hostel_room_id)
    {
        if($student_session_id != NULL && $hostel_room_id != NULL){

            $sql = "SELECT student_hostel_fees.*,hostel_feemaster.month,hostel_feemaster.due_date,hostel_rooms.cost_per_bed as fees,hostel_feemaster.fine_amount, hostel_feemaster.fine_type,hostel_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as student_fees_deposite_id, IFNULL(student_fees_deposite.amount_detail,0) as amount_detail FROM student_hostel_fees INNER JOIN hostel_feemaster on hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_hostel_fee_id = student_hostel_fees.id INNER JOIN hostel_rooms on hostel_rooms.id = student_hostel_fees.hostel_room_id WHERE student_hostel_fees.student_session_id = ".$student_session_id." AND student_hostel_fees.hostel_room_id = ".$hostel_room_id." ORDER BY student_hostel_fees.id ASC";
            $query = $this->db->query($sql);
            return $query->result();

        }
        return false;
    }

    public function studentHostelDeposit($student_hostel_fee_id)
    {
        $sql = "SELECT student_hostel_fees.*,hostel_feemaster.month,hostel_feemaster.due_date,hostel_rooms.cost_per_bed as fees,hostel_feemaster.fine_amount, hostel_feemaster.fine_type,hostel_feemaster.fine_percentage,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`, IFNULL(student_fees_deposite.amount_detail,0) as `amount_detail` FROM `student_hostel_fees` INNER JOIN hostel_feemaster on hostel_feemaster.id =student_hostel_fees.hostel_feemaster_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_hostel_fee_id=student_hostel_fees.id INNER JOIN hostel_rooms on hostel_rooms.id = student_hostel_fees.hostel_room_id where student_hostel_fees.id=".$this->db->escape($student_hostel_fee_id);
        $query = $this->db->query($sql);
        return $query->row();
    }



    public function getPreviousStudentFees($student_session_id)
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
            }
        }

        return $result;
    }

    public function fee_deposit_collections($data)
    {
        if (!empty($data)) {
            $collected_fees = array();

            foreach ($data as $d_key => $d_value) {

                if($d_value['fee_category'] == "transport"){

                    $this->db->where('student_transport_fee_id', $data[$d_key]['student_transport_fee_id']);
                    $data[$d_key]['student_fees_master_id']= NULL;
                    $data[$d_key]['fee_groups_feetype_id']= NULL;
                    $data[$d_key]['student_hostel_fee_id']= NULL;

                }elseif($d_value['fee_category'] == "hostel"){

                    $this->db->where('student_hostel_fee_id', $data[$d_key]['student_hostel_fee_id']);
                    $data[$d_key]['student_fees_master_id']= NULL;
                    $data[$d_key]['fee_groups_feetype_id']= NULL;
                    $data[$d_key]['student_transport_fee_id']= NULL;

                }elseif ($d_value['fee_category'] == "fees") {

                    $data[$d_key]['student_transport_fee_id']=NULL;
                    $data[$d_key]['student_hostel_fee_id']=NULL;
                    $this->db->where('student_fees_master_id', $data[$d_key]['student_fees_master_id']);
                    $this->db->where('fee_groups_feetype_id', $data[$d_key]['fee_groups_feetype_id']);

                }

                unset($data[$d_key]['fee_category']);

                $q = $this->db->get('student_fees_deposite');
                if ($q->num_rows() > 0) {
                    $desc = $data[$d_key]['amount_detail']['description'];
                    $row  = $q->row();
                    $this->db->where('id', $row->id);
                    $a                                       = json_decode($row->amount_detail, true);
                    $inv_no                                  = max(array_keys($a)) + 1;
                    $data[$d_key]['amount_detail']['inv_no'] = $inv_no;
                    $a[$inv_no]                              = $data[$d_key]['amount_detail'];
                    $data[$d_key]['amount_detail']           = json_encode($a);
                    $this->db->update('student_fees_deposite', $data[$d_key]);
                    // $collected_fees[] = array('invoice_id' => $row->id, 'sub_invoice_id' => $inv_no);

                    $collected_fees[] =array('invoice_id' => $row->id,
                                    'sub_invoice_id' => $inv_no,
                                    'fee_groups_feetype_id'=>$data[$d_key]['fee_groups_feetype_id'],
                                    'student_transport_fee_id'=>$data[$d_key]['student_transport_fee_id'],
                                    'student_hostel_fee_id'=>$data[$d_key]['student_hostel_fee_id'],
                                    'fee_category'=>$d_value['fee_category']
                    );

                } else {

                    $data[$d_key]['amount_detail']['inv_no'] = 1;
                    $desc                                    = $data[$d_key]['amount_detail']['description'];
                    $data[$d_key]['amount_detail']           = json_encode(array('1' => $data[$d_key]['amount_detail']));
                    $this->db->insert('student_fees_deposite', $data[$d_key]);
                    $inserted_id      = $this->db->insert_id();

                    $collected_fees[]=array('invoice_id' => $inserted_id,
                                        'sub_invoice_id' => 1,
                                        'fee_groups_feetype_id'=>$data[$d_key]['fee_groups_feetype_id'],
                                        'student_transport_fee_id'=>$data[$d_key]['student_transport_fee_id'],
                                        'student_hostel_fee_id'=>$data[$d_key]['student_hostel_fee_id'],
                                        'fee_category'=>$d_value['fee_category']
                                    );

                }
            }
            return $collected_fees;
        }
    }

    public function findOnlineObjectById($array, $st_date, $ed_date)
    {
        $ar    = json_decode($array->amount_detail);
        $mode  = array('Cheque', 'Cash', 'DD');
        $array = array();
        for ($i = $st_date; $i <= $ed_date; $i += 86400) {
            $find = date('Y-m-d', $i);
            foreach ($ar as $row_key => $row_value) {
                if ($row_value->date == $find) {
                    if (!in_array($row_value->payment_mode, $mode, true)) {
                        $array[] = $row_value;
                    }
                }
            }
        }
        return $array;
    }

    public function getOnlineFeeCollectionReport($start_date, $end_date)
    {
        $this->db->select('`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,`fee_groups`.`name`, `feetype`.`type`, `feetype`.`code`,feetype.is_system,student_fees_master.student_session_id,students.admission_no')->from('student_fees_deposite');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id= student_fees_master.student_session_id');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->order_by('student_fees_deposite.id');

        $query        = $this->db->get();
        $result_value = $query->result();
      $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){
         $this->db->select('`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_deposite');

        $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');


        $this->db->where('student_session.session_id',$this->current_session);
        $this->db->order_by('student_fees_deposite.id','desc');



        $query1        = $this->db->get();
        $result_value1 = $query1->result();
       }else{
        $result_value1 = array();
       }
        if(empty($result_value)){
            $result_value2=$result_value1;
        }elseif(empty($result_value1)){
            $result_value2=$result_value;
        }else{
            $result_value2=array_merge($result_value,$result_value1);
        }
        $return_array = array();
        if (!empty($result_value2)) {
            $st_date = strtotime($start_date);
            $ed_date = strtotime($end_date);
            foreach ($result_value2 as $key => $value) {
                $return = $this->findOnlineObjectById($value, $st_date, $ed_date);
                if (!empty($return)) {

                    foreach ($return as $r_key => $r_value) {
                        $a['id']                     = $value->id;
                        $a['student_fees_master_id'] = $value->student_fees_master_id;
                        $a['fee_groups_feetype_id']  = $value->fee_groups_feetype_id;
                        $a['firstname']              = $value->firstname;
                        $a['middlename']             = $value->middlename;
                        $a['lastname']               = $value->lastname;
                        $a['class_id']               = $value->class_id;
                        $a['class']                  = $value->class;
                        $a['section']                = $value->section;
                        $a['section_id']             = $value->section_id;
                        $a['student_id']             = $value->student_id;
                        $a['name']                   = $value->name;
                        $a['type']                   = $value->type;
                        $a['code']                   = $value->code;
                        $a['student_session_id']     = $value->student_session_id;
                        $a['admission_no']           = $value->admission_no;
                        $a['amount']                 = $r_value->amount;
                        $a['date']                   = $r_value->date;
                        $a['amount_discount']        = $r_value->amount_discount;
                        $a['amount_fine']            = $r_value->amount_fine;
                        $a['description']            = $r_value->description;
                        $a['payment_mode']           = $r_value->payment_mode;
                        $a['inv_no']                 = $r_value->inv_no;
                        $a['received_by']            = $r_value->received_by;
                        $a['is_system']              = $value->is_system;
                        $a['received_byname']        = $this->staff_model->get_StaffNameById($r_value->received_by);
                        $return_array[]              = $a;
                    }
                }
            }
        }

        return $return_array;
    }

    public function getFeesAwaiting($start_date, $end_date)
    {
        $sql    = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.amount_detail,students.firstname,students.middlename,students.is_active FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_session.session_id='" . $this->current_session . "' and  fee_session_groups.session_id='" . $this->current_session . "' and fee_groups_feetype.due_date BETWEEN '" . $start_date . "' and '" . $end_date . "' and students.is_active='yes' order by fee_groups_feetype.due_date asc";
        $query  = $this->db->query($sql);
        $result = $query->result();

        return $result;
    }

    public function getCurrentSessionStudentFees()
    {
        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.id as `student_fees_deposite_id`,student_fees_deposite.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,sections.section FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_session.session_id='" . $this->current_session . "' and  fee_session_groups.session_id='" . $this->current_session . "'";

        $query  = $this->db->query($sql);
        $result_value = $query->result();
        $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){
          $this->db->select('`student_fees_deposite`.*,student_fees_deposite.id as `student_fees_deposite_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_deposite');

        $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');


        $this->db->where('student_session.session_id',$this->current_session);
        $this->db->order_by('student_fees_deposite.id','desc');



        $query1        = $this->db->get();
        $result_value1 = $query1->result();
       }else{
        $result_value1 = array();
       }
        if(empty($result_value)){
            $result_value2=$result_value1;
        }elseif(empty($result_value1)){
            $result_value2=$result_value;
        }else{
            $result_value2=array_merge($result_value,$result_value1);
        }

        return $result_value2;
    }

    public function getFeesDepositeByIdArray($id_array = array())
    {
        $id_implode = $imp = "'" . implode("','", $id_array) . "'";
      //  print_r();die;
        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.id as `student_fees_deposite_id`,student_fees_deposite.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,sections.section FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id  JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_session.session_id='" . $this->current_session . "' and  fee_session_groups.session_id='" . $this->current_session . "' and student_fees_deposite.id in (" . $id_implode . ")";

        $query  = $this->db->query($sql);
        $result_value = $query->result();
        $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){
          $this->db->select('`student_fees_deposite`.*,student_fees_deposite.id as `student_fees_deposite_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no,students.father_name')->from('student_fees_deposite');

        $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');


        $this->db->where('student_session.session_id',$this->current_session);
        $this->db->where_in('student_fees_deposite.id',$id_array);
        // $this->db->order_by('student_fees_deposite.id','desc');



        $query1        = $this->db->get();
        $result_value1 = $query1->result();
       }else{
        $result_value1 = array();
       }
        if(empty($result_value)){
            $result_value2=$result_value1;
        }elseif(empty($result_value1)){
            $result_value2=$result_value;
        }else{
            $result_value2=array_merge($result_value,$result_value1);
        }
        return $result_value2;
    }

    public function getStudentDueFeeTypesByDate($date, $class_id = null, $section_id = null)
    {
        $where_condition = array();
        if ($class_id != null) {
            $where_condition[] = " AND student_session.class_id=" . $class_id;
        }
        if ($section_id != null) {
            $where_condition[] = "student_session.section_id=" . $section_id;
        }
        $where_condition_string = implode(" AND ", $where_condition);

        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,classes.id as class_id,sections.section,sections.id as section_id,students.id as student_id FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_session.session_id='" . $this->current_session . "' and  fee_session_groups.session_id='" . $this->current_session . "' and fee_groups_feetype.due_date <=" . $this->db->escape($date) . $where_condition_string;

        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }

    public function studentDepositByFeeGroupFeeTypeArray($student_session_id, $fee_type_array)
    {
        $fee_groups_feetype_ids = implode(', ', $fee_type_array);
        $sql = "SELECT fee_groups_feetype.*,student_fees_master.student_session_id,student_fees_master.amount as `previous_amount`,student_fees_master.is_system,student_fees_master.id as student_fees_master_id,feetype.code,feetype.type, IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`,student_fees_deposite.amount_detail,fee_groups.name as `fee_group_name` FROM `fee_groups_feetype` INNER join student_fees_master on student_fees_master.fee_session_group_id=fee_groups_feetype.fee_session_group_id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE fee_groups_feetype.id in (" . $fee_groups_feetype_ids . ") and student_fees_master.student_session_id=" . $this->db->escape($student_session_id) . "  order by fee_groups_feetype.due_date asc";
        $query                  = $this->db->query($sql);
        return $query->result();
    }


    public function fees_reminder($date,$fee_groups_feetype,$student_session)
    {
         $sql ="SELECT fee_groups_feetype.*,student_fees_master.id as `student_fees_master_id`,student_fees_master.student_session_id,IFNULL(student_fees_deposite.id,0) as `student_fees_deposite_id`,student_fees_deposite.amount_detail,feetype.code,feetype.type FROM `fee_groups_feetype` INNER join student_fees_master on student_fees_master.fee_session_group_id=fee_groups_feetype.fee_session_group_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id =fee_groups_feetype.id INNER JOIN feetype on feetype.id=fee_groups_feetype.feetype_id INNER JOIN fee_groups on fee_groups.id=fee_groups_feetype.fee_groups_id WHERE session_id=".$this->current_session." and due_date < '".$date."' and fee_groups_feetype.id not in ".$fee_groups_feetype." AND student_fees_master.student_session_id in ".$student_session." order by student_fees_master.student_session_id desc";

        $query  = $this->db->query($sql);
        return $query->result();
    }


    public function getCurrentSessionStudentFeess()
    {
        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.id as `student_fees_deposite_id`,student_fees_deposite.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,sections.section FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id ";

        $query  = $this->db->query($sql);
        $result_value = $query->result();
        $module=$this->module_model->getPermissionByModulename('transport');
        if($module['is_active']){
          $this->db->select('`student_fees_deposite`.*,student_fees_deposite.id as `student_fees_deposite_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_deposite');

        $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
        $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('students', 'students.id=student_session.student_id');


        // $this->db->where('student_session.session_id',$this->current_session);
        $this->db->order_by('student_fees_deposite.id','desc');



        $query1        = $this->db->get();
        $result_value1 = $query1->result();
       }else{
        $result_value1 = array();
       }
        if(empty($result_value)){
            $result_value2=$result_value1;
        }elseif(empty($result_value1)){
            $result_value2=$result_value;
        }else{
            $result_value2=array_merge($result_value,$result_value1);
        }

        return $result_value2;
    }

    public function getFeesDepositeByIdArrayy($id_array = array())
    {
        $id_implode = $imp = "'" . implode("','", $id_array) . "'";
        //  print_r();die;
        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.id as `student_fees_deposite_id`,student_fees_deposite.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,sections.section FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id  JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id and student_fees_deposite.id in (" . $id_implode . ")";

        $query  = $this->db->query($sql);
        $result_value = $query->result();
        $module = $this->module_model->getPermissionByModulename('transport');
        if ($module['is_active']) {
            $this->db->select('`student_fees_deposite`.*,student_fees_deposite.id as `student_fees_deposite_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no,students.father_name')->from('student_fees_deposite');

            $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
            $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
            $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
            $this->db->join('classes', 'classes.id= student_session.class_id');
            $this->db->join('sections', 'sections.id= student_session.section_id');
            $this->db->join('students', 'students.id=student_session.student_id');


            // $this->db->where('student_session.session_id', $this->current_session);
            $this->db->where_in('student_fees_deposite.id', $id_array);
            // $this->db->order_by('student_fees_deposite.id','desc');



            $query1        = $this->db->get();
            $result_value1 = $query1->result();
        } else {
            $result_value1 = array();
        }
        if (empty($result_value)) {
            $result_value2 = $result_value1;
        } elseif (empty($result_value1)) {
            $result_value2 = $result_value;
        } else {
            $result_value2 = array_merge($result_value, $result_value1);
        }
        return $result_value2;
    }

    public function getStudentDueFeeTypesByDatee($date, $class_id = null, $section_id = null)
    {
        $where_condition = array();
        if ($class_id != null) {
            $where_condition[] = "student_session.class_id = " . $class_id;
        }
        if ($section_id != null) {
            $where_condition[] = "student_session.section_id = " . $section_id;
        }
        $where_condition_string = '';
        if (!empty($where_condition)) {
            $where_condition_string = " AND " . implode(" AND ", $where_condition);
        }

        $sql = "SELECT student_fees_master.*, fee_session_groups.fee_groups_id, fee_session_groups.session_id, fee_groups.name, fee_groups.is_system, fee_groups_feetype.amount as `fee_amount`, fee_groups_feetype.id as fee_groups_feetype_id, student_fees_deposite.amount_detail, students.admission_no, students.roll_no, students.admission_date, students.firstname, students.middlename, students.lastname, students.father_name, students.image, students.mobileno, students.email, students.state, students.city, students.pincode, students.is_active, classes.class, classes.id as class_id, sections.section, sections.id as section_id, students.id as student_id
                FROM `student_fees_master`
                INNER JOIN fee_session_groups on fee_session_groups.id = student_fees_master.fee_session_group_id
                INNER JOIN student_session on student_session.id = student_fees_master.student_session_id
                INNER JOIN students on students.id = student_session.student_id
                INNER JOIN classes on student_session.class_id = classes.id
                INNER JOIN sections on sections.id = student_session.section_id
                INNER JOIN fee_groups on fee_groups.id = fee_session_groups.fee_groups_id
                INNER JOIN fee_groups_feetype on fee_groups.id = fee_groups_feetype.fee_groups_id
                LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id = student_fees_master.id and student_fees_deposite.fee_groups_feetype_id = fee_groups_feetype.id
                WHERE fee_groups_feetype.due_date <= " . $this->db->escape($date) .
                $where_condition_string;

        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }


    public function getStudentDueFeeTypesByDateee($date, $class_id = null, $section_id = null)
    {
        $where_condition = array();
        if ($class_id != null) {
            $where_condition[] = " AND student_session.class_id=" . $class_id;
        }
        if ($section_id != null) {
            $where_condition[] = "student_session.section_id=" . $section_id;
        }
        $where_condition_string = implode(" AND ", $where_condition);

        $sql = "SELECT student_fees_master.*,fee_session_groups.fee_groups_id,fee_session_groups.session_id,fee_groups.name,fee_groups.is_system,fee_groups_feetype.amount as `fee_amount`,fee_groups_feetype.id as fee_groups_feetype_id,student_fees_deposite.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,classes.id as class_id,sections.section,sections.id as section_id,students.id as student_id FROM `student_fees_master` INNER JOIN fee_session_groups on fee_session_groups.id=student_fees_master.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_master.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groups on fee_groups.id=fee_session_groups.fee_groups_id INNER JOIN fee_groups_feetype on fee_groups.id=fee_groups_feetype.fee_groups_id LEFT JOIN student_fees_deposite on student_fees_deposite.student_fees_master_id=student_fees_master.id and student_fees_deposite.fee_groups_feetype_id=fee_groups_feetype.id WHERE student_session.session_id='" . $this->current_session . "' and fee_groups_feetype.due_date <=" . $this->db->escape($date) . $where_condition_string;

        $query  = $this->db->query($sql);
        $result = $query->result();
        return $result;
    }



    public function getOtherfeesCurrentSessionStudentFeess()
    {
        // $sql = "SELECT student_fees_masteradding.*,fee_session_groupsadding.fee_groups_id,fee_session_groupsadding.session_id,fee_groupsadding.name,fee_groupsadding.is_system,fee_groups_feetypeadding.amount as `fee_amount`,fee_groups_feetypeadding.id as fee_groups_feetype_id,student_fees_depositeadding.id as `student_fees_deposite_id`,student_fees_depositeadding.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,sections.section FROM `student_fees_masteradding` INNER JOIN fee_session_groupsadding on fee_session_groupsadding.id=student_fees_masteradding.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_masteradding.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groupsadding on fee_groupsadding.id=fee_session_groupsadding.fee_groups_id INNER JOIN fee_groups_feetypeadding on fee_groupsadding.id=fee_groups_feetypeadding.fee_groups_id LEFT JOIN student_fees_depositeadding on student_fees_depositeadding.student_fees_master_id=student_fees_masteradding.id and student_fees_depositeadding.fee_groups_feetype_id=fee_groups_feetypeadding.id ";

        $sql="SELECT
            student_fees_masteradding.*,
            fee_session_groupsadding.fee_groups_id,
            fee_session_groupsadding.session_id,
            fee_groupsadding.name,
            fee_groupsadding.is_system,
            fee_groups_feetypeadding.amount AS fee_amount,
            fee_groups_feetypeadding.id AS fee_groups_feetype_id,
            student_fees_depositeadding.id AS student_fees_deposite_id,
            student_fees_depositeadding.amount_detail,
            students.admission_no,
            students.roll_no,
            students.admission_date,
            students.firstname,
            students.middlename,
            students.lastname,
            students.father_name,
            students.image,
            students.mobileno,
            students.email,
            students.state,
            students.city,
            students.pincode,
            students.is_active,
            classes.class,
            sections.section
        FROM
            student_fees_masteradding
        INNER JOIN
            fee_session_groupsadding ON fee_session_groupsadding.id = student_fees_masteradding.fee_session_group_id
        INNER JOIN
            student_session ON student_session.id = student_fees_masteradding.student_session_id
        INNER JOIN
            students ON students.id = student_session.student_id
        INNER JOIN
            classes ON student_session.class_id = classes.id
        INNER JOIN
            sections ON sections.id = student_session.section_id
        INNER JOIN
            fee_groupsadding ON fee_groupsadding.id = fee_session_groupsadding.fee_groups_id
        INNER JOIN
            fee_groups_feetypeadding ON fee_groupsadding.id = fee_groups_feetypeadding.fee_groups_id
        LEFT JOIN
            student_fees_depositeadding ON student_fees_depositeadding.student_fees_master_id = student_fees_masteradding.id
            AND student_fees_depositeadding.fee_groups_feetype_id = fee_groups_feetypeadding.id;";

        $query  = $this->db->query($sql);
        // $result_value = $query->result();
        return $query->result();
        // $module=$this->module_model->getPermissionByModulename('transport');
        $result_value1 = array();
    //     if($module['is_active']){
    //       $this->db->select('`student_fees_depositeadding`.*,student_fees_depositeadding.id as `student_fees_deposite_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no')->from('student_fees_depositeadding');

    //     $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_depositeadding`.`student_transport_fee_id`');
    //     $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
    //     $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
    //     $this->db->join('classes', 'classes.id= student_session.class_id');
    //     $this->db->join('sections', 'sections.id= student_session.section_id');
    //     $this->db->join('students', 'students.id=student_session.student_id');


    //     // $this->db->where('student_session.session_id',$this->current_session);
    //     $this->db->order_by('student_fees_depositeadding.id','desc');



    //     $query1        = $this->db->get();
    //     $result_value1 = $query1->result();
    //    }else{
    //     $result_value1 = array();
    //    }
        if(empty($result_value)){
            $result_value2=$result_value1;
        }elseif(empty($result_value1)){
            $result_value2=$result_value;
        }else{
            $result_value2=array_merge($result_value,$result_value1);
        }

        return $result_value2;
    }


    public function getotherFeesDepositeByIdArrayy($id_array = array())
    {
        $id_implode = $imp = "'" . implode("','", $id_array) . "'";
        //  print_r();die;
        $sql = "SELECT student_fees_masteradding.*,fee_session_groupsadding.fee_groups_id,fee_session_groupsadding.session_id,fee_groupsadding.name,fee_groupsadding.is_system,fee_groups_feetypeadding.amount as `fee_amount`,fee_groups_feetypeadding.id as fee_groups_feetype_id,student_fees_depositeadding.id as `student_fees_deposite_id`,student_fees_depositeadding.amount_detail,students.admission_no , students.roll_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.father_name,students.image, students.mobileno, students.email ,students.state ,   students.city , students.pincode ,students.is_active,classes.class,sections.section FROM `student_fees_masteradding` INNER JOIN fee_session_groupsadding on fee_session_groupsadding.id=student_fees_masteradding.fee_session_group_id INNER JOIN student_session on student_session.id=student_fees_masteradding.student_session_id INNER JOIN students on students.id=student_session.student_id inner join classes on student_session.class_id=classes.id INNER JOIN sections on sections.id=student_session.section_id inner join fee_groupsadding on fee_groupsadding.id=fee_session_groupsadding.fee_groups_id INNER JOIN fee_groups_feetypeadding on fee_groupsadding.id=fee_groups_feetypeadding.fee_groups_id  JOIN student_fees_depositeadding on student_fees_depositeadding.student_fees_master_id=student_fees_masteradding.id and student_fees_depositeadding.fee_groups_feetype_id=fee_groups_feetypeadding.id and student_fees_depositeadding.id in (" . $id_implode . ")";

        $query  = $this->db->query($sql);
        $result_value = $query->result();
        $result_value1 = array();
        // $module = $this->module_model->getPermissionByModulename('transport');
        // if ($module['is_active']) {
        //     $this->db->select('`student_fees_depositeadding`.*,student_fees_depositeadding.id as `student_fees_deposite_id`,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,"Transport Fees" as name, "Transport Fees" as `type`, "" as `code`,0 as is_system,student_transport_fees.student_session_id,students.admission_no,students.father_name')->from('student_fees_depositeadding');

        //     $this->db->join('student_transport_fees', 'student_transport_fees.id = `student_fees_deposite`.`student_transport_fee_id`');
        //     $this->db->join('transport_feemaster', '`student_transport_fees`.`transport_feemaster_id` = `transport_feemaster`.`id`');
        //     $this->db->join('student_session', 'student_session.id= `student_transport_fees`.`student_session_id`', 'INNER');
        //     $this->db->join('classes', 'classes.id= student_session.class_id');
        //     $this->db->join('sections', 'sections.id= student_session.section_id');
        //     $this->db->join('students', 'students.id=student_session.student_id');


        //     // $this->db->where('student_session.session_id', $this->current_session);
        //     $this->db->where_in('student_fees_depositeadding.id', $id_array);
        //     // $this->db->order_by('student_fees_deposite.id','desc');



        //     $query1        = $this->db->get();
        //     $result_value1 = $query1->result();
        // } else {
        //     $result_value1 = array();
        // }
        if (empty($result_value)) {
            $result_value2 = $result_value1;
        } elseif (empty($result_value1)) {
            $result_value2 = $result_value;
        } else {
            $result_value2 = array_merge($result_value, $result_value1);
        }
        return $result_value2;
    }


    // public function gettypewisereportt($session=null, $feetype_id=null, $class_id=null, $section_id=null) {
    //     $this->db->select('student_fees_deposite.amount_detail, fee_groups_feetype.amount as total, fee_groups_feetype.fine_amount as fine, feetype.type, sections.section, classes.class, students.admission_no, students.firstname, students.middlename, students.lastname')
    //              ->from('students')
    //              ->join('student_session', 'student_session.student_id = students.id')
    //              ->join('classes', 'classes.id = student_session.class_id')
    //              ->join('sections', 'sections.id = student_session.section_id')
    //              ->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id')
    //              ->join('student_fees_deposite', 'student_fees_deposite.student_fees_master_id = student_fees_master.id')
    //              ->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id')
    //              ->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id')
    //              ->where('fee_groups_feetype.feetype_id', $feetype_id)
    //              ->where('student_session.session_id', $session)
    //              ->where('student_session.class_id', $class_id)
    //              ->where('student_session.section_id', $section_id)
    //              ->order_by('students.id', 'desc');

    //     $query = $this->db->get();
    //     $results = $query->result_array();

    //     // Calculate total amount from amount_detail JSON
    //     foreach ($results as &$result) {
    //         $amount_detail = json_decode($result['amount_detail'], true);

    //         $total_amount = 0;
    //         foreach ($amount_detail as $detail) {
    //             $total_amount += $detail['amount'];
    //         }

    //         // Add the calculated total amount to the result array
    //         $result['total_amount'] = $total_amount;
    //     }

    //     return $results;
    // }




    // public function gettypewisereportt($session=null, $feetype_id=null,$group_id=null,$class_id=null, $section_id=null) {
    //     $this->db->select('fee_groups.name as feegroupname,student_fees_master.id as stfeemasid,fee_groups_feetype.amount as total, fee_groups_feetype.id as fgtid,fee_groups_feetype.fine_amount as fine, feetype.type, sections.section, classes.class, students.admission_no, students.firstname, students.middlename,students.mobileno, students.lastname')->from('students');
    //              $this->db->join('student_session', 'student_session.student_id = students.id');
    //              $this->db->join('classes', 'classes.id = student_session.class_id');
    //              $this->db->join('sections', 'sections.id = student_session.section_id');
    //              $this->db->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id');
    //              $this->db->join('fee_session_groups','fee_session_groups.id=student_fees_master.fee_session_group_id');
    //             //  $this->db->where('fee_session_groups.session_id',$session);

    //             // $this->db->where('fee_session_groups.fee_groups_id',$group_id);
    //             $this->db->join('fee_groups','fee_groups.id=fee_session_groups.fee_groups_id');
    //             $this->db->join('fee_groups_feetype', 'fee_groups_feetype.fee_groups_id = fee_session_groups.fee_groups_id');
    //             $this->db->where('fee_groups_feetype.feetype_id', $feetype_id);
    //             $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
    //             $this->db->where('student_session.session_id', $session);
    //             if($group_id != null){
    //                 $this->db->where('fee_groups.id',$group_id);
    //             }
    //             if($class_id != null){
    //                 $this->db->where('student_session.class_id', $class_id);
    //             }
    //             if($section_id != null){
    //                 $this->db->where('student_session.section_id', $section_id);
    //             }
    //             $this->db->order_by('students.id', 'desc');
    //             //  ->join('student_fees_deposite','student_fees_deposite.student_fees_master_id=student_fees_master.id','left');

    //     $query = $this->db->get();
    //     $results = $query->result_array();

    //     foreach ($results as &$result) {
    //         $amountres=$this->getdepositeamount($result['stfeemasid'],$result['fgtid']);
    //         if($amountres){
    //             $amount_detail = json_decode($amountres, true);
    //             $total_amount = 0;
    //             $total_fine =0;
    //             $total_discount =0;
    //             foreach ($amount_detail as $detail) {
    //                 $total_amount += $detail['amount'];
    //                 $total_discount += $detail['amount_discount'];
    //                 $total_fine += $detail['amount_fine'];
    //             }
    //             $result['total_amount'] = $total_amount;
    //             $result['total_fine'] = $total_fine;
    //             $result['total_discount']=$total_discount;
    //         }else{
    //             $result['total_amount']=0;
    //         }
    //     }

    //     return $results;
    // }


    // public function gettypewisereportt($session=null, $feetype_id=null, $class_id=null, $section_id=null) {
    //     $this->db->select('student_fees_master.id as stfeemasid,fee_groups_feetype.amount as total, fee_groups_feetype.id as fgtid,fee_groups_feetype.fine_amount as fine, feetype.type, sections.section, classes.class, students.admission_no, students.firstname, students.middlename, students.lastname')
    //              ->from('students')
    //              ->join('student_session', 'student_session.student_id = students.id')
    //              ->join('classes', 'classes.id = student_session.class_id')
    //              ->join('sections', 'sections.id = student_session.section_id')
    //              ->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id')
    //              ->join('fee_session_groups','fee_session_groups.id=student_fees_master.fee_session_group_id')
    //              ->where('fee_session_groups.session_id',$session)
    //              ->join('fee_groups_feetype', 'fee_groups_feetype.fee_groups_id = fee_session_groups.fee_groups_id')
    //              ->where('fee_groups_feetype.feetype_id', $feetype_id)
    //              ->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id')
    //              ->where('student_session.session_id', $session)
    //              ->where('student_session.class_id', $class_id)
    //              ->where('student_session.section_id', $section_id)
    //              ->order_by('students.id', 'desc');
    //             //  ->join('student_fees_deposite','student_fees_deposite.student_fees_master_id=student_fees_master.id','left');

    //     $query = $this->db->get();
    //     $results = $query->result_array();

    //     foreach ($results as &$result) {
    //         $amountres=$this->getdepositeamount($result['stfeemasid'],$result['fgtid']);
    //         if($amountres){
    //             $amount_detail = json_decode($amountres, true);
    //             $total_amount = 0;
    //             foreach ($amount_detail as $detail) {
    //                 $total_amount += $detail['amount'];
    //                 $total_amount += $detail['amount_discount'];
    //             }
    //             $result['total_amount'] = $total_amount;
    //         }else{
    //             $result['total_amount']=0;
    //         }
    //     }

    //     return $results;
    // }

    //   public function gettypewisereportt($session=null, $feetype_id=null,$group_id=null,$class_id=null, $section_id=null) {
    //     $this->db->distinct();
    //     $this->db->select('fee_groups.name as feegroupname,student_fees_master.id as stfeemasid,fee_groups_feetype.amount as total, fee_groups_feetype.id as fgtid,fee_groups_feetype.fine_amount as fine, feetype.type, sections.section, classes.class, students.admission_no, students.firstname, students.middlename, students.lastname')->from('students');
    //     $this->db->join('student_session', 'student_session.student_id = students.id');
    //     $this->db->join('classes', 'classes.id = student_session.class_id');
    //     $this->db->join('sections', 'sections.id = student_session.section_id');
    //     $this->db->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id');
    //     $this->db->join('fee_session_groups','fee_session_groups.id=student_fees_master.fee_session_group_id');
    //     //  $this->db->where('fee_session_groups.session_id',$session);

    //     // $this->db->where('fee_session_groups.fee_groups_id',$group_id);
    //     $this->db->join('fee_groups','fee_groups.id=fee_session_groups.fee_groups_id');
    //     $this->db->join('fee_groups_feetype', 'fee_groups_feetype.fee_groups_id = fee_session_groups.fee_groups_id');

    //     if($feetype_id != []){
    //         $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
    //     }

    //     // if (!empty($feetype_id) && is_array($feetype_id)) {
    //     //     $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
    //     // }
    //     if (!empty($feetype_id) && is_array($feetype_id)) {
    //         $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
    //     }

    //     $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
    //     $this->db->where('student_session.session_id', $session);
    //     if($group_id != []){
    //         $this->db->where_in('fee_groups.id',$group_id);
    //     }
    //     if($class_id != null){
    //         $this->db->where('student_session.class_id', $class_id);
    //     }
    //     if($section_id != null){
    //         $this->db->where('student_session.section_id', $section_id);
    //     }
    //     $this->db->order_by('students.id', 'desc');
    //     //  ->join('student_fees_deposite','student_fees_deposite.student_fees_master_id=student_fees_master.id','left');

    //     $query = $this->db->get();
    //     $results = $query->result_array();

    //     foreach ($results as &$result) {
    //         $amountres=$this->getdepositeamount($result['stfeemasid'],$result['fgtid']);
    //         if($amountres){
    //             $amount_detail = json_decode($amountres, true);
    //             $total_amount = 0;
    //             $total_fine =0;
    //             $total_discount =0;
    //             foreach ($amount_detail as $detail) {
    //                 $total_amount += $detail['amount'];
    //                 $total_discount += $detail['amount_discount'];
    //                 $total_fine += $detail['amount_fine'];
    //             }
    //             $result['total_amount'] = $total_amount;
    //             $result['total_fine'] = $total_fine;
    //             $result['total_discount']=$total_discount;
    //         }else{
    //             $result['total_amount']=0;
    //         }
    //     }

    //     return $results;
    // }



    public function gettypewisereportt($session=null, $feetype_id=null,$group_id=null,$class_id=null, $section_id=null) {
        $this->db->select('fee_groups.name as feegroupname,student_fees_master.id as stfeemasid,fee_groups_feetype.amount as total, fee_groups_feetype.id as fgtid,fee_groups_feetype.fine_amount as fine, feetype.type, sections.section, classes.class, students.admission_no,students.mobileno, students.firstname, students.middlename, students.lastname')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'classes.id = student_session.class_id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id');
        $this->db->join('fee_session_groups','fee_session_groups.id=student_fees_master.fee_session_group_id');
        $this->db->join('fee_groups','fee_session_groups.fee_groups_id=fee_groups.id');

        // Fix the problematic join - use fee_session_group_id instead of fee_groups_id to avoid duplicates
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.fee_session_group_id = fee_session_groups.id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');

        // Apply filters
        $this->db->where('student_session.session_id', $session);

        if($feetype_id != [] && !empty($feetype_id) && is_array($feetype_id)){
            $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
        }

        if($group_id != [] && !empty($group_id) && is_array($group_id)){
            $this->db->where_in('fee_groups.id',$group_id);
        }

        // Handle both single values and arrays for multi-select functionality - class_id
        if($class_id != null && !empty($class_id)){
            if (is_array($class_id) && count($class_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_class_ids = array_filter($class_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_class_ids)) {
                    $this->db->where_in('student_session.class_id', $valid_class_ids);
                }
            } elseif (!is_array($class_id) && !empty($class_id)) {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Handle both single values and arrays for multi-select functionality - section_id
        if($section_id != null && !empty($section_id)){
            if (is_array($section_id) && count($section_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_section_ids = array_filter($section_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_section_ids)) {
                    $this->db->where_in('student_session.section_id', $valid_section_ids);
                }
            } elseif (!is_array($section_id) && !empty($section_id)) {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Group by student and fee type to eliminate duplicates
        $this->db->group_by('students.id, fee_groups_feetype.id');
        $this->db->order_by('students.id', 'desc');

        $query = $this->db->get();
        $results = $query->result_array();

        foreach ($results as &$result) {
            $amountres=$this->getdepositeamount($result['stfeemasid'],$result['fgtid']);
            if($amountres){
                $amount_detail = json_decode($amountres, true);
                $total_amount = 0;
                $total_fine =0;
                $total_discount =0;
                foreach ($amount_detail as $detail) {
                    $total_amount += $detail['amount'];
                    $total_discount += $detail['amount_discount'];
                    $total_fine += $detail['amount_fine'];
                }
                $result['total_amount'] = $total_amount;
                $result['total_fine'] = $total_fine;
                $result['total_discount']=$total_discount;
            }else{
                $result['total_amount']=0;
                $result['total_fine']=0;
                $result['total_discount']=0;
            }
        }

        return $results;
    }




    public function getdepositeamount($studetmasterid, $feegrouptypeid) {
        $this->db->select('amount_detail')
                 ->from('student_fees_deposite')
                 ->where('student_fees_master_id', $studetmasterid)
                 ->where('fee_groups_feetype_id', $feegrouptypeid);

        $query = $this->db->get();
        $result = $query->row();

        return $result ? $result->amount_detail : null;
    }


    public function getTypewiseReport($session=null,$feetype_id= null,$class_id = null, $section_id = null)
    {
        $this->db->select('fee_groups_feetype.amount as total_amount,`student_fees_deposite`.*,students.firstname,students.middlename,students.lastname,student_session.class_id,classes.class,sections.section,student_session.section_id,student_session.student_id,`fee_groups`.`name`, `feetype`.`type`, `feetype`.`code`,feetype.is_system,student_fees_master.student_session_id,students.admission_no')->from('students');
        $this->db->join('fee_groups_feetype', 'fee_groups_feetype.id = student_fees_deposite.fee_groups_feetype_id');
        $this->db->join('fee_groups', 'fee_groups.id = fee_groups_feetype.fee_groups_id');
        $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id');
        $this->db->join('student_fees_master', 'student_fees_master.id=student_fees_deposite.student_fees_master_id');
        $this->db->join('student_session', 'student_session.id= students.id');
        $this->db->join('classes', 'classes.id= student_session.class_id');
        $this->db->join('sections', 'sections.id= student_session.section_id');
        $this->db->join('student_fees_deposite.student_fees_master_id=student_fees_master.id');

        if($feetype_id!=null){
            $this->db->where('fee_groups_feetype.feetype_id',$feetype_id);
        }
        // $this->db->where('fee_groups_feetype.session_id',$this->current_session);
        if($session != null ){
            $this->db->where('student_session.session_id',$session);
        }
        $this->db->order_by('student_fees_deposite.id','desc');

        // Handle both single values and arrays for multi-select functionality - class_id
        if($class_id != null && !empty($class_id)){
            if (is_array($class_id) && count($class_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_class_ids = array_filter($class_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_class_ids)) {
                    $this->db->where_in('student_session.class_id', $valid_class_ids);
                }
            } elseif (!is_array($class_id) && !empty($class_id)) {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Handle both single values and arrays for multi-select functionality - section_id
        if($section_id != null && !empty($section_id)){
            if (is_array($section_id) && count($section_id) > 0) {
                // Ensure all values are numeric/valid
                $valid_section_ids = array_filter($section_id, function($id) {
                    return !empty($id) && is_numeric($id);
                });
                if (!empty($valid_section_ids)) {
                    $this->db->where_in('student_session.section_id', $valid_section_ids);
                }
            } elseif (!is_array($section_id) && !empty($section_id)) {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        $query        = $this->db->get();
        $result_value = $query->result();

        return $result_value;
    }

   public function adddiscountstudentfee($data){
        $this->db->insert('fees_discount_approval', $data);
        // $id = $this->db->insert_id();
        // $message   = INSERT_RECORD_CONSTANT . " On fees_discount_approval id " . $id;
        // $action    = "Insert";
        // $record_id = $id;
        // $this->log($message, $record_id, $action);
    }

    public function getFeeCollectionReportColumnwise($start_date, $end_date, $feetype_id = null, $received_by = null, $group = null, $class_id = null, $section_id = null, $session_id = null)
    {
        try {
            log_message('debug', 'getFeeCollectionReportColumnwise called with params: start_date=' . $start_date . ', end_date=' . $end_date);

            // Use the existing working method and enhance the data structure
            $original_data = $this->getFeeCollectionReport($start_date, $end_date, $feetype_id, $received_by, $group, $class_id, $section_id, $session_id);

            log_message('debug', 'Original data count: ' . count($original_data));

            // Get ALL fee types for the selected filters to ensure complete column structure
            $all_fee_types = $this->getFeeTypesForColumnwise($start_date, $end_date, $feetype_id, $class_id, $section_id, $session_id);

            // Get assigned fee amounts for proper remaining calculation
            $assigned_fees = $this->getAssignedFeeAmounts($class_id, $section_id, $session_id, $feetype_id);

            // Process the data for enhanced column-wise display with detailed payment information
            $return_array = array();
            if (!empty($original_data)) {
                foreach ($original_data as $record) {
                    $student_key = $record['student_session_id'];

                    if (!isset($return_array[$student_key])) {
                        $return_array[$student_key] = array(
                            'student_session_id' => $record['student_session_id'],
                            'admission_no' => $record['admission_no'],
                            'firstname' => $record['firstname'],
                            'middlename' => isset($record['middlename']) ? $record['middlename'] : '',
                            'lastname' => $record['lastname'],
                            'class' => $record['class'],
                            'section' => $record['section'],
                            'fee_types' => array()
                        );
                    }

                    $fee_type_key = $record['type'];
                    if (!isset($return_array[$student_key]['fee_types'][$fee_type_key])) {
                        // Get assigned amount for this fee type
                        $assigned_amount = 0;
                        if (isset($assigned_fees[$student_key][$fee_type_key])) {
                            $assigned_amount = $assigned_fees[$student_key][$fee_type_key];
                        }

                        $return_array[$student_key]['fee_types'][$fee_type_key] = array(
                            'total_amount' => $assigned_amount,
                            'paid_amount' => 0,
                            'remaining_amount' => $assigned_amount,
                            'payments' => array()
                        );
                    }

                    // Get payment amount and details
                    $amount = $record['amount'];
                    $return_array[$student_key]['fee_types'][$fee_type_key]['paid_amount'] += $amount;

                    // Get collected by name from the record
                    $collected_by_name = 'System';
                    if (isset($record['received_byname']) && !empty($record['received_byname'])) {
                        if (is_array($record['received_byname'])) {
                            $collected_by_name = isset($record['received_byname']['name']) ? $record['received_byname']['name'] : 'System';
                        } else {
                            $collected_by_name = $record['received_byname'];
                        }
                    } elseif (isset($record['received_by']) && !empty($record['received_by'])) {
                        // If we have received_by ID but no name, try to get the name
                        try {
                            // Load staff_model if not already loaded
                            if (!isset($this->staff_model)) {
                                $this->load->model('staff_model');
                            }
                            $staff_info = $this->staff_model->get_StaffNameById($record['received_by']);
                            if (isset($staff_info['name']) && !empty($staff_info['name'])) {
                                $collected_by_name = $staff_info['name'];
                            }
                        } catch (Exception $e) {
                            // If staff_model is not available, use the ID as fallback
                            $collected_by_name = 'Staff ID: ' . $record['received_by'];
                        }
                    }

                    // Add detailed payment information
                    $return_array[$student_key]['fee_types'][$fee_type_key]['payments'][] = array(
                        'amount' => $amount,
                        'date' => $record['date'],
                        'collected_by_id' => isset($record['received_by']) ? $record['received_by'] : '',
                        'collected_by_name' => $collected_by_name,
                        'payment_mode' => isset($record['payment_mode']) ? $record['payment_mode'] : '',
                        'description' => isset($record['description']) ? $record['description'] : '',
                        'amount_fine' => isset($record['amount_fine']) ? $record['amount_fine'] : 0,
                        'amount_discount' => isset($record['amount_discount']) ? $record['amount_discount'] : 0,
                        'inv_no' => isset($record['inv_no']) ? $record['inv_no'] : ''
                    );
                }

                // Calculate remaining amounts (prevent negative values for display)
                foreach ($return_array as $student_key => $student_data) {
                    foreach ($student_data['fee_types'] as $fee_type => $fee_data) {
                        $calculated_remaining = $fee_data['total_amount'] - $fee_data['paid_amount'];
                        // Prevent negative remaining amounts for display purposes
                        // If paid amount exceeds total amount, remaining should be 0
                        $return_array[$student_key]['fee_types'][$fee_type]['remaining_amount'] =
                            max(0, $calculated_remaining);

                        // Store the actual calculation for audit purposes
                        $return_array[$student_key]['fee_types'][$fee_type]['actual_remaining'] = $calculated_remaining;

                        // If overpaid, store the overpaid amount
                        if ($calculated_remaining < 0) {
                            $return_array[$student_key]['fee_types'][$fee_type]['overpaid_amount'] = abs($calculated_remaining);
                        } else {
                            $return_array[$student_key]['fee_types'][$fee_type]['overpaid_amount'] = 0;
                        }
                    }
                }
            }

            // Ensure ALL fee types are included in each student's record, even if no payments exist
            if (!empty($return_array) && !empty($all_fee_types)) {
                foreach ($return_array as $student_key => $student_data) {
                    foreach ($all_fee_types as $fee_type_info) {
                        $fee_type = $fee_type_info['type'];

                        // If this fee type is not already in the student's record, add it with zero values
                        if (!isset($student_data['fee_types'][$fee_type])) {
                            $return_array[$student_key]['fee_types'][$fee_type] = array(
                                'total_amount' => 0,
                                'paid_amount' => 0,
                                'remaining_amount' => 0,
                                'overpaid_amount' => 0,
                                'actual_remaining' => 0,
                                'payments' => array()
                            );
                        }
                    }
                }
            }

            log_message('debug', 'Processed data count: ' . count($return_array));
            log_message('debug', 'All fee types count: ' . count($all_fee_types));
            return $return_array;
        } catch (Exception $e) {
            log_message('error', 'getFeeCollectionReportColumnwise Error: ' . $e->getMessage());
            return array();
        }
    }

    public function getAssignedFeeAmounts($class_id = null, $section_id = null, $session_id = null, $feetype_id = null)
    {
        try {
            // Get assigned fee amounts from fee_groups_feetype and student_fees_master
            $this->db->select('student_session.id as student_session_id, feetype.type, fee_groups_feetype.amount as assigned_amount');
            $this->db->from('student_session');
            $this->db->join('student_fees_master', 'student_fees_master.student_session_id = student_session.id', 'left');
            $this->db->join('fee_session_groups', 'fee_session_groups.id = student_fees_master.fee_session_group_id', 'left');
            $this->db->join('fee_groups_feetype', 'fee_groups_feetype.fee_session_group_id = fee_session_groups.id', 'left');
            $this->db->join('feetype', 'feetype.id = fee_groups_feetype.feetype_id', 'left');

            // Apply filters
            if ($session_id != null && !empty($session_id)) {
                if (is_array($session_id) && count($session_id) > 0) {
                    $this->db->where_in('student_session.session_id', $session_id);
                } elseif (!is_array($session_id)) {
                    $this->db->where('student_session.session_id', $session_id);
                }
            } else {
                $this->db->where('student_session.session_id', $this->current_session);
            }

            if ($class_id != null && !empty($class_id)) {
                if (is_array($class_id) && count($class_id) > 0) {
                    $this->db->where_in('student_session.class_id', $class_id);
                } elseif (!is_array($class_id)) {
                    $this->db->where('student_session.class_id', $class_id);
                }
            }

            if ($section_id != null && !empty($section_id)) {
                if (is_array($section_id) && count($section_id) > 0) {
                    $this->db->where_in('student_session.section_id', $section_id);
                } elseif (!is_array($section_id)) {
                    $this->db->where('student_session.section_id', $section_id);
                }
            }

            if ($feetype_id != null && !empty($feetype_id)) {
                if (is_array($feetype_id) && count($feetype_id) > 0) {
                    $this->db->where_in('fee_groups_feetype.feetype_id', $feetype_id);
                } elseif (!is_array($feetype_id)) {
                    $this->db->where('fee_groups_feetype.feetype_id', $feetype_id);
                }
            }

            $this->db->where('feetype.type IS NOT NULL');
            $query = $this->db->get();
            $results = $query->result_array();

            // Organize by student_session_id and fee type
            $assigned_fees = array();
            foreach ($results as $result) {
                $student_session_id = $result['student_session_id'];
                $fee_type = $result['type'];
                $amount = $result['assigned_amount'] ? $result['assigned_amount'] : 0;

                if (!isset($assigned_fees[$student_session_id])) {
                    $assigned_fees[$student_session_id] = array();
                }
                $assigned_fees[$student_session_id][$fee_type] = $amount;
            }

            return $assigned_fees;
        } catch (Exception $e) {
            log_message('error', 'getAssignedFeeAmounts Error: ' . $e->getMessage());
            return array();
        }
    }

    public function getFeeTypesForColumnwise($start_date, $end_date, $feetype_id = null, $class_id = null, $section_id = null, $session_id = null)
    {
        try {
            log_message('debug', 'getFeeTypesForColumnwise called with: start_date=' . $start_date . ', end_date=' . $end_date . ', class_id=' . (is_array($class_id) ? json_encode($class_id) : $class_id) . ', section_id=' . (is_array($section_id) ? json_encode($section_id) : $section_id) . ', session_id=' . (is_array($session_id) ? json_encode($session_id) : $session_id));

            // Get ONLY fee types that appear in the actual search results data
            $fee_types = array();
            $unique_types = array();

            // Get fee types from the actual search results data with all the same filters
            $search_results = $this->getFeeCollectionReport($start_date, $end_date, $feetype_id, null, null, $class_id, $section_id, $session_id);

            log_message('debug', 'Search results count: ' . count($search_results));

            if (!empty($search_results)) {
                foreach ($search_results as $record) {
                    if (!in_array($record['type'], $unique_types)) {
                        $unique_types[] = $record['type'];
                        $fee_types[] = array(
                            'type' => $record['type'],
                            'id' => isset($record['feetype_id']) ? $record['feetype_id'] : (isset($record['id']) ? $record['id'] : $record['type'])
                        );
                    }
                }
            }

            // Sort fee types alphabetically for consistent display
            usort($fee_types, function($a, $b) {
                return strcmp($a['type'], $b['type']);
            });

            log_message('debug', 'Dynamic fee types found: ' . count($fee_types) . ' - ' . implode(', ', array_column($fee_types, 'type')));
            return $fee_types;
        } catch (Exception $e) {
            log_message('error', 'getFeeTypesForColumnwise Error: ' . $e->getMessage());
            return array();
        }
    }

    /**
     * Get fee payment history for a specific fee item
     * @param int $student_fees_master_id
     * @param int $fee_groups_feetype_id
     * @return array
     */
    public function getFeePaymentHistory($student_fees_master_id, $fee_groups_feetype_id)
    {
        $sql = "SELECT sfd.id as student_fees_deposite_id, sfd.amount_detail, sfd.date, sfd.payment_mode, sfd.description,
                       sfd.created_at, sfd.received_by, staff.name as received_by_name, staff.surname as received_by_surname
                FROM student_fees_deposite sfd
                LEFT JOIN staff ON staff.id = sfd.received_by
                WHERE sfd.student_fees_master_id = ? AND sfd.fee_groups_feetype_id = ?
                ORDER BY sfd.date DESC, sfd.id DESC";

        $query = $this->db->query($sql, array($student_fees_master_id, $fee_groups_feetype_id));
        $results = $query->result();

        $payment_history = array();
        if (!empty($results)) {
            foreach ($results as $result) {
                $amount_detail = json_decode($result->amount_detail, true);
                if (!empty($amount_detail)) {
                    foreach ($amount_detail as $inv_no => $detail) {
                        $payment_history[] = (object) array(
                            'student_fees_deposite_id' => $result->student_fees_deposite_id,
                            'inv_no' => $inv_no,
                            'date' => $result->date,
                            'payment_mode' => $result->payment_mode,
                            'description' => isset($detail['description']) ? $detail['description'] : $result->description,
                            'amount' => isset($detail['amount']) ? $detail['amount'] : 0,
                            'amount_discount' => isset($detail['amount_discount']) ? $detail['amount_discount'] : 0,
                            'amount_fine' => isset($detail['amount_fine']) ? $detail['amount_fine'] : 0,
                            'received_by_name' => $result->received_by_name,
                            'received_by_surname' => $result->received_by_surname,
                            'created_at' => $result->created_at
                        );
                    }
                }
            }
        }

        return $payment_history;
    }

    /**
     * Get transport fee payment history for a specific transport fee
     * @param int $trans_fee_id
     * @return array
     */
    public function getTransportFeePaymentHistory($trans_fee_id)
    {
        $sql = "SELECT sfd.id as student_fees_deposite_id, sfd.amount_detail, sfd.date, sfd.payment_mode, sfd.description,
                       sfd.created_at, sfd.received_by, staff.name as received_by_name, staff.surname as received_by_surname
                FROM student_fees_deposite sfd
                LEFT JOIN staff ON staff.id = sfd.received_by
                WHERE sfd.student_transport_fee_id = ?
                ORDER BY sfd.date DESC, sfd.id DESC";

        $query = $this->db->query($sql, array($trans_fee_id));
        $results = $query->result();

        $payment_history = array();
        if (!empty($results)) {
            foreach ($results as $result) {
                $amount_detail = json_decode($result->amount_detail, true);
                if (!empty($amount_detail)) {
                    foreach ($amount_detail as $inv_no => $detail) {
                        $payment_history[] = (object) array(
                            'student_fees_deposite_id' => $result->student_fees_deposite_id,
                            'inv_no' => $inv_no,
                            'date' => $result->date,
                            'payment_mode' => $result->payment_mode,
                            'description' => isset($detail['description']) ? $detail['description'] : $result->description,
                            'amount' => isset($detail['amount']) ? $detail['amount'] : 0,
                            'amount_discount' => isset($detail['amount_discount']) ? $detail['amount_discount'] : 0,
                            'amount_fine' => isset($detail['amount_fine']) ? $detail['amount_fine'] : 0,
                            'received_by_name' => $result->received_by_name,
                            'received_by_surname' => $result->received_by_surname,
                            'created_at' => $result->created_at
                        );
                    }
                }
            }
        }

        return $payment_history;
    }

}
