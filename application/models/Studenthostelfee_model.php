<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Studenthostelfee_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    
    public function add($data_insert, $student_session_id, $remove_ids, $hostel_room_id)
    {
        $new_inserted = array();
        $this->db->trans_begin();

        // Remove fees from different rooms
        $this->db->where('hostel_room_id !=', $hostel_room_id);
        $this->db->where('student_session_id', $student_session_id);
        $this->db->delete('student_hostel_fees');

        // Remove specific fees if requested
        if (!empty($remove_ids)) {
            $this->db->where_in('id', $remove_ids);
            $this->db->where('student_session_id', $student_session_id);
            $this->db->delete('student_hostel_fees');
        }

        // FIXED: Use INSERT IGNORE or check for existing records
        if (!empty($data_insert)) {
            foreach ($data_insert as $insert_key => $insert_value) {
                // Check if this combination already exists
                $this->db->where('student_session_id', $insert_value['student_session_id']);
                $this->db->where('hostel_feemaster_id', $insert_value['hostel_feemaster_id']);
                $this->db->where('hostel_room_id', $insert_value['hostel_room_id']);
                $existing = $this->db->get('student_hostel_fees');
                
                // Only insert if it doesn't exist
                if ($existing->num_rows() == 0) {
                    $this->db->insert('student_hostel_fees', $insert_value);
                }
            }
        }

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    public function getStudentHostelFees($student_session_id, $hostel_room_id = null)
    {
        $this->db->select('student_hostel_fees.*, hostel_feemaster.month, hostel_feemaster.due_date, 
                          hostel_feemaster.fine_type, hostel_feemaster.fine_percentage, hostel_feemaster.fine_amount,
                          hostel_rooms.cost_per_bed as amount, hostel_rooms.room_no, hostel.hostel_name')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id')
                 ->where('student_hostel_fees.student_session_id', $student_session_id);
        
        if ($hostel_room_id != null) {
            $this->db->where('student_hostel_fees.hostel_room_id', $hostel_room_id);
        }
        
        $this->db->order_by('hostel_feemaster.id', 'ASC');
        return $this->db->get()->result();
    }

    public function getHostelFeeByStudentSession($student_session_id, $hostel_room_id)
    {

        if ($student_session_id != null && $hostel_room_id != null) {

            $sql = "SELECT hostel_feemaster.*,student_hostel_fees.id as student_hostel_fee_id FROM `hostel_feemaster` LEFT JOIN student_hostel_fees on hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id and student_hostel_fees.hostel_room_id=" . $hostel_room_id . " and student_hostel_fees.student_session_id=" . $student_session_id . " WHERE hostel_feemaster.session_id=" . $this->current_session . " ORDER by hostel_feemaster.id";

            $query = $this->db->query($sql);
            return $query->result();
        }

        return false;

    }

    public function getHostelFeeByMonthStudentSession($student_session_id, $hostel_room_id, $month)
    {

        if ($student_session_id != null && $hostel_room_id != null) {

            $sql = "SELECT hostel_feemaster.*,student_hostel_fees.id as student_hostel_fee_id FROM `hostel_feemaster` LEFT JOIN student_hostel_fees on hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id and student_hostel_fees.hostel_room_id=" . $hostel_room_id . " and student_hostel_fees.student_session_id=" . $student_session_id . " WHERE hostel_feemaster.session_id=" . $this->current_session . " and hostel_feemaster.month='" . $month . "' ORDER by hostel_feemaster.id";

            $query = $this->db->query($sql);
            return $query->result();
        }

        return false;

    }

    public function getHostelFeeMasterByStudentHostelID($student_hostel_fee_id)
    {

        $this->db->select('hostel_feemaster.*,hostel_rooms.cost_per_bed as `amount`');
        $this->db->join('hostel_feemaster', 'hostel_feemaster.id=student_hostel_fees.hostel_feemaster_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id=student_hostel_fees.hostel_room_id');
        $this->db->where('student_hostel_fees.id', $student_hostel_fee_id);
        $q = $this->db->get('student_hostel_fees');

        return $q->row();

    }

    /**
     * Get student hostel fee by ID
     * @param int $id
     * @return object
     */
    public function get($id = null)
    {
        $this->db->select('student_hostel_fees.*, hostel_feemaster.month, hostel_feemaster.due_date,
                          hostel_rooms.cost_per_bed as amount, hostel_rooms.room_no, hostel.hostel_name')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id');
        
        if ($id != null) {
            $this->db->where('student_hostel_fees.id', $id);
        } else {
            $this->db->order_by('student_hostel_fees.id', 'DESC');
        }
        
        $query = $this->db->get();
        if ($id != null) {
            return $query->row();
        } else {
            return $query->result();
        }
    }

    /**
     * Remove student hostel fee
     * @param int $id
     * @return bool
     */
    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        $this->db->delete('student_hostel_fees');
        
        $message = DELETE_RECORD_CONSTANT . " On student hostel fee id " . $id;
        $action = "Delete";
        $this->log($message, $id, $action);

        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

    /**
     * Get students with hostel fees for a specific month
     * @param string $month
     * @param int $session_id
     * @return array
     */
    public function getStudentsByMonth($month, $session_id = null)
    {
        if ($session_id == null) {
            $session_id = $this->current_session;
        }

        $this->db->select('student_hostel_fees.*, students.firstname, students.middlename, students.lastname,
                          students.admission_no, classes.class, sections.section, hostel_rooms.room_no,
                          hostel.hostel_name, hostel_feemaster.due_date, hostel_rooms.cost_per_bed as amount')
                 ->from('student_hostel_fees')
                 ->join('hostel_feemaster', 'hostel_feemaster.id = student_hostel_fees.hostel_feemaster_id')
                 ->join('student_session', 'student_session.id = student_hostel_fees.student_session_id')
                 ->join('students', 'students.id = student_session.student_id')
                 ->join('classes', 'classes.id = student_session.class_id')
                 ->join('sections', 'sections.id = student_session.section_id')
                 ->join('hostel_rooms', 'hostel_rooms.id = student_hostel_fees.hostel_room_id')
                 ->join('hostel', 'hostel.id = hostel_rooms.hostel_id')
                 ->where('hostel_feemaster.month', $month)
                 ->where('hostel_feemaster.session_id', $session_id)
                 ->where('students.is_active', 'yes')
                 ->order_by('students.firstname', 'ASC');
        
        return $this->db->get()->result();
    }

    /**
     * Get hostel fee payment history for a specific hostel fee
     * @param int $hostel_fee_id
     * @return array
     */
    /**
     * Get hostel fee by ID (alias for get() method)
     * @param int $id The hostel fee ID
     * @return object|null
     */
    public function getHostelFeeByID($id)
    {
        return $this->get($id);
    }

    /**
     * Get hostel fee payment history
     * @param int $hostel_fee_id
     * @return array
     */
    public function getHostelFeePaymentHistory($hostel_fee_id)
    {
        $sql = "SELECT sfd.id as student_fees_deposite_id, sfd.amount_detail, sfd.date, sfd.payment_mode, sfd.description,
                       sfd.created_at, sfd.received_by, staff.name as received_by_name, staff.surname as received_by_surname
                FROM student_fees_deposite sfd
                LEFT JOIN staff ON staff.id = sfd.received_by
                WHERE sfd.student_hostel_fee_id = ?
                ORDER BY sfd.date DESC, sfd.id DESC";

        $query = $this->db->query($sql, array($hostel_fee_id));
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
