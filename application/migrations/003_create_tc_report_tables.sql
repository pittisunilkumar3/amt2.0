-- =============================================
-- Migration: 003_create_tc_report_tables
-- Description: Tracking table and sidebar menu for TC Reports
-- =============================================

-- 1. Create tracking table
CREATE TABLE IF NOT EXISTS `student_tc_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `certificate_id` int(11) NOT NULL,
  `generated_by` int(11) NOT NULL,
  `generated_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- 2. Add Document Reports submenu
INSERT INTO `sidebar_sub_menus` (
    `sidebar_menu_id`, 
    `menu`, 
    `lang_key`, 
    `url`, 
    `level`, 
    `access_permissions`, 
    `activate_controller`, 
    `activate_methods`, 
    `is_active`
) 
SELECT 
    26, 
    'Document Reports', 
    'document_reports', 
    'admin/documentreport', 
    17, 
    '(\'student_report\', \'can_view\')', 
    'documentreport', 
    'index,tcreport', 
    1
FROM (SELECT 1) AS tmp
WHERE NOT EXISTS (
    SELECT id FROM `sidebar_sub_menus` WHERE `lang_key` = 'document_reports'
) LIMIT 1;

-- 3. Update settings
UPDATE `sidebar_sub_menus` SET 
    `url` = 'admin/documentreport', 
    `access_permissions` = '(\'student_report\', \'can_view\')', 
    `activate_controller` = 'documentreport', 
    `activate_methods` = 'index,tcreport',
    `is_active` = 1
WHERE `lang_key` = 'document_reports';
