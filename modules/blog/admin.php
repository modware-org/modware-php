<div class="content-header">
    <h1>Blog Management</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('posts')">Posts</button>
        <button class="tab-btn" onclick="switchTab('categories')">Categories</button>
        <button class="tab-btn" onclick="switchTab('tags')">Tags</button>
        <button class="tab-btn" onclick="switchTab('comments')">Comments</button>
        <button class="tab-btn" onclick="switchTab('settings')">Settings</button>
    </div>

    <!-- Posts Tab -->
    <div id="postsTab" class="tab-content active">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddPostModal()">Add New Post</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="postsTable">
                    <tr>
                        <td colspan="6">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Categories Tab -->
    <div id="categoriesTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddCategoryModal()">Add Category</button>
        </div>

        <div id="categoriesTree" class="tree-view">
            <!-- Categories will be loaded here -->
        </div>
    </div>

    <!-- Tags Tab -->
    <div id="tagsTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddTagModal()">Add Tag</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Posts</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tagsTable">
                    <tr>
                        <td colspan="4">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Comments Tab -->
    <div id="commentsTab" class="tab-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Comment</th>
                        <th>Post</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="commentsTable">
                    <tr>
                        <td colspan="6">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Settings Tab -->
    <div id="settingsTab" class="tab-content">
        <form id="blogSettingsForm" onsubmit="handleSettingsSubmit(event)">
            <div class="form-group">
                <label for="postsPerPage">Posts Per Page</label>
                <input type="number" id="postsPerPage" name="posts_per_page" class="form-control" min="1" required>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="showAuthor" name="show_author">
                    <label class="custom-control-label" for="showAuthor">Show Author</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="showDate" name="show_date">
                    <label class="custom-control-label" for="showDate">Show Date</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="showCategories" name="show_categories">
                    <label class="custom-control-label" for="showCategories">Show Categories</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="showTags" name="show_tags">
                    <label class="custom-control-label" for="showTags">Show Tags</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="showComments" name="show_comments">
                    <label class="custom-control-label" for="showComments">Enable Comments</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="moderateComments" name="moderate_comments">
                    <label class="custom-control-label" for="moderateComments">Moderate Comments</label>
                </div>
            </div>

            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="notifyComments" name="notify_comments">
                    <label class="custom-control-label" for="notifyComments">Email Notifications for Comments</label>
                </div>
            </div>

            <div class="form-group">
                <label for="excerptLength">Excerpt Length</label>
                <input type="number" id="excerptLength" name="excerpt_length" class="form-control" min="1" required>
            </div>

            <div class="form-group">
                <label for="sidebarPosition">Sidebar Position</label>
                <select id="sidebarPosition" name="sidebar_position" class="form-control">
                    <option value="right">Right</option>
                    <option value="left">Left</option>
                    <option value="none">None</option>
                </select>
            </div>

            <div class="form-group">
                <label for="featuredImageSize">Featured Image Size</label>
                <select id="featuredImageSize" name="featured_image_size" class="form-control">
                    <option value="large">Large</option>
                    <option value="medium">Medium</option>
                    <option value="small">Small</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>
</div>

