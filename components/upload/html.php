<?php
class UploadComponent {
    private $targetDir;
    private $allowedTypes;
    private $maxSize;

    public function __construct($targetDir = 'uploads/', $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'], $maxSize = 5242880) {
        $this->targetDir = $targetDir;
        $this->allowedTypes = $allowedTypes;
        $this->maxSize = $maxSize;
    }

    public function render($multiple = false, $accept = null) {
        $acceptAttr = $accept ? "accept=\"$accept\"" : "";
        $multipleAttr = $multiple ? "multiple" : "";
        
        return <<<HTML
        <div class="upload-component">
            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="upload-text">
                    <h3>Drag & Drop files here</h3>
                    <p>or</p>
                    <label class="upload-button">
                        Browse Files
                        <input type="file" name="files[]" $multipleAttr $acceptAttr class="file-input" style="display: none;">
                    </label>
                </div>
            </div>
            <div class="upload-preview" id="uploadPreview"></div>
            <div class="upload-progress" style="display: none;">
                <div class="progress-bar"></div>
                <div class="progress-text">0%</div>
            </div>
        </div>
HTML;
    }

    public function handleUpload() {
        $response = ['success' => false, 'message' => '', 'files' => []];
        
        if (!empty($_FILES['files'])) {
            foreach ($_FILES['files']['tmp_name'] as $key => $tmp_name) {
                $file_name = $_FILES['files']['name'][$key];
                $file_size = $_FILES['files']['size'][$key];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                // Validate file
                if (!in_array($file_ext, $this->allowedTypes)) {
                    $response['message'] = "Invalid file type: $file_ext";
                    continue;
                }

                if ($file_size > $this->maxSize) {
                    $response['message'] = "File too large: $file_name";
                    continue;
                }

                // Generate unique filename
                $new_file_name = uniqid() . '.' . $file_ext;
                $target_file = $this->targetDir . $new_file_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $response['files'][] = [
                        'original_name' => $file_name,
                        'saved_name' => $new_file_name,
                        'path' => $target_file
                    ];
                    $response['success'] = true;
                }
            }
        }

        return json_encode($response);
    }
}
?>
