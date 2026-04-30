<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Face_attendance_student_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Check if registration number already exists
     */
    public function check_registration_exists($registration_number) {
        $this->db->where('registration_number', $registration_number);
        $query = $this->db->get('face_attendance_students');
        return $query->num_rows() > 0;
    }

    /**
     * Get all registered persons by type (student/staff)
     */
    public function get_all_by_type($type = null) {
        if ($type !== null) {
            $this->db->where('person_type', $type);
        }
        $this->db->where('is_active', 1);
        $this->db->order_by('first_name', 'ASC');
        $this->db->order_by('last_name', 'ASC');
        $query = $this->db->get('face_attendance_students');
        return $query->result();
    }

    /**
     * Get all registered persons (backward compatible)
     */
    public function get_all_students($is_active = null) {
        if ($is_active !== null) {
            $this->db->where('is_active', $is_active);
        }
        $this->db->order_by('first_name', 'ASC');
        $this->db->order_by('last_name', 'ASC');
        $query = $this->db->get('face_attendance_students');
        return $query->result();
    }

    /**
     * Get person by database ID (student_id or staff_id)
     */
    public function get_by_person_id($type, $id) {
        if ($type === 'student') {
            $this->db->where('student_id', $id);
        } else {
            $this->db->where('staff_id', $id);
        }
        $this->db->where('person_type', $type);
        $query = $this->db->get('face_attendance_students');
        return $query->row();
    }

    /**
     * Get person by ID
     */
    public function get_student($id) {
        $this->db->where('id', $id);
        $query = $this->db->get('face_attendance_students');
        return $query->row();
    }

    /**
     * Get person by registration number
     */
    public function get_student_by_registration($registration_number) {
        $this->db->where('registration_number', $registration_number);
        $query = $this->db->get('face_attendance_students');
        return $query->row();
    }

    /**
     * Add new person
     */
    public function add_student($data) {
        $insert_data = array(
            'student_id'          => isset($data['student_id']) ? $data['student_id'] : null,
            'staff_id'            => isset($data['staff_id']) ? $data['staff_id'] : null,
            'person_type'         => isset($data['person_type']) ? $data['person_type'] : 'student',
            'registration_number' => $data['registration_number'],
            'admission_no'        => isset($data['admission_no']) ? $data['admission_no'] : null,
            'first_name'          => $data['first_name'],
            'last_name'           => isset($data['last_name']) ? $data['last_name'] : '',
            'class_id'            => isset($data['class_id']) ? $data['class_id'] : null,
            'section_id'          => isset($data['section_id']) ? $data['section_id'] : null,
            'email'               => isset($data['email']) ? $data['email'] : null,
            'phone'               => isset($data['phone']) ? $data['phone'] : null,
            'designation'         => isset($data['designation']) ? $data['designation'] : null,
            'face_images'         => isset($data['face_images']) ? json_encode($data['face_images']) : null,
            'face_descriptors'    => isset($data['face_descriptors']) ? json_encode($data['face_descriptors']) : null,
            'is_active'           => 1,
            'registered_by'       => $this->session->userdata('id'),
            'registration_date'   => date('Y-m-d H:i:s')
        );

        if ($this->db->insert('face_attendance_students', $insert_data)) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Update person information
     */
    public function update_student($id, $data) {
        $update_data = array();

        $allowed_fields = array(
            'student_id', 'staff_id', 'admission_no', 'first_name', 'last_name',
            'class_id', 'section_id', 'email', 'phone', 'is_active', 'designation', 'person_type'
        );

        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $update_data[$field] = $data[$field];
            }
        }

        if (isset($data['face_images'])) {
            $update_data['face_images'] = json_encode($data['face_images']);
        }
        if (isset($data['face_descriptors'])) {
            $update_data['face_descriptors'] = json_encode($data['face_descriptors']);
        }
        if (isset($data['last_updated'])) {
            $update_data['last_updated'] = $data['last_updated'];
        }

        if (!empty($update_data)) {
            $this->db->where('id', $id);
            return $this->db->update('face_attendance_students', $update_data);
        }
        return false;
    }

    /**
     * Delete person
     */
    public function delete_student($id) {
        $this->db->where('id', $id);
        return $this->db->delete('face_attendance_students');
    }

    /**
     * Mark attendance in face_attendance_records
     */
    public function mark_attendance($data) {
        $insert_data = array(
            'face_student_id'      => $data['face_student_id'],
            'registration_number'  => $data['registration_number'],
            'attendance_date'      => $data['attendance_date'],
            'attendance_time'      => $data['attendance_time'],
            'attendance_status'    => isset($data['attendance_status']) ? $data['attendance_status'] : 'Present',
            'confidence_score'     => isset($data['confidence_score']) ? $data['confidence_score'] : null,
            'captured_image'       => isset($data['captured_image']) ? $data['captured_image'] : null,
            'session_id'           => isset($data['session_id']) ? $data['session_id'] : null,
            'class_id'             => isset($data['class_id']) ? $data['class_id'] : null,
            'section_id'           => isset($data['section_id']) ? $data['section_id'] : null,
            'marked_by'            => $this->session->userdata('id'),
            'recognition_method'   => isset($data['recognition_method']) ? $data['recognition_method'] : 'Auto',
            'person_type'          => isset($data['person_type']) ? $data['person_type'] : 'student',
            'notes'                => isset($data['notes']) ? $data['notes'] : null,
            'staff_att_sync'       => isset($data['staff_att_sync']) ? $data['staff_att_sync'] : 0,
            'staff_att_type_id'    => isset($data['staff_att_type_id']) ? $data['staff_att_type_id'] : null
        );

        return $this->db->insert('face_attendance_records', $insert_data);
    }

    /**
     * Check if attendance already marked today
     */
    public function is_attendance_marked_today($face_student_id, $date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $this->db->where('face_student_id', $face_student_id);
        $this->db->where('attendance_date', $date);
        $query = $this->db->get('face_attendance_records');
        return $query->num_rows() > 0;
    }

    /**
     * Get attendance records with filters
     */
    public function get_attendance_records($filters = array()) {
        $this->db->select('far.*, fas.registration_number, fas.first_name, fas.last_name, fas.person_type, fas.staff_id');
        $this->db->from('face_attendance_records far');
        $this->db->join('face_attendance_students fas', 'far.face_student_id = fas.id', 'left');

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $this->db->where('far.attendance_date >=', $filters['date_from']);
        }
        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $this->db->where('far.attendance_date <=', $filters['date_to']);
        }
        if (isset($filters['class_id']) && !empty($filters['class_id'])) {
            $this->db->where('far.class_id', $filters['class_id']);
        }
        if (isset($filters['section_id']) && !empty($filters['section_id'])) {
            $this->db->where('far.section_id', $filters['section_id']);
        }
        if (isset($filters['status']) && !empty($filters['status'])) {
            $this->db->where('far.attendance_status', $filters['status']);
        }
        if (isset($filters['person_type']) && !empty($filters['person_type'])) {
            $this->db->where('far.person_type', $filters['person_type']);
        }

        $this->db->order_by('far.attendance_date', 'DESC');
        $this->db->order_by('far.attendance_time', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get attendance statistics for a date
     */
    public function get_attendance_stats($date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $this->db->select('attendance_status, COUNT(*) as count');
        $this->db->where('attendance_date', $date);
        $this->db->group_by('attendance_status');
        $query = $this->db->get('face_attendance_records');

        $stats = array(
            'Present' => 0,
            'Absent'  => 0,
            'Late'    => 0
        );

        foreach ($query->result() as $row) {
            $stats[$row->attendance_status] = $row->count;
        }

        return $stats;
    }

    /**
     * Get attendance by date
     */
    public function get_attendance_by_date($date) {
        $this->db->select('far.*, fas.registration_number, fas.first_name, fas.last_name, fas.admission_no, fas.person_type, fas.staff_id');
        $this->db->from('face_attendance_records far');
        $this->db->join('face_attendance_students fas', 'far.face_student_id = fas.id', 'left');
        $this->db->where('far.attendance_date', $date);
        $this->db->order_by('far.attendance_time', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Log attendance session
     */
    public function log_attendance_session($data) {
        $insert_data = array(
            'session_date'        => isset($data['session_date']) ? $data['session_date'] : date('Y-m-d H:i:s'),
            'recognition_time'    => date('Y-m-d H:i:s'),
            'detected_faces'      => isset($data['detected_faces']) ? $data['detected_faces'] : 0,
            'recognized_faces'    => isset($data['recognized_faces']) ? $data['recognized_faces'] : 0,
            'unknown_faces'       => isset($data['unknown_faces']) ? $data['unknown_faces'] : 0,
            'recognition_details' => isset($data['recognition_details']) ? $data['recognition_details'] : null,
            'created_by'          => isset($data['created_by']) ? $data['created_by'] : $this->session->userdata('id')
        );

        return $this->db->insert('face_attendance_logs', $insert_data);
    }

    /**
     * Log face recognition session
     */
    public function log_recognition_session($data) {
        return $this->log_attendance_session($data);
    }

    /**
     * Get total registered count by type
     */
    public function get_total_count($type = null) {
        $this->db->where('is_active', 1);
        if ($type !== null) {
            $this->db->where('person_type', $type);
        }
        return $this->db->count_all_results('face_attendance_students');
    }

    // =====================================================================
    // INTEGRATION WITH EXISTING STAFF ATTENDANCE SYSTEM
    // =====================================================================

    /**
     * Get payroll settings
     */
    public function get_payroll_settings() {
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
     * Check if staff already has attendance in staff_attendance for a given date
     * Returns the record if found, or null
     */
    public function get_staff_attendance_for_date($staff_id, $date) {
        $this->db->where('staff_id', $staff_id);
        $this->db->where('date', $date);
        $query = $this->db->get('staff_attendance');
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        return null;
    }

    /**
     * Check payroll cutoff - whether the given date falls within the current payroll period
     * Returns: ['in_period' => bool, 'cutoff_day' => int, 'period_start' => date, 'period_end' => date]
     */
    public function check_payroll_cutoff($date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }

        $settings = $this->get_payroll_settings();
        $cutoffDay = 1;

        if ($settings && isset($settings['payroll_cutoff_day']) && (int) $settings['payroll_cutoff_day'] > 0) {
            $cutoffDay = (int) $settings['payroll_cutoff_day'];
        }

        $dateObj = new DateTime($date);
        $day = (int) $dateObj->format('j');
        $month = (int) $dateObj->format('m');
        $year = (int) $dateObj->format('Y');

        // Determine period start and end
        if ($cutoffDay > 1 && $day < $cutoffDay) {
            // We're in the period that started on cutoff_day of the previous month
            $periodStart = new DateTime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($cutoffDay, 2, '0', STR_PAD_LEFT));
            $periodStart->modify('-1 month');
            $periodEnd = new DateTime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($cutoffDay - 1, 2, '0', STR_PAD_LEFT));
        } else {
            // We're in the period that started on cutoff_day of this month
            $periodStart = new DateTime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($cutoffDay, 2, '0', STR_PAD_LEFT));
            $periodEnd = new DateTime($year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($cutoffDay, 2, '0', STR_PAD_LEFT));
            $periodEnd->modify('+1 month');
            $periodEnd->modify('-1 day');
        }

        $inPeriod = ($dateObj >= $periodStart && $dateObj <= $periodEnd);

        return array(
            'in_period'    => $inPeriod,
            'cutoff_day'   => $cutoffDay,
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end'   => $periodEnd->format('Y-m-d'),
            'settings'     => $settings
        );
    }

    /**
     * Determine the staff_attendance_type_id based on check-in time
     * Uses payroll settings: shift_start_time and late_grace_minutes
     *
     * Returns: [
     *   'type_id'       => int (1=Present, 2=Late, 3=Absent, 4=HalfDay, 5=Holiday),
     *   'type_name'     => string,
     *   'check_in_time' => string (formatted time),
     *   'is_late'       => bool,
     *   'grace_until'   => string (time until which no late penalty),
     *   'shift_start'   => string,
     *   'grace_minutes' => int
     * ]
     */
    public function determine_staff_attendance_type($check_in_time_str = null) {
        $settings = $this->get_payroll_settings();

        $shiftStart = '09:00:00';
        $graceMinutes = 15;

        if ($settings) {
            if (!empty($settings['shift_start_time'])) {
                $shiftStart = $settings['shift_start_time'];
            }
            if (isset($settings['late_grace_minutes']) && (int) $settings['late_grace_minutes'] > 0) {
                $graceMinutes = (int) $settings['late_grace_minutes'];
            }
        }

        // Calculate grace time
        $shiftDateTime = new DateTime($shiftStart);
        $graceDateTime = clone $shiftDateTime;
        $graceDateTime->modify('+' . $graceMinutes . ' minutes');

        $graceUntil = $graceDateTime->format('H:i:s');

        // If no check-in time, use current time
        if (empty($check_in_time_str)) {
            $check_in_time_str = date('H:i:s');
        }

        $checkIn = new DateTime($check_in_time_str);

        // staff_attendance_type_id mapping:
        // 1 = Present, 2 = Late, 3 = Absent, 4 = Half Day, 5 = Holiday
        if ($checkIn <= $graceDateTime) {
            return array(
                'type_id'       => 1,
                'type_name'     => 'Present',
                'check_in_time' => $check_in_time_str,
                'is_late'       => false,
                'grace_until'   => $graceUntil,
                'shift_start'   => $shiftStart,
                'grace_minutes' => $graceMinutes
            );
        } else {
            return array(
                'type_id'       => 2,
                'type_name'     => 'Late',
                'check_in_time' => $check_in_time_str,
                'is_late'       => true,
                'grace_until'   => $graceUntil,
                'shift_start'   => $shiftStart,
                'grace_minutes' => $graceMinutes
            );
        }
    }

    /**
     * Write attendance to the main staff_attendance table
     * This integrates face recognition attendance with the existing payroll system.
     *
     * @param int $staff_id The staff.id from the staff table
     * @param array $params ['check_in_time' => 'H:i:s', 'date' => 'Y-m-d', 'remark' => string]
     * @return array ['success' => bool, 'message' => string, 'record' => array, 'type_info' => array]
     */
    public function sync_staff_attendance($staff_id, $params = array()) {
        $date = isset($params['date']) ? $params['date'] : date('Y-m-d');
        $check_in_time = isset($params['check_in_time']) ? $params['check_in_time'] : date('H:i:s');
        $remark = isset($params['remark']) ? $params['remark'] : '';
        $face_reg_id = isset($params['face_reg_id']) ? $params['face_reg_id'] : null;

        // 1. Check payroll cutoff
        $cutoffInfo = $this->check_payroll_cutoff($date);

        // 2. Determine attendance type (Present or Late) based on check-in time
        $typeInfo = $this->determine_staff_attendance_type($check_in_time);

        // 3. Check if attendance already exists in staff_attendance for this date
        $existing = $this->get_staff_attendance_for_date($staff_id, $date);

        if ($existing) {
            // Already has attendance - don't overwrite (biometric or manual may already be recorded)
            // Return the existing record info
            $existingType = isset($existing['staff_attendance_type_id']) ? (int) $existing['staff_attendance_type_id'] : null;
            $typeName = $this->get_attendance_type_name($existingType);

            return array(
                'success'       => true,
                'message'       => "Staff already has attendance recorded for this date as '{$typeName}'. Existing record preserved.",
                'skipped'       => true,
                'record'        => $existing,
                'type_info'     => $typeInfo,
                'cutoff_info'   => $cutoffInfo
            );
        }

        // 4. Insert new attendance record into staff_attendance
        $insert_data = array(
            'staff_id'                 => $staff_id,
            'date'                     => $date,
            'check_in_time'            => $typeInfo['check_in_time'],
            'staff_attendance_type_id' => $typeInfo['type_id'],
            'biometric_attendence'     => 0,
            'is_authorized_range'      => 1,
            'remark'                   => $remark ?: 'Face recognition attendance (ID: ' . $face_reg_id . ')',
            'is_active'                => 1,
            'created_at'               => date('Y-m-d H:i:s')
        );

        $this->db->insert('staff_attendance', $insert_data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return array(
                'success'       => true,
                'message'       => "Staff attendance recorded as '{$typeInfo['type_name']}' in staff_attendance table.",
                'skipped'       => false,
                'insert_id'     => $insert_id,
                'record'        => $insert_data,
                'type_info'     => $typeInfo,
                'cutoff_info'   => $cutoffInfo
            );
        } else {
            return array(
                'success'       => false,
                'message'       => 'Failed to insert staff attendance record.',
                'skipped'       => false,
                'record'        => null,
                'type_info'     => $typeInfo,
                'cutoff_info'   => $cutoffInfo
            );
        }
    }

    /**
     * Get the attendance type name by type_id
     */
    private function get_attendance_type_name($type_id) {
        $types = array(1 => 'Present', 2 => 'Late', 3 => 'Absent', 4 => 'Half Day', 5 => 'Holiday');
        return isset($types[$type_id]) ? $types[$type_id] : 'Unknown';
    }

    /**
     * Get existing staff attendance status for today (for display in mark_attendance)
     * Returns array of staff_id => attendance info
     */
    public function get_staff_today_attendance($staff_ids = array(), $date = null) {
        if (empty($staff_ids)) {
            return array();
        }

        if ($date === null) {
            $date = date('Y-m-d');
        }

        $this->db->select('staff_id, staff_attendance_type_id, check_in_time, check_out_time, remark, date');
        $this->db->from('staff_attendance');
        $this->db->where('date', $date);
        $this->db->where_in('staff_id', $staff_ids);
        $query = $this->db->get();

        $result = array();
        foreach ($query->result_array() as $row) {
            $result[$row['staff_id']] = $row;
        }

        return $result;
    }

    /**
     * Get payroll cutoff info for display
     */
    public function get_payroll_info_for_display() {
        $settings = $this->get_payroll_settings();
        $cutoffInfo = $this->check_payroll_cutoff();
        $typeInfo = $this->determine_staff_attendance_type();

        return array(
            'settings'       => $settings,
            'cutoff'         => $cutoffInfo,
            'attendance_type' => $typeInfo
        );
    }
}
