-- SQL script to clean up duplicate hostel menu entries
-- Run this ONLY after reviewing the results from check_duplicate_menus.sql

-- ========================================
-- STEP 1: Backup existing data (optional but recommended)
-- ========================================

-- CREATE TABLE sidebar_menus_backup AS SELECT * FROM sidebar_menus WHERE lang_key = 'hostel';
-- CREATE TABLE sidebar_sub_menus_backup AS 
-- SELECT ssm.* FROM sidebar_sub_menus ssm 
-- JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id 
-- WHERE sm.lang_key = 'hostel';

-- ========================================
-- STEP 2: Remove duplicate permission categories
-- ========================================

-- Keep only the permission category with the lowest ID for each short_code
DELETE pc1 FROM permission_category pc1
INNER JOIN permission_category pc2
WHERE pc1.id > pc2.id
AND pc1.short_code = pc2.short_code
AND pc1.short_code IN ('hostel_fees_master', 'assign_hostel_fees');

-- ========================================
-- STEP 3: Remove duplicate roles_permissions
-- ========================================

-- Remove duplicate role permissions, keeping only the one with lowest ID
DELETE rp1 FROM roles_permissions rp1
INNER JOIN roles_permissions rp2
WHERE rp1.id > rp2.id
AND rp1.role_id = rp2.role_id
AND rp1.perm_cat_id = rp2.perm_cat_id;

-- ========================================
-- STEP 4: Remove duplicate sidebar submenus
-- ========================================

-- Remove duplicate hostel submenus, keeping only the one with lowest ID
DELETE ssm1 FROM sidebar_sub_menus ssm1
INNER JOIN sidebar_sub_menus ssm2
WHERE ssm1.id > ssm2.id
AND ssm1.lang_key = ssm2.lang_key
AND ssm1.sidebar_menu_id = ssm2.sidebar_menu_id
AND ssm1.sidebar_menu_id IN (
    SELECT id FROM sidebar_menus WHERE lang_key = 'hostel'
);

-- ========================================
-- STEP 5: Remove duplicate main menus (if any)
-- ========================================

-- Remove duplicate main hostel menus, keeping only the one with lowest ID
DELETE sm1 FROM sidebar_menus sm1
INNER JOIN sidebar_menus sm2
WHERE sm1.id > sm2.id
AND sm1.lang_key = sm2.lang_key
AND sm1.lang_key = 'hostel';

-- ========================================
-- STEP 6: Ensure correct menu structure
-- ========================================

-- Get the hostel menu ID
SET @hostel_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'hostel' LIMIT 1);

-- Update any orphaned submenus to point to the correct hostel menu
UPDATE sidebar_sub_menus 
SET sidebar_menu_id = @hostel_menu_id
WHERE lang_key IN ('hostel_fees_master', 'assign_hostel_fees')
AND sidebar_menu_id != @hostel_menu_id;

-- ========================================
-- STEP 7: Ensure proper levels for submenus
-- ========================================

-- Update submenu levels to ensure proper ordering
UPDATE sidebar_sub_menus 
SET level = 1 
WHERE sidebar_menu_id = @hostel_menu_id AND lang_key = 'hostel_rooms';

UPDATE sidebar_sub_menus 
SET level = 2 
WHERE sidebar_menu_id = @hostel_menu_id AND lang_key = 'room_type';

UPDATE sidebar_sub_menus 
SET level = 3 
WHERE sidebar_menu_id = @hostel_menu_id AND lang_key = 'hostel';

UPDATE sidebar_sub_menus 
SET level = 4 
WHERE sidebar_menu_id = @hostel_menu_id AND lang_key = 'hostel_fees_master';

UPDATE sidebar_sub_menus 
SET level = 5 
WHERE sidebar_menu_id = @hostel_menu_id AND lang_key = 'assign_hostel_fees';

-- ========================================
-- STEP 8: Ensure all submenus are active
-- ========================================

UPDATE sidebar_sub_menus 
SET is_active = 1
WHERE sidebar_menu_id = @hostel_menu_id;

-- ========================================
-- STEP 9: Update main menu access permissions
-- ========================================

UPDATE sidebar_menus 
SET access_permissions = '(\'hostel_rooms\', \'can_view\') || (\'room_type\', \'can_view\') || (\'hostel\', \'can_view\') || (\'hostel_fees_master\', \'can_view\') || (\'assign_hostel_fees\', \'can_view\')'
WHERE lang_key = 'hostel';

-- ========================================
-- STEP 10: Verification queries
-- ========================================

SELECT '=== CLEANUP COMPLETE - VERIFICATION ===' as Info;

-- Show final hostel menu structure
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
ORDER BY sm.id, ssm.level;

-- Count final entries
SELECT 'Hostel main menus:' as description, COUNT(*) as count
FROM sidebar_menus WHERE lang_key = 'hostel'
UNION ALL
SELECT 'Hostel submenus:' as description, COUNT(*) as count
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel';

SELECT '=== CLEANUP SCRIPT COMPLETED ===' as Info;
