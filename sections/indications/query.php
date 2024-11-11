<?php
require_once __DIR__ . '/../../config/Database.php';

function getIndicationsData() {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $indications = [];
    
    // Get DBT indications
    $result = $conn->query(
        "SELECT title, description 
         FROM dbt_indications 
         WHERE is_active = 1 
         ORDER BY sort_order ASC"
    );
    
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $indications[] = $row;
    }
    
    // Get section configuration
    $config = [];
    $configResult = $conn->query(
        "SELECT name, value 
         FROM config 
         WHERE name LIKE 'indications_%'"
    );
    
    while ($row = $configResult->fetchArray(SQLITE3_ASSOC)) {
        $config[$row['name']] = $row['value'];
    }
    
    return [
        'indications' => $indications,
        'config' => $config
    ];
}
