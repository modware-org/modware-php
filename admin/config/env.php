<?php
// Only load environment if not already loaded
if (!defined('ENV_LOADED')) {
    require_once __DIR__ . '/../../config/env.php';
}

// Admin-specific environment configurations
if (!defined('ADMIN_ENV_LOADED')) {
    // Override or extend specific constants for admin
    define('SITE_NAME', getenv('APP_NAME') ?: 'Unity DBT Admin');
    define('SITE_URL', getenv('APP_URL') ?: 'http://localhost:8007/admin');
    define('LOG_PATH', __DIR__ . '/../../logs/admin.log');
    define('CACHE_PATH', __DIR__ . '/../../cache/admin');
    define('UPLOAD_PATH', getenv('UPLOAD_PATH') ?: __DIR__ . '/../../uploads/admin');
    
    // More restrictive security settings for admin
    define('PASSWORD_MIN_LENGTH', 12);
    define('API_RATE_LIMIT', 50);
    define('API_TOKEN_LIFETIME', getenv('JWT_LIFETIME') ?: 1800);

    define('ADMIN_ENV_LOADED', true);
}

// Secure session configuration
function configureSecureSession() {
    // Only set session parameters if no session is active
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        
        // Secure cookie only on HTTPS
        $siteUrl = getenv('APP_URL') ?: 'http://localhost:8007';
        if (parse_url($siteUrl, PHP_URL_SCHEME) === 'https') {
            ini_set('session.cookie_secure', 1);
        }
    }
}

// Configure secure session early
configureSecureSession();
