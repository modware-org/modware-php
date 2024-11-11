<div class="content-header">
    <h1>Menu Section Settings</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('general')">General Settings</button>
        <button class="tab-btn" onclick="switchTab('items')">Menu Items</button>
        <button class="tab-btn" onclick="switchTab('categories')">Categories</button>
    </div>

    <!-- General Settings Tab -->
    <div id="generalTab" class="tab-content active">
        <form id="menuGeneralForm" onsubmit="handleGeneralSubmit(event)">
            <div class="form-group">
                <label for="menuLogo">Menu Logo</label>
                <div class="input-group">
                    <input type="text" id="menuLogo" name="logo" class="form-control">
                    <button type="button" class="btn btn-secondary" onclick="openMediaLibrary('menuLogo')">Select Image</button>
                </div>
            </div>

            <div class="form-group">
                <label for="menuLogoAlt">Logo Alt Text</label>
                <input type="text" id="menuLogoAlt" name="logo_alt" class="form-control">
            </div>

            <div class="form-group">
                <label for="menuBreakpoint">Mobile Breakpoint (px)</label>
                <input type="number" id="menuBreakpoint" name="mobile_breakpoint" class="form-control" min="320">
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="menuSticky" name="sticky">
                    <label class="custom-control-label" for="menuSticky">Enable Sticky Menu</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="menuShowSearch" name="show_search">
                    <label class="custom-control-label" for="menuShowSearch">Show Search</label>
                </div>
            </div>

            <div class="form-group">
                <label for="menuCtaText">CTA Button Text</label>
                <input type="text" id="menuCtaText" name="cta_text" class="form-control">
            </div>

            <div class="form-group">
                <label for="menuCtaUrl">CTA Button URL</label>
                <input type="text" id="menuCtaUrl" name="cta_url" class="form-control">
            </div>

            <div class="form-group">
                <label>Section Status</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="menuActive" name="is_active">
                    <label class="custom-control-label" for="menuActive">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label for="menuOrder">Display Order</label>
                <input type="number" id="menuOrder" name="sort_order" class="form-control" min="0">
            </div>

            <button type="submit" class="btn btn-primary">Save General Settings</button>
        </form>
    </div>

    <!-- Menu Items Tab -->
    <div id="itemsTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddItemModal()">Add Menu Item</button>
        </div>

        <div id="menuItemsTree" class="menu-tree">
            <!-- Menu items will be loaded here -->
        </div>
    </div>

    <!-- Categories Tab -->
    <div id="categoriesTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddCategoryModal()">Add Category</button>
        </div>

        <div id="categoriesTree" class="menu-tree">
            <!-- Categories will be loaded here -->
        </div>
    </div>
</div>

