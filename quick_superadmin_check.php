<?php
/**
 * Quick Superadmin Check
 * This script quickly checks for superadmin users and tests menu API
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "amt";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<h2>🔐 Quick Superadmin Check</h2>";
} catch(PDOException $e) {
    die("<h2>❌ Connection failed: " . $e->getMessage() . "</h2>");
}

// Find superadmin users
echo "<h3>1. Superadmin Users in System:</h3>";
$stmt = $pdo->query("
    SELECT s.id as staff_id, s.name, s.surname, s.employee_id, s.is_active,
           sr.role_id, r.name as role_name, r.is_superadmin
    FROM staff s
    JOIN staff_roles sr ON sr.staff_id = s.id
    JOIN roles r ON r.id = sr.role_id
    WHERE r.is_superadmin = 1
    ORDER BY s.id
");
$superadmins = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($superadmins) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Staff ID</th><th>Name</th><th>Employee ID</th><th>Role</th><th>Active</th><th>Test API</th></tr>";
    foreach ($superadmins as $admin) {
        $active = $admin['is_active'] ? '✅ Yes' : '❌ No';
        echo "<tr style='background-color: #e8f5e8;'>";
        echo "<td><strong>{$admin['staff_id']}</strong></td>";
        echo "<td>{$admin['name']} {$admin['surname']}</td>";
        echo "<td>{$admin['employee_id']}</td>";
        echo "<td>{$admin['role_name']} (ID: {$admin['role_id']})</td>";
        echo "<td>$active</td>";
        echo "<td><button onclick=\"testMenuApi({$admin['staff_id']})\">🧪 Test</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No superadmin users found!</p>";
    
    // Check if role ID 7 exists
    echo "<h4>Checking Role ID 7:</h4>";
    $stmt = $pdo->query("SELECT * FROM roles WHERE id = 7");
    $role7 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($role7) {
        echo "<p>Role ID 7 exists: <strong>{$role7['name']}</strong> (is_superadmin: " . ($role7['is_superadmin'] ? 'Yes' : 'No') . ")</p>";
        
        if (!$role7['is_superadmin']) {
            echo "<p style='color: red;'>❌ Role ID 7 is NOT marked as superadmin!</p>";
            echo "<button onclick=\"fixRole7()\">🔧 Fix Role ID 7</button>";
        }
    } else {
        echo "<p style='color: red;'>❌ Role ID 7 does not exist!</p>";
        echo "<button onclick=\"createRole7()\">➕ Create Role ID 7</button>";
    }
}

// Show roles with is_superadmin = 1
echo "<h3>2. All Roles with Superadmin Flag:</h3>";
$stmt = $pdo->query("SELECT * FROM roles WHERE is_superadmin = 1 ORDER BY id");
$superadmin_roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($superadmin_roles) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Role ID</th><th>Name</th><th>Slug</th><th>Active</th><th>Actions</th></tr>";
    foreach ($superadmin_roles as $role) {
        $active = $role['is_active'] ? '✅ Yes' : '❌ No';
        echo "<tr style='background-color: #fff3cd;'>";
        echo "<td><strong>{$role['id']}</strong></td>";
        echo "<td>{$role['name']}</td>";
        echo "<td>{$role['slug']}</td>";
        echo "<td>$active</td>";
        echo "<td><button onclick=\"assignRole({$role['id']})\">👤 Assign to Staff</button></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ No roles marked as superadmin found!</p>";
}

// Show test staff members
echo "<h3>3. Sample Staff for Testing:</h3>";
$stmt = $pdo->query("
    SELECT s.id, s.name, s.surname, s.employee_id, 
           r.name as role_name, r.is_superadmin, r.id as role_id
    FROM staff s
    LEFT JOIN staff_roles sr ON sr.staff_id = s.id
    LEFT JOIN roles r ON r.id = sr.role_id
    WHERE s.is_active = 1
    ORDER BY r.is_superadmin DESC, s.id
    LIMIT 5
");
$sample_staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($sample_staff) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Staff ID</th><th>Name</th><th>Current Role</th><th>Is Superadmin</th><th>Test API</th></tr>";
    foreach ($sample_staff as $staff) {
        $superadmin = $staff['is_superadmin'] ? '✅ Yes' : '❌ No';
        $bg_color = $staff['is_superadmin'] ? '#e8f5e8' : '#f8f9fa';
        echo "<tr style='background-color: $bg_color;'>";
        echo "<td>{$staff['id']}</td>";
        echo "<td>{$staff['name']} {$staff['surname']}</td>";
        echo "<td>" . ($staff['role_name'] ?: 'No Role') . "</td>";
        echo "<td>$superadmin</td>";
        echo "<td><button onclick=\"testMenuApi({$staff['id']})\">🧪 Test</button></td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<div id='api-results' style='margin-top: 20px;'></div>";
?>

<script>
function testMenuApi(staffId) {
    const resultsDiv = document.getElementById('api-results');
    resultsDiv.innerHTML = `<h4>🧪 Testing Menu API for Staff ID: ${staffId}</h4><p>⏳ Loading...</p>`;
    
    fetch('/amt/api/teacher/menu', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({staff_id: parseInt(staffId)})
    })
    .then(response => response.json())
    .then(data => {
        let html = `<h4>📋 API Response for Staff ID: ${staffId}</h4>`;
        
        if (data.status === 1) {
            const roleInfo = data.data.role;
            const menuCount = data.data.total_menus;
            
            html += `<div style="background: #e8f5e8; padding: 10px; border: 1px solid #4caf50; border-radius: 5px;">`;
            html += `<p><strong>✅ Success!</strong></p>`;
            html += `<p><strong>Role:</strong> ${roleInfo.name} (ID: ${roleInfo.id})</p>`;
            html += `<p><strong>Is Superadmin:</strong> ${roleInfo.is_superadmin ? '✅ Yes' : '❌ No'}</p>`;
            html += `<p><strong>Total Menus:</strong> ${menuCount}</p>`;
            html += `</div>`;
            
            if (data.data.menus && data.data.menus.length > 0) {
                html += `<h5>📋 Available Menus:</h5>`;
                html += `<ul>`;
                data.data.menus.forEach(menu => {
                    html += `<li><strong>${menu.menu}</strong> (ID: ${menu.id})`;
                    if (menu.submenus && menu.submenus.length > 0) {
                        html += ` - ${menu.submenus.length} submenus`;
                    }
                    html += `</li>`;
                });
                html += `</ul>`;
            }
        } else {
            html += `<div style="background: #ffebee; padding: 10px; border: 1px solid #f44336; border-radius: 5px;">`;
            html += `<p><strong>❌ Error:</strong> ${data.message}</p>`;
            html += `</div>`;
        }
        
        html += `<details style="margin-top: 10px;">`;
        html += `<summary>🔍 View Full API Response</summary>`;
        html += `<pre style="background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow-x: auto;">${JSON.stringify(data, null, 2)}</pre>`;
        html += `</details>`;
        
        resultsDiv.innerHTML = html;
    })
    .catch(error => {
        resultsDiv.innerHTML = `<div style="background: #ffebee; padding: 10px; border: 1px solid #f44336; border-radius: 5px;">
            <p><strong>❌ API Error:</strong> ${error.message}</p>
        </div>`;
    });
}

function fixRole7() {
    if (confirm('Fix Role ID 7 to be superadmin?')) {
        fetch('update_role_7.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'fix_role_7'})
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => alert('Error: ' + error.message));
    }
}

function createRole7() {
    if (confirm('Create Role ID 7 as superadmin?')) {
        fetch('update_role_7.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'create_role_7'})
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => alert('Error: ' + error.message));
    }
}

function assignRole(roleId) {
    const staffId = prompt('Enter Staff ID to assign this role to:');
    if (staffId) {
        fetch('update_role_7.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'assign_role', staff_id: parseInt(staffId), role_id: roleId})
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            location.reload();
        })
        .catch(error => alert('Error: ' + error.message));
    }
}
</script>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; width: 100%; }
th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
th { background-color: #f0f0f0; }
button { padding: 5px 10px; margin: 2px; cursor: pointer; }
</style>