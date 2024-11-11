<?php

function getTeamFullData($language = 'pl') {
    try {
        $db = Database::getInstance();
        
        // Get section translation
        $sectionQuery = "SELECT 
            MAX(CASE WHEN field_name = 'title' THEN translation END) as title,
            MAX(CASE WHEN field_name = 'subtitle' THEN translation END) as subtitle,
            MAX(CASE WHEN field_name = 'description' THEN translation END) as description
        FROM section_translations st
        JOIN languages l ON st.language_id = l.id
        WHERE st.section_name = 'team_full' 
        AND l.code = :lang";
        
        $sectionData = $db->query($sectionQuery, [':lang' => $language])[0] ?? [
            'title' => $language === 'pl' ? 'Nasz Zespół' : 'Our Team',
            'subtitle' => $language === 'pl' ? 'Poznaj naszych specjalistów' : 'Meet Our Specialists',
            'description' => $language === 'pl' ? 'Nasz doświadczony zespół specjalistów' : 'Our experienced team of specialists'
        ];

        // Get team members with translations
        $query = "SELECT 
            t.id,
            t.name,
            t.photo,
            t.credentials,
            t.specialties,
            t.education,
            t.publications,
            COALESCE(tp.translation, t.position_key) as position,
            COALESCE(tb.translation, t.bio_key) as bio
        FROM team_members t
        JOIN languages l ON l.code = :lang
        LEFT JOIN team_translations tp ON t.position_key = tp.translation_key 
            AND tp.type = 'position' AND tp.language_id = l.id
        LEFT JOIN team_translations tb ON t.bio_key = tb.translation_key 
            AND tb.type = 'bio' AND tb.language_id = l.id
        WHERE t.is_active = 1 
        ORDER BY t.sort_order";
        
        $members = $db->query($query, [':lang' => $language]);

        // Get specialty translations
        $specialtyQuery = "SELECT translation_key, translation 
                          FROM team_translations tt
                          JOIN languages l ON l.code = :lang
                          WHERE tt.type = 'specialty' 
                          AND tt.language_id = l.id";
        
        $specialtiesResult = $db->query($specialtyQuery, [':lang' => $language]);
        $specialties = [];
        foreach ($specialtiesResult as $row) {
            $specialties[$row['translation_key']] = $row['translation'];
        }

        // Process JSON fields and translations
        foreach ($members as &$member) {
            $member['specialties'] = json_decode($member['specialties'], true) ?? [];
            $member['education'] = json_decode($member['education'], true) ?? [];
            $member['publications'] = json_decode($member['publications'], true) ?? [];

            // Translate specialties
            $member['specialties'] = array_map(function($specialty) use ($specialties) {
                return $specialties[$specialty] ?? $specialty;
            }, $member['specialties']);
        }
        
        return [
            'section' => $sectionData,
            'team_members' => $members,
            'labels' => [
                'specialties' => $language === 'pl' ? 'Specjalizacje' : 'Specialties',
                'education' => $language === 'pl' ? 'Edukacja' : 'Education',
                'publications' => $language === 'pl' ? 'Publikacje' : 'Publications'
            ]
        ];
        
    } catch (Exception $e) {
        error_log("Error fetching team full data: " . $e->getMessage());
        return [
            'section' => [
                'title' => $language === 'pl' ? 'Nasz Zespół' : 'Our Team',
                'subtitle' => $language === 'pl' ? 'Poznaj naszych specjalistów' : 'Meet Our Specialists',
                'description' => ''
            ],
            'team_members' => [],
            'labels' => []
        ];
    }
}
