-- Test the corrected calculation logic

-- Create temp tables as in the fix script
CREATE TEMPORARY TABLE temp_fee_group_totals AS
SELECT 
    fg.id as fee_group_id,
    SUM(fgft.amount) as total_amount
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
GROUP BY fg.id;

CREATE TEMPORARY TABLE temp_fee_amounts AS
SELECT 
    sfm.id as student_fees_master_id,
    sfm.fee_session_group_id,
    fg.id as fee_group_id,
    fg.name as fee_group_name,
    COALESCE(tfgt.total_amount, 0) as calculated_amount
FROM student_fees_master sfm
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
LEFT JOIN temp_fee_group_totals tfgt ON tfgt.fee_group_id = fg.id
WHERE sfm.is_system = 0
  AND sfm.amount = 0;

-- Show summary
SELECT 
    fee_group_name,
    calculated_amount as amount_per_student,
    COUNT(*) as student_count,
    calculated_amount * COUNT(*) as total_amount
FROM temp_fee_amounts
WHERE calculated_amount > 0
  AND fee_group_name LIKE '%2025-2026%'
GROUP BY fee_group_name, calculated_amount
ORDER BY fee_group_name;

-- Show specific examples
SELECT 
    'EXAMPLES FOR 2025-2026 -SR- 0NTC' as info;

SELECT 
    student_fees_master_id,
    fee_group_name,
    calculated_amount
FROM temp_fee_amounts
WHERE fee_group_name = '2025-2026 -SR- 0NTC'
LIMIT 10;

-- Clean up
DROP TEMPORARY TABLE temp_fee_amounts;
DROP TEMPORARY TABLE temp_fee_group_totals;

