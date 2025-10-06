-- FINAL SOLUTION: Remove duplicate hostel fee menu entries
-- This script identifies and removes all duplicate hostel fee menu items from the database

-- ========================================
-- STEP 1: Show current duplicate entries
-- ========================================

SELECT '=== CURRENT DUPLICATE HOSTEL FEE MENU ENTRIES ===' as Info;

-- Show all hostel fee submenus currently in database
SELECT 
    ssm.id,
    ssm.menu,
    ssm.lang_key,
    ssm.url,
    ssm.level,
    ssm.access_permissions,
    ssm.is_active,
    ssm.created_at
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel' 
AND ssm.lang_key IN ('hostel_fees_master', 'assign_hostel_fees')
ORDER BY ssm.lang_key, ssm.id;

-- ========================================
-- STEP 2: Remove ALL hostel fee menu duplicates
-- ========================================

-- Delete all hostel fee submenus (we'll keep only the most recent ones)
DELETE ssm FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel' 
AND ssm.lang_key IN ('hostel_fees_master', 'assign_hostel_fees');

-- ========================================
-- STEP 3: Insert clean, standardized entries
-- ========================================

-- Get the hostel menu ID
SET @hostel_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'hostel' LIMIT 1);

-- Insert clean hostel fee menu entries with proper format
INSERT INTO `sidebar_sub_menus` (`sidebar_menu_id`, `menu`, `key`, `lang_key`, `url`, `level`, `access_permissions`, `permission_group_id`, `activate_controller`, `activate_methods`, `addon_permission`, `is_active`, `created_at`) VALUES
(@hostel_menu_id, 'Hostel Fees Master', 'hostel_fees_master', 'hostel_fees_master', 'admin/hostel/feemaster', 4, '(\'hostel_fees_master\', \'can_view\')', NULL, 'hostel', 'feemaster', '', 1, NOW()),
(@hostel_menu_id, 'Assign Hostel Fees', 'assign_hostel_fees', 'assign_hostel_fees', 'admin/hostel/assignhostelfee', 5, '(\'assign_hostel_fees\', \'can_view\')', NULL, 'hostel', 'assignhostelfee,assignhostelfeestudent,assignhostelfeepost', '', 1, NOW());

-- ========================================
-- STEP 4: Clean up permission categories (remove duplicates)
-- ========================================

-- Remove duplicate permission categories, keeping only the highest ID ones
DELETE pc1 FROM permission_category pc1
INNER JOIN permission_category pc2
WHERE pc1.id < pc2.id
AND pc1.short_code = pc2.short_code
AND pc1.short_code IN ('hostel_fees_master', 'assign_hostel_fees');

-- ========================================
-- STEP 5: Clean up roles_permissions (remove duplicates)
-- ========================================

-- Remove duplicate role permissions
DELETE rp1 FROM roles_permissions rp1
INNER JOIN roles_permissions rp2
WHERE rp1.id < rp2.id
AND rp1.role_id = rp2.role_id
AND rp1.perm_cat_id = rp2.perm_cat_id;

-- ========================================
-- STEP 6: Ensure Super Admin has correct permissions
-- ========================================

-- Get permission category IDs
SET @hostel_fees_master_id = (SELECT id FROM permission_category WHERE short_code = 'hostel_fees_master' LIMIT 1);
SET @assign_hostel_fees_id = (SELECT id FROM permission_category WHERE short_code = 'assign_hostel_fees' LIMIT 1);

-- Ensure Super Admin (role_id = 1) has permissions
INSERT IGNORE INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete)
VALUES 
(1, @hostel_fees_master_id, 1, 1, 1, 1),
(1, @assign_hostel_fees_id, 1, 1, 1, 1);

-- ========================================
-- STEP 7: Update main hostel menu permissions
-- ========================================

UPDATE sidebar_menus 
SET access_permissions = '(\'hostel_rooms\', \'can_view\') || (\'room_type\', \'can_view\') || (\'hostel\', \'can_view\') || (\'hostel_fees_master\', \'can_view\') || (\'assign_hostel_fees\', \'can_view\')'
WHERE lang_key = 'hostel';

-- ========================================
-- STEP 8: Final verification
-- ========================================

SELECT '=== CLEANUP COMPLETE - FINAL VERIFICATION ===' as Info;

-- Show final hostel menu structure
SELECT 'Final Hostel Menu Structure:' as description;
SELECT
    sm.id as menu_id,
    sm.menu as main_menu,
    sm.lang_key as main_menu_key,
    ssm.id as submenu_id,
    ssm.menu as submenu,
    ssm.lang_key as submenu_key,
    ssm.url as submenu_url,
    ssm.level as submenu_level,
    ssm.is_active as submenu_active
FROM sidebar_menus sm
LEFT JOIN sidebar_sub_menus ssm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel'
ORDER BY ssm.level;

-- Count final entries
SELECT 'Total hostel fee submenus (should be 2):' as description, COUNT(*) as count
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel' AND ssm.lang_key IN ('hostel_fees_master', 'assign_hostel_fees');

-- Show permission categories
SELECT 'Permission Categories:' as description;
SELECT id, name, short_code 
FROM permission_category 
WHERE short_code IN ('hostel_fees_master', 'assign_hostel_fees')
ORDER BY short_code;

SELECT '=== DUPLICATE REMOVAL COMPLETED ===' as Info;
SELECT 'Please clear browser cache and refresh to see the changes.' as Message;
