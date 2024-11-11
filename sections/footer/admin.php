<div class="content-header">
    <h1>Footer Section Settings</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('general')">General Settings</button>
        <button class="tab-btn" onclick="switchTab('links')">Footer Links</button>
        <button class="tab-btn" onclick="switchTab('social')">Social Media</button>
    </div>

    <!-- General Settings Tab -->
    <div id="generalTab" class="tab-content active">
        <form id="footerGeneralForm" onsubmit="handleGeneralSubmit(event)">
            <div class="form-group">
                <label for="footerLogo">Footer Logo</label>
                <div class="input-group">
                    <input type="text" id="footerLogo" name="logo" class="form-control">
                    <button type="button" class="btn btn-secondary" onclick="openMediaLibrary('footerLogo')">Select Image</button>
                </div>
            </div>

            <div class="form-group">
                <label for="footerDescription">Footer Description</label>
                <textarea id="footerDescription" name="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="footerCopyright">Copyright Text</label>
                <input type="text" id="footerCopyright" name="copyright" class="form-control">
                <small>Use {year} to insert current year automatically</small>
            </div>

            <div class="form-group">
                <label for="footerColumns">Number of Link Columns</label>
                <input type="number" id="footerColumns" name="columns" class="form-control" min="1" max="4">
            </div>

            <div class="form-group">
                <label>Section Status</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="footerActive" name="is_active">
                    <label class="custom-control-label" for="footerActive">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label for="footerOrder">Display Order</label>
                <input type="number" id="footerOrder" name="sort_order" class="form-control" min="0">
            </div>

            <button type="submit" class="btn btn-primary">Save General Settings</button>
        </form>
    </div>

    <!-- Footer Links Tab -->
    <div id="linksTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddLinkModal()">Add Footer Link</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Column</th>
                        <th>Order</th>
                        <th>Title</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="footerLinksTable">
                    <tr>
                        <td colspan="6">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Social Media Tab -->
    <div id="socialTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddSocialModal()">Add Social Link</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Platform</th>
                        <th>URL</th>
                        <th>Icon</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="socialLinksTable">
                    <tr>
                        <td colspan="6">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Link Modal -->
<div id="linkModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="linkModalTitle">Add Footer Link</h2>
            <button class="btn btn-text" onclick="toggleModal('linkModal', false)">&times;</button>
        </div>
        <form id="linkForm" onsubmit="handleLinkSubmit(event)">
            <input type="hidden" id="linkId" name="id">
            <div class="form-group">
                <label for="linkTitle">Title</label>
                <input type="text" id="linkTitle" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="linkUrl">URL</label>
                <input type="text" id="linkUrl" name="url" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="linkColumn">Column</label>
                <input type="number" id="linkColumn" name="column_number" class="form-control" min="1" required>
            </div>
            <div class="form-group">
                <label for="linkOrder">Sort Order</label>
                <input type="number" id="linkOrder" name="sort_order" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="linkActive" name="is_active" checked>
                    <label class="custom-control-label" for="linkActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('linkModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Social Link Modal -->
<div id="socialModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="socialModalTitle">Add Social Link</h2>
            <button class="btn btn-text" onclick="toggleModal('socialModal', false)">&times;</button>
        </div>
        <form id="socialForm" onsubmit="handleSocialSubmit(event)">
            <input type="hidden" id="socialId" name="id">
            <div class="form-group">
                <label for="socialPlatform">Platform</label>
                <input type="text" id="socialPlatform" name="platform" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="socialUrl">URL</label>
                <input type="text" id="socialUrl" name="url" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="socialIcon">Icon SVG</label>
                <textarea id="socialIcon" name="icon_svg" class="form-control" rows="5" required></textarea>
                <small>SVG code for the social media icon</small>
            </div>
            <div class="form-group">
                <label for="socialOrder">Sort Order</label>
                <input type="number" id="socialOrder" name="sort_order" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="socialActive" name="is_active" checked>
                    <label class="custom-control-label" for="socialActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('socialModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadGeneralSettings();
    loadFooterLinks();
    loadSocialLinks();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

async function loadGeneralSettings() {
    try {
        const section = await handleApiRequest('sections/footer');
        document.getElementById('footerActive').checked = section.is_active === 1;
        document.getElementById('footerOrder').value = section.sort_order;

        const config = await handleApiRequest('config?prefix=footer_');
        document.getElementById('footerLogo').value = config.footer_logo || '';
        document.getElementById('footerDescription').value = config.footer_description || '';
        document.getElementById('footerCopyright').value = config.footer_copyright || '';
        document.getElementById('footerColumns').value = config.footer_columns || '3';
    } catch (error) {
        console.error('Error loading general settings:', error);
        showError('Failed to load general settings');
    }
}

