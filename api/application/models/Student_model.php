<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Student_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $CI = &get_instance();
        $CI->load->model('setting_model');
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_date    = $this->setting_model->getDateYmd();
    }

  public function getStudentByClassSectionID($class_id = null, $section_id = null, $id = null, $session_id=null)
    {
        if($session_id != ""){
           $session_id= $session_id;
        }else{
            $session_id=$this->current_session;
        }

        $this->db->select('pickup_point.name as pickup_point_name,student_session.route_pickup_point_id,student_session.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname, students.middlename, students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast, school_houses.house_name,   students.dob ,students.current_address, students.previous_school,
            students.guardian_is,students.parent_id,
            students.permanent_address,students.category_id,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email,sessions.session, users.username,users.password,students.dis_reason,students.dis_note,students.app_key,students.parent_app_key')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('sessions', 'sessions.id = student_session.session_id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');       
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_session.route_pickup_point_id', 'left');
        $this->db->join('pickup_point', 'route_pickup_point.pickup_point_id = pickup_point.id', 'left');
        $this->db->join('transport_route', 'route_pickup_point.transport_route_id = transport_route.id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = student_session.vehroute_id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->where('student_session.class_id', $class_id);
        $this->db->where('student_session.section_id', $section_id);
        $this->db->where('student_session.session_id', $session_id);
        $this->db->where('users.role', 'student');

        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->where('students.is_active', 'yes');
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function read_siblings_students($parent_id)
    {
        $this->db->select('students.*,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('parent_id', $parent_id);
        $this->db->where('students.is_active', 'yes');
        $this->db->group_by('students.id');
        $query = $this->db->get();
        return $query->result();
    }

    public function get($id = null)
    {  
        $this->db->select('pickup_point.name as pickup_point_name,IFNULL(student_session.route_pickup_point_id,0) as `route_pickup_point_id`,student_session.transport_fees,students.app_key,students.parent_app_key,student_session.vehroute_id,vehicle_routes.route_id,vehicle_routes.vehicle_id,transport_route.route_title,vehicles.vehicle_no,hostel_rooms.room_no,vehicles.driver_name,vehicles.driver_contact,vehicles.vehicle_model,vehicles.manufacture_year,vehicles.driver_licence,vehicles.vehicle_photo,hostel.id as `hostel_id`,hostel.hostel_name,room_types.id as `room_type_id`,room_types.room_type ,students.hostel_room_id,student_session.id as `student_session_id`,student_session.fees_discount,classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_no,students.admission_date,students.firstname,students.middlename,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode , students.note, students.religion, students.cast, school_houses.house_name,   students.dob ,students.current_address, students.previous_school,
            students.guardian_is,students.parent_id,
            students.permanent_address,students.category_id,categories.category,students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.father_pic ,students.height ,students.weight,students.measurement_date, students.mother_pic , students.guardian_pic , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.blood_group,students.school_house_id,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is,students.rte,students.guardian_email, users.username,users.password,students.dis_reason,students.dis_note,students.disable_at,IFNULL(currencies.short_name,0) as currency_name,IFNULL(currencies.symbol,0) as symbol,IFNULL(currencies.base_price,0) as base_price,IFNULL(currencies.id,0) as `currency_id`, student_session.session_id,sessions.session')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('hostel_rooms', 'hostel_rooms.id = students.hostel_room_id', 'left');
        $this->db->join('hostel', 'hostel.id = hostel_rooms.hostel_id', 'left');
        $this->db->join('room_types', 'room_types.id = hostel_rooms.room_type_id', 'left');
        $this->db->join('route_pickup_point', 'route_pickup_point.id = student_session.route_pickup_point_id', 'left');
        $this->db->join('pickup_point', 'route_pickup_point.pickup_point_id = pickup_point.id', 'left');
        $this->db->join('vehicle_routes', 'vehicle_routes.id = student_session.vehroute_id', 'left');
        $this->db->join('transport_route', 'vehicle_routes.route_id = transport_route.id', 'left');
        $this->db->join('vehicles', 'vehicles.id = vehicle_routes.vehicle_id', 'left');
        $this->db->join('school_houses', 'school_houses.id = students.school_house_id', 'left');
        $this->db->join('users', 'users.user_id = students.id', 'left');
         $this->db->join('currencies', 'currencies.id=users.currency_id', 'left');
        $this->db->join('categories', 'categories.id = students.category_id', 'left');
        $this->db->join('sessions', 'sessions.id = student_session.session_id', 'left');
        
        $this->db->where('student_session.session_id', $this->current_session);
        $this->db->where('users.role', 'student');
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {
            $this->db->where('students.is_active', 'yes');
            $this->db->order_by('students.id', 'desc');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_object();
        } else {
            return $query->result_array();
        }
    }

    public function getStudentSession($id)
    {
        $query = $this->db->query("SELECT  max(sessions.id) as student_session_id, max(sessions.session) as session from sessions join student_session on (sessions.id = student_session.session_id)  where student_session.student_id = " . $id);
        return $query->row_array();
    }

    public function getRecentRecord($id = null)
    {
        $this->db->select('classes.id AS `class_id`,classes.class,sections.id AS `section_id`,sections.section,students.id,students.admission_no , students.roll_no,students.admission_date,students.firstname,  students.lastname,students.image,    students.mobileno, students.email ,students.state ,   students.city , students.pincode ,     students.religion,     students.dob ,students.current_address,    students.permanent_address,students.category_id,    students.adhar_no,students.samagra_id,students.bank_account_no,students.bank_name, students.ifsc_code , students.guardian_name , students.guardian_relation,students.guardian_phone,students.guardian_address,students.is_active ,students.created_at ,students.updated_at,students.father_name,students.father_phone,students.father_occupation,students.mother_name,students.mother_phone,students.mother_occupation,students.guardian_occupation,students.gender,students.guardian_is')->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->where('student_session.session_id', $this->current_session);
        if ($id != null) {
            $this->db->where('students.id', $id);
        } else {

        }
        $this->db->order_by('students.id', 'desc');
        $this->db->limit(5);
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function getstudentdoc($id)
    {
        $this->db->select()->from('student_doc');
        $this->db->where('student_id', $id);
        $query = $this->db->get();
        return $query->result();
    }

    public function add($data)
    {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('students', $data);
        }
    }

    public function adddoc($data)
    {
        $this->db->insert('student_doc', $data);
        return $this->db->insert_id();
    }

    public function updatestudentlanguage($data)
    {
        if (isset($data['user_id'])) {
            $this->db->where('user_id', $data['user_id']);
            $this->db->update('users', $data);
        }
    }

    /**
     * Get student report data by filters
     *
     * Retrieves student report data based on optional filter parameters.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     *
     * @param mixed $class_id Class ID or array of class IDs (optional)
     * @param mixed $section_id Section ID or array of section IDs (optional)
     * @param mixed $category_id Category ID or array of category IDs (optional)
     * @param int $session_id Session ID (optional, defaults to current session)
     * @return array Array of student records
     */
    public function getStudentReportByFilters($class_id = null, $section_id = null, $category_id = null, $session_id = null)
    {
        // Use current session if not provided
        if (empty($session_id)) {
            $session_id = $this->current_session;
        }

        // Build the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.roll_no,
            students.firstname,
            students.middlename,
            students.lastname,
            students.father_name,
            students.dob,
            students.gender,
            students.mobileno,
            students.email,
            students.samagra_id,
            students.adhar_no,
            students.rte,
            students.guardian_name,
            students.guardian_phone,
            students.guardian_relation,
            students.current_address,
            students.permanent_address,
            students.is_active,
            classes.id AS class_id,
            classes.class,
            sections.id AS section_id,
            sections.section,
            students.category_id,
            categories.category
        ');
        $this->db->from('students');
        $this->db->join('student_session', 'student_session.student_id = students.id');
        $this->db->join('classes', 'student_session.class_id = classes.id');
        $this->db->join('sections', 'sections.id = student_session.section_id');
        $this->db->join('categories', 'students.category_id = categories.id', 'left');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Apply category filter if provided
        if ($category_id !== null && !empty($category_id)) {
            if (is_array($category_id) && count($category_id) > 0) {
                $this->db->where_in('students.category_id', $category_id);
            } else {
                $this->db->where('students.category_id', $category_id);
            }
        }

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get Guardian Report by Filters
     *
     * Retrieves guardian report data with optional filtering by class, section, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $section_id  Section ID (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student records with guardian information
     */
    public function getGuardianReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.firstname,
            students.middlename,
            students.lastname,
            students.mobileno,
            students.guardian_name,
            students.guardian_relation,
            students.guardian_phone,
            students.father_name,
            students.father_phone,
            students.mother_name,
            students.mother_phone,
            students.is_active,
            student_session.class_id,
            student_session.section_id,
            classes.class,
            sections.section
        ');

        // Join with student_session table
        $this->db->join('student_session', 'students.id = student_session.student_id', 'inner');

        // Join with classes table
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');

        // Join with sections table
        $this->db->join('sections', 'student_session.section_id = sections.id', 'inner');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get('students');
        return $query->result_array();
    }

    /**
     * Get Admission Report by Filters
     *
     * Retrieves admission report data with optional filtering by class, year, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $year        Admission year (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student records with admission information
     */
    public function getAdmissionReportByFilters($class_id = null, $year = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.admission_date,
            students.firstname,
            students.middlename,
            students.lastname,
            students.mobileno,
            students.guardian_name,
            students.guardian_relation,
            students.guardian_phone,
            students.is_active,
            student_session.class_id,
            student_session.section_id,
            student_session.session_id,
            classes.class,
            sections.section,
            sessions.session
        ');

        // Join with student_session table
        $this->db->join('student_session', 'students.id = student_session.student_id', 'inner');

        // Join with classes table
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');

        // Join with sections table
        $this->db->join('sections', 'student_session.section_id = sections.id', 'inner');

        // Join with sessions table
        $this->db->join('sessions', 'student_session.session_id = sessions.id', 'inner');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply year filter if provided (filter by admission_date year)
        if ($year !== null && !empty($year)) {
            if (is_array($year) && count($year) > 0) {
                // For multiple years, use group_start/group_end with OR conditions
                $this->db->group_start();
                foreach ($year as $index => $y) {
                    if ($index === 0) {
                        $this->db->where('YEAR(students.admission_date)', $y);
                    } else {
                        $this->db->or_where('YEAR(students.admission_date)', $y);
                    }
                }
                $this->db->group_end();
            } else {
                $this->db->where('YEAR(students.admission_date)', $year);
            }
        }

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get('students');
        return $query->result_array();
    }

    /**
     * Get Login Detail Report by Filters
     *
     * Retrieves student login credential information with optional filtering by class, section, and session.
     * Handles null/empty parameters gracefully by returning all records when filters are not provided.
     * Supports both single values and arrays for multi-select functionality.
     * Includes username and password from users table.
     *
     * @param mixed $class_id    Class ID (single value, array, or null)
     * @param mixed $section_id  Section ID (single value, array, or null)
     * @param int   $session_id  Session ID (defaults to current session if not provided)
     * @return array Array of student records with login credential information
     */
    public function getLoginDetailReportByFilters($class_id = null, $section_id = null, $session_id = null)
    {
        // If session_id is not provided, use current session
        if (empty($session_id)) {
            $session_id = $this->setting_model->getCurrentSession();
        }

        // Start building the query
        $this->db->select('
            students.id,
            students.admission_no,
            students.firstname,
            students.middlename,
            students.lastname,
            students.mobileno,
            students.email,
            students.is_active,
            student_session.class_id,
            student_session.section_id,
            student_session.session_id,
            classes.class,
            sections.section,
            sessions.session,
            users.username,
            users.password
        ');

        // Join with student_session table
        $this->db->join('student_session', 'students.id = student_session.student_id', 'inner');

        // Join with classes table
        $this->db->join('classes', 'student_session.class_id = classes.id', 'inner');

        // Join with sections table
        $this->db->join('sections', 'sections.id = student_session.section_id', 'inner');

        // Join with sessions table
        $this->db->join('sessions', 'student_session.session_id = sessions.id', 'inner');

        // Join with users table to get login credentials
        $this->db->join('users', 'students.id = users.user_id AND users.role = "student"', 'left');

        // Apply session filter
        $this->db->where('student_session.session_id', $session_id);

        // Apply active status filter
        $this->db->where('students.is_active', 'yes');

        // Apply class filter if provided
        if ($class_id !== null && !empty($class_id)) {
            if (is_array($class_id) && count($class_id) > 0) {
                $this->db->where_in('student_session.class_id', $class_id);
            } else {
                $this->db->where('student_session.class_id', $class_id);
            }
        }

        // Apply section filter if provided
        if ($section_id !== null && !empty($section_id)) {
            if (is_array($section_id) && count($section_id) > 0) {
                $this->db->where_in('student_session.section_id', $section_id);
            } else {
                $this->db->where('student_session.section_id', $section_id);
            }
        }

        // Group by student ID to avoid duplicates
        $this->db->group_by('students.id');

        // Order by admission number
        $this->db->order_by('students.admission_no', 'asc');

        // Execute query
        $query = $this->db->get('students');
        return $query->result_array();
    }

}
