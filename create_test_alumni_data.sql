-- ============================================================================
-- SQL Script to Create Test Alumni Data
-- ============================================================================
-- This script will mark some existing students as alumni and optionally
-- add their current contact information to the alumni_students table.
--
-- Run this script in phpMyAdmin or MySQL command line:
-- mysql -u root amt < create_test_alumni_data.sql
-- ============================================================================

USE amt;

-- ============================================================================
-- STEP 1: Mark some students as alumni in student_session table
-- ============================================================================
-- This will mark the first 10 students as alumni (is_alumni = 1)
-- You can adjust the LIMIT to mark more or fewer students

UPDATE student_session 
SET is_alumni = 1 
WHERE student_id IN (
    SELECT id FROM (
        SELECT s.id 
        FROM students s 
        WHERE s.is_active = 'yes' 
        LIMIT 10
    ) AS temp
);

-- ============================================================================
-- STEP 2: Add alumni contact information (optional)
-- ============================================================================
-- This adds current contact information for the alumni students
-- You can customize the email, phone, occupation, and address

INSERT INTO alumni_students (student_id, current_email, current_phone, occupation, address)
SELECT 
    s.id,
    CONCAT(LOWER(s.firstname), '.', LOWER(s.lastname), '@gmail.com') as current_email,
    CONCAT('98765', LPAD(s.id, 5, '0')) as current_phone,
    CASE 
        WHEN s.id % 5 = 0 THEN 'Software Engineer'
        WHEN s.id % 5 = 1 THEN 'Doctor'
        WHEN s.id % 5 = 2 THEN 'Teacher'
        WHEN s.id % 5 = 3 THEN 'Business Owner'
        ELSE 'Government Employee'
    END as occupation,
    CONCAT('Current Address for ', s.firstname, ' ', s.lastname) as address
FROM students s
JOIN student_session ss ON s.id = ss.student_id
WHERE s.is_active = 'yes' 
AND ss.is_alumni = 1
AND s.id NOT IN (SELECT student_id FROM alumni_students)
LIMIT 10;

-- ============================================================================
-- VERIFICATION QUERIES
-- ============================================================================
-- Run these queries to verify the data was created successfully

-- Check how many students are marked as alumni
SELECT COUNT(*) as 'Students Marked as Alumni' 
FROM student_session 
WHERE is_alumni = 1;

-- Check how many alumni records exist
SELECT COUNT(*) as 'Alumni Records with Contact Info' 
FROM alumni_students;

-- View sample alumni data
SELECT 
    s.admission_no,
    s.firstname,
    s.lastname,
    c.class,
    sec.section,
    sess.session as pass_out_year,
    IFNULL(a.current_email, 'Not provided') as current_email,
    IFNULL(a.occupation, 'Not provided') as occupation
FROM students s
JOIN student_session ss ON s.id = ss.student_id
JOIN classes c ON ss.class_id = c.id
JOIN sections sec ON ss.section_id = sec.id
JOIN sessions sess ON ss.session_id = sess.id
LEFT JOIN alumni_students a ON a.student_id = s.id
WHERE s.is_active = 'yes' 
AND ss.is_alumni = 1
LIMIT 10;

-- ============================================================================
-- CLEANUP (if you want to remove test data later)
-- ============================================================================
-- Uncomment these lines to remove the test alumni data

-- DELETE FROM alumni_students WHERE student_id IN (
--     SELECT id FROM students WHERE is_active = 'yes' LIMIT 10
-- );

-- UPDATE student_session 
-- SET is_alumni = 0 
-- WHERE student_id IN (
--     SELECT id FROM (
--         SELECT s.id FROM students s WHERE s.is_active = 'yes' LIMIT 10
--     ) AS temp
-- );

