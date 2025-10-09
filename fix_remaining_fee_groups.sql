-- Fix remaining fee groups that weren't updated in the first run
-- Target specific fee groups that still have amount = 0

-- Create temporary table for remaining fee group totals
CREATE TEMPORARY TABLE temp_remaining_fee_totals AS
SELECT 
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    SUM(fgft.amount) as total_amount
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
WHERE fg.name IN ('2025-2026 JR-MPC(BOOKS FEE)', '2025-2026 SR-JEE-MAINS')
GROUP BY fg.id, fg.name;

-- Show what we're about to fix
SELECT 'REMAINING FEE GROUPS TO FIX' as info, fee_group_name, total_amount
FROM temp_remaining_fee_totals;

-- Create temporary table with calculated amounts for student_fees_master
CREATE TEMPORARY TABLE temp_remaining_amounts AS
SELECT 
    sfm.id as student_fees_master_id,
    sfm.fee_session_group_id,
    trft.total_amount as calculated_amount,
    trft.fee_group_name
FROM student_fees_master sfm
INNER JOIN fee_session_groups fsg ON fsg.id = sfm.fee_session_group_id
INNER JOIN temp_remaining_fee_totals trft ON trft.fee_group_id = fsg.fee_groups_id
WHERE sfm.amount = 0;

-- Show records to be updated
SELECT 
    'RECORDS TO UPDATE' as info,
    fee_group_name,
    COUNT(*) as student_count,
    calculated_amount as amount_per_student,
    (COUNT(*) * calculated_amount) as total_amount
FROM temp_remaining_amounts
GROUP BY fee_group_name, calculated_amount;

-- Update student_fees_master with correct amounts
UPDATE student_fees_master sfm
INNER JOIN temp_remaining_amounts tra ON tra.student_fees_master_id = sfm.id
SET sfm.amount = tra.calculated_amount
WHERE tra.calculated_amount > 0;

-- Verify the fix
SELECT 
    'VERIFICATION AFTER UPDATE' as info,
    fg.name as fee_group_name,
    COUNT(sfm.id) as student_count,
    SUM(sfm.amount) as total_amount_now
FROM fee_groups fg
INNER JOIN fee_session_groups fsg ON fsg.fee_groups_id = fg.id
INNER JOIN student_fees_master sfm ON sfm.fee_session_group_id = fsg.id
WHERE fg.name IN ('2025-2026 JR-MPC(BOOKS FEE)', '2025-2026 SR-JEE-MAINS')
GROUP BY fg.id, fg.name;

-- Clean up temporary tables
DROP TEMPORARY TABLE temp_remaining_fee_totals;
DROP TEMPORARY TABLE temp_remaining_amounts;
