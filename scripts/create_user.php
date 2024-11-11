<?php
require_once __DIR__ . '/../config/Database.php';

class UserManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function createUser($username, $password, $email = null) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $email = $email ?? $username . '@example.com';

        try {
            // Check if user exists
            $checkQuery = "SELECT id FROM admin_users WHERE username = :username OR email = :email";
            $result = $this->db->query($checkQuery, [
                ':username' => $username,
                ':email' => $email
            ]);

            if (!empty($result)) {
                // User exists, update password
                $updateQuery = "UPDATE admin_users SET 
                    password_hash = :password_hash,
                    is_active = 1
                    WHERE username = :username OR email = :email";
                
                $success = $this->db->execute($updateQuery, [
                    ':username' => $username,
                    ':email' => $email,
                    ':password_hash' => $passwordHash
                ]);

                if ($success) {
                    return [
                        'success' => true,
                        'message' => 'User updated',
                        'username' => $username
                    ];
                }
            } else {
                // Create new user
                $insertQuery = "INSERT INTO admin_users (username, email, password_hash, role, is_active) 
                              VALUES (:username, :email, :password_hash, 'admin', 1)";
                
                $success = $this->db->execute($insertQuery, [
                    ':username' => $username,
                    ':email' => $email,
                    ':password_hash' => $passwordHash
                ]);

                if ($success) {
                    return [
                        'success' => true,
                        'message' => 'User created',
                        'username' => $username
                    ];
                }
            }

            throw new Exception("Failed to manage user");
        } catch (Exception $e) {
            error_log("User management error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function updatePassword($username, $password) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $query = "UPDATE admin_users SET 
                     password_hash = :password_hash 
                     WHERE username = :username";
            
            $success = $this->db->execute($query, [
                ':username' => $username,
                ':password_hash' => $passwordHash
            ]);

            if ($success) {
                return [
                    'success' => true,
                    'message' => 'Password updated',
                    'username' => $username
                ];
            } else {
                throw new Exception("Failed to update password");
            }
        } catch (Exception $e) {
            error_log("Password update error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}

// Check if script is running from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

// Parse command line arguments
$options = getopt('', ['action:', 'username:', 'password:', 'email::']);

if (!isset($options['action'])) {
    die("Usage: php create_user.php --action=[create|update] --username=user --password=pass [--email=user@example.com]\n");
}

$userManager = new UserManager();

switch ($options['action']) {
    case 'create':
        if (!isset($options['username']) || !isset($options['password'])) {
            die("Username and password are required for user creation\n");
        }
        
        $result = $userManager->createUser(
            $options['username'], 
            $options['password'],
            $options['email'] ?? null
        );
        break;

    case 'update':
        if (!isset($options['username']) || !isset($options['password'])) {
            die("Username and password are required for password update\n");
        }
        
        $result = $userManager->updatePassword(
            $options['username'], 
            $options['password']
        );
        break;

    default:
        die("Invalid action. Use 'create' or 'update'\n");
}

if ($result['success']) {
    echo $result['message'] . "!\n";
    echo "Username: " . $result['username'] . "\n";
} else {
    echo "Error: " . $result['error'] . "\n";
}
