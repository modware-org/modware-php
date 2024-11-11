-- Ensure languages exist
INSERT OR IGNORE INTO languages (code, name) VALUES 
('pl', 'Polish'),
('en', 'English');

-- Get language IDs
WITH pl_lang AS (SELECT id FROM languages WHERE code = 'pl'),
     en_lang AS (SELECT id FROM languages WHERE code = 'en')

-- Insert section translations for team_full
INSERT OR REPLACE INTO section_translations (
    language_id, 
    section_name, 
    field_name, 
    translation
) VALUES 
    ((SELECT id FROM pl_lang), 'team_full', 'title', 'Nasz Zespół'),
    ((SELECT id FROM pl_lang), 'team_full', 'subtitle', 'Poznaj naszych specjalistów'),
    ((SELECT id FROM pl_lang), 'team_full', 'description', 'Nasz doświadczony zespół specjalistów'),
    
    ((SELECT id FROM en_lang), 'team_full', 'title', 'Our Team'),
    ((SELECT id FROM en_lang), 'team_full', 'subtitle', 'Meet Our Specialists'),
    ((SELECT id FROM en_lang), 'team_full', 'description', 'Our experienced team of specialists');
