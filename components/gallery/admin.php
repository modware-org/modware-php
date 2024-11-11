<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../api/Api.php';

class GalleryAdmin {
    private $db;
    private $api;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->api = new Api('gallery', [
            'title',
            'description',
            'image_path',
            'thumbnail_path',
            'alt_text',
            'category',
            'sort_order',
            'created_at',
            'updated_at'
        ]);
    }

    public function getImages($category = null) {
        $query = "SELECT * FROM gallery";
        $params = [];
        if ($category) {
            $query .= " WHERE category = :category ORDER BY sort_order ASC, created_at DESC";
            $params[':category'] = $category;
            return $this->db->query($query, $params);
        }
        return $this->db->query($query . " ORDER BY sort_order ASC, created_at DESC");
    }

    public function deleteImage($id) {
        // Get image info first
        $query = "SELECT image_path, thumbnail_path FROM gallery WHERE id = :id";
        $result = $this->db->query($query, [':id' => $id]);
        $image = $result[0] ?? null;
        
        if ($image) {
            // Delete physical files
            if (file_exists($image['image_path'])) {
                unlink($image['image_path']);
            }
            if (file_exists($image['thumbnail_path'])) {
                unlink($image['thumbnail_path']);
            }
        }
        
        // Delete database record
        $query = "DELETE FROM gallery WHERE id = :id";
        return $this->db->execute($query, [':id' => $id]);
    }

    public function updateSortOrder($id, $newOrder) {
        $query = "UPDATE gallery SET sort_order = :order, updated_at = :updated_at WHERE id = :id";
        return $this->db->execute($query, [
            ':order' => $newOrder,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $id
        ]);
    }

    public function getStats() {
        $query = "SELECT 
            COUNT(*) as total_images,
            COUNT(DISTINCT category) as total_categories,
            MAX(created_at) as last_upload,
            SUM(CASE WHEN thumbnail_path IS NOT NULL THEN 1 ELSE 0 END) as thumbnails_count
            FROM gallery";
        $result = $this->db->query($query);
        return $result[0] ?? null;
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM gallery WHERE category IS NOT NULL ORDER BY category";
        return $this->db->query($query);
    }

    public function updateMetadata($id, $data) {
        $allowedFields = ['title', 'description', 'alt_text', 'category', 'sort_order'];
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $updates[] = "updated_at = :updated_at";
        $params[':updated_at'] = date('Y-m-d H:i:s');
        $params[':id'] = $id;
        
        $query = "UPDATE gallery SET " . implode(', ', $updates) . " WHERE id = :id";
        return $this->db->execute($query, $params);
    }
}

// Initialize admin interface
$galleryAdmin = new GalleryAdmin();

// Handle AJAX requests
if (isset($_POST['action'])) {
    $response = ['success' => false];
    
    switch ($_POST['action']) {
        case 'get_images':
            $category = isset($_POST['category']) ? $_POST['category'] : null;
            $response['data'] = $galleryAdmin->getImages($category);
            $response['success'] = true;
            break;
            
        case 'delete_image':
            if (isset($_POST['id'])) {
                $response['success'] = $galleryAdmin->deleteImage($_POST['id']);
            }
            break;
            
        case 'update_sort':
            if (isset($_POST['id']) && isset($_POST['sort_order'])) {
                $response['success'] = $galleryAdmin->updateSortOrder(
                    $_POST['id'],
                    $_POST['sort_order']
                );
            }
            break;
            
        case 'update_metadata':
            if (isset($_POST['id']) && isset($_POST['data'])) {
                $response['success'] = $galleryAdmin->updateMetadata(
                    $_POST['id'],
                    $_POST['data']
                );
            }
            break;
            
        case 'get_stats':
            $response['data'] = $galleryAdmin->getStats();
            $response['success'] = true;
            break;
            
        case 'get_categories':
            $response['data'] = $galleryAdmin->getCategories();
            $response['success'] = true;
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
