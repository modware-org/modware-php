<?php
require_once dirname(__DIR__) . '/auth.php';
require_once dirname(__DIR__) . '/header.php';
?>

<div class="content-header">
    <h1>Page Management</h1>
    <button type="button" class="btn btn-primary" onclick="openNewPageModal()">Add New Page</button>
</div>

<!-- Pages List -->
<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Slug</th>
                    <th>Template</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="pagesTableBody">
                <!-- Pages will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Page Edit Modal -->
<div class="modal fade" id="pageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pageModalTitle">Edit Page</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="pageForm">
                    <input type="hidden" id="pageId" name="id">
                    
                    <div class="form-group">
                        <label for="pageTitle">Title</label>
                        <input type="text" id="pageTitle" name="title" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="pageSlug">Slug</label>
                        <input type="text" id="pageSlug" name="slug" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="pageTemplate">Template</label>
                        <select id="pageTemplate" name="template" class="form-control">
                            <option value="default">Default</option>
                            <option value="full-width">Full Width</option>
                            <option value="sidebar">With Sidebar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="pageStatus">Status</label>
                        <select id="pageStatus" name="status" class="form-control">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <!-- Sections Management -->
                    <div class="form-group">
                        <label>Page Sections</label>
                        <div id="pageSections" class="list-group">
                            <!-- Sections will be loaded here -->
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" onclick="openAddSectionModal()">
                            Add Section
                        </button>
                    </div>

                    <!-- Components Management -->
                    <div class="form-group">
                        <label>Page Components</label>
                        <div id="pageComponents" class="list-group">
                            <!-- Components will be loaded here -->
                        </div>
                        <button type="button" class="btn btn-secondary mt-2" onclick="openAddComponentModal()">
                            Add Component
                        </button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="savePage()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Section Modal -->
<div class="modal fade" id="addSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Section</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group" id="availableSections">
                    <!-- Available sections will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Component Modal -->
<div class="modal fade" id="addComponentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Component</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="list-group" id="availableComponents">
                    <!-- Available components will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentPageId = null;

document.addEventListener('DOMContentLoaded', loadPages);

