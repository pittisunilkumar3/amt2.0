-- ============================================
-- AUTO PAYROLL GENERATION SYSTEM
-- Date: 2026-03-30
-- ============================================

-- 1. Create payroll_settings table
CREATE TABLE IF NOT EXISTS `payroll_settings` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `auto_payroll_enabled` ENUM('yes','no') NOT NULL DEFAULT 'no' COMMENT 'Enable/disable auto payroll generation',
    `auto_payroll_day` INT(11) NOT NULL DEFAULT 1 COMMENT 'Day of month (1-28) to auto-generate PREVIOUS month payroll',
    `working_days_method` ENUM('exclude_sundays','exclude_sundays_saturdays','all_days') NOT NULL DEFAULT 'exclude_sundays' COMMENT 'How to calculate working days in a month',
    
    -- Deduction config for Absent
    `absent_deduction_type` ENUM('per_day','fixed') NOT NULL DEFAULT 'per_day' COMMENT 'per_day = multiply per_day_salary, fixed = flat amount',
    `absent_deduction_value` DECIMAL(10,2) NOT NULL DEFAULT 1.00 COMMENT 'If per_day: multiplier (1=full day), If fixed: flat amount in currency',
    
    -- Deduction config for Late
    `late_deduction_type` ENUM('per_day','fixed') NOT NULL DEFAULT 'per_day',
    `late_deduction_value` DECIMAL(10,2) NOT NULL DEFAULT 0.50 COMMENT 'Default: half day salary deducted per late',
    
    -- Deduction config for Half Day
    `half_day_deduction_type` ENUM('per_day','fixed') NOT NULL DEFAULT 'per_day',
    `half_day_deduction_value` DECIMAL(10,2) NOT NULL DEFAULT 0.50 COMMENT 'Default: half day salary deducted',
    
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Insert default settings (single row, always id=1)
INSERT INTO `payroll_settings` (`id`) VALUES (1)
ON DUPLICATE KEY UPDATE `id` = `id`;

-- 3. Update permissions: give can_edit to Admin and Accountant for staff_payroll
UPDATE `roles_permissions` SET `can_edit` = 1 WHERE `perm_cat_id` = 90 AND `role_id` IN (1, 3);
