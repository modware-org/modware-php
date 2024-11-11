<?php
require_once __DIR__ . '/../config/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch DBT indications
$dbtIndications = [];
$result = $conn->query("SELECT title, description FROM dbt_indications WHERE is_active = 1 ORDER BY sort_order ASC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $dbtIndications[] = $row;
}
?>

<section class="indications-section" aria-labelledby="indications-heading">
    <div class="indications-container">
        <header class="section-header">
            <h2 id="indications-heading">Кому показана комплексная ДБТ программа</h2>
        </header>

        <div class="indications-grid" role="list">
            <?php foreach($dbtIndications as $indication): ?>
                <article class="indication-card" role="listitem">
                    <h3 class="indication-title"><?php echo htmlspecialchars($indication['title']); ?></h3>
                    <p class="indication-description"><?php echo htmlspecialchars($indication['description']); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.indication-card');
        
        // Add animation when cards come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    // Add ARIA live region announcement for screen readers
                    entry.target.setAttribute('aria-live', 'polite');
                }
            });
        }, {
            threshold: 0.1
        });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
            
            // Add keyboard interaction
            card.setAttribute('tabindex', '0');
            card.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    this.click();
                }
            });
        });
    });
</script>
