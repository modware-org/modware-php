<?php
$currentLang = $_GET['lang'] ?? 'en';
$specialties = $sectionData['specialties'] ?? [];
?>

<div class="specialists-intro-section">
    <div class="container">
        <h2 class="section-title">
            [translate key="specialists_intro_title" lang="<?php echo $currentLang; ?>"]
        </h2>

        <div class="section-description">
            [translate key="specialists_intro_description" lang="<?php echo $currentLang; ?>"]
        </div>

        <div class="specialists-overview">
            <div class="intro-text">
                [translate key="specialists_intro_text" lang="<?php echo $currentLang; ?>"]
            </div>

            <?php if (!empty($specialties)): ?>
                <div class="specialties-grid">
                    <?php foreach ($specialties as $specialty): ?>
                        <div class="specialty-item">
                            <?php if (!empty($specialty['icon'])): ?>
                                <div class="specialty-icon">
                                    <img src="<?php echo htmlspecialchars($specialty['icon']); ?>" 
                                         alt="<?php echo htmlspecialchars($specialty['title']); ?>">
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="specialty-title">
                                [translate key="specialty_<?php echo $specialty['key']; ?>_title" 
                                          lang="<?php echo $currentLang; ?>"]
                            </h3>
                            
                            <div class="specialty-description">
                                [translate key="specialty_<?php echo $specialty['key']; ?>_description" 
                                          lang="<?php echo $currentLang; ?>"]
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="cta-wrapper">
                <a href="#contact" class="cta-button">
                    [translate key="specialists_contact_button" lang="<?php echo $currentLang; ?>"]
                </a>
            </div>
        </div>
    </div>
</div>
