<?php
require_once __DIR__ . '/query.php';

$mediaQuery = new MediaQuery();
$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'edit':
        $item = $mediaQuery->getMediaItem($id);
        ?>
        <div class="media-edit">
            <h3>Edit Media Item</h3>
            <form onsubmit="return handleMediaUpdate(event, <?= $id ?>)">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($item['title']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Alt Text</label>
                    <input type="text" name="alt_text" value="<?= htmlspecialchars($item['alt_text']) ?>">
                </div>
                <div class="preview">
                    <img src="<?= htmlspecialchars($item['filename']) ?>" alt="Preview">
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="button" onclick="location.href='?page=media'" class="btn">Cancel</button>
            </form>
        </div>
        <?php
        break;

    default:
        $items = $mediaQuery->getMediaItems();
        ?>
        <div class="media-manager">
            <h2>Media Manager</h2>
            
            <div class="upload-section">
                <h3>Upload New Media</h3>
                <form id="uploadForm" onsubmit="return handleMediaUpload(event)">
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" name="file" required accept="image/*,video/*,application/pdf">
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Alt Text</label>
                        <input type="text" name="alt_text">
                    </div>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </form>
            </div>

            <div class="media-list">
                <h3>Media Library</h3>
                <div class="grid">
                    <?php foreach ($items as $item): ?>
                        <div class="media-item">
                            <div class="preview">
                                <?php if (strpos($item['type'], 'image/') === 0): ?>
                                    <img src="<?= htmlspecialchars($item['filename']) ?>" 
                                         alt="<?= htmlspecialchars($item['alt_text']) ?>">
                                <?php else: ?>
                                    <div class="file-icon"><?= strtoupper(pathinfo($item['filename'], PATHINFO_EXTENSION)) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="details">
                                <h4><?= htmlspecialchars($item['title']) ?></h4>
                                <div class="actions">
                                    <a href="?page=media&action=edit&id=<?= $item['id'] ?>" 
                                       class="btn btn-sm">Edit</a>
                                    <button onclick="deleteMedia(<?= $item['id'] ?>)" 
                                            class="btn btn-sm btn-danger">Delete</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <script>
        async function handleMediaUpload(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch('../api/endpoints/media.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) throw new Error('Upload failed');
                
                showSuccess('Media uploaded successfully');
                location.reload();
            } catch (error) {
                showError(error.message);
            }
        }

        async function handleMediaUpdate(event, id) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            formData.append('id', id);
            
            try {
                const response = await fetch('../api/endpoints/media.php', {
                    method: 'PUT',
                    body: JSON.stringify(Object.fromEntries(formData))
                });
                
                if (!response.ok) throw new Error('Update failed');
                
                showSuccess('Media updated successfully');
                location.href = '?page=media';
            } catch (error) {
                showError(error.message);
            }
            
            return false;
        }

        async function deleteMedia(id) {
            if (!confirm('Are you sure you want to delete this media item?')) return;
            
            try {
                const response = await fetch(`../api/endpoints/media.php?id=${id}`, {
                    method: 'DELETE'
                });
                
                if (!response.ok) throw new Error('Delete failed');
                
                showSuccess('Media deleted successfully');
                location.reload();
            } catch (error) {
                showError(error.message);
            }
        }
        </script>

        <style>
        .media-manager {
            padding: 20px;
        }

        .upload-section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .media-list .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .media-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .media-item .preview {
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .media-item .preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .media-item .file-icon {
            padding: 20px;
            background: #e9ecef;
            border-radius: 4px;
            font-weight: bold;
        }

        .media-item .details {
            padding: 10px;
        }

        .media-item .details h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .media-item .actions {
            display: flex;
            gap: 5px;
        }

        .media-edit {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .media-edit .preview {
            margin: 20px 0;
            text-align: center;
        }

        .media-edit .preview img {
            max-width: 100%;
            max-height: 300px;
        }
        </style>
        <?php
}
