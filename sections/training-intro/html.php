<?php
$pageData = $this->db->query(
    "SELECT * FROM sections 
    WHERE page_id = 8 
    AND name = 'training-intro'
    LIMIT 1"
);

if (!empty($pageData)) {
    $data = $pageData[0];
    ?>
    <section class="training-intro">
        <div class="container">
            <h1><?php echo htmlspecialchars($data['title'] ?? 'Training'); ?></h1>
            <div class="content">
                <?php echo $data['description'] ?? ''; ?>
            </div>
        </div>
    </section>
    <?php
}
?>
