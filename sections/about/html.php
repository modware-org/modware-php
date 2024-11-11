<?php
require_once __DIR__ . '/query.php';
$aboutData = getAboutData();
?>

<section class="about-section" aria-labelledby="about-heading">
    <div class="about-grid">
        <div class="about-content">
            <h2 id="about-heading">O НАС</h2>
            <p>В 2024 году наша команда прошла обучение диалектической поведенческой терапии от <?php echo htmlspecialchars($aboutData['certification'] && isset($aboutData['certification']['institution']) ? $aboutData['certification']['institution'] : ''); ?> под
                руководством преподавателей: <?php 
                $instructorNames = array_map(function($instructor) {
                    return $instructor['name'] . (isset($instructor['title']) && $instructor['title'] ? ', ' . $instructor['title'] : '');
                }, $aboutData['instructors']);
                echo htmlspecialchars(implode(' и ', $instructorNames)); 
                ?>.</p>

            <div class="certification-details" aria-labelledby="team-heading">
                <h3 id="team-heading">Наша команда сертифицированных специалистов:</h3>
                <div class="team-list" role="list">
                    <?php foreach ($aboutData['team_members'] as $member): ?>
                        <div class="team-member" role="listitem"><?php echo htmlspecialchars($member); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php if ($aboutData['certification']): ?>
        <div class="certification-card" aria-labelledby="cert-heading">
            <a href="Cert_Unity_241108_073542.pdf" 
               aria-label="Посмотреть сертификат <?php echo htmlspecialchars(isset($aboutData['certification']['institution']) ? $aboutData['certification']['institution'] : ''); ?>">
                <img src="/img/unitydbt-cert.png"
                     alt="Сертификат <?php echo htmlspecialchars(isset($aboutData['certification']['program']) ? $aboutData['certification']['program'] : ''); ?> от <?php echo htmlspecialchars(isset($aboutData['certification']['institution']) ? $aboutData['certification']['institution'] : ''); ?>" 
                     class="certification-logo"
                     height="350">
            </a>

            <div class="certification-details">
                <h3 id="cert-heading"><?php echo htmlspecialchars(isset($aboutData['certification']['program']) ? $aboutData['certification']['program'] : ''); ?></h3>
                <p>
                    <span class="highlight-text">Часть 1:</span> 
                    <?php echo htmlspecialchars(isset($aboutData['certification']['part1_dates']) ? $aboutData['certification']['part1_dates'] : ''); ?>
                </p>
                <p>
                    <span class="highlight-text">Часть 2:</span> 
                    <?php echo htmlspecialchars(isset($aboutData['certification']['part2_dates']) ? $aboutData['certification']['part2_dates'] : ''); ?>
                </p>

                <h3 id="instructors-heading">Преподаватели:</h3>
                <div role="list" aria-labelledby="instructors-heading">
                    <?php foreach ($aboutData['instructors'] as $instructor): ?>
                        <p role="listitem">
                            <?php echo htmlspecialchars($instructor['name'] . (isset($instructor['title']) && $instructor['title'] ? ', ' . $instructor['title'] : '')); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
