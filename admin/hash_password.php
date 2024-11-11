<?php

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
    // Generate new password
    // get arguments from command line
    // Example: php hash_password.php --password=admin123
    // $password = 12; // default password length if not provided as argument
    // $newPassword = generatePassword($password);
    $args = getopt('p:', ['password:']);
//    var_dump($args);
//    $password = len($args['password'])? (int)$args['password'] : 12;
    $newPassword = $args['password'];
//    if ($password < 8 || $password > 50) {
//        echo ("Password must be between 8 and 50 characters.\n");
//        $newPassword = generatePassword();
//    }
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    printf("New password:\n%s\n", $newPassword);
    printf("New hash:\n%s\n", $passwordHash);

} catch (Exception $e) {
    echo "Error resetting password: " . $e->getMessage() . "\n";
}
