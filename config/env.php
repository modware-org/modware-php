<?php
if (!function_exists('loadEnv')) {
    function loadEnv() {
        static $loaded = false;
        if ($loaded) return;

        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
                    if (!empty($name)) {
                        // Remove quotes if present
                        $value = trim($value, '"\'');
                        putenv("$name=$value");
                        $_ENV[$name] = $value;
                    }
                }
            }
        }
        $loaded = true;
    }
}

// Load environment variables
loadEnv();

// Debug settings
define('DEBUG', getenv('APP_DEBUG') === 'true');

// Database settings
define('DB_PATH', getenv('DB_PATH') ?: __DIR__ . '/../database.site');

// Site settings
define('SITE_NAME', getenv('APP_NAME') ?: 'Unity DBT');
define('SITE_URL', getenv('APP_URL') ?: 'http://localhost:8007');

// Logging settings
define('LOG_PATH', __DIR__ . '/../logs/app.log');
define('LOG_LEVEL', 'INFO'); // Available levels: DEBUG, INFO, WARNING, ERROR

// Cache settings
define('CACHE_ENABLED', true);
define('CACHE_PATH', __DIR__ . '/../cache');
define('CACHE_LIFETIME', 3600); // 1 hour

// Upload settings
define('UPLOAD_PATH', getenv('UPLOAD_PATH') ?: __DIR__ . '/../uploads');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf,doc,docx');

// Security settings
define('SESSION_LIFETIME', getenv('SESSION_LIFETIME') ?: 7200); // 2 hours
define('CSRF_PROTECTION', true);
define('PASSWORD_MIN_LENGTH', 8);

// API settings
define('API_RATE_LIMIT', 100); // requests per hour
define('API_TOKEN_LIFETIME', getenv('JWT_LIFETIME') ?: 3600); // 1 hour

// Email settings
define('SMTP_HOST', getenv('MAIL_HOST') ?: '');
define('SMTP_PORT', getenv('MAIL_PORT') ?: 587);
define('SMTP_USERNAME', getenv('MAIL_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('MAIL_PASSWORD') ?: '');
define('SMTP_FROM_EMAIL', getenv('MAIL_FROM_ADDRESS') ?: '');
define('SMTP_FROM_NAME', getenv('MAIL_FROM_NAME') ?: SITE_NAME);

// Social media
define('FACEBOOK_URL', 'https://facebook.com/dbtunity');
define('INSTAGRAM_URL', 'https://instagram.com/dbtunity');
define('TELEGRAM_URL', 'https://t.me/dbtunity');

// Contact information
define('CONTACT_EMAIL', 'contact@unitydbt.com');
define('CONTACT_PHONE', '(555) 123-4567');
define('CONTACT_ADDRESS', '123 Therapy Street, Suite 100, Mental Health City, MH 12345');

// Time zone
date_default_timezone_set('UTC');

// Character encoding
mb_internal_encoding('UTF-8');

// Error reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
