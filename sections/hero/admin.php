<div class="content-header">
    <h1>Hero Section Settings</h1>
</div>

<div class="card">
    <form id="heroSectionForm" onsubmit="handleHeroSubmit(event)">
        <div class="form-group">
            <label for="heroTitle">Title</label>
            <input type="text" id="heroTitle" name="hero_title" class="form-control" required>
            <small>Main title displayed in the hero section. HTML tags like &lt;br&gt; are allowed.</small>
        </div>

        <div class="form-group">
            <label for="heroSubtitle">Subtitle</label>
            <input type="text" id="heroSubtitle" name="hero_subtitle" class="form-control" required>
            <small>Subtitle or tagline displayed below the main title</small>
        </div>

        <div class="form-group">
            <label for="heroCtaText">CTA Button Text</label>
            <input type="text" id="heroCtaText" name="hero_cta_text" class="form-control" required>
            <small>Call to action button text</small>
        </div>

        <div class="form-group">
            <label for="heroImage">Hero Image</label>
            <div class="input-group">
                <input type="text" id="heroImage" name="hero_image" class="form-control" required>
                <button type="button" class="btn btn-secondary" onclick="openMediaLibrary('heroImage')">Select Image</button>
            </div>
            <small>Main image displayed in the hero section</small>
        </div>

        <div class="form-group">
            <label for="heroImageAlt">Image Alt Text</label>
            <input type="text" id="heroImageAlt" name="hero_image_alt" class="form-control" required>
            <small>Alternative text for accessibility and SEO</small>
        </div>

        <div class="form-group">
            <label>Section Status</label>
            <div class="custom-control custom-switch">
                <input type="checkbox" class="custom-control-input" id="heroActive" name="is_active">
                <label class="custom-control-label" for="heroActive">Active</label>
            </div>
            <small>Enable or disable this section</small>
        </div>

        <div class="form-group">
            <label for="heroOrder">Display Order</label>
            <input type="number" id="heroOrder" name="sort_order" class="form-control" min="0" required>
            <small>Order in which this section appears on the page (0 = first)</small>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', loadHeroSettings);

async function loadHeroSettings() {
    try {
        // Load section settings
        const sectionResult = await handleApiRequest('sections/hero');
        const section = sectionResult.data;
        
        document.getElementById('heroActive').checked = section.is_active === 1;
        document.getElementById('heroOrder').value = section.sort_order;

        // Load hero-specific settings
        const configResult = await handleApiRequest('config?prefix=hero_');
        const config = configResult.data;
        
        document.getElementById('heroTitle').value = config.hero_title || '';
        document.getElementById('heroSubtitle').value = config.hero_subtitle || '';
        document.getElementById('heroCtaText').value = config.hero_cta_text || '';
        document.getElementById('heroImage').value = config.hero_image || '';
        document.getElementById('heroImageAlt').value = config.hero_image_alt || '';
    } catch (error) {
        console.error('Error loading hero settings:', error);
        showError('Failed to load hero section settings');
    }
}

async function handleHeroSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            section: {
                is_active: formData.get('is_active') === 'on' ? 1 : 0,
                sort_order: parseInt(formData.get('sort_order'))
            },
            config: {
                hero_title: formData.get('hero_title'),
                hero_subtitle: formData.get('hero_subtitle'),
                hero_cta_text: formData.get('hero_cta_text'),
                hero_image: formData.get('hero_image'),
                hero_image_alt: formData.get('hero_image_alt')
            }
        };

        // Update section settings
        await handleApiRequest('sections/hero', 'POST', data.section);
        
        // Update config settings
        await handleApiRequest('config/bulk', 'POST', data.config);
        
        showSuccess('Hero section updated successfully');
    } catch (error) {
        console.error('Error updating hero settings:', error);
        showError('Failed to update hero section');
    }
}

function openMediaLibrary(inputId) {
    // Open media library modal and handle image selection
    window.selectedImageInput = inputId;
    toggleModal('mediaLibraryModal', true);
}
</script>

<style>
.custom-switch {
    padding-left: 2.25rem;
}

.input-group {
    display: flex;
    gap: 10px;
}

.btn-secondary {
    white-space: nowrap;
}
</style>
