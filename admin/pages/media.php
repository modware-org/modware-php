<div class="content-header">
    <h1>Media Library</h1>
    <button class="btn btn-primary" onclick="document.getElementById('uploadInput').click()">Upload Media</button>
    <input type="file" id="uploadInput" style="display: none" multiple onchange="handleFileUpload(event)">
</div>

<div class="card">
    <div class="media-filters">
        <div class="search-box">
            <input type="text" id="searchMedia" placeholder="Search media..." class="form-control" onkeyup="filterMedia()">
        </div>
        <div class="filter-buttons">
            <button class="filter-btn active" onclick="filterByType('all')">All</button>
            <button class="filter-btn" onclick="filterByType('image')">Images</button>
            <button class="filter-btn" onclick="filterByType('document')">Documents</button>
            <button class="filter-btn" onclick="filterByType('other')">Other</button>
        </div>
    </div>

    <div class="media-grid" id="mediaGrid">
        Loading...
    </div>

    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="loading-spinner"></div>
        <div class="loading-text">Uploading...</div>
    </div>
</div>

<!-- Media Details Modal -->
<div id="mediaDetailsModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Media Details</h2>
            <button class="btn btn-text" onclick="toggleModal('mediaDetailsModal', false)">&times;</button>
        </div>
        <div class="media-details-content">
            <div class="media-preview">
                <img id="mediaPreview" src="" alt="Media preview">
            </div>
            <div class="media-info">
                <form id="mediaDetailsForm" onsubmit="handleMediaUpdate(event)">
                    <input type="hidden" id="mediaId" name="id">
                    <div class="form-group">
                        <label for="mediaFilename">Filename</label>
                        <input type="text" id="mediaFilename" name="filename" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="mediaOriginalFilename">Original Filename</label>
                        <input type="text" id="mediaOriginalFilename" name="original_filename" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="mediaMimeType">MIME Type</label>
                        <input type="text" id="mediaMimeType" name="mime_type" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="mediaFileSize">File Size</label>
                        <input type="text" id="mediaFileSize" name="file_size" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label for="mediaPath">File Path</label>
                        <input type="text" id="mediaPath" name="path" class="form-control" readonly>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="copyToClipboard(document.getElementById('mediaPath').value)">
                            Copy Path
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="mediaCreatedAt">Upload Date</label>
                        <input type="text" id="mediaCreatedAt" name="created_at" class="form-control" readonly>
                    </div>
                </form>
                <div class="media-actions">
                    <button class="btn btn-danger" onclick="deleteMedia(document.getElementById('mediaId').value)">Delete</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadMedia);

let mediaItems = [];
let currentFilter = 'all';
let searchQuery = '';

async function loadMedia() {
    try {
        mediaItems = await handleApiRequest('media');
        renderMedia();
    } catch (error) {
        console.error('Error loading media:', error);
    }
}

function renderMedia() {
    const grid = document.getElementById('mediaGrid');
    
    let filteredItems = mediaItems;
    
    // Apply type filter
    if (currentFilter !== 'all') {
        filteredItems = filteredItems.filter(item => getMediaType(item.mime_type) === currentFilter);
    }
    
    // Apply search filter
    if (searchQuery) {
        filteredItems = filteredItems.filter(item => 
            item.original_filename.toLowerCase().includes(searchQuery.toLowerCase())
        );
    }
    
    if (filteredItems.length === 0) {
        grid.innerHTML = '<div class="no-media">No media files found</div>';
        return;
    }

    grid.innerHTML = filteredItems.map(item => `
        <div class="media-item" onclick="showMediaDetails(${item.id})">
            ${getMediaPreview(item)}
            <div class="media-item-info">
                <div class="media-item-name">${item.original_filename}</div>
                <div class="media-item-meta">${formatFileSize(item.file_size)}</div>
            </div>
        </div>
    `).join('');
}

function getMediaPreview(item) {
    const type = getMediaType(item.mime_type);
    
    if (type === 'image') {
        return `<img src="${item.path}" alt="${item.original_filename}">`;
    }
    
    return `
        <div class="media-item-icon">
            <i class="fas fa-${type === 'document' ? 'file-alt' : 'file'}"></i>
        </div>
    `;
}

function getMediaType(mimeType) {
    if (mimeType.startsWith('image/')) return 'image';
    if (['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'].includes(mimeType)) {
        return 'document';
    }
    return 'other';
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function filterByType(type) {
    currentFilter = type;
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[onclick="filterByType('${type}')"]`).classList.add('active');
    renderMedia();
}

function filterMedia() {
    searchQuery = document.getElementById('searchMedia').value;
    renderMedia();
}

async function handleFileUpload(event) {
    const files = event.target.files;
    if (!files.length) return;

    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'flex';

    try {
        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const response = await fetch('../api/media/upload', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();
        if (!response.ok) throw new Error(result.error);

        loadMedia();
    } catch (error) {
        console.error('Error uploading files:', error);
        alert('Error uploading files: ' + error.message);
    } finally {
        loadingOverlay.style.display = 'none';
        event.target.value = ''; // Reset file input
    }
}

async function showMediaDetails(id) {
    try {
        const media = await handleApiRequest(`media/${id}`);
        document.getElementById('mediaId').value = media.id;
        document.getElementById('mediaFilename').value = media.filename;
        document.getElementById('mediaOriginalFilename').value = media.original_filename;
        document.getElementById('mediaMimeType').value = media.mime_type;
        document.getElementById('mediaFileSize').value = formatFileSize(media.file_size);
        document.getElementById('mediaPath').value = media.path;
        document.getElementById('mediaCreatedAt').value = new Date(media.created_at).toLocaleString();
        
        const preview = document.getElementById('mediaPreview');
        if (getMediaType(media.mime_type) === 'image') {
            preview.src = media.path;
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
        
        toggleModal('mediaDetailsModal', true);
    } catch (error) {
        console.error('Error loading media details:', error);
    }
}

async function deleteMedia(id) {
    if (confirm('Are you sure you want to delete this media file?')) {
        try {
            await handleApiRequest(`media/${id}`, 'DELETE');
            toggleModal('mediaDetailsModal', false);
            loadMedia();
        } catch (error) {
            console.error('Error deleting media:', error);
        }
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Path copied to clipboard!');
    }).catch(err => {
        console.error('Error copying to clipboard:', err);
    });
}
</script>

<style>
.media-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    flex-wrap: wrap;
    gap: 10px;
}

.search-box {
    flex: 1;
    max-width: 300px;
}

.filter-buttons {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 16px;
    border: 1px solid var(--primary-color);
    background: none;
    color: var(--primary-color);
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-btn:hover,
.filter-btn.active {
    background: var(--primary-color);
    color: white;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.media-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.media-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.media-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.media-item-icon {
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    font-size: 3rem;
    color: #666;
}

.media-item-info {
    padding: 10px;
}

.media-item-name {
    font-size: 14px;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.media-item-meta {
    font-size: 12px;
    color: #666;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255,255,255,0.8);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-text {
    margin-top: 10px;
    color: var(--primary-color);
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.media-details-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.media-preview {
    text-align: center;
}

.media-preview img {
    max-width: 100%;
    max-height: 300px;
    object-fit: contain;
}

.media-info {
    padding: 20px;
}

.media-actions {
    margin-top: 20px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

@media (max-width: 768px) {
    .media-details-content {
        grid-template-columns: 1fr;
    }
    
    .media-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-box {
        max-width: none;
    }
}
</style>
