<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Schsettings extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('media_storage');
        $this->load->library('upload');
        $this->load->model('setting_model');
        $this->load->model(array('class_section_time_model','sidebarmenu_model'));
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('general_setting', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/index');

        $timezoneList             = $this->customlib->timezone_list();
        $session_result           = $this->session_model->get();
        $data['sessionlist']      = $session_result;
        $currency_formats         = $this->customlib->currency_format();
        $month_list               = $this->customlib->getMonthList();
        $days_list                = $this->customlib->getDayList();
        $data['currency_formats'] = $currency_formats;
        $data['daysList']         = $days_list;
        $data['timezoneList']     = $timezoneList;
        $data['monthList']        = $month_list;
        $dateFormat               = $this->customlib->getDateFormat();
        $currency                 = $this->customlib->getCurrency();
        $data['dateFormatList']   = $dateFormat;
        $data['currencyList']     = $currency;
        $currencyPlace            = $this->customlib->getCurrencyPlace();
        $data['currencyPlace']    = $currencyPlace;
        $setting                  = $this->setting_model->getSetting();
        $setting->base_url        = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path     = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']           = $setting;
        $this->load->view('layout/header', $data);
        $this->load->view('setting/settingList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function ajax_editlogo()
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_start();

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'trim|required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
                'id' => form_error('id'),
            );
            $array = array('success' => false, 'error' => $data);
            ob_end_clean();
            echo json_encode($array);
        } else {
            $id = $this->input->post('id');

            $setting = $this->setting_model->getSetting();

            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                $img_name = $this->media_storage->fileupload("file", "./uploads/school_content/logo/");

                if ($img_name === null) {
                    $array = array('success' => false, 'error' => array('file' => 'Unable to save file. Please check upload folder permissions.'), 'message' => '');
                    ob_end_clean();
                    echo json_encode($array);
                    return;
                }
            } else {
                $img_name = $setting->image;
            }
            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                $this->media_storage->filedelete($setting->image, "uploads/school_content/logo");
            }

            $data_record = array('id' => $id, 'image' => $img_name);
            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('success_message'));
            ob_end_clean();
            echo json_encode($array);
        }
    }

    public function ajax_editadmin_smalllogo()
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_start();

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'trim|required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
            );
            $array = array('success' => false, 'error' => $data);
            ob_end_clean();
            echo json_encode($array);
        } else {
            $id = $this->input->post('id');

            $setting = $this->setting_model->getSetting();

            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                $img_name = $this->media_storage->fileupload("file", "./uploads/school_content/admin_small_logo/");
                if ($img_name === null) {
                    $array = array('success' => false, 'error' => array('file' => 'File upload failed. Please check file type and try again.'), 'message' => '');
                    ob_end_clean();
                    echo json_encode($array);
                    return;
                }
            } else {
                $img_name = $setting->admin_small_logo;
            }
            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                $this->media_storage->filedelete($setting->admin_small_logo, "uploads/school_content/admin_small_logo");
            }
            $data_record = array('id' => $id, 'admin_small_logo' => $img_name);
            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('success_message'));
            ob_end_clean();
            echo json_encode($array);
        }
    }

    public function ajax_editadmin_adminlogo()
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_start();

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'trim|required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
            );
            $array = array('success' => false, 'error' => $data);
            ob_end_clean();
            echo json_encode($array);
        } else {
            $id = $this->input->post('id');

            $setting = $this->setting_model->getSetting();

            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                $img_name = $this->media_storage->fileupload("file", "./uploads/school_content/admin_logo/");
                if ($img_name === null) {
                    $array = array('success' => false, 'error' => array('file' => 'File upload failed. Please check file type and try again.'), 'message' => '');
                    ob_end_clean();
                    echo json_encode($array);
                    return;
                }
            } else {
                $img_name = $setting->admin_logo;
            }
            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                if ($setting->admin_logo != '') {
                    $this->media_storage->filedelete($setting->admin_logo, "uploads/school_content/admin_logo");
                }
            }

            $data_record = array('id' => $id, 'admin_logo' => $img_name);
            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('success_message'));
            ob_end_clean();
            echo json_encode($array);
        }
    }

    public function editLogo($id)
    {
        $data['title']       = 'School Logo';
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $data['id']          = $id;
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('setting/editLogo', $data);
            $this->load->view('layout/footer', $data);
        } else {
            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/school_content/logo/" . $img_name);
            }
            $data_record = array('id' => $id, 'image' => $img_name);
            $this->setting_model->add($data_record);
            $this->session->set_flashdata('msg', '<div class="alert alert-left">' . $this->lang->line('update_message') . '</div>');
            redirect('schsettings/index');
        }
    }

    public function handle_upload()
    {   
        log_message('debug', '=== handle_upload callback START ===');
        log_message('debug', 'FILES[file] raw: ' . json_encode($_FILES['file']));
        
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts  = array('jpg', 'jpeg', 'png', 'gif', 'webp');
            $allowedMimes = array('image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/x-png', 'image/webp');
            $temp         = explode(".", $_FILES["file"]["name"]);
            $extension    = strtolower(end($temp));
            
            log_message('debug', 'File name: ' . $_FILES["file"]["name"]);
            log_message('debug', 'File extension: ' . $extension);
            log_message('debug', 'File browser-type: ' . $_FILES["file"]["type"]);
            log_message('debug', 'File error code: ' . $_FILES["file"]["error"]);
            log_message('debug', 'File size: ' . $_FILES["file"]["size"]);
            log_message('debug', 'tmp_name exists: ' . (file_exists($_FILES["file"]["tmp_name"]) ? 'YES' : 'NO'));
            
            // Check for actual upload errors
            $upload_errors = array(
                1 => 'The uploaded file exceeds the upload_max_filesize directive',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive',
                3 => 'The uploaded file was only partially uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk',
                8 => 'A PHP extension stopped the file upload',
            );
            if ($_FILES["file"]["error"] > 0) {
                $err_msg = isset($upload_errors[$_FILES["file"]["error"]]) ? $upload_errors[$_FILES["file"]["error"]] : $this->lang->line('file_type_not_allowed');
                log_message('debug', 'Upload error code > 0: ' . $err_msg);
                $this->form_validation->set_message('handle_upload', $err_msg);
                return false;
            }

            // Detect real MIME type from file content (not browser-reported)
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
            finfo_close($finfo);

            log_message('debug', 'finfo detected MIME: ' . $mime_type);
            log_message('debug', 'MIME in allowed list: ' . (in_array($mime_type, $allowedMimes) ? 'YES' : 'NO'));
            log_message('debug', 'Ext in allowed list: ' . (in_array($extension, $allowedExts) ? 'YES' : 'NO'));

            if (!in_array($mime_type, $allowedMimes)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_type_not_allowed') . ' (detected: ' . $mime_type . ')');
                return false;
            }
            if (!in_array($extension, $allowedExts)) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('extension_not_allowed'));
                return false;
            }
            if ($_FILES["file"]["size"] > 1024000) {
                $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . " 1MB");
                return false;
            }
            log_message('debug', '=== handle_upload PASSED ===');
            return true;
        } else {
            log_message('debug', 'handle_upload: No file found in _FILES');
            $this->form_validation->set_message('handle_upload', $this->lang->line('logo_file_is_required'));
            return false;
        }
    }

    public function view($id)
    {
        $data['title']   = 'Setting List';
        $setting         = $this->setting_model->get($id);
        $data['setting'] = $setting;
        $this->load->view('layout/header', $data);
        $this->load->view('setting/settingShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getSchsetting()
    {
        $data = $this->setting_model->getSetting();
        echo json_encode($data);
    }

    public function generalsetting()
    {
        $this->form_validation->set_rules('currency_format', $this->lang->line('currency_format'), 'trim|required');
        $this->form_validation->set_rules('sch_session_id', $this->lang->line('session'), 'trim|required');
        $this->form_validation->set_rules('sch_name', $this->lang->line('school_name'), 'trim|required');
        $this->form_validation->set_rules('sch_phone', $this->lang->line('phone'), 'trim|required');
        $this->form_validation->set_rules('sch_start_month', $this->lang->line('start_month'), 'trim|required');
        $this->form_validation->set_rules('sch_address', $this->lang->line('address'), 'trim|required');
        $this->form_validation->set_rules('sch_email', $this->lang->line('email'), 'trim|required');
        $this->form_validation->set_rules('sch_timezone', $this->lang->line('timezone'), 'trim|required');
        $this->form_validation->set_rules('currency_place', $this->lang->line('currency_place'), 'trim|required');
        $this->form_validation->set_rules('sch_date_format', $this->lang->line('date_format'), 'trim|required');
        $this->form_validation->set_rules('sch_start_week', $this->lang->line('start_day_of_week'), 'trim|required');
        $this->form_validation->set_rules('base_url', $this->lang->line('url'), 'trim|required');
        $this->form_validation->set_rules('folder_path', $this->lang->line('folder_path'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $data = array(
                'sch_session_id'  => form_error('sch_session_id'),
                'sch_name'        => form_error('sch_name'),
                'sch_phone'       => form_error('sch_phone'),
                'sch_start_month' => form_error('sch_start_month'),
                'sch_start_week'  => form_error('sch_start_week'),
                'sch_address'     => form_error('sch_address'),
                'sch_email'       => form_error('sch_email'),
                'sch_timezone'    => form_error('sch_timezone'),
                'currency_place'  => form_error('currency_place'),
                'currency_format' => form_error('currency_format'),
                'sch_date_format' => form_error('sch_date_format'),
                'base_url'        => form_error('base_url'),
                'folder_path'     => form_error('folder_path'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $data = array(
                'id'              => $this->input->post('sch_id'),
                'session_id'      => $this->input->post('sch_session_id'),
                'name'            => $this->input->post('sch_name'),
                'phone'           => $this->input->post('sch_phone'),
                'dise_code'       => $this->input->post('sch_dise_code'),
                'start_month'     => $this->input->post('sch_start_month'),
                'start_week'      => $this->input->post('sch_start_week'),
                'address'         => $this->input->post('sch_address'),
                'email'           => $this->input->post('sch_email'),
                'timezone'        => $this->input->post('sch_timezone'),
                'date_format'     => $this->input->post('sch_date_format'),
                'currency_format' => $this->input->post('currency_format'),
                'currency_place'  => $this->input->post('currency_place'),
                'base_url'        => $this->input->post('base_url'),
                'folder_path'     => $this->input->post('folder_path'),
            );

            $this->setting_model->add($data);

            $this->session->userdata['admin']['base_url']        = $this->input->post('base_url');
            $this->session->userdata['admin']['folder_path']     = $this->input->post('folder_path');
            $this->session->userdata['admin']['currency_format'] = $this->input->post('currency_format');
            $this->session->userdata['admin']['date_format']     = $this->input->post('sch_date_format');
            $this->session->userdata['admin']['start_week']      = date("w", strtotime($this->input->post('sch_start_week')));
            $this->session->userdata['admin']['timezone']        = $this->input->post('sch_timezone');
            $this->session->userdata['admin']['currency_place']  = $this->input->post('currency_place');
            $array                                               = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function ajax_applogo()
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_start();

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'trim|required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
            );
            $array = array('success' => false, 'error' => $data);
            ob_end_clean();
            echo json_encode($array);
        } else {

            $id      = $this->input->post('id');
            $setting = $this->setting_model->getSetting();

            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {

                $img_name = $this->media_storage->fileupload("file", "./uploads/school_content/logo/app_logo/");
                if ($img_name === null) {
                    $array = array('success' => false, 'error' => array('file' => 'File upload failed. Please check file type and try again.'), 'message' => '');
                    ob_end_clean();
                    echo json_encode($array);
                    return;
                }
            } else {
                $img_name = $setting->app_logo;
            }
            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                if ($setting->app_logo != '') {
                    $this->media_storage->filedelete($setting->app_logo, "uploads/school_content/logo/app_logo/");
                }
            }

            $data_record = array('id' => $id, 'app_logo' => $img_name);

            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('update_message'));
            ob_end_clean();
            echo json_encode($array);
        }
    }

    public function check_admission_digit()
    {
        $adm_start_from = $this->input->post('adm_start_from');
        $adm_no_digit   = $this->input->post('adm_no_digit');
        if ($adm_no_digit != "") {
            if (strlen($adm_start_from) == $adm_no_digit) {
                return true;
            }
            $this->form_validation->set_message('check_admission_digit', $this->lang->line('admission_start_from') . ' ' . $adm_no_digit . ' ' . $this->lang->line('digit_long'));
            return false;
        }
        return true;
    }

    public function check_staff_id_digit()
    {
        $adm_start_from   = $this->input->post('staffid_start_from');
        $staffid_no_digit = $this->input->post('staffid_no_digit');
        if ($staffid_no_digit != "") {
            if (strlen($adm_start_from) == $staffid_no_digit) {
                return true;
            }
            $this->form_validation->set_message('check_staff_id_digit', $this->lang->line('staff_id_start_from_must_be') . ' ' . strlen($adm_start_from) . ' ' . $this->lang->line('digit_long'));
            return false;
        }
        return true;
    }

    public function logo()
    {        
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/logo');
    
        $setting              = $this->setting_model->getSetting();
        // $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        // $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header');
        $this->load->view('setting/logo', $data);
        $this->load->view('layout/footer');
    }

    public function miscellaneous()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/miscellaneous');
        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header');
        $this->load->view('setting/miscellaneous', $data);
        $this->load->view('layout/footer');
    }

    public function savemiscellaneous()
    {
        $event_reminder = $this->input->post('event_reminder');
        if ($event_reminder == 'enabled') {
            $calendar_event_reminder = $this->input->post('calendar_event_reminder');
        } else {
            $calendar_event_reminder = '0';
        }

        $data = array(
            'id'                       => $this->input->post('sch_id'),
            'my_question'              => $this->input->post('my_question'),
            'exam_result'              => $this->input->post('exam_result'),
            'class_teacher'            => $this->input->post('class_teacher'),
            'superadmin_restriction'   => $this->input->post('superadmin_restriction_mode'),
            'calendar_event_reminder'  => $calendar_event_reminder,
            'event_reminder'           => $this->input->post('event_reminder'),
            'staff_notification_email' => $this->input->post('staff_notification_email'),
        );

        $this->setting_model->add($data);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);

    }

    public function backendtheme()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/backendtheme');
        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header');
        $this->load->view('setting/backendtheme', $data);
        $this->load->view('layout/footer');
    }

    public function savebackendtheme()
    {
        $this->form_validation->set_rules('theme', $this->lang->line('theme'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $data = array(
                'theme' => form_error('theme'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $data = array(
                'id'    => $this->input->post('sch_id'),
                'theme' => $this->input->post('theme'),
            );

            $this->setting_model->add($data);
            $this->session->userdata['admin']['theme'] = $this->input->post('theme');
            $array                                     = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function mobileapp()
    {
        $app_ver = $this->config->item('app_ver');
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/mobileapp');
        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $data['app_response'] = $this->auth->andapp_validate();
        $this->load->view('layout/header');
        $this->load->view('setting/mobileapp', $data);
        $this->load->view('layout/footer');
    }

    public function savemobileapp()
    {
        $data = array(
            'id'                             => $this->input->post('sch_id'),
            'mobile_api_url'                 => $this->input->post('mobile_api_url'),
            'app_primary_color_code'         => $this->input->post('app_primary_color_code'),
            'app_secondary_color_code'       => $this->input->post('app_secondary_color_code'),
            'admin_app_primary_color_code'   => $this->input->post('admin_app_primary_color_code'),
            'admin_app_secondary_color_code' => $this->input->post('admin_app_secondary_color_code'),
            'admin_mobile_api_url'           => $this->input->post('admin_mobile_api_url'),
        );

        $this->setting_model->add($data);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function studentguardianpanel()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/studentguardianpanel');
        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header');
        $this->load->view('setting/studentguardianpanel', $data);
        $this->load->view('layout/footer');
    }

    public function studentguardian()
    {
        $parent_panel_login  = 0;
        $student_panel_login = 0;

        if (isset($_POST['student_panel_login'])) {
            $student_panel_login = 1;
            if (isset($_POST['parent_panel_login'])) {
                $parent_panel_login = 1;
            }
        }

        $data = array(
            'id'                  => $this->input->post('sch_id'),
            'student_timeline'    => $this->input->post('student_timeline'),
            'student_login'       => json_encode($this->input->post('student_login')),
            'parent_login'        => json_encode($this->input->post('parent_login')),
            'student_panel_login' => $student_panel_login,
            'parent_panel_login'  => $parent_panel_login,
        );

        $this->setting_model->add($data);

        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function fees()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/fees');

        $setting                        = $this->setting_model->getSetting();
        $setting->base_url              = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path           = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']                 = $setting;
        $data['duplicate_fees_invoice'] = explode(",", $setting->is_duplicate_fees_invoice);
        $this->load->view('layout/header');
        $this->load->view('setting/fees', $data);
        $this->load->view('layout/footer');
    }

    public function savefees()
    {
        $this->form_validation->set_rules('is_student_feature_lock', $this->lang->line('is_student_feature_lock'), 'trim|required');
        $this->form_validation->set_rules('is_offline_fee_payment', $this->lang->line('offline_fee_payment_in_student_panel'), 'trim|required');

        $this->form_validation->set_rules('is_duplicate_fees_invoice[]', $this->lang->line('print_fees_receipt_for'), 'trim|required');
        $this->form_validation->set_rules('lock_grace_period', $this->lang->line('fees_payment_grace_period'), 'trim|required');
        $this->form_validation->set_rules('fee_due_days', $this->lang->line('carry_forward_fees_due_days'), 'trim|required');
        $this->form_validation->set_rules('single_page_print', $this->lang->line('single_page_print'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $data = array(
                'is_duplicate_fees_invoice' => form_error('is_duplicate_fees_invoice[]'),
                'single_page_print'         => form_error('single_page_print'),
                'fee_due_days'              => form_error('fee_due_days'),
                'lock_grace_period'         => form_error('lock_grace_period'),
                'is_student_feature_lock'   => form_error('is_student_feature_lock'),
                'is_offline_fee_payment'   => form_error('is_offline_fee_payment'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {

            $is_duplicate_fees_invoice = implode(",", $this->input->post('is_duplicate_fees_invoice'));
            $data                      = array(
                'id'                        => $this->input->post('sch_id'),
                'is_duplicate_fees_invoice' => $is_duplicate_fees_invoice,
                'single_page_print'         => $this->input->post('single_page_print'),
                'fee_due_days'              => $this->input->post('fee_due_days'),
                'lock_grace_period'         => $this->input->post('lock_grace_period'),
                'collect_back_date_fees'    => $this->input->post('collect_back_date_fees'),
                'is_student_feature_lock'   => $this->input->post('is_student_feature_lock'),
                'is_offline_fee_payment'    => $this->input->post('is_offline_fee_payment'),
                'offline_bank_payment_instruction'  => $this->input->post('offline_bank_payment_instruction'),
            );

            $this->setting_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function idautogeneration()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/idautogeneration');

        $digit                = $this->customlib->getDigits();
        $data['digitList']    = $digit;
        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header');
        $this->load->view('setting/idautogeneration', $data);
        $this->load->view('layout/footer');
    }

    public function saveidautogeneration()
    {
        $this->form_validation->set_rules('sch_id', 'Id', 'trim|required');

        if ($this->input->post('adm_auto_insert')) {
            $this->form_validation->set_rules('adm_prefix', $this->lang->line('admission_no_prefix'), 'trim|required');
            $this->form_validation->set_rules('adm_start_from', $this->lang->line('admission_start_from'), 'trim|integer|required');
            $this->form_validation->set_rules('adm_no_digit', $this->lang->line('admission_no_digit'), 'trim|integer|required|callback_check_admission_digit');
        }
        if ($this->input->post('sroll_auto_insert')) {
            $this->form_validation->set_rules('sroll_prefix', $this->lang->line('roll_no_prefix'), 'trim|required');
            $this->form_validation->set_rules('sroll_start_from', $this->lang->line('roll_start_from'), 'trim|integer|required');
            $this->form_validation->set_rules('sroll_no_digit', $this->lang->line('roll_no_digit'), 'trim|integer|required|callback_check_sroll_digit');
        }
        
        if ($this->input->post('staffid_auto_insert')) {

            $this->form_validation->set_rules('staffid_prefix', $this->lang->line('staff_id_prefix'), 'trim|required');
            $this->form_validation->set_rules('staffid_start_from', $this->lang->line('staff_id_start_from'), 'trim|integer|required');
            $this->form_validation->set_rules('staffid_no_digit', $this->lang->line('staff_id_digit'), 'trim|integer|required|callback_check_staff_id_digit');
        }

        if ($this->form_validation->run() == false) {
            $data = array(
                'adm_start_from'     => form_error('adm_start_from'),
                'adm_prefix'         => form_error('adm_prefix'),
                'adm_no_digit'       => form_error('adm_no_digit'),
                
                'sroll_start_from'     => form_error('sroll_start_from'),
                'sroll_prefix'         => form_error('sroll_prefix'),
                'sroll_no_digit'       => form_error('sroll_no_digit'),

                
                'staffid_start_from' => form_error('staffid_start_from'),
                'staffid_prefix'     => form_error('staffid_prefix'),
                'staffid_no_digit'   => form_error('staffid_no_digit'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $setting_result = $this->setting_model->getSetting();

            $data = array(
                'id'                  => $this->input->post('sch_id'),
                'adm_start_from'      => $this->input->post('adm_start_from'),
                'adm_prefix'          => $this->input->post('adm_prefix'),
                'adm_no_digit'        => $this->input->post('adm_no_digit'),
                'adm_auto_insert'     => $this->input->post('adm_auto_insert'),
                
                'sroll_start_from'      => $this->input->post('sroll_start_from'),
                'sroll_prefix'          => $this->input->post('sroll_prefix'),
                'sroll_no_digit'        => $this->input->post('sroll_no_digit'),
                'sroll_auto_insert'     => $this->input->post('sroll_auto_insert'),
                
                'staffid_start_from'  => $this->input->post('staffid_start_from'),
                'staffid_prefix'      => $this->input->post('staffid_prefix'),
                'staffid_no_digit'    => $this->input->post('staffid_no_digit'),
                'staffid_auto_insert' => $this->input->post('staffid_auto_insert'),
            );

            $data['adm_update_status']     = 1;
            $data['staffid_update_status'] = 1;
            $data['sroll_update_status']   = 1;
            if ($this->input->post('adm_auto_insert')) {
                if ($setting_result->adm_prefix != $this->input->post('adm_prefix') ||
                    $setting_result->adm_start_from != $this->input->post('adm_start_from') ||
                    $setting_result->adm_no_digit != $this->input->post('adm_no_digit')
                ) {
                    $data['adm_update_status'] = 0;
                }
            }

            if ($this->input->post('sroll_auto_insert')) {
                if ($setting_result->sroll_prefix != $this->input->post('sroll_prefix') ||
                    $setting_result->sroll_start_from != $this->input->post('sroll_start_from') ||
                    $setting_result->sroll_no_digit != $this->input->post('sroll_no_digit')
                ) {
                    $data['sroll_update_status'] = 0;
                }
            }


            if ($this->input->post('staffid_auto_insert')) {
                if ($setting_result->staffid_prefix != $this->input->post('staffid_prefix') ||
                    $setting_result->staffid_start_from != $this->input->post('staffid_start_from') ||
                    $setting_result->staffid_no_digit != $this->input->post('staffid_no_digit')
                ) {
                    $data['staffid_update_status'] = 0;
                }
            }

            $data['adm_update_status'];
             $data['sroll_update_status'];
            $this->setting_model->add($data);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function attendancetype()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/attendancetype');

        $class_list=$this->class_section_time_model->allClassSections();
        $data['class_list']=$class_list;

        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header', $data);
        $this->load->view('setting/attendancetype', $data);
        $this->load->view('layout/footer', $data);
    }

    public function maintenance()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/maintenance');

        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header', $data);
        $this->load->view('setting/maintenance', $data);
        $this->load->view('layout/footer', $data);
    }

    public function saveattendancetype()
    {
        $this->form_validation->set_rules('attendence_type', $this->lang->line('attendance_type'), 'trim|required');
        

        if ($this->form_validation->run() == false) {
            $data = array(
                'attendence_type' => form_error('attendence_type'),
                 
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $data = array(
                'id'               => $this->input->post('sch_id'),
                'attendence_type'  => $this->input->post('attendence_type'),
                'biometric_device' => $this->input->post('biometric_device'),
                'biometric'        => $this->input->post('biometric'),
                'low_attendance_limit' => $this->input->post('low_attendance_limit'),
            );
            
            $this->setting_model->add($data);
                    $period_attendance=0;
                    $student_attendance=1;
                     if($this->input->post('attendence_type')){
                          $period_attendance=1;
                          $student_attendance=0;
                     }

              $this->sidebarmenu_model->update_submenu_by_key(
                  [
                      ['key'=>'period_attendance_by_date','is_active'=>$period_attendance],
                      ['key'=>'period_attendance','is_active'=>$period_attendance],
                      ['key'=>'student_attendance','is_active'=>$student_attendance],
                      ['key'=>'attendance_by_date','is_active'=>$student_attendance]
                  ]
                );


            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function save_maintenance()
    {
        $this->form_validation->set_rules('maintenance_mode', $this->lang->line('maintenance_mode'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $data = array(
                'maintenance_mode' => form_error('maintenance_mode'),
            );
            $array = array('status' => 0, 'error' => $data);
            echo json_encode($array);
        } else {
            $data = array(
                'id'               => $this->input->post('sch_id'),
                'maintenance_mode' => $this->input->post('maintenance_mode'),
            );
            $this->setting_model->add($data);

            $array = array('status' => 1, 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }
    
    public function login_page_background()
    {        
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/login_page_background');
    
        $setting              = $this->setting_model->getSetting();
        // $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        // $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header');
        $this->load->view('setting/login_page_background', $data);
        $this->load->view('layout/footer');
    }
    
    public function add_admin_login_background()
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_start();

        $this->form_validation->set_rules('id', $this->lang->line('id'), 'trim|required');
        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $data = array(
                'file' => form_error('file'),
            );
            $array = array('success' => false, 'error' => $data);
            ob_end_clean();
            echo json_encode($array);
        } else {
            $id = $this->input->post('id');
            $logo_type = $this->input->post('logo_type');
 
            $setting = $this->setting_model->getSetting();
            if($logo_type != 'admin_logo'){                
                $background =   $setting->user_login_page_background;
            }else {
                $background =   $setting->admin_login_page_background;
            }
            
            if (isset($_FILES["file"]) && $_FILES['file']['name'] != '' && (!empty($_FILES['file']['name']))) {
                $img_name = $this->media_storage->fileupload("file", "./uploads/school_content/login_image/");
            } else {
                $img_name = $background;
            }
            
            if (isset($background)) {
                $this->media_storage->filedelete($background, "uploads/school_content/login_image");
            }
            
            if($logo_type != 'admin_logo'){                
                $data_record = array('id' => $id, 'user_login_page_background' => $img_name);
            }else {                 
                $data_record = array('id' => $id, 'admin_login_page_background' => $img_name);
            }          
            
            $this->setting_model->add($data_record);
            $array = array('success' => true, 'error' => '', 'message' => $this->lang->line('success_message'));
            ob_end_clean();
            echo json_encode($array);
        }
    }
    
    
    public function check_sroll_digit()
    {
        $sroll_start_from = $this->input->post('sroll_start_from');
        $sroll_no_digit   = $this->input->post('sroll_no_digit');
        if ($sroll_no_digit != "") {
            if (strlen($sroll_start_from) == $sroll_no_digit) {
                return true;
            }
            $this->form_validation->set_message('check_sroll_digit', $this->lang->line('roll_start_from') . ' ' . $sroll_no_digit . ' ' . $this->lang->line('digit_long'));
            return false;
        }
        return true;
    }
    
    
    
    

    // biometric time settings
    

    // public function biometraicattendancetypesave() {
    //     // Enable error reporting for debugging
    //     ini_set('display_errors', 1);
    //     error_reporting(E_ALL);

    //     $this->form_validation->set_rules('checkintimestart', 'Check In Time Start', 'required|trim');
    //     $this->form_validation->set_rules('checkintimeend', 'Check In Time End', 'required|trim');
    //     $this->form_validation->set_rules('checkoutstart', 'Check Out Time Start', 'required|trim');
    //     $this->form_validation->set_rules('checkoutend', 'Check Out Time End', 'required|trim');

    //     if ($this->form_validation->run() == FALSE) {
    //         $response = array(
    //             'status' => 'fail',
    //             'error' => validation_errors()
    //         );
    //     } else {
    //         // Match the field names with database columns
    //         $data = array(
    //             'start_time' => $this->input->post('checkintimestart'),
    //             'checkin_end' => $this->input->post('checkintimeend'),
    //             'checkout_start' => $this->input->post('checkoutstart'),
    //             'checkout_end' => $this->input->post('checkoutend')
    //         );

    //         $result = $this->setting_model->save_attendance_settings($data);
            
    //         if ($result) {
    //             $response = array(
    //                 'status' => 'success',
    //                 'message' => 'Biometric attendance settings updated successfully'
    //             );
    //         } else {
    //             $response = array(
    //                 'status' => 'fail',
    //                 'error' => 'Fail to update'
    //             );
    //         }
    //     }

    //     header('Content-Type: application/json');
    //     echo json_encode($response);
    // }


    public function biometraicattendancetypesave() {
        try {
            // Check if it's a POST request
            if (!$this->input->post()) {
                $response = array('status' => 'fail', 'message' => 'Invalid request method');
                echo json_encode($response);
                return;
            }
    
            // Get POST data
            $input_data = array(
                'checkintimestart' => $this->input->post('checkintimestart'),
                'checkintimeend' => $this->input->post('checkintimeend'),
                'checkoutstart' => $this->input->post('checkoutstart'),
                'checkoutend' => $this->input->post('checkoutend')
            );
    
            // Validate required fields
            foreach ($input_data as $key => $value) {
                if (empty($value)) {
                    $response = array('status' => 'fail', 'message' => 'All fields are required');
                    echo json_encode($response);
                    return;
                }
            }
    
            // Call model to save data
            $result = $this->setting_model->save_attendance_settings($input_data);
    
            if ($result) {
                $response = array('status' => 'success', 'message' => 'Settings saved successfully');
            } else {
                $response = array('status' => 'fail', 'message' => 'Failed to save settings');
            }
    
            echo json_encode($response);
    
        } catch (Exception $e) {
            log_message('error', 'Error in biometraicattendancetypesave: ' . $e->getMessage());
            $response = array('status' => 'fail', 'message' => 'An error occurred');
            echo json_encode($response);
        }
    }




    
    public function biometricsetting() {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/biometricsetting');
        
        $data['result'] = $this->setting_model->getSetting();
        // $data['biometric_setting'] = $this->attendancetype_model->get_biometric_settings();
        
        $this->load->view('layout/header', $data);
        $this->load->view('setting/biometricsetting', $data);
        $this->load->view('layout/footer', $data);
    }

    public function themecolour()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/themecolour');
        $setting              = $this->setting_model->getSetting();
        $setting->base_url    = ($setting->base_url == "") ? base_url() : $setting->base_url;
        $setting->folder_path = ($setting->folder_path == "") ? FCPATH : $setting->folder_path;
        $data['result']       = $setting;
        $this->load->view('layout/header');
        $this->load->view('setting/themecolour', $data);
        $this->load->view('layout/footer');
    }

    public function savethemecolour()
    {
        $fields = array(
            'theme_header_colour'  => 'Header Colour',
            'theme_sidebar_colour' => 'Sidebar Colour',
        );

        foreach ($fields as $field => $label) {
            $this->form_validation->set_rules($field, $label, 'trim|max_length[20]');
        }
        $this->form_validation->set_rules('theme_header_gradient', 'Header Gradient', 'trim|max_length[20]');
        $this->form_validation->set_rules('theme_sidebar_gradient', 'Sidebar Gradient', 'trim|max_length[20]');
        $this->form_validation->set_rules('theme_body_bg', 'Body Background', 'trim|max_length[20]');
        $this->form_validation->set_rules('theme_accent_start', 'Accent Start', 'trim|max_length[20]');
        $this->form_validation->set_rules('theme_accent_end', 'Accent End', 'trim|max_length[20]');

        if ($this->form_validation->run() == false) {
            $errors = array();
            foreach ($fields as $field => $label) {
                $err = form_error($field);
                if ($err) {
                    $errors[$field] = $err;
                }
            }
            $array = array('status' => 'fail', 'error' => $errors);
            echo json_encode($array);
        } else {
            // If a value matches the default shown in the picker, save NULL
            // so the theme default colours are NOT overridden
            $defaults = array(
                'theme_header_colour'  => '#367fa9',
                'theme_header_gradient'  => '#367fa9',
                'theme_sidebar_colour' => '#222d32',
                'theme_sidebar_gradient' => '#222d32',
                'theme_body_bg'          => '#ecf0f5',
                'theme_accent_start'     => '#f33057',
                'theme_accent_end'       => '#3858f9',
            );

            $data = array('id' => $this->input->post('sch_id'));
            foreach ($defaults as $field => $default) {
                $val = $this->input->post($field);
                $data[$field] = (strtolower($val) === strtolower($default)) ? null : $val;
            }

            $this->setting_model->add($data);

            // Update session
            $admin = $this->session->userdata('admin');
            if ($admin) {
                foreach ($defaults as $field => $default) {
                    $admin[$field] = $data[$field];
                }
                $this->session->set_userdata('admin', $admin);
            }

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function resethemecolour()
    {
        $setting = $this->setting_model->getSetting();
        $data    = array(
            'id'                     => $setting->id,
            'theme_colour'           => NULL,
            'theme_header_colour'    => NULL,
            'theme_header_gradient'  => NULL,
            'theme_sidebar_colour'   => NULL,
            'theme_sidebar_gradient' => NULL,
            'theme_body_bg'          => NULL,
            'theme_accent_start'     => NULL,
            'theme_accent_end'       => NULL,
        );
        $this->setting_model->add($data);

        // Clear session
        $admin = $this->session->userdata('admin');
        if ($admin) {
            unset($admin['theme_colour']);
            unset($admin['theme_header_colour']);
            unset($admin['theme_header_gradient']);
            unset($admin['theme_sidebar_colour']);
            unset($admin['theme_sidebar_gradient']);
            unset($admin['theme_body_bg']);
            unset($admin['theme_accent_start']);
            unset($admin['theme_accent_end']);
            $this->session->set_userdata('admin', $admin);
        }

        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    public function whatsappsettings()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'schsettings/index');
        $this->session->set_userdata('subsub_menu', 'schsettings/whatsappsettings');
        $setting              = $this->setting_model->getSetting();
        $data['result']       = $setting;
        $this->load->view('layout/header', $data);
        $this->load->view('setting/whatsapp_chat_widget', $data);
        $this->load->view('layout/footer', $data);
    }

    public function time_check()
    {
        $fields = array(
            'front_side_whatsapp',
            'admin_panel_whatsapp',
            'student_panel_whatsapp'
        );

        foreach ($fields as $field) {
            $from = strtotime($this->input->post("{$field}_from"));
            $to = strtotime($this->input->post("{$field}_to"));

            if (!empty($from) && !empty($to) && $from >= $to) {
                $this->form_validation->set_message('time_check', '%s cannot be less than from time');
                return false;
            }
        }

        return true;
    }

    public function savewhatsappsettings()
    {
        $this->form_validation->set_rules('sch_id', ('sch_id'), 'trim|required');
        $this->form_validation->set_rules('time_to', $this->lang->line('time_to'), 'callback_time_check');

        $whatsapp_fields = array(
            'front_side_whatsapp'    => 'front_side_whatsapp_mobile',
            'admin_panel_whatsapp'   => 'admin_panel_whatsapp_mobile',
            'student_panel_whatsapp' => 'student_panel_whatsapp_mobile'
        );

        foreach ($whatsapp_fields as $input_name => $field_name) {
            if ($this->input->post($input_name)) {
                $this->form_validation->set_rules($field_name, $this->lang->line('mobile_no'), 'trim|required');
            }

            $from_field = "{$input_name}_from";
            $to_field   = "{$input_name}_to";

            if (empty($this->input->post($from_field)) && !empty($this->input->post($to_field))) {
                $this->form_validation->set_rules($from_field, $this->lang->line('time_from'), 'trim|required');
            }

            if (!empty($this->input->post($from_field)) && empty($this->input->post($to_field))) {
                $this->form_validation->set_rules($to_field, $this->lang->line('time_to'), 'trim|required');
            }
        }

        if ($this->form_validation->run() == false) {
            $fields = array('sch_id', 'front_side_whatsapp', 'admin_panel_whatsapp', 'student_panel_whatsapp', 'front_side_whatsapp_mobile', 'admin_panel_whatsapp_mobile', 'student_panel_whatsapp_mobile', 'front_side_whatsapp_from', 'front_side_whatsapp_to', 'admin_panel_whatsapp_from', 'admin_panel_whatsapp_to', 'student_panel_whatsapp_from', 'student_panel_whatsapp_to', 'time_to');

            $error = array();
            foreach ($fields as $field) {
                $error[$field] = form_error($field);
            }

            $array = array('status' => 'fail', 'error' => $error);
            echo json_encode($array);
        } else {
            $time_fields = array('front_side_whatsapp_from', 'front_side_whatsapp_to', 'admin_panel_whatsapp_from', 'admin_panel_whatsapp_to', 'student_panel_whatsapp_from', 'student_panel_whatsapp_to');
            foreach ($time_fields as $field) {
                $$field = $this->input->post($field) ?: null;
            }

            $data = array(
                'id'                        => $this->input->post('sch_id'),
                'front_side_whatsapp'       => $this->input->post('front_side_whatsapp'),
                'front_side_whatsapp_mobile'    => $this->input->post('front_side_whatsapp_mobile'),
                'front_side_whatsapp_from'      => $front_side_whatsapp_from,
                'front_side_whatsapp_to'        => $front_side_whatsapp_to,
                'admin_panel_whatsapp'      => $this->input->post('admin_panel_whatsapp'),
                'admin_panel_whatsapp_mobile'   => $this->input->post('admin_panel_whatsapp_mobile'),
                'admin_panel_whatsapp_from'     => $admin_panel_whatsapp_from,
                'admin_panel_whatsapp_to'       => $admin_panel_whatsapp_to,
                'student_panel_whatsapp'   => $this->input->post('student_panel_whatsapp'),
                'student_panel_whatsapp_mobile' => $this->input->post('student_panel_whatsapp_mobile'),
                'student_panel_whatsapp_from'   => $student_panel_whatsapp_from,
                'student_panel_whatsapp_to'     => $student_panel_whatsapp_to,
            );

            $this->setting_model->add($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }
}
