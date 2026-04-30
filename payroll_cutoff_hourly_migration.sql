-- ============================================
-- PAYROLL CUTOFF + HOURLY RATE MIGRATION
-- Date: 2026-03-31
-- ============================================

-- 1. Add payroll cutoff date column to payroll_settings
ALTER TABLE `payroll_settings` 
    ADD COLUMN `payroll_cutoff_day` INT(11) NOT NULL DEFAULT 0 
    COMMENT 'Payroll cutoff day of month (1-28). Attendance after this day counts toward NEXT month. 0 = no cutoff (entire month).' 
    AFTER `auto_payroll_day`;

-- Set default cutoff to last day of month (no cutoff)
UPDATE `payroll_settings` SET `payroll_cutoff_day` = 0 WHERE `id` = 1;

-- 2. Add salary_type column to staff table (monthly or hourly)
ALTER TABLE `staff` 
    ADD COLUMN `salary_type` ENUM('monthly','hourly') NOT NULL DEFAULT 'monthly' 
    COMMENT 'monthly = fixed monthly salary, hourly = based on hours worked' 
    AFTER `basic_salary`;

-- 3. Add hourly_rate column to staff table
ALTER TABLE `staff` 
    ADD COLUMN `hourly_rate` DECIMAL(10,2) NOT NULL DEFAULT 0.00 
    COMMENT 'Hourly rate for hourly salary type staff' 
    AFTER `salary_type`;

-- 4. Add payroll hours worked columns to staff_payslip
ALTER TABLE `staff_payslip` 
    ADD COLUMN `total_hours_worked` DECIMAL(10,2) NOT NULL DEFAULT 0.00 
    COMMENT 'Total hours worked in the payroll period' 
    AFTER `leave_deduction`;

ALTER TABLE `staff_payslip` 
    ADD COLUMN `hourly_rate` DECIMAL(10,2) NOT NULL DEFAULT 0.00 
    COMMENT 'Hourly rate used for this payslip (0 for monthly staff)' 
    AFTER `total_hours_worked`;

ALTER TABLE `staff_payslip` 
    ADD COLUMN `salary_type` ENUM('monthly','hourly') NOT NULL DEFAULT 'monthly' 
    COMMENT 'monthly or hourly calculation used for this payslip' 
    AFTER `hourly_rate`;
