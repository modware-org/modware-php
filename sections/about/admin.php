<div class="content-header">
    <h1>About Section Settings</h1>
</div>

<div class="card">
    <div class="section-tabs">
        <button class="tab-btn active" onclick="switchTab('section')">Section Settings</button>
        <button class="tab-btn" onclick="switchTab('team')">Team Members</button>
        <button class="tab-btn" onclick="switchTab('certification')">Certification</button>
    </div>

    <!-- Section Settings Tab -->
    <div id="sectionTab" class="tab-content active">
        <form id="aboutSectionForm" onsubmit="handleSectionSubmit(event)">
            <div class="form-group">
                <label>Section Status</label>
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="aboutActive" name="is_active">
                    <label class="custom-control-label" for="aboutActive">Active</label>
                </div>
            </div>

            <div class="form-group">
                <label for="aboutOrder">Display Order</label>
                <input type="number" id="aboutOrder" name="sort_order" class="form-control" min="0" required>
            </div>

            <button type="submit" class="btn btn-primary">Save Section Settings</button>
        </form>
    </div>

    <!-- Team Members Tab -->
    <div id="teamTab" class="tab-content">
        <div class="mb-3">
            <button class="btn btn-primary" onclick="openAddTeamMemberModal()">Add Team Member</button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="teamMembersTable">
                    <tr>
                        <td colspan="5">Loading...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Certification Tab -->
    <div id="certificationTab" class="tab-content">
        <form id="certificationForm" onsubmit="handleCertificationSubmit(event)">
            <div class="form-group">
                <label for="institution">Institution</label>
                <input type="text" id="institution" name="institution" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="program">Program</label>
                <input type="text" id="program" name="program" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="part1Dates">Part 1 Dates</label>
                <input type="text" id="part1Dates" name="part1_dates" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="part2Dates">Part 2 Dates</label>
                <input type="text" id="part2Dates" name="part2_dates" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="certFile">Certificate File</label>
                <div class="input-group">
                    <input type="text" id="certFile" name="certificate_file" class="form-control">
                    <button type="button" class="btn btn-secondary" onclick="openMediaLibrary('certFile')">Select File</button>
                </div>
            </div>

            <h3 class="mt-4">Instructors</h3>
            <div id="instructorsList">
                <!-- Instructors will be added here -->
            </div>

            <button type="button" class="btn btn-secondary mb-3" onclick="addInstructor()">Add Instructor</button>
            <button type="submit" class="btn btn-primary">Save Certification Details</button>
        </form>
    </div>
</div>

<!-- Add/Edit Team Member Modal -->
<div id="teamMemberModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="teamModalTitle">Add Team Member</h2>
            <button class="btn btn-text" onclick="toggleModal('teamMemberModal', false)">&times;</button>
        </div>
        <form id="teamMemberForm" onsubmit="handleTeamMemberSubmit(event)">
            <input type="hidden" id="teamMemberId" name="id">
            <div class="form-group">
                <label for="memberName">Name</label>
                <input type="text" id="memberName" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="memberPosition">Position</label>
                <input type="text" id="memberPosition" name="position" class="form-control">
            </div>
            <div class="form-group">
                <label for="memberBio">Bio</label>
                <textarea id="memberBio" name="bio" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="memberOrder">Sort Order</label>
                <input type="number" id="memberOrder" name="sort_order" class="form-control" min="0" required>
            </div>
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="checkbox" class="custom-control-input" id="memberActive" name="is_active" checked>
                    <label class="custom-control-label" for="memberActive">Active</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="toggleModal('teamMemberModal', false)">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    loadSectionSettings();
    loadTeamMembers();
    loadCertification();
});

function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
    
    document.querySelector(`[onclick="switchTab('${tab}')"]`).classList.add('active');
    document.getElementById(`${tab}Tab`).classList.add('active');
}

async function loadSectionSettings() {
    try {
        const section = await handleApiRequest('sections/about');
        document.getElementById('aboutActive').checked = section.is_active === 1;
        document.getElementById('aboutOrder').value = section.sort_order;
    } catch (error) {
        console.error('Error loading section settings:', error);
        showError('Failed to load section settings');
    }
}

