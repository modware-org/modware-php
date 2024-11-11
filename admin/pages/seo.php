<div class="content-header">
    <h1>SEO Management</h1>
</div>

<div class="card">
    <div class="seo-tabs">
        <button class="tab-btn active" onclick="switchTab('global')">Global SEO</button>
        <button class="tab-btn" onclick="switchTab('pages')">Page SEO</button>
    </div>

    <div id="globalSeoTab" class="tab-content active">
        <form id="globalSeoForm" onsubmit="handleGlobalSeoSubmit(event)">
            <div class="form-group">
                <label for="defaultTitle">Default Meta Title</label>
                <input type="text" id="defaultTitle" name="default_title" class="form-control" required>
                <small>Default title for pages without specific SEO settings</small>
            </div>
            <div class="form-group">
                <label for="defaultDescription">Default Meta Description</label>
                <textarea id="defaultDescription" name="default_description" class="form-control" rows="3" required></textarea>
                <small>Default description for pages without specific SEO settings</small>
            </div>
            <div class="form-group">
                <label for="defaultKeywords">Default Keywords</label>
                <input type="text" id="defaultKeywords" name="default_keywords" class="form-control">
                <small>Comma-separated keywords</small>
            </div>
            <div class="form-group">
                <label for="ogImage">Default OG Image</label>
                <input type="text" id="ogImage" name="og_image" class="form-control">
                <small>Default social sharing image URL</small>
            </div>
            <button type="submit" class="btn btn-primary">Save Global SEO Settings</button>
        </form>
    </div>

    <div id="pagesSeoTab" class="tab-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Page</th>
                        <th>Meta Title</th>
                        <th>Meta Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="pageSeoTable">
                    <tr>
                        <td colspan="4">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Page SEO Modal -->
<div id="editSeoModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Page SEO</h2>
            <button class="btn btn-text" onclick="toggleModal('editSeoModal', false)">&times;</button>
        </div>
        <form id="editSeoForm" onsubmit="handlePageSeoSubmit(event)">
            <input type="hidden" id="editPageId" name="page_id">
            <div class="form-group">
                <label for="editMetaTitle">Meta Title</label>
                <input type="text" id="editMetaTitle" name="meta_title" class="form-control" required>
                <div class="meta-title-counter counter"></div>
            </div>
            <div class="form-group">
                <label for="editMetaDescription">Meta Description</label>
                <textarea id="editMetaDescription" name="meta_description" class="form-control" rows="3" required></textarea>
                <div class="meta-description-counter counter"></div>
            </div>
            <div class="form-group">
                <label for="editMetaKeywords">Meta Keywords</label>
                <input type="text" id="editMetaKeywords" name="meta_keywords" class="form-control">
                <small>Comma-separated keywords</small>
            </div>
            <div class="form-group">
                <label for="editOgTitle">OG Title</label>
                <input type="text" id="editOgTitle" name="og_title" class="form-control">
            </div>
            <div class="form-group">
                <label for="editOgDescription">OG Description</label>
                <textarea id="editOgDescription" name="og_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="editOgImage">OG Image</label>
                <input type="text" id="editOgImage" name="og_image" class="form-control">
            </div>
            <div class="form-group">
                <label for="editCanonicalUrl">Canonical URL</label>
                <input type="text" id="editCanonicalUrl" name="canonical_url" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('editSeoModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Update SEO</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadGlobalSeo();
    loadPagesSeo();
    initializeCounters();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}SeoTab`).classList.add('active');
}

async function loadGlobalSeo() {
    try {
        const globalSeo = await handleApiRequest('seo/global');
        document.getElementById('defaultTitle').value = globalSeo.default_title || '';
        document.getElementById('defaultDescription').value = globalSeo.default_description || '';
        document.getElementById('defaultKeywords').value = globalSeo.default_keywords || '';
        document.getElementById('ogImage').value = globalSeo.og_image || '';
    } catch (error) {
        console.error('Error loading global SEO:', error);
    }
}

async function loadPagesSeo() {
    try {
        const pages = await handleApiRequest('seo/pages');
        const tbody = document.getElementById('pageSeoTable');
        
        if (pages.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No pages found</td></tr>';
            return;
        }

        tbody.innerHTML = pages.map(page => `
            <tr>
                <td>${page.title}</td>
                <td>${page.meta_title || '<em>Using default</em>'}</td>
                <td>${page.meta_description ? page.meta_description.substring(0, 50) + '...' : '<em>Using default</em>'}</td>
                <td>
                    <button class="btn btn-secondary btn-sm" onclick="editPageSeo(${page.id})">Edit SEO</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading pages SEO:', error);
    }
}

function initializeCounters() {
    // Meta title counter
    document.getElementById('editMetaTitle').addEventListener('input', function() {
        const counter = document.querySelector('.meta-title-counter');
        counter.textContent = `${this.value.length}/60 characters`;
        counter.style.color = this.value.length > 60 ? 'red' : 'inherit';
    });

    // Meta description counter
    document.getElementById('editMetaDescription').addEventListener('input', function() {
        const counter = document.querySelector('.meta-description-counter');
        counter.textContent = `${this.value.length}/160 characters`;
        counter.style.color = this.value.length > 160 ? 'red' : 'inherit';
    });
}

async function handleGlobalSeoSubmit(event) {
    event.preventDefault();
    try {
        await handleFormSubmit(event.target, 'seo/global');
        alert('Global SEO settings updated successfully');
    } catch (error) {
        console.error('Error updating global SEO:', error);
    }
}

async function editPageSeo(pageId) {
    try {
        const seo = await handleApiRequest(`seo/pages/${pageId}`);
        document.getElementById('editPageId').value = pageId;
        document.getElementById('editMetaTitle').value = seo.meta_title || '';
        document.getElementById('editMetaDescription').value = seo.meta_description || '';
        document.getElementById('editMetaKeywords').value = seo.meta_keywords || '';
        document.getElementById('editOgTitle').value = seo.og_title || '';
        document.getElementById('editOgDescription').value = seo.og_description || '';
        document.getElementById('editOgImage').value = seo.og_image || '';
        document.getElementById('editCanonicalUrl').value = seo.canonical_url || '';
        
        // Trigger counters
        document.getElementById('editMetaTitle').dispatchEvent(new Event('input'));
        document.getElementById('editMetaDescription').dispatchEvent(new Event('input'));
        
        toggleModal('editSeoModal', true);
    } catch (error) {
        console.error('Error loading page SEO:', error);
    }
}

async function handlePageSeoSubmit(event) {
    event.preventDefault();
    try {
        const pageId = document.getElementById('editPageId').value;
        await handleFormSubmit(event.target, `seo/pages/${pageId}`);
        toggleModal('editSeoModal', false);
        loadPagesSeo();
    } catch (error) {
        console.error('Error updating page SEO:', error);
    }
}
</script>

<style>
.seo-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

.tab-btn {
    padding: 10px 20px;
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
}

.tab-content.active {
    display: block;
}

.counter {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
    text-align: right;
}
</style>
