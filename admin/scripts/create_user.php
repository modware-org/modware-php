<?php
require_once __DIR__ . '/../config/Database.php';

class AdminUserManager {
    private $db;

    public function __construct() {
        $this->db = AdminDatabase::getInstance();
    }

    public function createUser($username, $password, $email = null, $role = 'editor') {
        error_log("Creating user: $username with role: $role");
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $email = $email ?? $username . '@example.com';

        try {
            $query = "INSERT INTO admin_users (username, email, password_hash, role, is_active) 
                     VALUES (:username, :email, :password_hash, :role, 1)";
            
            $result = $this->db->getConnection()->prepare($query);
            if (!$result) {
                $error = $this->db->getConnection()->lastErrorMsg();
                error_log("Failed to prepare statement: $error");
                throw new Exception($error);
            }

            $result->bindValue(':username', $username, SQLITE3_TEXT);
            $result->bindValue(':email', $email, SQLITE3_TEXT);
            $result->bindValue(':password_hash', $passwordHash, SQLITE3_TEXT);
            $result->bindValue(':role', $role, SQLITE3_TEXT);

            if ($result->execute()) {
                error_log("User created successfully: $username");
                // Verify the user was created
                $verifyQuery = "SELECT * FROM admin_users WHERE username = :username";
                $verifyStmt = $this->db->getConnection()->prepare($verifyQuery);
                $verifyStmt->bindValue(':username', $username, SQLITE3_TEXT);
                $verifyResult = $verifyStmt->execute();
                $user = $verifyResult->fetchArray(SQLITE3_ASSOC);
                error_log("Verification result: " . json_encode($user));
                
                return [
                    'success' => true,
                    'message' => 'User created successfully',
                    'username' => $username,
                    'role' => $role
                ];
            } else {
                $error = $this->db->getConnection()->lastErrorMsg();
                error_log("Failed to execute statement: $error");
                throw new Exception("Failed to create user: $error");
            }
        } catch (Exception $e) {
            error_log("Error creating user: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function userExists($username) {
        try {
            $query = "SELECT COUNT(*) as count FROM admin_users WHERE username = :username";
            $stmt = $this->db->getConnection()->prepare($query);
            if (!$stmt) {
                $error = $this->db->getConnection()->lastErrorMsg();
                error_log("Failed to prepare statement: $error");
                throw new Exception($error);
            }

            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $result = $stmt->execute();
            
            if ($result) {
                $row = $result->fetchArray(SQLITE3_ASSOC);
                error_log("User exists check for $username: " . ($row['count'] > 0 ? 'true' : 'false'));
                return $row['count'] > 0;
            }
            return false;
        } catch (Exception $e) {
            error_log("Error checking user existence: " . $e->getMessage());
            return false;
        }
    }

    public function createDefaultUsers() {
        error_log("Creating default users...");
        $defaultUsers = [
            ['admin', 'admin123', 'admin@example.com', 'admin'],
            ['editor', 'editor123', 'editor@example.com', 'editor'],
            ['viewer', 'viewer123', 'viewer@example.com', 'viewer']
        ];

        $results = [];
        foreach ($defaultUsers as $user) {
            if (!$this->userExists($user[0])) {
                error_log("Creating default user: " . $user[0]);
                $results[] = $this->createUser($user[0], $user[1], $user[2], $user[3]);
            } else {
                error_log("Default user already exists: " . $user[0]);
            }
        }
        return $results;
    }

    public function countUsers() {
        try {
            $query = "SELECT COUNT(*) as count FROM admin_users";
            $result = $this->db->getConnection()->query($query);
            if ($result) {
                $row = $result->fetchArray(SQLITE3_ASSOC);
                error_log("Total user count: " . $row['count']);
                return $row['count'];
            }
            return 0;
        } catch (Exception $e) {
            error_log("Error counting users: " . $e->getMessage());
            return 0;
        }
    }
}

// Check if script is running from command line
if (php_sapi_name() !== 'cli') {
    die('This script can only be run from the command line');
}

$userManager = new AdminUserManager();

// Parse command line arguments
$options = getopt('', ['action:', 'username:', 'password:', 'email::', 'role::']);

// If no users exist or no action specified, create default users
if ($userManager->countUsers() === 0 || !isset($options['action'])) {
    error_log("Creating default users...");
    $results = $userManager->createDefaultUsers();
    foreach ($results as $result) {
        if ($result['success']) {
            echo "Created default user: " . $result['username'] . " (". $result['role'] . ")\n";
        } else {
            echo "Error creating user: " . $result['error'] . "\n";
        }
    }
    exit(0);
}

// Handle manual user creation
if ($options['action'] === 'create') {
    if (!isset($options['username']) || !isset($options['password'])) {
        die("Usage: php create_user.php --action=create --username=user --password=pass [--email=user@example.com] [--role=admin|editor|viewer]\n");
    }
    
    $result = $userManager->createUser(
        $options['username'], 
        $options['password'],
        $options['email'] ?? null,
        $options['role'] ?? 'editor'
    );
    
    if ($result['success']) {
        echo "User created successfully!\n";
        echo "Username: " . $result['username'] . "\n";
        echo "Role: " . $result['role'] . "\n";
    } else {
        echo "Error: " . $result['error'] . "\n";
    }
} else {
    die("Invalid action. Use 'create' or run without arguments to create default users\n");
}
