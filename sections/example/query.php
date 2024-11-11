<?php
require_once __DIR__ . '/../../config/Database.php';

// Handle form submissions for section content updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sectionId = $_POST['section_id'] ?? null;
    $videoId = $_POST['video_id'] ?? null;
    
    if ($sectionId && $videoId) {
        // Get old data for webhook
        $stmt = $db->prepare("SELECT data FROM sections WHERE id = ?");
        $stmt->execute([$sectionId]);
        $oldData = json_decode($stmt->fetchColumn(), true);
        
        // Update section data
        $data = [
            'video_id' => $videoId
        ];
        
        $stmt = $db->prepare("UPDATE sections SET data = ? WHERE id = ?");
        $stmt->execute([json_encode($data), $sectionId]);

        // Trigger webhook for content update
        \Integrations\Webhooks\WebhookProcessor::trigger('content.updated', [
            'content_id' => $sectionId,
            'content_type' => 'section',
            'section' => 'example',
            'user_id' => $_SESSION['user_id'] ?? null,
            'changes' => [
                'video_id' => [
                    'old' => $oldData['video_id'] ?? null,
                    'new' => $videoId
                ]
            ]
        ]);
    }

    // Handle translation updates
    $translations = $_POST['translations'] ?? [];
    foreach ($translations as $langCode => $fields) {
        foreach ($fields as $key => $value) {
            // Get language ID
            $stmt = $db->prepare("SELECT id FROM languages WHERE code = ?");
            $stmt->execute([$langCode]);
            $languageId = $stmt->fetchColumn();

            if ($languageId) {
                // Update or insert translation
                $stmt = $db->prepare("
                    INSERT INTO translations (
                        language_id, content_type, content_id, field_name, translation
                    ) VALUES (?, 'section', ?, ?, ?)
                    ON DUPLICATE KEY UPDATE translation = ?
                ");
                
                $stmt->execute([
                    $languageId,
                    $sectionId,
                    $key,
                    $value,
                    $value
                ]);
            }
        }
    }
}

// Load section data
$sectionId = $_GET['section_id'] ?? null;
$sectionData = [];

if ($sectionId) {
    $stmt = $db->prepare("SELECT data FROM sections WHERE id = ?");
    $stmt->execute([$sectionId]);
    $sectionData = json_decode($stmt->fetchColumn(), true) ?? [];
}

// Load translations for the current language
$currentLang = $_GET['lang'] ?? 'en';
$translations = [];

if ($sectionId) {
    $stmt = $db->prepare("
        SELECT t.field_name, t.translation 
        FROM translations t
        JOIN languages l ON t.language_id = l.id
        WHERE l.code = ? 
        AND t.content_type = 'section'
        AND t.content_id = ?
    ");
    
    $stmt->execute([$currentLang, $sectionId]);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $translations[$row['field_name']] = $row['translation'];
    }
}

// Return data for use in html.php
return [
    'sectionData' => $sectionData,
    'translations' => $translations
];
