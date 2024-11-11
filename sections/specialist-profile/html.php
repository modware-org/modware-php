<?php
require_once __DIR__ . '/query.php';
$specialistData = getSpecialistData();
?>

<section class="specialist-profile" aria-labelledby="specialist-heading">
    <div class="specialist-container">
        <?php if ($specialistData): ?>
            <div class="specialist-header">
                <h2 id="specialist-heading"><?php echo htmlspecialchars($specialistData['name']); ?></h2>
                <?php if (isset($specialistData['title'])): ?>
                    <h3 class="specialist-title"><?php echo htmlspecialchars($specialistData['title']); ?></h3>
                <?php endif; ?>
            </div>

            <?php if (isset($specialistData['photo'])): ?>
                <div class="specialist-photo">
                    <img src="<?php echo htmlspecialchars($specialistData['photo']); ?>" 
                         alt="<?php echo htmlspecialchars($specialistData['name']); ?>"
                         loading="lazy">
                </div>
            <?php endif; ?>

            <?php if (isset($specialistData['bio'])): ?>
                <div class="specialist-bio">
                    <?php echo htmlspecialchars($specialistData['bio']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($specialistData['specializations']) && !empty($specialistData['specializations'])): ?>
                <div class="specialist-specializations">
                    <h3>[translate key="specialist_specializations_title" lang="en"]</h3>
                    <ul>
                        <?php foreach ($specialistData['specializations'] as $spec): ?>
                            <li><?php echo htmlspecialchars($spec); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="specialist-contact">
                <a href="/consultations" class="contact-button">
                    [translate key="specialists_contact_button" lang="en"]
                </a>
            </div>
        <?php else: ?>
            <p class="no-specialist">[translate key="specialist_not_found" lang="en"]</p>
        <?php endif; ?>
    </div>
</section>
