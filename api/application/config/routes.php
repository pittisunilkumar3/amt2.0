<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	https://codeigniter.com/user_guide/general/routing.html
  |
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There are three reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router which controller/method to use if those
  | provided in the URL cannot be matched to a valid route.
  |
  |	$route['translate_uri_dashes'] = FALSE;
  |
  | This is not exactly a route, but allows you to automatically route
  | controller and method names that contain dashes. '-' isn't a valid
  | class or method name character, so it requires translation.
  | When you set this option to TRUE, it will replace ALL dashes in the
  | controller and method URI segments.
  |
  | Examples:	my-controller/index	-> my_controller/index
  |		my-controller/my-method	-> my_controller/my_method
 */
$route['default_controller'] = 'welcome';
$route['404_override'] = 'teacher_webservice/not_found';
$route['translate_uri_dashes'] = FALSE;

// Test Routes
$route['test-db'] = 'test_db/index';
$route['test-db/staff'] = 'test_db/test_staff';
$route['test-db/settings'] = 'test_db/test_settings';
$route['test-db/auth-tables'] = 'test_db/test_auth_tables';

// Debug Auth Routes
$route['debug-auth/check-settings'] = 'debug_auth/check_settings';
$route['debug-auth/fix-settings'] = 'debug_auth/fix_settings';
$route['debug-auth/test-auth'] = 'debug_auth/test_auth';
$route['debug-auth/enable-login'] = 'debug_auth/enable_login';

// Teacher Authentication Routes
$route['teacher/test'] = 'teacher_auth/test';
$route['teacher/check-credentials'] = 'teacher_auth/check_credentials';
$route['teacher/debug-login'] = 'teacher_auth/debug_login';
$route['teacher/simple-login'] = 'teacher_auth/simple_login';
$route['teacher/login'] = 'teacher_auth/login';
$route['teacher/logout'] = 'teacher_auth/logout';
$route['teacher/profile/(:num)'] = 'teacher_auth/profile/$1';
$route['teacher/profile'] = 'teacher_auth/profile';
$route['teacher/profile/update'] = 'teacher_auth/update_profile';
$route['teacher/change-password'] = 'teacher_auth/change_password';
$route['teacher/dashboard'] = 'teacher_auth/dashboard';
$route['teacher/refresh-token'] = 'teacher_auth/refresh_token';
$route['teacher/validate-token'] = 'teacher_auth/validate_token';
$route['teacher/qr-code/(:num)'] = 'teacher_auth/generate_qr_code/$1';
$route['teacher/download-document/(:num)/(:any)'] = 'teacher_auth/download_document/$1/$2';

// Teacher Webservice Routes
$route['teacher/menu']['POST'] = 'teacher_webservice/menu';
$route['teacher/simple_menu']['POST'] = 'teacher_webservice/simple_menu';
$route['teacher/students']['POST'] = 'teacher_webservice/students';
$route['teacher/classes-with-sections']['POST'] = 'teacher_webservice/classes_with_sections';
$route['teacher/sessions-with-classes-sections']['POST'] = 'teacher_webservice/sessions_with_classes_sections';
$route['teacher/student-categories']['POST'] = 'teacher_webservice/student_categories';
$route['teacher/student-category/get']['POST'] = 'teacher_webservice/student_category_get';
$route['teacher/student-category/create']['POST'] = 'teacher_webservice/student_category_create';
$route['teacher/student-category/update']['POST'] = 'teacher_webservice/student_category_update';
$route['teacher/student-category/delete']['POST'] = 'teacher_webservice/student_category_delete';
$route['teacher/test'] = 'teacher_webservice/test';
$route['teacher/debug-menu'] = 'teacher_webservice/debug_menu';
$route['teacher/permissions'] = 'teacher_webservice/permissions';
$route['teacher/modules'] = 'teacher_webservice/modules';
$route['teacher/check-permission'] = 'teacher_webservice/check_permission';
$route['teacher/role'] = 'teacher_webservice/role';
$route['teacher/settings'] = 'teacher_webservice/settings';
$route['teacher/sidebar-menu'] = 'teacher_webservice/sidebar_menu';
$route['teacher/breadcrumb'] = 'teacher_webservice/breadcrumb';
$route['teacher/permission-groups'] = 'teacher_webservice/permission_groups';
$route['teacher/group-permissions'] = 'teacher_webservice/group_permissions';
$route['teacher/bulk-permission-check'] = 'teacher_webservice/bulk_permission_check';
$route['teacher/module-status'] = 'teacher_webservice/module_status';
$route['teacher/features'] = 'teacher_webservice/features';
$route['teacher/dashboard-summary'] = 'teacher_webservice/dashboard_summary';
$route['teacher/attendance-summary'] = 'teacher_webservice/attendance_summary';
$route['teacher/staff-attendance'] = 'teacher_webservice/staff_attendance';
$route['attendance/summary'] = 'attendance_api/summary';

