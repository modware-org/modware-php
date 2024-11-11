<?php

function getConsultationsData($language = 'pl') {
    try {
        $db = Database::getInstance();
        
        // Get section translation
        $sectionQuery = "SELECT title, subtitle, description 
                        FROM section_translations 
                        WHERE section_name = 'consultations' 
                        AND language_code = :lang";
        
        $sectionData = $db->query($sectionQuery, [':lang' => $language])[0] ?? null;
        
        // Get consultation types
        $query = "SELECT 
            id,
            title,
            description,
            icon,
            duration,
            price,
            features,
            booking_url
        FROM consultation_types 
        WHERE is_active = 1 
        AND language_code = :lang
        ORDER BY sort_order";
        
        $consultations = $db->query($query, [':lang' => $language]);
        
        // Get section note from config
        $noteQuery = "SELECT value 
                     FROM config 
                     WHERE name = 'consultations_note' 
                     AND language_code = :lang";
        
        $noteData = $db->query($noteQuery, [':lang' => $language])[0] ?? null;
        
        // Process features JSON
        foreach ($consultations as &$consultation) {
            if (isset($consultation['features'])) {
                $consultation['features'] = json_decode($consultation['features'], true) ?? [];
            } else {
                $consultation['features'] = [];
            }
        }
        
        return [
            'section' => $sectionData ?? ['title' => 'Consultations'],
            'consultations' => $consultations,
            'note' => $noteData['value'] ?? null
        ];
        
    } catch (Exception $e) {
        error_log("Error fetching consultations data: " . $e->getMessage());
        return [
            'section' => ['title' => 'Consultations'],
            'consultations' => [],
            'note' => null
        ];
    }
}
