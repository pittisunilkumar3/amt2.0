<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Onlinestudent_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get online admission fee collection report
     * Returns online admission payments with student details for a date range
     *
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function getOnlineAdmissionFeeCollectionReport($start_date, $end_date)
    {
        $query = "SELECT online_admissions.*,
                         online_admission_payment.*,
                         classes.class,
                         sections.section,
                         categories.category,
                         hostel.hostel_name,
                         room_types.room_type,
                         hostel_rooms.room_no,
                         transport_route.route_title,
                         vehicles.vehicle_no,
                         school_houses.house_name
                  FROM online_admissions
                  JOIN online_admission_payment ON online_admissions.id = online_admission_payment.online_admission_id
                  LEFT JOIN class_sections ON class_sections.id = online_admissions.class_section_id
                  LEFT JOIN classes ON class_sections.class_id = classes.id
                  LEFT JOIN sections ON sections.id = class_sections.section_id
                  LEFT JOIN hostel_rooms ON hostel_rooms.id = online_admissions.hostel_room_id
                  LEFT JOIN hostel ON hostel.id = hostel_rooms.hostel_id
                  LEFT JOIN room_types ON room_types.id = hostel_rooms.room_type_id
                  LEFT JOIN categories ON online_admissions.category_id = categories.id
                  LEFT JOIN vehicle_routes ON vehicle_routes.id = online_admissions.vehroute_id
                  LEFT JOIN transport_route ON vehicle_routes.route_id = transport_route.id
                  LEFT JOIN vehicles ON vehicles.id = vehicle_routes.vehicle_id
                  LEFT JOIN school_houses ON school_houses.id = online_admissions.school_house_id
                  WHERE DATE_FORMAT(online_admission_payment.date, '%Y-%m-%d') >= " . $this->db->escape($start_date) . "
                  AND DATE_FORMAT(online_admission_payment.date, '%Y-%m-%d') <= " . $this->db->escape($end_date) . "
                  ORDER BY online_admission_payment.date DESC, online_admissions.id DESC";

        $query = $this->db->query($query);
        return $query->result_array();
    }

    /**
     * Get online admission payment summary
     *
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function getOnlineAdmissionPaymentSummary($start_date, $end_date)
    {
        $this->db->select('COUNT(DISTINCT online_admission_payment.online_admission_id) as total_admissions,
                          SUM(online_admission_payment.paid_amount) as total_amount,
                          online_admission_payment.payment_mode,
                          COUNT(online_admission_payment.id) as payment_count');
        $this->db->from('online_admission_payment');
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") <=', $end_date);
        $this->db->group_by('online_admission_payment.payment_mode');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Get online admissions by class
     *
     * @param string $start_date Start date (Y-m-d format)
     * @param string $end_date End date (Y-m-d format)
     * @return array
     */
    public function getOnlineAdmissionsByClass($start_date, $end_date)
    {
        $this->db->select('classes.class,
                          sections.section,
                          COUNT(DISTINCT online_admissions.id) as admission_count,
                          SUM(online_admission_payment.paid_amount) as total_amount');
        $this->db->from('online_admissions');
        $this->db->join('online_admission_payment', 'online_admissions.id = online_admission_payment.online_admission_id');
        $this->db->join('class_sections', 'class_sections.id = online_admissions.class_section_id', 'left');
        $this->db->join('classes', 'class_sections.class_id = classes.id', 'left');
        $this->db->join('sections', 'sections.id = class_sections.section_id', 'left');
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") >=', $start_date);
        $this->db->where('DATE_FORMAT(online_admission_payment.date, "%Y-%m-%d") <=', $end_date);
        $this->db->group_by('classes.id, sections.id');
        $this->db->order_by('classes.id', 'asc');

        $query = $this->db->get();
        return $query->result_array();
    }

}

