<?php
// Get all sections that have admin.php files
$sections = [];
$sectionsPath = __DIR__ . '/../../sections/';
$sectionDirs = glob($sectionsPath . '*', GLOB_ONLYDIR);

foreach ($sectionDirs as $dir) {
    $adminFile = $dir . '/admin.php';
    if (file_exists($adminFile)) {
        $sectionName = basename($dir);
        $sections[] = [
            'name' => $sectionName,
            'path' => $adminFile
        ];
    }
}
?>

<div class="sections-page">
    <div class="content-header">
        <h1>Sections Management</h1>
        <button class="btn btn-primary mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span class="menu-icon"></span>
        </button>
    </div>
    
    <div class="sections-grid">
        <?php foreach ($sections as $section): ?>
        <div class="section-card" data-section="<?= htmlspecialchars($section['name']) ?>">
            <h3><?= ucfirst($section['name']) ?> Section</h3>
            <div class="section-content">
                <?php include $section['path']; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.sections-page {
    padding: var(--spacing-md);
}

.sections-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: var(--spacing-lg);
    margin-top: var(--spacing-lg);
}

.section-card {
    background: var(--white);
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: var(--spacing-lg);
    transition: transform 0.2s ease;
}

.section-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.section-card h3 {
    margin: 0 0 var(--spacing-md) 0;
    padding-bottom: var(--spacing-md);
    border-bottom: 1px solid #eee;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.section-content {
    min-height: 100px;
}

.mobile-menu-toggle {
    display: none;
    padding: var(--spacing-sm);
    width: 40px;
    height: 40px;
    position: relative;
}

.menu-icon,
.menu-icon::before,
.menu-icon::after {
    content: '';
    display: block;
    background: var(--white);
    height: 2px;
    width: 24px;
    position: absolute;
    left: 8px;
    transition: transform 0.3s ease;
}

.menu-icon {
    top: 19px;
}

.menu-icon::before {
    top: -8px;
}

.menu-icon::after {
    bottom: -8px;
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: block;
    }

    .sections-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
    }

    .section-card {
        margin-bottom: var(--spacing-md);
    }

    .sidebar.mobile-visible {
        transform: translateX(0);
    }

    .sidebar {
        transform: translateX(-100%);
    }
}
</style>

<script>
function toggleMobileMenu() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('mobile-visible');
}

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    const sidebar = document.querySelector('.sidebar');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    
    if (!sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
        sidebar.classList.remove('mobile-visible');
    }
});

// Handle section card interactions
document.querySelectorAll('.section-card').forEach(card => {
    card.addEventListener('click', function(e) {
        if (e.target.tagName.toLowerCase() !== 'button' && 
            e.target.tagName.toLowerCase() !== 'input' && 
            e.target.tagName.toLowerCase() !== 'select' && 
            e.target.tagName.toLowerCase() !== 'textarea') {
            const sectionName = this.dataset.section;
            // You can add specific section handling here if needed
        }
    });
});
</script>
