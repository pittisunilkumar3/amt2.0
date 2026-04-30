<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Documentreport extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tcgeneration_model');
        $this->load->model('class_model');
        $this->load->model('section_model');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    public function index()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/document_reports');
        $data['title']      = 'Document Reports';
        
        $data['sch_setting'] = $this->sch_setting_detail;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/documentreport/index', $data);
        $this->load->view('layout/footer', $data);
    }

    public function tcreport()
    {
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/document_reports/tc_report');
        $class_id   = $this->input->post('class_id');
        $section_id = $this->input->post('section_id');
        
        $data['class_id'] = $class_id;
        $data['section_id'] = $section_id;
        
        $data['classlist']  = $this->class_model->get();
        if ($class_id) {
            $data['section_list'] = $this->section_model->getClassBySection($class_id);
        }
        
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $data['resultlist'] = $this->tcgeneration_model->get_tc_report($class_id, $section_id);
        } else {
            $data['resultlist'] = array();
        }
        
        $data['sch_setting'] = $this->sch_setting_detail;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/documentreport/tcreport', $data);
        $this->load->view('layout/footer', $data);
    }
}
