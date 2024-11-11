<?php
require_once __DIR__ . '/../../config/env.php';
require_once __DIR__ . '/../../config/Logger.php';
require_once __DIR__ . '/../../config/Database.php';
putenv("APP_NAME=visualizer");

$logger = Logger::getInstance();
$logger->log("Request started: " . $_SERVER['REQUEST_URI']);

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();

    // Handle AJAX requests for section reordering
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_section_position') {
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => 'Invalid request'];

        try {
            $sectionId = $_POST['section_id'] ?? null;
            $newPosition = $_POST['new_position'] ?? null;
            $pageId = $_POST['page_id'] ?? null;

            if ($sectionId !== null && $newPosition !== null && $pageId !== null) {
                $sql = "UPDATE sections SET position = :new_position WHERE id = :section_id AND page_id = :page_id";
                $result = $db->execute($sql, [
                    ':new_position' => $newPosition,
                    ':section_id' => $sectionId,
                    ':page_id' => $pageId
                ]);

                if ($result) {
                    $response = [
                        'success' => true, 
                        'message' => 'Section position updated successfully'
                    ];
                    $logger->log("Updated section $sectionId position to $newPosition");
                }
            }
        } catch (Exception $e) {
            $response = [
                'success' => false, 
                'message' => 'Error updating section position: ' . $e->getMessage()
            ];
        }

        echo json_encode($response);
        exit();
    }

    // Handle form submissions for adding/removing
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add_site':
                    $name = $_POST['name'] ?? '';
                    $domain = $_POST['domain'] ?? '';
                    if ($name && $domain) {
                        $sql = "INSERT INTO sites (name, domain) VALUES (:name, :domain)";
                        $db->execute($sql, [':name' => $name, ':domain' => $domain]);
                        $logger->log("Added new site: $name");
                    }
                    break;

                case 'remove_site':
                    $siteId = $_POST['site_id'] ?? null;
                    if ($siteId) {
                        $db->execute("DELETE FROM sections WHERE page_id IN (SELECT id FROM pages WHERE site_id = :site_id)", [':site_id' => $siteId]);
                        $db->execute("DELETE FROM pages WHERE site_id = :site_id", [':site_id' => $siteId]);
                        $db->execute("DELETE FROM sites WHERE id = :site_id", [':site_id' => $siteId]);
                        $logger->log("Removed site with ID: $siteId");
                    }
                    break;

                case 'add_page':
                    $siteId = $_POST['site_id'] ?? null;
                    $title = $_POST['title'] ?? '';
                    $slug = $_POST['slug'] ?? '';
                    $status = $_POST['status'] ?? 'draft';
                    if ($siteId && $title && $slug) {
                        $sql = "INSERT INTO pages (site_id, title, slug, status) VALUES (:site_id, :title, :slug, :status)";
                        $db->execute($sql, [
                            ':site_id' => $siteId, 
                            ':title' => $title, 
                            ':slug' => $slug, 
                            ':status' => $status
                        ]);
                        $logger->log("Added new page: $title");
                    }
                    break;

                case 'remove_page':
                    $pageId = $_POST['page_id'] ?? null;
                    if ($pageId) {
                        $db->execute("DELETE FROM sections WHERE page_id = :page_id", [':page_id' => $pageId]);
                        $db->execute("DELETE FROM pages WHERE id = :page_id", [':page_id' => $pageId]);
                        $logger->log("Removed page with ID: $pageId");
                    }
                    break;

                case 'add_section':
                    $pageId = $_POST['page_id'] ?? null;
                    $name = $_POST['name'] ?? '';
                    $title = $_POST['title'] ?? '';
                    $type = $_POST['type'] ?? '';
                    $position = $_POST['position'] ?? 0;
                    if ($pageId && $name && $type) {
                        $sql = "INSERT INTO sections (page_id, name, title, type, position) VALUES (:page_id, :name, :title, :type, :position)";
                        $db->execute($sql, [
                            ':page_id' => $pageId,
                            ':name' => $name,
                            ':title' => $title,
                            ':type' => $type,
                            ':position' => $position
                        ]);
                        $logger->log("Added new section: $name");
                    }
                    break;

                case 'remove_section':
                    $sectionId = $_POST['section_id'] ?? null;
                    if ($sectionId) {
                        $db->execute("DELETE FROM sections WHERE id = :section_id", [':section_id' => $sectionId]);
                        $logger->log("Removed section with ID: $sectionId");
                    }
                    break;
            }
            // Redirect to prevent form resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    function fetchStructure($db) {
        $structure = [];
        
        $siteQuery = "SELECT id, name, domain FROM sites ORDER BY domain";
        $sites = $db->query($siteQuery);
        
        foreach ($sites as $site) {
            $structure[$site['id']] = [
                'info' => $site,
                'pages' => []
            ];
            
            $pageQuery = "SELECT id, title, slug, status FROM pages WHERE site_id = :site_id ORDER BY title";
            $pages = $db->query($pageQuery, [':site_id' => $site['id']]);
            
            foreach ($pages as $page) {
                $structure[$site['id']]['pages'][$page['id']] = [
                    'info' => $page,
                    'sections' => []
                ];
                
                $sectionQuery = "SELECT id, name, title, type, position FROM sections WHERE page_id = :page_id ORDER BY position";
                $sections = $db->query($sectionQuery, [':page_id' => $page['id']]);
                
                $structure[$site['id']]['pages'][$page['id']]['sections'] = $sections;
            }
        }
        
        return $structure;
    }

    $structure = fetchStructure($db);

    // Include header which handles the layout
    require_once __DIR__ . '/../../header.php';
