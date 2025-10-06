<?php
/**
 * Quick Submenu Test for Staff ID 6
 * Shows submenu counts for each menu
 */

echo "<h1>Quick Submenu Test - Staff ID 6 (Accountant)</h1>";
echo "<hr>";

$url = 'http://localhost/amt/api/teacher/menu';
$data = json_encode(array('staff_id' => 6));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json'
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<strong>HTTP Status:</strong> $http_code<br><br>";

$json = json_decode($response, true);

if ($json && isset($json['data']['menus'])) {
    $menus = $json['data']['menus'];
    $total_menus = count($menus);
    $total_submenus = 0;
    $menus_with_submenus = 0;
    
    echo "<h2>Total Menus: $total_menus</h2>";
    
    echo "<table border='1' cellpadding='10' style='border-collapse:collapse; width:100%;'>";
    echo "<tr style='background:#f0f0f0;'>";
    echo "<th>ID</th><th>Menu Name</th><th>Submenu Count</th><th>Submenu Names</th>";
    echo "</tr>";
    
    foreach ($menus as $menu) {
        $submenu_count = isset($menu['submenus']) ? count($menu['submenus']) : 0;
        if ($submenu_count > 0) {
            $menus_with_submenus++;
            $total_submenus += $submenu_count;
        }
        
        $bg_color = $submenu_count > 0 ? '#d4edda' : '#ffffff';
        
        echo "<tr style='background:$bg_color;'>";
        echo "<td>" . $menu['id'] . "</td>";
        echo "<td><strong>" . $menu['menu'] . "</strong></td>";
        echo "<td style='text-align:center; font-size:18px;'><strong>" . $submenu_count . "</strong></td>";
        echo "<td>";
        
        if ($submenu_count > 0) {
            foreach ($menu['submenus'] as $submenu) {
                echo "• " . $submenu['menu'] . "<br>";
            }
        } else {
            echo "<em style='color:#999;'>-</em>";
        }
        
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<div style='background:#007bff; color:white; padding:20px; margin-top:20px; border-radius:5px;'>";
    echo "<h2 style='margin:0 0 15px 0;'>📊 Summary Statistics</h2>";
    echo "<div style='font-size:18px;'>";
    echo "✅ <strong>Total Menus:</strong> $total_menus<br>";
    echo "✅ <strong>Menus with Submenus:</strong> $menus_with_submenus<br>";
    echo "✅ <strong>Total Submenus:</strong> $total_submenus<br>";
    echo "✅ <strong>Average Submenus per Menu:</strong> " . ($total_menus > 0 ? round($total_submenus / $total_menus, 1) : 0);
    echo "</div>";
    echo "</div>";
    
} else {
    echo "<div style='background:#f8d7da; padding:15px; color:#721c24;'>";
    echo "<strong>Error:</strong> Unable to retrieve menus<br>";
    if (isset($json['message'])) {
        echo "<strong>Message:</strong> " . $json['message'];
    }
    echo "</div>";
}
?>