// Fee Group API Routes
$route['fee-groups/list']['POST'] = 'fee_group_api/list';
$route['fee-groups/get']['POST'] = 'fee_group_api/get';
$route['fee-groups/create']['POST'] = 'fee_group_api/create';
$route['fee-groups/update']['POST'] = 'fee_group_api/update';
$route['fee-groups/delete']['POST'] = 'fee_group_api/delete';

// Fee Type API Routes
$route['fee-types/list']['POST'] = 'fee_type_api/list';
$route['fee-types/get']['POST'] = 'fee_type_api/get';
$route['fee-types/create']['POST'] = 'fee_type_api/create';
$route['fee-types/update']['POST'] = 'fee_type_api/update';
$route['fee-types/delete']['POST'] = 'fee_type_api/delete';

// Fee Master API Routes
$route['fee-masters/list']['POST'] = 'fee_master_api/list';
$route['fee-masters/get']['POST'] = 'fee_master_api/get';
$route['fee-masters/create']['POST'] = 'fee_master_api/create';
$route['fee-masters/update']['POST'] = 'fee_master_api/update';
$route['fee-masters/delete']['POST'] = 'fee_master_api/delete';

// Student Fee Search API Routes
$route['student-fee-search/by-class']['POST'] = 'student_fee_search_api/by_class';
$route['student-fee-search/by-keyword']['POST'] = 'student_fee_search_api/by_keyword';
$route['student-fee-search/by-category']['POST'] = 'student_fee_search_api/by_category';
$route['student-fee-search/classes']['POST'] = 'student_fee_search_api/classes';
$route['student-fee-search/sections']['POST'] = 'student_fee_search_api/sections';
$route['student-fee-search/fee-categories']['POST'] = 'student_fee_search_api/fee_categories';
$route['student-fee-search/student-fees']['POST'] = 'student_fee_search_api/student_fees';

// Student Fee Payment Search API Routes
$route['student-fee-payment-search/by-payment-id']['POST'] = 'student_fee_payment_search_api/by_payment_id';
$route['student-fee-payment-search/by-invoice-id']['POST'] = 'student_fee_payment_search_api/by_invoice_id';
$route['student-fee-payment-search/transport-fee']['POST'] = 'student_fee_payment_search_api/transport_fee';
$route['student-fee-payment-search/receipt']['POST'] = 'student_fee_payment_search_api/receipt';
$route['student-fee-payment-search/validate-payment-id']['POST'] = 'student_fee_payment_search_api/validate_payment_id';

// Online Admission API Routes
$route['online-admission/list']['POST'] = 'online_admission_api/list';
$route['online-admission/get/(:num)']['POST'] = 'online_admission_api/get/$1';
$route['online-admission/filter']['POST'] = 'online_admission_api/filter';

