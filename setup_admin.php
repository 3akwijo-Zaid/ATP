<?php
require_once 'config/config.php';
require_once 'src/classes/Database.php';

$db = new Database();

// Create admin user with correct password hash
$username = 'admin';
$password = 'tenniss123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin user already exists
$db->query('SELECT id FROM users WHERE username = :username');
$db->bind(':username', $username);
$existing_user = $db->single();

if ($existing_user) {
    // Update existing admin user
    $db->query('UPDATE users SET password_hash = :password_hash, is_admin = 1 WHERE username = :username');
    $db->bind(':password_hash', $hashed_password);
    $db->bind(':username', $username);
    
    if ($db->execute()) {
        echo "Admin user updated successfully!\n";
        echo "Username: admin\n";
        echo "Password: tenniss123\n";
    } else {
        echo "Failed to update admin user.\n";
    }
} else {
    // Create new admin user
    $db->query('INSERT INTO users (username, password_hash, is_admin) VALUES (:username, :password_hash, 1)');
    $db->bind(':username', $username);
    $db->bind(':password_hash', $hashed_password);
    
    if ($db->execute()) {
        echo "Admin user created successfully!\n";
        echo "Username: admin\n";
        echo "Password: tenniss123\n";
    } else {
        echo "Failed to create admin user.\n";
    }
}

// Ensure point settings exist
$db->query('SELECT id FROM point_settings WHERE id = 1');
$existing_settings = $db->single();

if (!$existing_settings) {
    $db->query('INSERT INTO point_settings (id, match_winner_points, set_winner_points, set_score_points) VALUES (1, 10, 3, 5)');
    if ($db->execute()) {
        echo "Point settings created successfully!\n";
    } else {
        echo "Failed to create point settings.\n";
    }
}

echo "\nSetup complete! You can now log in with:\n";
echo "Username: admin\n";
echo "Password: tenniss123\n";
?> 