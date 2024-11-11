<?php
class GalleryComponent {
    private $imageDir;
    private $allowedTypes;
    private $thumbnailSize;
    
    public function __construct($imageDir = 'uploads/images/', $thumbnailSize = 300) {
        $this->imageDir = rtrim($imageDir, '/') . '/';
        $this->allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $this->thumbnailSize = $thumbnailSize;
    }
    
    public function render($columns = 4) {
        $images = $this->getImages();
        
        $output = <<<HTML
        <div class="gallery-component">
            <div class="gallery-filters">
                <div class="gallery-search">
                    <input type="text" placeholder="Search images..." class="gallery-search-input">
                </div>
                <div class="gallery-sort">
                    <select class="sort-select">
                        <option value="name">Name</option>
                        <option value="date">Date</option>
                        <option value="size">Size</option>
                    </select>
                </div>
            </div>
            <div class="gallery-grid" style="--columns: {$columns}">
HTML;
        
        if (empty($images)) {
            $output .= '<div class="no-images">No images found</div>';
        } else {
            foreach ($images as $image) {
                $thumbnail = $this->getThumbnailUrl($image['path']);
                $output .= <<<HTML
                <div class="gallery-item" data-image="{$image['path']}">
                    <div class="image-wrapper">
                        <img src="{$thumbnail}" alt="{$image['name']}" loading="lazy">
                        <div class="image-overlay">
                            <div class="image-actions">
                                <button class="action-btn view-btn" title="View">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button class="action-btn download-btn" title="Download">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button class="action-btn delete-btn" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="image-info">
                        <div class="image-name" title="{$image['name']}">{$image['name']}</div>
                        <div class="image-meta">
                            <span class="image-size">{$this->formatFileSize($image['size'])}</span>
                            <span class="image-date">{$this->formatDate($image['modified'])}</span>
                        </div>
                    </div>
                </div>
HTML;
            }
        }
        
        $output .= <<<HTML
            </div>
            <div class="lightbox" style="display: none;">
                <div class="lightbox-content">
                    <button class="lightbox-close">&times;</button>
                    <button class="lightbox-prev">&lt;</button>
                    <button class="lightbox-next">&gt;</button>
                    <div class="lightbox-image"></div>
                    <div class="lightbox-caption"></div>
                </div>
            </div>
        </div>
HTML;
        
        return $output;
    }
    
    private function getImages() {
        $images = [];
        if (is_dir($this->imageDir)) {
            $items = scandir($this->imageDir);
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') continue;
                
                $path = $this->imageDir . $item;
                if (is_file($path)) {
                    $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                    if (in_array($ext, $this->allowedTypes)) {
                        $images[] = [
                            'name' => $item,
                            'path' => $path,
                            'size' => filesize($path),
                            'modified' => filemtime($path),
                            'dimensions' => $this->getImageDimensions($path)
                        ];
                    }
                }
            }
        }
        return $images;
    }
    
    private function getImageDimensions($path) {
        $dimensions = getimagesize($path);
        return $dimensions ? [
            'width' => $dimensions[0],
            'height' => $dimensions[1]
        ] : null;
    }
    
    private function getThumbnailUrl($path) {
        // In a real implementation, you might want to generate and cache thumbnails
        // For now, we'll return the original image path
        return $path;
    }
    
    private function formatFileSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($size, 1024));
        return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }
    
    private function formatDate($timestamp) {
        return date('Y-m-d H:i', $timestamp);
    }
    
    public function handleDelete($filename) {
        $filepath = $this->imageDir . basename($filename);
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}
?>
