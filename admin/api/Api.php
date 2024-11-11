<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/Database.php';

class Api {
    protected $db;
    protected $secret_key;

    public function __construct() {
        $this->db = AdminDatabase::getInstance();
        $this->secret_key = AdminEnvironment::get('JWT_SECRET') ?: 'default-secret-key-change-in-production';
    }

    protected function verifyToken() {
        $headers = getallheaders();
        $auth_header = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        
        if (!preg_match('/Bearer\s+(.*)$/i', $auth_header, $matches)) {
            throw new Exception('No token provided');
        }

        $token = $matches[1];
        $parts = explode('.', $token);
        
        if (count($parts) !== 3) {
            throw new Exception('Invalid token format');
        }

        list($header, $payload, $signature) = $parts;
        
        $valid_signature = hash_hmac('sha256', "$header.$payload", $this->secret_key);
        
        if ($signature !== $valid_signature) {
            throw new Exception('Invalid token signature');
        }

        $payload_data = json_decode(base64_decode($payload), true);
        
        if (!$payload_data || !isset($payload_data['exp'])) {
            throw new Exception('Invalid token payload');
        }

        if ($payload_data['exp'] < time()) {
            throw new Exception('Token has expired');
        }

        // Verify token in database
        $query = "SELECT * FROM admin_sessions WHERE token = :token AND expires_at > datetime('now')";
        $stmt = $this->db->getConnection()->prepare($query);
        $stmt->bindValue(':token', $token, SQLITE3_TEXT);
        $result = $stmt->execute();
        
        if (!$result || !$result->fetchArray(SQLITE3_ASSOC)) {
            throw new Exception('Token not found or expired in database');
        }

        return $payload_data;
    }

    protected function requireAuth() {
        try {
            return $this->verifyToken();
        } catch (Exception $e) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized: ' . $e->getMessage()]);
            exit();
        }
    }

    protected function sendResponse($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function sendError($message, $status = 400) {
        $this->sendResponse(['success' => false, 'message' => $message], $status);
    }

    protected function getRequestData() {
        $json = file_get_contents('php://input');
        if (!$json) {
            throw new Exception('No request data provided');
        }

        $data = json_decode($json, true);
        if (!$data) {
            throw new Exception('Invalid JSON data');
        }

        return $data;
    }
}
