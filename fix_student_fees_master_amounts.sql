-- ============================================================================
-- FIX STUDENT_FEES_MASTER AMOUNTS
-- This script calculates and updates the correct fee amounts in student_fees_master
-- based on the sum of fee_groups_feetype amounts for each fee group
-- ============================================================================

-- BACKUP RECOMMENDATION: Before running this script, backup the tables:
-- mysqldump -u root amt student_fees_master student_fees_masteradding > backup_before_fix.sql

-- ============================================================================
-- PART 1: FIX REGULAR FEES (student_fees_master)
-- ============================================================================

-- Step 1: Create a temporary table with calculated amounts
-- First calculate the total amount per fee group
CREATE TEMPORARY TABLE temp_fee_group_totals AS
SELECT
    fg.id as fee_group_id,
    SUM(fgft.amount) as total_amount
FROM fee_groups fg
INNER JOIN fee_groups_feetype fgft ON fgft.fee_groups_id = fg.id
GROUP BY fg.id;

-- Then join with student_fees_master to get amount per student
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

-- Step 2: Show what will be updated (for verification)
SELECT 
    'REGULAR FEES - RECORDS TO BE UPDATED' as info,
    COUNT(*) as total_records,
    SUM(calculated_amount) as total_amount_to_add
FROM temp_fee_amounts
WHERE calculated_amount > 0;

-- Step 3: Show sample of updates
SELECT 
    fee_group_name,
    COUNT(*) as student_count,
    calculated_amount as amount_per_student,
    COUNT(*) * calculated_amount as total_amount
FROM temp_fee_amounts
WHERE calculated_amount > 0
GROUP BY fee_group_name, calculated_amount
ORDER BY fee_group_name
LIMIT 20;

-- Step 4: Update student_fees_master with calculated amounts
UPDATE student_fees_master sfm
INNER JOIN temp_fee_amounts tfa ON tfa.student_fees_master_id = sfm.id
SET sfm.amount = tfa.calculated_amount
WHERE tfa.calculated_amount > 0;

-- Step 5: Verify the update
SELECT 
    'REGULAR FEES - VERIFICATION AFTER UPDATE' as info,
    COUNT(*) as records_updated,
    SUM(sfm.amount) as total_amount_now
FROM student_fees_master sfm
INNER JOIN temp_fee_amounts tfa ON tfa.student_fees_master_id = sfm.id
WHERE tfa.calculated_amount > 0;

-- Clean up temporary tables
DROP TEMPORARY TABLE temp_fee_amounts;
DROP TEMPORARY TABLE temp_fee_group_totals;

-- ============================================================================
-- PART 2: FIX ADDITIONAL FEES (student_fees_masteradding)
-- ============================================================================

-- Step 1: Create a temporary table with calculated amounts for additional fees
-- First calculate the total amount per fee group
CREATE TEMPORARY TABLE temp_fee_group_totals_adding AS
SELECT
    fga.id as fee_group_id,
    SUM(fgfta.amount) as total_amount
FROM fee_groupsadding fga
INNER JOIN fee_groups_feetypeadding fgfta ON fgfta.fee_groups_id = fga.id
GROUP BY fga.id;

-- Then join with student_fees_masteradding to get amount per student
CREATE TEMPORARY TABLE temp_fee_amounts_adding AS
SELECT
    sfma.id as student_fees_master_id,
    sfma.fee_session_group_id,
    fga.id as fee_group_id,
    fga.name as fee_group_name,
    COALESCE(tfgt.total_amount, 0) as calculated_amount
FROM student_fees_masteradding sfma
INNER JOIN fee_groupsadding fga ON fga.id = sfma.fee_session_group_id
LEFT JOIN temp_fee_group_totals_adding tfgt ON tfgt.fee_group_id = fga.id
WHERE sfma.is_system = 0
  AND sfma.amount = 0;

-- Step 2: Show what will be updated (for verification)
SELECT 
    'ADDITIONAL FEES - RECORDS TO BE UPDATED' as info,
    COUNT(*) as total_records,
    SUM(calculated_amount) as total_amount_to_add
FROM temp_fee_amounts_adding
WHERE calculated_amount > 0;

-- Step 3: Show sample of updates
SELECT 
    fee_group_name,
    COUNT(*) as student_count,
    calculated_amount as amount_per_student,
    COUNT(*) * calculated_amount as total_amount
FROM temp_fee_amounts_adding
WHERE calculated_amount > 0
GROUP BY fee_group_name, calculated_amount
ORDER BY fee_group_name
LIMIT 20;

-- Step 4: Update student_fees_masteradding with calculated amounts
UPDATE student_fees_masteradding sfma
INNER JOIN temp_fee_amounts_adding tfa ON tfa.student_fees_master_id = sfma.id
SET sfma.amount = tfa.calculated_amount
WHERE tfa.calculated_amount > 0;

-- Step 5: Verify the update
SELECT 
    'ADDITIONAL FEES - VERIFICATION AFTER UPDATE' as info,
    COUNT(*) as records_updated,
    SUM(sfma.amount) as total_amount_now
FROM student_fees_masteradding sfma
INNER JOIN temp_fee_amounts_adding tfa ON tfa.student_fees_master_id = sfma.id
WHERE tfa.calculated_amount > 0;

-- Clean up temporary tables
DROP TEMPORARY TABLE temp_fee_amounts_adding;
DROP TEMPORARY TABLE temp_fee_group_totals_adding;

-- ============================================================================
-- PART 3: FINAL VERIFICATION
-- ============================================================================

-- Check if there are still any records with amount = 0 but payments exist
SELECT 
    'REMAINING ISSUES - REGULAR FEES' as report_type,
    COUNT(*) as records_with_issue
FROM student_fees_master sfm
LEFT JOIN student_fees_deposite sfd ON sfd.student_fees_master_id = sfm.id
WHERE sfm.amount = 0
  AND sfd.id IS NOT NULL
  AND sfm.is_system = 0;

SELECT 
    'REMAINING ISSUES - ADDITIONAL FEES' as report_type,
    COUNT(*) as records_with_issue
FROM student_fees_masteradding sfma
LEFT JOIN student_fees_depositeadding sfda ON sfda.student_fees_master_id = sfma.id
WHERE sfma.amount = 0
  AND sfda.id IS NOT NULL
  AND sfma.is_system = 0;

-- Show summary of fee groups that were fixed
SELECT 
    'SUMMARY - REGULAR FEES FIXED' as report_type,
    fg.name as fee_group_name,
    COUNT(DISTINCT sfm.id) as students_fixed,
    SUM(sfm.amount) as total_amount_assigned
FROM student_fees_master sfm
INNER JOIN fee_groups fg ON fg.id = sfm.fee_session_group_id
WHERE sfm.amount > 0
  AND fg.is_system = 0
  AND fg.name LIKE '%2025-2026%'
GROUP BY fg.name
ORDER BY fg.name;

SELECT 
    'SUMMARY - ADDITIONAL FEES FIXED' as report_type,
    fga.name as fee_group_name,
    COUNT(DISTINCT sfma.id) as students_fixed,
    SUM(sfma.amount) as total_amount_assigned
FROM student_fees_masteradding sfma
INNER JOIN fee_groupsadding fga ON fga.id = sfma.fee_session_group_id
WHERE sfma.amount > 0
  AND fga.is_system = 0
GROUP BY fga.name
ORDER BY fga.name;

