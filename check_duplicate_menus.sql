-- SQL script to check for and clean up duplicate sidebar menu entries
-- This script identifies and removes duplicate hostel menu items

-- ========================================
-- STEP 1: Check for duplicate main menus
-- ========================================

SELECT '=== CHECKING FOR DUPLICATE MAIN MENUS ===' as Info;

SELECT 
    lang_key,
    COUNT(*) as count,
    GROUP_CONCAT(id) as menu_ids,
    GROUP_CONCAT(menu) as menu_names
FROM sidebar_menus 
WHERE lang_key = 'hostel'
GROUP BY lang_key
HAVING COUNT(*) > 1;

-- ========================================
-- STEP 2: Check for duplicate submenus
-- ========================================

SELECT '=== CHECKING FOR DUPLICATE SUBMENUS ===' as Info;

SELECT 
    ssm.lang_key,
    ssm.sidebar_menu_id,
    COUNT(*) as count,
    GROUP_CONCAT(ssm.id) as submenu_ids,
    GROUP_CONCAT(ssm.menu) as submenu_names,
    GROUP_CONCAT(ssm.url) as urls
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel'
GROUP BY ssm.lang_key, ssm.sidebar_menu_id
HAVING COUNT(*) > 1;

-- ========================================
-- STEP 3: Show all hostel-related menu structure
-- ========================================

SELECT '=== CURRENT HOSTEL MENU STRUCTURE ===' as Info;

SELECT
    sm.id as menu_id,
    sm.menu as main_menu,
    sm.lang_key as main_menu_key,
    sm.permission_group_id,
    sm.is_active as menu_active,
    ssm.id as submenu_id,
    ssm.menu as submenu,
    ssm.lang_key as submenu_key,
    ssm.url as submenu_url,
    ssm.level as submenu_level,
    ssm.is_active as submenu_active,
    ssm.activate_controller,
    ssm.activate_methods,
    ssm.access_permissions
FROM sidebar_menus sm
LEFT JOIN sidebar_sub_menus ssm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel'
ORDER BY sm.id, ssm.level, ssm.id;

-- ========================================
-- STEP 4: Clean up duplicate submenus (COMMENTED OUT FOR SAFETY)
-- ========================================

-- UNCOMMENT THE FOLLOWING LINES ONLY AFTER REVIEWING THE RESULTS ABOVE

/*
-- Remove duplicate hostel fee submenus, keeping only the ones with lowest IDs
DELETE ssm1 FROM sidebar_sub_menus ssm1
INNER JOIN sidebar_sub_menus ssm2
WHERE ssm1.id > ssm2.id
AND ssm1.lang_key = ssm2.lang_key
AND ssm1.sidebar_menu_id = ssm2.sidebar_menu_id
AND ssm1.sidebar_menu_id IN (
    SELECT id FROM sidebar_menus WHERE lang_key = 'hostel'
);
*/

-- ========================================
-- STEP 5: Check permission categories for duplicates
-- ========================================

SELECT '=== CHECKING PERMISSION CATEGORIES ===' as Info;

SELECT 
    short_code,
    COUNT(*) as count,
    GROUP_CONCAT(id) as category_ids,
    GROUP_CONCAT(name) as category_names
FROM permission_category 
WHERE short_code IN ('hostel_fees_master', 'assign_hostel_fees')
GROUP BY short_code
HAVING COUNT(*) > 1;

-- ========================================
-- STEP 6: Check roles_permissions for duplicates
-- ========================================

SELECT '=== CHECKING ROLES PERMISSIONS ===' as Info;

SELECT 
    rp.role_id,
    pc.short_code,
    COUNT(*) as count,
    GROUP_CONCAT(rp.id) as permission_ids
FROM roles_permissions rp
JOIN permission_category pc ON pc.id = rp.perm_cat_id
WHERE pc.short_code IN ('hostel_fees_master', 'assign_hostel_fees')
GROUP BY rp.role_id, pc.short_code
HAVING COUNT(*) > 1;

-- ========================================
-- STEP 7: Final verification query
-- ========================================

SELECT '=== FINAL VERIFICATION ===' as Info;

SELECT 'Total hostel main menus:' as description, COUNT(*) as count
FROM sidebar_menus 
WHERE lang_key = 'hostel'

UNION ALL

SELECT 'Total hostel submenus:' as description, COUNT(*) as count
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel'

UNION ALL

SELECT 'Active hostel submenus:' as description, COUNT(*) as count
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'hostel' AND ssm.is_active = 1;
