<div class="content-header">
    <h1>Menu Management</h1>
    <button class="btn btn-primary" onclick="toggleModal('addMenuItemModal', true)">Add Menu Item</button>
</div>

<div class="card">
    <div class="menu-items-container">
        <div id="menuList" class="sortable-menu">
            Loading...
        </div>
    </div>
</div>

<!-- Add Menu Item Modal -->
<div id="addMenuItemModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Menu Item</h2>
            <button class="btn btn-text" onclick="toggleModal('addMenuItemModal', false)">&times;</button>
        </div>
        <form id="addMenuItemForm" onsubmit="handleMenuItemSubmit(event)">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="url">URL</label>
                <input type="text" id="url" name="url" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="parent_id">Parent Menu Item</label>
                <select id="parent_id" name="parent_id" class="form-control">
                    <option value="">None</option>
                </select>
            </div>
            <div class="form-group">
                <label for="position">Position</label>
                <input type="number" id="position" name="position" class="form-control" min="0" value="0">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" checked>
                    Active
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('addMenuItemModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Menu Item Modal -->
<div id="editMenuItemModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Menu Item</h2>
            <button class="btn btn-text" onclick="toggleModal('editMenuItemModal', false)">&times;</button>
        </div>
        <form id="editMenuItemForm" onsubmit="handleMenuItemEdit(event)">
            <input type="hidden" id="editId" name="id">
            <div class="form-group">
                <label for="editTitle">Title</label>
                <input type="text" id="editTitle" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="editUrl">URL</label>
                <input type="text" id="editUrl" name="url" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="editParentId">Parent Menu Item</label>
                <select id="editParentId" name="parent_id" class="form-control">
                    <option value="">None</option>
                </select>
            </div>
            <div class="form-group">
                <label for="editPosition">Position</label>
                <input type="number" id="editPosition" name="position" class="form-control" min="0">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" id="editIsActive" name="is_active">
                    Active
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('editMenuItemModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadMenu);

async function loadMenu() {
    try {
        const menuItems = await handleApiRequest('menu');
        const menuList = document.getElementById('menuList');
        
        if (menuItems.length === 0) {
            menuList.innerHTML = '<p>No menu items found</p>';
            return;
        }

        // Create a hierarchical menu structure
        const menuHierarchy = buildMenuHierarchy(menuItems);
        menuList.innerHTML = generateMenuHTML(menuHierarchy);
        
        // Update parent menu dropdowns
        updateParentMenuDropdowns(menuItems);
        
        // Initialize sortable functionality
        initSortable();
    } catch (error) {
        console.error('Error loading menu:', error);
    }
}

function buildMenuHierarchy(items) {
    const hierarchy = [];
    const lookup = {};
    
    // First pass: create lookup object
    items.forEach(item => {
        lookup[item.id] = { ...item, children: [] };
    });
    
    // Second pass: build hierarchy
    items.forEach(item => {
        if (item.parent_id) {
            lookup[item.parent_id].children.push(lookup[item.id]);
        } else {
            hierarchy.push(lookup[item.id]);
        }
    });
    
    return hierarchy;
}

function generateMenuHTML(items, level = 0) {
    return items.map(item => `
        <div class="menu-item" data-id="${item.id}" style="margin-left: ${level * 20}px">
            <div class="menu-item-content">
                <span class="menu-item-handle">☰</span>
                <span class="menu-item-title">${item.title}</span>
                <span class="menu-item-url">${item.url}</span>
                <span class="menu-item-status ${item.is_active ? 'active' : 'inactive'}">
                    ${item.is_active ? 'Active' : 'Inactive'}
                </span>
                <div class="menu-item-actions">
                    <button class="btn btn-secondary btn-sm" onclick="editMenuItem(${item.id})">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteMenuItem(${item.id})">Delete</button>
                </div>
            </div>
            ${item.children.length ? generateMenuHTML(item.children, level + 1) : ''}
        </div>
    `).join('');
}

function updateParentMenuDropdowns(items) {
    const parentSelects = document.querySelectorAll('#parent_id, #editParentId');
    const options = items.map(item => 
        `<option value="${item.id}">${item.title}</option>`
    ).join('');
    
    parentSelects.forEach(select => {
        select.innerHTML = '<option value="">None</option>' + options;
    });
}

function initSortable() {
    // Initialize drag and drop functionality
    // You can use a library like Sortable.js here
}

async function handleMenuItemSubmit(event) {
    event.preventDefault();
    try {
        await handleFormSubmit(event.target, 'menu');
        toggleModal('addMenuItemModal', false);
        loadMenu();
    } catch (error) {
        console.error('Error adding menu item:', error);
    }
}

async function editMenuItem(id) {
    try {
        const menuItem = await handleApiRequest(`menu?id=${id}`);
        document.getElementById('editId').value = menuItem.id;
        document.getElementById('editTitle').value = menuItem.title;
        document.getElementById('editUrl').value = menuItem.url;
        document.getElementById('editParentId').value = menuItem.parent_id || '';
        document.getElementById('editPosition').value = menuItem.position;
        document.getElementById('editIsActive').checked = menuItem.is_active;
        toggleModal('editMenuItemModal', true);
    } catch (error) {
        console.error('Error loading menu item for edit:', error);
    }
}

async function handleMenuItemEdit(event) {
    event.preventDefault();
    try {
        const id = document.getElementById('editId').value;
        await handleFormSubmit(event.target, `menu/${id}`);
        toggleModal('editMenuItemModal', false);
        loadMenu();
    } catch (error) {
        console.error('Error updating menu item:', error);
    }
}

async function deleteMenuItem(id) {
    if (confirm('Are you sure you want to delete this menu item?')) {
        try {
            await handleApiRequest(`menu/${id}`, 'DELETE');
            loadMenu();
        } catch (error) {
            console.error('Error deleting menu item:', error);
        }
    }
}
</script>

<style>
.menu-items-container {
    margin-top: 20px;
}

.menu-item {
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.menu-item-content {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    gap: 16px;
    border: 1px solid var(--border-color);
    transition: all 0.3s ease;
}

.menu-item-content:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transform: translateY(-1px);
    border-color: var(--accent-color);
}

.menu-item-handle {
    cursor: move;
    color: #95a5a6;
    font-size: 18px;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.menu-item-handle:hover {
    background: var(--background-light);
    color: var(--accent-color);
}

.menu-item-title {
    font-weight: 600;
    flex: 1;
    color: var(--primary-color);
    font-size: 15px;
}

.menu-item-url {
    color: #7f8c8d;
    flex: 2;
    font-size: 14px;
    font-family: monospace;
    padding: 4px 8px;
    background: var(--background-light);
    border-radius: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.menu-item-status {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.menu-item-status.active {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.menu-item-status.inactive {
    background-color: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}

.menu-item-actions {
    display: flex;
    gap: 8px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 13px;
    border-radius: 4px;
}

.btn-secondary {
    background: var(--background-light);
    color: var(--primary-color);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}

.btn-danger {
    background: #fff5f5;
    color: var(--danger-color);
    border: 1px solid #ffe3e3;
}

.btn-danger:hover {
    background: #ffe3e3;
    border-color: #ffc9c9;
}

/* Modal improvements */
.modal-content {
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.modal-header {
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 16px;
    margin-bottom: 24px;
}

.modal-footer {
    border-top: 1px solid var(--border-color);
    padding-top: 16px;
    margin-top: 24px;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.form-control {
    transition: all 0.3s ease;
}

.form-control:hover {
    border-color: var(--accent-color);
}
</style>
