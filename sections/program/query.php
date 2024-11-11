<?php
require_once __DIR__ . '/../../config/Database.php';

function getProgramData() {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $data = [
        'components' => [],
        'config' => []
    ];
    
    // Get program components
    $result = $conn->query(
        "SELECT title, description, icon_svg 
         FROM program_components 
         WHERE is_active = 1 
         ORDER BY sort_order ASC"
    );
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $data['components'][] = $row;
    }
    
    // Get section configuration
    $configResult = $conn->query(
        "SELECT name, value 
         FROM config 
         WHERE name LIKE 'program_%'"
    );
    
    while ($row = $configResult->fetchArray(SQLITE3_ASSOC)) {
        $data['config'][$row['name']] = $row['value'];
    }
    
    return $data;
}
