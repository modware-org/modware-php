<?php
class FilesComponent {
    private $baseDir;
    private $allowedTypes;
    
    public function __construct($baseDir = 'uploads/', $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf']) {
        $this->baseDir = rtrim($baseDir, '/') . '/';
        $this->allowedTypes = $allowedTypes;
    }
    
    public function render($viewType = 'grid') {
        require_once 'sections/visualizer/query.php';
        $filesQuery = new VisualizerFilesQuery($this->baseDir, $this->allowedTypes);
        $files = $filesQuery->getFiles();
        
        $output = <<<HTML
        <div class="files-component">
            <div class="files-header">
                <div class="view-toggle">
                    <button class="view-btn grid-view" data-view="grid">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="view-btn list-view" data-view="list">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
                <div class="files-search">
                    <input type="text" placeholder="Search files..." class="search-input">
                </div>
            </div>
            <div class="files-container {$viewType}-view" id="filesContainer">
HTML;
        
        if (empty($files)) {
            $output .= '<div class="no-files">No files found</div>';
        } else {
            foreach ($files as $file) {
                $fileIcon = $filesQuery->getFileIcon($file['type']);
                $fileSize = $filesQuery->formatFileSize($file['size']);
                $fileDate = date('Y-m-d H:i', $file['modified']);
                
                if ($viewType === 'grid') {
                    $output .= <<<HTML
                    <div class="file-item" data-filename="{$file['name']}">
                        <div class="file-preview">
                            {$fileIcon}
                        </div>
                        <div class="file-info">
                            <div class="file-name" title="{$file['name']}">{$file['name']}</div>
                            <div class="file-meta">{$fileSize}</div>
                        </div>
                        <div class="file-actions">
                            <button class="action-btn preview-btn" title="Preview">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn download-btn" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
HTML;
                } else {
                    $output .= <<<HTML
                    <div class="file-item" data-filename="{$file['name']}">
                        <div class="file-icon">
                            {$fileIcon}
                        </div>
                        <div class="file-name" title="{$file['name']}">{$file['name']}</div>
                        <div class="file-size">{$fileSize}</div>
                        <div class="file-date">{$fileDate}</div>
                        <div class="file-actions">
                            <button class="action-btn preview-btn" title="Preview">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="action-btn download-btn" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                            <button class="action-btn delete-btn" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
HTML;
                }
            }
        }
        
        $output .= <<<HTML
            </div>
            <div class="file-preview-modal" style="display: none;">
                <div class="modal-content">
                    <span class="close-modal">&times;</span>
                    <div class="preview-content"></div>
                </div>
            </div>
        </div>
HTML;
        
        return $output;
    }
}
?>
