class FilesManager {
    constructor(options = {}) {
        this.options = {
            deleteEndpoint: options.deleteEndpoint || 'delete.php',
            downloadEndpoint: options.downloadEndpoint || 'download.php'
        };

        this.container = document.getElementById('filesContainer');
        this.searchInput = document.querySelector('.search-input');
        this.viewButtons = document.querySelectorAll('.view-btn');
        this.modal = document.querySelector('.file-preview-modal');
        this.modalContent = document.querySelector('.preview-content');
        this.closeModal = document.querySelector('.close-modal');

        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // View toggle
        this.viewButtons.forEach(btn => {
            btn.addEventListener('click', () => this.toggleView(btn.dataset.view));
        });

        // Search functionality
        this.searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));

        // File actions
        this.container.addEventListener('click', (e) => {
            const fileItem = e.target.closest('.file-item');
            if (!fileItem) return;

            const filename = fileItem.dataset.filename;

            if (e.target.closest('.preview-btn')) {
                this.previewFile(filename);
            } else if (e.target.closest('.download-btn')) {
                this.downloadFile(filename);
            } else if (e.target.closest('.delete-btn')) {
                this.deleteFile(filename, fileItem);
            }
        });

        // Modal close
        this.closeModal.addEventListener('click', () => this.closePreviewModal());
        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) this.closePreviewModal();
        });

        // Keyboard events
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.style.display === 'block') {
                this.closePreviewModal();
            }
        });
    }

    toggleView(viewType) {
        this.container.className = `files-container ${viewType}-view`;
        this.viewButtons.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === viewType);
        });
    }

    handleSearch(query) {
        const items = this.container.querySelectorAll('.file-item');
        const searchTerm = query.toLowerCase();

        items.forEach(item => {
            const filename = item.dataset.filename.toLowerCase();
            item.style.display = filename.includes(searchTerm) ? '' : 'none';
        });
    }

    previewFile(filename) {
        const ext = filename.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(ext);
        const isPdf = ext === 'pdf';

        this.modalContent.innerHTML = '';

        if (isImage) {
            const img = document.createElement('img');
            img.src = `${this.options.downloadEndpoint}?file=${encodeURIComponent(filename)}`;
            img.alt = filename;
            this.modalContent.appendChild(img);
        } else if (isPdf) {
            const embed = document.createElement('embed');
            embed.src = `${this.options.downloadEndpoint}?file=${encodeURIComponent(filename)}`;
            embed.type = 'application/pdf';
            embed.style.width = '100%';
            embed.style.height = '80vh';
            this.modalContent.appendChild(embed);
        } else {
            this.modalContent.innerHTML = `
                <div class="preview-fallback">
                    <i class="far fa-file fa-3x"></i>
                    <p>Preview not available for this file type</p>
                    <p class="filename">${filename}</p>
                </div>
            `;
        }

        this.modal.style.display = 'block';
    }

    closePreviewModal() {
        this.modal.style.display = 'none';
        this.modalContent.innerHTML = '';
    }

    downloadFile(filename) {
        window.location.href = `${this.options.downloadEndpoint}?file=${encodeURIComponent(filename)}&download=1`;
    }

    deleteFile(filename, element) {
        if (!confirm('Are you sure you want to delete this file?')) return;

        fetch(this.options.deleteEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ filename })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.remove();
                this.showNotification('File deleted successfully', 'success');
                
                // Check if there are no more files
                if (this.container.children.length === 0) {
                    this.container.innerHTML = '<div class="no-files">No files found</div>';
                }
            } else {
                this.showNotification('Failed to delete file', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Error deleting file', 'error');
        });
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    refreshFiles() {
        // This method can be implemented to fetch and update the file list
        // via AJAX if needed
    }
}

// Initialize the files manager
document.addEventListener('DOMContentLoaded', () => {
    const filesManager = new FilesManager({
        deleteEndpoint: 'delete.php',
        downloadEndpoint: 'download.php'
    });
});
