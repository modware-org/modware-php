<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../api/Api.php';

class FilesAdmin {
    private $db;
    private $api;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->api = new Api('files', [
            'file_name',
            'original_name',
            'file_path',
            'file_size',
            'mime_type',
            'category',
            'created_at',
            'updated_at'
        ]);
    }

    public function getFiles($category = null) {
        $query = "SELECT * FROM files";
        $params = [];
        if ($category) {
            $query .= " WHERE category = :category";
            $params[':category'] = $category;
        }
        return $this->db->query($query, $params);
    }

    public function deleteFile($id) {
        // Get file info first
        $query = "SELECT file_path FROM files WHERE id = :id";
        $result = $this->db->query($query, [':id' => $id]);
        $file = $result[0] ?? null;
        
        if ($file && file_exists($file['file_path'])) {
            unlink($file['file_path']); // Delete physical file
        }
        
        // Delete database record
        $query = "DELETE FROM files WHERE id = :id";
        return $this->db->execute($query, [':id' => $id]);
    }

    public function getStats() {
        $query = "SELECT 
            COUNT(*) as total_files,
            SUM(file_size) as total_size,
            COUNT(DISTINCT category) as total_categories,
            MAX(created_at) as last_upload
            FROM files";
        $result = $this->db->query($query);
        return $result[0] ?? null;
    }

    public function getCategories() {
        $query = "SELECT DISTINCT category FROM files WHERE category IS NOT NULL";
        return $this->db->query($query);
    }
}

// Initialize admin interface
$filesAdmin = new FilesAdmin();

// Handle AJAX requests
if (isset($_POST['action'])) {
    $response = ['success' => false];
    
    switch ($_POST['action']) {
        case 'get_files':
            $category = isset($_POST['category']) ? $_POST['category'] : null;
            $response['data'] = $filesAdmin->getFiles($category);
            $response['success'] = true;
            break;
            
        case 'delete_file':
            if (isset($_POST['id'])) {
                $response['success'] = $filesAdmin->deleteFile($_POST['id']);
            }
            break;
            
        case 'get_stats':
            $response['data'] = $filesAdmin->getStats();
            $response['success'] = true;
            break;
            
        case 'get_categories':
            $response['data'] = $filesAdmin->getCategories();
            $response['success'] = true;
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
