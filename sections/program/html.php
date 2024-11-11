<?php
require_once __DIR__ . '/query.php';
$data = getProgramData();
?>

<section class="program-section" aria-labelledby="program-heading">
    <div class="program-container">
        <header class="program-header">
            <h2 id="program-heading">
                <?php echo htmlspecialchars($data['config']['program_title'] ?? 'Что включает в себя комплексная ДБТ программа'); ?>
            </h2>
            <?php if (isset($data['config']['program_subtitle'])): ?>
                <p class="section-subtitle"><?php echo htmlspecialchars($data['config']['program_subtitle']); ?></p>
            <?php endif; ?>
        </header>

        <div class="program-components" role="list">
            <?php foreach($data['components'] as $component): ?>
                <article class="component-card" role="listitem">
                    <div class="icon-wrapper" aria-hidden="true">
                        <?php echo $component['icon_svg']; ?>
                    </div>
                    <h3 class="component-title"><?php echo htmlspecialchars($component['title']); ?></h3>
                    <p class="component-description"><?php echo htmlspecialchars($component['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if (isset($data['config']['program_show_cta']) && $data['config']['program_show_cta'] === 'true'): ?>
            <div class="text-center">
                <a href="<?php echo htmlspecialchars($data['config']['program_cta_link'] ?? '#contact'); ?>" 
                   class="cta-button" 
                   role="button"
                   aria-label="<?php echo htmlspecialchars($data['config']['program_cta_text'] ?? 'ЗАПИСАТЬСЯ НА ПРИЕМ СПЕЦИАЛИСТА'); ?>">
                    <?php echo htmlspecialchars($data['config']['program_cta_text'] ?? 'ЗАПИСАТЬСЯ НА ПРИЕМ СПЕЦИАЛИСТА'); ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>
