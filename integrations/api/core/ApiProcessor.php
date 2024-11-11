<?php
namespace Integrations\Api;

class ApiProcessor {
    private static $db;
    private static $endpoints = [];

    public static function init($db) {
        self::$db = $db;
        self::validateRequest();
    }

    private static function validateRequest() {
        $apiKey = self::getApiKey();
        if (!$apiKey) {
            self::sendResponse(401, 'API key required');
        }

        if (!self::validateApiKey($apiKey)) {
            self::sendResponse(403, 'Invalid API key');
        }
    }

    private static function getApiKey() {
        $headers = getallheaders();
        return $headers['X-API-Key'] ?? null;
    }

    private static function validateApiKey($apiKey) {
        if (!self::$db) {
            return false;
        }

        $stmt = self::$db->prepare("
            SELECT permissions 
            FROM api_keys 
            WHERE api_key = ? AND is_active = 1
        ");
        
        $stmt->execute([$apiKey]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            // Update last used timestamp
            $updateStmt = self::$db->prepare("
                UPDATE api_keys 
                SET last_used = CURRENT_TIMESTAMP 
                WHERE api_key = ?
            ");
            $updateStmt->execute([$apiKey]);

            return true;
        }

        return false;
    }

    public static function registerEndpoint($path, $method, $callback, $requiredPermissions = []) {
        self::$endpoints[$path][$method] = [
            'callback' => $callback,
            'permissions' => $requiredPermissions
        ];
    }

    public static function handleRequest($path, $method) {
        if (!isset(self::$endpoints[$path][$method])) {
            self::sendResponse(404, 'Endpoint not found');
        }

        $endpoint = self::$endpoints[$path][$method];
        
        // Check permissions if required
        if (!empty($endpoint['permissions'])) {
            if (!self::checkPermissions($endpoint['permissions'])) {
                self::sendResponse(403, 'Insufficient permissions');
            }
        }

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = call_user_func($endpoint['callback'], $data);
            self::sendResponse(200, 'Success', $result);
        } catch (\Exception $e) {
            self::sendResponse(500, $e->getMessage());
        }
    }

    private static function checkPermissions($requiredPermissions) {
        $apiKey = self::getApiKey();
        $stmt = self::$db->prepare("
            SELECT permissions 
            FROM api_keys 
            WHERE api_key = ? AND is_active = 1
        ");
        
        $stmt->execute([$apiKey]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        $permissions = json_decode($result['permissions'], true);
        return empty(array_diff($requiredPermissions, $permissions));
    }

    private static function sendResponse($status, $message, $data = null) {
        http_response_code($status);
        echo json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}

// Usage example:
// 1. Register API endpoint:
// ApiProcessor::registerEndpoint('/api/sections', 'GET', function($data) {
//     // Handle section list request
//     return ['sections' => $sectionsList];
// }, ['read_sections']);
//
// 2. Handle request in section:
// ApiProcessor::handleRequest('/api/sections', $_SERVER['REQUEST_METHOD']);
