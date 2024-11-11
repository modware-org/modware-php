<?php

function getExpertiseData($language = 'pl') {
    try {
        $db = Database::getInstance();
        
        // Get section translation
        $sectionQuery = "SELECT title, subtitle, description 
                        FROM section_translations 
                        WHERE section_name = 'expertise' 
                        AND language_code = :lang";
        
        $sectionData = $db->query($sectionQuery, [':lang' => $language])[0] ?? null;
        
        // Get expertise items with their points
        $query = "SELECT 
            e.id,
            e.title,
            e.description,
            e.icon_url as icon,
            GROUP_CONCAT(ep.point_text) as key_points
        FROM expertise e
        LEFT JOIN expertise_points ep ON e.id = ep.expertise_id 
            AND ep.language_code = :lang
        WHERE e.active = 1 
        AND e.language_code = :lang
        GROUP BY e.id
        ORDER BY e.display_order";
        
        $expertise = $db->query($query, [':lang' => $language]);
        
        // Process key_points string to array
        foreach ($expertise as &$item) {
            $item['key_points'] = $item['key_points'] 
                ? explode(',', $item['key_points']) 
                : [];
        }
        
        return [
            'section' => $sectionData ?? ['title' => 'Expertise'],
            'expertise' => $expertise
        ];
        
    } catch (Exception $e) {
        error_log("Error fetching expertise data: " . $e->getMessage());
        return [
            'section' => ['title' => 'Expertise'],
            'expertise' => []
        ];
    }
}
