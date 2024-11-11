<div class="content-header">
    <h1>Sitemap Management</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('settings')">Settings</button>
        <button class="tab-btn" onclick="switchTab('types')">URL Types</button>
        <button class="tab-btn" onclick="switchTab('exclusions')">Exclusions</button>
        <button class="tab-btn" onclick="switchTab('tools')">Tools</button>
    </div>

    <!-- Settings Tab -->
    <div id="settingsTab" class="tab-content active">
        <form id="sitemapSettingsForm" onsubmit="handleSettingsSubmit(event)">
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="autoPing" name="auto_ping">
                    <label class="custom-control-label" for="autoPing">Automatically ping search engines</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="includeImages" name="include_images">
                    <label class="custom-control-label" for="includeImages">Include image information</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="includeLastMod" name="include_last_mod">
                    <label class="custom-control-label" for="includeLastMod">Include last modification date</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="robotsTxt" name="robots_txt">
                    <label class="custom-control-label" for="robotsTxt">Add sitemap to robots.txt</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="compression" name="compression">
                    <label class="custom-control-label" for="compression">Enable compression (gzip)</label>
                </div>
            </div>

            <div class="form-group">
                <label for="maxUrls">Maximum URLs per sitemap</label>
                <input type="number" id="maxUrls" name="max_urls" class="form-control" min="1" max="50000">
                <small>Maximum 50,000 URLs per sitemap file as per protocol specification</small>
            </div>

            <div class="form-group">
                <label for="cacheLifetime">Cache Lifetime (seconds)</label>
                <input type="number" id="cacheLifetime" name="cache_lifetime" class="form-control" min="0">
                <small>0 to disable caching</small>
            </div>

            <div class="form-group">
                <label for="searchEngines">Search Engines to Ping</label>
                <input type="text" id="searchEngines" name="search_engines" class="form-control">
                <small>Comma-separated list of search engine domains (e.g., google.com,bing.com)</small>
            </div>

            <div class="form-group">
                <label for="excludedCategories">Excluded Categories</label>
                <select id="excludedCategories" name="excluded_categories[]" class="form-control" multiple>
                    <!-- Categories will be loaded dynamically -->
                </select>
            </div>

            <div class="form-group">
                <label for="additionalUrls">Additional URLs</label>
                <textarea id="additionalUrls" name="additional_urls" class="form-control" rows="5"></textarea>
                <small>One URL per line</small>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    <!-- URL Types Tab -->
    <div id="typesTab" class="tab-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Change Frequency</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="urlTypesTable">
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
                        <th>URL</th>
                        <th>Reason</th>
                        <th>Date Added</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="exclusionsTable">
                    <tr>
                        <td colspan="4">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tools Tab -->
    <div id="toolsTab" class="tab-content">
        <div class="tool-buttons">
            <button class="btn btn-primary" onclick="generateSitemap()">Generate Sitemap Now</button>
            <button class="btn btn-secondary" onclick="clearCache()">Clear Cache</button>
            <button class="btn btn-secondary" onclick="pingSearchEngines()">Ping Search Engines</button>
            <button class="btn btn-secondary" onclick="validateSitemap()">Validate Sitemap</button>
        </div>

        <div class="mt-4">
            <h3>Sitemap Information</h3>
            <div id="sitemapInfo">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Edit URL Type Modal -->
<div id="urlTypeModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit URL Type Settings</h2>
            <button class="btn btn-text" onclick="toggleModal('urlTypeModal', false)">&times;</button>
        </div>
        <form id="urlTypeForm" onsubmit="handleUrlTypeSubmit(event)">
            <input type="hidden" id="urlType" name="type">
            <div class="form-group">
                <label for="changefreq">Change Frequency</label>
                <select id="changefreq" name="changefreq" class="form-control">
                    <option value="always">Always</option>
                    <option value="hourly">Hourly</option>
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                    <option value="never">Never</option>
                </select>
            </div>
            <div class="form-group">
                <label for="priority">Priority</label>
                <input type="number" id="priority" name="priority" class="form-control" min="0" max="1" step="0.1">
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="isActive" name="is_active">
                    <label class="custom-control-label" for="isActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('urlTypeModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Exclusion Modal -->