async function loadPages() {
    try {
        const result = await handleApiRequest('pages');
        const pages = result.data;
        
        const tbody = document.getElementById('pagesTableBody');
        tbody.innerHTML = pages.map(page => `
            <tr>
                <td>${page.title}</td>
                <td>${page.slug}</td>
                <td>${page.template}</td>
                <td>${page.status}</td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editPage(${page.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deletePage(${page.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading pages:', error);
        showError('Failed to load pages');
    }
}

function openNewPageModal() {
    currentPageId = null;
    document.getElementById('pageForm').reset();
    document.getElementById('pageModalTitle').textContent = 'Add New Page';
    $('#pageModal').modal('show');
}

async function editPage(id) {
    try {
        currentPageId = id;
        const result = await handleApiRequest(`pages/${id}`);
        const page = result.data;
        
        document.getElementById('pageId').value = page.id;
        document.getElementById('pageTitle').value = page.title;
        document.getElementById('pageSlug').value = page.slug;
        document.getElementById('pageTemplate').value = page.template;
        document.getElementById('pageStatus').value = page.status;
        
        document.getElementById('pageModalTitle').textContent = 'Edit Page';
        
        await loadPageSections(id);
        await loadPageComponents(id);
        
        $('#pageModal').modal('show');
    } catch (error) {
        console.error('Error loading page:', error);
        showError('Failed to load page details');
    }
}

async function loadPageSections(pageId) {
    try {
        const result = await handleApiRequest(`pages/${pageId}/sections`);
        const sections = result.data;
        
        const container = document.getElementById('pageSections');
        container.innerHTML = sections.map(section => `
            <div class="list-group-item d-flex justify-content-between align-items-center" data-id="${section.id}">
                <div>
                    <h6 class="mb-0">${section.title}</h6>
                    <small class="text-muted">${section.type}</small>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-secondary" onclick="editSection(${section.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="removeSection(${section.id})">Remove</button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading page sections:', error);
        showError('Failed to load page sections');
    }
}

async function loadPageComponents(pageId) {
    try {
        const result = await handleApiRequest(`pages/${pageId}/components`);
        const components = result.data;
        
        const container = document.getElementById('pageComponents');
        container.innerHTML = components.map(component => `
            <div class="list-group-item d-flex justify-content-between align-items-center" data-id="${component.id}">
                <div>
                    <h6 class="mb-0">${component.title}</h6>
                    <small class="text-muted">${component.type}</small>
                </div>
                <div class="btn-group">
                    <button class="btn btn-sm btn-secondary" onclick="editComponent(${component.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="removeComponent(${component.id})">Remove</button>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading page components:', error);
        showError('Failed to load page components');
    }
}

async function openAddSectionModal() {
    try {
        const result = await handleApiRequest('sections');
        const sections = result.data;
        
        const container = document.getElementById('availableSections');
        container.innerHTML = sections.map(section => `
            <a href="#" class="list-group-item list-group-item-action" onclick="addSection(${section.id})">
                <h6 class="mb-0">${section.title}</h6>
                <small class="text-muted">${section.type}</small>
            </a>
        `).join('');
        
        $('#addSectionModal').modal('show');
    } catch (error) {
        console.error('Error loading available sections:', error);
        showError('Failed to load available sections');
    }
}

async function openAddComponentModal() {
    try {
        const result = await handleApiRequest('components');
        const components = result.data;
        
        const container = document.getElementById('availableComponents');
        container.innerHTML = components.map(component => `
            <a href="#" class="list-group-item list-group-item-action" onclick="addComponent(${component.id})">
                <h6 class="mb-0">${component.title}</h6>
                <small class="text-muted">${component.type}</small>
            </a>
        `).join('');
        
        $('#addComponentModal').modal('show');
    } catch (error) {
        console.error('Error loading available components:', error);
        showError('Failed to load available components');
    }
}

async function addSection(sectionId) {
    try {
        await handleApiRequest(`pages/${currentPageId}/sections`, 'POST', {
            section_id: sectionId
        });
        
        $('#addSectionModal').modal('hide');
        await loadPageSections(currentPageId);
        showSuccess('Section added successfully');
    } catch (error) {
        console.error('Error adding section:', error);
        showError('Failed to add section');
    }
}

async function addComponent(componentId) {
    try {
        await handleApiRequest(`pages/${currentPageId}/components`, 'POST', {
            component_id: componentId
        });
        
        $('#addComponentModal').modal('hide');
        await loadPageComponents(currentPageId);
        showSuccess('Component added successfully');
    } catch (error) {
        console.error('Error adding component:', error);
        showError('Failed to add component');
    }
}

async function removeSection(sectionId) {
    if (!confirm('Are you sure you want to remove this section?')) return;
    
    try {
        await handleApiRequest(`pages/${currentPageId}/sections/${sectionId}`, 'DELETE');
        await loadPageSections(currentPageId);
        showSuccess('Section removed successfully');
    } catch (error) {
        console.error('Error removing section:', error);
        showError('Failed to remove section');
    }
}

async function removeComponent(componentId) {
    if (!confirm('Are you sure you want to remove this component?')) return;
    
    try {
        await handleApiRequest(`pages/${currentPageId}/components/${componentId}`, 'DELETE');
        await loadPageComponents(currentPageId);
        showSuccess('Component removed successfully');
    } catch (error) {
        console.error('Error removing component:', error);
        showError('Failed to remove component');
    }
}

async function savePage() {
    try {
        const form = document.getElementById('pageForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        const method = currentPageId ? 'PUT' : 'POST';
        const url = currentPageId ? `pages/${currentPageId}` : 'pages';
        
        await handleApiRequest(url, method, data);
        
        $('#pageModal').modal('hide');
        await loadPages();
        showSuccess('Page saved successfully');
    } catch (error) {
        console.error('Error saving page:', error);
        showError('Failed to save page');
    }
}

async function deletePage(id) {
    if (!confirm('Are you sure you want to delete this page?')) return;
    
    try {
        await handleApiRequest(`pages/${id}`, 'DELETE');
        await loadPages();
        showSuccess('Page deleted successfully');
    } catch (error) {
        console.error('Error deleting page:', error);
        showError('Failed to delete page');
    }
}

function editSection(sectionId) {
    // Load section admin interface in a modal or redirect to section admin page
    window.location.href = `../sections/${sectionId}/admin.php?page_id=${currentPageId}`;
}

function editComponent(componentId) {
    // Load component admin interface in a modal or redirect to component admin page
    window.location.href = `../components/${componentId}/admin.php?page_id=${currentPageId}`;
}
</script>

<style>
.content-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.list-group-item {
    cursor: pointer;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.btn-group {
    display: flex;
    gap: 0.5rem;
}
</style>

<?php require_once dirname(__DIR__) . '/footer.php'; ?>
