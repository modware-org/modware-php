<?php
require_once __DIR__ . '/query.php';
$data = getIndicationsData();
?>

<section class="indications-section" aria-labelledby="indications-heading">
    <div class="indications-container">
        <header class="section-header">
            <h2 id="indications-heading">
                <?php echo htmlspecialchars($data['config']['indications_title'] ?? 'Кому показана комплексная ДБТ программа'); ?>
            </h2>
            <?php if (isset($data['config']['indications_subtitle'])): ?>
                <p class="section-subtitle"><?php echo htmlspecialchars($data['config']['indications_subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <div class="indications-grid" role="list">
            <?php foreach($data['indications'] as $indication): ?>
                <article class="indication-card" role="listitem">
                    <h3 class="indication-title"><?php echo htmlspecialchars($indication['title']); ?></h3>
                    <p class="indication-description"><?php echo htmlspecialchars($indication['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (isset($data['config']['indications_show_cta']) && $data['config']['indications_show_cta'] === 'true'): ?>
    <div class="indications-cta">
        <a href="<?php echo htmlspecialchars($data['config']['indications_cta_link'] ?? '#contact'); ?>" 
           class="cta-button"
           role="button"
           aria-label="<?php echo htmlspecialchars($data['config']['indications_cta_text'] ?? 'Записаться на консультацию'); ?>">
            <?php echo htmlspecialchars($data['config']['indications_cta_text'] ?? 'Записаться на консультацию'); ?>
        </a>
    </div>
<?php endif; ?>