<!-- Add/Edit Menu Item Modal -->
<div id="menuItemModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="menuItemModalTitle">Add Menu Item</h2>
            <button class="btn btn-text" onclick="toggleModal('menuItemModal', false)">&times;</button>
        </div>
        <form id="menuItemForm" onsubmit="handleMenuItemSubmit(event)">
            <input type="hidden" id="menuItemId" name="id">
            <div class="form-group">
                <label for="menuItemTitle">Title</label>
                <input type="text" id="menuItemTitle" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="menuItemUrl">URL</label>
                <input type="text" id="menuItemUrl" name="url" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="menuItemParent">Parent Item</label>
                <select id="menuItemParent" name="parent_id" class="form-control">
                    <option value="">None</option>
                    <!-- Options will be loaded dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="menuItemPosition">Position</label>
                <input type="number" id="menuItemPosition" name="position" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <label for="menuItemTarget">Open In</label>
                <select id="menuItemTarget" name="target" class="form-control">
                    <option value="_self">Same Window</option>
                    <option value="_blank">New Window</option>
                </select>
            </div>
            <div class="form-group">
                <label for="menuItemIcon">Icon Class</label>
                <input type="text" id="menuItemIcon" name="icon_class" class="form-control">
                <small>Optional CSS class for menu item icon</small>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="menuItemActive" name="is_active" checked>
                    <label class="custom-control-label" for="menuItemActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('menuItemModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div id="categoryModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="categoryModalTitle">Add Category</h2>
            <button class="btn btn-text" onclick="toggleModal('categoryModal', false)">&times;</button>
        </div>
        <form id="categoryForm" onsubmit="handleCategorySubmit(event)">
            <input type="hidden" id="categoryId" name="id">
            <div class="form-group">
                <label for="categoryName">Name</label>
                <input type="text" id="categoryName" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="categorySlug">Slug</label>
                <input type="text" id="categorySlug" name="slug" class="form-control" required>
                <small>URL-friendly version of name</small>
            </div>
            <div class="form-group">
                <label for="categoryDescription">Description</label>
                <textarea id="categoryDescription" name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="categoryParent">Parent Category</label>
                <select id="categoryParent" name="parent_id" class="form-control">
                    <option value="">None</option>
                    <!-- Options will be loaded dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="categorySortOrder">Sort Order</label>
                <input type="number" id="categorySortOrder" name="sort_order" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="categoryActive" name="is_active" checked>
                    <label class="custom-control-label" for="categoryActive">Active</label>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="categoryShowInMenu" name="show_in_menu">
                    <label class="custom-control-label" for="categoryShowInMenu">Show in Menu</label>
                </div>
            </div>
            <div class="form-group menu-position" style="display: none;">
                <label for="categoryMenuPosition">Menu Position</label>
                <input type="number" id="categoryMenuPosition" name="menu_position" class="form-control" min="0">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('categoryModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadGeneralSettings();
    loadMenuItems();
    loadCategories();
    initCategoryForm();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

async function loadGeneralSettings() {
    try {
        const section = await handleApiRequest('sections/menu');
        document.getElementById('menuActive').checked = section.is_active === 1;
        document.getElementById('menuOrder').value = section.sort_order;

        const config = await handleApiRequest('config?prefix=menu_');
        document.getElementById('menuLogo').value = config.menu_logo || '';
        document.getElementById('menuLogoAlt').value = config.menu_logo_alt || '';
        document.getElementById('menuBreakpoint').value = config.menu_mobile_breakpoint || '768';
        document.getElementById('menuSticky').checked = config.menu_sticky === 'true';
        document.getElementById('menuShowSearch').checked = config.menu_show_search === 'true';
        document.getElementById('menuCtaText').value = config.menu_cta_text || '';
        document.getElementById('menuCtaUrl').value = config.menu_cta_url || '';
    } catch (error) {
        console.error('Error loading general settings:', error);
        showError('Failed to load general settings');
    }
}

async function loadMenuItems() {
    try {
        const items = await handleApiRequest('menu-items');
        renderMenuTree(items, 'menuItemsTree');
        updateMenuItemParentOptions(items);
    } catch (error) {
        console.error('Error loading menu items:', error);
        showError('Failed to load menu items');
    }
}

async function loadCategories() {
    try {
        const categories = await handleApiRequest('menu-categories');
        renderCategoryTree(categories, 'categoriesTree');
        updateCategoryParentOptions(categories);
    } catch (error) {
        console.error('Error loading categories:', error);
        showError('Failed to load categories');
    }
}

function renderMenuTree(items, containerId, parentId = null) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const list = document.createElement('ul');
    list.className = 'tree-list';

    const filteredItems = items.filter(item => item.parent_id === parentId);
    filteredItems.forEach(item => {
        const li = document.createElement('li');
        li.className = 'tree-item';
        li.innerHTML = `
            <div class="tree-item-content">
                <span class="tree-item-title">${item.title}</span>
                <div class="tree-item-actions">
                    <button class="btn btn-sm btn-secondary" onclick="editMenuItem(${item.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteMenuItem(${item.id})">Delete</button>
                </div>
            </div>
        `;

        const children = items.filter(child => child.parent_id === item.id);
        if (children.length > 0) {
            renderMenuTree(items, li, item.id);
        }

        list.appendChild(li);
    });

    if (parentId === null) {
        container.innerHTML = '';
        container.appendChild(list);
    } else {
        container.appendChild(list);
    }
}

