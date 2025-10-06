<?php
/**
 * Final API Response Demo
 * Shows actual API response with submenus for Staff ID 6
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Menu API Response - With Submenus</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-left: 4px solid #17a2b8; margin: 20px 0; }
        pre { background: #f8f9fa; padding: 20px; border-radius: 4px; overflow-x: auto; border: 1px solid #dee2e6; }
        .menu-card { background: #fff; border: 1px solid #e0e0e0; margin: 15px 0; padding: 15px; border-radius: 6px; }
        .menu-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .menu-header { background: #007bff; color: white; padding: 10px 15px; margin: -15px -15px 15px -15px; border-radius: 6px 6px 0 0; }
        .submenu-list { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-top: 10px; }
        .submenu-item { padding: 8px; margin: 5px 0; background: white; border-left: 3px solid #28a745; padding-left: 12px; }
        .badge { background: #28a745; color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 36px; font-weight: bold; margin: 10px 0; }
        .stat-label { font-size: 14px; opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Menu API Response - Staff ID 6 (Accountant)</h1>
        
        <?php
        $url = 'http://localhost/amt/api/teacher/menu';
        $data = json_encode(array('staff_id' => 6));
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $json = json_decode($response, true);
        
        if ($json && isset($json['data']['menus'])) {
            $staff_info = $json['data']['staff_info'];
            $role = $json['data']['role'];
            $menus = $json['data']['menus'];
            
            $total_menus = count($menus);
            $total_submenus = 0;
            $menus_with_submenus = 0;
            
            foreach ($menus as $menu) {
                $submenu_count = isset($menu['submenus']) ? count($menu['submenus']) : 0;
                if ($submenu_count > 0) {
                    $menus_with_submenus++;
                    $total_submenus += $submenu_count;
                }
            }
            
            echo '<div class="success">';
            echo '<h2 style="margin:0 0 10px 0;">✅ API Response Successful</h2>';
            echo '<strong>HTTP Status:</strong> ' . $http_code . '<br>';
            echo '<strong>Staff:</strong> ' . $staff_info['full_name'] . ' (ID: ' . $staff_info['id'] . ')<br>';
            echo '<strong>Role:</strong> ' . $role['name'] . ' (Superadmin: ' . ($role['is_superadmin'] ? 'Yes' : 'No') . ')';
            echo '</div>';
            
            echo '<div class="stats">';
            echo '<div class="stat-card">';
            echo '<div class="stat-label">Total Menus</div>';
            echo '<div class="stat-number">' . $total_menus . '</div>';
            echo '</div>';
            echo '<div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">';
            echo '<div class="stat-label">Menus with Submenus</div>';
            echo '<div class="stat-number">' . $menus_with_submenus . '</div>';
            echo '</div>';
            echo '<div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">';
            echo '<div class="stat-label">Total Submenus</div>';
            echo '<div class="stat-number">' . $total_submenus . '</div>';
            echo '</div>';
            echo '</div>';
            
            echo '<h2>📋 Menu Structure (Showing All ' . $total_menus . ' Menus)</h2>';
            
            foreach ($menus as $index => $menu) {
                $submenu_count = isset($menu['submenus']) ? count($menu['submenus']) : 0;
                
                echo '<div class="menu-card">';
                echo '<div class="menu-header">';
                echo '<strong>' . ($index + 1) . '. ' . $menu['menu'] . '</strong> ';
                echo '<span class="badge">' . $submenu_count . ' submenus</span>';
                echo '<br><small style="opacity:0.8;">Menu ID: ' . $menu['id'] . ' | Level: ' . $menu['level'] . '</small>';
                echo '</div>';
                
                if ($submenu_count > 0) {
                    echo '<div class="submenu-list">';
                    echo '<strong>🔹 Submenus:</strong>';
                    foreach ($menu['submenus'] as $submenu) {
                        echo '<div class="submenu-item">';
                        echo '<strong>' . $submenu['menu'] . '</strong>';
                        if (isset($submenu['url'])) {
                            echo '<br><small style="color:#666;">URL: ' . $submenu['url'] . '</small>';
                        }
                        echo '<br><small style="color:#999;">ID: ' . $submenu['id'] . ' | Key: ' . (isset($submenu['key']) ? $submenu['key'] : 'N/A') . '</small>';
                        echo '</div>';
                    }
                    echo '</div>';
                } else {
                    echo '<div style="padding:10px; color:#999; font-style:italic;">No submenus for this menu</div>';
                }
                
                echo '</div>';
            }
            
            echo '<h2>📄 Raw JSON Response</h2>';
            echo '<div class="info">Click to expand and see the complete JSON response</div>';
            echo '<details>';
            echo '<summary style="cursor:pointer; padding:10px; background:#e7f3ff; border-radius:4px; margin-bottom:10px;"><strong>Show Complete JSON Response</strong></summary>';
            echo '<pre>' . json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
            echo '</details>';
            
        } else {
            echo '<div style="background:#f8d7da; color:#721c24; padding:20px; border-left:4px solid #f5c6cb;">';
            echo '<h2>❌ Error</h2>';
            echo '<strong>HTTP Status:</strong> ' . $http_code . '<br>';
            if (isset($json['message'])) {
                echo '<strong>Message:</strong> ' . $json['message'] . '<br>';
            }
            echo '<pre>' . htmlspecialchars($response) . '</pre>';
            echo '</div>';
        }
        ?>
        
        <div class="info" style="margin-top:30px;">
            <h3>✨ Implementation Complete</h3>
            <p><strong>What was fixed:</strong></p>
            <ul>
                <li>✅ Removed restrictive permission-based submenu filtering</li>
                <li>✅ Simplified to: "If user has parent menu access, show ALL active submenus"</li>
                <li>✅ Applied to both <code>menu()</code> and <code>simple_menu()</code> endpoints</li>
                <li>✅ All menus now correctly display their submenus</li>
            </ul>
            <p><strong>API Endpoints:</strong></p>
            <ul>
                <li><code>POST /api/teacher/menu</code></li>
                <li><code>POST /api/teacher/simple_menu</code></li>
            </ul>
        </div>
    </div>
</body>
</html>
