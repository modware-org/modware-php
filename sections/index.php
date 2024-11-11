<?php
require_once __DIR__ . '/../config/Database.php';

class SectionManager {
    private $db;
    private $sections = [];

    public function __construct() {
        $this->db = Database::getInstance();
        $this->loadSections();
    }

    private function loadSections() {
        $result = $this->db->getConnection()->query(
            "SELECT * FROM sections ORDER BY sort_order ASC"
        );
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $this->sections[] = $row;
        }
    }

    public function renderSectionTabs() {
        echo '<div class="section-tabs">';
        foreach ($this->sections as $section) {
            $activeClass = isset($_GET['section']) && $_GET['section'] === $section['name'] ? 'active' : '';
            echo sprintf(
                '<button class="section-tab %s" onclick="loadSection(\'%s\')">%s</button>',
                $activeClass,
                htmlspecialchars($section['name']),
                htmlspecialchars($section['title'])
            );
        }
        echo '</div>';
    }

    public function renderSectionContent() {
        $sectionName = $_GET['section'] ?? null;
        
        if (!$sectionName) {
            echo '<div class="alert alert-info">Please select a section to edit</div>';
            return;
        }

        $adminFile = __DIR__ . "/{$sectionName}/admin.php";
        if (file_exists($adminFile)) {
            include $adminFile;
        } else {
            echo '<div class="alert alert-danger">Section admin interface not found</div>';
        }
    }
}

// Initialize section manager
$sectionManager = new SectionManager();
?>

<div class="content-header">
    <h1>Landing Page Sections</h1>
    <button class="btn btn-primary" onclick="reorderSections()">Reorder Sections</button>
</div>

<div class="card">
    <?php $sectionManager->renderSectionTabs(); ?>
    
    <div class="section-content">
        <?php $sectionManager->renderSectionContent(); ?>
    </div>
</div>

<!-- Reorder Sections Modal -->
<div id="reorderModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Reorder Sections</h2>
            <button class="btn btn-text" onclick="toggleModal('reorderModal', false)">&times;</button>
        </div>
        <div class="modal-body">
            <div id="sectionList" class="sortable-list">
                <!-- Sections will be loaded here -->
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="toggleModal('reorderModal', false)">Cancel</button>
            <button type="button" class="btn btn-primary" onclick="saveSectionOrder()">Save Order</button>
        </div>
    </div>
</div>

<script>
function loadSection(sectionName) {
    window.location.href = `?page=sections&section=${sectionName}`;
}

async function reorderSections() {
    try {
        const sections = await handleApiRequest('sections');
        const list = document.getElementById('sectionList');
        
        list.innerHTML = sections.map(section => `
            <div class="sortable-item" data-id="${section.id}">
                <span class="drag-handle">☰</span>
                <span class="section-title">${section.title}</span>
                <span class="section-status ${section.is_active ? 'active' : 'inactive'}">
                    ${section.is_active ? 'Active' : 'Inactive'}
                </span>
            </div>
        `).join('');

        // Initialize sortable
        new Sortable(list, {
            animation: 150,
            handle: '.drag-handle',
            ghostClass: 'sortable-ghost'
        });

        toggleModal('reorderModal', true);
    } catch (error) {
        console.error('Error loading sections:', error);
        showError('Failed to load sections');
    }
}

async function saveSectionOrder() {
    try {
        const items = document.querySelectorAll('.sortable-item');
        const order = Array.from(items).map((item, index) => ({
            id: parseInt(item.dataset.id),
            sort_order: index
        }));

        await handleApiRequest('sections/reorder', 'POST', { order });
        
        toggleModal('reorderModal', false);
        showSuccess('Section order updated successfully');
        setTimeout(() => window.location.reload(), 1000);
    } catch (error) {
        console.error('Error saving section order:', error);
        showError('Failed to save section order');
    }
}
</script>

<style>
.section-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 10px;
    overflow-x: auto;
}

.section-tab {
    padding: 8px 16px;
    border: none;
    background: none;
    cursor: pointer;
    color: var(--text-muted);
    position: relative;
    white-space: nowrap;
}

.section-tab:hover {
    color: var(--primary-color);
}

.section-tab.active {
    color: var(--primary-color);
}

.section-tab.active::after {
    content: '';
    position: absolute;
    bottom: -11px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--primary-color);
}

.section-content {
    padding: 20px;
}

.sortable-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sortable-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px;
    background: var(--bg-light);
    border-radius: 4px;
    cursor: move;
}

.drag-handle {
    color: var(--text-muted);
    cursor: move;
}

.section-title {
    flex: 1;
}

.section-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
}

.section-status.active {
    background: var(--success-color);
    color: white;
}

.section-status.inactive {
    background: var(--danger-color);
    color: white;
}

.sortable-ghost {
    opacity: 0.5;
}
</style>
