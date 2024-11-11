<?php
// Auth is already handled by index.php
$db = AdminDatabase::getInstance();
$conn = $db->getConnection();

function fetchStructure($conn) {
    $structure = [];
    
    // Fetch all sites
    $siteQuery = "SELECT id, name, domain FROM sites ORDER BY domain";
    $siteStmt = $conn->prepare($siteQuery);
    $result = $siteStmt->execute();
    
    while ($site = $result->fetchArray(SQLITE3_ASSOC)) {
        $structure[$site['id']] = [
            'info' => $site,
            'pages' => []
        ];
        
        // Fetch pages for each site
        $pageQuery = "SELECT id, title, slug, status FROM pages WHERE site_id = :site_id ORDER BY title";
        $pageStmt = $conn->prepare($pageQuery);
        $pageStmt->bindValue(':site_id', $site['id'], SQLITE3_INTEGER);
        $pageResult = $pageStmt->execute();
        
        while ($page = $pageResult->fetchArray(SQLITE3_ASSOC)) {
            $structure[$site['id']]['pages'][$page['id']] = [
                'info' => $page,
                'sections' => []
            ];
            
            // Fetch sections for each page
            $sectionQuery = "SELECT id, name, title, type, position FROM sections WHERE page_id = :page_id ORDER BY position";
            $sectionStmt = $conn->prepare($sectionQuery);
            $sectionStmt->bindValue(':page_id', $page['id'], SQLITE3_INTEGER);
            $sectionResult = $sectionStmt->execute();
            
            while ($section = $sectionResult->fetchArray(SQLITE3_ASSOC)) {
                $structure[$site['id']]['pages'][$page['id']]['sections'][] = $section;
            }
        }
    }
    
    return $structure;
}

$structure = fetchStructure($conn);
?>

<div class="content-header">
    <h2>Project Structure Visualizer</h2>
</div>

<div class="content-body">
    <div class="stats-panel">
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

    <div class="structure-container">
        <?php foreach ($structure as $site): ?>
            <div class="site-card">
                <div class="site-header">
                    <h3><?= htmlspecialchars($site['info']['name']) ?></h3>
                    <div class="domain"><?= htmlspecialchars($site['info']['domain']) ?></div>
                </div>
                
                <?php foreach ($site['pages'] as $page): ?>
                    <div class="page-card">
                        <div class="page-header">
                            <h4>
                                <?= htmlspecialchars($page['info']['title']) ?>
                                <span class="status-badge status-<?= $page['info']['status'] ?>">
                                    <?= ucfirst($page['info']['status']) ?>
                                </span>
                            </h4>
                            <div class="slug">Slug: <?= htmlspecialchars($page['info']['slug']) ?></div>
                        </div>
                        
                        <?php foreach ($page['sections'] as $section): ?>
                            <div class="section-card">
                                <strong><?= htmlspecialchars($section['name']) ?></strong>
                                <span class="type-badge"><?= htmlspecialchars($section['type']) ?></span>
                                <?php if ($section['title']): ?>
                                    <div class="section-title">Title: <?= htmlspecialchars($section['title']) ?></div>
                                <?php endif; ?>
                                <div class="position">Position: <?= htmlspecialchars($section['position']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.content-body {
    padding: 20px;
}

.stats-panel {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.structure-container {
    display: grid;
    gap: 20px;
}

.site-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.site-header {
    background: #2c3e50;
    color: white;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
}

.site-header h3 {
    margin: 0 0 5px 0;
}

.domain {
    font-size: 0.9em;
    opacity: 0.9;
}

.page-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin: 10px 0;
    padding: 15px;
}

.page-header {
    background: #34495e;
    color: white;
    padding: 12px;
    border-radius: 4px;
    margin-bottom: 10px;
}

.page-header h4 {
    margin: 0 0 5px 0;
}

.status-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    margin-left: 8px;
}

.status-published {
    background: #27ae60;
}

.status-draft {
    background: #e74c3c;
}

.section-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    margin: 8px 0;
    padding: 12px;
}

.type-badge {
    display: inline-block;
    padding: 2px 6px;
    background: #3498db;
    color: white;
    border-radius: 4px;
    font-size: 0.8em;
    margin-left: 8px;
}

.section-title {
    margin: 5px 0;
    color: #666;
}

.position {
    font-size: 0.9em;
    color: #666;
}

.slug {
    font-size: 0.9em;
    opacity: 0.9;
}
</style>
