<?php
require_once __DIR__ . '/query.php';
$heroData = getHeroData();
?>

<section class="hero-section" aria-label="Главный баннер">
    <div class="hero-content">
        <h1><?php echo $heroData['title']; ?></h1>
        <p><?php echo $heroData['subtitle']; ?></p>
        <button class="cta-button" aria-label="<?php echo $heroData['cta_text']; ?>">
            <?php echo $heroData['cta_text']; ?>
        </button>
    </div>
    <div class="hero-image">
        <img src="/img/unitydbt-logo.png" alt="DBT Unity логотип - Диалектическая поведенческая терапия" height="300">
    </div>
</section>
