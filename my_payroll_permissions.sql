-- ============================================
-- MY PAYROLL (Staff Self-Service) PERMISSION + MENU
-- Date: 2026-03-31
-- ============================================

-- 1. Add permission category "My Payroll" (view own payslips)
INSERT INTO `permission_category` (`perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`) VALUES
(NULL, 'My Payroll', 'my_payroll', 1, 0, 0, 0);

SET @pc_id = LAST_INSERT_ID();

-- 2. Grant can_view to all relevant roles:
-- Role 1 = Admin
INSERT IGNORE INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`) VALUES
(1, @pc_id, 1, 0, 0, 0);

-- Role 2 = Teacher/Staff (view own payslips)
INSERT IGNORE INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`) VALUES
(2, @pc_id, 1, 0, 0, 0);

-- Role 3 = Accountant
INSERT IGNORE INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`) VALUES
(3, @pc_id, 1, 0, 0, 0);

-- Role 7 = Super Admin
INSERT IGNORE INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`) VALUES
(7, @pc_id, 1, 0, 0, 0);

-- Role 8 = Operator
INSERT IGNORE INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`) VALUES
(8, @pc_id, 1, 0, 0, 0);

-- 3. Add sidebar sub-menu "My Payroll" under HR (sidebar_menu_id = 15)
INSERT INTO `sidebar_sub_menus` (`sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`) VALUES
(15, 'My Payroll', 'my_payroll', 'my_payroll', 'admin/payroll/myPayroll', 0, '("my_payroll", "can_view")', NULL, 'payroll', 'myPayroll', '', 1);

-- ============================================
-- ADDITIONAL: Let staff see payroll_settings (view-only)
-- ============================================
INSERT INTO `permission_category` (`perm_group_id`, `name`, `short_code`, `enable_view`, `enable_add`, `enable_edit`, `enable_delete`) VALUES
(NULL, 'View Payroll Settings', 'view_payroll_settings', 1, 0, 0, 0);

SET @vps_id = LAST_INSERT_ID();

-- Role 2 = Teacher/Staff (view-only)
INSERT IGNORE INTO `roles_permissions` (`role_id`, `perm_cat_id`, `can_view`, `can_add`, `can_edit`, `can_delete`) VALUES
(2, @vps_id, 1, 0, 0, 0);
