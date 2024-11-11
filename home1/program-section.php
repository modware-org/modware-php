<?php
require_once __DIR__ . '/../config/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch program components
$programComponents = [];
$result = $conn->query("SELECT title, description, icon_svg FROM program_components WHERE is_active = 1 ORDER BY sort_order ASC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $programComponents[] = $row;
}
?>

<section class="program-section" aria-labelledby="program-heading">
    <div class="program-container">
        <header class="program-header">
            <h2 id="program-heading">Что включает в себя комплексная ДБТ программа</h2>
        </header>

        <div class="program-components" role="list">
            <?php foreach($programComponents as $component): ?>
                <article class="component-card" role="listitem">
                    <div class="icon-wrapper" aria-hidden="true">
                        <?php echo $component['icon_svg']; ?>
                    </div>
                    <h3 class="component-title"><?php echo htmlspecialchars($component['title']); ?></h3>
                    <p class="component-description"><?php echo htmlspecialchars($component['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="text-center">
            <a href="#contact" 
               class="cta-button" 
               role="button"
               aria-label="Записаться на прием к специалисту">
                ЗАПИСАТЬСЯ НА ПРИЕМ СПЕЦИАЛИСТА
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.component-card');
    
    // Add keyboard interaction for cards
    cards.forEach(card => {
        card.setAttribute('tabindex', '0');
        
        card.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                this.click();
            }
        });

        // Add hover effect announcement for screen readers
        card.addEventListener('mouseenter', function() {
            this.setAttribute('aria-expanded', 'true');
        });
        
        card.addEventListener('mouseleave', function() {
            this.setAttribute('aria-expanded', 'false');
        });
    });
});
</script>
