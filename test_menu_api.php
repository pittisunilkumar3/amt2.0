<?php
// Test script to check the teacher menu API

echo "<h2>Testing Teacher Menu API</h2>";

// Test with different staff IDs
$test_staff_ids = [1, 2, 3]; // You can modify these based on your database

// First, let's check what staff members exist
echo "<h3>Available Staff Members:</h3>";
$mysqli = new mysqli("localhost", "root", "", "school"); // Adjust database credentials as needed

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$staff_query = "SELECT s.id, s.name, s.surname, s.employee_id, s.is_active, 
                       r.name as role_name, sd.designation
                FROM staff s 
                LEFT JOIN staff_roles sr ON sr.staff_id = s.id 
                LEFT JOIN roles r ON r.id = sr.role_id 
                LEFT JOIN staff_designation sd ON sd.id = s.designation 
                WHERE s.is_active = 1 
                ORDER BY s.id LIMIT 10";

$result = $mysqli->query($staff_query);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Employee ID</th><th>Name</th><th>Role</th><th>Designation</th><th>Test Menu API</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['id'] . "</td>";
    echo "<td>" . ($row['employee_id'] ?? 'N/A') . "</td>";
    echo "<td>" . $row['name'] . " " . $row['surname'] . "</td>";
    echo "<td>" . ($row['role_name'] ?? 'No Role') . "</td>";
    echo "<td>" . ($row['designation'] ?? 'No Designation') . "</td>";
    echo "<td><a href='test_menu_api_call.php?staff_id=" . $row['id'] . "' target='_blank'>Test</a></td>";
    echo "</tr>";
}
echo "</table>";

$mysqli->close();
?>