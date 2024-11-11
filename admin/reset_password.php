<?php
require_once __DIR__ . '/../config/Database.php';

function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, strlen($chars) - 1)];
    }
    return $password;
}

// Check if script is running from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

try {
    $db = Database::getInstance();
    
    // Generate new password
    $newPassword = generatePassword();
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update admin password
    $db->query(
        "UPDATE users SET password_hash = :password_hash WHERE username = 'admin'",
        [':password_hash' => $passwordHash]
    );
    
    echo "Password reset successful!\n";
    echo "New password: " . $newPassword . "\n";
    
} catch (Exception $e) {
    echo "Error resetting password: " . $e->getMessage() . "\n";
}
