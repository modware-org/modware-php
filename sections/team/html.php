<?php
require_once __DIR__ . '/query.php';

$currentLang = $_GET['lang'] ?? 'pl';
$data = getTeamData($currentLang);
?>

<section class="team-section">
    <div class="container">
        <h2 class="section-title">
            <?php echo htmlspecialchars($data['section']['title']); ?>
        </h2>

        <?php if (!empty($data['section']['description'])): ?>
            <div class="section-description">
                <?php echo htmlspecialchars($data['section']['description']); ?>
            </div>
        <?php endif; ?>

        <div class="team-grid">
            <?php foreach ($data['members'] as $member): ?>
                <div class="team-member">
                    <?php if (!empty($member['photo'])): ?>
                        <div class="member-photo">
                            <img src="<?php echo htmlspecialchars($member['photo']); ?>" 
                                 alt="<?php echo htmlspecialchars($member['name']); ?>"
                                 loading="lazy">
                        </div>
                    <?php endif; ?>

                    <div class="member-info">
                        <h3 class="member-name">
                            <?php echo htmlspecialchars($member['name']); ?>
                        </h3>

                        <?php if (!empty($member['credentials'])): ?>
                            <div class="member-credentials">
                                <?php echo htmlspecialchars($member['credentials']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($member['position'])): ?>
                            <div class="member-position">
                                <?php echo htmlspecialchars($member['position']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($member['bio'])): ?>
                            <div class="member-bio">
                                <?php echo htmlspecialchars($member['bio']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($member['specialties'])): ?>
                            <div class="member-specialties">
                                <h4><?php echo $currentLang === 'pl' ? 'Specjalizacje' : 'Specialties'; ?></h4>
                                <ul>
                                    <?php foreach ($member['specialties'] as $specialty): ?>
                                        <li><?php echo htmlspecialchars($specialty); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($member['education'])): ?>
                            <div class="member-education">
                                <h4><?php echo $currentLang === 'pl' ? 'Edukacja' : 'Education'; ?></h4>
                                <ul>
                                    <?php foreach ($member['education'] as $edu): ?>
                                        <li>
                                            <?php echo htmlspecialchars($edu['degree']); ?>
                                            <?php if (!empty($edu['institution'])): ?>
                                                - <?php echo htmlspecialchars($edu['institution']); ?>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($member['publications'])): ?>
                            <div class="member-publications">
                                <h4><?php echo $currentLang === 'pl' ? 'Publikacje' : 'Publications'; ?></h4>
                                <ul>
                                    <?php foreach ($member['publications'] as $pub): ?>
                                        <li><?php echo htmlspecialchars($pub); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($data['note'])): ?>
            <div class="team-note">
                <?php echo htmlspecialchars($data['note']); ?>
            </div>
        <?php endif; ?>
    </div>
</section>
