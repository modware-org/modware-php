<?php
require_once __DIR__ . '/query.php';

$currentLang = $_GET['lang'] ?? 'pl';
$data = getCertificationData($currentLang);
?>

<section class="certification-section">
    <div class="container">
        <h2 class="section-title">
            <?php echo htmlspecialchars($data['section']['title']); ?>
        </h2>

        <?php if (!empty($data['section']['description'])): ?>
            <div class="section-description">
                <?php echo htmlspecialchars($data['section']['description']); ?>
            </div>
        <?php endif; ?>

        <div class="certification-grid">
            <?php foreach ($data['items'] as $item): ?>
                <div class="certification-item">
                    <?php if (!empty($item['icon_url'])): ?>
                        <div class="certification-icon">
                            <img src="<?php echo htmlspecialchars($item['icon_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>"
                                 loading="lazy">
                        </div>
                    <?php endif; ?>

                    <div class="certification-content">
                        <h3 class="certification-title">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </h3>

                        <?php if (!empty($item['issuer'])): ?>
                            <div class="certification-issuer">
                                <?php echo htmlspecialchars($item['issuer']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($item['description'])): ?>
                            <div class="certification-description">
                                <?php echo htmlspecialchars($item['description']); ?>
                            </div>
                        <?php endif; ?>

                        <div class="certification-dates">
                            <?php if (!empty($item['date_received'])): ?>
                                <div class="date-received">
                                    <span class="label"><?php echo $currentLang === 'pl' ? 'Data otrzymania' : 'Date received'; ?>:</span>
                                    <span class="value"><?php echo htmlspecialchars($item['date_received']); ?></span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($item['expiry_date'])): ?>
                                <div class="expiry-date">
                                    <span class="label"><?php echo $currentLang === 'pl' ? 'Data ważności' : 'Valid until'; ?>:</span>
                                    <span class="value"><?php echo htmlspecialchars($item['expiry_date']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($item['certificate_url'])): ?>
                            <a href="<?php echo htmlspecialchars($item['certificate_url']); ?>" 
                               class="view-certificate" 
                               target="_blank">
                                <?php echo $currentLang === 'pl' ? 'Zobacz certyfikat' : 'View certificate'; ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
