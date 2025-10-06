<?php
// Setup sample menu data for testing

echo "<h1>Setup Sample Menu Data</h1>";

$mysqli = new mysqli("localhost", "root", "", "school");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "<p>This script will create sample menu data if tables are missing or empty.</p>";

$confirm = $_GET['confirm'] ?? '';
if ($confirm !== 'yes') {
    echo "<p style='color: orange;'><strong>Warning:</strong> This will modify your database. Make sure to backup first.</p>";
    echo "<p><a href='?confirm=yes' style='background: #007cba; color: white; padding: 10px; text-decoration: none;'>Proceed with Setup</a></p>";
    echo "<p><a href='check_menu_tables.php'>← Back to Table Check</a></p>";
    exit;
}

echo "<h2>Setting up sample data...</h2>";

// Create permission groups if missing
echo "<h3>1. Permission Groups:</h3>";
$groups_data = [
    ['name' => 'Academic', 'short_code' => 'academic', 'is_active' => 1],
    ['name' => 'Student Information', 'short_code' => 'student_information', 'is_active' => 1],
    ['name' => 'Attendance', 'short_code' => 'attendance', 'is_active' => 1],
    ['name' => 'Finance', 'short_code' => 'finance', 'is_active' => 1],
    ['name' => 'Reports', 'short_code' => 'reports', 'is_active' => 1],
    ['name' => 'Communication', 'short_code' => 'communication', 'is_active' => 1],
    ['name' => 'System Settings', 'short_code' => 'system_settings', 'is_active' => 1]
];

foreach ($groups_data as $group) {
    // Check if exists
    $check = $mysqli->prepare("SELECT id FROM permission_group WHERE short_code = ?");
    $check->bind_param("s", $group['short_code']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        $insert = $mysqli->prepare("INSERT INTO permission_group (name, short_code, is_active) VALUES (?, ?, ?)");
        $insert->bind_param("ssi", $group['name'], $group['short_code'], $group['is_active']);
        if ($insert->execute()) {
            echo "<p>✓ Created permission group: {$group['name']}</p>";
        } else {
            echo "<p>✗ Failed to create: {$group['name']} - " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p>- Permission group exists: {$group['name']}</p>";
    }
}

// Create sample sidebar menus
echo "<h3>2. Sidebar Menus:</h3>";

// Get permission group IDs
$group_ids = [];
$group_result = $mysqli->query("SELECT id, short_code FROM permission_group");
while ($row = $group_result->fetch_assoc()) {
    $group_ids[$row['short_code']] = $row['id'];
}

$menus_data = [
    [
        'menu' => 'Dashboard',
        'icon' => 'fa fa-dashboard',
        'level' => 1,
        'is_active' => 1,
        'sidebar_display' => 1,
        'permission_group_id' => null,
        'lang_key' => 'dashboard',
        'activate_menu' => 'dashboard'
    ],
    [
        'menu' => 'Academics',
        'icon' => 'fa fa-graduation-cap',
        'level' => 2,
        'is_active' => 1,
        'sidebar_display' => 1,
        'permission_group_id' => $group_ids['academic'] ?? null,
        'lang_key' => 'academics',
        'activate_menu' => 'academics'
    ],
    [
        'menu' => 'Student Information',
        'icon' => 'fa fa-users',
        'level' => 3,
        'is_active' => 1,
        'sidebar_display' => 1,
        'permission_group_id' => $group_ids['student_information'] ?? null,
        'lang_key' => 'student_information',
        'activate_menu' => 'student'
    ],
    [
        'menu' => 'Attendance',
        'icon' => 'fa fa-calendar-check-o',
        'level' => 4,
        'is_active' => 1,
        'sidebar_display' => 1,
        'permission_group_id' => $group_ids['attendance'] ?? null,
        'lang_key' => 'attendance',
        'activate_menu' => 'attendance'
    ],
    [
        'menu' => 'Reports',
        'icon' => 'fa fa-bar-chart',
        'level' => 5,
        'is_active' => 1,
        'sidebar_display' => 1,
        'permission_group_id' => $group_ids['reports'] ?? null,
        'lang_key' => 'reports',
        'activate_menu' => 'reports'
    ]
];

foreach ($menus_data as $menu) {
    // Check if exists
    $check = $mysqli->prepare("SELECT id FROM sidebar_menus WHERE menu = ?");
    $check->bind_param("s", $menu['menu']);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        $insert = $mysqli->prepare("INSERT INTO sidebar_menus (menu, icon, level, is_active, sidebar_display, permission_group_id, lang_key, activate_menu) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssiiiiss", 
            $menu['menu'], 
            $menu['icon'], 
            $menu['level'], 
            $menu['is_active'], 
            $menu['sidebar_display'], 
            $menu['permission_group_id'], 
            $menu['lang_key'], 
            $menu['activate_menu']
        );
        if ($insert->execute()) {
            echo "<p>✓ Created menu: {$menu['menu']}</p>";
        } else {
            echo "<p>✗ Failed to create menu: {$menu['menu']} - " . $mysqli->error . "</p>";
        }
    } else {
        echo "<p>- Menu exists: {$menu['menu']}</p>";
    }
}

