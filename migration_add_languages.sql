-- Add language support to team_members
ALTER TABLE team_members ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';
CREATE INDEX idx_team_members_lang ON team_members(language_code);

-- Create team translations table
CREATE TABLE IF NOT EXISTS team_translations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    translation_key VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL, -- 'position', 'bio', 'specialty'
    translation TEXT NOT NULL,
    language_code VARCHAR(10) NOT NULL DEFAULT 'pl',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(translation_key, type, language_code)
);

CREATE INDEX idx_team_translations_key ON team_translations(translation_key, type, language_code);

-- Add language support to consultation_types
ALTER TABLE consultation_types ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';
CREATE INDEX idx_consultation_types_lang ON consultation_types(language_code);

-- Add language support to expertise table
ALTER TABLE expertise ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';
CREATE INDEX idx_expertise_lang ON expertise(language_code);

-- Add language support to expertise_points
ALTER TABLE expertise_points ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';
CREATE INDEX idx_expertise_points_lang ON expertise_points(language_code);

-- Add language support to education_items
ALTER TABLE education_items ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';
CREATE INDEX idx_education_items_lang ON education_items(language_code);

-- Add language support to certification_items
ALTER TABLE certification_items ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';
CREATE INDEX idx_certification_items_lang ON certification_items(language_code);

-- Add language support to config table for translatable content
ALTER TABLE config ADD COLUMN language_code VARCHAR(10) DEFAULT NULL;
CREATE INDEX idx_config_lang ON config(language_code);

-- Create section_translations table for section-level content
CREATE TABLE IF NOT EXISTS section_translations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    section_name VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    subtitle TEXT,
    description TEXT,
    language_code VARCHAR(10) NOT NULL DEFAULT 'pl',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(section_name, language_code)
);

-- Insert default section translations
INSERT INTO section_translations (section_name, title, subtitle, description, language_code) VALUES
-- Team section
('team', 'Zespół', NULL, 'Nasz zespół certyfikowanych specjalistów DBT', 'pl'),
('team', 'Team', NULL, 'Our team of certified DBT specialists', 'en'),

-- Team full section
('team_full', 'Nasz Zespół', NULL, 'Poznaj naszych certyfikowanych specjalistów DBT', 'pl'),
('team_full', 'Our Team', NULL, 'Meet our certified DBT specialists', 'en'),

-- Consultations section
('consultations', 'Konsultacje', NULL, 'Oferujemy różne formy konsultacji i terapii', 'pl'),
('consultations', 'Consultations', NULL, 'We offer various forms of consultation and therapy', 'en'),

-- Expertise section
('expertise', 'Obszary Ekspertyzy', NULL, 'Nasze główne obszary specjalizacji', 'pl'),
('expertise', 'Areas of Expertise', NULL, 'Our main areas of specialization', 'en'),

-- Education section
('education', 'Edukacja', NULL, 'Programy szkoleniowe i edukacyjne', 'pl'),
('education', 'Education', NULL, 'Training and educational programs', 'en'),

-- Certification section
('certification', 'Certyfikacja', NULL, 'Nasze certyfikaty i akredytacje', 'pl'),
('certification', 'Certification', NULL, 'Our certificates and accreditations', 'en');

-- Insert team translations
INSERT INTO team_translations (translation_key, type, translation, language_code) VALUES
-- Positions
('clinical_director', 'position', 'Dyrektor Kliniczny', 'pl'),
('clinical_director', 'position', 'Clinical Director', 'en'),
('lead_therapist', 'position', 'Główny Terapeuta', 'pl'),
('lead_therapist', 'position', 'Lead Therapist', 'en'),
('dbt_therapist', 'position', 'Terapeuta DBT', 'pl'),
('dbt_therapist', 'position', 'DBT Therapist', 'en'),
('skills_trainer', 'position', 'Trener Umiejętności', 'pl'),
('skills_trainer', 'position', 'Skills Trainer', 'en'),
('research_director', 'position', 'Dyrektor ds. Badań', 'pl'),
('research_director', 'position', 'Research Director', 'en'),

-- Specialties
('dbt', 'specialty', 'Terapia Dialektyczno-Behawioralna', 'pl'),
('dbt', 'specialty', 'Dialectical Behavior Therapy', 'en'),
('cbt', 'specialty', 'Terapia Poznawczo-Behawioralna', 'pl'),
('cbt', 'specialty', 'Cognitive Behavioral Therapy', 'en'),
('trauma', 'specialty', 'Leczenie Traumy', 'pl'),
('trauma', 'specialty', 'Trauma Treatment', 'en'),
('mindfulness', 'specialty', 'Uważność', 'pl'),
('mindfulness', 'specialty', 'Mindfulness', 'en'),
('group', 'specialty', 'Terapia Grupowa', 'pl'),
('group', 'specialty', 'Group Therapy', 'en'),
('individual', 'specialty', 'Terapia Indywidualna', 'pl'),
('individual', 'specialty', 'Individual Therapy', 'en'),
('crisis', 'specialty', 'Interwencja Kryzysowa', 'pl'),
('crisis', 'specialty', 'Crisis Intervention', 'en'),
('emotion', 'specialty', 'Regulacja Emocji', 'pl'),
('emotion', 'specialty', 'Emotion Regulation', 'en'),
('skills', 'specialty', 'Trening Umiejętności', 'pl'),
('skills', 'specialty', 'Skills Training', 'en'),
('assessment', 'specialty', 'Diagnostyka', 'pl'),
('assessment', 'specialty', 'Assessment', 'en');

-- Function to duplicate existing content for English language
CREATE TEMPORARY TABLE tmp_consultation_types AS SELECT * FROM consultation_types;
INSERT INTO consultation_types (title, description, icon, duration, price, features, booking_url, is_active, sort_order, language_code)
SELECT title, description, icon, duration, price, features, booking_url, is_active, sort_order, 'en'
FROM tmp_consultation_types;
DROP TABLE tmp_consultation_types;

CREATE TEMPORARY TABLE tmp_expertise AS SELECT * FROM expertise;
INSERT INTO expertise (title, description, icon_url, active, display_order, language_code)
SELECT title, description, icon_url, active, display_order, 'en'
FROM tmp_expertise;
DROP TABLE tmp_expertise;

CREATE TEMPORARY TABLE tmp_expertise_points AS SELECT * FROM expertise_points;
INSERT INTO expertise_points (expertise_id, point_text, language_code)
SELECT expertise_id, point_text, 'en'
FROM tmp_expertise_points;
DROP TABLE tmp_expertise_points;

-- Update config entries that need translation
UPDATE config SET language_code = 'pl' WHERE name IN ('consultations_note', 'team_note', 'education_note');
INSERT INTO config (name, value, type, description, language_code)
SELECT name, value, type, description, 'en'
FROM config
WHERE language_code = 'pl' AND name IN ('consultations_note', 'team_note', 'education_note');
