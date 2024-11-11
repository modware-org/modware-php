<?php
$pageData = $this->db->query(
    "SELECT * FROM sections 
    WHERE page_id = 8 
    AND name = 'modules'
    LIMIT 1"
);

if (!empty($pageData)) {
    $data = $pageData[0];
    $modules = json_decode($data['data'] ?? '[]', true);
    ?>
    <section class="modules">
        <div class="container">
            <?php if (!empty($data['title'])): ?>
                <h2><?php echo htmlspecialchars($data['title']); ?></h2>
            <?php endif; ?>
            
            <?php if (!empty($data['description'])): ?>
                <div class="section-description">
                    <?php echo $data['description']; ?>
                </div>
            <?php endif; ?>

            <div class="modules-grid">
                <?php foreach ($modules as $module): ?>
                    <div class="module-card">
                        <?php if (!empty($module['icon'])): ?>
                            <div class="module-icon">
                                <img src="<?php echo htmlspecialchars($module['icon']); ?>" alt="<?php echo htmlspecialchars($module['title'] ?? ''); ?>">
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($module['title'])): ?>
                            <h3><?php echo htmlspecialchars($module['title']); ?></h3>
                        <?php endif; ?>
                        
                        <?php if (!empty($module['description'])): ?>
                            <p><?php echo htmlspecialchars($module['description']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}
?>
