<?php
require_once __DIR__ . '/../config/Database.php';

$db = Database::getInstance();
$conn = $db->getConnection();

// Fetch team members
$teamMembers = [];
$result = $conn->query("SELECT name FROM team_members WHERE is_active = 1 ORDER BY sort_order ASC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $teamMembers[] = $row['name'];
}

// Fetch certification details
$result = $conn->query("SELECT * FROM certification_details ORDER BY created_at DESC LIMIT 1");
$certificationDetails = $result->fetchArray(SQLITE3_ASSOC);

// Fetch certification instructors
$instructors = [];
$stmt = $conn->prepare("SELECT name, title FROM certification_instructors WHERE certification_id = ? ORDER BY sort_order ASC");
$stmt->bindValue(1, $certificationDetails['id'], SQLITE3_INTEGER);
$result = $stmt->execute();
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $instructors[] = $row;
}
?>

<section class="about-section" aria-labelledby="about-heading">
    <div class="about-grid">
        <div class="about-content">
            <h2 id="about-heading">O НАС</h2>
            <p>В 2024 году наша команда прошла обучение диалектической поведенческой терапии от <?php echo htmlspecialchars($certificationDetails['institution']); ?> под
                руководством преподавателей: <?php 
                $instructorNames = array_map(function($instructor) {
                    return $instructor['name'] . ($instructor['title'] ? ', ' . $instructor['title'] : '');
                }, $instructors);
                echo htmlspecialchars(implode(' и ', $instructorNames)); 
                ?>.</p>

            <div class="certification-details" aria-labelledby="team-heading">
                <h3 id="team-heading">Наша команда сертифицированных специалистов:</h3>
                <div class="team-list" role="list">
                    <?php foreach ($teamMembers as $member): ?>
                        <div class="team-member" role="listitem"><?php echo htmlspecialchars($member); ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="certification-card" aria-labelledby="cert-heading">
            <a href="Cert_Unity_241108_073542.pdf" aria-label="Посмотреть сертификат <?php echo htmlspecialchars($certificationDetails['institution']); ?>">
                <img src="img/unitydbt-cert.png" 
                     alt="Сертификат <?php echo htmlspecialchars($certificationDetails['program']); ?> от <?php echo htmlspecialchars($certificationDetails['institution']); ?>" 
                     class="certification-logo"
                     height="350">
            </a>

            <div class="certification-details">
                <h3 id="cert-heading"><?php echo htmlspecialchars($certificationDetails['program']); ?></h3>
                <p>
                    <span class="highlight-text">Часть 1:</span> <?php echo htmlspecialchars($certificationDetails['part1_dates']); ?>
                </p>
                <p>
                    <span class="highlight-text">Часть 2:</span> <?php echo htmlspecialchars($certificationDetails['part2_dates']); ?>
                </p>

                <h3 id="instructors-heading">Преподаватели:</h3>
                <div role="list" aria-labelledby="instructors-heading">
                    <?php foreach ($instructors as $instructor): ?>
                        <p role="listitem"><?php echo htmlspecialchars($instructor['name'] . ($instructor['title'] ? ', ' . $instructor['title'] : '')); ?></p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>