<!-- Add/Edit Post Modal -->
<div id="postModal" class="modal" style="display: none;">
    <div class="modal-content modal-lg">
        <div class="modal-header">
            <h2 id="postModalTitle">Add New Post</h2>
            <button class="btn btn-text" onclick="toggleModal('postModal', false)">&times;</button>
        </div>
        <form id="postForm" onsubmit="handlePostSubmit(event)">
            <input type="hidden" id="postId" name="id">
            <div class="form-group">
                <label for="postTitle">Title</label>
                <input type="text" id="postTitle" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="postSlug">Slug</label>
                <input type="text" id="postSlug" name="slug" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="postContent">Content</label>
                <textarea id="postContent" name="content" class="form-control rich-editor" rows="10"></textarea>
            </div>
            <div class="form-group">
                <label for="postExcerpt">Excerpt</label>
                <textarea id="postExcerpt" name="excerpt" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="postCategory">Category</label>
                <select id="postCategory" name="category_id" class="form-control">
                    <!-- Categories will be loaded dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="postTags">Tags</label>
                <select id="postTags" name="tags[]" class="form-control" multiple>
                    <!-- Tags will be loaded dynamically -->
                </select>
            </div>
            <div class="form-group">
                <label for="postFeaturedImage">Featured Image</label>
                <div class="input-group">
                    <input type="text" id="postFeaturedImage" name="featured_image" class="form-control">
                    <button type="button" class="btn btn-secondary" onclick="openMediaLibrary('postFeaturedImage')">Select Image</button>
                </div>
            </div>
            <div class="form-group">
                <label for="postStatus">Status</label>
                <select id="postStatus" name="status" class="form-control">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('postModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Post</button>
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
            </div>
            <div class="form-group">
                <label for="categoryDescription">Description</label>
                <textarea id="categoryDescription" name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="categoryParent">Parent Category</label>
                <select id="categoryParent" name="parent_id" class="form-control">
                    <option value="">None</option>
                    <!-- Categories will be loaded dynamically -->
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('categoryModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Add/Edit Tag Modal -->
<div id="tagModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="tagModalTitle">Add Tag</h2>
            <button class="btn btn-text" onclick="toggleModal('tagModal', false)">&times;</button>
        </div>
        <form id="tagForm" onsubmit="handleTagSubmit(event)">
            <input type="hidden" id="tagId" name="id">
            <div class="form-group">
                <label for="tagName">Name</label>
                <input type="text" id="tagName" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tagSlug">Slug</label>
                <input type="text" id="tagSlug" name="slug" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="tagDescription">Description</label>
                <textarea id="tagDescription" name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('tagModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Tag</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadPosts();
    loadCategories();
    loadTags();
    loadComments();
    loadSettings();
    initRichEditor();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

// Posts Management
async function loadPosts() {
    try {
        const posts = await handleApiRequest('blog-posts');
        const tbody = document.getElementById('postsTable');
        
        if (posts.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No posts found</td></tr>';
            return;
        }

        tbody.innerHTML = posts.map(post => `
            <tr>
                <td>${post.title}</td>
                <td>${post.category_name || ''}</td>
                <td>${post.author_name}</td>
                <td>
                    <span class="badge ${post.status === 'published' ? 'badge-success' : 'badge-warning'}">
                        ${post.status}
                    </span>
                </td>
                <td>${formatDate(post.published_at || post.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editPost(${post.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deletePost(${post.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading posts:', error);
        showError('Failed to load posts');
    }
}

function openAddPostModal() {
    document.getElementById('postModalTitle').textContent = 'Add New Post';
    document.getElementById('postForm').reset();
    document.getElementById('postId').value = '';
    toggleModal('postModal', true);
}

async function editPost(id) {
    try {
        const post = await handleApiRequest(`blog-posts/${id}`);
        document.getElementById('postModalTitle').textContent = 'Edit Post';
        document.getElementById('postId').value = post.id;
        document.getElementById('postTitle').value = post.title;
        document.getElementById('postSlug').value = post.slug;
        document.getElementById('postContent').value = post.content;
        document.getElementById('postExcerpt').value = post.excerpt || '';
        document.getElementById('postCategory').value = post.category_id || '';
        document.getElementById('postFeaturedImage').value = post.featured_image || '';
        document.getElementById('postStatus').value = post.status;
        
        // Set selected tags
        const tagSelect = document.getElementById('postTags');
        Array.from(tagSelect.options).forEach(option => {
            option.selected = post.tags.some(tag => tag.id === parseInt(option.value));
        });
        
        toggleModal('postModal', true);
    } catch (error) {
        console.error('Error loading post:', error);
        showError('Failed to load post details');
    }
}

async function handlePostSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            title: formData.get('title'),
            slug: formData.get('slug'),
            content: formData.get('content'),
            excerpt: formData.get('excerpt'),
            category_id: formData.get('category_id'),
            featured_image: formData.get('featured_image'),
            status: formData.get('status'),
            tags: Array.from(event.target.querySelector('[name="tags[]"]').selectedOptions).map(opt => opt.value)
        };

        await handleApiRequest('blog-posts' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('postModal', false);
        loadPosts();
        showSuccess('Post saved successfully');
    } catch (error) {
        console.error('Error saving post:', error);
        showError('Failed to save post');
    }
}

async function deletePost(id) {
    if (confirm('Are you sure you want to delete this post?')) {
        try {
            await handleApiRequest(`blog-posts/${id}`, 'DELETE');
            loadPosts();
            showSuccess('Post deleted successfully');
        } catch (error) {
            console.error('Error deleting post:', error);
            showError('Failed to delete post');
        }
    }
}

// Categories Management
async function loadCategories() {
    try {
        const categories = await handleApiRequest('menu-categories');
        renderCategoryTree(categories);
        updateCategoryOptions(categories);
    } catch (error) {
        console.error('Error loading categories:', error);
        showError('Failed to load categories');
    }
}

function renderCategoryTree(categories, parentId = null) {
    const container = document.getElementById('categoriesTree');
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
            li.appendChild(renderCategoryTree(categories, category.id));
        }

        list.appendChild(li);
    });

    return list;
}

// Tags Management
async function loadTags() {
    try {
        const tags = await handleApiRequest('tags');
        const tbody = document.getElementById('tagsTable');
        
        if (tags.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4">No tags found</td></tr>';
            return;
        }

        tbody.innerHTML = tags.map(tag => `
            <tr>
                <td>${tag.name}</td>
                <td>${tag.slug}</td>
                <td>${tag.post_count}</td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editTag(${tag.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTag(${tag.id})">Delete</button>
                </td>
            </tr>
        `).join('');

        // Update tags select in post form
        const tagSelect = document.getElementById('postTags');
        if (tagSelect) {
            tagSelect.innerHTML = tags.map(tag => 
                `<option value="${tag.id}">${tag.name}</option>`
            ).join('');
        }
    } catch (error) {
        console.error('Error loading tags:', error);
        showError('Failed to load tags');
    }
}

// Comments Management
async function loadComments() {
    try {
        const comments = await handleApiRequest('blog-comments');
        const tbody = document.getElementById('commentsTable');
        
        if (comments.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">No comments found</td></tr>';
            return;
        }

        tbody.innerHTML = comments.map(comment => `
            <tr>
                <td>
                    ${comment.author_name}
                    <br>
                    <small>${comment.author_email}</small>
                </td>
                <td>${comment.content.substring(0, 100)}...</td>
                <td>${comment.post_title}</td>
                <td>${formatDate(comment.created_at)}</td>
                <td>
                    <span class="badge ${comment.status === 'approved' ? 'badge-success' : 'badge-warning'}">
                        ${comment.status}
                    </span>
                </td>
                <td>
                    ${comment.status === 'pending' ? 
                        `<button class="btn btn-sm btn-success" onclick="approveComment(${comment.id})">Approve</button>` : 
                        ''}
                    <button class="btn btn-sm btn-danger" onclick="deleteComment(${comment.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading comments:', error);
        showError('Failed to load comments');
    }
}

async function approveComment(id) {
    try {
        await handleApiRequest(`blog-comments/${id}/approve`, 'POST');
        loadComments();
        showSuccess('Comment approved successfully');
    } catch (error) {
        console.error('Error approving comment:', error);
        showError('Failed to approve comment');
    }
}

async function deleteComment(id) {
    if (confirm('Are you sure you want to delete this comment?')) {
        try {
            await handleApiRequest(`blog-comments/${id}`, 'DELETE');
            loadComments();
            showSuccess('Comment deleted successfully');
        } catch (error) {
            console.error('Error deleting comment:', error);
            showError('Failed to delete comment');
        }
    }
}

// Settings Management
async function loadSettings() {
    try {
        const config = await handleApiRequest('config?prefix=blog_');
        
        document.getElementById('postsPerPage').value = config.blog_posts_per_page || '10';
        document.getElementById('showAuthor').checked = config.blog_show_author === 'true';
        document.getElementById('showDate').checked = config.blog_show_date === 'true';
        document.getElementById('showCategories').checked = config.blog_show_categories === 'true';
        document.getElementById('showTags').checked = config.blog_show_tags === 'true';
        document.getElementById('showComments').checked = config.blog_show_comments === 'true';
        document.getElementById('moderateComments').checked = config.blog_moderate_comments === 'true';
        document.getElementById('notifyComments').checked = config.blog_notify_comments === 'true';
        document.getElementById('excerptLength').value = config.blog_excerpt_length || '150';
        document.getElementById('sidebarPosition').value = config.blog_sidebar_position || 'right';
        document.getElementById('featuredImageSize').value = config.blog_featured_image_size || 'large';
    } catch (error) {
        console.error('Error loading settings:', error);
        showError('Failed to load settings');
    }
}

async function handleSettingsSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            blog_posts_per_page: formData.get('posts_per_page'),
            blog_show_author: formData.get('show_author') === 'on' ? 'true' : 'false',
            blog_show_date: formData.get('show_date') === 'on' ? 'true' : 'false',
            blog_show_categories: formData.get('show_categories') === 'on' ? 'true' : 'false',
            blog_show_tags: formData.get('show_tags') === 'on' ? 'true' : 'false',
            blog_show_comments: formData.get('show_comments') === 'on' ? 'true' : 'false',
            blog_moderate_comments: formData.get('moderate_comments') === 'on' ? 'true' : 'false',
            blog_notify_comments: formData.get('notify_comments') === 'on' ? 'true' : 'false',
            blog_excerpt_length: formData.get('excerpt_length'),
            blog_sidebar_position: formData.get('sidebar_position'),
            blog_featured_image_size: formData.get('featured_image_size')
        };

        await handleApiRequest('config/bulk', 'POST', data);
        showSuccess('Settings saved successfully');
    } catch (error) {
        console.error('Error saving settings:', error);
        showError('Failed to save settings');
    }
}

// Rich Text Editor
function initRichEditor() {
    const editor = document.querySelector('.rich-editor');
    if (!editor) return;

    // Initialize your preferred rich text editor here
    // For example, TinyMCE, CKEditor, etc.
}

// Utility Functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('ru-RU');
}

function slugify(text) {
    return text.toString().toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '');
}

// Auto-generate slug from title
document.getElementById('postTitle')?.addEventListener('input', function() {
    const slugInput = document.getElementById('postSlug');
    if (slugInput && !slugInput.value) {
        slugInput.value = slugify(this.value);
    }
});

document.getElementById('categoryName')?.addEventListener('input', function() {
    const slugInput = document.getElementById('categorySlug');
    if (slugInput && !slugInput.value) {
        slugInput.value = slugify(this.value);
    }
});

document.getElementById('tagName')?.addEventListener('input', function() {
    const slugInput = document.getElementById('tagSlug');
    if (slugInput && !slugInput.value) {
        slugInput.value = slugify(this.value);
    }
});
</script>

<style>
.modal-lg {
    max-width: 800px;
    width: 90%;
}

.tree-view {
    margin: 1rem 0;
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

.badge {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 12px;
}

.badge-success {
    background: var(--success-color);
    color: white;
}

.badge-warning {
    background: var(--warning-color);
    color: white;
}

@media (max-width: 768px) {
    .tree-list {
        padding-left: 10px;
    }

    .tree-item-content {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}
</style>
