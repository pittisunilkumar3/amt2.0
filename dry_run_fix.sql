-- DRY RUN: Show what will be updated WITHOUT making changes

-- Check Regular Fees
SELECT 
    'REGULAR FEES - PREVIEW OF UPDATES' as report_section;

SELECT 
    fg.name as fee_group_name,
    COUNT(DISTINCT sfm.id) as students_to_update,
    COALESCE(SUM(fgft.amount), 0) as amount_per_student,
    COUNT(DISTINCT sfm.id) * COALESCE(SUM(fgft.amount), 0) as total_amount_to_add
FROM student_fees_master sfm
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
LEFT JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
WHERE sfm.is_system = 0
  AND sfm.amount = 0
  AND fg.name LIKE '%2025-2026%'
GROUP BY fg.id, fg.name
HAVING COALESCE(SUM(fgft.amount), 0) > 0
ORDER BY fg.name;

-- Check Additional Fees
SELECT 
    'ADDITIONAL FEES - PREVIEW OF UPDATES' as report_section;

SELECT 
    fga.name as fee_group_name,
    COUNT(DISTINCT sfma.id) as students_to_update,
    COALESCE(SUM(fgfta.amount), 0) as amount_per_student,
    COUNT(DISTINCT sfma.id) * COALESCE(SUM(fgfta.amount), 0) as total_amount_to_add
FROM student_fees_masteradding sfma
INNER JOIN fee_groupsadding fga ON fga.id = sfma.fee_session_group_id
LEFT JOIN fee_groups_feetypeadding fgfta ON fgfta.fee_groups_id = fga.id
WHERE sfma.is_system = 0
  AND sfma.amount = 0
GROUP BY fga.id, fga.name
HAVING COALESCE(SUM(fgfta.amount), 0) > 0
ORDER BY fga.name;

-- Show specific examples for 2025-2026 -SR- 0NTC
SELECT 
    'EXAMPLE: 2025-2026 -SR- 0NTC' as report_section;

SELECT 
    s.admission_no,
    CONCAT(s.firstname, ' ', IFNULL(s.lastname, '')) as student_name,
    sfm.id as student_fees_master_id,
    sfm.amount as current_amount,
    COALESCE(SUM(fgft.amount), 0) as new_amount,
    COUNT(sfd.id) as payment_count
FROM student_fees_master sfm
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id
INNER JOIN students s ON s.id = ss.student_id
LEFT JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
LEFT JOIN student_fees_deposite sfd ON sfd.student_fees_master_id = sfm.id
WHERE fg.name = '2025-2026 -SR- 0NTC'
  AND sfm.is_system = 0
GROUP BY s.admission_no, s.firstname, s.lastname, sfm.id, sfm.amount
LIMIT 10;

