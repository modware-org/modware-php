<div class="content-header">
    <h1>Indications Section Settings</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('section')">Section Settings</button>
        <button class="tab-btn" onclick="switchTab('indications')">DBT Indications</button>
    </div>

    <!-- Section Settings Tab -->
    <div id="sectionTab" class="tab-content active">
        <form id="indicationsSectionForm" onsubmit="handleSectionSubmit(event)">
            <div class="form-group">
                <label for="sectionTitle">Section Title</label>
                <input type="text" id="sectionTitle" name="indications_title" class="form-control" required>
                <small>Main heading for the indications section</small>
            </div>

            <div class="form-group">
                <label for="sectionSubtitle">Section Subtitle</label>
                <input type="text" id="sectionSubtitle" name="indications_subtitle" class="form-control">
                <small>Optional subtitle or description text</small>
            </div>

            <div class="form-group">
                <label>Section Status</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="indicationsActive" name="is_active">
                    <label class="custom-control-label" for="indicationsActive">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label for="indicationsOrder">Display Order</label>
                <input type="number" id="indicationsOrder" name="sort_order" class="form-control" min="0" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Section Settings</button>
        </form>
    </div>

    <!-- Indications List Tab -->
    <div id="indicationsTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddIndicationModal()">Add Indication</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="indicationsTable">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Indication Modal -->
<div id="indicationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="indicationModalTitle">Add DBT Indication</h2>
            <button class="btn btn-text" onclick="toggleModal('indicationModal', false)">&times;</button>
        </div>
        <form id="indicationForm" onsubmit="handleIndicationSubmit(event)">
            <input type="hidden" id="indicationId" name="id">
            <div class="form-group">
                <label for="indicationTitle">Title</label>
                <input type="text" id="indicationTitle" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="indicationDescription">Description</label>
                <textarea id="indicationDescription" name="description" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label for="indicationOrder">Sort Order</label>
                <input type="number" id="indicationOrder" name="sort_order" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="indicationActive" name="is_active" checked>
                    <label class="custom-control-label" for="indicationActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('indicationModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadSectionSettings();
    loadIndications();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

async function loadSectionSettings() {
    try {
        const section = await handleApiRequest('sections/indications');
        document.getElementById('indicationsActive').checked = section.is_active === 1;
        document.getElementById('indicationsOrder').value = section.sort_order;

        // Load section config
        const config = await handleApiRequest('config?prefix=indications_');
        document.getElementById('sectionTitle').value = config.indications_title || '';
        document.getElementById('sectionSubtitle').value = config.indications_subtitle || '';
    } catch (error) {
        console.error('Error loading section settings:', error);
        showError('Failed to load section settings');
    }
}

async function loadIndications() {
    try {
        const indications = await handleApiRequest('dbt-indications');
        const tbody = document.getElementById('indicationsTable');
        
        if (indications.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">No indications found</td></tr>';
            return;
        }

        tbody.innerHTML = indications.map(indication => `
            <tr>
                <td>${indication.sort_order}</td>
                <td>${indication.title}</td>
                <td>${indication.description.substring(0, 50)}...</td>
                <td>
                    <span class="badge ${indication.is_active ? 'badge-success' : 'badge-danger'}">
                        ${indication.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editIndication(${indication.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteIndication(${indication.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading indications:', error);
        showError('Failed to load indications');
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
            indications_title: formData.get('indications_title'),
            indications_subtitle: formData.get('indications_subtitle')
        };

        await handleApiRequest('sections/indications', 'POST', sectionData);
        await handleApiRequest('config/bulk', 'POST', configData);
        
        showSuccess('Section settings updated successfully');
    } catch (error) {
        console.error('Error updating section:', error);
        showError('Failed to update section settings');
    }
}

function openAddIndicationModal() {
    document.getElementById('indicationModalTitle').textContent = 'Add DBT Indication';
    document.getElementById('indicationForm').reset();
    document.getElementById('indicationId').value = '';
    toggleModal('indicationModal', true);
}

async function editIndication(id) {
    try {
        const indication = await handleApiRequest(`dbt-indications/${id}`);
        document.getElementById('indicationModalTitle').textContent = 'Edit DBT Indication';
        document.getElementById('indicationId').value = indication.id;
        document.getElementById('indicationTitle').value = indication.title;
        document.getElementById('indicationDescription').value = indication.description;
        document.getElementById('indicationOrder').value = indication.sort_order;
        document.getElementById('indicationActive').checked = indication.is_active === 1;
        toggleModal('indicationModal', true);
    } catch (error) {
        console.error('Error loading indication:', error);
        showError('Failed to load indication details');
    }
}

async function handleIndicationSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            title: formData.get('title'),
            description: formData.get('description'),
            sort_order: parseInt(formData.get('sort_order')),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest('dbt-indications' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('indicationModal', false);
        loadIndications();
        showSuccess('Indication saved successfully');
    } catch (error) {
        console.error('Error saving indication:', error);
        showError('Failed to save indication');
    }
}

async function deleteIndication(id) {
    if (confirm('Are you sure you want to delete this indication?')) {
        try {
            await handleApiRequest(`dbt-indications/${id}`, 'DELETE');
            loadIndications();
            showSuccess('Indication deleted successfully');
        } catch (error) {
            console.error('Error deleting indication:', error);
            showError('Failed to delete indication');
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