<div id="exclusionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add URL Exclusion</h2>
            <button class="btn btn-text" onclick="toggleModal('exclusionModal', false)">&times;</button>
        </div>
        <form id="exclusionForm" onsubmit="handleExclusionSubmit(event)">
            <div class="form-group">
                <label for="exclusionUrl">URL</label>
                <input type="url" id="exclusionUrl" name="url" class="form-control" required>
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
    loadUrlTypes();
    loadExclusions();
    loadCategories();
    loadSitemapInfo();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

async function loadSettings() {
    try {
        const config = await handleApiRequest('config?prefix=sitemap_');
        
        document.getElementById('autoPing').checked = config.sitemap_auto_ping === 'true';
        document.getElementById('includeImages').checked = config.sitemap_include_images === 'true';
        document.getElementById('includeLastMod').checked = config.sitemap_include_last_mod === 'true';
        document.getElementById('robotsTxt').checked = config.sitemap_robots_txt === 'true';
        document.getElementById('compression').checked = config.sitemap_compression === 'true';
        document.getElementById('maxUrls').value = config.sitemap_max_urls || '50000';
        document.getElementById('cacheLifetime').value = config.sitemap_cache_lifetime || '3600';
        document.getElementById('searchEngines').value = config.sitemap_search_engines || '';
        document.getElementById('additionalUrls').value = config.sitemap_additional_urls || '';
        
        // Set excluded categories
        const excludedCategories = (config.sitemap_excluded_categories || '').split(',');
        Array.from(document.getElementById('excludedCategories').options).forEach(option => {
            option.selected = excludedCategories.includes(option.value);
        });
    } catch (error) {
        console.error('Error loading settings:', error);
        showError('Failed to load settings');
    }
}

async function loadUrlTypes() {
    try {
        const types = await handleApiRequest('sitemap-types');
        const tbody = document.getElementById('urlTypesTable');
        
        if (types.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">No URL types found</td></tr>';
            return;
        }

        tbody.innerHTML = types.map(type => `
            <tr>
                <td>${type.type}</td>
                <td>${type.changefreq}</td>
                <td>${type.priority}</td>
                <td>
                    <span class="badge ${type.is_active ? 'badge-success' : 'badge-danger'}">
                        ${type.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editUrlType('${type.type}')">Edit</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading URL types:', error);
        showError('Failed to load URL types');
    }
}

async function loadExclusions() {
    try {
        const exclusions = await handleApiRequest('sitemap-exclusions');
        const tbody = document.getElementById('exclusionsTable');
        
        if (exclusions.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No exclusions found</td></tr>';
            return;
        }

        tbody.innerHTML = exclusions.map(exclusion => `
            <tr>
                <td>${exclusion.url}</td>
                <td>${exclusion.reason || ''}</td>
                <td>${formatDate(exclusion.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeExclusion('${exclusion.url}')">Remove</button>
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

async function loadSitemapInfo() {
    try {
        const info = await handleApiRequest('sitemap-info');
        document.getElementById('sitemapInfo').innerHTML = `
            <table class="info-table">
                <tr>
                    <td>Last Generated:</td>
                    <td>${formatDate(info.last_generated)}</td>
                </tr>
                <tr>
                    <td>Total URLs:</td>
                    <td>${info.total_urls}</td>
                </tr>
                <tr>
                    <td>File Size:</td>
                    <td>${formatFileSize(info.file_size)}</td>
                </tr>
                <tr>
                    <td>Cache Status:</td>
                    <td>${info.cache_valid ? 'Valid' : 'Expired'}</td>
                </tr>
                <tr>
                    <td>Sitemap URL:</td>
                    <td>
                        <a href="${info.sitemap_url}" target="_blank">${info.sitemap_url}</a>
                    </td>
                </tr>
            </table>
        `;
    } catch (error) {
        console.error('Error loading sitemap info:', error);
        showError('Failed to load sitemap information');
    }
}

async function handleSettingsSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            sitemap_auto_ping: formData.get('auto_ping') === 'on' ? 'true' : 'false',
            sitemap_include_images: formData.get('include_images') === 'on' ? 'true' : 'false',
            sitemap_include_last_mod: formData.get('include_last_mod') === 'on' ? 'true' : 'false',
            sitemap_robots_txt: formData.get('robots_txt') === 'on' ? 'true' : 'false',
            sitemap_compression: formData.get('compression') === 'on' ? 'true' : 'false',
            sitemap_max_urls: formData.get('max_urls'),
            sitemap_cache_lifetime: formData.get('cache_lifetime'),
            sitemap_search_engines: formData.get('search_engines'),
            sitemap_excluded_categories: Array.from(event.target.querySelector('[name="excluded_categories[]"]').selectedOptions)
                                            .map(opt => opt.value)
                                            .join(','),
            sitemap_additional_urls: formData.get('additional_urls')
        };

        await handleApiRequest('config/bulk', 'POST', data);
        showSuccess('Settings saved successfully');
    } catch (error) {
        console.error('Error saving settings:', error);
        showError('Failed to save settings');
    }
}

async function editUrlType(type) {
    try {
        const settings = await handleApiRequest(`sitemap-types/${type}`);
        document.getElementById('urlType').value = type;
        document.getElementById('changefreq').value = settings.changefreq;
        document.getElementById('priority').value = settings.priority;
        document.getElementById('isActive').checked = settings.is_active === 1;
        toggleModal('urlTypeModal', true);
    } catch (error) {
        console.error('Error loading URL type:', error);
        showError('Failed to load URL type settings');
    }
}

async function handleUrlTypeSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            changefreq: formData.get('changefreq'),
            priority: parseFloat(formData.get('priority')),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest(`sitemap-types/${formData.get('type')}`, 'POST', data);
        toggleModal('urlTypeModal', false);
        loadUrlTypes();
        showSuccess('URL type settings updated successfully');
    } catch (error) {
        console.error('Error updating URL type:', error);
        showError('Failed to update URL type settings');
    }
}

