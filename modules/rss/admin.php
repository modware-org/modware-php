<div class="content-header">
    <h1>RSS Feed Management</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('settings')">Settings</button>
        <button class="tab-btn" onclick="switchTab('types')">Feed Types</button>
        <button class="tab-btn" onclick="switchTab('exclusions')">Exclusions</button>
        <button class="tab-btn" onclick="switchTab('tools')">Tools</button>
    </div>

    <!-- Settings Tab -->
    <div id="settingsTab" class="tab-content active">
        <form id="rssSettingsForm" onsubmit="handleSettingsSubmit(event)">
            <div class="form-group">
                <label for="feedTitle">Feed Title</label>
                <input type="text" id="feedTitle" name="feed_title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="feedDescription">Feed Description</label>
                <textarea id="feedDescription" name="feed_description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="feedLanguage">Feed Language</label>
                <input type="text" id="feedLanguage" name="feed_language" class="form-control" value="ru">
            </div>

            <div class="form-group">
                <label for="itemsCount">Items Per Feed</label>
                <input type="number" id="itemsCount" name="items_count" class="form-control" min="1" max="100" required>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="autoDiscovery" name="auto_discovery">
                    <label class="custom-control-label" for="autoDiscovery">Enable RSS auto-discovery</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="includeContent" name="include_content">
                    <label class="custom-control-label" for="includeContent">Include full content</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="includeFeaturedImage" name="include_featured_image">
                    <label class="custom-control-label" for="includeFeaturedImage">Include featured images</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="includeCategories" name="include_categories">
                    <label class="custom-control-label" for="includeCategories">Include categories</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="includeAuthor" name="include_author">
                    <label class="custom-control-label" for="includeAuthor">Include author information</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="compression" name="compression">
                    <label class="custom-control-label" for="compression">Enable compression (gzip)</label>
                </div>
            </div>

            <div class="form-group">
                <label for="cacheLifetime">Cache Lifetime (seconds)</label>
                <input type="number" id="cacheLifetime" name="cache_lifetime" class="form-control" min="0">
                <small>0 to disable caching</small>
            </div>

            <div class="form-group">
                <label for="excludedCategories">Excluded Categories</label>
                <select id="excludedCategories" name="excluded_categories[]" class="form-control" multiple>
                    <!-- Categories will be loaded dynamically -->
                </select>
            </div>

            <div class="form-group">
                <label for="customNamespaces">Custom XML Namespaces</label>
                <textarea id="customNamespaces" name="custom_namespaces" class="form-control" rows="3"></textarea>
                <small>Example: xmlns:media="http://search.yahoo.com/mrss/"</small>
            </div>

            <div class="form-group">
                <label for="customElements">Custom Feed Elements</label>
                <textarea id="customElements" name="custom_elements" class="form-control" rows="3"></textarea>
                <small>Custom XML elements to include in the feed</small>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    <!-- Feed Types Tab -->
    <div id="typesTab" class="tab-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="feedTypesTable">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Exclusions Tab -->
    <div id="exclusionsTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddExclusionModal()">Add Exclusion</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Content</th>
                        <th>Type</th>
                        <th>Reason</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="exclusionsTable">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tools Tab -->
    <div id="toolsTab" class="tab-content">
        <div class="tool-buttons">
            <button class="btn btn-primary" onclick="generateFeeds()">Generate All Feeds</button>
            <button class="btn btn-secondary" onclick="clearCache()">Clear Cache</button>
            <button class="btn btn-secondary" onclick="validateFeeds()">Validate Feeds</button>
        </div>

        <div class="mt-4">
            <h3>Feed Information</h3>
            <div id="feedInfo">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Feed Type Modal -->
<div id="feedTypeModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Feed Type</h2>
            <button class="btn btn-text" onclick="toggleModal('feedTypeModal', false)">&times;</button>
        </div>
        <form id="feedTypeForm" onsubmit="handleFeedTypeSubmit(event)">
            <input type="hidden" id="feedTypeSlug" name="slug">
            <div class="form-group">
                <label for="feedTypeName">Name</label>
                <input type="text" id="feedTypeName" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="feedTypeDescription">Description</label>
                <textarea id="feedTypeDescription" name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="feedTypeActive" name="is_active">
                    <label class="custom-control-label" for="feedTypeActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('feedTypeModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Exclusion Modal -->
