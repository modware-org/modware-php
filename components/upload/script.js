class UploadHandler {
    constructor(options = {}) {
        this.options = {
            uploadUrl: options.uploadUrl || 'upload.php',
            maxFileSize: options.maxFileSize || 5242880, // 5MB
            allowedTypes: options.allowedTypes || ['jpg', 'jpeg', 'png', 'pdf'],
            multiple: options.multiple || false
        };

        this.uploadArea = document.getElementById('uploadArea');
        this.uploadPreview = document.getElementById('uploadPreview');
        this.fileInput = document.querySelector('.file-input');
        this.progressBar = document.querySelector('.progress-bar');
        this.progressText = document.querySelector('.progress-text');
        this.progressContainer = document.querySelector('.upload-progress');

        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            this.uploadArea.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });

        this.uploadArea.addEventListener('dragover', () => {
            this.uploadArea.classList.add('dragover');
        });

        this.uploadArea.addEventListener('dragleave', () => {
            this.uploadArea.classList.remove('dragover');
        });

        this.uploadArea.addEventListener('drop', (e) => {
            this.uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            this.handleFiles(files);
        });

        // File input change event
        this.fileInput.addEventListener('change', () => {
            this.handleFiles(this.fileInput.files);
        });
    }

    handleFiles(files) {
        const validFiles = Array.from(files).filter(file => this.validateFile(file));
        
        if (validFiles.length === 0) {
            this.showError('No valid files selected');
            return;
        }

        this.showPreview(validFiles);
        this.uploadFiles(validFiles);
    }

    validateFile(file) {
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        if (!this.options.allowedTypes.includes(fileExt)) {
            this.showError(`File type ${fileExt} not allowed`);
            return false;
        }

        if (file.size > this.options.maxFileSize) {
            this.showError('File too large');
            return false;
        }

        return true;
    }

    showPreview(files) {
        this.uploadPreview.innerHTML = '';
        
        files.forEach(file => {
            const reader = new FileReader();
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';

            reader.onload = (e) => {
                if (file.type.startsWith('image/')) {
                    previewItem.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <div class="file-name">${file.name}</div>
                        <span class="remove-file">&times;</span>
                    `;
                } else {
                    previewItem.innerHTML = `
                        <div class="file-icon">📄</div>
                        <div class="file-name">${file.name}</div>
                        <span class="remove-file">&times;</span>
                    `;
                }
            };

            reader.readAsDataURL(file);
            this.uploadPreview.appendChild(previewItem);
        });
    }

    uploadFiles(files) {
        const formData = new FormData();
        files.forEach(file => {
            formData.append('files[]', file);
        });

        this.progressContainer.style.display = 'block';
        
        fetch(this.options.uploadUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showSuccess('Files uploaded successfully');
            } else {
                this.showError(data.message || 'Upload failed');
            }
        })
        .catch(error => {
            this.showError('Upload failed: ' + error.message);
        })
        .finally(() => {
            this.progressContainer.style.display = 'none';
            this.resetProgress();
        });
    }

    updateProgress(percent) {
        this.progressBar.style.width = `${percent}%`;
        this.progressText.textContent = `${percent}%`;
    }

    resetProgress() {
        this.updateProgress(0);
    }

    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'upload-error';
        errorDiv.textContent = message;
        this.uploadArea.appendChild(errorDiv);
        setTimeout(() => errorDiv.remove(), 3000);
    }

    showSuccess(message) {
        const successDiv = document.createElement('div');
        successDiv.className = 'upload-success';
        successDiv.textContent = message;
        this.uploadArea.appendChild(successDiv);
        setTimeout(() => successDiv.remove(), 3000);
    }
}

// Initialize the upload handler
document.addEventListener('DOMContentLoaded', () => {
    const uploader = new UploadHandler({
        uploadUrl: 'upload.php',
        maxFileSize: 5242880,
        allowedTypes: ['jpg', 'jpeg', 'png', 'pdf'],
        multiple: true
    });
});
