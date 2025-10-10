-- ============================================
-- Diagnostic SQL Queries for Other Collection Report
-- Run these queries in phpMyAdmin to diagnose the issue
-- ============================================

-- Query 1: Check if additional fees data exists
-- Expected: Should return > 0 if data exists
SELECT COUNT(*) as total_additional_fees_records
FROM student_fees_depositeadding;

-- Query 2: Check additional fees by session
-- This shows which sessions have additional fees data
SELECT 
    ss.session_id,
    s.session,
    s.is_active,
    COUNT(*) as fee_count
FROM student_fees_depositeadding sfd
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN sessions s ON s.id = ss.session_id
GROUP BY ss.session_id, s.session, s.is_active
ORDER BY s.id DESC;

-- Query 3: Check current active session
-- This shows which session is currently active
SELECT id, session, is_active
FROM sessions
WHERE is_active = 'yes'
LIMIT 1;

-- Query 4: Check fee_groups_feetypeadding records by session
-- This table is critical for the JOIN in getFeeCollectionReport()
SELECT 
    session_id,
    COUNT(*) as count
FROM fee_groups_feetypeadding
GROUP BY session_id
ORDER BY session_id DESC;

-- Query 5: Check if current session has fee_groups_feetypeadding records
-- Expected: Should return > 0 for the report to work
SELECT COUNT(*) as count
FROM fee_groups_feetypeadding
WHERE session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1);

-- Query 6: Sample additional fees records with payment dates
-- This shows actual payment dates to help determine correct date range
SELECT 
    sfd.id,
    s.firstname,
    s.lastname,
    s.admission_no,
    ft.type as fee_type,
    sfd.amount_detail,
    sess.session,
    sess.is_active
FROM student_fees_depositeadding sfd
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN students s ON s.id = ss.student_id
JOIN sessions sess ON sess.id = ss.session_id
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
LIMIT 5;

-- Query 7: Extract payment dates from JSON amount_detail
-- This shows when payments were actually made
SELECT 
    sfd.id,
    s.firstname,
    s.lastname,
    ft.type as fee_type,
    JSON_EXTRACT(sfd.amount_detail, '$[0].date') as payment_date_1,
    JSON_EXTRACT(sfd.amount_detail, '$[0].amount') as payment_amount_1,
    sess.session
FROM student_fees_depositeadding sfd
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN students s ON s.id = ss.student_id
JOIN sessions sess ON sess.id = ss.session_id
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
WHERE sess.is_active = 'yes'
LIMIT 10;

-- Query 8: Check fee types (additional fees)
-- This shows what additional fee types are defined
SELECT 
    id,
    type,
    code,
    is_system
FROM feetypeadding
ORDER BY id;

-- Query 9: Compare regular fees vs additional fees count
-- This helps understand the data distribution
SELECT 
    'Regular Fees' as fee_category,
    COUNT(*) as count
FROM student_fees_deposite
UNION ALL
SELECT 
    'Additional Fees' as fee_category,
    COUNT(*) as count
FROM student_fees_depositeadding;

-- Query 10: Full diagnostic - Simulate getFeeCollectionReport() query
-- This simulates what the model does (for current session, all dates)
SELECT 
    sfd.*,
    s.firstname,
    s.middlename,
    s.lastname,
    s.admission_no,
    ss.class_id,
    c.class,
    sec.section,
    ss.section_id,
    ss.student_id,
    fg.name as fee_group_name,
    ft.type as fee_type,
    ft.code as fee_code,
    ft.is_system,
    sfm.student_session_id
FROM student_fees_depositeadding sfd
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN fee_groupsadding fg ON fg.id = fgft.fee_groups_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
LEFT JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN classes c ON c.id = ss.class_id
JOIN sections sec ON sec.id = ss.section_id
JOIN students s ON s.id = ss.student_id
WHERE fgft.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
  AND ss.session_id = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1)
ORDER BY sfd.id DESC
LIMIT 10;

-- ============================================
-- INTERPRETATION GUIDE
-- ============================================

-- Query 1 Result:
-- - If 0: No additional fees data exists at all
-- - If > 0: Additional fees data exists, continue to Query 2

-- Query 2 Result:
-- - Shows which sessions have data
-- - Look for is_active = 'yes' to see if current session has data
-- - If current session has 0 records, that's why report shows no data

-- Query 3 Result:
-- - Shows the current active session ID
-- - Use this ID to check other queries

-- Query 4 & 5 Results:
-- - If Query 5 returns 0, the JOIN in getFeeCollectionReport() will fail
-- - This means fee groups are not set up for additional fees in current session

-- Query 6 & 7 Results:
-- - Shows actual payment dates
-- - Use these dates to set the correct date range in the report

-- Query 10 Result:
-- - If this returns 0 records, the report will show "No record found"
-- - This simulates exactly what getFeeCollectionReport() does
-- - If this returns data, the issue is in the date filtering or controller logic

-- ============================================
-- COMMON ISSUES AND SOLUTIONS
-- ============================================

-- Issue 1: Query 1 returns 0
-- Solution: No additional fees data exists. You need to:
--   1. Define additional fee types (Admin → Fees → Other Fees)
--   2. Assign fees to students
--   3. Collect some fees

-- Issue 2: Query 2 shows data for other sessions but not current
-- Solution: Either:
--   1. Select a different session in the report dropdown
--   2. Or assign additional fees to the current session

-- Issue 3: Query 5 returns 0
-- Solution: Set up fee groups for additional fees:
--   1. Go to Admin → Fees → Fee Groups (Other)
--   2. Create fee groups
--   3. Link to current session

-- Issue 4: Query 10 returns 0 but Query 1 returns > 0
-- Solution: The JOINs are failing. Check:
--   1. fee_groups_feetypeadding table has records for current session
--   2. student_session table has correct session_id
--   3. All foreign keys are properly set

-- ============================================
-- QUICK FIX TEST
-- ============================================

-- If you want to quickly test if the report works with a different session,
-- temporarily change the active session:

-- Step 1: Find a session that has data (from Query 2)
-- Step 2: Temporarily make it active:
-- UPDATE sessions SET is_active = 'no';
-- UPDATE sessions SET is_active = 'yes' WHERE id = X; -- Replace X with session ID that has data

-- Step 3: Test the report
-- Step 4: Change back to original session if needed