// Create sample sub-menus
echo "<h3>3. Sub-menus:</h3>";

// Get menu IDs
$menu_ids = [];
$menu_result = $mysqli->query("SELECT id, menu FROM sidebar_menus");
while ($row = $menu_result->fetch_assoc()) {
    $menu_ids[$row['menu']] = $row['id'];
}

$submenus_data = [
    [
        'sidebar_menu_id' => $menu_ids['Academics'] ?? null,
        'menu' => 'Class Timetable',
        'key' => 'class_timetable',
        'url' => 'admin/timetable/classreport',
        'is_active' => 1,
        'level' => 1,
        'permission_group_id' => $group_ids['academic'] ?? null,
        'lang_key' => 'class_timetable'
    ],
    [
        'sidebar_menu_id' => $menu_ids['Academics'] ?? null,
        'menu' => 'Teachers Timetable',
        'key' => 'teachers_timetable',
        'url' => 'admin/timetable/teacherreport',
        'is_active' => 1,
        'level' => 2,
        'permission_group_id' => $group_ids['academic'] ?? null,
        'lang_key' => 'teachers_timetable'
    ],
    [
        'sidebar_menu_id' => $menu_ids['Student Information'] ?? null,
        'menu' => 'Student Details',
        'key' => 'student_details',
        'url' => 'admin/student/search',
        'is_active' => 1,
        'level' => 1,
        'permission_group_id' => $group_ids['student_information'] ?? null,
        'lang_key' => 'student_details'
    ],
    [
        'sidebar_menu_id' => $menu_ids['Attendance'] ?? null,
        'menu' => 'Student Attendance',
        'key' => 'student_attendance',
        'url' => 'admin/admin/search',
        'is_active' => 1,
        'level' => 1,
        'permission_group_id' => $group_ids['attendance'] ?? null,
        'lang_key' => 'student_attendance'
    ]
];

foreach ($submenus_data as $submenu) {
    if ($submenu['sidebar_menu_id']) {
        // Check if exists
        $check = $mysqli->prepare("SELECT id FROM sidebar_sub_menus WHERE sidebar_menu_id = ? AND menu = ?");
        $check->bind_param("is", $submenu['sidebar_menu_id'], $submenu['menu']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows == 0) {
            $insert = $mysqli->prepare("INSERT INTO sidebar_sub_menus (sidebar_menu_id, menu, `key`, url, is_active, level, permission_group_id, lang_key) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("isssiiis", 
                $submenu['sidebar_menu_id'],
                $submenu['menu'], 
                $submenu['key'], 
                $submenu['url'], 
                $submenu['is_active'], 
                $submenu['level'], 
                $submenu['permission_group_id'], 
                $submenu['lang_key']
            );
            if ($insert->execute()) {
                echo "<p>✓ Created submenu: {$submenu['menu']}</p>";
            } else {
                echo "<p>✗ Failed to create submenu: {$submenu['menu']} - " . $mysqli->error . "</p>";
            }
        } else {
            echo "<p>- Submenu exists: {$submenu['menu']}</p>";
        }
    }
}

