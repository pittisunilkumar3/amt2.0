-- SQL script to ensure advance payment tables have the correct structure
-- Run this in your database if the revert functionality is not working

-- Check if advance_payment_usage table exists and has required columns
-- Add missing columns if they don't exist

-- Add is_reverted column if it doesn't exist
ALTER TABLE `advance_payment_usage` 
ADD COLUMN `is_reverted` ENUM('yes','no') DEFAULT 'no' AFTER `description`;

-- Add revert_reason column if it doesn't exist
ALTER TABLE `advance_payment_usage` 
ADD COLUMN `revert_reason` TEXT NULL AFTER `is_reverted`;

-- Add reverted_at column if it doesn't exist
ALTER TABLE `advance_payment_usage` 
ADD COLUMN `reverted_at` DATETIME NULL AFTER `revert_reason`;

-- Ensure the advance_payment_usage table has the correct structure
-- If the table doesn't exist, create it
CREATE TABLE IF NOT EXISTS `advance_payment_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `advance_payment_id` int(11) NOT NULL,
  `student_fees_deposite_id` int(11) DEFAULT NULL,
  `student_fees_depositeadding_id` int(11) DEFAULT NULL,
  `amount_used` decimal(10,2) NOT NULL,
  `usage_date` date NOT NULL,
  `fee_category` varchar(50) DEFAULT 'fees',
  `description` text,
  `is_reverted` enum('yes','no') DEFAULT 'no',
  `revert_reason` text,
  `reverted_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `advance_payment_id` (`advance_payment_id`),
  KEY `student_fees_deposite_id` (`student_fees_deposite_id`),
  KEY `student_fees_depositeadding_id` (`student_fees_depositeadding_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Ensure the student_advance_payments table exists
CREATE TABLE IF NOT EXISTS `student_advance_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_session_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `balance` decimal(10,2) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_mode` varchar(50) NOT NULL,
  `description` text,
  `invoice_id` varchar(100) NOT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `collected_by` varchar(100) DEFAULT NULL,
  `is_active` enum('yes','no') DEFAULT 'yes',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_session_id` (`student_session_id`),
  KEY `invoice_id` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Add foreign key constraints if they don't exist
-- Note: Uncomment these if your database supports foreign keys and you want to enforce referential integrity

-- ALTER TABLE `advance_payment_usage` 
-- ADD CONSTRAINT `fk_advance_payment_usage_advance_payment` 
-- FOREIGN KEY (`advance_payment_id`) REFERENCES `student_advance_payments` (`id`) ON DELETE CASCADE;

-- Update any existing records to have default values
UPDATE `advance_payment_usage` SET `is_reverted` = 'no' WHERE `is_reverted` IS NULL;

-- Show table structures for verification
-- DESCRIBE `student_advance_payments`;
-- DESCRIBE `advance_payment_usage`;
