<?php
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/Api.php';
require_once __DIR__ . '/endpoints/auth.php';
require_once __DIR__ . '/endpoints/seo.php';
require_once __DIR__ . '/endpoints/menu.php';
require_once __DIR__ . '/endpoints/sections.php';
require_once __DIR__ . '/endpoints/sitemap.php';

header('Content-Type: application/json');

// Get the requested endpoint from the URL
$requestUri = $_SERVER['REQUEST_URI'];
$baseUri = dirname($_SERVER['SCRIPT_NAME']);
$endpoint = str_replace($baseUri, '', $requestUri);
$endpoint = trim($endpoint, '/');

// Parse endpoint parts
$parts = explode('/', $endpoint);
$resource = $parts[0] ?? '';
$id = $parts[1] ?? null;
$action = $parts[2] ?? null;

// Handle authentication
if ($resource === 'auth') {
    $auth = new Auth();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
            exit;
        }
        
        if (!isset($data['username']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing credentials']);
            exit;
        }
        
        $result = $auth->login($data['username'], $data['password']);
        if (!$result['success']) {
            http_response_code(401);
        }
        echo json_encode($result);
        exit;
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        exit;
    }
}

// Handle SEO endpoints
if ($resource === 'seo') {
    try {
        $seo = new SEO();
        
        if ($id === 'global') {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                echo json_encode($seo->getGlobalSeo());
                exit;
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) {
                    throw new Exception('Invalid JSON data');
                }
                echo json_encode($seo->updateGlobalSeo($data));
                exit;
            }
        } elseif ($id === 'pages') {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if ($action) {
                    echo json_encode($seo->getPageSeo($action));
                } else {
                    echo json_encode($seo->getPagesSeo());
                }
                exit;
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $action) {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) {
                    throw new Exception('Invalid JSON data');
                }
                echo json_encode($seo->updatePageSeo($action, $data));
                exit;
            }
        }
        
        throw new Exception('Invalid SEO endpoint');
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Handle Menu endpoints
if ($resource === 'menu') {
    try {
        $menu = new MenuEndpoint();
        $params = ['id' => $id];
        $result = $menu->handle($_SERVER['REQUEST_METHOD'], $params);
        if (is_array($result)) {
            echo json_encode($result);
        } else {
            echo json_encode(['success' => true, 'data' => $result]);
        }
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Handle Sections endpoints
if ($resource === 'sections') {
    try {
        $sections = new SectionsEndpoint();
        $params = ['id' => $id];
        $result = $sections->handle($_SERVER['REQUEST_METHOD'], $params);
        echo json_encode(['success' => true, 'data' => $result]);
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Handle Sitemap endpoints
if ($resource === 'sitemap') {
    try {
        $sitemap = new SitemapEndpoint();
        $params = ['action' => $id];
        $result = $sitemap->handle($_SERVER['REQUEST_METHOD'], $params);
        echo json_encode(['success' => true, 'data' => $result]);
        exit;
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Define allowed fields for each resource
$allowedFields = [
    'users' => ['username', 'email', 'is_admin', 'created_at'],
    'content' => ['title', 'content', 'slug', 'created_at', 'updated_at'],
    'media' => ['filename', 'type', 'path', 'created_at'],
    'settings' => ['key', 'value', 'created_at', 'updated_at']
];

try {
    if (empty($resource)) {
        throw new Exception('No resource specified');
    }

    if (!isset($allowedFields[$resource])) {
        throw new Exception('Invalid resource');
    }

    // Create API instance for the requested resource
    $api = new Api($resource, $allowedFields[$resource]);
    
    // Handle the request
    $api->handleRequest();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