// Disable Reason API Routes
$route['disable-reason/list']['POST'] = 'disable_reason_api/list';
$route['disable-reason/get/(:num)']['POST'] = 'disable_reason_api/get/$1';
$route['disable-reason/create']['POST'] = 'disable_reason_api/create';
$route['disable-reason/update/(:num)']['POST'] = 'disable_reason_api/update/$1';
$route['disable-reason/delete/(:num)']['POST'] = 'disable_reason_api/delete/$1';

// Bulk Delete API Routes
$route['bulk-delete/students']['POST'] = 'bulk_delete_api/students';
$route['bulk-delete/validate']['POST'] = 'bulk_delete_api/validate';

// Student House API Routes
$route['student-house/list']['POST'] = 'student_house_api/list';
$route['student-house/get/(.+)']['POST'] = 'student_house_api/get/$1';
$route['student-house/create']['POST'] = 'student_house_api/create';
$route['student-house/update/(.+)']['POST'] = 'student_house_api/update/$1';
$route['student-house/delete/(.+)']['POST'] = 'student_house_api/delete/$1';

// Classes API Routes
$route['classes/list']['POST'] = 'classes_api/list';
$route['classes/get/(:num)']['POST'] = 'classes_api/get/$1';
$route['classes/create']['POST'] = 'classes_api/create';
$route['classes/update/(:num)']['POST'] = 'classes_api/update/$1';
$route['classes/delete/(:num)']['POST'] = 'classes_api/delete/$1';

// Sections API Routes
$route['sections/list']['POST'] = 'sections_api/list';
$route['sections/get/(:num)']['POST'] = 'sections_api/get/$1';
$route['sections/create']['POST'] = 'sections_api/create';
$route['sections/update/(:num)']['POST'] = 'sections_api/update/$1';
$route['sections/delete/(:num)']['POST'] = 'sections_api/delete/$1';

// Department API Routes
$route['department/list']['POST'] = 'department_api/list';
$route['department/get/(:num)']['POST'] = 'department_api/get/$1';
$route['department/create']['POST'] = 'department_api/create';
$route['department/update/(:num)']['POST'] = 'department_api/update/$1';
$route['department/delete/(:num)']['POST'] = 'department_api/delete/$1';

// Designation API Routes
$route['designation/list']['POST'] = 'designation_api/list';
$route['designation/get/(:num)']['POST'] = 'designation_api/get/$1';
$route['designation/create']['POST'] = 'designation_api/create';
$route['designation/update/(:num)']['POST'] = 'designation_api/update/$1';
$route['designation/delete/(:num)']['POST'] = 'designation_api/delete/$1';

// Income Search API Routes
$route['income-search/search']['POST'] = 'income_search_api/search';
$route['income-search/income-heads']['POST'] = 'income_search_api/income_heads';

// Income API Routes
$route['income/list']['POST'] = 'income_api/list';
$route['income/get/(:num)']['POST'] = 'income_api/get/$1';
$route['income/create']['POST'] = 'income_api/create';
$route['income/update/(:num)']['POST'] = 'income_api/update/$1';
$route['income/delete/(:num)']['POST'] = 'income_api/delete/$1';

// Income Head API Routes
$route['income-head/list']['POST'] = 'income_head_api/list';
$route['income-head/get/(:num)']['POST'] = 'income_head_api/get/$1';
$route['income-head/create']['POST'] = 'income_head_api/create';
$route['income-head/update/(:num)']['POST'] = 'income_head_api/update/$1';
$route['income-head/delete/(:num)']['POST'] = 'income_head_api/delete/$1';

// Student Report API Routes
$route['student-report/filter']['POST'] = 'student_report_api/filter';
$route['student-report/list']['POST'] = 'student_report_api/list';

// Guardian Report API Routes
$route['guardian-report/filter']['POST'] = 'guardian_report_api/filter';
$route['guardian-report/list']['POST'] = 'guardian_report_api/list';

