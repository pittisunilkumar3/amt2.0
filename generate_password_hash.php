<?php
/**
 * Password Hash Generator
 * 
 * This simple script generates a bcrypt password hash
 * that can be used to manually update passwords in the database
 */

// Check if password is provided as command line argument
if (isset($argv[1])) {
    $password = $argv[1];
} else {
    // Prompt for password
    echo "Password Hash Generator\n";
    echo "======================\n\n";
    echo "Enter password to hash: ";
    $password = trim(fgets(STDIN));
}

if (empty($password)) {
    echo "Error: Password cannot be empty!\n";
    exit(1);
}

// Generate hash
$hash = password_hash($password, PASSWORD_DEFAULT);

// Display results
echo "\n";
echo "==============================================\n";
echo "Password Hash Generated Successfully\n";
echo "==============================================\n\n";
echo "Password: $password\n";
echo "Hash: $hash\n";
echo "Hash Length: " . strlen($hash) . " characters\n\n";

// Verify the hash works
$verify = password_verify($password, $hash);
echo "Verification Test: " . ($verify ? "✅ PASSED" : "❌ FAILED") . "\n\n";

// Provide SQL update command
echo "==============================================\n";
echo "SQL Update Command\n";
echo "==============================================\n\n";
echo "To update the superadmin password, run this SQL:\n\n";
echo "UPDATE staff \n";
echo "SET password = '$hash' \n";
echo "WHERE email = 'amaravatijuniorcollege@gmail.com';\n\n";

echo "Or use this PHP code:\n\n";
echo "\$pdo = new PDO('mysql:host=localhost;dbname=amt', 'root', '');\n";
echo "\$stmt = \$pdo->prepare('UPDATE staff SET password = ? WHERE email = ?');\n";
echo "\$stmt->execute(['$hash', 'amaravatijuniorcollege@gmail.com']);\n\n";

echo "==============================================\n";
echo "Done!\n";
echo "==============================================\n";
?>

