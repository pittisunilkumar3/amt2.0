-- ============================================================================
-- DIAGNOSTIC SQL QUERIES FOR NEGATIVE BALANCE INVESTIGATION
-- Fee Group-wise Collection Report
-- ============================================================================

-- Query 1: Identify fee groups with negative balances (amount = 0 but payments exist)
-- ============================================================================
SELECT 
    'REGULAR FEES - NEGATIVE BALANCE GROUPS' as report_section;

SELECT 
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    COUNT(DISTINCT sfm.id) as total_master_records,
    COUNT(DISTINCT sfm.student_session_id) as total_students,
    SUM(sfm.amount) as total_assigned_amount,
    COUNT(DISTINCT sfd.id) as total_payment_records
FROM fee_groups fg
INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id
LEFT JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
LEFT JOIN student_fees_deposite sfd ON sfd.student_fees_master_id = sfm.id
WHERE fg.is_system = 0
GROUP BY fg.id, fg.name
HAVING SUM(sfm.amount) = 0 AND COUNT(DISTINCT sfd.id) > 0
ORDER BY fg.name;

-- Query 2: Same for additional fees
-- ============================================================================
SELECT 
    'ADDITIONAL FEES - NEGATIVE BALANCE GROUPS' as report_section;

SELECT 
    fga.id as fee_group_id,
    fga.name as fee_group_name,
    COUNT(DISTINCT sfma.id) as total_master_records,
    COUNT(DISTINCT sfma.student_session_id) as total_students,
    SUM(sfma.amount) as total_assigned_amount,
    COUNT(DISTINCT sfda.id) as total_payment_records
FROM fee_groupsadding fga
INNER JOIN fee_session_groupsadding fsga ON fsga.fee_groups_id = fga.id
LEFT JOIN student_fees_masteradding sfma ON sfma.fee_session_group_id = fga.id
LEFT JOIN student_fees_depositeadding sfda ON sfda.student_fees_master_id = sfma.id
WHERE fga.is_system = 0
GROUP BY fga.id, fga.name
HAVING SUM(sfma.amount) = 0 AND COUNT(DISTINCT sfda.id) > 0
ORDER BY fga.name;

-- Query 3: Detailed breakdown for specific problematic fee groups (REGULAR)
-- ============================================================================
SELECT
    'DETAILED BREAKDOWN - REGULAR FEES' as report_section;

SELECT
    fg.name as fee_group_name,
    s.admission_no,
    CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', IFNULL(s.lastname, '')) as student_name,
    c.class as class_name,
    sec.section as section_name,
    sfm.id as student_fees_master_id,
    sfm.amount as assigned_amount,
    COUNT(sfd.id) as payment_count,
    sfd.amount as payment_amount,
    sfd.amount_detail
FROM fee_groups fg
INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id
INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id
INNER JOIN students s ON s.id = ss.student_id
INNER JOIN classes c ON c.id = ss.class_id
INNER JOIN sections sec ON sec.id = ss.section_id
LEFT JOIN student_fees_deposite sfd ON sfd.student_fees_master_id = sfm.id
WHERE fg.is_system = 0
  AND sfm.amount = 0
  AND sfd.id IS NOT NULL
  AND fg.name LIKE '%2025-2026%'
GROUP BY fg.name, s.admission_no, s.firstname, s.middlename, s.lastname,
         c.class, sec.section, sfm.id, sfm.amount, sfd.amount, sfd.amount_detail
ORDER BY fg.name, s.admission_no
LIMIT 20;

-- Query 4: Detailed breakdown for specific problematic fee groups (ADDITIONAL)
-- ============================================================================
SELECT
    'DETAILED BREAKDOWN - ADDITIONAL FEES' as report_section;

SELECT
    fga.name as fee_group_name,
    s.admission_no,
    CONCAT(s.firstname, ' ', IFNULL(s.middlename, ''), ' ', IFNULL(s.lastname, '')) as student_name,
    c.class as class_name,
    sec.section as section_name,
    sfma.id as student_fees_master_id,
    sfma.amount as assigned_amount,
    COUNT(sfda.id) as payment_count,
    sfda.amount as payment_amount,
    sfda.amount_detail
FROM fee_groupsadding fga
INNER JOIN fee_session_groupsadding fsga ON fsga.fee_groups_id = fga.id
INNER JOIN student_fees_masteradding sfma ON sfma.fee_session_group_id = fga.id
INNER JOIN student_session ss ON ss.id = sfma.student_session_id
INNER JOIN students s ON s.id = ss.student_id
INNER JOIN classes c ON c.id = ss.class_id
INNER JOIN sections sec ON sec.id = ss.section_id
LEFT JOIN student_fees_depositeadding sfda ON sfda.student_fees_master_id = sfma.id
WHERE fga.is_system = 0
  AND sfma.amount = 0
  AND sfda.id IS NOT NULL
