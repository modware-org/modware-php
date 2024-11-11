class GalleryManager {
    constructor(options = {}) {
        this.options = {
            deleteEndpoint: options.deleteEndpoint || 'delete.php',
            downloadEndpoint: options.downloadEndpoint || 'download.php'
        };

        this.container = document.querySelector('.gallery-grid');
        this.searchInput = document.querySelector('.gallery-search-input');
        this.sortSelect = document.querySelector('.sort-select');
        this.lightbox = document.querySelector('.lightbox');
        this.lightboxImage = document.querySelector('.lightbox-image');
        this.lightboxCaption = document.querySelector('.lightbox-caption');
        
        this.currentImageIndex = 0;
        this.images = [];
        
        this.initializeEventListeners();
        this.loadImages();
    }

    initializeEventListeners() {
        // Search functionality
        this.searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));

        // Sort functionality
        this.sortSelect.addEventListener('change', (e) => this.handleSort(e.target.value));

        // Image actions
        this.container.addEventListener('click', (e) => {
            const galleryItem = e.target.closest('.gallery-item');
            if (!galleryItem) return;

            const imagePath = galleryItem.dataset.image;

            if (e.target.closest('.view-btn')) {
                this.openLightbox(imagePath);
            } else if (e.target.closest('.download-btn')) {
                this.downloadImage(imagePath);
            } else if (e.target.closest('.delete-btn')) {
                this.deleteImage(imagePath, galleryItem);
            }
        });

        // Lightbox navigation
        document.querySelector('.lightbox-close').addEventListener('click', () => this.closeLightbox());
        document.querySelector('.lightbox-prev').addEventListener('click', () => this.navigateImage(-1));
        document.querySelector('.lightbox-next').addEventListener('click', () => this.navigateImage(1));

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (!this.lightbox.style.display === 'block') return;

            switch(e.key) {
                case 'Escape':
                    this.closeLightbox();
                    break;
                case 'ArrowLeft':
                    this.navigateImage(-1);
                    break;
                case 'ArrowRight':
                    this.navigateImage(1);
                    break;
            }
        });

        // Close lightbox when clicking outside
        this.lightbox.addEventListener('click', (e) => {
            if (e.target === this.lightbox) {
                this.closeLightbox();
            }
        });
    }

    loadImages() {
        this.images = Array.from(this.container.querySelectorAll('.gallery-item'))
            .map(item => ({
                element: item,
                path: item.dataset.image,
                name: item.querySelector('.image-name').textContent,
                date: new Date(item.querySelector('.image-date').textContent),
                size: this.parseFileSize(item.querySelector('.image-size').textContent)
            }));
    }

    parseFileSize(sizeStr) {
        const [size, unit] = sizeStr.split(' ');
        const units = {'B': 1, 'KB': 1024, 'MB': 1024*1024, 'GB': 1024*1024*1024};
        return parseFloat(size) * units[unit];
    }

    handleSearch(query) {
        const searchTerm = query.toLowerCase();
        this.images.forEach(image => {
            const visible = image.name.toLowerCase().includes(searchTerm);
            image.element.style.display = visible ? '' : 'none';
        });
    }

    handleSort(criteria) {
        const sortedImages = [...this.images].sort((a, b) => {
            switch(criteria) {
                case 'name':
                    return a.name.localeCompare(b.name);
                case 'date':
                    return b.date - a.date;
                case 'size':
                    return b.size - a.size;
                default:
                    return 0;
            }
        });

        // Reorder DOM elements
        sortedImages.forEach(image => {
            this.container.appendChild(image.element);
        });
    }

    openLightbox(imagePath) {
        this.currentImageIndex = this.images.findIndex(img => img.path === imagePath);
        this.updateLightboxContent();
        this.lightbox.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    closeLightbox() {
        this.lightbox.style.display = 'none';
        document.body.style.overflow = '';
        this.lightboxImage.innerHTML = '';
        this.lightboxCaption.textContent = '';
    }

    navigateImage(direction) {
        this.currentImageIndex = (this.currentImageIndex + direction + this.images.length) % this.images.length;
        this.updateLightboxContent();
    }

    updateLightboxContent() {
        const image = this.images[this.currentImageIndex];
        this.lightboxImage.innerHTML = `<img src="${image.path}" alt="${image.name}">`;
        this.lightboxCaption.textContent = image.name;

        // Update navigation buttons visibility
        document.querySelector('.lightbox-prev').style.display = this.images.length > 1 ? '' : 'none';
        document.querySelector('.lightbox-next').style.display = this.images.length > 1 ? '' : 'none';
    }

    downloadImage(imagePath) {
        window.location.href = `${this.options.downloadEndpoint}?file=${encodeURIComponent(imagePath)}&download=1`;
    }

    deleteImage(imagePath, element) {
        if (!confirm('Are you sure you want to delete this image?')) return;

        fetch(this.options.deleteEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ filename: imagePath })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.remove();
                this.images = this.images.filter(img => img.path !== imagePath);
                this.showNotification('Image deleted successfully', 'success');
                
                if (this.images.length === 0) {
                    this.container.innerHTML = '<div class="no-images">No images found</div>';
                }
            } else {
                this.showNotification('Failed to delete image', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('Error deleting image', 'error');
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
}

// Initialize the gallery manager
document.addEventListener('DOMContentLoaded', () => {
    const gallery = new GalleryManager({
        deleteEndpoint: 'delete.php',
        downloadEndpoint: 'download.php'
    });
});
