-- COMPREHENSIVE FIX FOR DUPLICATE HOSTEL MENU ENTRIES
-- This script will clean up all duplicates and ensure only one set of hostel fee menus exists

-- ========================================
-- STEP 1: Show current state before cleanup
-- ========================================

SELECT '=== BEFORE CLEANUP - CURRENT STATE ===' as Info;

-- Show all hostel-related permission categories
SELECT 'Permission Categories:' as type, id, name, short_code 
FROM permission_category 
WHERE short_code IN ('hostel_fees_master', 'assign_hostel_fees')
ORDER BY short_code, id;

-- Show all hostel submenus
SELECT 'Submenus:' as type, ssm.id, ssm.menu, ssm.lang_key, ssm.url, ssm.level
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel' AND ssm.lang_key IN ('hostel_fees_master', 'assign_hostel_fees')
ORDER BY ssm.lang_key, ssm.id;

-- ========================================
-- STEP 2: Remove ALL duplicate permission categories
-- ========================================

-- Delete all hostel fee permission categories except the ones with highest IDs (most recent)
DELETE FROM permission_category 
WHERE short_code = 'hostel_fees_master' 
AND id NOT IN (
    SELECT max_id FROM (
        SELECT MAX(id) as max_id 
        FROM permission_category 
        WHERE short_code = 'hostel_fees_master'
    ) as temp
);

DELETE FROM permission_category 
WHERE short_code = 'assign_hostel_fees' 
AND id NOT IN (
    SELECT max_id FROM (
        SELECT MAX(id) as max_id 
        FROM permission_category 
        WHERE short_code = 'assign_hostel_fees'
    ) as temp
);

-- ========================================
-- STEP 3: Clean up orphaned roles_permissions
-- ========================================

-- Remove roles_permissions entries that reference deleted permission categories
DELETE FROM roles_permissions 
WHERE perm_cat_id NOT IN (SELECT id FROM permission_category);

-- ========================================
-- STEP 4: Remove ALL duplicate sidebar submenus
-- ========================================

-- Get the hostel menu ID
SET @hostel_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'hostel' LIMIT 1);

-- Delete all hostel fee submenus except the ones with highest IDs (most recent)
DELETE FROM sidebar_sub_menus 
WHERE lang_key = 'hostel_fees_master' 
AND sidebar_menu_id = @hostel_menu_id
AND id NOT IN (
    SELECT max_id FROM (
        SELECT MAX(id) as max_id 
        FROM sidebar_sub_menus 
        WHERE lang_key = 'hostel_fees_master' 
        AND sidebar_menu_id = @hostel_menu_id
    ) as temp
);

DELETE FROM sidebar_sub_menus 
WHERE lang_key = 'assign_hostel_fees' 
AND sidebar_menu_id = @hostel_menu_id
AND id NOT IN (
    SELECT max_id FROM (
        SELECT MAX(id) as max_id 
        FROM sidebar_sub_menus 
        WHERE lang_key = 'assign_hostel_fees' 
        AND sidebar_menu_id = @hostel_menu_id
    ) as temp
);

-- ========================================
-- STEP 5: Ensure proper roles_permissions exist
-- ========================================

-- Get the remaining permission category IDs
SET @hostel_fees_master_id = (SELECT id FROM permission_category WHERE short_code = 'hostel_fees_master' LIMIT 1);
SET @assign_hostel_fees_id = (SELECT id FROM permission_category WHERE short_code = 'assign_hostel_fees' LIMIT 1);

-- Ensure Super Admin (role_id = 1) has permissions for hostel fees
INSERT IGNORE INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete)
VALUES 
(1, @hostel_fees_master_id, 1, 1, 1, 1),
(1, @assign_hostel_fees_id, 1, 1, 1, 1);

-- ========================================
-- STEP 6: Update main hostel menu access permissions
-- ========================================

UPDATE sidebar_menus 
SET access_permissions = '(\'hostel_rooms\', \'can_view\') || (\'room_type\', \'can_view\') || (\'hostel\', \'can_view\') || (\'hostel_fees_master\', \'can_view\') || (\'assign_hostel_fees\', \'can_view\')'
WHERE lang_key = 'hostel';

-- ========================================
-- STEP 7: Ensure proper submenu properties
-- ========================================

-- Update hostel fees master submenu
UPDATE sidebar_sub_menus 
SET 
    menu = 'hostel_fees_master',
    url = 'admin/hostel/feemaster',
    level = 4,
    access_permissions = '(\'hostel_fees_master\', \'can_view\')',
    activate_controller = 'hostel',
    activate_methods = 'feemaster',
    is_active = 1
WHERE lang_key = 'hostel_fees_master' 
AND sidebar_menu_id = @hostel_menu_id;

-- Update assign hostel fees submenu
UPDATE sidebar_sub_menus 
SET 
    menu = 'assign_hostel_fees',
    url = 'admin/hostel/assignhostelfee',
    level = 5,
    access_permissions = '(\'assign_hostel_fees\', \'can_view\')',
    activate_controller = 'hostel',
    activate_methods = 'assignhostelfee,assignhostelfeestudent,assignhostelfeepost',
    is_active = 1
WHERE lang_key = 'assign_hostel_fees' 
AND sidebar_menu_id = @hostel_menu_id;

-- ========================================
-- STEP 8: Final verification
-- ========================================

SELECT '=== AFTER CLEANUP - FINAL STATE ===' as Info;

-- Show remaining permission categories
SELECT 'Final Permission Categories:' as type, id, name, short_code 
FROM permission_category 
WHERE short_code IN ('hostel_fees_master', 'assign_hostel_fees')
ORDER BY short_code;

-- Show remaining submenus
SELECT 'Final Submenus:' as type, ssm.id, ssm.menu, ssm.lang_key, ssm.url, ssm.level, ssm.is_active
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel' AND ssm.lang_key IN ('hostel_fees_master', 'assign_hostel_fees')
ORDER BY ssm.level;

-- Show roles permissions
SELECT 'Final Roles Permissions:' as type, rp.id, rp.role_id, pc.short_code, rp.can_view, rp.can_add, rp.can_edit, rp.can_delete
FROM roles_permissions rp
JOIN permission_category pc ON pc.id = rp.perm_cat_id
WHERE pc.short_code IN ('hostel_fees_master', 'assign_hostel_fees')
ORDER BY pc.short_code;

-- Show complete hostel menu structure
SELECT 'Complete Hostel Menu:' as type, sm.id as menu_id, sm.menu as main_menu, 
       ssm.id as submenu_id, ssm.menu as submenu, ssm.lang_key, ssm.url, ssm.level
FROM sidebar_menus sm
LEFT JOIN sidebar_sub_menus ssm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel'
ORDER BY ssm.level;

SELECT '=== DUPLICATE CLEANUP COMPLETED ===' as Info;
SELECT 'Please refresh your browser and clear cache to see the changes.' as Message;
