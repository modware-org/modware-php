<?php
require_once __DIR__ . '/query.php';

$currentLang = $_GET['lang'] ?? 'pl';
$data = getConsultationsData($currentLang);
?>

<div class="consultations-section">
    <div class="container">
        <h2 class="section-title">
            <?php echo htmlspecialchars($data['section']['title']); ?>
        </h2>

        <?php if (!empty($data['section']['description'])): ?>
            <div class="section-description">
                <?php echo htmlspecialchars($data['section']['description']); ?>
            </div>
        <?php endif; ?>

        <div class="consultations-grid">
            <?php foreach ($data['consultations'] as $type): ?>
                <div class="consultation-card">
                    <div class="consultation-icon">
                        <img src="<?php echo htmlspecialchars($type['icon']); ?>" 
                             alt="<?php echo htmlspecialchars($type['title']); ?>">
                    </div>
                    
                    <h3 class="consultation-title">
                        <?php echo htmlspecialchars($type['title']); ?>
                    </h3>
                    
                    <div class="consultation-description">
                        <?php echo htmlspecialchars($type['description']); ?>
                    </div>

                    <?php if (!empty($type['duration'])): ?>
                        <div class="consultation-duration">
                            <svg viewBox="0 0 24 24" width="16" height="16">
                                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"
                                      fill="currentColor"/>
                            </svg>
                            <?php echo htmlspecialchars($type['duration']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($type['price'])): ?>
                        <div class="consultation-price">
                            <?php echo htmlspecialchars($type['price']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($type['features'])): ?>
                        <ul class="consultation-features">
                            <?php foreach ($type['features'] as $feature): ?>
                                <li><?php echo htmlspecialchars($feature); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <a href="<?php echo htmlspecialchars($type['booking_url']); ?>" 
                       class="consultation-cta">
                        <?php echo $currentLang === 'pl' ? 'Zarezerwuj' : 'Book Now'; ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($data['note'])): ?>
            <div class="consultations-note">
                <?php echo htmlspecialchars($data['note']); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
