<div class="content-header">
    <h1>Program Section Settings</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('section')">Section Settings</button>
        <button class="tab-btn" onclick="switchTab('components')">Program Components</button>
    </div>

    <!-- Section Settings Tab -->
    <div id="sectionTab" class="tab-content active">
        <form id="programSectionForm" onsubmit="handleSectionSubmit(event)">
            <div class="form-group">
                <label for="sectionTitle">Section Title</label>
                <input type="text" id="sectionTitle" name="program_title" class="form-control" required>
                <small>Main heading for the program section</small>
            </div>

            <div class="form-group">
                <label for="sectionSubtitle">Section Subtitle</label>
                <input type="text" id="sectionSubtitle" name="program_subtitle" class="form-control">
                <small>Optional subtitle or description text</small>
            </div>

            <div class="form-group">
                <label>Show CTA Button</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="showCta" name="program_show_cta">
                    <label class="custom-control-label" for="showCta">Show call to action button</label>
                </div>
            </div>

            <div id="ctaSettings" style="display: none;">
                <div class="form-group">
                    <label for="ctaText">CTA Button Text</label>
                    <input type="text" id="ctaText" name="program_cta_text" class="form-control">
                </div>

                <div class="form-group">
                    <label for="ctaLink">CTA Button Link</label>
                    <input type="text" id="ctaLink" name="program_cta_link" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label>Section Status</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="programActive" name="is_active">
                    <label class="custom-control-label" for="programActive">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label for="programOrder">Display Order</label>
                <input type="number" id="programOrder" name="sort_order" class="form-control" min="0" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Section Settings</button>
        </form>
    </div>

    <!-- Program Components Tab -->
    <div id="componentsTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddComponentModal()">Add Program Component</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Icon</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="componentsTable">
                    <tr>
                        <td colspan="6">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Component Modal -->
<div id="componentModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="componentModalTitle">Add Program Component</h2>
            <button class="btn btn-text" onclick="toggleModal('componentModal', false)">&times;</button>
        </div>
        <form id="componentForm" onsubmit="handleComponentSubmit(event)">
            <input type="hidden" id="componentId" name="id">
            <div class="form-group">
                <label for="componentTitle">Title</label>
                <input type="text" id="componentTitle" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="componentDescription">Description</label>
                <textarea id="componentDescription" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="componentIcon">Icon SVG</label>
                <textarea id="componentIcon" name="icon_svg" class="form-control" rows="5" required></textarea>
                <small>SVG code for the component icon</small>
            </div>
            <div class="form-group">
                <label for="componentOrder">Sort Order</label>
                <input type="number" id="componentOrder" name="sort_order" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="componentActive" name="is_active" checked>
                    <label class="custom-control-label" for="componentActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('componentModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadSectionSettings();
    loadComponents();
    initializeCtaToggle();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

function initializeCtaToggle() {
    const showCta = document.getElementById('showCta');
    const ctaSettings = document.getElementById('ctaSettings');
    
    showCta.addEventListener('change', function() {
        ctaSettings.style.display = this.checked ? 'block' : 'none';
    });
}

async function loadSectionSettings() {
    try {
        const section = await handleApiRequest('sections/program');
        document.getElementById('programActive').checked = section.is_active === 1;
        document.getElementById('programOrder').value = section.sort_order;

        // Load section config
        const config = await handleApiRequest('config?prefix=program_');
        document.getElementById('sectionTitle').value = config.program_title || '';
        document.getElementById('sectionSubtitle').value = config.program_subtitle || '';
        document.getElementById('showCta').checked = config.program_show_cta === 'true';
        document.getElementById('ctaText').value = config.program_cta_text || '';
        document.getElementById('ctaLink').value = config.program_cta_link || '';
        
        // Show/hide CTA settings
        document.getElementById('ctaSettings').style.display = config.program_show_cta === 'true' ? 'block' : 'none';
    } catch (error) {
        console.error('Error loading section settings:', error);
        showError('Failed to load section settings');
    }
}

async function loadComponents() {
    try {
        const components = await handleApiRequest('program-components');
        const tbody = document.getElementById('componentsTable');
        
        if (components.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No components found</td></tr>';
            return;
        }

        tbody.innerHTML = components.map(component => `
            <tr>
                <td>${component.sort_order}</td>
                <td>
                    <div class="icon-preview">
                        ${component.icon_svg}
                    </div>
                </td>
                <td>${component.title}</td>
                <td>${component.description.substring(0, 50)}...</td>
                <td>
                    <span class="badge ${component.is_active ? 'badge-success' : 'badge-danger'}">
                        ${component.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editComponent(${component.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteComponent(${component.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading components:', error);
        showError('Failed to load program components');
    }
}

async function handleSectionSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const sectionData = {
            is_active: formData.get('is_active') === 'on' ? 1 : 0,
            sort_order: parseInt(formData.get('sort_order'))
        };

        const configData = {
            program_title: formData.get('program_title'),
            program_subtitle: formData.get('program_subtitle'),
            program_show_cta: formData.get('program_show_cta') === 'on' ? 'true' : 'false',
            program_cta_text: formData.get('program_cta_text'),
            program_cta_link: formData.get('program_cta_link')
        };

        await handleApiRequest('sections/program', 'POST', sectionData);
        await handleApiRequest('config/bulk', 'POST', configData);
        
        showSuccess('Section settings updated successfully');
    } catch (error) {
        console.error('Error updating section:', error);
        showError('Failed to update section settings');
    }
}

function openAddComponentModal() {
    document.getElementById('componentModalTitle').textContent = 'Add Program Component';
    document.getElementById('componentForm').reset();
    document.getElementById('componentId').value = '';
    toggleModal('componentModal', true);
}

async function editComponent(id) {
    try {
        const component = await handleApiRequest(`program-components/${id}`);
        document.getElementById('componentModalTitle').textContent = 'Edit Program Component';
        document.getElementById('componentId').value = component.id;
        document.getElementById('componentTitle').value = component.title;
        document.getElementById('componentDescription').value = component.description;
        document.getElementById('componentIcon').value = component.icon_svg;
        document.getElementById('componentOrder').value = component.sort_order;
        document.getElementById('componentActive').checked = component.is_active === 1;
        toggleModal('componentModal', true);
    } catch (error) {
        console.error('Error loading component:', error);
        showError('Failed to load component details');
    }
}

async function handleComponentSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            title: formData.get('title'),
            description: formData.get('description'),
            icon_svg: formData.get('icon_svg'),
            sort_order: parseInt(formData.get('sort_order')),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest('program-components' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('componentModal', false);
        loadComponents();
        showSuccess('Component saved successfully');
    } catch (error) {
        console.error('Error saving component:', error);
        showError('Failed to save component');
    }
}

async function deleteComponent(id) {
    if (confirm('Are you sure you want to delete this component?')) {
        try {
            await handleApiRequest(`program-components/${id}`, 'DELETE');
            loadComponents();
            showSuccess('Component deleted successfully');
        } catch (error) {
            console.error('Error deleting component:', error);
            showError('Failed to delete component');
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
