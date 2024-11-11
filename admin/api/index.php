<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Api.php';

// Set CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Parse request path
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/admin/api';
$path = parse_url($request_uri, PHP_URL_PATH);
$endpoint = substr($path, strlen($base_path));

// Remove trailing slash if present
$endpoint = rtrim($endpoint, '/');

// Initialize API
$api = new Api();

// Route request to appropriate endpoint
try {
    switch ($endpoint) {
        case '/auth':
            require __DIR__ . '/endpoints/auth.php';
            break;
        case '/menu':
            require __DIR__ . '/endpoints/menu.php';
            break;
        case '/pages':
            require __DIR__ . '/endpoints/pages.php';
            break;
        case '/sections':
            require __DIR__ . '/endpoints/sections.php';
            break;
        case '/seo':
            require __DIR__ . '/endpoints/seo.php';
            break;
        case '/sitemap':
            require __DIR__ . '/endpoints/sitemap.php';
            break;
        default:
            http_response_code(404);
            echo json_encode(['error' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
