<?php
require_once __DIR__ . '/../../config/Database.php';

function handleTeamFullAdmin() {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_member':
                    $name = $_POST['name'] ?? '';
                    $position_key = $_POST['position_key'] ?? '';
                    $bio_key = $_POST['bio_key'] ?? '';
                    $photo = $_POST['photo'] ?? '';
                    $credentials = $_POST['credentials'] ?? '';
                    $specialties = json_encode($_POST['specialties'] ?? []);
                    $education = json_encode($_POST['education'] ?? []);
                    $publications = json_encode($_POST['publications'] ?? []);
                    $display_order = (int)($_POST['display_order'] ?? 0);
                    
                    $stmt = $conn->prepare("
                        INSERT INTO team_members (
                            name, position_key, bio_key, photo, credentials,
                            specialties, education, publications, display_order
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->bindParam(1, $name);
                    $stmt->bindParam(2, $position_key);
                    $stmt->bindParam(3, $bio_key);
                    $stmt->bindParam(4, $photo);
                    $stmt->bindParam(5, $credentials);
                    $stmt->bindParam(6, $specialties);
                    $stmt->bindParam(7, $education);
                    $stmt->bindParam(8, $publications);
                    $stmt->bindParam(9, $display_order);
                    
                    $stmt->execute();
                    break;
                    
                case 'update_member':
                    $id = (int)$_POST['id'];
                    $name = $_POST['name'] ?? '';
                    $position_key = $_POST['position_key'] ?? '';
                    $bio_key = $_POST['bio_key'] ?? '';
                    $photo = $_POST['photo'] ?? '';
                    $credentials = $_POST['credentials'] ?? '';
                    $specialties = json_encode($_POST['specialties'] ?? []);
                    $education = json_encode($_POST['education'] ?? []);
                    $publications = json_encode($_POST['publications'] ?? []);
                    $display_order = (int)($_POST['display_order'] ?? 0);
                    $active = (int)($_POST['active'] ?? 1);
                    
                    $stmt = $conn->prepare("
                        UPDATE team_members SET
                            name = ?, position_key = ?, bio_key = ?, photo = ?,
                            credentials = ?, specialties = ?, education = ?,
                            publications = ?, display_order = ?, active = ?,
                            updated_at = CURRENT_TIMESTAMP
                        WHERE id = ?
                    ");
                    
                    $stmt->bindParam(1, $name);
                    $stmt->bindParam(2, $position_key);
                    $stmt->bindParam(3, $bio_key);
                    $stmt->bindParam(4, $photo);
                    $stmt->bindParam(5, $credentials);
                    $stmt->bindParam(6, $specialties);
                    $stmt->bindParam(7, $education);
                    $stmt->bindParam(8, $publications);
                    $stmt->bindParam(9, $display_order);
                    $stmt->bindParam(10, $active);
                    $stmt->bindParam(11, $id);
                    
                    $stmt->execute();
                    break;
                    
                case 'delete_member':
                    $id = (int)$_POST['id'];
                    $stmt = $conn->prepare("DELETE FROM team_members WHERE id = ?");
                    $stmt->bindParam(1, $id);
                    $stmt->execute();
                    break;
            }
        }
    }
    
    // Fetch all team members for display
    $result = $conn->query("
        SELECT * FROM team_members 
        ORDER BY display_order ASC, name ASC
    ");
    
    $teamMembers = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $row['specialties'] = json_decode($row['specialties'], true) ?? [];
        $row['education'] = json_decode($row['education'], true) ?? [];
        $row['publications'] = json_decode($row['publications'], true) ?? [];
        $teamMembers[] = $row;
    }
    ?>
    
    <div class="admin-section">
        <h2>Team Members Management</h2>
        
        <!-- Add New Member Form -->
        <div class="admin-form">
            <h3>Add New Team Member</h3>
            <form method="POST" class="form-grid">
                <input type="hidden" name="action" value="add_member">
                
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="position_key">Position Key:</label>
                    <input type="text" id="position_key" name="position_key" required>
                </div>
                
                <div class="form-group">
                    <label for="bio_key">Bio Key:</label>
                    <input type="text" id="bio_key" name="bio_key" required>
                </div>
                
                <div class="form-group">
                    <label for="photo">Photo URL:</label>
                    <input type="text" id="photo" name="photo">
                </div>
                
                <div class="form-group">
                    <label for="credentials">Credentials:</label>
                    <input type="text" id="credentials" name="credentials">
                </div>
                
                <div class="form-group">
                    <label for="specialties">Specialties (one per line):</label>
                    <textarea id="specialties" name="specialties[]" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="education">Education (one per line):</label>
                    <textarea id="education" name="education[]" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="publications">Publications (one per line):</label>
                    <textarea id="publications" name="publications[]" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="display_order">Display Order:</label>
                    <input type="number" id="display_order" name="display_order" value="0">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Add Member</button>
                </div>
            </form>
        </div>
        
        <!-- Existing Members List -->
        <div class="admin-list">
            <h3>Existing Team Members</h3>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Active</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teamMembers as $member): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($member['name']); ?></td>
                                <td><?php echo htmlspecialchars($member['position_key']); ?></td>
                                <td>
                                    <form method="POST" class="inline-form">
                                        <input type="hidden" name="action" value="update_member">
                                        <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($member['name']); ?>">
                                        <input type="hidden" name="position_key" value="<?php echo htmlspecialchars($member['position_key']); ?>">
                                        <input type="hidden" name="bio_key" value="<?php echo htmlspecialchars($member['bio_key']); ?>">
                                        <input type="hidden" name="display_order" value="<?php echo $member['display_order']; ?>">
                                        <input type="checkbox" name="active" value="1" 
                                               <?php echo $member['active'] ? 'checked' : ''; ?>
                                               onchange="this.form.submit()">
                                    </form>
                                </td>
                                <td><?php echo $member['display_order']; ?></td>
                                <td>
                                    <button class="btn btn-edit" onclick="editMember(<?php echo htmlspecialchars(json_encode($member)); ?>)">
                                        Edit
                                    </button>
                                    <form method="POST" class="inline-form" onsubmit="return confirm('Are you sure you want to delete this member?')">
                                        <input type="hidden" name="action" value="delete_member">
                                        <input type="hidden" name="id" value="<?php echo $member['id']; ?>">
                                        <button type="submit" class="btn btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="editMemberModal" class="modal">
        <div class="modal-content">
            <h3>Edit Team Member</h3>
            <form method="POST" class="form-grid">
                <input type="hidden" name="action" value="update_member">
                <input type="hidden" name="id" id="edit_id">
                
                <!-- Same fields as Add form -->
                <div class="form-group">
                    <label for="edit_name">Name:</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_position_key">Position Key:</label>
                    <input type="text" id="edit_position_key" name="position_key" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_bio_key">Bio Key:</label>
                    <input type="text" id="edit_bio_key" name="bio_key" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_photo">Photo URL:</label>
                    <input type="text" id="edit_photo" name="photo">
                </div>
                
                <div class="form-group">
                    <label for="edit_credentials">Credentials:</label>
                    <input type="text" id="edit_credentials" name="credentials">
                </div>
                
                <div class="form-group">
                    <label for="edit_specialties">Specialties (one per line):</label>
                    <textarea id="edit_specialties" name="specialties[]" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_education">Education (one per line):</label>
                    <textarea id="edit_education" name="education[]" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_publications">Publications (one per line):</label>
                    <textarea id="edit_publications" name="publications[]" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="edit_display_order">Display Order:</label>
                    <input type="number" id="edit_display_order" name="display_order">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Member</button>
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editMember(member) {
            document.getElementById('edit_id').value = member.id;
            document.getElementById('edit_name').value = member.name;
            document.getElementById('edit_position_key').value = member.position_key;
            document.getElementById('edit_bio_key').value = member.bio_key;
            document.getElementById('edit_photo').value = member.photo;
            document.getElementById('edit_credentials').value = member.credentials;
            document.getElementById('edit_specialties').value = member.specialties.join('\n');
            document.getElementById('edit_education').value = member.education.join('\n');
            document.getElementById('edit_publications').value = member.publications.join('\n');
            document.getElementById('edit_display_order').value = member.display_order;
            
            document.getElementById('editMemberModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editMemberModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editMemberModal')) {
                closeEditModal();
            }
        }
    </script>
    <?php
}