?>

<div class="container">
    <h1>Project Structure Visualizer</h1>
    
    <!-- Add Site Form -->
    <div class="add-site-form">
        <h3>Add New Site</h3>
        <form method="POST">
            <input type="hidden" name="action" value="add_site">
            <input type="text" name="name" placeholder="Site Name" required>
            <input type="text" name="domain" placeholder="Domain" required>
            <button type="submit">Add Site</button>
        </form>
    </div>
    
    <div class="stats">
        <h3>Project Statistics</h3>
        <?php
        $totalSites = count($structure);
        $totalPages = 0;
        $totalSections = 0;
        foreach ($structure as $site) {
            $totalPages += count($site['pages']);
            foreach ($site['pages'] as $page) {
                $totalSections += count($page['sections']);
            }
        }
        echo "Sites: $totalSites | Pages: $totalPages | Sections: $totalSections";
        ?>
    </div>

    <?php if (empty($structure)): ?>
        <div class="no-data">
            <p>No project structure data available. Create your first site to populate the visualizer.</p>
        </div>
    <?php else: ?>
        <?php foreach ($structure as $site): ?>
            <div class="site">
                <div class="site-header">
                    <h2><?= htmlspecialchars($site['info']['name']) ?></h2>
                    <div><?= htmlspecialchars($site['info']['domain']) ?></div>
                    
                    <!-- Remove Site Form -->
                    <form method="POST" class="remove-form">
                        <input type="hidden" name="action" value="remove_site">
                        <input type="hidden" name="site_id" value="<?= $site['info']['id'] ?>">
                        <button type="submit" onclick="return confirm('Are you sure you want to remove this site and all its pages?')">Remove Site</button>
                    </form>
                    
                    <!-- Add Page Form -->
                    <div class="add-page-form">
                        <h4>Add New Page</h4>
                        <form method="POST">
                            <input type="hidden" name="action" value="add_page">
                            <input type="hidden" name="site_id" value="<?= $site['info']['id'] ?>">
                            <input type="text" name="title" placeholder="Page Title" required>
                            <input type="text" name="slug" placeholder="Page Slug" required>
                            <select name="status">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            <button type="submit">Add Page</button>
                        </form>
                    </div>
                </div>
                
                <?php if (empty($site['pages'])): ?>
                    <div class="no-pages">
                        No pages created for this site
                    </div>
                <?php else: ?>
                    <?php foreach ($site['pages'] as $page): ?>
                        <div class="page">
                            <div class="page-header">
                                <h3>
                                    <?= htmlspecialchars($page['info']['title']) ?>
                                    <span class="status-badge status-<?= $page['info']['status'] ?>">
                                        <?= ucfirst($page['info']['status']) ?>
                                    </span>
                                </h3>
                                <div>Slug: <?= htmlspecialchars($page['info']['slug']) ?></div>
                                
                                <!-- Remove Page Form -->
                                <form method="POST" class="remove-form">
                                    <input type="hidden" name="action" value="remove_page">
                                    <input type="hidden" name="page_id" value="<?= $page['info']['id'] ?>">
                                    <button type="submit" onclick="return confirm('Are you sure you want to remove this page and all its sections?')">Remove Page</button>
                                </form>
                                
                                <!-- Add Section Form -->
                                <div class="add-section-form">
                                    <h4>Add New Section</h4>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="add_section">
                                        <input type="hidden" name="page_id" value="<?= $page['info']['id'] ?>">
                                        <input type="text" name="name" placeholder="Section Name" required>
                                        <input type="text" name="title" placeholder="Section Title">
                                        <select name="type" required>
                                            <option value="">Select Section Type</option>
                                            <option value="text">Text</option>
                                            <option value="image">Image</option>
                                            <option value="gallery">Gallery</option>
                                            <option value="form">Form</option>
                                        </select>
                                        <input type="number" name="position" placeholder="Position" value="0">
                                        <button type="submit">Add Section</button>
                                    </form>
                                </div>
                            </div>
                            
                            <?php if (empty($page['sections'])): ?>
                                <div class="no-sections">
                                    No sections created for this page
                                </div>
                            <?php else: ?>
                                <div class="page-sections" data-page-id="<?= $page['info']['id'] ?>">
                                    <div class="sections-container sortable">
                                        <?php foreach ($page['sections'] as $section): ?>
                                            <div class="section draggable" 
                                                 data-section-id="<?= $section['id'] ?>" 
                                                 data-position="<?= $section['position'] ?>">
                                                <strong><?= htmlspecialchars($section['name']) ?></strong>
                                                <span class="section-type"><?= htmlspecialchars($section['type']) ?></span>
                                                <div class="section-position">Position: <span class="position-value"><?= $section['position'] ?></span></div>
                                                
                                                <!-- Remove Section Form -->
                                                <form method="POST" class="remove-form">
                                                    <input type="hidden" name="action" value="remove_section">
                                                    <input type="hidden" name="section_id" value="<?= $section['id'] ?>">
                                                    <button type="submit" onclick="return confirm('Are you sure you want to remove this section?')">Remove Section</button>
                                                </form>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
    // Include footer
    require_once __DIR__ . '/../../footer.php';
?>

<link rel="stylesheet" href="style.css">
<script src="script.js"></script>

<?php
} catch (Exception $e) {
    // Log the error and show a user-friendly message
    error_log('Visualizer Error: ' . $e->getMessage());
    echo '<div class="error-message">Unable to load project structure. Please contact support.</div>';
}
