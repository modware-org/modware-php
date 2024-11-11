<?php
session_start();

require_once __DIR__ . '/config/Database.php';

function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        // Set status code first, then exit with error message
        http_response_code(401);
        exit('Unauthorized');
    }
}

function getCurrentUser() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    $db = AdminDatabase::getInstance();
    $query = "SELECT id, username, role FROM admin_users WHERE id = :id AND is_active = 1";
    $result = $db->query($query, [':id' => $_SESSION['user_id']]);
    
    if ($result) {
        return $result->fetchArray(SQLITE3_ASSOC);
    }
    
    return null;
}

// Require auth for all admin pages except login
$currentScript = basename($_SERVER['SCRIPT_FILENAME']);
if ($currentScript !== 'login.php') {
    requireAuth();
}
