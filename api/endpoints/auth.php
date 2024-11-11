<?php
require_once __DIR__ . '/../../admin/config/env.php';
require_once __DIR__ . '/../../admin/config/Database.php';

class Auth {
    private $db;
    private $secret_key;
    private $token_lifetime;

    public function __construct() {
        $this->db = AdminDatabase::getInstance();
        $this->secret_key = getenv('JWT_SECRET') ?: 'default-secret-key-change-in-production';
        $this->token_lifetime = (int)(getenv('JWT_LIFETIME') ?: 3600);
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
            if (empty($username) || empty($password)) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $query = "SELECT id, username, password_hash FROM admin_users WHERE username = :username AND is_active = 1";
            $stmt = $this->db->getConnection()->prepare($query);
            if (!$stmt) {
                error_log("Database query failed in auth login");
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $stmt->bindValue(':username', $username, SQLITE3_TEXT);
            $result = $stmt->execute();
            
            if (!$result) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $user = $result->fetchArray(SQLITE3_ASSOC);
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }

            $token = $this->generateToken($user['id'], $user['username']);
            
            // Store token in admin_sessions
            try {
                $sessionQuery = "INSERT INTO admin_sessions (user_id, token, expires_at) VALUES (:user_id, :token, datetime('now', '+1 hour'))";
                $stmt = $this->db->getConnection()->prepare($sessionQuery);
                $stmt->bindValue(':user_id', $user['id'], SQLITE3_INTEGER);
                $stmt->bindValue(':token', $token, SQLITE3_TEXT);
                $stmt->execute();
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
        $data = json_decode($json, true);

        // Validate request data
        if (!$json || !$data || !isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid request data']);
            return;
        }

        // Attempt login
        $result = $this->login($data['username'], $data['password']);
        
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
