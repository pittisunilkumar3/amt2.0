-- ============================================================================
-- DIAGNOSTIC SQL QUERIES FOR OTHER FEE COLLECTION REPORT ISSUE
-- ============================================================================
-- Issue: Transaction appears in Daily Collection Report but NOT in Other Fee Collection Report
-- Student: JOREPALLI LAKSHMI DEVI (ID: 2023412)
-- Receipt: 945/1
-- Amount: ₹3,000.00 (Fine)
-- ============================================================================

-- Query 1: Find the student's session information
-- ============================================================================
SELECT 
    s.id as student_id,
    s.admission_no,
    s.firstname,
    s.lastname,
    s.father_name,
    ss.id as student_session_id,
    ss.session_id,
    ss.class_id,
    c.class,
    ss.section_id,
    sec.section,
    sess.session
FROM students s
INNER JOIN student_session ss ON ss.student_id = s.id
INNER JOIN classes c ON c.id = ss.class_id
INNER JOIN sections sec ON sec.id = ss.section_id
INNER JOIN sessions sess ON sess.id = ss.session_id
WHERE s.admission_no = '2023412'
ORDER BY ss.session_id DESC;

-- Query 2: Find all other fee deposits for this student
-- ============================================================================
SELECT 
    sfd.id as deposit_id,
    sfd.student_fees_master_id,
    sfd.fee_groups_feetype_id,
    sfd.amount_detail,
    sfd.created_at,
    sfm.student_session_id,
    ss.session_id as student_session_session_id,
    fg.name as fee_group_name,
    ft.type as fee_type,
    fgft.session_id as fee_groups_feetype_session_id
FROM student_fees_depositeadding sfd
INNER JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id
INNER JOIN students s ON s.id = ss.student_id
INNER JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
INNER JOIN fee_groupsadding fg ON fg.id = fgft.fee_groups_id
INNER JOIN feetypeadding ft ON ft.id = fgft.feetype_id
WHERE s.admission_no = '2023412'
ORDER BY sfd.id DESC;

-- Query 3: Check the amount_detail JSON for receipt 945/1
-- ============================================================================
SELECT 
    sfd.id as deposit_id,
    sfd.amount_detail,
    s.admission_no,
    s.firstname,
    s.lastname,
    fg.name as fee_group_name
FROM student_fees_depositeadding sfd
INNER JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id
INNER JOIN students s ON s.id = ss.student_id
INNER JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
INNER JOIN fee_groupsadding fg ON fg.id = fgft.fee_groups_id
WHERE s.admission_no = '2023412'
AND sfd.amount_detail LIKE '%945/1%';

-- Query 4: Compare session IDs between student_session and fee_groups_feetypeadding
-- ============================================================================
-- This query identifies session mismatches that could cause filtering issues
SELECT 
    s.admission_no,
    s.firstname,
    s.lastname,
    ss.session_id as student_enrolled_session,
    fgft.session_id as fee_defined_session,
    CASE 
        WHEN ss.session_id = fgft.session_id THEN 'MATCH'
        ELSE 'MISMATCH'
    END as session_match_status,
    fg.name as fee_group_name,
    ft.type as fee_type,
    sfd.id as deposit_id
FROM student_fees_depositeadding sfd
INNER JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id
INNER JOIN students s ON s.id = ss.student_id
INNER JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
INNER JOIN fee_groupsadding fg ON fg.id = fgft.fee_groups_id
INNER JOIN feetypeadding ft ON ft.id = fgft.feetype_id
WHERE s.admission_no = '2023412'
ORDER BY sfd.id DESC;

-- Query 5: Get current session ID
-- ============================================================================
SELECT id, session FROM sessions WHERE is_active = 'yes' ORDER BY id DESC LIMIT 1;