async function loadFooterLinks() {
    try {
        const links = await handleApiRequest('footer-links');
        const tbody = document.getElementById('footerLinksTable');
        
        if (links.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No footer links found</td></tr>';
            return;
        }

        tbody.innerHTML = links.map(link => `
            <tr>
                <td>${link.column_number}</td>
                <td>${link.sort_order}</td>
                <td>${link.title}</td>
                <td>${link.url}</td>
                <td>
                    <span class="badge ${link.is_active ? 'badge-success' : 'badge-danger'}">
                        ${link.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editLink(${link.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteLink(${link.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading footer links:', error);
        showError('Failed to load footer links');
    }
}

async function loadSocialLinks() {
    try {
        const links = await handleApiRequest('footer-social');
        const tbody = document.getElementById('socialLinksTable');
        
        if (links.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No social links found</td></tr>';
            return;
        }

        tbody.innerHTML = links.map(link => `
            <tr>
                <td>${link.sort_order}</td>
                <td>${link.platform}</td>
                <td>${link.url}</td>
                <td>
                    <div class="icon-preview">
                        ${link.icon_svg}
                    </div>
                </td>
                <td>
                    <span class="badge ${link.is_active ? 'badge-success' : 'badge-danger'}">
                        ${link.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editSocialLink(${link.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteSocialLink(${link.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading social links:', error);
        showError('Failed to load social links');
    }
}

// Form submission handlers
async function handleGeneralSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const sectionData = {
            is_active: formData.get('is_active') === 'on' ? 1 : 0,
            sort_order: parseInt(formData.get('sort_order'))
        };

        const configData = {
            footer_logo: formData.get('logo'),
            footer_description: formData.get('description'),
            footer_copyright: formData.get('copyright'),
            footer_columns: formData.get('columns')
        };

        await handleApiRequest('sections/footer', 'POST', sectionData);
        await handleApiRequest('config/bulk', 'POST', configData);
        
        showSuccess('Footer settings updated successfully');
    } catch (error) {
        console.error('Error updating footer settings:', error);
        showError('Failed to update footer settings');
    }
}

function openAddLinkModal() {
    document.getElementById('linkModalTitle').textContent = 'Add Footer Link';
    document.getElementById('linkForm').reset();
    document.getElementById('linkId').value = '';
    toggleModal('linkModal', true);
}

async function editLink(id) {
    try {
        const link = await handleApiRequest(`footer-links/${id}`);
        document.getElementById('linkModalTitle').textContent = 'Edit Footer Link';
        document.getElementById('linkId').value = link.id;
        document.getElementById('linkTitle').value = link.title;
        document.getElementById('linkUrl').value = link.url;
        document.getElementById('linkColumn').value = link.column_number;
        document.getElementById('linkOrder').value = link.sort_order;
        document.getElementById('linkActive').checked = link.is_active === 1;
        toggleModal('linkModal', true);
    } catch (error) {
        console.error('Error loading link:', error);
        showError('Failed to load link details');
    }
}

async function handleLinkSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            title: formData.get('title'),
            url: formData.get('url'),
            column_number: parseInt(formData.get('column_number')),
            sort_order: parseInt(formData.get('sort_order')),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest('footer-links' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('linkModal', false);
        loadFooterLinks();
        showSuccess('Footer link saved successfully');
    } catch (error) {
        console.error('Error saving link:', error);
        showError('Failed to save footer link');
    }
}

async function deleteLink(id) {
    if (confirm('Are you sure you want to delete this footer link?')) {
        try {
            await handleApiRequest(`footer-links/${id}`, 'DELETE');
            loadFooterLinks();
            showSuccess('Footer link deleted successfully');
        } catch (error) {
            console.error('Error deleting link:', error);
            showError('Failed to delete footer link');
        }
    }
}

function openAddSocialModal() {
    document.getElementById('socialModalTitle').textContent = 'Add Social Link';
    document.getElementById('socialForm').reset();
    document.getElementById('socialId').value = '';
    toggleModal('socialModal', true);
}

async function editSocialLink(id) {
    try {
        const link = await handleApiRequest(`footer-social/${id}`);
        document.getElementById('socialModalTitle').textContent = 'Edit Social Link';
        document.getElementById('socialId').value = link.id;
        document.getElementById('socialPlatform').value = link.platform;
        document.getElementById('socialUrl').value = link.url;
        document.getElementById('socialIcon').value = link.icon_svg;
        document.getElementById('socialOrder').value = link.sort_order;
        document.getElementById('socialActive').checked = link.is_active === 1;
        toggleModal('socialModal', true);
    } catch (error) {
        console.error('Error loading social link:', error);
        showError('Failed to load social link details');
    }
}

async function handleSocialSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            platform: formData.get('platform'),
            url: formData.get('url'),
            icon_svg: formData.get('icon_svg'),
            sort_order: parseInt(formData.get('sort_order')),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest('footer-social' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('socialModal', false);
        loadSocialLinks();
        showSuccess('Social link saved successfully');
    } catch (error) {
        console.error('Error saving social link:', error);
        showError('Failed to save social link');
    }
}

async function deleteSocialLink(id) {
    if (confirm('Are you sure you want to delete this social link?')) {
        try {
            await handleApiRequest(`footer-social/${id}`, 'DELETE');
            loadSocialLinks();
            showSuccess('Social link deleted successfully');
        } catch (error) {
            console.error('Error deleting social link:', error);
            showError('Failed to delete social link');
        }
    }
}
</script>

<style>
.section-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.tab-btn {
    padding: 8px 16px;
    border: none;
    background: none;
    cursor: pointer;
    border-bottom: 2px solid transparent;
}

.tab-btn.active {
    border-bottom-color: var(--primary-color);
    color: var(--primary-color);
}

.tab-content {
    display: none;
    padding: 20px;
}

.tab-content.active {
    display: block;
}

.icon-preview {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--bg-light);
    border-radius: 4px;
    padding: 8px;
}

.icon-preview svg {
    width: 24px;
    height: 24px;
}

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.badge-success {
    background: var(--success-color);
    color: white;
}

.badge-danger {
    background: var(--danger-color);
    color: white;
}
</style>
