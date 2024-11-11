<?php
// Fetch expertise translations from the section_translations table
$expertise_translations = [];
try {
    global $db;
    $stmt = $db->prepare("SELECT * FROM section_translations WHERE section_name = 'expertise' AND field_name = 'title'");
    $stmt->execute();
    $expertise_translations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching expertise translations: " . $e->getMessage());
}

// Safely access translations with null checks
$title = !empty($expertise_translations) && isset($expertise_translations[0]['translation']) 
    ? htmlspecialchars($expertise_translations[0]['translation'], ENT_QUOTES, 'UTF-8') 
    : 'Expertise';

// Fetch expertise items
$expertise_items = [];
try {
    $stmt = $db->prepare("SELECT * FROM expertise_items WHERE active = 1 ORDER BY sort_order");
    $stmt->execute();
    $expertise_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching expertise items: " . $e->getMessage());
}
?>

<section id="expertise">
    <div class="container">
        <h2><?php echo $title; ?></h2>
        <div class="expertise-grid">
            <?php foreach ($expertise_items as $item): ?>
                <div class="expertise-item">
                    <?php if (!empty($item['icon'])): ?>
                        <img src="<?php echo htmlspecialchars($item['icon'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <?php if (!empty($item['description'])): ?>
                        <p><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
