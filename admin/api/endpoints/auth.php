<?php
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/Database.php';

class Auth {
    private $db;
    private $secret_key;
    private $token_lifetime;

    public function __construct() {
        $this->db = AdminDatabase::getInstance();
        $this->secret_key = AdminEnvironment::get('JWT_SECRET') ?: 'default-secret-key-change-in-production';
        $this->token_lifetime = (int)(AdminEnvironment::get('JWT_LIFETIME') ?: 3600);
    }

    private function generateToken($user_id, $username) {
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode([
            'user_id' => $user_id,
            'username' => $username,
            'exp' => time() + $this->token_lifetime
        ]));
        
        $signature = hash_hmac('sha256', "$header.$payload", $this->secret_key);
        return "$header.$payload.$signature";
    }

    public function login($username, $password) {
        try {
            error_log("Login attempt - Username: $username");

            if (empty($username) || empty($password)) {
                error_log("Empty username or password");
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            // Debug database connection
            error_log("Database connection state: " . ($this->db->getConnection() ? "Connected" : "Not connected"));

            $query = "SELECT id, username, password_hash FROM admin_users WHERE username = :username AND is_active = 1";
            error_log("Executing query: $query");

            $stmt = $this->db->getConnection()->prepare($query);
            if (!$stmt) {
                $error = $this->db->getConnection()->lastErrorMsg();
                error_log("Failed to prepare statement: $error");
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $result = $stmt->execute();
            
            if (!$result) {
                $error = $this->db->getConnection()->lastErrorMsg();
                error_log("Query execution failed: $error");
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $user = $result->fetchArray(SQLITE3_ASSOC);
            error_log("Query result: " . json_encode($user));

            if (!$user) {
                error_log("No user found with username: $username");
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            // Debug password verification
            error_log("Stored password hash: " . $user['password_hash']);
            error_log("Verifying password...");
            $passwordVerified = password_verify($password, $user['password_hash']);
            error_log("Password verification result: " . ($passwordVerified ? "Success" : "Failed"));

            if (!$passwordVerified) {
                error_log("Password verification failed for user: $username");
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $token = $this->generateToken($user['id'], $user['username']);
            error_log("Generated token: $token");
            
            // Store token in admin_sessions
            try {
                $sessionQuery = "INSERT INTO admin_sessions (user_id, token, expires_at) VALUES (:user_id, :token, datetime('now', '+1 hour'))";
                $stmt = $this->db->getConnection()->prepare($sessionQuery);
                $stmt->bindValue(':user_id', $user['id'], SQLITE3_INTEGER);
                $stmt->bindValue(':token', $token, SQLITE3_TEXT);
                $stmt->execute();
                error_log("Session stored successfully");
            } catch (Exception $e) {
                error_log("Failed to store session: " . $e->getMessage());
                // Continue even if session storage fails
            }
            
            return [
                'success' => true,
                'token' => $token,
                'message' => 'Login successful'
            ];
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
    }

    public function handleRequest() {
        header('Content-Type: application/json');

        // Check request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        // Parse JSON input
        $json = file_get_contents('php://input');
        error_log("Received request data: " . $json);
        
        $data = json_decode($json, true);
        error_log("Parsed request data: " . json_encode($data));

        // Validate request data
        if (!$json || !$data || !isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request data']);
            return;
        }

        // Attempt login
        $result = $this->login($data['username'], $data['password']);
        error_log("Login result: " . json_encode($result));
        
        // Set appropriate status code
        if (!$result['success']) {
            http_response_code(401);
        }
        
        // Send response
        echo json_encode($result);
    }
}

// Handle the request
$auth = new Auth();
$auth->handleRequest();
