<?php
require_once __DIR__ . '/../../components/upload/html.php';
require_once __DIR__ . '/../../components/files/html.php';
require_once __DIR__ . '/../../components/gallery/html.php';

class MediaSection {
    private $uploadComponent;
    private $filesComponent;
    private $galleryComponent;
    
    public function __construct() {
        $this->uploadComponent = new UploadComponent('uploads/');
        $this->filesComponent = new FilesComponent('uploads/');
        $this->galleryComponent = new GalleryComponent('uploads/images/');
    }
    
    public function render() {
        $output = <<<HTML
        <section class="media-section">
            <div class="media-tabs">
                <button class="tab-btn active" data-tab="upload">Upload Files</button>
                <button class="tab-btn" data-tab="files">File Manager</button>
                <button class="tab-btn" data-tab="gallery">Image Gallery</button>
            </div>
            
            <div class="tab-content">
                <div class="tab-pane active" id="upload">
                    <h2>Upload Files</h2>
                    {$this->uploadComponent->render(true)}
                </div>
                
                <div class="tab-pane" id="files">
                    <h2>File Manager</h2>
                    {$this->filesComponent->render()}
                </div>
                
                <div class="tab-pane" id="gallery">
                    <h2>Image Gallery</h2>
                    {$this->galleryComponent->render(4)}
                </div>
            </div>
        </section>
        
        <!-- Include required CSS -->
        <link rel="stylesheet" href="/components/upload/style.css">
        <link rel="stylesheet" href="/components/files/style.css">
        <link rel="stylesheet" href="/components/gallery/style.css">
        <link rel="stylesheet" href="/sections/media/style.css">
        
        <!-- Include required JavaScript -->
        <script src="/components/upload/script.js"></script>
        <script src="/components/files/script.js"></script>
        <script src="/components/gallery/script.js"></script>
        <script src="/sections/media/script.js"></script>
HTML;
        
        return $output;
    }
}
?>
