<?php
require_once __DIR__ . '/query.php';

$currentLang = $_GET['lang'] ?? 'pl';
$data = getEducationData($currentLang);
?>

<section class="education-section">
    <div class="container">
        <h2 class="section-title">
            <?php echo htmlspecialchars($data['section']['title']); ?>
        </h2>

        <?php if (!empty($data['section']['description'])): ?>
            <div class="section-description">
                <?php echo htmlspecialchars($data['section']['description']); ?>
            </div>
        <?php endif; ?>

        <div class="education-grid">
            <?php foreach ($data['items'] as $item): ?>
                <div class="education-item">
                    <h3 class="item-title">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </h3>

                    <?php if (!empty($item['description'])): ?>
                        <div class="item-description">
                            <?php echo htmlspecialchars($item['description']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item['duration'])): ?>
                        <div class="item-duration">
                            <svg viewBox="0 0 24 24" width="16" height="16">
                                <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.4 0-8-3.6-8-8s3.6-8 8-8 8 3.6 8 8-3.6 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"
                                      fill="currentColor"/>
                            </svg>
                            <?php echo htmlspecialchars($item['duration']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item['price'])): ?>
                        <div class="item-price">
                            <?php echo htmlspecialchars($item['price']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item['features'])): ?>
                        <div class="item-features">
                            <h4><?php echo $currentLang === 'pl' ? 'Program obejmuje' : 'Program includes'; ?></h4>
                            <ul>
                                <?php foreach ($item['features'] as $feature): ?>
                                    <li><?php echo htmlspecialchars($feature); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item['schedule'])): ?>
                        <div class="item-schedule">
                            <h4><?php echo $currentLang === 'pl' ? 'Harmonogram' : 'Schedule'; ?></h4>
                            <ul>
                                <?php foreach ($item['schedule'] as $session): ?>
                                    <li>
                                        <?php if (!empty($session['date'])): ?>
                                            <span class="schedule-date"><?php echo htmlspecialchars($session['date']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($session['time'])): ?>
                                            <span class="schedule-time"><?php echo htmlspecialchars($session['time']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($session['topic'])): ?>
                                            <span class="schedule-topic"><?php echo htmlspecialchars($session['topic']); ?></span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($item['registration_url'])): ?>
                        <a href="<?php echo htmlspecialchars($item['registration_url']); ?>" 
                           class="registration-button">
                            <?php echo $currentLang === 'pl' ? 'Zapisz się' : 'Register'; ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($data['note'])): ?>
            <div class="education-note">
                <?php echo htmlspecialchars($data['note']); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
