-- Final comprehensive fix for all remaining fee groups with amount = 0
-- This will fix ALL fee groups that have fee types defined but student_fees_master.amount = 0

-- Create temporary table for ALL fee group totals that need fixing
CREATE TEMPORARY TABLE temp_all_fee_totals AS
SELECT 
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    SUM(fgft.amount) as total_amount
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
WHERE EXISTS (
    SELECT 1 FROM fee_session_groups fsg 
    INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fsg.id 
    WHERE fsg.fee_groups_id = fg.id AND sfm.amount = 0
)
GROUP BY fg.id, fg.name
HAVING total_amount > 0;

-- Show what we're about to fix
SELECT 'ALL REMAINING FEE GROUPS TO FIX' as info, fee_group_name, total_amount
FROM temp_all_fee_totals
ORDER BY total_amount DESC;

-- Create temporary table with calculated amounts for student_fees_master
CREATE TEMPORARY TABLE temp_all_amounts AS
SELECT 
    sfm.id as student_fees_master_id,
    sfm.fee_session_group_id,
    taft.total_amount as calculated_amount,
    taft.fee_group_name
FROM student_fees_master sfm
INNER JOIN fee_session_groups fsg ON fsg.id = sfm.fee_session_group_id
INNER JOIN temp_all_fee_totals taft ON taft.fee_group_id = fsg.fee_groups_id
WHERE sfm.amount = 0;

-- Show records to be updated
SELECT 
    'RECORDS TO UPDATE' as info,
    fee_group_name,
    COUNT(*) as student_count,
    calculated_amount as amount_per_student,
    (COUNT(*) * calculated_amount) as total_amount
FROM temp_all_amounts
GROUP BY fee_group_name, calculated_amount
ORDER BY total_amount DESC;

-- Update student_fees_master with correct amounts
UPDATE student_fees_master sfm
INNER JOIN temp_all_amounts taa ON taa.student_fees_master_id = sfm.id
SET sfm.amount = taa.calculated_amount
WHERE taa.calculated_amount > 0;

-- Show update count
SELECT ROW_COUNT() as 'TOTAL RECORDS UPDATED';

-- Final verification - show all 2025-2026 fee groups status
SELECT 
    'FINAL STATUS - ALL 2025-2026 FEE GROUPS' as info,
    fg.name as fee_group_name,
    COUNT(sfm.id) as student_count,
    SUM(sfm.amount) as total_amount_assigned,
    SUM(CASE WHEN sfm.amount = 0 THEN 1 ELSE 0 END) as still_zero_records,
    SUM(CASE WHEN sfm.amount > 0 THEN 1 ELSE 0 END) as fixed_records
FROM fee_groups fg
INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id
INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fsg.id
WHERE fg.name LIKE '2025-2026%'
GROUP BY fg.id, fg.name
ORDER BY total_amount_assigned DESC;

-- Clean up temporary tables
DROP TEMPORARY TABLE temp_all_fee_totals;
DROP TEMPORARY TABLE temp_all_amounts;