function openAddExclusionModal() {
    document.getElementById('exclusionForm').reset();
    toggleModal('exclusionModal', true);
}

async function handleExclusionSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            url: formData.get('url'),
            reason: formData.get('reason')
        };

        await handleApiRequest('sitemap-exclusions', 'POST', data);
        toggleModal('exclusionModal', false);
        loadExclusions();
        showSuccess('URL exclusion added successfully');
    } catch (error) {
        console.error('Error adding exclusion:', error);
        showError('Failed to add URL exclusion');
    }
}

async function removeExclusion(url) {
    if (confirm('Are you sure you want to remove this exclusion?')) {
        try {
            await handleApiRequest('sitemap-exclusions', 'DELETE', { url });
            loadExclusions();
            showSuccess('Exclusion removed successfully');
        } catch (error) {
            console.error('Error removing exclusion:', error);
            showError('Failed to remove exclusion');
        }
    }
}

// Tool functions
async function generateSitemap() {
    try {
        await handleApiRequest('sitemap/generate', 'POST');
        loadSitemapInfo();
        showSuccess('Sitemap generated successfully');
    } catch (error) {
        console.error('Error generating sitemap:', error);
        showError('Failed to generate sitemap');
    }
}

async function clearCache() {
    try {
        await handleApiRequest('sitemap/clear-cache', 'POST');
        loadSitemapInfo();
        showSuccess('Cache cleared successfully');
    } catch (error) {
        console.error('Error clearing cache:', error);
        showError('Failed to clear cache');
    }
}

async function pingSearchEngines() {
    try {
        await handleApiRequest('sitemap/ping', 'POST');
        showSuccess('Search engines pinged successfully');
    } catch (error) {
        console.error('Error pinging search engines:', error);
        showError('Failed to ping search engines');
    }
}

async function validateSitemap() {
    try {
        const result = await handleApiRequest('sitemap/validate', 'POST');
        if (result.valid) {
            showSuccess('Sitemap is valid');
        } else {
            showError('Sitemap validation failed: ' + result.errors.join(', '));
        }
    } catch (error) {
        console.error('Error validating sitemap:', error);
        showError('Failed to validate sitemap');
    }
}

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
}

function formatFileSize(bytes) {
    const units = ['B', 'KB', 'MB', 'GB'];
    let size = bytes;
    let unitIndex = 0;
    
    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    
    return `${size.toFixed(2)} ${units[unitIndex]}`;
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
