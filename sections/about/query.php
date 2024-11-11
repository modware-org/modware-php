<?php
require_once __DIR__ . '/../../config/Database.php';

function getAboutData() {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $data = [
        'team_members' => [],
        'certification' => null,
        'instructors' => []
    ];
    
    // Get team members
    $result = $conn->query(
        "SELECT name FROM team_members 
         WHERE is_active = 1 
         ORDER BY sort_order ASC"
    );
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $data['team_members'][] = $row['name'];
    }
    
    // Get certification details
    $result = $conn->query(
        "SELECT * FROM certification_details 
         ORDER BY created_at DESC 
         LIMIT 1"
    );
    $data['certification'] = $result->fetchArray(SQLITE3_ASSOC);
    
    // Get certification instructors
    if ($data['certification']) {
        $stmt = $conn->prepare(
            "SELECT name, title 
             FROM certification_instructors 
             WHERE certification_id = ? 
             ORDER BY sort_order ASC"
        );
        $stmt->bindValue(1, $data['certification']['id'], SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data['instructors'][] = $row;
        }
    }
    
    return $data;
}
