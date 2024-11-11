<?php
require_once __DIR__ . '/../../config/Database.php';

function getHeroData() {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $data = [
        'title' => '',
        'subtitle' => '',
        'cta_text' => ''
    ];
    
    // Get hero section data from config table
    $queries = [
        'title' => "SELECT value FROM config WHERE name = 'hero_title' LIMIT 1",
        'subtitle' => "SELECT value FROM config WHERE name = 'hero_subtitle' LIMIT 1",
        'cta_text' => "SELECT value FROM config WHERE name = 'hero_cta_text' LIMIT 1"
    ];
    
    foreach ($queries as $key => $query) {
        $result = $conn->query($query);
        if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[$key] = $row['value'];
        }
    }
    
    return $data;
}
