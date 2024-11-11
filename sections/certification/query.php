<?php

function getCertificationData($language = 'pl') {
    try {
        $db = Database::getInstance();
        
        // Get section translation
        $sectionQuery = "SELECT title, subtitle, description 
                        FROM section_translations 
                        WHERE section_name = 'certification' 
                        AND language_code = :lang";
        
        $sectionData = $db->query($sectionQuery, [':lang' => $language])[0] ?? null;
        
        // Get certification items
        $query = "SELECT 
            id,
            title,
            description,
            issuer,
            date_received,
            expiry_date,
            certificate_url,
            icon_url
        FROM certification_items 
        WHERE is_active = 1 
        AND language_code = :lang
        ORDER BY sort_order";
        
        $items = $db->query($query, [':lang' => $language]);
        
        return [
            'section' => $sectionData ?? ['title' => 'Certification'],
            'items' => $items
        ];
        
    } catch (Exception $e) {
        error_log("Error fetching certification data: " . $e->getMessage());
        return [
            'section' => ['title' => 'Certification'],
            'items' => []
        ];
    }
}