<div id="exclusionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add Content Exclusion</h2>
            <button class="btn btn-text" onclick="toggleModal('exclusionModal', false)">&times;</button>
        </div>
        <form id="exclusionForm" onsubmit="handleExclusionSubmit(event)">
            <div class="form-group">
                <label for="contentType">Content Type</label>
                <select id="contentType" name="content_type" class="form-control" required>
                    <option value="post">Blog Post</option>
                    <option value="category">Category</option>
                </select>
            </div>
            <div class="form-group">
                <label for="contentId">Content</label>
                <select id="contentId" name="content_id" class="form-control" required>
                    <!-- Content will be loaded based on type -->
                </select>
            </div>
            <div class="form-group">
                <label for="exclusionReason">Reason</label>
                <textarea id="exclusionReason" name="reason" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('exclusionModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Add Exclusion</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadSettings();
    loadFeedTypes();
    loadExclusions();
    loadCategories();
    loadFeedInfo();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

async function loadSettings() {
    try {
        const settings = await handleApiRequest('rss-settings');
        const config = await handleApiRequest('config?prefix=rss_');
        
        // Feed settings
        document.getElementById('feedTitle').value = settings.feed_title;
        document.getElementById('feedDescription').value = settings.feed_description || '';
        document.getElementById('feedLanguage').value = settings.feed_language;
        document.getElementById('itemsCount').value = settings.items_count;
        
        // Config settings
        document.getElementById('autoDiscovery').checked = config.rss_auto_discovery === 'true';
        document.getElementById('includeContent').checked = config.rss_include_content === 'true';
        document.getElementById('includeFeaturedImage').checked = config.rss_include_featured_image === 'true';
        document.getElementById('includeCategories').checked = config.rss_include_categories === 'true';
        document.getElementById('includeAuthor').checked = config.rss_include_author === 'true';
        document.getElementById('compression').checked = config.rss_compression === 'true';
        document.getElementById('cacheLifetime').value = config.rss_cache_lifetime || '3600';
        document.getElementById('customNamespaces').value = config.rss_custom_namespaces || '';
        document.getElementById('customElements').value = config.rss_custom_elements || '';
        
        // Set excluded categories
        const excludedCategories = (config.rss_excluded_categories || '').split(',');
        Array.from(document.getElementById('excludedCategories').options).forEach(option => {
            option.selected = excludedCategories.includes(option.value);
        });
    } catch (error) {
        console.error('Error loading settings:', error);
        showError('Failed to load settings');
    }
}

async function loadFeedTypes() {
    try {
        const types = await handleApiRequest('rss-feed-types');
        const tbody = document.getElementById('feedTypesTable');
        
        if (types.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">No feed types found</td></tr>';
            return;
        }

        tbody.innerHTML = types.map(type => `
            <tr>
                <td>${type.name}</td>
                <td>${type.slug}</td>
                <td>${type.description || ''}</td>
                <td>
                    <span class="badge ${type.is_active ? 'badge-success' : 'badge-danger'}">
                        ${type.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editFeedType('${type.slug}')">Edit</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading feed types:', error);
        showError('Failed to load feed types');
    }
}

async function loadExclusions() {
    try {
        const exclusions = await handleApiRequest('rss-exclusions');
        const tbody = document.getElementById('exclusionsTable');
        
        if (exclusions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">No exclusions found</td></tr>';
            return;
        }

        tbody.innerHTML = exclusions.map(exclusion => `
            <tr>
                <td>${exclusion.content_title}</td>
                <td>${exclusion.content_type}</td>
                <td>${exclusion.reason || ''}</td>
                <td>${formatDate(exclusion.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeExclusion(${exclusion.id})">Remove</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading exclusions:', error);
        showError('Failed to load exclusions');
    }
}

async function loadCategories() {
    try {
        const categories = await handleApiRequest('menu-categories');
        const select = document.getElementById('excludedCategories');
        
        select.innerHTML = categories.map(category => 
            `<option value="${category.id}">${category.name}</option>`
        ).join('');
    } catch (error) {
        console.error('Error loading categories:', error);
        showError('Failed to load categories');
    }
}

async function loadFeedInfo() {
    try {
        const info = await handleApiRequest('rss-info');
        document.getElementById('feedInfo').innerHTML = `
            <table class="info-table">
                <tr>
                    <td>Last Generated:</td>
                    <td>${formatDate(info.last_generated)}</td>
                </tr>
                <tr>
                    <td>Active Feed Types:</td>
                    <td>${info.active_types}</td>
                </tr>
                <tr>
                    <td>Total Items:</td>
                    <td>${info.total_items}</td>
                </tr>
                <tr>
                    <td>Cache Status:</td>
                    <td>${info.cache_valid ? 'Valid' : 'Expired'}</td>
                </tr>
                <tr>
                    <td>Main Feed URL:</td>
                    <td>
                        <a href="${info.main_feed_url}" target="_blank">${info.main_feed_url}</a>
                    </td>
                </tr>
            </table>
        `;
    } catch (error) {
        console.error('Error loading feed info:', error);
        showError('Failed to load feed information');
    }
}

async function handleSettingsSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        
        // Feed settings
        const feedSettings = {
            feed_title: formData.get('feed_title'),
            feed_description: formData.get('feed_description'),
            feed_language: formData.get('feed_language'),
            items_count: parseInt(formData.get('items_count')),
            is_active: 1
        };
        
        // Config settings
        const configData = {
            rss_auto_discovery: formData.get('auto_discovery') === 'on' ? 'true' : 'false',
            rss_include_content: formData.get('include_content') === 'on' ? 'true' : 'false',
            rss_include_featured_image: formData.get('include_featured_image') === 'on' ? 'true' : 'false',
            rss_include_categories: formData.get('include_categories') === 'on' ? 'true' : 'false',
            rss_include_author: formData.get('include_author') === 'on' ? 'true' : 'false',
            rss_compression: formData.get('compression') === 'on' ? 'true' : 'false',
            rss_cache_lifetime: formData.get('cache_lifetime'),
            rss_excluded_categories: Array.from(event.target.querySelector('[name="excluded_categories[]"]').selectedOptions)
                                        .map(opt => opt.value)
                                        .join(','),
            rss_custom_namespaces: formData.get('custom_namespaces'),
            rss_custom_elements: formData.get('custom_elements')
        };

        await handleApiRequest('rss-settings', 'POST', feedSettings);
        await handleApiRequest('config/bulk', 'POST', configData);
        
        showSuccess('Settings saved successfully');
    } catch (error) {
        console.error('Error saving settings:', error);
        showError('Failed to save settings');
    }
}

async function editFeedType(slug) {
    try {
        const type = await handleApiRequest(`rss-feed-types/${slug}`);
        document.getElementById('feedTypeSlug').value = type.slug;
        document.getElementById('feedTypeName').value = type.name;
        document.getElementById('feedTypeDescription').value = type.description || '';
        document.getElementById('feedTypeActive').checked = type.is_active === 1;
        toggleModal('feedTypeModal', true);
    } catch (error) {
        console.error('Error loading feed type:', error);
        showError('Failed to load feed type');
    }
}

async function handleFeedTypeSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name'),
            description: formData.get('description'),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest(`rss-feed-types/${formData.get('slug')}`, 'POST', data);
        toggleModal('feedTypeModal', false);
        loadFeedTypes();
        showSuccess('Feed type updated successfully');
    } catch (error) {
        console.error('Error updating feed type:', error);
        showError('Failed to update feed type');
    }
}