GROUP BY fga.name, s.admission_no, s.firstname, s.middlename, s.lastname,
         c.class, sec.section, sfma.id, sfma.amount, sfda.amount, sfda.amount_detail
ORDER BY fga.name, s.admission_no
LIMIT 20;

-- Query 5: Check fee_groups_feetype to understand fee structure
-- ============================================================================
SELECT 
    'FEE STRUCTURE - REGULAR FEES' as report_section;

SELECT 
    fg.name as fee_group_name,
    ft.type as fee_type,
    fgft.amount as fee_type_amount,
    fgft.due_date,
    COUNT(DISTINCT sfm.id) as students_assigned
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
INNER JOIN feetype ft ON ft.id = fgft.feetype_id
LEFT JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
WHERE fg.is_system = 0
  AND fg.name LIKE '%2025-2026%'
GROUP BY fg.name, ft.type, fgft.amount, fgft.due_date
ORDER BY fg.name, ft.type;

-- Query 6: Check fee_groups_feetypeadding for additional fees
-- ============================================================================
SELECT 
    'FEE STRUCTURE - ADDITIONAL FEES' as report_section;

SELECT 
    fga.name as fee_group_name,
    fta.type as fee_type,
    fgfta.amount as fee_type_amount,
    fgfta.due_date,
    COUNT(DISTINCT sfma.id) as students_assigned
FROM fee_groupsadding fga
INNER JOIN fee_groups_feetypeadding fgfta ON fgfta.fee_groups_id = fga.id
INNER JOIN feetypeadding fta ON fta.id = fgfta.feetype_id
LEFT JOIN student_fees_masteradding sfma ON sfma.fee_session_group_id = fga.id
WHERE fga.is_system = 0
  AND fga.name LIKE '%2025-2026%'
GROUP BY fga.name, fta.type, fgfta.amount, fgfta.due_date
ORDER BY fga.name, fta.type;

-- Query 7: Sample payment records to understand amount_detail JSON structure
-- ============================================================================
SELECT 
    'SAMPLE PAYMENT RECORDS - REGULAR' as report_section;

SELECT 
    sfd.id,
    sfd.student_fees_master_id,
    sfd.amount,
    sfd.amount_discount,
    sfd.amount_fine,
    sfd.amount_detail,
    sfd.date as payment_date,
    fg.name as fee_group_name
FROM student_fees_deposite sfd
INNER JOIN student_fees_master sfm ON sfm.id = sfd.student_fees_master_id
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
WHERE sfm.amount = 0
  AND fg.is_system = 0
LIMIT 10;

-- Query 8: Sample payment records for additional fees
-- ============================================================================
SELECT 
    'SAMPLE PAYMENT RECORDS - ADDITIONAL' as report_section;

SELECT 
    sfda.id,
    sfda.student_fees_master_id,
    sfda.amount,
    sfda.amount_discount,
    sfda.amount_fine,
    sfda.amount_detail,
    sfda.date as payment_date,
    fga.name as fee_group_name
FROM student_fees_depositeadding sfda
INNER JOIN student_fees_masteradding sfma ON sfma.id = sfda.student_fees_master_id
INNER JOIN fee_groupsadding fga ON fga.id = sfma.fee_session_group_id
WHERE sfma.amount = 0
  AND fga.is_system = 0
LIMIT 10;

-- Query 9: Check if there are any fee_groups_feetype records that should have been summed
-- ============================================================================
SELECT 
    'POTENTIAL MISSING AMOUNTS - REGULAR' as report_section;

SELECT 
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    SUM(fgft.amount) as total_fee_types_amount,
    COUNT(DISTINCT fgft.id) as fee_type_count,
    AVG(sfm.amount) as avg_student_master_amount,
    COUNT(DISTINCT sfm.id) as student_master_count
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
LEFT JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
WHERE fg.is_system = 0
GROUP BY fg.id, fg.name
HAVING AVG(sfm.amount) = 0 AND SUM(fgft.amount) > 0
ORDER BY fg.name;

-- Query 10: Same for additional fees
-- ============================================================================
SELECT 
    'POTENTIAL MISSING AMOUNTS - ADDITIONAL' as report_section;

SELECT 
    fga.id as fee_group_id,
    fga.name as fee_group_name,
    SUM(fgfta.amount) as total_fee_types_amount,
    COUNT(DISTINCT fgfta.id) as fee_type_count,
    AVG(sfma.amount) as avg_student_master_amount,
    COUNT(DISTINCT sfma.id) as student_master_count
FROM fee_groupsadding fga
INNER JOIN fee_groups_feetypeadding fgfta ON fgfta.fee_groups_id = fga.id
LEFT JOIN student_fees_masteradding sfma ON sfma.fee_session_group_id = fga.id
WHERE fga.is_system = 0
GROUP BY fga.id, fga.name
HAVING AVG(sfma.amount) = 0 AND SUM(fgfta.amount) > 0
ORDER BY fga.name;

