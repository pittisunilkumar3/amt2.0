-- SQL Migration for Student Admission Optional Fields and Reference Mapping
-- This script ensures that database tables for student references and application documents allow optional (NULL) values.

-- Ensure student_reference staff_id is nullable (Optional Reference By)
ALTER TABLE `student_reference` MODIFY `staff_id` int(11) NULL;

-- Ensure student_application file_path is nullable (Optional Application Document)
ALTER TABLE `student_application` MODIFY `file_path` varchar(250) NULL;

-- Ensure student_admi table structure (for Admission No module)
CREATE TABLE IF NOT EXISTS `student_admi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `admi_no` varchar(100) DEFAULT NULL,
  `admi_status` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
