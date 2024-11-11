<div class="content-header">
    <h1>Site Configuration</h1>
    <button class="btn btn-primary" onclick="toggleModal('addConfigModal', true)">Add New Setting</button>
</div>

<div class="card">
    <div class="config-sections">
        <button class="section-btn active" onclick="filterConfig('all')">All Settings</button>
        <button class="section-btn" onclick="filterConfig('general')">General</button>
        <button class="section-btn" onclick="filterConfig('contact')">Contact</button>
        <button class="section-btn" onclick="filterConfig('hero')">Hero Section</button>
        <button class="section-btn" onclick="filterConfig('team')">Team</button>
        <button class="section-btn" onclick="filterConfig('certification')">Certification</button>
        <button class="section-btn" onclick="filterConfig('indications')">Indications</button>
        <button class="section-btn" onclick="filterConfig('program')">Program</button>
        <button class="section-btn" onclick="filterConfig('api')">API</button>
        <button class="section-btn" onclick="filterConfig('custom')">Custom</button>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="configTable">
                <tr>
                    <td colspan="5">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Configuration Modal -->
<div id="addConfigModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Configuration</h2>
            <button class="btn btn-text" onclick="toggleModal('addConfigModal', false)">&times;</button>
        </div>
        <form id="addConfigForm" onsubmit="handleConfigSubmit(event)">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" class="form-control" required>
                <small>Use snake_case for naming (e.g., site_name)</small>
            </div>
            <div class="form-group">
                <label for="value">Value</label>
                <textarea id="value" name="value" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="type">Type</label>
                <select id="type" name="type" class="form-control">
                    <option value="text">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="email">Email</option>
                    <option value="url">URL</option>
                    <option value="number">Number</option>
                    <option value="boolean">Boolean</option>
                    <option value="html">HTML</option>
                    <option value="svg">SVG</option>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('addConfigModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Configuration Modal -->
<div id="editConfigModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Configuration</h2>
            <button class="btn btn-text" onclick="toggleModal('editConfigModal', false)">&times;</button>
        </div>
        <form id="editConfigForm" onsubmit="handleConfigEdit(event)">
            <input type="hidden" id="editId" name="id">
            <div class="form-group">
                <label for="editName">Name</label>
                <input type="text" id="editName" name="name" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="editValue">Value</label>
                <textarea id="editValue" name="value" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="editType">Type</label>
                <select id="editType" name="type" class="form-control">
                    <option value="text">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="email">Email</option>
                    <option value="url">URL</option>
                    <option value="number">Number</option>
                    <option value="boolean">Boolean</option>
                    <option value="html">HTML</option>
                    <option value="svg">SVG</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editDescription">Description</label>
                <input type="text" id="editDescription" name="description" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('editConfigModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadConfig);

let currentFilter = 'all';
let configItems = [];

async function loadConfig() {
    try {
        configItems = await handleApiRequest('config');
        renderConfig();
    } catch (error) {
        console.error('Error loading configuration:', error);
    }
}

function renderConfig() {
    const tbody = document.getElementById('configTable');
    
    const filteredItems = currentFilter === 'all' 
        ? configItems 
        : configItems.filter(item => getConfigSection(item.name) === currentFilter);
    
    if (filteredItems.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5">No configuration items found</td></tr>';
        return;
    }

    tbody.innerHTML = filteredItems.map(item => `
        <tr>
            <td>${item.name}</td>
            <td>${formatConfigValue(item.value, item.type)}</td>
            <td>${item.type}</td>
            <td>${item.description || ''}</td>
            <td>
                <button class="btn btn-secondary btn-sm" onclick="editConfig(${item.id})">Edit</button>
                <button class="btn btn-danger btn-sm" onclick="deleteConfig(${item.id})">Delete</button>
            </td>
        </tr>
    `).join('');
}

function formatConfigValue(value, type) {
    if (!value) return '';
    if (type === 'boolean') {
        return value === '1' || value === 'true' ? 'Yes' : 'No';
    }
    if (type === 'textarea' || type === 'html' || type === 'svg') {
        return value.length > 50 ? value.substring(0, 50) + '...' : value;
    }
    return value;
}

function getConfigSection(name) {
    if (name.startsWith('site_') || name.startsWith('app_')) return 'general';
    if (name.startsWith('contact_')) return 'contact';
    if (name.startsWith('hero_')) return 'hero';
    if (name.startsWith('team_')) return 'team';
    if (name.startsWith('cert_')) return 'certification';
    if (name.startsWith('indication_')) return 'indications';
    if (name.startsWith('program_')) return 'program';
    if (name.startsWith('api_')) return 'api';
    return 'custom';
}

function filterConfig(section) {
    currentFilter = section;
    document.querySelectorAll('.section-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`[onclick="filterConfig('${section}')"]`).classList.add('active');
    renderConfig();
}

async function handleConfigSubmit(event) {
    event.preventDefault();
    try {
        await handleFormSubmit(event.target, 'config');
        toggleModal('addConfigModal', false);
        loadConfig();
    } catch (error) {
        console.error('Error adding configuration:', error);
    }
}

async function editConfig(id) {
    try {
        const config = await handleApiRequest(`config?id=${id}`);
        document.getElementById('editId').value = config.id;
        document.getElementById('editName').value = config.name;
        document.getElementById('editValue').value = config.value;
        document.getElementById('editType').value = config.type;
        document.getElementById('editDescription').value = config.description;
        toggleModal('editConfigModal', true);
    } catch (error) {
        console.error('Error loading configuration for edit:', error);
    }
}

async function handleConfigEdit(event) {
    event.preventDefault();
    try {
        const id = document.getElementById('editId').value;
        await handleFormSubmit(event.target, `config/${id}`);
        toggleModal('editConfigModal', false);
        loadConfig();
    } catch (error) {
        console.error('Error updating configuration:', error);
    }
}

async function deleteConfig(id) {
    if (confirm('Are you sure you want to delete this configuration?')) {
        try {
            await handleApiRequest(`config/${id}`, 'DELETE');
            loadConfig();
        } catch (error) {
            console.error('Error deleting configuration:', error);
        }
    }
}
</script>

<style>
.config-sections {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.section-btn {
    padding: 8px 16px;
    border: 1px solid var(--primary-color);
    background: none;
    color: var(--primary-color);
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.section-btn:hover {
    background: var(--primary-color);
    color: white;
}

.section-btn.active {
    background: var(--primary-color);
    color: white;
}

.form-group small {
    color: #666;
    font-size: 12px;
    margin-top: 4px;
    display: block;
}
</style>
