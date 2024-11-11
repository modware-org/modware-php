<?php
require_once __DIR__ . '/../config/Database.php';

try {
    $db = Database::getInstance('../database.sqlite');
    
    // Begin transaction
    $db->beginTransaction();
    
    // Insert the team page
    $db->execute(
        "INSERT INTO pages (site_id, title, slug, meta_description, status) VALUES (:site_id, :title, :slug, :meta_description, :status)",
        [
            ':site_id' => 1,
            ':title' => 'Our Team',
            ':slug' => 'team',
            ':meta_description' => 'Meet our dedicated team of professionals',
            ':status' => 'published'
        ]
    );
    
    $pageId = $db->getLastInsertId();
    
    // Add the team section
    $db->execute(
        "INSERT INTO sections (page_id, name, title, type, position) VALUES (:page_id, :name, :title, :type, :position)",
        [
            ':page_id' => $pageId,
            ':name' => 'team',
            ':title' => 'Our Team',
            ':type' => 'team',
            ':position' => 1
        ]
    );
    
    // Commit transaction
    $db->commit();
    
    echo "Team page created successfully!\n";
} catch (Exception $e) {
    // Rollback transaction on error
    $db->rollback();
    echo "Error creating team page: " . $e->getMessage() . "\n";
}
