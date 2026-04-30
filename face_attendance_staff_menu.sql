-- ============================================
-- ADD STAFF FACE REGISTRATION SUBMENU
-- ============================================

USE amt;

-- Get the existing Face Attendance main menu ID
SET @face_menu_id = (SELECT id FROM sidebar_menus WHERE lang_key = 'face_attendance' LIMIT 1);

-- Add Staff Registration submenu under Face Attendance main menu
INSERT INTO sidebar_sub_menus (
    sidebar_menu_id, 
    menu, 
    `key`, 
    lang_key, 
    url, 
    level, 
    access_permissions, 
    permission_group_id, 
    activate_controller, 
    activate_methods, 
    addon_permission, 
    is_active, 
    created_at
) VALUES (
    @face_menu_id,
    'Staff Registration',
    'face_attendance_staff_registration',
    'face_attendance_staff_registration',
    'admin/face_attendance_register/index',
    3,
    '("face_attendance_register", "can_view")',
    NULL,
    'Face_attendance_register',
    'index,register_staff_face,get_staff_by_role,delete_registration',
    '',
    1,
    NOW()
)
ON DUPLICATE KEY UPDATE 
    menu = VALUES(menu),
    url = VALUES(url),
    is_active = 1;

-- Update the Student Registration submenu URL (now both share same index page with tabs)
UPDATE sidebar_sub_menus 
SET url = 'admin/face_attendance_register/index'
WHERE lang_key = 'face_attendance_student_registration';

-- Verify
SELECT ssm.menu, ssm.url, ssm.level, ssm.is_active
FROM sidebar_sub_menus ssm
JOIN sidebar_menus sm ON sm.id = ssm.sidebar_menu_id
WHERE sm.lang_key = 'face_attendance'
ORDER BY ssm.level;
