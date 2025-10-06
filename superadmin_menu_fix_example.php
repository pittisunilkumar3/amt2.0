<?php
// If needed, this is how to update the Teacher_permission_model getTeacherMenus method
// to ensure superadmin gets ALL menus

// In the getTeacherMenus method, around line 125, add this logic:

public function getTeacherMenus($staff_id)
{
    $role = $this->getTeacherRole($staff_id);
    if (!$role) {
        return array();
    }

    // NEW: If superadmin, return ALL menus without permission checks
    if ($role->is_superadmin == 1) {
        return $this->getAllMenusForSuperadmin();
    }

    // Continue with existing logic for non-superadmin users...
    // (rest of the existing method)
}

// Add this new method to get all menus for superadmin
private function getAllMenusForSuperadmin()
{
    // Get ALL main menu items
    $this->db->select('
        sm.id, sm.menu, sm.icon, sm.activate_menu, sm.lang_key, sm.level,
        sm.access_permissions
    ');
    $this->db->from('sidebar_menus sm');
    $this->db->where('sm.is_active', 1);
    $this->db->where('sm.sidebar_display', 1);
    $this->db->order_by('sm.level');
    $query = $this->db->get();

    $menus = array();
    foreach ($query->result() as $menu) {
        $menu_item = array(
            'id' => $menu->id,
            'menu' => $menu->menu,
            'icon' => $menu->icon,
            'activate_menu' => $menu->activate_menu,
            'lang_key' => $menu->lang_key,
            'level' => $menu->level,
            'submenus' => $this->getAllSubmenuForSuperadmin($menu->id)
        );
        $menus[] = $menu_item;
    }

    return $menus;
}

// Add this method to get all submenus for superadmin
private function getAllSubmenuForSuperadmin($main_menu_id)
{
    $this->db->select('
        ssm.id, ssm.menu, ssm.url, ssm.level, ssm.lang_key
    ');
    $this->db->from('sidebar_sub_menus ssm');
    $this->db->where('ssm.sidebar_menu_id', $main_menu_id);
    $this->db->where('ssm.is_active', 1);
    $this->db->order_by('ssm.level');
    $query = $this->db->get();

    $submenus = array();
    foreach ($query->result() as $submenu) {
        $submenus[] = array(
            'id' => $submenu->id,
            'menu' => $submenu->menu,
            'url' => $submenu->url,
            'level' => $submenu->level,
            'lang_key' => $submenu->lang_key
        );
    }

    return $submenus;
}
?>