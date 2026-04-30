ALTER TABLE `print_headerfooter` ADD COLUMN `header_content` TEXT NULL AFTER `header_image`;
ALTER TABLE `print_headerfooter` ADD COLUMN `receipt_template` VARCHAR(50) DEFAULT 'image' AFTER `header_content`;
