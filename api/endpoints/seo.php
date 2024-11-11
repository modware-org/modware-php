<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../sections/meta/query.php';

class SEO {
    private $db;
    private $metaQuery;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->metaQuery = new MetaQuery($this->db);
    }

    public function getGlobalSeo() {
        return $this->metaQuery->getMetaByPageId('global');
    }

    public function updateGlobalSeo($data) {
        $data['page_id'] = 'global';
        return ['success' => $this->metaQuery->updateMeta($data)];
    }

    public function getPageSeo($pageId) {
        return $this->metaQuery->getMetaByPageId($pageId);
    }

    public function getPagesSeo() {
        // This would need to be implemented in MetaQuery if needed
        return [];
    }

    public function updatePageSeo($pageId, $data) {
        $data['page_id'] = $pageId;
        return ['success' => $this->metaQuery->updateMeta($data)];
    }
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['page_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request data']);
        exit;
    }

    try {
        $db = Database::getInstance()->getConnection();
        $metaQuery = new MetaQuery($db);
        $success = $metaQuery->updateMeta($data);

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Meta data updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to update meta data']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['page_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Page ID is required']);
        exit;
    }

    try {
        $db = Database::getInstance()->getConnection();
        $metaQuery = new MetaQuery($db);
        $meta = $metaQuery->getMetaByPageId($_GET['page_id']);

        if ($meta) {
            echo json_encode(['success' => true, 'data' => $meta]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Meta data not found']);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
