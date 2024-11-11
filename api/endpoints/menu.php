<?php
require_once __DIR__ . '/../../sections/menu/query.php';

class MenuEndpoint {
    private $query;

    public function __construct() {
        $this->query = new MenuQuery();
    }

    public function handle($method, $params = []) {
        switch ($method) {
            case 'GET':
                if (isset($params['id'])) {
                    return $this->getMenuItem($params['id']);
                }
                // Pass true for adminContext to get all items including inactive ones
                $menuData = $this->query->getMenuData(true);
                return $menuData['items'] ?? [];

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) {
                    http_response_code(400);
                    return ['error' => 'Invalid request data'];
                }
                // Convert checkbox value to integer for is_active
                if (isset($data['is_active'])) {
                    $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                }
                $result = $this->query->addMenuItem($data);
                return ['success' => (bool)$result];

            case 'PUT':
                if (!isset($params['id'])) {
                    http_response_code(400);
                    return ['error' => 'ID is required'];
                }
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) {
                    http_response_code(400);
                    return ['error' => 'Invalid request data'];
                }
                // Convert checkbox value to integer for is_active
                if (isset($data['is_active'])) {
                    $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
                }
                $result = $this->query->updateMenuItem($params['id'], $data);
                return ['success' => (bool)$result];

            case 'DELETE':
                if (!isset($params['id'])) {
                    http_response_code(400);
                    return ['error' => 'ID is required'];
                }
                $result = $this->query->deleteMenuItem($params['id']);
                return ['success' => (bool)$result];

            default:
                http_response_code(405);
                return ['error' => 'Method not allowed'];
        }
    }

    private function getMenuItem($id) {
        // Use admin context to get all items including inactive ones
        $items = $this->query->getMenuData(true)['items'];
        $found = $this->findMenuItem($items, $id);
        
        if ($found) {
            return $found;
        }
        
        http_response_code(404);
        return ['error' => 'Menu item not found'];
    }

    private function findMenuItem($items, $id) {
        foreach ($items as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
            if (isset($item['children'])) {
                $found = $this->findMenuItem($item['children'], $id);
                if ($found) {
                    return $found;
                }
            }
        }
        return null;
    }
}

// Register the endpoint
return new MenuEndpoint();
