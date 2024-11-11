<?php
class VisualizerFilesQuery {
    private $baseDir;
    private $allowedTypes;
    
    public function __construct($baseDir = 'uploads/', $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
        $this->baseDir = rtrim($baseDir, '/') . '/';
        $this->allowedTypes = $allowedTypes;
    }
    
    public function getFiles() {
        $files = [];
        if (is_dir($this->baseDir)) {
            $items = scandir($this->baseDir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $path = $this->baseDir . $item;
                if (is_file($path)) {
                    $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    if (in_array($ext, $this->allowedTypes)) {
                        $files[] = [
                            'name' => $item,
                            'path' => $path,
                            'type' => $ext,
                            'size' => filesize($path),
                            'modified' => filemtime($path)
                        ];
                    }
                }
            }
        }
        return $files;
    }
    
    public function handleDelete($filename) {
        $filepath = $this->baseDir . basename($filename);
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
    
    public function formatFileSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($size, 1024));
        return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }
    
    public function getFileIcon($type) {
        switch ($type) {
            case 'pdf':
                return '<i class="far fa-file-pdf"></i>';
            case 'jpg':
            case 'jpeg':
            case 'png':
                return '<i class="far fa-file-image"></i>';
            default:
                return '<i class="far fa-file"></i>';
        }
    }
}
?>