function renderCategoryTree(categories, containerId, parentId = null) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const list = document.createElement('ul');
    list.className = 'tree-list';

    const filteredCategories = categories.filter(cat => cat.parent_id === parentId);
    filteredCategories.forEach(category => {
        const li = document.createElement('li');
        li.className = 'tree-item';
        li.innerHTML = `
            <div class="tree-item-content">
                <span class="tree-item-title">${category.name}</span>
                <div class="tree-item-actions">
                    <button class="btn btn-sm btn-secondary" onclick="editCategory(${category.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.id})">Delete</button>
                </div>
            </div>
        `;

        const children = categories.filter(child => child.parent_id === category.id);
        if (children.length > 0) {
            renderCategoryTree(categories, li, category.id);
        }

        list.appendChild(li);
    });

    if (parentId === null) {
        container.innerHTML = '';
        container.appendChild(list);
    } else {
        container.appendChild(list);
    }
}

function updateMenuItemParentOptions(items, selectedId = null) {
    const select = document.getElementById('menuItemParent');
    if (!select) return;

    select.innerHTML = '<option value="">None</option>';
    items.forEach(item => {
        if (item.id !== selectedId) {
            select.innerHTML += `<option value="${item.id}">${item.title}</option>`;
        }
    });
}

function updateCategoryParentOptions(categories, selectedId = null) {
    const select = document.getElementById('categoryParent');
    if (!select) return;

    select.innerHTML = '<option value="">None</option>';
    categories.forEach(category => {
        if (category.id !== selectedId) {
            select.innerHTML += `<option value="${category.id}">${category.name}</option>`;
        }
    });
}

function initCategoryForm() {
    const showInMenuCheckbox = document.getElementById('categoryShowInMenu');
    const menuPositionGroup = document.querySelector('.menu-position');
    
    if (showInMenuCheckbox && menuPositionGroup) {
        showInMenuCheckbox.addEventListener('change', function() {
            menuPositionGroup.style.display = this.checked ? 'block' : 'none';
        });
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
            menu_logo: formData.get('logo'),
            menu_logo_alt: formData.get('logo_alt'),
            menu_mobile_breakpoint: formData.get('mobile_breakpoint'),
            menu_sticky: formData.get('sticky') === 'on' ? 'true' : 'false',
            menu_show_search: formData.get('show_search') === 'on' ? 'true' : 'false',
            menu_cta_text: formData.get('cta_text'),
            menu_cta_url: formData.get('cta_url')
        };

        await handleApiRequest('sections/menu', 'POST', sectionData);
        await handleApiRequest('config/bulk', 'POST', configData);
        
        showSuccess('Menu settings updated successfully');
    } catch (error) {
        console.error('Error updating menu settings:', error);
        showError('Failed to update menu settings');
    }
}

function openAddItemModal() {
    document.getElementById('menuItemModalTitle').textContent = 'Add Menu Item';
    document.getElementById('menuItemForm').reset();
    document.getElementById('menuItemId').value = '';
    toggleModal('menuItemModal', true);
}

async function editMenuItem(id) {
    try {
        const item = await handleApiRequest(`menu-items/${id}`);
        document.getElementById('menuItemModalTitle').textContent = 'Edit Menu Item';
        document.getElementById('menuItemId').value = item.id;
        document.getElementById('menuItemTitle').value = item.title;
        document.getElementById('menuItemUrl').value = item.url;
        document.getElementById('menuItemParent').value = item.parent_id || '';
        document.getElementById('menuItemPosition').value = item.position;
        document.getElementById('menuItemTarget').value = item.target || '_self';
        document.getElementById('menuItemIcon').value = item.icon_class || '';
        document.getElementById('menuItemActive').checked = item.is_active === 1;
        toggleModal('menuItemModal', true);
    } catch (error) {
        console.error('Error loading menu item:', error);
        showError('Failed to load menu item details');
    }
}

