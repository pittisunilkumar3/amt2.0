-- Complete fix for missing columns in student_fees_deposite table
-- This script adds both student_session_id and student_hostel_fee_id columns

-- Check current table structure
SELECT 'Current table structure:' as info;
DESCRIBE student_fees_deposite;

-- Add student_session_id column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'student_fees_deposite' 
     AND COLUMN_NAME = 'student_session_id') > 0,
    'SELECT "student_session_id column already exists" as result',
    'ALTER TABLE student_fees_deposite ADD COLUMN student_session_id int(11) DEFAULT NULL AFTER fee_groups_feetype_id'
));


PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add student_hostel_fee_id column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'student_fees_deposite' 
     AND COLUMN_NAME = 'student_hostel_fee_id') > 0,
    'SELECT "student_hostel_fee_id column already exists" as result',
    'ALTER TABLE student_fees_deposite ADD COLUMN student_hostel_fee_id int(11) DEFAULT NULL AFTER student_transport_fee_id'
));


PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Add indexes for better performance
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'student_fees_deposite' 
     AND INDEX_NAME = 'idx_student_session_id') > 0,
    'SELECT "student_session_id index already exists" as result',
    'ALTER TABLE student_fees_deposite ADD INDEX idx_student_session_id (student_session_id)'
));


PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_NAME = 'student_fees_deposite' 
     AND INDEX_NAME = 'idx_student_hostel_fee_id') > 0,
    'SELECT "student_hostel_fee_id index already exists" as result',
    'ALTER TABLE student_fees_deposite ADD INDEX idx_student_hostel_fee_id (student_hostel_fee_id)'
));


PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verify the final table structure
SELECT 'Updated table structure:' as info;
DESCRIBE student_fees_deposite;

-- Show sample data to verify columns exist
SELECT 'Sample data check:' as info;
SELECT id, student_fees_master_id, fee_groups_feetype_id, student_session_id, student_transport_fee_id, student_hostel_fee_id, created_at 
FROM student_fees_deposite 
ORDER BY id DESC 
LIMIT 5;
