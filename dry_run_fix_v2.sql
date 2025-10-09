-- DRY RUN V2: Correct calculation per student

-- First, let's see the fee structure for problematic groups
SELECT 
    'FEE STRUCTURE FOR 2025-2026 -SR- 0NTC' as report_section;

SELECT 
    fg.name as fee_group_name,
    ft.type as fee_type,
    fgft.amount as fee_type_amount
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
INNER JOIN feetype ft ON ft.id = fgft.feetype_id
WHERE fg.name = '2025-2026 -SR- 0NTC';

-- Now calculate the correct total per fee group
SELECT 
    'CORRECT CALCULATION - PER FEE GROUP' as report_section;

SELECT 
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    SUM(fgft.amount) as total_fee_amount_per_student,
    COUNT(DISTINCT sfm.id) as students_to_update,
    SUM(fgft.amount) * COUNT(DISTINCT sfm.id) as total_amount_to_add
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fg.id
WHERE sfm.is_system = 0
  AND sfm.amount = 0
  AND fg.name LIKE '%2025-2026%'
GROUP BY fg.id, fg.name
ORDER BY fg.name;

-- Show the calculation for individual students
SELECT 
    'INDIVIDUAL STUDENT CALCULATION' as report_section;

SELECT 
    s.admission_no,
    CONCAT(s.firstname, ' ', IFNULL(s.lastname, '')) as student_name,
    fg.name as fee_group_name,
    sfm.id as student_fees_master_id,
    sfm.amount as current_amount,
    (SELECT SUM(fgft2.amount) 
     FROM fee_groups_feetype fgft2 
     WHERE fgft2.fee_groups_id = fg.id) as calculated_new_amount
FROM student_fees_master sfm
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
INNER JOIN student_session ss ON ss.id = sfm.student_session_id
INNER JOIN students s ON s.id = ss.student_id
WHERE fg.name = '2025-2026 -SR- 0NTC'
  AND sfm.is_system = 0
  AND sfm.amount = 0
LIMIT 10;

-- Check another fee group
SELECT 
    'FEE STRUCTURE FOR 2025-2026 JR-BIPC(BOOKS FEE)' as report_section;

SELECT 
    fg.name as fee_group_name,
    ft.type as fee_type,
    fgft.amount as fee_type_amount
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
INNER JOIN feetype ft ON ft.id = fgft.feetype_id
WHERE fg.name = '2025-2026 JR-BIPC(BOOKS FEE)';

