<div class="content-header">
    <h1>Contact Form Settings</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('settings')">Form Settings</button>
        <button class="tab-btn" onclick="switchTab('messages')">Messages</button>
    </div>

    <!-- Settings Tab -->
    <div id="settingsTab" class="tab-content active">
        <form id="formSettingsForm" onsubmit="handleSettingsSubmit(event)">
            <div class="form-group">
                <label>Form Status</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="formActive" name="is_active">
                    <label class="custom-control-label" for="formActive">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label for="recipientEmail">Recipient Email</label>
                <input type="email" id="recipientEmail" name="recipient_email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="emailSubject">Email Subject Template</label>
                <input type="text" id="emailSubject" name="email_subject" class="form-control" required>
                <small class="form-text text-muted">Use {name} and {subject} as placeholders</small>
            </div>

            <div class="form-group">
                <label for="successMessage">Success Message</label>
                <textarea id="successMessage" name="success_message" class="form-control" required></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save Settings</button>
        </form>
    </div>

    <!-- Messages Tab -->
    <div id="messagesTab" class="tab-content">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="messagesTable">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- View Message Modal -->
<div id="messageModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Message Details</h2>
            <button class="btn btn-text" onclick="toggleModal('messageModal', false)">&times;</button>
        </div>
        <div class="modal-body">
            <div class="message-details">
                <p><strong>From:</strong> <span id="modalName"></span></p>
                <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
                <p><strong>Date:</strong> <span id="modalDate"></span></p>
                <hr>
                <p><strong>Message:</strong></p>
                <div id="modalMessage" class="message-content"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="toggleModal('messageModal', false)">Close</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadFormSettings();
    loadMessages();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

async function loadFormSettings() {
    try {
        const settings = await handleApiRequest('contact-form/settings');
        document.getElementById('formActive').checked = settings.is_active === 1;
        document.getElementById('recipientEmail').value = settings.recipient_email;
        document.getElementById('emailSubject').value = settings.email_subject;
        document.getElementById('successMessage').value = settings.success_message;
    } catch (error) {
        console.error('Error loading form settings:', error);
        showError('Failed to load form settings');
    }
}

async function loadMessages() {
    try {
        const messages = await handleApiRequest('contact-form/messages');
        const tbody = document.getElementById('messagesTable');
        
        if (messages.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">No messages found</td></tr>';
            return;
        }

        tbody.innerHTML = messages.map(msg => `
            <tr>
                <td>${formatDate(msg.created_at)}</td>
                <td>${msg.name}</td>
                <td>${msg.email}</td>
                <td>${msg.subject}</td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="viewMessage(${msg.id})">View</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteMessage(${msg.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading messages:', error);
        showError('Failed to load messages');
    }
}

async function handleSettingsSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            is_active: formData.get('is_active') === 'on' ? 1 : 0,
            recipient_email: formData.get('recipient_email'),
            email_subject: formData.get('email_subject'),
            success_message: formData.get('success_message')
        };

        await handleApiRequest('contact-form/settings', 'POST', data);
        showSuccess('Form settings updated successfully');
    } catch (error) {
        console.error('Error updating settings:', error);
        showError('Failed to update form settings');
    }
}

async function viewMessage(id) {
    try {
        const message = await handleApiRequest(`contact-form/messages/${id}`);
        document.getElementById('modalName').textContent = message.name;
        document.getElementById('modalEmail').textContent = message.email;
        document.getElementById('modalSubject').textContent = message.subject;
        document.getElementById('modalDate').textContent = formatDate(message.created_at);
        document.getElementById('modalMessage').textContent = message.message;
        toggleModal('messageModal', true);
    } catch (error) {
        console.error('Error loading message:', error);
        showError('Failed to load message details');
    }
}

async function deleteMessage(id) {
    if (confirm('Are you sure you want to delete this message?')) {
        try {
            await handleApiRequest(`contact-form/messages/${id}`, 'DELETE');
            loadMessages();
            showSuccess('Message deleted successfully');
        } catch (error) {
            console.error('Error deleting message:', error);
            showError('Failed to delete message');
        }
    }
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleString();
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

.message-content {
    white-space: pre-wrap;
    background: var(--bg-light);
    padding: 15px;
    border-radius: 4px;
    margin-top: 10px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    padding: 12px;
    border-bottom: 1px solid var(--border-color);
}

.table th {
    text-align: left;
    background: var(--bg-light);
}
</style>