// Admission Report API Routes
$route['admission-report/filter']['POST'] = 'admission_report_api/filter';
$route['admission-report/list']['POST'] = 'admission_report_api/list';

// Login Detail Report API Routes
$route['login-detail-report/filter']['POST'] = 'login_detail_report_api/filter';
$route['login-detail-report/list']['POST'] = 'login_detail_report_api/list';

// Parent Login Detail Report API Routes
$route['parent-login-detail-report/filter']['POST'] = 'parent_login_detail_report_api/filter';
$route['parent-login-detail-report/list']['POST'] = 'parent_login_detail_report_api/list';

// Class Subject Report API Routes
$route['class-subject-report/filter']['POST'] = 'class_subject_report_api/filter';
$route['class-subject-report/list']['POST'] = 'class_subject_report_api/list';

// Student Profile Report API Routes
$route['student-profile-report/filter']['POST'] = 'student_profile_report_api/filter';
$route['student-profile-report/list']['POST'] = 'student_profile_report_api/list';

// Boys Girls Ratio Report API Routes
$route['boys-girls-ratio-report/filter']['POST'] = 'boys_girls_ratio_report_api/filter';
$route['boys-girls-ratio-report/list']['POST'] = 'boys_girls_ratio_report_api/list';

// Online Admission Report API Routes
$route['online-admission-report/filter']['POST'] = 'online_admission_report_api/filter';
$route['online-admission-report/list']['POST'] = 'online_admission_report_api/list';

// Student Teacher Ratio Report API Routes
$route['student-teacher-ratio-report/filter']['POST'] = 'student_teacher_ratio_report_api/filter';
$route['student-teacher-ratio-report/list']['POST'] = 'student_teacher_ratio_report_api/list';

// Daily Attendance Report API Routes
$route['daily-attendance-report/filter']['POST'] = 'daily_attendance_report_api/filter';
$route['daily-attendance-report/list']['POST'] = 'daily_attendance_report_api/list';

// Biometric Attlog Report API Routes
$route['biometric-attlog-report/filter']['POST'] = 'biometric_attlog_report_api/filter';
$route['biometric-attlog-report/list']['POST'] = 'biometric_attlog_report_api/list';

// Attendance Report API Routes
$route['attendance-report/filter']['POST'] = 'attendance_report_api/filter';
$route['attendance-report/list']['POST'] = 'attendance_report_api/list';

// Staff Attendance Report API Routes
$route['staff-attendance-report/filter']['POST'] = 'staff_attendance_report_api/filter';
$route['staff-attendance-report/list']['POST'] = 'staff_attendance_report_api/list';

// Class Attendance Report API Routes
$route['class-attendance-report/filter']['POST'] = 'class_attendance_report_api/filter';
$route['class-attendance-report/list']['POST'] = 'class_attendance_report_api/list';

// Rank Report API Routes
$route['rank-report/filter']['POST'] = 'rank_report_api/filter';
$route['rank-report/list']['POST'] = 'rank_report_api/list';

// Online Exam Rank Report API Routes
$route['online-exam-rank-report/filter']['POST'] = 'online_exam_rank_report_api/filter';
$route['online-exam-rank-report/list']['POST'] = 'online_exam_rank_report_api/list';

// Online Exam Attend Report API Routes
$route['online-exam-attend-report/filter']['POST'] = 'online_exam_attend_report_api/filter';
$route['online-exam-attend-report/list']['POST'] = 'online_exam_attend_report_api/list';

// Online Exams Report API Routes
$route['online-exams-report/filter']['POST'] = 'online_exams_report_api/filter';
$route['online-exams-report/list']['POST'] = 'online_exams_report_api/list';

// Online Exam Report API Routes
$route['online-exam-report/filter']['POST'] = 'online_exam_report_api/filter';
$route['online-exam-report/list']['POST'] = 'online_exam_report_api/list';

