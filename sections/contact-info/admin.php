<div class="content-header">
    <h1>Contact Info Settings</h1>
</div>

<div class="card">
    <form id="contactInfoForm" onsubmit="handleContactInfoSubmit(event)">
        <div class="form-group">
            <label for="phone">Phone Number</label>
            <input type="text" id="phone" name="phone" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="form-control" rows="3" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadContactInfo();
});

async function loadContactInfo() {
    try {
        const info = await handleApiRequest('contact-info');
        document.getElementById('phone').value = info.phone;
        document.getElementById('email').value = info.email;
        document.getElementById('address').value = info.address;
    } catch (error) {
        console.error('Error loading contact info:', error);
        showError('Failed to load contact information');
    }
}

async function handleContactInfoSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            phone: formData.get('phone'),
            email: formData.get('email'),
            address: formData.get('address')
        };

        await handleApiRequest('contact-info', 'POST', data);
        showSuccess('Contact information updated successfully');
    } catch (error) {
        console.error('Error updating contact info:', error);
        showError('Failed to update contact information');
    }
}
</script>

<style>
.card {
    padding: 20px;
    background: var(--bg-light);
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}
</style>