async function handleMenuItemSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            title: formData.get('title'),
            url: formData.get('url'),
            parent_id: formData.get('parent_id') || null,
            position: parseInt(formData.get('position')),
            target: formData.get('target'),
            icon_class: formData.get('icon_class'),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest('menu-items' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('menuItemModal', false);
        loadMenuItems();
        showSuccess('Menu item saved successfully');
    } catch (error) {
        console.error('Error saving menu item:', error);
        showError('Failed to save menu item');
    }
}

async function deleteMenuItem(id) {
    if (confirm('Are you sure you want to delete this menu item?')) {
        try {
            await handleApiRequest(`menu-items/${id}`, 'DELETE');
            loadMenuItems();
            showSuccess('Menu item deleted successfully');
        } catch (error) {
            console.error('Error deleting menu item:', error);
            showError('Failed to delete menu item');
        }
    }
}

function openAddCategoryModal() {
    document.getElementById('categoryModalTitle').textContent = 'Add Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.querySelector('.menu-position').style.display = 'none';
    toggleModal('categoryModal', true);
}

async function editCategory(id) {
    try {
        const category = await handleApiRequest(`menu-categories/${id}`);
        document.getElementById('categoryModalTitle').textContent = 'Edit Category';
        document.getElementById('categoryId').value = category.id;
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categorySlug').value = category.slug;
        document.getElementById('categoryDescription').value = category.description || '';
        document.getElementById('categoryParent').value = category.parent_id || '';
        document.getElementById('categorySortOrder').value = category.sort_order;
        document.getElementById('categoryActive').checked = category.is_active === 1;
        document.getElementById('categoryShowInMenu').checked = category.show_in_menu === 1;
        document.getElementById('categoryMenuPosition').value = category.menu_position;
        document.querySelector('.menu-position').style.display = category.show_in_menu === 1 ? 'block' : 'none';
        toggleModal('categoryModal', true);
    } catch (error) {
        console.error('Error loading category:', error);
        showError('Failed to load category details');
    }
}

async function handleCategorySubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            name: formData.get('name'),
            slug: formData.get('slug'),
            description: formData.get('description'),
            parent_id: formData.get('parent_id') || null,
            sort_order: parseInt(formData.get('sort_order')),
            is_active: formData.get('is_active') === 'on' ? 1 : 0,
            show_in_menu: formData.get('show_in_menu') === 'on' ? 1 : 0,
            menu_position: parseInt(formData.get('menu_position'))
        };

        await handleApiRequest('menu-categories' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('categoryModal', false);
        loadCategories();
        showSuccess('Category saved successfully');
    } catch (error) {
        console.error('Error saving category:', error);
        showError('Failed to save category');
    }
}

async function deleteCategory(id) {
    if (confirm('Are you sure you want to delete this category?')) {
        try {
            await handleApiRequest(`menu-categories/${id}`, 'DELETE');
            loadCategories();
            showSuccess('Category deleted successfully');
        } catch (error) {
            console.error('Error deleting category:', error);
            showError('Failed to delete category');
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

.menu-tree {
    margin-top: 20px;
}

.tree-list {
    list-style: none;
    padding-left: 20px;
    margin: 0;
}

.tree-list:first-child {
    padding-left: 0;
}

.tree-item {
    margin: 10px 0;
}

.tree-item-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px;
    background: var(--bg-light);
    border-radius: 4px;
    border: 1px solid var(--border-color);
}

.tree-item-title {
    font-weight: 500;
}

.tree-item-actions {
    display: flex;
    gap: 5px;
}

.menu-position {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}
</style>