// Lesson Plan Report API Routes
$route['lesson-plan-report/filter']['POST'] = 'lesson_plan_report_api/filter';
$route['lesson-plan-report/list']['POST'] = 'lesson_plan_report_api/list';

// Teacher Syllabus Status Report API Routes
$route['teacher-syllabus-status-report/filter']['POST'] = 'teacher_syllabus_status_report_api/filter';
$route['teacher-syllabus-status-report/list']['POST'] = 'teacher_syllabus_status_report_api/list';

// Payroll Report API Routes
$route['payroll-report/filter']['POST'] = 'payroll_report_api/filter';
$route['payroll-report/list']['POST'] = 'payroll_report_api/list';

// Staff Report API Routes
$route['staff-report/filter']['POST'] = 'staff_report_api/filter';
$route['staff-report/list']['POST'] = 'staff_report_api/list';

// Daily Assignment Report API Routes
$route['daily-assignment-report/filter']['POST'] = 'daily_assignment_report_api/filter';
$route['daily-assignment-report/list']['POST'] = 'daily_assignment_report_api/list';

// Evaluation Report API Routes
$route['evaluation-report/filter']['POST'] = 'evaluation_report_api/filter';
$route['evaluation-report/list']['POST'] = 'evaluation_report_api/list';

// Homework Report API Routes
$route['homework-report/filter']['POST'] = 'homework_report_api/filter';
$route['homework-report/list']['POST'] = 'homework_report_api/list';

// Issue Return Report API Routes
$route['issue-return-report/filter']['POST'] = 'issue_return_report_api/filter';
$route['issue-return-report/list']['POST'] = 'issue_return_report_api/list';

// Student Book Issue Report API Routes
$route['student-book-issue-report/filter']['POST'] = 'student_book_issue_report_api/filter';
$route['student-book-issue-report/list']['POST'] = 'student_book_issue_report_api/list';

// Book Due Report API Routes
$route['book-due-report/filter']['POST'] = 'book_due_report_api/filter';
$route['book-due-report/list']['POST'] = 'book_due_report_api/list';

// Book Inventory Report API Routes
$route['book-inventory-report/filter']['POST'] = 'book_inventory_report_api/filter';
$route['book-inventory-report/list']['POST'] = 'book_inventory_report_api/list';

// Due Fees Report API Routes
$route['due-fees-report/filter']['POST'] = 'due_fees_report_api/filter';
$route['due-fees-report/list']['POST'] = 'due_fees_report_api/list';

// Daily Collection Report API Routes
$route['daily-collection-report/filter']['POST'] = 'daily_collection_report_api/filter';
$route['daily-collection-report/list']['POST'] = 'daily_collection_report_api/list';

// Year Report Due Fees API Routes
$route['year-report-due-fees/filter']['POST'] = 'year_report_due_fees_api/filter';
$route['year-report-due-fees/list']['POST'] = 'year_report_due_fees_api/list';

// Type Wise Balance Report API Routes
$route['type-wise-balance-report/filter']['POST'] = 'type_wise_balance_report_api/filter';
$route['type-wise-balance-report/list']['POST'] = 'type_wise_balance_report_api/list';

// Collection Report API Routes
$route['collection-report/filter']['POST'] = 'collection_report_api/filter';
$route['collection-report/list']['POST'] = 'collection_report_api/list';

// Total Student Academic Report API Routes
$route['total-student-academic-report/filter']['POST'] = 'total_student_academic_report_api/filter';
$route['total-student-academic-report/list']['POST'] = 'total_student_academic_report_api/list';

// Student Academic Report API Routes
$route['student-academic-report/filter']['POST'] = 'student_academic_report_api/filter';
$route['student-academic-report/list']['POST'] = 'student_academic_report_api/list';

// Report By Name API Routes
$route['report-by-name/filter']['POST'] = 'report_by_name_api/filter';
$route['report-by-name/list']['POST'] = 'report_by_name_api/list';
