-- ============================================
-- ADD STAFF ATTENDANCE SYNC COLUMNS
-- ============================================
-- These columns track whether face recognition attendance
-- has been synced to the main staff_attendance table

USE amt;

-- Add sync status column
ALTER TABLE `face_attendance_records` 
ADD COLUMN `staff_att_sync` TINYINT(1) DEFAULT 0 
COMMENT '1 if synced to staff_attendance table' 
AFTER `person_type`;

-- Add staff attendance type column (tracks what was recorded)
ALTER TABLE `face_attendance_records` 
ADD COLUMN `staff_att_type_id` INT(11) DEFAULT NULL 
COMMENT 'staff_attendance_type_id recorded in staff_attendance (1=Present,2=Late)' 
AFTER `staff_att_sync`;

-- Verify
SELECT 'Columns added!' as status;
SHOW COLUMNS FROM face_attendance_records WHERE Field IN ('staff_att_sync', 'staff_att_type_id');
