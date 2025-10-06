-- Fix Dashboard Charts: Add Current Sample Data for Income and Expense Charts
-- This script adds sample data for the current month/year to make charts visible

USE amt;

-- ========================================
-- STEP 1: Add Current Month Sample Income Data
-- ========================================

-- Insert sample income data for current month (September 2025)
INSERT INTO `income` (`name`, `invoice_no`, `date`, `amount`, `note`, `income_head_id`, `documents`, `created_at`) VALUES
('Student Fees Collection', 'INV-2025-001', '2025-09-01', 15000.00, 'Monthly student fees collection', 1, '', NOW()),
('Building Rent Income', 'INV-2025-002', '2025-09-05', 8000.00, 'Monthly building rent', 2, '', NOW()),
('Donation Received', 'INV-2025-003', '2025-09-10', 5000.00, 'Donation from alumni', 3, '', NOW()),
('Book Sales', 'INV-2025-004', '2025-09-15', 3000.00, 'School book sales', 4, '', NOW()),
('Uniform Sales', 'INV-2025-005', '2025-09-20', 2500.00, 'School uniform sales', 5, '', NOW()),
('Miscellaneous Income', 'INV-2025-006', '2025-09-25', 1500.00, 'Other miscellaneous income', 6, '', NOW());

-- ========================================
-- STEP 2: Add Current Month Sample Expense Data
-- ========================================

-- Insert sample expense data for current month (September 2025)
INSERT INTO `expenses` (`name`, `invoice_no`, `date`, `amount`, `note`, `exp_head_id`, `documents`, `created_at`) VALUES
('Office Stationery Purchase', 'EXP-2025-001', '2025-09-02', 2500.00, 'Monthly stationery supplies', 1, '', NOW()),
('Electricity Bill', 'EXP-2025-002', '2025-09-05', 4500.00, 'Monthly electricity bill', 2, '', NOW()),
('Telephone Bill', 'EXP-2025-003', '2025-09-08', 1200.00, 'Monthly telephone and internet', 3, '', NOW()),
('Miscellaneous Expenses', 'EXP-2025-004', '2025-09-12', 3000.00, 'Various miscellaneous expenses', 4, '', NOW()),
('Flower Decoration', 'EXP-2025-005', '2025-09-18', 800.00, 'School event decoration', 5, '', NOW()),
('Maintenance Costs', 'EXP-2025-006', '2025-09-22', 2200.00, 'Building maintenance', 6, '', NOW());

-- ========================================
-- STEP 3: Verify Income Head Categories Exist
-- ========================================

-- Ensure we have proper income head categories
INSERT IGNORE INTO `income_head` (`income_category`, `description`, `is_active`, `is_deleted`, `created_at`) VALUES
('Donation', 'Donations and contributions', 'yes', 'no', NOW()),
('Rent', 'Rental income', 'yes', 'no', NOW()),
('Miscellaneous', 'Other miscellaneous income', 'yes', 'no', NOW()),
('Book Sale', 'Book sales income', 'yes', 'no', NOW()),
('Uniform Sale', 'Uniform sales income', 'yes', 'no', NOW()),
('Fees Collection', 'Student fees collection', 'yes', 'no', NOW());

-- ========================================
-- STEP 4: Verify Expense Head Categories Exist
-- ========================================

-- Ensure we have proper expense head categories
INSERT IGNORE INTO `expense_head` (`exp_category`, `description`, `is_active`, `is_deleted`, `created_at`) VALUES
('Stationery Purchase', 'Office and school stationery', 'yes', 'no', NOW()),
('Electricity Bill', 'Monthly electricity expenses', 'yes', 'no', NOW()),
('Telephone Bill', 'Phone and internet bills', 'yes', 'no', NOW()),
('Miscellaneous', 'Other miscellaneous expenses', 'yes', 'no', NOW()),
('Flower', 'Decoration and flower expenses', 'yes', 'no', NOW()),
('Maintenance', 'Building and equipment maintenance', 'yes', 'no', NOW());

-- ========================================
-- STEP 5: Add More Sample Data for Better Chart Visualization
-- ========================================

-- Add additional income data for better chart representation
INSERT INTO `income` (`name`, `invoice_no`, `date`, `amount`, `note`, `income_head_id`, `documents`, `created_at`) VALUES
('Additional Fees', 'INV-2025-007', '2025-09-28', 12000.00, 'Additional student fees', 1, '', NOW()),
('Extra Donation', 'INV-2025-008', '2025-09-29', 7500.00, 'Extra donation received', 3, '', NOW()),
('Book Sales Extra', 'INV-2025-009', '2025-09-30', 4500.00, 'Additional book sales', 4, '', NOW());

-- Add additional expense data for better chart representation
INSERT INTO `expenses` (`name`, `invoice_no`, `date`, `amount`, `note`, `exp_head_id`, `documents`, `created_at`) VALUES
('Extra Stationery', 'EXP-2025-007', '2025-09-28', 1800.00, 'Additional stationery purchase', 1, '', NOW()),
('Extra Electricity', 'EXP-2025-008', '2025-09-29', 2200.00, 'Additional electricity costs', 2, '', NOW()),
('Extra Maintenance', 'EXP-2025-009', '2025-09-30', 3500.00, 'Emergency maintenance', 6, '', NOW());

-- ========================================
-- STEP 6: Verification Queries
-- ========================================

SELECT '=== DASHBOARD CHART DATA VERIFICATION ===' as Info;

-- Show current month income data for charts
SELECT 'Current Month Income Data (September 2025):' as description;
SELECT 
    ih.income_category,
    SUM(i.amount) as total_amount,
    COUNT(i.id) as record_count
FROM income i 
JOIN income_head ih ON i.income_head_id = ih.id 
WHERE DATE_FORMAT(i.date, '%Y-%m') = '2025-09'
GROUP BY ih.income_category
ORDER BY total_amount DESC;

-- Show current month expense data for charts
SELECT 'Current Month Expense Data (September 2025):' as description;
SELECT 
    eh.exp_category,
    SUM(e.amount) as total_amount,
    COUNT(e.id) as record_count
FROM expenses e 
JOIN expense_head eh ON e.exp_head_id = eh.id 
WHERE DATE_FORMAT(e.date, '%Y-%m') = '2025-09'
GROUP BY eh.exp_category
ORDER BY total_amount DESC;

-- Show total counts
SELECT 'Total Records Summary:' as description;
SELECT 
    'Income Records' as type,
    COUNT(*) as total_count,
    SUM(amount) as total_amount
FROM income 
WHERE DATE_FORMAT(date, '%Y-%m') = '2025-09'
UNION ALL
SELECT 
    'Expense Records' as type,
    COUNT(*) as total_count,
    SUM(amount) as total_amount
FROM expenses 
WHERE DATE_FORMAT(date, '%Y-%m') = '2025-09';

SELECT '=== CHART DATA SETUP COMPLETED ===' as Info;
SELECT 'Dashboard charts should now display current month data properly!' as Message;