async function loadTeamMembers() {
    try {
        const members = await handleApiRequest('team-members');
        const tbody = document.getElementById('teamMembersTable');
        
        if (members.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5">No team members found</td></tr>';
            return;
        }

        tbody.innerHTML = members.map(member => `
            <tr>
                <td>${member.sort_order}</td>
                <td>${member.name}</td>
                <td>${member.position || ''}</td>
                <td>
                    <span class="badge ${member.is_active ? 'badge-success' : 'badge-danger'}">
                        ${member.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-secondary" onclick="editTeamMember(${member.id})">Edit</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteTeamMember(${member.id})">Delete</button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Error loading team members:', error);
        showError('Failed to load team members');
    }
}

async function loadCertification() {
    try {
        const cert = await handleApiRequest('certification');
        document.getElementById('institution').value = cert.institution;
        document.getElementById('program').value = cert.program;
        document.getElementById('part1Dates').value = cert.part1_dates;
        document.getElementById('part2Dates').value = cert.part2_dates;
        document.getElementById('certFile').value = cert.certificate_file;
        
        // Load instructors
        const instructors = await handleApiRequest('certification/instructors');
        const container = document.getElementById('instructorsList');
        container.innerHTML = '';
        
        instructors.forEach((instructor, index) => {
            addInstructor(instructor);
        });
    } catch (error) {
        console.error('Error loading certification:', error);
        showError('Failed to load certification details');
    }
}

function addInstructor(data = null) {
    const container = document.getElementById('instructorsList');
    const index = container.children.length;
    
    const div = document.createElement('div');
    div.className = 'instructor-item mb-3 p-3 border rounded';
    div.innerHTML = `
        <div class="form-group">
            <label>Instructor Name</label>
            <input type="text" name="instructors[${index}][name]" class="form-control" value="${data?.name || ''}" required>
        </div>
        <div class="form-group">
            <label>Title</label>
            <input type="text" name="instructors[${index}][title]" class="form-control" value="${data?.title || ''}">
        </div>
        <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Remove</button>
    `;
    
    container.appendChild(div);
}

// Form submission handlers
async function handleSectionSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            is_active: formData.get('is_active') === 'on' ? 1 : 0,
            sort_order: parseInt(formData.get('sort_order'))
        };

        await handleApiRequest('sections/about', 'POST', data);
        showSuccess('Section settings updated successfully');
    } catch (error) {
        console.error('Error updating section:', error);
        showError('Failed to update section settings');
    }
}

async function handleTeamMemberSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            id: formData.get('id'),
            name: formData.get('name'),
            position: formData.get('position'),
            bio: formData.get('bio'),
            sort_order: parseInt(formData.get('sort_order')),
            is_active: formData.get('is_active') === 'on' ? 1 : 0
        };

        await handleApiRequest('team-members' + (data.id ? `/${data.id}` : ''), 'POST', data);
        toggleModal('teamMemberModal', false);
        loadTeamMembers();
        showSuccess('Team member saved successfully');
    } catch (error) {
        console.error('Error saving team member:', error);
        showError('Failed to save team member');
    }
}

async function handleCertificationSubmit(event) {
    event.preventDefault();
    try {
        const formData = new FormData(event.target);
        const data = {
            institution: formData.get('institution'),
            program: formData.get('program'),
            part1_dates: formData.get('part1_dates'),
            part2_dates: formData.get('part2_dates'),
            certificate_file: formData.get('certificate_file'),
            instructors: []
        };

        // Get instructors data
        const instructorInputs = event.target.querySelectorAll('.instructor-item');
        instructorInputs.forEach((item, index) => {
            data.instructors.push({
                name: formData.get(`instructors[${index}][name]`),
                title: formData.get(`instructors[${index}][title]`)
            });
        });

        await handleApiRequest('certification', 'POST', data);
        showSuccess('Certification details updated successfully');
    } catch (error) {
        console.error('Error updating certification:', error);
        showError('Failed to update certification details');
    }
}

// Helper functions
function openAddTeamMemberModal() {
    document.getElementById('teamModalTitle').textContent = 'Add Team Member';
    document.getElementById('teamMemberForm').reset();
    document.getElementById('teamMemberId').value = '';
    toggleModal('teamMemberModal', true);
}

async function editTeamMember(id) {
    try {
        const member = await handleApiRequest(`team-members/${id}`);
        document.getElementById('teamModalTitle').textContent = 'Edit Team Member';
        document.getElementById('teamMemberId').value = member.id;
        document.getElementById('memberName').value = member.name;
        document.getElementById('memberPosition').value = member.position || '';
        document.getElementById('memberBio').value = member.bio || '';
        document.getElementById('memberOrder').value = member.sort_order;
        document.getElementById('memberActive').checked = member.is_active === 1;
        toggleModal('teamMemberModal', true);
    } catch (error) {
        console.error('Error loading team member:', error);
        showError('Failed to load team member details');
    }
}

async function deleteTeamMember(id) {
    if (confirm('Are you sure you want to delete this team member?')) {
        try {
            await handleApiRequest(`team-members/${id}`, 'DELETE');
            loadTeamMembers();
            showSuccess('Team member deleted successfully');
        } catch (error) {
            console.error('Error deleting team member:', error);
            showError('Failed to delete team member');
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

.instructor-item {
    background: var(--bg-light);
    margin-bottom: 15px;
    position: relative;
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

.badge-danger {
    background: var(--danger-color);
    color: white;
}
</style>
