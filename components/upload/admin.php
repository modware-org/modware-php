<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../api/Api.php';

class UploadAdmin {
    private $db;
    private $api;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->api = new Api('uploads', [
            'file_name',
            'file_path',
            'file_size',
            'mime_type',
            'created_at',
            'updated_at'
        ]);
    }

    public function getUploads() {
        $query = "SELECT * FROM uploads ORDER BY created_at DESC";
        return $this->db->query($query);
    }

    public function deleteUpload($id) {
        $query = "DELETE FROM uploads WHERE id = ?";
        return $this->db->query($query, [$id]);
    }

    public function getStats() {
        $query = "SELECT 
            COUNT(*) as total_uploads,
            SUM(file_size) as total_size,
            MAX(created_at) as last_upload
            FROM uploads";
        return $this->db->query($query);
    }
}

// Initialize admin interface
$uploadAdmin = new UploadAdmin();

// Handle AJAX requests
if (isset($_POST['action'])) {
    $response = ['success' => false];
    
    switch ($_POST['action']) {
        case 'get_uploads':
            $response['data'] = $uploadAdmin->getUploads();
            $response['success'] = true;
            break;
            
        case 'delete_upload':
            if (isset($_POST['id'])) {
                $response['success'] = $uploadAdmin->deleteUpload($_POST['id']);
            }
            break;
            
        case 'get_stats':
            $response['data'] = $uploadAdmin->getStats();
            $response['success'] = true;
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>
