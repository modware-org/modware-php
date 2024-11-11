<?php
if (!defined('ACCESS')) {
    die('Direct access not permitted');
}

function renderMetaAdmin($db, $pageId) {
    $metaQuery = new MetaQuery($db);
    $meta = $metaQuery->getMetaByPageId($pageId) ?? [];
?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Meta Tags & SEO Settings</h5>
        </div>
        <div class="card-body">
            <form id="metaForm" class="needs-validation" novalidate>
                <input type="hidden" name="page_id" value="<?php echo htmlspecialchars($pageId); ?>">
                
                <div class="mb-3">
                    <h6 class="mb-3">Basic Meta Tags</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo htmlspecialchars($meta['title'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3"><?php echo htmlspecialchars($meta['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keywords</label>
                            <input type="text" class="form-control" name="keywords" value="<?php echo htmlspecialchars($meta['keywords'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Author</label>
                            <input type="text" class="form-control" name="author" value="<?php echo htmlspecialchars($meta['author'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Robots</label>
                            <input type="text" class="form-control" name="robots" value="<?php echo htmlspecialchars($meta['robots'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="mb-3">Open Graph Meta Tags</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">OG Title</label>
                            <input type="text" class="form-control" name="og_title" value="<?php echo htmlspecialchars($meta['og_title'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">OG Description</label>
                            <textarea class="form-control" name="og_description" rows="3"><?php echo htmlspecialchars($meta['og_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">OG Image</label>
                            <input type="text" class="form-control" name="og_image" value="<?php echo htmlspecialchars($meta['og_image'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">OG Type</label>
                            <input type="text" class="form-control" name="og_type" value="<?php echo htmlspecialchars($meta['og_type'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="mb-3">Twitter Card Meta Tags</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Twitter Card</label>
                            <input type="text" class="form-control" name="twitter_card" value="<?php echo htmlspecialchars($meta['twitter_card'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Twitter Site</label>
                            <input type="text" class="form-control" name="twitter_site" value="<?php echo htmlspecialchars($meta['twitter_site'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Twitter Creator</label>
                            <input type="text" class="form-control" name="twitter_creator" value="<?php echo htmlspecialchars($meta['twitter_creator'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Twitter Title</label>
                            <input type="text" class="form-control" name="twitter_title" value="<?php echo htmlspecialchars($meta['twitter_title'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Twitter Description</label>
                            <textarea class="form-control" name="twitter_description" rows="3"><?php echo htmlspecialchars($meta['twitter_description'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Twitter Image</label>
                            <input type="text" class="form-control" name="twitter_image" value="<?php echo htmlspecialchars($meta['twitter_image'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <h6 class="mb-3">Additional Settings</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Canonical URL</label>
                            <input type="text" class="form-control" name="canonical_url" value="<?php echo htmlspecialchars($meta['canonical_url'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Save Meta Settings</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('metaForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/endpoints/seo.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                const result = await response.json();
                if (result.success) {
                    showAlert('success', 'Meta settings saved successfully');
                } else {
                    showAlert('danger', 'Failed to save meta settings');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while saving meta settings');
            }
        });
    </script>
<?php
}
?>