function openAddExclusionModal() {
    document.getElementById('exclusionForm').reset();
    loadContentOptions();
    toggleModal('exclusionModal', true);
}

async function loadContentOptions() {
    const contentType = document.getElementById('contentType').value;
    const contentSelect = document.getElementById('contentId');
    
    try {
        let items;
        if (contentType === 'post') {
            items = await handleApiRequest('blog-posts');
        } else {
            items = await handleApiRequest('menu-categories');
        }
        
        contentSelect.innerHTML = items.map(item => 
            `<option value="${item.id}">${item.title || item.name}</option>`
        ).join('');
    } catch (error) {
        console.error('Error loading content options:', error);
        showError('Failed to load content options');
    }
}

document.getElementById('contentType')?.addEventListener('change', loadContentOptions);

async function handleExclusionSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            content_id: parseInt(formData.get('content_id')),
            content_type: formData.get('content_type'),
            reason: formData.get('reason')
        };

        await handleApiRequest('rss-exclusions', 'POST', data);
        toggleModal('exclusionModal', false);
        loadExclusions();
        showSuccess('Exclusion added successfully');
    } catch (error) {
        console.error('Error adding exclusion:', error);
        showError('Failed to add exclusion');
    }
}

async function removeExclusion(id) {
    if (confirm('Are you sure you want to remove this exclusion?')) {
        try {
            await handleApiRequest(`rss-exclusions/${id}`, 'DELETE');
            loadExclusions();
            showSuccess('Exclusion removed successfully');
        } catch (error) {
            console.error('Error removing exclusion:', error);
            showError('Failed to remove exclusion');
        }
    }
}

// Tool functions
async function generateFeeds() {
    try {
        await handleApiRequest('rss/generate', 'POST');
        loadFeedInfo();
        showSuccess('Feeds generated successfully');
    } catch (error) {
        console.error('Error generating feeds:', error);
        showError('Failed to generate feeds');
    }
}

async function clearCache() {
    try {
        await handleApiRequest('rss/clear-cache', 'POST');
        loadFeedInfo();
        showSuccess('Cache cleared successfully');
    } catch (error) {
        console.error('Error clearing cache:', error);
        showError('Failed to clear cache');
    }
}

async function validateFeeds() {
    try {
        const result = await handleApiRequest('rss/validate', 'POST');
        if (result.valid) {
            showSuccess('All feeds are valid');
        } else {
            showError('Feed validation failed: ' + result.errors.join(', '));
        }
    } catch (error) {
        console.error('Error validating feeds:', error);
        showError('Failed to validate feeds');
    }
}

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}
</script>

<style>
.tool-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.info-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

.info-table td {
    padding: 0.5rem;
    border-bottom: 1px solid var(--border-color);
}

.info-table td:first-child {
    font-weight: 500;
    width: 150px;
}

@media (max-width: 768px) {
    .tool-buttons {
        flex-direction: column;
    }

    .tool-buttons .btn {
        width: 100%;
    }
}
</style>
