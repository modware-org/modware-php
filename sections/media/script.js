class MediaManager {
    constructor() {
        this.tabs = document.querySelectorAll('.tab-btn');
        this.panes = document.querySelectorAll('.tab-pane');
        this.activeComponents = new Set();
        
        this.initializeEventListeners();
        this.initializeComponents();
    }

    initializeEventListeners() {
        this.tabs.forEach(tab => {
            tab.addEventListener('click', () => this.switchTab(tab.dataset.tab));
        });
    }

    switchTab(tabId) {
        // Update tab buttons
        this.tabs.forEach(tab => {
            tab.classList.toggle('active', tab.dataset.tab === tabId);
        });

        // Update tab panes
        this.panes.forEach(pane => {
            const isActive = pane.id === tabId;
            pane.classList.toggle('active', isActive);

            // Initialize components when tab becomes active
            if (isActive && !this.activeComponents.has(tabId)) {
                this.initializeTabComponents(tabId);
                this.activeComponents.add(tabId);
            }
        });
    }

    initializeComponents() {
        // Initialize components for the active tab
        const activeTab = document.querySelector('.tab-btn.active');
        if (activeTab) {
            this.initializeTabComponents(activeTab.dataset.tab);
            this.activeComponents.add(activeTab.dataset.tab);
        }
    }

    initializeTabComponents(tabId) {
        switch(tabId) {
            case 'upload':
                new UploadHandler({
                    uploadUrl: 'upload.php',
                    maxFileSize: 5242880, // 5MB
                    allowedTypes: ['jpg', 'jpeg', 'png', 'pdf'],
                    multiple: true
                });
                break;

            case 'files':
                new FilesManager({
                    deleteEndpoint: 'delete.php',
                    downloadEndpoint: 'download.php'
                });
                break;

            case 'gallery':
                new GalleryManager({
                    deleteEndpoint: 'delete.php',
                    downloadEndpoint: 'download.php'
                });
                break;
        }
    }

    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 2000);
    }

    // Helper method to handle component communication
    handleUploadComplete(files) {
        // Refresh files and gallery components if they're initialized
        if (this.activeComponents.has('files')) {
            const filesManager = document.querySelector('.files-component')?.__filesManager;
            if (filesManager) filesManager.refreshFiles();
        }
        
        if (this.activeComponents.has('gallery')) {
            const galleryManager = document.querySelector('.gallery-component')?.__galleryManager;
            if (galleryManager) galleryManager.loadImages();
        }
        
        this.showNotification(`Successfully uploaded ${files.length} file(s)`);
    }

    // Helper method to handle errors
    handleError(message) {
        this.showNotification(message, 'error');
    }
}

// Initialize the media manager
document.addEventListener('DOMContentLoaded', () => {
    const mediaManager = new MediaManager();
    
    // Store the instance globally for potential external access
    window.__mediaManager = mediaManager;
});
