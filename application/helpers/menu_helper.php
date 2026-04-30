<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

if (!function_exists('active_link')) {

    function activate_menu($controller, $action)
    {
        $CI     = get_instance();
        $method = $CI->router->fetch_method();
        $class  = $CI->router->fetch_class();
        return ($method == $action && $controller == $class) ? 'active' : '';
    }

    function set_Topmenu($top_menu_name)
    {
        $CI               = get_instance();
        $session_top_menu = $CI->session->userdata('top_menu');
        if ($session_top_menu == $top_menu_name) {
            return 'active';
        }
        return "";
    }

    function set_Submenu($sub_menu_name)
    {
        $CI               = get_instance();
        $session_sub_menu = $CI->session->userdata('sub_menu');
        if ($session_sub_menu == $sub_menu_name) {
            return 'active';
        }
        return "";
    }

    function set_SubSubmenu($sub_menu_name)
    {
        $CI               = get_instance();
        $session_sub_menu = $CI->session->userdata('subsub_menu');
        if ($session_sub_menu == $sub_menu_name) {
            return 'active';
        }
        return "";
    }
}

function access_denied()
{
    redirect('admin/unauthorized');
}

function update_config_installed()
{
    $CI          = &get_instance();
    $config_path = APPPATH . 'config/config.php';
    $CI->load->helper('file');
    @chmod($config_path, FILE_WRITE_MODE);
    $config_file = read_file($config_path);
    $config_file = trim($config_file);
    $config_file = str_replace("\$config['installed'] = false;", "\$config['installed'] = true;", $config_file);
    $config_file = str_replace("\$config['base_url'] = '';", "\$config['base_url'] = '" . site_url() . "';", $config_file);
    if (!$fp = fopen($config_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $config_file, strlen($config_file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($config_path, FILE_READ_MODE);
    return true;
}

function update_autoload_installed()
{
    $CI            = &get_instance();
    $autoload_path = APPPATH . 'config/autoload.php';
    $CI->load->helper('file');
    @chmod($autoload_path, FILE_WRITE_MODE);
    $autoload_file = read_file($autoload_path);
    $autoload_file = trim($autoload_file);
    $autoload_file = str_replace("\$autoload['libraries'] = array('database', 'session', 'form_validation')", "\$autoload['libraries'] = array('email','session', 'form_validation', 'upload', 'pagination','Customlib')", $autoload_file);
    if (!$fp = fopen($autoload_path, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
        return false;
    }
    flock($fp, LOCK_EX);
    fwrite($fp, $autoload_file, strlen($autoload_file));
    flock($fp, LOCK_UN);
    fclose($fp);
    @chmod($config_path, FILE_READ_MODE);
    return true;
}

function delete_dir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_dir($file);
        } else {
            unlink($file);
        }
    }
    if (rmdir($dirPath)) {
        return true;
    }
    return false;
}

function admin_url($url = '')
{
    if ($url == '') {
        return site_url() . 'site/login';
    } else {
        return site_url() . 'site/login';
    }
}

if (!function_exists('main_menu_array')) {

    function main_menu_array($find_array)
    {
        $array = array(

            'account_module' => array(
                'accountreport'            => array('search', 'index'),
                'accounttranscationreport' => array('index', 'search'),
                'accounttranscation'       => array('index', 'edit'),
                'addaccount'               => array('index', 'edit'),
                'accountcategorygroup'     => array('index', 'edit'),
                'accountcategory'          => array('index', 'edit'),
                'accounttype'              => array('index', 'edit'),
            ),

            'front_office' => array(
                'enquiry'         => array('index'),
                'visitors'        => array('index'),
                'generalcall'     => array('index', 'edit'),
                'dispatch'        => array('index', 'editdispatch'),
                'receive'         => array('index', 'editreceive'),
                'complaint'       => array('index', 'edit'),
                'visitorspurpose' => array('index', 'edit'),
                'complainttype'   => array('index', 'editcomplainttype'),
                'source'          => array('index', 'edit'),
                'reference'       => array('index', 'edit'),
            ),

            'student_information' => array(
                'student'         => array('search', 'create', 'import', 'disablestudentslist', 'multiclass', 'bulkdelete', 'view', 'edit'),
                'onlinestudent'   => array('index', 'edit'),
                'category'        => array('index', 'edit'),
                'schoolhouse'     => array('index', 'edit'),
                'disable_reason'  => array('index', 'edit'),
            ),

            'fees_collection' => array(
                'studentfee'     => array('index', 'addfee', 'searchpayment', 'feesearch', 'advancePayment', 'advanceSearch', 'ajaxAdvanceSearch', 'createAdvancePayment', 'getAdvanceBalance', 'printAdvanceReceipt', 'getAdvanceHistory'),
                'feemaster'      => array('index', 'assign', 'edit'),
                'feegroup'       => array('index', 'edit'),
                'feetype'        => array('index', 'edit'),
                'feediscount'    => array('index', 'edit', 'assign'),
                'feesforward'    => array('index'),
                'feereminder'    => array('setting'),
                'offlinepayment' => array('index'),
            ),

            'income' => array(
                'income'        => array('index', 'edit', 'incomesearch'),
                'incomehead'    => array('index', 'edit'),
            ),

            'expense' => array(
                'expense'       => array('index', 'edit', 'expensesearch'),
                'expensehead'   => array('index', 'edit'),
            ),

            'examinations' => array(
                'examgroup'     => array('index', 'edit', 'addexam'),
                'exam_schedule' => array('index'),
                'examresult'    => array('index', 'admitcard', 'marksheet'),
                'admitcard'     => array('index', 'edit'),
                'marksheet'     => array('index', 'edit'),
                'grade'         => array('index', 'edit'),
                'marksdivision'         => array('index', 'edit'),
            ),

            'attendance' => array(
                'approve_leave'    => array('index'),
                'stuattendence'    => array('index', 'edit', 'attendencereport'),
                'subjectattendence'    => array('index', 'reportbydate'),

            ),

            'online_examinations' => array(
                'onlineexam'    => array('index', 'evalution', 'assign'),
                'question'      => array('index', 'read'),
            ),

            'lesson_plan' => array(
                'syllabus'      => array('index', 'status'),
                'lessonplan'    => array('lesson', 'topic', 'copylesson', 'edittopic', 'editlesson'),
            ),

            'academics' => array(
                'timetable'     => array('classreport', 'mytimetable', 'create'),
                'teacher'       => array('assign_class_teacher', 'update_class_teacher'),
                'stdtransfer'   => array('index'),
                'subjectgroup'  => array('index', 'edit'),
                'subject'       => array('index', 'edit'),
                'classes'       => array('index', 'edit'),
                'sections'      => array('index', 'edit'),
            ),

            'human_resource' => array(
                'staff'             => array('index', 'profile', 'edit', 'leaverequest', 'rating', 'disablestafflist', 'create'),
                'staffattendance'   => array('index'),
                'payroll'           => array('index', 'edit', 'create'),
                'leaverequest'      => array('leaverequest'),
                'leavetypes'        => array('index', 'leaveedit', 'createleavetype'),
                'department'        => array('department', 'departmentedit'),
                'designation'       => array('designation', 'designationedit'),
            ),

            'communicate' => array(
                'notification'      => array('index', 'edit', 'add'),
                'mailsms'           => array('compose', 'compose_sms', 'index', 'schedule', 'email_template', 'sms_template', 'edit_schedule'),
                'student'           => array('bulkmail'),
            ),

            'download_center' => array(
                'contenttype'       => array('index', 'edit'),
                'content'           => array('list', 'upload'),
                'video_tutorial'    => array('index'),
            ),

            'homework' => array(
                'homework'      => array('index', 'dailyassignment'),
            ),

            'library' => array(
                'book'      => array('getall', 'edit', 'index', 'import'),
                'member'    => array('index', 'issue', 'student', 'teacher'),
            ),

            'inventory' => array(
                'issueitem'      => array('index', 'create'),
                'itemstock'      => array('index', 'edit'),
                'item'           => array('index', 'edit'),
                'itemcategory'   => array('index', 'edit'),
                'itemstore'      => array('index', 'edit', 'create'),
                'itemsupplier'   => array('index', 'edit', 'create'),
            ),

            'transport' => array(
                'transport'      => array('feemaster'),
                'pickuppoint'    => array('index', 'assign', 'student_fees'),
                'route'    => array('index', 'edit'),
                'vehicle'    => array('index'),
                'vehroute'    => array('index', 'edit'),
            ),

            'hostel' => array(
                'hostelroom'  => array('index', 'edit'),
                'roomtype'    => array('index', 'edit'),
                'hostel'      => array('index', 'edit', 'feemaster', 'assignhostelfee', 'assignhostelfeestudent', 'assignhostelfeepost'),
            ),

            'certificate' => array(
                'certificate'           => array('index', 'edit'),
                'generatecertificate'   => array('index', 'search'),
                'studentidcard'         => array('index', 'edit'),
                'generateidcard'        => array('search'),
                'staffidcard'           => array('index', 'edit'),
                'generatestaffidcard'   => array('index', 'search'),
            ),

            'resume' => array(
                'resume'        => array('index', 'download', 'resume_setting', 'student_resume_details'),
            ),

            'front_cms' => array(
                'events'        => array('index', 'edit', 'create'),
                'gallery'       => array('index', 'edit', 'create'),
                'notice'        => array('index', 'edit', 'create'),
                'media'         => array('index'),
                'page'          => array('index', 'edit', 'create'),
                'menus'         => array('index', 'additem'),
                'banner'        => array('index'),
            ),

            'alumni' => array(
                'alumni'        => array('alumnilist', 'events'),
            ),

            'reports' => array(
                'report'            => array('alumnireport', 'inventory', 'issueinventory', 'additem', 'inventorystock', 'library', 'studentbookissuereport', 'bookduereport', 'bookinventory', 'human_resource', 'staff_report', 'lesson_plan', 'teachersyllabusstatus', 'onlineexamrank', 'onlineexamattend', 'onlineexams', 'attendance', 'studentinformation', 'studentreport', 'online_admission_report', 'student_teacher_ratio', 'boys_girls_ratio', 'student_profile', 'sibling_report', 'admission_report', 'class_subject', 'classsectionreport', 'guardianreport', 'admissionreport', 'logindetailreport', 'parentlogindetailreport', 'result', 'internal_result', 'external_result'),
                'attendencereports' => array('attendance', 'classattendencereport', 'attendancereport', 'daily_attendance_report', 'staffattendancereport', 'biometric_attlog', 'reportbymonthstudent', 'reportbymonth'),
                'payroll'           => array('payrollreport'),
                'onlineexam'        => array('report'),
                'examresult'        => array('rankreport', 'examinations'),
                'book'              => array('issue_returnreport'),
                'homework'          => array('homeworkreport', 'evaluation_report'),
                'route'             => array('studenttransportdetails'),
                'hostelroom'        => array('studenthosteldetails'),
                'userlog'           => array('index'),
                'audit'             => array('index'),
                'financereports'    => array('finance', 'daysheet', 'reportduefees', 'yearreportduefees', 'reportdailycollection', 'typewisebalancereport', 'reportbyname', 'studentacademicreport', 'totalstudentacademicreport', 'total_fee_collection_report', 'collection_report', 'other_collection_report', 'combined_collection_report', 'fee_collection_report_columnwise', 'onlinefees_report', 'duefeesremark', 'income', 'expense', 'payroll', 'incomegroup', 'expensegroup', 'onlineadmission', 'feegroupwise_collection'),
                'biometric_checkin_report' => array('index', 'staff_checkin', 'student_checkin'),
                'homework'          => array('homeworkordailyassignmentreport', 'homeworkreport', 'evaluation_report', 'dailyassignmentreport'),
                'documentreport'    => array('index', 'tcreport'),
            ),

            'system_settings' => array(
                'schsettings'           => array('index', 'logo', 'miscellaneous', 'backendtheme', 'mobileapp', 'studentguardianpanel', 'fees', 'idautogeneration', 'attendancetype', 'maintenance', 'biometricsetting'),
                'sessions'              => array('index', 'edit'),
                'notification'          => array('setting'),
                'smsconfig'             => array('index'),
                'emailconfig'           => array('index'),
                'paymentsettings'       => array('index'),
                'print_headerfooter'    => array('index'),
                'frontcms'              => array('index'),
                'roles'                 => array('index', 'permission'),
                'admin'                 => array('backup', 'filetype'),
                'time_range_assignment' => array('index'),
                'language'              => array('index', 'create'),
                'currency'              => array('index'),
                'users'                 => array('index'),
                'module'                => array('index'),
                'customfield'           => array('index', 'edit'),
                'captcha'               => array('index'),
                'systemfield'           => array('index'),
                'student'               => array('profilesetting'),
                'onlineadmission'       => array('admissionsetting'),
                'updater'               => array('index'),
                'sidemenu'              => array('index'),
            ),

            'gmeet_live_classes' => array(
                'gmeet'        => array('timetable', 'meeting', 'class_report', 'meeting_report', 'index'),
            ),

            'zoom_live_classes' => array(
                'conference'        => array('timetable', 'meeting', 'class_report', 'meeting_report', 'index'),
            ),

            'behaviour_records' => array(
                'studentincidents'  => array('index'),
                'incidents' => array('index'),
                'report'    => array('index', 'studentincidentreport', 'studentbehaviorsrankreport', 'classwiserankreport', 'classsectionwiserank', 'housewiserank', 'incidentwisereport'),
                'setting'   => array('index'),
            ),

            'multi_branch' => array(
                'branch'    => array('overview', 'index'),
                'finance'   => array('dailycollectionreport', 'payroll', 'incomelist', 'expenselist', 'incomereport', 'expensereport', 'userlogreport', 'index'),
            ),

            'two_factor_authentication' => array(
                'admin'        => array('setup', 'index'),
            ),

            'online_course' => array(
                'course'        => array('index', 'setting'),
                'coursecategory'  => array('categoryadd', 'categoryedit'),
                'coursereport'   => array('report', 'coursepurchase', 'coursesellreport', 'trendingreport', 'completereport', 'courseratingreport', 'guestlist', 'quizperformance'),
                'offlinepayment'   => array('payment'),
            ),

            'generate_paper' => array(
                'generatepaper'  => array('index'),
            ),

            'face_attendance' => array(
                'Face_attendance_register'  => array('index', 'mark_attendance', 'register_student', 'get_students', 'check_registration', 'delete_student', 'get_registered_students', 'save_attendance', 'get_attendance_records'),
            ),

            'other_fees' => array(
                'additionalfeeassigns' => array('index', 'search'),
                'feemasteradding'      => array('index'),
                'feegroupadding'       => array('index'),
                'feetypeadding'        => array('index'),
            ),

            'admission_no' => array(
                'admino'           => array('index', 'search', 'searching', 'addadmino', 'getadmino'),
                'adminoimport'     => array('import', 'handle_csv_upload', 'exportformat'),
            ),

            'hallticket_no' => array(
                'hallticket'       => array('index', 'search', 'addadmino', 'getadmino'),
                'hallticketimport' => array('import', 'handle_csv_upload', 'exportformat'),
            ),

            'results' => array(
                'internalresult'      => array('index', 'search'),
                'publicresult'        => array('index', 'search'),
                'addresult'           => array('index', 'search'),
                'addpublicresult'     => array('index', 'search'),
                'subjectgroup'        => array('index', 'view', 'edit', 'delete', 'create'),
                'resultsubjectgroup'  => array('index', 'view', 'edit', 'delete', 'create'),
                'examtype'            => array('index', 'view', 'edit', 'delete', 'create'),
                'publicexamtype'      => array('index', 'view', 'edit', 'delete', 'create'),
                'subjects'            => array('index', 'view', 'edit', 'delete', 'create'),
                'internalbulkimport'  => array('import'),
                'externalbulkimport'  => array('import'),
            ),

            'online_classes' => array(
                'conference' => array('index', 'meeting', 'timetable', 'class_report', 'meeting_report'),
            ),

            'gmeet' => array(
                'gmeet' => array('index', 'meeting', 'timetable', 'class_report', 'meeting_report'),
            ),

            'cbse_exam' => array(
                'exam'                 => array('index', 'examwiserank', 'examtimetable'),
                'result'               => array('marksheet'),
                'grade'                => array('gradelist'),
                'observation'          => array('index', 'assign'),
                'observationparameter' => array('index', 'edit'),
                'assessment'           => array('index'),
                'term'                 => array('index'),
                'template'             => array('index', 'templatewiserank'),
                'report'               => array('index', 'templatewise', 'examsubject'),
                'setting'              => array('index'),
            ),

            'behaviour_records' => array(
                'studentincidents' => array('index'),
                'incidents'        => array('index'),
                'report'           => array('index', 'studentincidentreport', 'studentbehaviorsrankreport', 'classwiserankreport', 'classsectionwiserank', 'housewiserank', 'incidentwisereport'),
                'setting'          => array('index'),
            ),

            'importing' => array(
                'testt' => array('import', 'importfee'),
            ),

            'fee_dis_appr' => array(
                'feesdis' => array('index', 'search'),
            ),

            'referral_branch' => array(
                'referral' => array('index'),
            ),

            'admission_no_add' => array(
                'add_admisno'       => array('index', 'search'),
                'admibulkimport'    => array('import'),
                'search_admission'  => array('search'),
            ),

            'hallticket_no_add' => array(
                'add_hallticno'         => array('index', 'search'),
                'hallticketbulkimport'  => array('import'),
            ),

            'results_branch' => array(
                'results' => array('index', 'search', 'view', 'edit', 'delete', 'create', 'import'),
            ),

            'tc_generation' => array(
                'tcgeneration' => array('index', 'search', 'createtc'),
            ),

            'hallticketgeneration' => array(
                'halltickectgeneration' => array('index', 'search', 'generatemultiple', 'create', 'createtc', 'edit', 'delete', 'view', 'subcreate', 'subedit', 'subdel', 'subgrpcreate', 'subgrpedit', 'subgrpdel', 'subgroupcombo', 'subgroupcomboedit', 'subgroupcombodel', 'deletecombogrp', 'deletecomboitem'),
            ),
        );
        if (array_key_exists($find_array, $array)) {
            return $array[$find_array];
        }
        return false;
    }
}

if (!function_exists('activate_main_menu')) {

    function activate_main_menu($menu, $class_active = "active")
    {
        $CI     = get_instance();
        $class  = strtolower($CI->router->fetch_class());
        $method = $CI->router->fetch_method();
        $current_uri = strtolower(trim($CI->uri->uri_string(), '/'));

        // Define URI prefix requirements for modules with controllers in subdirectories
        // These modules ONLY activate when URI starts with their prefix
        $module_uri_prefixes = array(
            'results'          => 'admin/results/',
            'results_branch'   => 'admin/results/',
            'admission_no'     => 'admin/hallticket/admino',
            'admission_no_add' => 'admin/hallticket/admino',
            'hallticket_no'    => 'admin/hallticket/hallticket',
            'hallticket_no_add'=> 'admin/hallticket/hallticket',
            'hallticketgeneration' => 'admin/halltickectgeneration/',
            'tc_generation'    => 'admin/tcgeneration/',
            'cbse_exam'        => 'cbseexam/',
            'behaviour_records'=> 'behaviour/',
            'fee_dis_appr'     => 'admin/feesdiscountapproval/',
            'referral_branch'  => 'admin/student_referral/',
        );

        // Define URI exclusion patterns for modules with conflicting controller names
        // These modules will NOT activate when URI contains any of their exclusion patterns
        // This is needed when different modules have controllers with the same name in different directories
        $module_uri_exclusions = array(
            'academics' => array('admin/results/', 'admin/halltickectgeneration/', 'admin/tcgeneration/', 'admin/feesdiscountapproval/', 'admin/student_referral/'),
        );

        // Check if this module has a URI prefix requirement
        if (isset($module_uri_prefixes[$menu])) {
            $required_prefix = $module_uri_prefixes[$menu];
            // If URI doesn't start with the required prefix, this menu should NOT be active
            if (strpos($current_uri, $required_prefix) !== 0) {
                return "";
            }
        }

        // Check if this module has URI exclusion patterns
        if (isset($module_uri_exclusions[$menu])) {
            foreach ($module_uri_exclusions[$menu] as $exclusion_pattern) {
                // If URI contains the exclusion pattern, this menu should NOT be active
                if (strpos($current_uri, $exclusion_pattern) !== false) {
                    return "";
                }
            }
        }

        // For modules with URI prefix check passed (or no prefix required),
        // also verify against the main_menu_array for controller/method matching
        $return_array = main_menu_array($menu);
        if ($return_array) {
            if (array_key_exists($class, $return_array)) {
                $a = $return_array[$class];

                if (!empty($a)) {
                    foreach ($a as $method_key => $method_value) {
                        if ($method_value == $method) {
                            return $class_active;
                            break;
                        }
                    }
                }
            }
        }
        
        return "";
    }
}

if (!function_exists("activate_submenu")) {

    function activate_submenu($arg_class = "", $arg_methods = array(), $class_active = "active", $url = "")
    {
        $CI = get_instance();

        // Current request details
        $class  = strtolower($CI->router->fetch_class());
        $method = $CI->router->fetch_method();
        $current_uri = trim($CI->uri->uri_string(), '/');
        $clean_url = trim((string)$url, '/');

        // 1. If a URL is provided, use URL-based matching ONLY
        // This is critical for controllers with same name in different directories
        if ($clean_url !== "") {
            // Exact match - highest priority
            if ($current_uri === $clean_url) {
                return $class_active;
            }
            
            // Check if menu URL ends with /index - if so, also match base URL
            if (preg_match('/\/index$/', $clean_url)) {
                $clean_url_without_index = preg_replace('/\/index$/', '', $clean_url);
                if ($current_uri === $clean_url_without_index) {
                    return $class_active;
                }
            }
            
            // Check if current URI is the menu URL with /index appended
            if ($current_uri === $clean_url . '/index') {
                return $class_active;
            }
            
            // For URLs that specify a specific method (contain at least 3 segments like admin/controller/method),
            // also allow sub-paths (e.g., admin/controller/method/param matches admin/controller/method)
            $url_segments = explode('/', $clean_url);
            if (count($url_segments) >= 3) {
                // This URL specifies a method, allow prefix matching for sub-parameters
                if (strpos($current_uri, $clean_url . '/') === 0) {
                    return $class_active;
                }
            }
            // Special mapping for Accounting Reports
            if ($clean_url === 'admin/accountreport') {
                 $acc_report_pages = array(
                    'admin/accountreport/search',
                    'admin/accountreport/index'
                 );
                 
                 if (in_array($current_uri, $acc_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for Accounting Transaction Reports
            if ($clean_url === 'admin/accounttranscationreport') {
                 $acc_trans_pages = array(
                    'admin/accounttranscationreport/search',
                    'admin/accounttranscationreport/index'
                 );
                 
                 if ($current_uri === 'admin/accounttranscationreport' || $current_uri === 'admin/accounttranscationreport/index' || $current_uri === 'admin/accounttranscationreport/search') {
                     return $class_active;
                 }
            }

            // Special mapping for Additional Fee Assigns
            if ($clean_url === 'admin/additionalfeeassigns') {
                 if ($current_uri === 'admin/additionalfeeassigns/search') {
                     return $class_active;
                 }
            }

            // Special mapping for Student Information Reports
            // Maps various report pages to the "Student Information" sidebar item (report/studentinformation)
            if ($clean_url === 'report/studentinformation') {
                 $related_report_pages = array(
                    'report/studentreport', 
                    'report/classsectionreport',
                    'report/guardianreport',
                    'report/admissionreport',
                    'report/logindetailreport',
                    'report/parentlogindetailreport',
                    'report/class_subject',
                    'report/admission_report',
                    'report/sibling_report',
                    'report/student_profile',
                    'report/boys_girls_ratio',
                    'report/student_teacher_ratio',
                    'report/online_admission_report'
                 );
                 
                 if (in_array($current_uri, $related_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for Finance Reports
            // Maps various finance report pages to the "Finance" sidebar item (financereports/finance)
            if ($clean_url === 'financereports/finance') {
                 $finance_report_pages = array(
                    'financereports/reportduefees',
                    'financereports/yearreportduefees',
                    'financereports/reportdailycollection',
                    'financereports/typewisebalancereport',
                    'financereports/reportbyname',
                    'financereports/studentacademicreport',
                    'financereports/totalstudentacademicreport',
                    'financereports/total_fee_collection_report',
                    'financereports/collection_report',
                    'financereports/other_collection_report',
                    'financereports/combined_collection_report',
                    'financereports/fee_collection_report_columnwise',
                    'financereports/onlinefees_report',
                    'financereports/duefeesremark',
                    'financereports/income',
                    'financereports/expense',
                    'financereports/payroll',
                    'financereports/incomegroup',
                    'financereports/expensegroup',
                    'financereports/onlineadmission',
                    'financereports/daysheet',
                    'financereports/feegroupwise_collection'
                 );
                 
                 if (in_array($current_uri, $finance_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for Attendance Reports
            if ($clean_url === 'attendencereports/attendance') {
                 $attendance_report_pages = array(
                    'attendencereports/classattendencereport',
                    'attendencereports/attendancereport',
                    'attendencereports/daily_attendance_report',
                    'attendencereports/staffattendancereport',
                    'attendencereports/biometric_attlog'
                 );
                 
                 if (in_array($current_uri, $attendance_report_pages) || strpos($current_uri, 'biometric_checkin_report') === 0) {
                     return $class_active;
                 }
            }

            // Special mapping for Examinations Report
            if ($clean_url === 'admin/examresult/examinations') {
                 if ($current_uri === 'admin/examresult/rankreport') {
                     return $class_active;
                 }
            }



            // Special mapping for Online Examinations
            if ($clean_url === 'admin/onlineexam/report') {
                 $online_exam_pages = array(
                    'report/onlineexams',
                    'report/onlineexamattend',
                    'report/onlineexamrank'
                 );
                 if (in_array($current_uri, $online_exam_pages)) {
                     return $class_active;
                 }
            }



            // Special mapping for Lesson Plan Report
            if ($clean_url === 'report/lesson_plan') {
                 if ($current_uri === 'report/teachersyllabusstatus') {
                     return $class_active;
                 }
            }

            // Special mapping for Human Resource Report
            if ($clean_url === 'report/human_resource') {
                 $hr_report_pages = array(
                    'report/staff_report',
                    'admin/payroll/payrollreport'
                 );
                 if (in_array($current_uri, $hr_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for Homework Report
            if ($clean_url === 'homework/homeworkordailyassignmentreport') {
                 $homework_report_pages = array(
                    'homework/homeworkreport',
                    'homework/evaluation_report',
                    'homework/dailyassignmentreport'
                 );
                 if (in_array($current_uri, $homework_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for Library Report
            if ($clean_url === 'report/library') {
                 $library_report_pages = array(
                    'report/studentbookissuereport',
                    'report/bookduereport',
                    'report/bookinventory',
                    'admin/book/issue_returnreport'
                 );
                 if (in_array($current_uri, $library_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for Inventory Report
            if ($clean_url === 'report/inventory') {
                 $inventory_report_pages = array(
                    'report/inventorystock',
                    'report/additem',
                    'report/issueinventory'
                 );
                 if (in_array($current_uri, $inventory_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for Result Report
            if ($clean_url === 'report/result') {
                 $result_report_pages = array(
                    'report/internal_result',
                    'report/external_result'
                 );
                 if (in_array($current_uri, $result_report_pages)) {
                     return $class_active;
                 }
            }

            // Special mapping for System Settings (General Settings)
            if ($clean_url === 'schsettings/index' || $clean_url === 'schsettings') {
                 $sch_setting_pages = array(
                    'schsettings/logo',
                    'schsettings/login_page_background',
                    'schsettings/studentguardianpanel',
                    'schsettings/fees',
                    'schsettings/idautogeneration',
                    'schsettings/attendancetype',
                    'schsettings/biometricsetting',
                    'schsettings/maintenance',
                    'schsettings/miscellaneous',
                    'schsettings/backendtheme',
                    'schsettings/mobileapp'
                 );
                 if (in_array($current_uri, $sch_setting_pages)) {
                     return $class_active;
                 }
            }

            // For 2+ segment URLs that didn't match above, try matching as
            // prefix so that URLs like "student/search" work when the actual
            // URI is also "student/search"
            if (strpos($current_uri, $clean_url) === 0) {
                $after = substr($current_uri, strlen($clean_url));
                if ($after === '' || $after === '/index' || $after === '/index/') {
                    return $class_active;
                }
            }

            // URL-based matching didn't find a hit — fall through to
            // class/method fallback below instead of returning empty.
        }


        // 3. Direct Class and Method match (only when no URL provided)
        if (strtolower($class) === strtolower($arg_class) && is_array($arg_methods)) {
            if (in_array($method, $arg_methods)) {
                return $class_active;
            }
        }


        // 5. Front Office Setup Group Fix
        $front_setup_controllers = array('visitorspurpose', 'complainttype', 'source', 'reference');
        if (in_array($arg_class, $front_setup_controllers) && in_array($class, $front_setup_controllers)) {
            return $class_active;
        }

        return "";
    }
}

function side_menu_list($list = -1)
{

    $CI = &get_instance();
    $CI->load->model('sidebarmenu_model');
    $result = $CI->sidebarmenu_model->getMenuwithSubmenus($list);
    return $result;
}

function access_permission_sidebar_remove_pipe($access_permissions)
{
    if (empty($access_permissions)) {
        return array();
    }
    // remove pipe sign ||
    $module_permission = array_map('trim', explode('||', preg_replace('/\(\'|\'|\)/', '', $access_permissions)));

    return $module_permission;
}

function access_permission_remove_comma($m_permission_value)
{
    // remove pipe sign ||
    $module_permission_seprated = array_map('trim', explode(',', preg_replace('/\s+/', '', $m_permission_value)));
    return $module_permission_seprated;
}
