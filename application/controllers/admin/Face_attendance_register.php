<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Face_attendance_register extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('Face_attendance_student_model');
    }

    function index() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'admin/face_attendance_register');

        $data['title'] = 'Face Attendance Registration';

        // Get classes for filter
        $data['classlist'] = $this->class_model->get();

        // Get staff roles for filter
        $data['staff_roles'] = $this->staff_model->getStaffRole();

        // Get already registered face attendance records
        $data['registered_students'] = $this->Face_attendance_student_model->get_all_by_type('student');
        $data['registered_staff'] = $this->Face_attendance_student_model->get_all_by_type('staff');

        // Get students from main students table (without users join to avoid empty results)
        $data['students'] = $this->_get_students_simple();

        // Get staff from main staff table
        $data['staffs'] = $this->staff_model->get();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/face_attendance_register/index', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Get students by class and section (AJAX)
     */
    public function get_students_by_class() {
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');

        $session_id = $this->setting_model->getCurrentSession();
        $this->db->reset_query();
        $this->db->select('students.id, students.admission_no, students.firstname, students.middlename, students.lastname, students.email, students.mobileno, students.image, classes.class, sections.section, classes.id as class_id, sections.id as section_id');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('students.is_active', 'yes');

        if (!empty($class_id)) {
            $this->db->where('student_session.class_id', $class_id);
        }
        if (!empty($section_id)) {
            $this->db->where('student_session.section_id', $section_id);
        }

        $this->db->order_by('students.firstname', 'ASC');
        $query = $this->db->get();

        $students = $query->result_array();

        // Get already registered student IDs
        $registered = $this->Face_attendance_student_model->get_all_by_type('student');
        $registered_ids = array();
        foreach ($registered as $reg) {
            if ($reg->student_id) {
                $registered_ids[$reg->student_id] = $reg;
            }
        }

        // Mark which students already have face registration
        foreach ($students as &$student) {
            $student['face_registered'] = isset($registered_ids[$student['id']]);
            if ($student['face_registered']) {
                $reg = $registered_ids[$student['id']];
                $student['face_reg_id'] = $reg->id;
                $student['registration_number'] = $reg->registration_number;
                $face_images = json_decode($reg->face_images, true) ?: [];
                $student['face_image_count'] = count($face_images);
                // Build full URLs for each face image
                $student['face_image_urls'] = array();
                $encoded_reg = rawurlencode($reg->registration_number);
                foreach ($face_images as $img) {
                    $student['face_image_urls'][] = base_url('uploads/face_attendance_images/' . $encoded_reg . '/' . $img);
                }
            }
        }

        echo json_encode(array('status' => 'success', 'students' => $students));
    }

    /**
     * Get staff by role (AJAX)
     */
    public function get_staff_by_role() {
        $role_id = $this->input->post('role_id');

        $this->db->reset_query();
        $this->db->select('staff.id, staff.name, staff.surname, staff.employee_id, staff.email, staff.contact_no, staff.image, staff.designation, roles.name as user_type, roles.id as role_id');
        $this->db->from('staff');
        $this->db->join('staff_roles', 'staff_roles.staff_id = staff.id', 'left');
        $this->db->join('roles', 'roles.id = staff_roles.role_id', 'left');
        $this->db->where('staff.is_active', 1);

        if (!empty($role_id)) {
            $this->db->where('roles.id', $role_id);
        }

        $this->db->order_by('staff.name', 'ASC');
        $query = $this->db->get();
        $staffs = $query->result_array();

        // Get already registered staff IDs
        $registered = $this->Face_attendance_student_model->get_all_by_type('staff');
        $registered_ids = array();
        foreach ($registered as $reg) {
            if ($reg->staff_id) {
                $registered_ids[$reg->staff_id] = $reg;
            }
        }

        // Mark which staff already have face registration
        foreach ($staffs as &$staff) {
            $staff['face_registered'] = isset($registered_ids[$staff['id']]);
            if ($staff['face_registered']) {
                $reg = $registered_ids[$staff['id']];
                $staff['face_reg_id'] = $reg->id;
                $staff['registration_number'] = $reg->registration_number;
                $face_images = json_decode($reg->face_images, true) ?: [];
                $staff['face_image_count'] = count($face_images);
                // Build full URLs for each face image
                $staff['face_image_urls'] = array();
                $encoded_reg = rawurlencode($reg->registration_number);
                foreach ($face_images as $img) {
                    $staff['face_image_urls'][] = base_url('uploads/face_attendance_images/' . $encoded_reg . '/' . $img);
                }
            }
        }

        echo json_encode(array('status' => 'success', 'staffs' => $staffs));
    }

    /**
     * Get sections by class (AJAX)
     */
    public function get_sections_by_class() {
        $class_id = $this->input->post('class_id');

        $sections = $this->classsection_model->get($class_id);

        echo json_encode(array('status' => 'success', 'sections' => $sections));
    }

    /**
     * Check if registration number exists (AJAX)
     */
    public function check_registration() {
        $registration_number = $this->input->post('registration_number');
        $current_id = $this->input->post('current_id'); // For edit, exclude current record

        if (empty($registration_number)) {
            echo json_encode(array('status' => 'error', 'message' => 'Registration number is required'));
            return;
        }

        $exists = $this->Face_attendance_student_model->check_registration_exists($registration_number);

        echo json_encode(array(
            'status' => 'success',
            'exists' => $exists,
            'message' => $exists ? 'Registration number already exists' : 'Registration number is available'
        ));
    }

    /**
     * Register face for a student from database
     */
    public function register_student_face() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_add')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $student_id = $this->input->post('student_id');
        $registration_number = $this->input->post('registration_number');
        $admission_no = $this->input->post('admission_no');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $class_id = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');

        if (empty($student_id) || empty($registration_number) || empty($first_name)) {
            echo json_encode(array('status' => 'error', 'message' => 'Required fields are missing'));
            return;
        }

        // Sanitize registration number - remove spaces and special characters
        $registration_number = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($registration_number));

        // Check if already registered for this student
        $existing = $this->Face_attendance_student_model->get_by_person_id('student', $student_id);
        if ($existing) {
            // Update existing record
            return $this->update_face_registration($existing->id);
        }

        // Check if at least 3 images are captured
        $captured_images = array();
        for ($i = 1; $i <= 5; $i++) {
            $image_data = $this->input->post("captured_image_$i");
            if (!empty($image_data)) {
                $captured_images[] = $image_data;
            }
        }

        if (count($captured_images) < 3) {
            echo json_encode(array('status' => 'error', 'message' => 'Please capture at least 3 face images'));
            return;
        }

        // Create directory for face images
        $upload_path = './uploads/face_attendance_images/' . $registration_number . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // Save images
        $saved_images = array();
        $save_errors = array();
        foreach ($captured_images as $index => $image_data) {
            $image_parts = explode(';base64,', $image_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $file_name = ($index + 1) . '.png';
                $file_path = $upload_path . $file_name;

                $result = @file_put_contents($file_path, $image_base64);
                if ($result !== false) {
                    $saved_images[] = $file_name;
                } else {
                    $save_errors[] = "Failed to save image " . ($index + 1) . ": " . error_get_last()['message'];
                    log_message('error', 'Face Register: Failed to save image ' . ($index + 1) . ' to ' . $file_path . ' - ' . print_r(error_get_last(), true));
                }
            } else {
                $save_errors[] = "Invalid image format for image " . ($index + 1);
            }
        }

        if (count($saved_images) < 3) {
            $error_msg = 'Error saving images. ' . implode('; ', $save_errors);
            // Also check directory writability
            if (!is_writable($upload_path)) {
                $error_msg .= ' | Directory not writable: ' . $upload_path;
            }
            log_message('error', 'Face Register: ' . $error_msg . ' | Upload path: ' . $upload_path . ' | Dir exists: ' . (is_dir($upload_path) ? 'yes' : 'no') . ' | Writable: ' . (is_writable($upload_path) ? 'yes' : 'no'));
            echo json_encode(array('status' => 'error', 'message' => $error_msg));
            return;
        }

        // Prepare data
        $student_data = array(
            'student_id'          => $student_id,
            'registration_number' => $registration_number,
            'first_name'          => $first_name,
            'last_name'           => $last_name,
            'email'               => $email,
            'phone'               => $phone,
            'admission_no'        => $admission_no,
            'class_id'            => $class_id,
            'section_id'          => $section_id,
            'person_type'         => 'student',
            'face_images'         => $saved_images
        );

        $insert_id = $this->Face_attendance_student_model->add_student($student_data);

        if ($insert_id) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Student face registered successfully!',
                'student_id' => $insert_id
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error saving student data'));
        }
    }

    /**
     * Register face for a staff member from database
     */
    public function register_staff_face() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_add')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $staff_id = $this->input->post('staff_id');
        $registration_number = $this->input->post('registration_number');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $email = $this->input->post('email');
        $phone = $this->input->post('phone');
        $designation = $this->input->post('designation');

        if (empty($staff_id) || empty($registration_number) || empty($first_name)) {
            echo json_encode(array('status' => 'error', 'message' => 'Required fields are missing'));
            return;
        }

        // Sanitize registration number - remove spaces and special characters
        $registration_number = preg_replace('/[^a-zA-Z0-9_\-]/', '_', trim($registration_number));

        // Check if already registered for this staff
        $existing = $this->Face_attendance_student_model->get_by_person_id('staff', $staff_id);
        if ($existing) {
            // Update existing record
            return $this->update_face_registration($existing->id);
        }

        // Check if at least 3 images are captured
        $captured_images = array();
        for ($i = 1; $i <= 5; $i++) {
            $image_data = $this->input->post("captured_image_$i");
            if (!empty($image_data)) {
                $captured_images[] = $image_data;
            }
        }

        if (count($captured_images) < 3) {
            echo json_encode(array('status' => 'error', 'message' => 'Please capture at least 3 face images'));
            return;
        }

        // Create directory for face images
        $upload_path = './uploads/face_attendance_images/' . $registration_number . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        }

        // Save images
        $saved_images = array();
        $save_errors = array();
        foreach ($captured_images as $index => $image_data) {
            $image_parts = explode(';base64,', $image_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $file_name = ($index + 1) . '.png';
                $file_path = $upload_path . $file_name;

                $result = @file_put_contents($file_path, $image_base64);
                if ($result !== false) {
                    $saved_images[] = $file_name;
                } else {
                    $save_errors[] = "Failed to save image " . ($index + 1) . ": " . error_get_last()['message'];
                    log_message('error', 'Face Register: Failed to save image ' . ($index + 1) . ' to ' . $file_path . ' - ' . print_r(error_get_last(), true));
                }
            } else {
                $save_errors[] = "Invalid image format for image " . ($index + 1);
            }
        }

        if (count($saved_images) < 3) {
            $error_msg = 'Error saving images. ' . implode('; ', $save_errors);
            if (!is_writable($upload_path)) {
                $error_msg .= ' | Directory not writable: ' . $upload_path;
            }
            log_message('error', 'Face Register: ' . $error_msg . ' | Upload path: ' . $upload_path . ' | Dir exists: ' . (is_dir($upload_path) ? 'yes' : 'no') . ' | Writable: ' . (is_writable($upload_path) ? 'yes' : 'no'));
            echo json_encode(array('status' => 'error', 'message' => $error_msg));
            return;
        }

        // Prepare data
        $staff_data = array(
            'staff_id'            => $staff_id,
            'registration_number' => $registration_number,
            'first_name'          => $first_name,
            'last_name'           => $last_name,
            'email'               => $email,
            'phone'               => $phone,
            'designation'         => $designation,
            'person_type'         => 'staff',
            'face_images'         => $saved_images
        );

        $insert_id = $this->Face_attendance_student_model->add_student($staff_data);

        if ($insert_id) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Staff face registered successfully!',
                'student_id' => $insert_id
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error saving staff data'));
        }
    }

    /**
     * Update existing face registration (re-capture)
     */
    private function update_face_registration($face_reg_id) {
        $captured_images = array();
        for ($i = 1; $i <= 5; $i++) {
            $image_data = $this->input->post("captured_image_$i");
            if (!empty($image_data)) {
                $captured_images[] = $image_data;
            }
        }

        if (count($captured_images) < 3) {
            echo json_encode(array('status' => 'error', 'message' => 'Please capture at least 3 face images'));
            return;
        }

        // Get existing record
        $existing = $this->Face_attendance_student_model->get_student($face_reg_id);
        if (!$existing) {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
            return;
        }

        // Create directory for face images
        $upload_path = './uploads/face_attendance_images/' . $existing->registration_number . '/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0777, true);
        } else {
            // Clean up old images
            foreach (glob($upload_path . '*.png') as $old_file) {
                unlink($old_file);
            }
        }

        // Save new images
        $saved_images = array();
        $save_errors = array();
        foreach ($captured_images as $index => $image_data) {
            $image_parts = explode(';base64,', $image_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $file_name = ($index + 1) . '.png';
                $file_path = $upload_path . $file_name;

                $result = @file_put_contents($file_path, $image_base64);
                if ($result !== false) {
                    $saved_images[] = $file_name;
                } else {
                    $save_errors[] = "Failed to save image " . ($index + 1) . ": " . error_get_last()['message'];
                    log_message('error', 'Face Register Update: Failed to save image ' . ($index + 1) . ' to ' . $file_path . ' - ' . print_r(error_get_last(), true));
                }
            } else {
                $save_errors[] = "Invalid image format for image " . ($index + 1);
            }
        }

        if (count($saved_images) < 3) {
            $error_msg = 'Error saving images. ' . implode('; ', $save_errors);
            if (!is_writable($upload_path)) {
                $error_msg .= ' | Directory not writable: ' . $upload_path;
            }
            log_message('error', 'Face Register Update: ' . $error_msg);
            echo json_encode(array('status' => 'error', 'message' => $error_msg));
            return;
        }

        // Update database
        $update_data = array(
            'face_images' => $saved_images,
            'last_updated' => date('Y-m-d H:i:s')
        );

        if ($this->Face_attendance_student_model->update_student($face_reg_id, $update_data)) {
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Face registration updated successfully!'
            ));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error updating face data'));
        }
    }

    /**
     * Delete registration
     */
    public function delete_registration() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_delete')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $reg_id = $this->input->post('reg_id');

        if (empty($reg_id)) {
            echo json_encode(array('status' => 'error', 'message' => 'ID is required'));
            return;
        }

        // Get record data before deletion
        $record = $this->Face_attendance_student_model->get_student($reg_id);

        if (!$record) {
            echo json_encode(array('status' => 'error', 'message' => 'Record not found'));
            return;
        }

        // Delete images directory
        $upload_path = './uploads/face_attendance_images/' . $record->registration_number . '/';
        if (is_dir($upload_path)) {
            $this->delete_directory($upload_path);
        }

        // Delete from database
        if ($this->Face_attendance_student_model->delete_student($reg_id)) {
            echo json_encode(array('status' => 'success', 'message' => 'Face registration deleted successfully'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Error deleting record'));
        }
    }

    /**
     * Mark Attendance - Face Recognition Page
     */
    public function mark_attendance() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Attendance');
        $this->session->set_userdata('sub_menu', 'admin/face_attendance_register/mark_attendance');

        $data['title'] = 'Mark Face Attendance';
        $data['registered_students'] = $this->Face_attendance_student_model->get_all_by_type('student');
        $data['registered_staff'] = $this->Face_attendance_student_model->get_all_by_type('staff');
        $data['classlist'] = $this->class_model->get();

        // Get payroll/cutoff info for display
        $data['payroll_info'] = $this->Face_attendance_student_model->get_payroll_info_for_display();

        // Get today's existing staff attendance (to show who already has attendance)
        $staff_ids = array();
        foreach ($data['registered_staff'] as $rs) {
            if ($rs->staff_id) {
                $staff_ids[] = $rs->staff_id;
            }
        }
        $data['staff_today_att'] = $this->Face_attendance_student_model->get_staff_today_attendance($staff_ids);

        $this->load->view('layout/header', $data);
        $this->load->view('admin/face_attendance_register/mark_attendance', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Get all registered persons for face recognition (AJAX)
     * Returns both students and staff, with existing staff_att status
     */
    public function get_registered_students() {
        $type = $this->input->post('type'); // 'student', 'staff', or 'all'

        if (empty($type) || $type === 'all') {
            $registered = $this->Face_attendance_student_model->get_all_students(1);
        } else {
            $registered = $this->Face_attendance_student_model->get_all_by_type($type);
        }

        $person_list = array();
        $staff_ids = array();

        foreach ($registered as $person) {
            $face_images = json_decode($person->face_images, true);
            $image_urls = array();

            if (is_array($face_images)) {
                $encoded_reg = rawurlencode($person->registration_number);
                foreach ($face_images as $image) {
                    $image_urls[] = base_url('uploads/face_attendance_images/' . $encoded_reg . '/' . $image);
                }
            }

            if ($person->person_type === 'staff' && $person->staff_id) {
                $staff_ids[] = $person->staff_id;
            }

            $person_list[] = array(
                'id'                  => $person->id,
                'registration_number' => $person->registration_number,
                'first_name'          => $person->first_name,
                'last_name'           => $person->last_name,
                'admission_no'        => $person->admission_no,
                'class_id'            => $person->class_id,
                'section_id'          => $person->section_id,
                'email'               => $person->email,
                'person_type'         => $person->person_type,
                'staff_id'            => $person->staff_id,
                'face_images'         => $image_urls
            );
        }

        // Get today's existing staff attendance from staff_attendance table
        $staff_att_status = array();
        if (!empty($staff_ids)) {
            $staff_att_status = $this->Face_attendance_student_model->get_staff_today_attendance($staff_ids);
        }

        // Get payroll cutoff info
        $payroll_info = $this->Face_attendance_student_model->get_payroll_info_for_display();

        echo json_encode(array(
            'status'        => 'success',
            'students'      => $person_list,
            'staff_att'     => $staff_att_status,
            'payroll_info'  => $payroll_info
        ));
    }

    /**
     * Save attendance records (AJAX)
     * Handles both student and staff attendance.
     * For STAFF: also syncs to the main staff_attendance table with payroll cutoff checks.
     */
    public function save_attendance() {
        if (!$this->rbac->hasPrivilege('face_attendance_register', 'can_add')) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }

        $attendance_data = $this->input->post('attendance_data');

        if (empty($attendance_data)) {
            echo json_encode(array('status' => 'error', 'message' => 'No attendance data provided'));
            return;
        }

        $attendance_records = json_decode($attendance_data, true);

        if (!is_array($attendance_records) || count($attendance_records) == 0) {
            echo json_encode(array('status' => 'error', 'message' => 'Invalid attendance data'));
            return;
        }

        $success_count = 0;
        $error_count = 0;
        $session_id = uniqid('session_', true);
        $marked_by = $this->customlib->getStaffID();
        $today = date('Y-m-d');
        $now = date('H:i:s');

        // Track staff sync results for response
        $staff_sync_results = array();

        foreach ($attendance_records as $record) {
            $person_type = isset($record['person_type']) ? $record['person_type'] : 'student';

            $attendance_entry = array(
                'face_student_id'     => isset($record['student_id']) ? $record['student_id'] : null,
                'registration_number' => $record['registration_number'],
                'attendance_date'     => $today,
                'attendance_time'     => $now,
                'attendance_status'   => $record['status'],
                'confidence_score'    => isset($record['confidence']) ? $record['confidence'] : null,
                'session_id'          => $session_id,
                'class_id'            => isset($record['class_id']) ? $record['class_id'] : null,
                'section_id'          => isset($record['section_id']) ? $record['section_id'] : null,
                'marked_by'           => $marked_by,
                'recognition_method'  => 'face_recognition',
                'person_type'         => $person_type,
                'notes'               => isset($record['notes']) ? $record['notes'] : null
            );

            // ---- STAFF: Sync to main staff_attendance table ----
            if ($person_type === 'staff' && $record['status'] === 'Present') {
                // Get staff_id from face_attendance_students
                $face_person = $this->Face_attendance_student_model->get_student($record['student_id']);
                $staff_db_id = null;

                if ($face_person && $face_person->staff_id) {
                    $staff_db_id = $face_person->staff_id;

                    // Sync to staff_attendance table
                    $sync_result = $this->Face_attendance_student_model->sync_staff_attendance($staff_db_id, array(
                        'date'          => $today,
                        'check_in_time' => $now,
                        'remark'        => 'Face recognition at ' . $now,
                        'face_reg_id'   => $record['student_id']
                    ));

                    if ($sync_result['success']) {
                        $attendance_entry['staff_att_sync'] = $sync_result['skipped'] ? 0 : 1;
                        $attendance_entry['staff_att_type_id'] = $sync_result['skipped'] ? null : $sync_result['type_info']['type_id'];

                        $staff_sync_results[] = array(
                            'name'     => $face_person->first_name . ' ' . $face_person->last_name,
                            'synced'   => !$sync_result['skipped'],
                            'message'  => $sync_result['message'],
                            'type'     => $sync_result['type_info']['type_name'],
                            'is_late'  => $sync_result['type_info']['is_late']
                        );
                    } else {
                        $staff_sync_results[] = array(
                            'name'     => $face_person->first_name . ' ' . $face_person->last_name,
                            'synced'   => false,
                            'message'  => $sync_result['message'],
                            'type'     => 'Error',
                            'is_late'  => false
                        );
                    }
                }
            }

            if ($this->Face_attendance_student_model->mark_attendance($attendance_entry)) {
                $success_count++;
            } else {
                $error_count++;
            }
        }

        // Log the attendance session
        $detected_faces = $this->input->post('detected_faces');
        $log_entry = array(
            'session_date'       => date('Y-m-d H:i:s'),
            'detected_faces'     => $detected_faces ? $detected_faces : count($attendance_records),
            'recognized_faces'   => $success_count,
            'unknown_faces'      => $error_count,
            'recognition_details' => json_encode(array(
                'session_id'        => $session_id,
                'total_records'     => count($attendance_records),
                'success'           => $success_count,
                'errors'            => $error_count,
                'staff_sync_results' => $staff_sync_results
            )),
            'created_by'         => $marked_by
        );

        $this->Face_attendance_student_model->log_attendance_session($log_entry);

        // Build response message
        $msg = "Attendance saved! Present: $success_count, Errors: $error_count";
        if (!empty($staff_sync_results)) {
            $synced_count = count(array_filter($staff_sync_results, function($r) { return $r['synced']; }));
            $skipped_count = count(array_filter($staff_sync_results, function($r) { return !$r['synced']; }));
            $late_count = count(array_filter($staff_sync_results, function($r) { return $r['is_late']; }));
            $msg .= " | Staff: $synced_count synced, $skipped_count existing";
            if ($late_count > 0) {
                $msg .= ", $late_count marked Late (after grace period)";
            }
        }

        echo json_encode(array(
            'status'             => 'success',
            'message'            => $msg,
            'session_id'         => $session_id,
            'success_count'      => $success_count,
            'error_count'        => $error_count,
            'staff_sync_results' => $staff_sync_results
        ));
    }

    /**
     * Get attendance records for a date (AJAX)
     */
    public function get_attendance_records() {
        $date = $this->input->get('date') ? $this->input->get('date') : date('Y-m-d');
        $records = $this->Face_attendance_student_model->get_attendance_by_date($date);

        echo json_encode(array(
            'status'  => 'success',
            'date'    => $date,
            'records' => $records
        ));
    }

    /**
     * Helper function to delete directory recursively
     */
    private function delete_directory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Simple student fetch without users join.
     * The students table may not have corresponding users records,
     * so we skip the users.role check entirely.
     */
    private function _get_students_simple() {
        $session_id = $this->setting_model->getCurrentSession();
        $this->db->reset_query();
        $this->db->select('students.id, students.admission_no, students.firstname, students.middlename, students.lastname, students.email, students.mobileno, students.image, students.is_active, classes.class, classes.id as class_id, sections.section, sections.id as section_id');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('students.is_active', 'yes');
        $this->db->order_by('students.firstname', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}
