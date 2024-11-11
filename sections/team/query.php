<?php

function getTeamData($language = 'pl') {
    try {
        $db = Database::getInstance();
        
        // Get section translation
        $sectionQuery = "SELECT title, subtitle, description 
                        FROM section_translations 
                        WHERE section_name = 'team' 
                        AND language_code = :lang";
        
        $sectionData = $db->query($sectionQuery, [':lang' => $language])[0] ?? null;
        
        // Get team members with translations
        $query = "SELECT 
            t.id,
            t.name,
            t.photo,
            t.credentials,
            t.specialties,
            t.education,
            t.publications,
            tp.position as position,
            tb.bio as bio
        FROM team_members t
        LEFT JOIN team_translations tp ON t.position_key = tp.translation_key 
            AND tp.type = 'position' AND tp.language_code = :lang
        LEFT JOIN team_translations tb ON t.bio_key = tb.translation_key 
            AND tb.type = 'bio' AND tb.language_code = :lang
        WHERE t.is_active = 1 
        ORDER BY t.sort_order";
        
        $members = $db->query($query, [':lang' => $language]);
        
        // Get section note from config
        $noteQuery = "SELECT value 
                     FROM config 
                     WHERE name = 'team_note' 
                     AND language_code = :lang";
        
        $noteData = $db->query($noteQuery, [':lang' => $language])[0] ?? null;
        
        // Process JSON fields
        foreach ($members as &$member) {
            $member['specialties'] = json_decode($member['specialties'], true) ?? [];
            $member['education'] = json_decode($member['education'], true) ?? [];
            $member['publications'] = json_decode($member['publications'], true) ?? [];
        }
        
        return [
            'section' => $sectionData ?? ['title' => 'Team'],
            'members' => $members,
            'note' => $noteData['value'] ?? null
        ];
        
    } catch (Exception $e) {
        error_log("Error fetching team data: " . $e->getMessage());
        return [
            'section' => ['title' => 'Team'],
            'members' => [],
            'note' => null
        ];
    }
}
