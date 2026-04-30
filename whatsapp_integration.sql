-- =============================================================
-- WHATSAPP CLOUD MESSAGING INTEGRATION
-- Run this SQL to add WhatsApp support to your existing database
-- =============================================================

-- 1. Add WhatsApp columns to notification_setting table
ALTER TABLE `notification_setting`
  ADD COLUMN `is_whatsapp` int(11) DEFAULT 0 AFTER `is_sms`,
  ADD COLUMN `display_whatsapp` int(11) DEFAULT 1 AFTER `display_sms`,
  ADD COLUMN `whatsapp_template_id` varchar(255) NOT NULL DEFAULT '' AFTER `template_id`;

-- 2. Create whatsapp_config table (stores API credentials for Meta & Twilio)
CREATE TABLE IF NOT EXISTS `whatsapp_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provider` enum('meta','twilio') NOT NULL DEFAULT 'meta',
  `phone_number_id` varchar(100) NOT NULL DEFAULT '',
  `business_account_id` varchar(100) NOT NULL DEFAULT '',
  `access_token` varchar(500) NOT NULL DEFAULT '',
  `verify_token` varchar(255) NOT NULL DEFAULT '',
  `api_version` varchar(20) NOT NULL DEFAULT 'v21.0',
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `webhook_url` varchar(500) NOT NULL DEFAULT '',
  `twilio_account_sid` varchar(200) NOT NULL DEFAULT '',
  `twilio_auth_token` varchar(500) NOT NULL DEFAULT '',
  `twilio_phone_number` varchar(30) NOT NULL DEFAULT '',
  `twilio_is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Create whatsapp_messages table (stores sent/received messages)
CREATE TABLE IF NOT EXISTS `whatsapp_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_type` enum('outgoing','incoming') NOT NULL DEFAULT 'outgoing',
  `event_type` varchar(100) DEFAULT NULL,
  `recipient_phone` varchar(20) NOT NULL DEFAULT '',
  `recipient_name` varchar(255) DEFAULT NULL,
  `template_name` varchar(255) DEFAULT NULL,
  `template_body` text DEFAULT NULL,
  `template_language` varchar(20) DEFAULT 'en_US',
  `message_json` longtext DEFAULT NULL,
  `whatsapp_message_id` varchar(100) DEFAULT NULL,
  `whatsapp_conversation_id` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `provider` varchar(20) DEFAULT 'meta',
  `sent_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_recipient` (`recipient_phone`),
  KEY `idx_event_type` (`event_type`),
  KEY `idx_whatsapp_message_id` (`whatsapp_message_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_provider` (`provider`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4;

-- 4. Create whatsapp_template_language table (template language mapping)
CREATE TABLE IF NOT EXISTS `whatsapp_template_language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_code` varchar(20) NOT NULL DEFAULT 'en_US',
  `language_name` varchar(100) NOT NULL DEFAULT 'English (US)',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_language_code` (`language_code`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4;

-- 5. Insert default template languages
INSERT INTO `whatsapp_template_language` (`language_code`, `language_name`, `is_active`) VALUES
('en_US', 'English (US)', 1),
('en_GB', 'English (UK)', 1),
('es', 'Spanish', 1),
('es_ES', 'Spanish (Spain)', 1),
('fr', 'French', 1),
('fr_FR', 'French (France)', 1),
('de', 'German', 1),
('hi', 'Hindi', 1),
('ar', 'Arabic', 1),
('pt_BR', 'Portuguese (Brazil)', 1),
('mr', 'Marathi', 1),
('ur', 'Urdu', 1);

-- 6. Update existing notification_setting records to show WhatsApp option
UPDATE `notification_setting` SET `display_whatsapp` = 1 WHERE `display_whatsapp` IS NULL OR `display_whatsapp` = 0;

-- 7. Add WhatsApp chat widget columns to sch_settings table
ALTER TABLE `sch_settings`
  ADD COLUMN `front_side_whatsapp` int NOT NULL DEFAULT 0,
  ADD COLUMN `front_side_whatsapp_mobile` varchar(50) DEFAULT NULL,
  ADD COLUMN `front_side_whatsapp_from` time DEFAULT NULL,
  ADD COLUMN `front_side_whatsapp_to` time DEFAULT NULL,
  ADD COLUMN `admin_panel_whatsapp` int NOT NULL DEFAULT 0,
  ADD COLUMN `admin_panel_whatsapp_mobile` varchar(50) DEFAULT NULL,
  ADD COLUMN `admin_panel_whatsapp_from` time DEFAULT NULL,
  ADD COLUMN `admin_panel_whatsapp_to` time DEFAULT NULL,
  ADD COLUMN `student_panel_whatsapp` int NOT NULL DEFAULT 0,
  ADD COLUMN `student_panel_whatsapp_mobile` varchar(50) DEFAULT NULL,
  ADD COLUMN `student_panel_whatsapp_from` time DEFAULT NULL,
  ADD COLUMN `student_panel_whatsapp_to` time DEFAULT NULL;

-- 8. Add permission_category for WhatsApp Settings under System Settings
INSERT INTO `permission_category` (`perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`) VALUES
(15, 'WhatsApp Setting', 'whatsapp_setting', 1, 0, 1, 0);

-- 9. Add sidebar submenu under System Settings for WhatsApp Messaging
INSERT INTO `sidebar_sub_menus` (`sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `is_active`) VALUES
(27, 'whatsapp_messaging', 'whatsapp_messaging', 'whatsapp_messaging', 'admin/whatsappconfig', 23, '(''whatsapp_setting'', ''can_view'')', NULL, 'whatsappconfig', 'index,messages,testconnection,save,savetwilio,testtwilio,webhook,webhook_receive,getStats', 1);
