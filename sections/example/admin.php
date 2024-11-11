<?php
require_once __DIR__ . '/../../admin/header.php';
require_once __DIR__ . '/../../config/Database.php';

// Get section data
$sectionId = $_GET['section_id'] ?? null;
$stmt = $db->prepare("SELECT data FROM sections WHERE id = ?");
$stmt->execute([$sectionId]);
$sectionData = json_decode($stmt->fetchColumn(), true) ?? [];

// Get available languages
$stmt = $db->query("SELECT * FROM languages WHERE is_active = TRUE ORDER BY is_default DESC, name");
$languages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get translations for all languages
$translations = [];
if ($sectionId) {
    $stmt = $db->prepare("
        SELECT l.code, t.field_name, t.translation 
        FROM translations t
        JOIN languages l ON t.language_id = l.id
        WHERE t.content_type = 'section'
        AND t.content_id = ?
    ");
    
    $stmt->execute([$sectionId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $translations[$row['code']][$row['field_name']] = $row['translation'];
    }
}
?>

<div class="content-header">
    <h2>Example Section Management</h2>
    <p>Configure video content and translations for the example section.</p>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Video Configuration</h3>
    </div>
    
    <form method="POST" action="query.php">
        <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($sectionId); ?>">
        
        <div class="form-group">
            <label class="form-label">YouTube Video ID</label>
            <input type="text" name="video_id" class="form-control" 
                   value="<?php echo htmlspecialchars($sectionData['video_id'] ?? ''); ?>"
                   placeholder="e.g., dQw4w9WgXcQ">
            <small class="form-text text-muted">
                Enter the YouTube video ID from the URL (e.g., for https://www.youtube.com/watch?v=dQw4w9WgXcQ, enter dQw4w9WgXcQ)
            </small>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save Video</button>
        </div>
    </form>
</div>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Content Translations</h3>
    </div>

    <div class="tabs">
        <?php foreach ($languages as $index => $language): ?>
            <div class="tab <?php echo $index === 0 ? 'active' : ''; ?>" 
                 data-tab="lang-<?php echo $language['code']; ?>">
                <?php echo htmlspecialchars($language['name']); ?>
                <?php if ($language['is_default']): ?>
                    <span class="badge badge-primary">Default</span>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="POST" action="query.php">
        <input type="hidden" name="section_id" value="<?php echo htmlspecialchars($sectionId); ?>">
        
        <?php foreach ($languages as $index => $language): ?>
            <div class="tab-content" id="lang-<?php echo $language['code']; ?>"
                 style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>">
                
                <div class="form-group">
                    <label class="form-label">Section Title</label>
                    <input type="text" 
                           name="translations[<?php echo $language['code']; ?>][example_section_title]" 
                           class="form-control"
                           value="<?php echo htmlspecialchars($translations[$language['code']]['example_section_title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Section Description</label>
                    <textarea name="translations[<?php echo $language['code']; ?>][example_section_description]" 
                              class="form-control" rows="3"><?php 
                        echo htmlspecialchars($translations[$language['code']]['example_section_description'] ?? '');
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Block 1 Title</label>
                    <input type="text" 
                           name="translations[<?php echo $language['code']; ?>][example_block1_title]" 
                           class="form-control"
                           value="<?php echo htmlspecialchars($translations[$language['code']]['example_block1_title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Block 1 Content</label>
                    <textarea name="translations[<?php echo $language['code']; ?>][example_block1_content]" 
                              class="form-control" rows="3"><?php 
                        echo htmlspecialchars($translations[$language['code']]['example_block1_content'] ?? '');
                    ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Block 2 Title</label>
                    <input type="text" 
                           name="translations[<?php echo $language['code']; ?>][example_block2_title]" 
                           class="form-control"
                           value="<?php echo htmlspecialchars($translations[$language['code']]['example_block2_title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Block 2 Content</label>
                    <textarea name="translations[<?php echo $language['code']; ?>][example_block2_content]" 
                              class="form-control" rows="3"><?php 
                        echo htmlspecialchars($translations[$language['code']]['example_block2_content'] ?? '');
                    ?></textarea>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save Translations</button>
        </div>
    </form>
</div>

<script>
// Tab switching for languages
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).style.display = 'block';
    });
});
</script>

<?php require_once __DIR__ . '/../../admin/footer.php'; ?>
