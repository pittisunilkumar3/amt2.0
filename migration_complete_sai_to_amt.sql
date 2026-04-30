-- ============================================================
-- COMPLETE MIGRATION: Make saimodelhighschool identical to amt
-- Generated: 2026-04-02
-- Updated: 2026-04-02 (Permission system fixes - dedicated groups, no duplicates, clean formats)
-- Target: saimodelhighschool database
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- ============================================================
-- PART 1: SCHEMA CHANGES (Tables & Columns)
-- ============================================================

-- 1A: Create tables that only exist in amt
-- Table: chat_messages_gp
CREATE TABLE `chat_messages_gp` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `sender` enum('user','ai') NOT NULL DEFAULT 'user',
  `message_type` varchar(50) DEFAULT 'text',
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: face_attendance_logs
CREATE TABLE `face_attendance_logs` (
  `id` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `recognition_time` datetime NOT NULL,
  `detected_faces` int(11) DEFAULT 0,
  `recognized_faces` int(11) DEFAULT 0,
  `unknown_faces` int(11) DEFAULT 0,
  `recognition_details` text DEFAULT NULL COMMENT 'JSON details of all recognitions',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: face_attendance_records
CREATE TABLE `face_attendance_records` (
  `id` int(11) NOT NULL,
  `face_student_id` int(11) NOT NULL COMMENT 'Reference to face_attendance_students',
  `registration_number` varchar(100) NOT NULL,
  `attendance_date` date NOT NULL,
  `attendance_time` time NOT NULL,
  `attendance_status` enum('Present','Absent','Late') NOT NULL DEFAULT 'Present',
  `confidence_score` decimal(5,2) DEFAULT NULL COMMENT 'Face recognition match confidence',
  `captured_image` varchar(255) DEFAULT NULL COMMENT 'Path to captured attendance image',
  `session_id` int(11) DEFAULT NULL COMMENT 'Link to school session',
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `marked_by` int(11) DEFAULT NULL COMMENT 'User who initiated attendance',
  `recognition_method` enum('Auto','Manual','Verified') DEFAULT 'Auto',
  `notes` text DEFAULT NULL,
  `person_type` enum('student','staff') DEFAULT 'student' COMMENT 'Whether record is for student or staff',
  `staff_att_sync` tinyint(1) DEFAULT 0 COMMENT '1 if synced to staff_attendance table',
  `staff_att_type_id` int(11) DEFAULT NULL COMMENT 'staff_attendance_type_id recorded in staff_attendance (1=Present,2=Late)',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: face_attendance_students
CREATE TABLE `face_attendance_students` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL COMMENT 'Reference to existing student table if needed',
  `staff_id` int(11) DEFAULT NULL COMMENT 'Reference to staff table',
  `registration_number` varchar(100) NOT NULL,
  `admission_no` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL COMMENT 'Staff designation',
  `face_images` text DEFAULT NULL COMMENT 'JSON array of image file names',
  `face_descriptors` longtext DEFAULT NULL COMMENT 'JSON array of face descriptors for recognition',
  `is_active` tinyint(1) DEFAULT 1,
  `registered_by` int(11) DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `person_type` enum('student','staff') DEFAULT 'student' COMMENT 'Whether this record is for a student or staff member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: papers
CREATE TABLE `papers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `instructions` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- Table: payroll_settings
CREATE TABLE `payroll_settings` (
  `id` int(11) NOT NULL,
  `auto_payroll_enabled` enum('yes','no') NOT NULL DEFAULT 'no' COMMENT 'Enable/disable auto payroll generation',
  `auto_payroll_day` int(11) NOT NULL DEFAULT 1 COMMENT 'Day of month (1-28) to auto-generate PREVIOUS month payroll',
  `payroll_cutoff_day` int(11) NOT NULL DEFAULT 0 COMMENT 'Payroll cutoff day of month (1-28). Attendance after this day counts toward NEXT month. 0 = no cutoff (entire month).',
  `required_hours_per_day` decimal(5,2) NOT NULL DEFAULT 8.00,
  `late_grace_minutes` int(11) NOT NULL DEFAULT 15,
  `shift_start_time` time NOT NULL DEFAULT '09:00:00',
  `working_days_method` enum('exclude_sundays','exclude_sundays_saturdays','all_days') NOT NULL DEFAULT 'exclude_sundays' COMMENT 'How to calculate working days in a month',
  `absent_deduction_type` enum('per_day','fixed') NOT NULL DEFAULT 'per_day' COMMENT 'per_day = multiply per_day_salary, fixed = flat amount',
  `absent_deduction_value` decimal(10,2) NOT NULL DEFAULT 1.00 COMMENT 'If per_day: multiplier (1=full day), If fixed: flat amount in currency',
  `late_deduction_type` enum('per_day','fixed') NOT NULL DEFAULT 'per_day',
  `late_deduction_value` decimal(10,2) NOT NULL DEFAULT 0.50 COMMENT 'Default: half day salary deducted per late',
  `half_day_deduction_type` enum('per_day','fixed') NOT NULL DEFAULT 'per_day',
  `half_day_deduction_value` decimal(10,2) NOT NULL DEFAULT 0.50 COMMENT 'Default: half day salary deducted',
  `short_hour_deduction_type` enum('per_hour','fixed','disabled') NOT NULL DEFAULT 'disabled',
  `short_hour_deduction_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `short_hour_threshold` decimal(5,2) NOT NULL DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: resume_additional_fields_settings
CREATE TABLE `resume_additional_fields_settings` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: resume_settings_fields
CREATE TABLE `resume_settings_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(250) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: student_educational_details
CREATE TABLE `student_educational_details` (
  `id` int(11) NOT NULL,
  `course` varchar(255) NOT NULL,
  `university` varchar(255) NOT NULL,
  `education_year` varchar(255) NOT NULL,
  `education_detail` varchar(255) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: student_refrence
CREATE TABLE `student_refrence` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(255) NOT NULL,
  `age` varchar(255) NOT NULL,
  `profession` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `student_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: student_skills_detail
CREATE TABLE `student_skills_detail` (
  `id` int(11) NOT NULL,
  `skill_category` varchar(255) NOT NULL,
  `skill_detail` varchar(255) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: student_work_experience
CREATE TABLE `student_work_experience` (
  `id` int(11) NOT NULL,
  `institute` text NOT NULL,
  `designation` text NOT NULL,
  `year` varchar(255) NOT NULL,
  `location` text NOT NULL,
  `detail` text NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: whatsapp_config
CREATE TABLE `whatsapp_config` (
  `id` int(11) NOT NULL,
  `provider` enum('meta','twilio') NOT NULL DEFAULT 'meta',
  `phone_number_id` varchar(100) NOT NULL DEFAULT '',
  `business_account_id` varchar(100) NOT NULL DEFAULT '',
  `access_token` varchar(500) NOT NULL DEFAULT '',
  `verify_token` varchar(255) NOT NULL DEFAULT '',
  `api_version` varchar(20) NOT NULL DEFAULT 'v21.0',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `webhook_url` varchar(500) NOT NULL DEFAULT '',
  `twilio_account_sid` varchar(200) NOT NULL DEFAULT '',
  `twilio_auth_token` varchar(500) NOT NULL DEFAULT '',
  `twilio_phone_number` varchar(30) NOT NULL DEFAULT '',
  `twilio_is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: whatsapp_messages
CREATE TABLE `whatsapp_messages` (
  `id` int(11) NOT NULL,
  `message_type` enum('outgoing','incoming') NOT NULL DEFAULT 'outgoing',
  `event_type` varchar(100) DEFAULT NULL,
  `recipient_phone` varchar(20) NOT NULL DEFAULT '',
  `recipient_name` varchar(255) DEFAULT NULL,
  `template_name` varchar(255) DEFAULT NULL,
  `template_body` text DEFAULT NULL,
  `template_language` varchar(20) DEFAULT 'en_US',
  `message_json` longtext DEFAULT NULL,
  `whatsapp_message_id` varchar(100) DEFAULT NULL,
  `whatsapp_conversation_id` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `provider` varchar(20) DEFAULT 'meta',
  `sent_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: whatsapp_template_language
CREATE TABLE `whatsapp_template_language` (
  `id` int(11) NOT NULL,
  `language_code` varchar(20) NOT NULL DEFAULT 'en_US',
  `language_name` varchar(100) NOT NULL DEFAULT 'English (US)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1B: Add new columns to existing tables
-- Table: notification_setting
ALTER TABLE `notification_setting` ADD COLUMN `is_whatsapp` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `notification_setting` ADD COLUMN `display_whatsapp` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `notification_setting` ADD COLUMN `whatsapp_template_id` int(11) DEFAULT NULL;

-- Table: sch_settings
ALTER TABLE `sch_settings` ADD COLUMN `admin_panel_whatsapp` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `sch_settings` ADD COLUMN `admin_panel_whatsapp_to` varchar(100) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `admin_panel_whatsapp_from` varchar(100) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `admin_panel_whatsapp_mobile` varchar(50) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `student_panel_whatsapp` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `sch_settings` ADD COLUMN `student_panel_whatsapp_to` varchar(100) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `student_panel_whatsapp_from` varchar(100) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `student_panel_whatsapp_mobile` varchar(50) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `front_side_whatsapp` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `sch_settings` ADD COLUMN `front_side_whatsapp_to` varchar(100) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `front_side_whatsapp_from` varchar(100) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `front_side_whatsapp_mobile` varchar(50) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `theme_colour` varchar(20) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `theme_header_colour` varchar(20) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `theme_body_bg` varchar(20) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `theme_sidebar_colour` varchar(20) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `theme_accent_start` varchar(20) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `theme_accent_end` varchar(20) DEFAULT NULL;
ALTER TABLE `sch_settings` ADD COLUMN `theme_header_gradient` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `sch_settings` ADD COLUMN `theme_sidebar_gradient` int(1) NOT NULL DEFAULT 0;
ALTER TABLE `sch_settings` ADD COLUMN `student_resume_download` int(1) NOT NULL DEFAULT 0;

-- Table: staff
ALTER TABLE `staff` ADD COLUMN `salary_type` enum('monthly','hourly') DEFAULT 'monthly';
ALTER TABLE `staff` ADD COLUMN `hourly_rate` decimal(16,2) DEFAULT NULL;

-- Table: staff_payslip
ALTER TABLE `staff_payslip` ADD COLUMN `salary_type` enum('monthly','hourly') DEFAULT 'monthly';
ALTER TABLE `staff_payslip` ADD COLUMN `hourly_rate` decimal(16,2) DEFAULT NULL;
ALTER TABLE `staff_payslip` ADD COLUMN `total_hours_worked` decimal(16,2) DEFAULT NULL;

-- Table: students
ALTER TABLE `students` ADD COLUMN `about` text DEFAULT NULL;
ALTER TABLE `students` ADD COLUMN `designation` varchar(255) DEFAULT NULL;

-- 1C: v_active_biometric_devices is a VIEW, not a table.
-- Cannot ALTER a view. All DROP/ADD/MODIFY COLUMN operations below were targeting
-- a VIEW and would FAIL on production. These statements have been REMOVED.
-- The underlying biometric_devices table already has the correct structure.


-- Table: notification_setting (REPLACE to update existing + add new)
REPLACE INTO `notification_setting` (`id`, `type`, `is_mail`, `is_sms`, `is_whatsapp`, `is_notification`, `display_notification`, `display_sms`, `display_whatsapp`, `is_student_recipient`, `is_guardian_recipient`, `is_staff_recipient`, `display_student_recipient`, `display_guardian_recipient`, `display_staff_recipient`, `subject`, `template_id`, `whatsapp_template_id`, `template`, `variables`, `created_at`) VALUES
(1, 'student_admission', '1', '0', 0, 0, 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Student Admission', '', '', 'Dear {{student_name}} your admission is confirmed in Class: {{class}} in Amaravathi Junior College.', '{{student_name}} {{class}}  {{section}}  {{admission_no}}  {{roll_no}}  {{admission_date}}   {{mobileno}}  {{email}}  {{dob}}  {{guardian_name}}  {{guardian_relation}}  {{guardian_phone}}  {{father_name}}  {{father_phone}}  {{blood_group}}  {{mother_name}}  {{gender}} {{guardian_email}} {{current_session_name}} ', '2024-05-23 10:40:48'),
(2, 'exam_result', '1', '0', 0, 0, 1, 1, 1, 1, 0, 0, 1, 1, NULL, 'Exam Result', '', '', 'Dear {{student_name}} - {{exam_roll_no}}, your {{exam}} result has been published.', '{{student_name}} {{exam_roll_no}} {{exam}}', '2024-05-23 10:40:48'),
(3, 'fee_submission', '1', '1', 0, 0, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Fee Submission', '', '', 'Dear parents, we have received Fees Amount {{fee_amount}} for  {{student_name}}  by Amaravathi junior college', '{{student_name}} {{class}} {{section}} {{fine_type}} {{fine_percentage}} {{fine_amount}} {{fee_group_name}} {{type}} {{code}} {{email}} {{contact_no}} {{invoice_id}} {{sub_invoice_id}} {{due_date}} {{amount}} {{fee_amount}}', '2024-05-23 10:40:48'),
(4, 'absent_attendence', '1', '1', 0, 0, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Absent Attendence', '', '', 'Absent Notice :{{student_name}}  was absent on date {{date}} -  Amaravathi junior college', '{{student_name}} {{mobileno}} {{email}} {{father_name}} {{father_phone}} {{father_occupation}} {{mother_name}} {{mother_phone}} {{guardian_name}} {{guardian_phone}} {{guardian_occupation}} {{guardian_email}} {{date}} {{current_session_name}} {{time_from}} {{time_to}} {{subject_name}} {{subject_code}} {{subject_type}}  ', '2024-05-23 10:42:39'),
(6, 'homework', '1', '0', 0, 0, 1, 1, 1, 1, 0, 0, 1, 1, NULL, 'Homework', '', '', 'New Homework has been created for \r\n{{student_name}} at\r\n\r\n\r\n\r\n{{homework_date}} for the class {{class}} {{section}} {{subject}}. kindly submit your\r\n\r\n\r\n homework before {{submit_date}} .Thank you', '{{homework_date}} {{submit_date}} {{class}} {{section}} {{subject}} {{student_name}} {{admission_no}} ', '2024-05-23 10:40:48'),
(7, 'fees_reminder', '1', '0', 0, 0, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Fees Reminder', '', '', 'Dear parents, please pay fee amount Rs.{{due_amount}} of {{fee_type}} before {{due_date}} for {{student_name}}  from Amaravathi Junior College (ignore if you already paid)', '{{fee_type}}{{fee_code}}{{due_date}}{{student_name}}{{school_name}}{{fee_amount}}{{due_amount}}{{deposit_amount}} ', '2024-05-23 10:40:48'),
(8, 'forgot_password', '1', '0', 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 'Forgot Password', '', '', 'Dear  {{name}} , \r\n    Recently a request was submitted to reset password for your account. If you didn\'t make the request, just ignore this email. Otherwise you can reset your password using this link <a href=\'{{resetPassLink}}\'>Click here to reset your password</a>,\r\nif you\'re having trouble clicking the password reset button, copy and paste the URL below into your web browser. your username {{username}}\r\n{{resetPassLink}}\r\n Regards,\r\n {{school_name}}', '{{school_name}}{{name}}{{username}}{{resetPassLink}} ', '2022-12-28 09:52:24'),
(9, 'online_examination_publish_exam', '1', '0', 0, 0, 1, 1, 1, 1, 0, 0, 1, 1, NULL, 'Online Examination Publish Exam', '', '', 'A new exam {{exam_title}} has been created for  duration: {{time_duration}} min, which will be available from:  {{exam_from}} to  {{exam_to}}.', '{{exam_title}} {{exam_from}} {{exam_to}} {{time_duration}} {{attempt}} {{passing_percentage}}', '2024-05-23 10:40:48'),
(10, 'online_examination_publish_result', '1', '0', 0, 0, 1, 1, 1, 1, 0, 0, 1, 1, NULL, 'Online Examination Publish Result', '', '', 'Exam {{exam_title}} result has been declared which was conducted between  {{exam_from}} to   {{exam_to}}, for more details, please check your student portal.', '{{exam_title}} {{exam_from}} {{exam_to}} {{time_duration}} {{attempt}} {{passing_percentage}}', '2024-05-23 10:40:48'),
(11, 'online_admission_form_submission', '1', '0', 0, 0, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Online Admission Form Submission', '', '', 'Dear {{firstname}}  {{lastname}} your online admission form is Submitted successfully  on date {{date}}. Your Reference number is {{reference_no}}. Please remember your reference number for further process.', ' {{firstname}} {{lastname}} {{date}} {{reference_no}}', '2024-05-23 10:40:48'),
(12, 'online_admission_fees_submission', '0', '0', 0, 0, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Online Admission Fees Submission', '', '', 'Dear {{firstname}}  {{lastname}} your online admission form is Submitted successfully and the payment of {{paid_amount}} has recieved successfully on date {{date}}. Your Reference number is {{reference_no}}. Please remember your reference number for further process.', ' {{firstname}} {{lastname}} {{date}} {{paid_amount}} {{reference_no}}', '2024-05-23 10:40:48'),
(13, 'student_login_credential', '1', '1', 0, 0, 0, 1, 1, 1, 1, 0, 1, 1, NULL, 'Student Login Credential', '1707163291685208209', '', 'Hello {{display_name}} your login details for Url: {{url}} Username: {{username}}  Password: {{password}} admission No: {{admission_no}}', '{{url}} {{display_name}} {{username}} {{password}} {{admission_no}}', '2022-08-06 05:34:41'),
(14, 'staff_login_credential', '1', '1', 0, 0, 0, 1, 1, 0, 0, 1, NULL, NULL, 1, 'Staff Login Credential', '1707163291685208209', '', 'Hello {{first_name}} {{last_name}} your login details for Url: {{url}} Username: {{username}}  Password: {{password}} Employee ID: {{employee_id}}', '{{url}} {{first_name}} {{last_name}} {{username}} {{password}} {{employee_id}}', '2021-12-23 11:59:13'),
(15, 'fee_processing', '1', '1', 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Fee processing', '1707163291301326898', '', 'Dear parents, we have received Fees Amount {{fee_amount}} for  {{student_name}}  by Amaravathi Junior College\r\n{{class}} {{section}} {{email}} {{contact_no}}\r\n\r\n{{student_name}} {{class}} {{section}} {{email}} {{contact_no}} transaction_id :{{transaction_id}} {{fee_amount}}', '{{student_name}} {{class}} {{section}} {{email}} {{contact_no}} {{transaction_id}} {{fee_amount}}', '2024-05-23 10:30:59'),
(16, 'online_admission_fees_processing', '1', '1', 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Online Admission Fees Processing', '', '', 'Dear {{firstname}}  {{lastname}} your online admission form is Submitted successfully and the payment of {{paid_amount}} has processing on date {{date}}. Your Reference number is {{reference_no}} and your transaction id {{transaction_id}}. Please remember your reference number for further process.', ' {{firstname}} {{lastname}} {{date}} {{paid_amount}} {{reference_no}} {{transaction_id}}', '2022-08-06 11:09:47'),
(17, 'student_apply_leave', '1', '1', 0, 0, 0, 1, 1, 0, 1, 1, NULL, 1, 1, 'Student Apply Leave ( {{student_name}} - {{admission_no}} )', '', '', 'My Name is {{student_name}} Class {{class}} section {{section}}. I have to apply leave on {{apply_date}}and from {{from_date}} to {{to_date}}. {{message}} please provide.', '{{message}} {{apply_date}} {{from_date}} {{to_date}} {{student_name}} {{class}} {{section}}', '2022-03-12 11:58:37'),
(18, 'email_pdf_exam_marksheet', '1', '0', 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, NULL, 'Email PDF Exam Marksheet ( {{student_name}} - {{admission_no}} )', '', '', 'Dear {{student_name}} ({{admission_no}}) {{class}} Section {{section}}. We have mailed you the marksheet of Exam {{exam}} Roll no.{{roll_no}}', '{{student_name}} {{class}}  {{section}}  {{admission_no}}  {{roll_no}} {{exam}} {{admit_card_roll_no}} ', '2022-03-12 12:24:42'),
(19, 'behaviour_incident_assigned', '1', '1', 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'Behaviour Incident Assigned', '', '', 'A new {{incident_title}}  behaviour incident with {{incident_point}} point is assigned on you. {{student_name}} {{class}} {{section}} {{admission_no}} {{mobileno}} {{email}} {{guardian_name}} {{guardian_phone}} {{guardian_email}}', '{{incident_title}} {{incident_point}} {{student_name}} {{class}} {{section}} {{admission_no}} {{mobileno}} {{email}} {{guardian_name}} {{guardian_phone}} {{guardian_email}}', '2023-01-02 01:05:44'),
(20, 'cbse_email_pdf_exam_marksheet', '1', '1', 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'CBSE Exam Marksheet PDF ( {{student_name}} - {{admission_no}} )', '', '', 'Dear {{student_name}} ({{admission_no}}) {{class}} Section {{section}}. We have mailed you the marksheet with Roll no.{{roll_no}}', '{{student_name}} {{class}} {{section}} {{admission_no}} {{roll_no}}', '2024-05-23 10:40:48'),
(21, 'cbse_exam_result', '1', '1', 0, 1, 1, 1, 1, 1, 1, 0, 1, 1, NULL, 'CBSE Exam Result', '', '', 'Dear {{student_name}} - {{roll_no}}, your {{exam}} result has been published.', '{{student_name}} {{roll_no}} {{exam}}', '2024-05-23 10:40:48'),
(22, 'online_classes', '1', '0', 0, 0, 1, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Zoom Live Classes', '', '', 'Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}}', '2024-05-23 10:40:48'),
(23, 'online_meeting', '1', '0', 0, 0, 0, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Zoom Live Meeting', '', '', 'Dear staff, your live meeting {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2024-05-23 10:40:48'),
(24, 'zoom_online_classes_start', '1', '0', 0, 0, 1, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Zoom Live Classes Start', '', '', 'Dear student, your live class {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}}', '2024-05-23 10:40:48'),
(25, 'zoom_online_meeting_start', '1', '0', 0, 0, 0, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Zoom Live Meeting Start', '', '', 'Dear {{name}},  your live meeting {{title}}  has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2024-05-23 10:40:48'),
(26, 'gmeet_online_classes', '1', '0', 0, 0, 1, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Gmeet Live Classes', '', '', 'Dear student, your live class {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}}', '2024-05-23 10:40:48'),
(27, 'gmeet_online_meeting', '1', '0', 0, 0, 0, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Gmeet Live Meeting', '', '', 'Dear staff, your live meeting {{title}} has been scheduled on {{date}} for the duration of {{duration}} minute, please do not share the link to any body.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2024-05-23 10:40:48'),
(28, 'gmeet_online_classes_start', '1', '0', 0, 0, 1, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Gmeet  Live Classes Start', '', '', 'Dear student, your live class {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}}', '2024-05-23 10:40:48'),
(29, 'gmeet_online_meeting_start', '1', '0', 0, 0, 0, 1, 1, 0, 0, 0, NULL, NULL, NULL, 'Gmeet Live Meeting Start', '', '', 'Dear {{name}},  your live meeting {{title}} has been started  for the duration of {{duration}} minute.', '{{title}} {{date}} {{duration}} {{employee_id}} {{department}} {{designation}} {{name}} {{contact_no}} {{email}}', '2024-05-23 10:40:48');

-- ============================================================
-- PART 5: PERMISSION SYSTEM SETUP
-- ============================================================

-- 5A: Create dedicated permission groups for custom features
-- (Each feature gets its own group so it appears as a separate section on the permission page)
INSERT IGNORE INTO `permission_group` (`id`, `name`, `short_code`, `is_active`, `system`) VALUES
(1021, 'Face Attendance', 'face_attendance', 1, 0),
(1022, 'Generate Paper', 'generate_paper', 1, 0),
(1023, 'Student Resume', 'student_resume', 1, 0);

INSERT IGNORE INTO `permission_category` (`id`, `perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`, `created_at`) VALUES
(11052, 1021, 'Face Attendance Register', 'face_attendance_register', 1, 1, 0, 1, '2025-12-11 06:21:26'),
(11054, 1022, 'Generate Paper', 'generate_paper', 1, 1, 0, 1, '2025-12-11 06:35:44'),
(11056, 15, 'WhatsApp Setting', 'whatsapp_setting', 1, 0, 1, 0, '2026-03-29 17:55:30'),
(11057, 18, 'My Payroll', 'my_payroll', 1, 0, 0, 0, '2026-03-31 17:32:00'),
(11058, 18, 'View Payroll Settings', 'view_payroll_settings', 1, 0, 0, 0, '2026-03-31 17:32:00'),
(11059, 1023, 'Build CV', 'build_cv', 1, 1, 1, 1, '2026-04-01 08:24:19'),
(11060, 1023, 'Download CV Setting', 'download_cv_setting', 1, 1, 1, 1, '2026-04-01 08:24:19'),
(11061, 1023, 'Download CV', 'download_cv', 1, 1, 1, 1, '2026-04-01 09:54:24'),
(11062, 1021, 'Mark Face Attendance', 'face_attendance_mark', 1, 1, 0, 0, '2026-04-02 16:30:00');

INSERT IGNORE INTO `sidebar_menus` (`id`, `permission_group_id`, `icon`, `menu`, `activate_menu`, `lang_key`, `system_level`, `level`, `sidebar_display`, `access_permissions`, `is_active`, `created_at`) VALUES
(52, 1021, 'ri-user-follow-line', 'Face Attendance', 'face_attendance', 'face_attendance', 0, 11, 1, '(\'face_attendance_register\', \'can_view\')', 1, '2026-03-30 16:46:50'),
(54, 1022, 'ri-sparkling-line', 'Generate Paper', 'generate_paper', 'generate_paper', 0, 12, 1, '(\'generate_paper\', \'can_view\')', 1, '2026-03-30 16:46:50'),
(55, 1023, 'fa fa-file-text-o', 'Student Resume', 'resume', 'student_resume', 200, 26, 1, '(\'build_cv\', \'can_view\') || (\'download_cv\', \'can_view\') || (\'download_cv_setting\', \'can_view\')', 1, '2026-04-01 09:52:41');

INSERT IGNORE INTO `sidebar_sub_menus` (`id`, `sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(501, 21, 'Transport Fees Master', 'transport_fees_master', 'transport_fees_master', 'admin/transportfeesmaster', 1, '(\'transport_fees_master\', \'can_view\')', NULL, 'transportfeesmaster', 'index', '', 1, '2025-10-18 17:01:12'),
(504, 27, 'Time Range Assignments', NULL, 'time_range_assignments', 'admin/timerangeassignments', 1, '(\'time_range_assignments\', \'can_view\')', NULL, 'timerangeassignments', 'index', '', 1, '2025-10-18 17:01:12'),
(506, 52, 'Student Registration', 'face_attendance_student_registration', 'face_attendance_student_registration', 'admin/face_attendance_register/index', 1, '(\'face_attendance_register\', \'can_view\')', NULL, 'Face_attendance_register', 'index,register_student,get_students,check_registration,delete_student', '', 1, '2026-04-01 03:19:00'),
(507, 52, 'Mark Attendance', 'face_attendance_mark_attendance', 'face_attendance_mark_attendance', 'admin/face_attendance_register/mark_attendance', 2, '(\'face_attendance_mark\', \'can_view\')', NULL, 'Face_attendance_register', 'mark_attendance,get_registered_students,save_attendance,get_attendance_records', '', 1, '2025-12-11 06:21:27'),
(510, 54, 'AI Question Paper Generator', 'generate_paper_ai_generator', 'generate_paper_ai_generator', 'admin/generatepaper', 1, '(\'generate_paper\', \'can_view\')', NULL, 'Generatepaper', 'index,save_message,upload_file,generate_pdf,preview', '', 1, '2025-12-11 06:35:44'),
(512, 27, 'whatsapp_messaging', 'whatsapp_messaging', 'whatsapp_messaging', 'admin/whatsappconfig', 23, '(\'whatsapp_setting\', \'can_view\')', NULL, 'whatsappconfig', 'index,messages,testconnection,save,savetwilio,testtwilio,webhook,webhook_receive,getStats', NULL, 1, '2026-03-29 17:56:28'),
(513, 15, 'My Payroll', 'my_payroll', 'my_payroll', 'admin/payroll/myPayroll', 1, '(\'my_payroll\', \'can_view\')', NULL, 'payroll', 'myPayroll', '', 1, '2026-03-31 17:59:05'),
(515, 55, 'build_cv', NULL, 'build_cv', 'admin/resume/index', 1, '(\'build_cv\', \'can_view\')', NULL, 'resume', 'index,resume_setting,student_resume_details', NULL, 1, '2026-04-01 09:52:25'),
(516, 55, 'setting', NULL, 'setting', 'admin/resume/resume_setting', 3, '(\'download_cv_setting\', \'can_view\')', NULL, 'resume', 'resume_setting', NULL, 1, '2026-04-01 09:52:35'),
(517, 55, 'download_cv', NULL, 'download_cv', 'admin/resume/download', 2, '(\'download_cv\', \'can_view\')', NULL, 'resume', 'download', NULL, 1, '2026-04-01 09:52:19');

UPDATE `sidebar_menus` SET `access_permissions` = '(\'online_examination\', \'can_view\') || (\'question_bank\', \'can_view\')' WHERE `id` = 12 AND `access_permissions` LIKE '%can_view\'%';

UPDATE `sidebar_menus` SET `access_permissions` = TRIM(REPLACE(REPLACE(`access_permissions`, '\n', ''), '\r', '')) WHERE `id` = 27;

DELETE FROM `permission_category` WHERE `id` IN (11053, 11055, 11036);

DELETE rp1 FROM `roles_permissions` rp1
INNER JOIN `roles_permissions` rp2 ON 
    rp1.perm_cat_id = rp2.perm_cat_id AND 
    rp1.role_id = rp2.role_id AND 
    rp1.id > rp2.id;

UPDATE `permission_category` SET `perm_group_id` = 18 WHERE `id` IN (11057, 11058) AND `perm_group_id` IS NULL;
