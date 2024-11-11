<?php

function getEducationData($language = 'pl') {
    try {
        $db = Database::getInstance();
        
        // Get section translation
        $sectionQuery = "SELECT title, subtitle, description 
                        FROM section_translations 
                        WHERE section_name = 'education' 
                        AND language_code = :lang";
        
        $sectionData = $db->query($sectionQuery, [':lang' => $language])[0] ?? null;
        
        // Get education items
        $query = "SELECT 
            id,
            title,
            description,
            duration,
            price,
            features,
            schedule,
            registration_url
        FROM education_items 
        WHERE is_active = 1 
        AND language_code = :lang
        ORDER BY sort_order";
        
        $items = $db->query($query, [':lang' => $language]);
        
        // Get section note from config
        $noteQuery = "SELECT value 
                     FROM config 
                     WHERE name = 'education_note' 
                     AND language_code = :lang";
        
        $noteData = $db->query($noteQuery, [':lang' => $language])[0] ?? null;
        
        // Process JSON fields
        foreach ($items as &$item) {
            if (isset($item['features'])) {
                $item['features'] = json_decode($item['features'], true) ?? [];
            }
            if (isset($item['schedule'])) {
                $item['schedule'] = json_decode($item['schedule'], true) ?? [];
            }
        }
        
        return [
            'section' => $sectionData ?? ['title' => 'Education'],
            'items' => $items,
            'note' => $noteData['value'] ?? null
        ];
        
    } catch (Exception $e) {
        error_log("Error fetching education data: " . $e->getMessage());
        return [
            'section' => ['title' => 'Education'],
            'items' => [],
            'note' => null
        ];
    }
}
