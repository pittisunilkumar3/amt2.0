-- ============================================
-- FACE ATTENDANCE - ADD STAFF SUPPORT COLUMNS
-- ============================================
-- Adds person_type, staff_id, and designation columns
-- to existing face_attendance_students table
-- to support both student and staff face registration
-- ============================================

USE amt;

-- Add person_type column (student/staff)
ALTER TABLE `face_attendance_students` 
ADD COLUMN `person_type` ENUM('student', 'staff') DEFAULT 'student' 
COMMENT 'Whether this record is for a student or staff member' 
AFTER `last_updated`;

-- Add staff_id column
ALTER TABLE `face_attendance_students` 
ADD COLUMN `staff_id` INT(11) DEFAULT NULL 
COMMENT 'Reference to staff table' 
AFTER `student_id`;

-- Add designation column
ALTER TABLE `face_attendance_students` 
ADD COLUMN `designation` VARCHAR(150) DEFAULT NULL 
COMMENT 'Staff designation' 
AFTER `phone`;

-- Add person_type to face_attendance_records
ALTER TABLE `face_attendance_records` 
ADD COLUMN `person_type` ENUM('student', 'staff') DEFAULT 'student' 
COMMENT 'Whether record is for student or staff' 
AFTER `notes`;

-- Add index for staff_id
ALTER TABLE `face_attendance_students` 
ADD INDEX `idx_staff_id` (`staff_id`);

-- Add index for person_type
ALTER TABLE `face_attendance_students` 
ADD INDEX `idx_person_type` (`person_type`);

-- Add index for person_type on records
ALTER TABLE `face_attendance_records` 
ADD INDEX `idx_person_type` (`person_type`);

-- Update existing records to have person_type = 'student'
UPDATE `face_attendance_students` SET `person_type` = 'student' WHERE `person_type` IS NULL;

-- Verify
SELECT 'Migration Complete!' as status;
SHOW COLUMNS FROM face_attendance_students WHERE Field IN ('person_type', 'staff_id', 'designation');
SHOW COLUMNS FROM face_attendance_records WHERE Field = 'person_type';