-- Query 6: Simulate the Daily Collection Report query (getOtherfeesCurrentSessionStudentFeess)
-- ============================================================================
-- This query does NOT filter by session at all
SELECT
    student_fees_masteradding.*,
    fee_session_groupsadding.fee_groups_id,
    fee_session_groupsadding.session_id,
    fee_groupsadding.name,
    fee_groupsadding.is_system,
    fee_groups_feetypeadding.amount AS fee_amount,
    fee_groups_feetypeadding.id AS fee_groups_feetype_id,
    student_fees_depositeadding.id AS student_fees_deposite_id,
    student_fees_depositeadding.amount_detail,
    students.admission_no,
    students.firstname,
    students.lastname,
    students.father_name,
    classes.class,
    sections.section
FROM
    student_fees_masteradding
INNER JOIN
    fee_session_groupsadding ON fee_session_groupsadding.id = student_fees_masteradding.fee_session_group_id
INNER JOIN
    student_session ON student_session.id = student_fees_masteradding.student_session_id
INNER JOIN
    students ON students.id = student_session.student_id
INNER JOIN
    classes ON student_session.class_id = classes.id
INNER JOIN
    sections ON sections.id = student_session.section_id
INNER JOIN
    fee_groupsadding ON fee_groupsadding.id = fee_session_groupsadding.fee_groups_id
INNER JOIN
    fee_groups_feetypeadding ON fee_groupsadding.id = fee_groups_feetypeadding.fee_groups_id
LEFT JOIN
    student_fees_depositeadding ON student_fees_depositeadding.student_fees_master_id = student_fees_masteradding.id
    AND student_fees_depositeadding.fee_groups_feetype_id = fee_groups_feetypeadding.id
WHERE students.admission_no = '2023412'
ORDER BY student_fees_depositeadding.id DESC;

-- Query 7: Simulate the Other Fee Collection Report query (getFeeCollectionReport)
-- ============================================================================
-- This query DOES filter by session - this is the problem!
-- Replace {CURRENT_SESSION_ID} with the actual current session ID from Query 5
SELECT
    student_fees_depositeadding.*,
    students.firstname,
    students.middlename,
    students.lastname,
    student_session.class_id,
    classes.class,
    sections.section,
    student_session.section_id,
    student_session.student_id,
    fee_groupsadding.name,
    feetypeadding.type,
    feetypeadding.code,
    feetypeadding.is_system,
    student_fees_masteradding.student_session_id,
    students.admission_no,
    student_session.session_id as student_enrolled_session
FROM student_fees_depositeadding
INNER JOIN fee_groups_feetypeadding ON fee_groups_feetypeadding.id = student_fees_depositeadding.fee_groups_feetype_id
INNER JOIN fee_groupsadding ON fee_groupsadding.id = fee_groups_feetypeadding.fee_groups_id
INNER JOIN feetypeadding ON feetypeadding.id = fee_groups_feetypeadding.feetype_id
INNER JOIN student_fees_masteradding ON student_fees_masteradding.id = student_fees_depositeadding.student_fees_master_id
LEFT JOIN student_session ON student_session.id = student_fees_masteradding.student_session_id
INNER JOIN classes ON classes.id = student_session.class_id
INNER JOIN sections ON sections.id = student_session.section_id
INNER JOIN students ON students.id = student_session.student_id
WHERE students.admission_no = '2023412'
-- AND student_session.session_id = {CURRENT_SESSION_ID}  -- This line might be filtering out the record!
ORDER BY student_fees_depositeadding.id DESC;

-- ============================================================================
-- EXPECTED FINDINGS:
-- ============================================================================
-- 1. Query 4 should show if there's a session mismatch
-- 2. Query 6 should return the transaction (Daily Collection Report logic)
-- 3. Query 7 WITH the session filter might NOT return the transaction
-- 4. Query 7 WITHOUT the session filter should return the transaction
--
-- ROOT CAUSE: The session filtering in getFeeCollectionReport() is too restrictive
-- compared to getOtherfeesCurrentSessionStudentFeess() which doesn't filter by session
-- ============================================================================