// Ensure there's a default role and assign to staff if needed
echo "<h3>4. Roles and Staff Assignment:</h3>";

// Check if Teacher role exists
$role_check = $mysqli->query("SELECT id FROM roles WHERE name = 'Teacher'");
if ($role_check->num_rows == 0) {
    $create_role = $mysqli->query("INSERT INTO roles (name, slug, is_superadmin, is_active, created_at) VALUES ('Teacher', 'teacher', 0, 1, NOW())");
    if ($create_role) {
        echo "<p>✓ Created Teacher role</p>";
    } else {
        echo "<p>✗ Failed to create Teacher role: " . $mysqli->error . "</p>";
    }
}

// Get Teacher role ID
$teacher_role = $mysqli->query("SELECT id FROM roles WHERE name = 'Teacher'")->fetch_assoc();
$teacher_role_id = $teacher_role['id'] ?? null;

if ($teacher_role_id) {
    // Assign Teacher role to staff members who don't have a role
    $staff_without_role = $mysqli->query("
        SELECT s.id 
        FROM staff s 
        LEFT JOIN staff_roles sr ON sr.staff_id = s.id 
        WHERE sr.staff_id IS NULL AND s.is_active = 1 
        LIMIT 5
    ");
    
    while ($staff = $staff_without_role->fetch_assoc()) {
        $assign_role = $mysqli->prepare("INSERT INTO staff_roles (staff_id, role_id, is_active, created_at) VALUES (?, ?, 1, NOW())");
        $assign_role->bind_param("ii", $staff['id'], $teacher_role_id);
        if ($assign_role->execute()) {
            echo "<p>✓ Assigned Teacher role to staff ID: {$staff['id']}</p>";
        }
    }
}

// Create sample permission categories and role permissions
echo "<h3>5. Permission Categories:</h3>";

foreach ($group_ids as $group_code => $group_id) {
    // Check if permission category exists for this group
    $cat_check = $mysqli->prepare("SELECT id FROM permission_category WHERE perm_group_id = ? AND short_code = ?");
    $cat_name = $group_code . '_view';
    $cat_check->bind_param("is", $group_id, $cat_name);
    $cat_check->execute();
    
    if ($cat_check->get_result()->num_rows == 0) {
        $create_cat = $mysqli->prepare("INSERT INTO permission_category (perm_group_id, name, short_code, enable_view, enable_add, enable_edit, enable_delete, created_at) VALUES (?, ?, ?, 1, 1, 1, 1, NOW())");
        $friendly_name = ucwords(str_replace('_', ' ', $group_code)) . ' Management';
        $create_cat->bind_param("iss", $group_id, $friendly_name, $cat_name);
        if ($create_cat->execute()) {
            echo "<p>✓ Created permission category: {$friendly_name}</p>";
            
            // Create role permission for Teacher role
            if ($teacher_role_id) {
                $cat_id = $mysqli->insert_id;
                $create_role_perm = $mysqli->prepare("INSERT INTO roles_permissions (role_id, perm_cat_id, can_view, can_add, can_edit, can_delete, created_at) VALUES (?, ?, 1, 0, 0, 0, NOW())");
                $create_role_perm->bind_param("ii", $teacher_role_id, $cat_id);
                $create_role_perm->execute();
                echo "<p>✓ Assigned view permission to Teacher role</p>";
            }
        }
    }
}

echo "<h2>Setup Complete!</h2>";
echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; color: #155724;'>";
echo "<p><strong>Sample data has been created. You can now test the menu API.</strong></p>";
echo "</div>";

$mysqli->close();

echo "<p><a href='comprehensive_menu_test.php'>Test Menu API</a></p>";
echo "<p><a href='check_menu_tables.php'>Check Tables Again</a></p>";
?>