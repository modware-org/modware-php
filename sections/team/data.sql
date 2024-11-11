-- Team members
INSERT OR IGNORE INTO team_members (name, position_key, bio_key, photo, credentials, specialties, education, publications, is_active, sort_order) VALUES
    ('Dr. Sarah Johnson', 'clinical_director', 'sarah_bio', '/img/team/sarah.jpg', 'PhD, Licensed Psychologist', '["dbt","cbt","trauma"]', '[{"degree": "PhD in Clinical Psychology", "institution": "Stanford University"}]', '[]', 1, 1),
    ('Dr. Michael Chen', 'lead_therapist', 'michael_bio', '/img/team/michael.jpg', 'PsyD, DBT-LBC', '["dbt","adolescent","family"]', '[{"degree": "PsyD in Psychology", "institution": "UCLA"}]', '[]', 1, 2),
    ('Lisa Rodriguez', 'skills_trainer', 'lisa_bio', '/img/team/lisa.jpg', 'LCSW, DBT-LBC', '["dbt","group","mindfulness"]', '[{"degree": "MSW", "institution": "Columbia University"}]', '[]', 1, 3),
    ('James Wilson', 'research_director', 'james_bio', '/img/team/james.jpg', 'PhD, Research Psychologist', '["research","assessment","outcomes"]', '[{"degree": "PhD in Psychology", "institution": "Harvard University"}]', '[]', 1, 4),
    ('Daria Dymont', 'dbt_therapist', 'daria_bio', '/img/team/daria.jpg', 'DBT-LBC', '["dbt","individual","crisis"]', '[]', '[]', 1, 5),
    ('Ekaterina Khisamieva', 'dbt_therapist', 'ekaterina_bio', '/img/team/ekaterina.jpg', 'DBT-LBC', '["dbt","mindfulness","skills"]', '[]', '[]', 1, 6),
    ('Anastasia Nikolaeva', 'dbt_therapist', 'anastasia_bio', '/img/team/anastasia.jpg', 'DBT-LBC', '["dbt","trauma","emotion"]', '[]', '[]', 1, 7),
    ('Alsou Fazullina', 'dbt_therapist', 'alsou_bio', '/img/team/alsou.jpg', 'DBT-LBC', '["dbt","group","individual"]', '[]', '[]', 1, 8),
    ('Olga Sapietta', 'dbt_therapist', 'olga_bio', '/img/team/olga.jpg', 'DBT-LBC', '["dbt","assessment","skills"]', '[]', '[]', 1, 9),
    ('Liudmila Grishanova', 'dbt_therapist', 'liudmila_bio', '/img/team/liudmila.jpg', 'DBT-LBC', '["dbt","crisis","emotion"]', '[]', '[]', 1, 10);
