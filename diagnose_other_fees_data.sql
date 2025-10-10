-- ============================================
-- CRITICAL DIAGNOSTIC: Other Collection Report Data Issue
-- ============================================
-- Run these queries in phpMyAdmin to find the root cause

-- STEP 1: Check if additional fees data exists at all
-- ============================================
SELECT 'STEP 1: Check if additional fees data exists' as step;

SELECT COUNT(*) as total_records
FROM student_fees_depositeadding;
-- Expected: > 0 if data exists

-- STEP 2: Check current active session
-- ============================================
SELECT 'STEP 2: Current active session' as step;

SELECT id, session, is_active
FROM sessions
WHERE is_active = 'yes';
-- Note the session ID for next queries

-- STEP 3: Check fee_groups_feetypeadding table (CRITICAL!)
-- ============================================
SELECT 'STEP 3: Check fee_groups_feetypeadding by session' as step;

SELECT 
    session_id,
    COUNT(*) as record_count
FROM fee_groups_feetypeadding
GROUP BY session_id
ORDER BY session_id DESC;
-- This table is CRITICAL for getFeeCollectionReport()
-- If current session has 0 records, the report will show no data!

-- STEP 4: Check fee_session_groupsadding table (Used by Daily Collection)
-- ============================================
SELECT 'STEP 4: Check fee_session_groupsadding by session' as step;

SELECT 
    session_id,
    COUNT(*) as record_count
FROM fee_session_groupsadding
GROUP BY session_id
ORDER BY session_id DESC;
-- This is what Daily Collection Report uses

-- STEP 5: Compare the two approaches
-- ============================================
SELECT 'STEP 5: KEY DIFFERENCE - Two different table structures!' as step;

-- Daily Collection Report uses this structure:
-- student_fees_masteradding -> fee_session_groupsadding -> fee_groupsadding -> fee_groups_feetypeadding

-- Other Collection Report uses this structure:
-- student_fees_depositeadding -> fee_groups_feetypeadding -> fee_groupsadding

-- STEP 6: Check if student_fees_depositeadding has records
-- ============================================
SELECT 'STEP 6: Check student_fees_depositeadding records' as step;

SELECT COUNT(*) as total_payment_records
FROM student_fees_depositeadding;

-- STEP 7: Check if the JOIN is failing
-- ============================================
SELECT 'STEP 7: Test the JOIN that getFeeCollectionReport() uses' as step;

-- This simulates the exact query from getFeeCollectionReport()
SELECT COUNT(*) as records_after_join
FROM student_fees_depositeadding sfd
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN fee_groupsadding fg ON fg.id = fgft.fee_groups_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
LEFT JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN classes c ON c.id = ss.class_id
JOIN sections sec ON sec.id = ss.section_id
JOIN students s ON s.id = ss.student_id;
-- If this returns 0, the JOINs are failing

-- STEP 8: Test with session filter (THE CRITICAL TEST!)
-- ============================================
SELECT 'STEP 8: Test with session filter (CRITICAL!)' as step;

-- Get current session ID first
SET @current_session = (SELECT id FROM sessions WHERE is_active = 'yes' LIMIT 1);

-- Test the exact query with session filter
SELECT COUNT(*) as records_with_session_filter
FROM student_fees_depositeadding sfd
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
JOIN fee_groupsadding fg ON fg.id = fgft.fee_groups_id
JOIN feetypeadding ft ON ft.id = fgft.feetype_id
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
LEFT JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN classes c ON c.id = ss.class_id
JOIN sections sec ON sec.id = ss.section_id
JOIN students s ON s.id = ss.student_id
WHERE fgft.session_id = @current_session
  AND ss.session_id = @current_session;
-- If this returns 0, the session filter is excluding all data!

-- STEP 9: Check each session individually
-- ============================================
SELECT 'STEP 9: Check data by session' as step;

SELECT 
    ss.session_id,
    sess.session,
    sess.is_active,
    COUNT(*) as payment_count
FROM student_fees_depositeadding sfd
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN sessions sess ON sess.id = ss.session_id
GROUP BY ss.session_id, sess.session, sess.is_active
ORDER BY ss.session_id DESC;
-- This shows which sessions have payment data

-- STEP 10: Check fee_groups_feetypeadding for each session
-- ============================================
SELECT 'STEP 10: Check fee_groups_feetypeadding for each session' as step;

