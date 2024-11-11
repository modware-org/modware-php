<?php
require_once '../header.php';
require_once '../../config/Database.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_shortcode'])) {
        $stmt = $db->prepare("
            INSERT INTO app_admin.admin_integrations 
            (name, type, config, is_active) 
            VALUES (?, 'shortcode', ?, TRUE)
            ON DUPLICATE KEY UPDATE config = ?
        ");
        
        $config = json_encode([
            'tag' => $_POST['tag'],
            'handler' => $_POST['handler'],
            'description' => $_POST['description']
        ]);
        
        $stmt->execute([$_POST['name'], $config, $config]);
    }
    
    if (isset($_POST['save_webhook'])) {
        $stmt = $db->prepare("
            INSERT INTO app_admin.admin_integrations 
            (name, type, config, is_active) 
            VALUES (?, 'webhook', ?, TRUE)
            ON DUPLICATE KEY UPDATE config = ?
        ");
        
        $config = json_encode([
            'url' => $_POST['url'],
            'events' => explode(',', $_POST['events']),
            'secret_key' => $_POST['secret_key']
        ]);
        
        $stmt->execute([$_POST['name'], $config, $config]);
    }
}

// Get current integrations
$stmt = $db->query("SELECT * FROM app_admin.admin_integrations ORDER BY type, name");
$integrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-header">
    <h2>Integrations Management</h2>
    <p>Configure and manage system integrations including shortcodes, webhooks, and API endpoints.</p>
</div>

<div class="tabs">
    <div class="tab active" data-tab="shortcodes">Shortcodes</div>
    <div class="tab" data-tab="webhooks">Webhooks</div>
    <div class="tab" data-tab="api">API</div>
    <div class="tab" data-tab="translations">Translations</div>
</div>

<!-- Shortcodes Section -->
<div class="tab-content" id="shortcodes">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Shortcode Configuration</h3>
            <button class="btn btn-primary" onclick="showModal('shortcode-modal')">Add New Shortcode</button>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Tag</th>
                    <th>Handler</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($integrations as $integration): ?>
                    <?php if ($integration['type'] === 'shortcode'): ?>
                        <?php $config = json_decode($integration['config'], true); ?>
                        <tr>
                            <td><?php echo htmlspecialchars($integration['name']); ?></td>
                            <td><?php echo htmlspecialchars($config['tag']); ?></td>
                            <td><?php echo htmlspecialchars($config['handler']); ?></td>
                            <td>
                                <span class="badge <?php echo $integration['is_active'] ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $integration['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editShortcode(<?php echo htmlspecialchars(json_encode($integration)); ?>)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteIntegration(<?php echo $integration['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Example Usage -->
        <div class="card mt-3">
            <h3 class="card-title">Example Usage</h3>
            <div class="example-code">
                <h4>YouTube Shortcode</h4>
                <pre><code>// In your section content:
[youtube id="VIDEO_ID" width="560" height="315" autoplay="0" controls="1"]</code></pre>
                
                <h4>Translation Shortcode</h4>
                <pre><code>// In your section content:
[translate key="welcome_message" lang="es"]</code></pre>
            </div>
        </div>
    </div>
</div>

<!-- Webhooks Section -->
<div class="tab-content" id="webhooks" style="display: none;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Webhook Configuration</h3>
            <button class="btn btn-primary" onclick="showModal('webhook-modal')">Add New Webhook</button>
        </div>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>URL</th>
                    <th>Events</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($integrations as $integration): ?>
                    <?php if ($integration['type'] === 'webhook'): ?>
                        <?php $config = json_decode($integration['config'], true); ?>
                        <tr>
                            <td><?php echo htmlspecialchars($integration['name']); ?></td>
                            <td><?php echo htmlspecialchars($config['url']); ?></td>
                            <td><?php echo htmlspecialchars(implode(', ', $config['events'])); ?></td>
                            <td>
                                <span class="badge <?php echo $integration['is_active'] ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $integration['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="editWebhook(<?php echo htmlspecialchars(json_encode($integration)); ?>)">Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteIntegration(<?php echo $integration['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Example Usage -->
        <div class="card mt-3">
            <h3 class="card-title">Example Usage</h3>
            <div class="example-code">
                <h4>Content Update Webhook</h4>
                <pre><code>// In your section's query.php:
WebhookProcessor::trigger('content.updated', [
    'content_id' => $contentId,
    'content_type' => 'section',
    'section' => $sectionName,
    'changes' => [
        'title' => ['old' => $oldTitle, 'new' => $newTitle]
    ]
]);</code></pre>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="shortcode-modal" class="modal-backdrop" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add/Edit Shortcode</h3>
            <button class="modal-close" onclick="hideModal('shortcode-modal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="save_shortcode" value="1">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Tag</label>
                <input type="text" name="tag" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Handler Class</label>
                <input type="text" name="handler" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="hideModal('shortcode-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<div id="webhook-modal" class="modal-backdrop" style="display: none;">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Add/Edit Webhook</h3>
            <button class="modal-close" onclick="hideModal('webhook-modal')">&times;</button>
        </div>
        <form method="POST">
            <input type="hidden" name="save_webhook" value="1">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">URL</label>
                <input type="url" name="url" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Events (comma-separated)</label>
                <input type="text" name="events" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Secret Key</label>
                <input type="text" name="secret_key" class="form-control">
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-secondary" onclick="hideModal('webhook-modal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
function showModal(id) {
    document.getElementById(id).style.display = 'flex';
}

function hideModal(id) {
    document.getElementById(id).style.display = 'none';
}

function editShortcode(integration) {
    const config = JSON.parse(integration.config);
    const modal = document.getElementById('shortcode-modal');
    modal.querySelector('[name="name"]').value = integration.name;
    modal.querySelector('[name="tag"]').value = config.tag;
    modal.querySelector('[name="handler"]').value = config.handler;
    modal.querySelector('[name="description"]').value = config.description || '';
    showModal('shortcode-modal');
}

function editWebhook(integration) {
    const config = JSON.parse(integration.config);
    const modal = document.getElementById('webhook-modal');
    modal.querySelector('[name="name"]').value = integration.name;
    modal.querySelector('[name="url"]').value = config.url;
    modal.querySelector('[name="events"]').value = config.events.join(',');
    modal.querySelector('[name="secret_key"]').value = config.secret_key || '';
    showModal('webhook-modal');
}

function deleteIntegration(id) {
    if (confirm('Are you sure you want to delete this integration?')) {
        fetch(`api/integrations/${id}`, { method: 'DELETE' })
            .then(() => window.location.reload())
            .catch(err => alert('Error deleting integration'));
    }
}

// Tab switching
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.style.display = 'none');
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).style.display = 'block';
    });
});
</script>

<?php require_once '../footer.php'; ?>
