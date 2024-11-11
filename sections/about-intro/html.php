<?php
$currentLang = $_GET['lang'] ?? 'en';
?>

<div class="about-intro-section">
    <div class="container">
        <h2 class="section-title">
            [translate key="about_intro_title" lang="<?php echo $currentLang; ?>"]
        </h2>

        <div class="section-description">
            [translate key="about_intro_description" lang="<?php echo $currentLang; ?>"]
        </div>

        <div class="intro-content">
            <div class="intro-text">
                [translate key="about_intro_content" lang="<?php echo $currentLang; ?>"]
            </div>
        </div>
    </div>
</div>