SELECT 
    fgft.session_id,
    sess.session,
    sess.is_active,
    COUNT(*) as fee_group_mapping_count
FROM fee_groups_feetypeadding fgft
JOIN sessions sess ON sess.id = fgft.session_id
GROUP BY fgft.session_id, sess.session, sess.is_active
ORDER BY fgft.session_id DESC;
-- This shows which sessions have fee group mappings

-- STEP 11: THE ROOT CAUSE TEST
-- ============================================
SELECT 'STEP 11: ROOT CAUSE - Check if session mismatch exists' as step;

-- Check if payments exist but fee_groups_feetypeadding doesn't match the session
SELECT 
    'Payments in student_fees_depositeadding' as data_type,
    ss.session_id,
    sess.session,
    COUNT(*) as count
FROM student_fees_depositeadding sfd
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN sessions sess ON sess.id = ss.session_id
GROUP BY ss.session_id, sess.session

UNION ALL

SELECT 
    'Mappings in fee_groups_feetypeadding' as data_type,
    fgft.session_id,
    sess.session,
    COUNT(*) as count
FROM fee_groups_feetypeadding fgft
JOIN sessions sess ON sess.id = fgft.session_id
GROUP BY fgft.session_id, sess.session
ORDER BY session_id DESC, data_type;

-- STEP 12: Sample data to understand the structure
-- ============================================
SELECT 'STEP 12: Sample payment record' as step;

SELECT 
    sfd.id as payment_id,
    sfd.student_fees_master_id,
    sfd.fee_groups_feetype_id,
    sfm.student_session_id,
    ss.session_id as student_session_session_id,
    fgft.session_id as fee_group_session_id,
    CASE 
        WHEN ss.session_id = fgft.session_id THEN 'MATCH'
        ELSE 'MISMATCH'
    END as session_match_status
FROM student_fees_depositeadding sfd
JOIN student_fees_masteradding sfm ON sfm.id = sfd.student_fees_master_id
JOIN student_session ss ON ss.id = sfm.student_session_id
JOIN fee_groups_feetypeadding fgft ON fgft.id = sfd.fee_groups_feetype_id
LIMIT 10;
-- This shows if there's a session mismatch between student_session and fee_groups_feetypeadding

-- ============================================
-- INTERPRETATION GUIDE
-- ============================================

/*
SCENARIO 1: Step 1 returns 0
- No additional fees data exists at all
- Solution: Assign and collect additional fees

SCENARIO 2: Step 7 returns 0 but Step 6 returns > 0
- The JOINs are failing
- Likely cause: Missing records in fee_groups_feetypeadding
- Solution: Set up fee groups properly

SCENARIO 3: Step 7 returns > 0 but Step 8 returns 0
- Data exists but session filter excludes everything
- ROOT CAUSE: fee_groups_feetypeadding.session_id doesn't match student_session.session_id
- This is the MOST LIKELY issue!
- Solution: Either:
  a) Update fee_groups_feetypeadding.session_id to match
  b) Remove one of the session filters from the query
  c) Use a different session

SCENARIO 4: Step 9 and Step 10 show different sessions
- Payments exist in one session
- Fee group mappings exist in a different session
- ROOT CAUSE: Session mismatch
- Solution: Align the sessions or modify the query

SCENARIO 5: Step 12 shows 'MISMATCH'
- The session_id in student_session doesn't match session_id in fee_groups_feetypeadding
- This is why the double WHERE clause fails
- Solution: Fix the data or modify the query
*/

-- ============================================
-- RECOMMENDED FIX BASED ON RESULTS
-- ============================================

/*
If Step 8 returns 0 but Step 7 returns > 0:

The issue is the double session filter in getFeeCollectionReport():
- Line 773: WHERE fee_groups_feetypeadding.session_id = current_session
- Line 774: AND student_session.session_id = current_session

These two conditions might not match!

SOLUTION OPTIONS:

Option 1: Remove the fee_groups_feetypeadding.session_id filter
- Only filter by student_session.session_id
- This matches how the system actually stores data

Option 2: Update fee_groups_feetypeadding records
- Update the session_id to match student_session.session_id

Option 3: Use fee_session_groupsadding like Daily Collection Report
- Change the query structure to match Daily Collection Report

I recommend Option 1 as it's the safest and most logical fix.
*/

