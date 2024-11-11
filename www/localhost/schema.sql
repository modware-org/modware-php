-- Create site record for localhost
INSERT INTO sites (name, domain)
SELECT 'DBT Unity Local', 'localhost'
WHERE NOT EXISTS (
    SELECT 1 FROM sites WHERE domain = 'localhost'
);


-- Insert default language
INSERT OR IGNORE INTO languages (code, name) VALUES ('en', 'English');
INSERT OR IGNORE INTO languages (code, name) VALUES ('pl', 'Polish');

-- Insert translations for English language
INSERT OR IGNORE INTO translations (language_id, content_type, content_id, field_name, translation)
SELECT
    (SELECT id FROM languages WHERE code = 'en'),
    'certification',
    1,
    'section_title',
    'Certification'
WHERE NOT EXISTS (
    SELECT 1 FROM translations
    WHERE language_id = (SELECT id FROM languages WHERE code = 'en')
      AND content_type = 'certification'
      AND content_id = 1
      AND field_name = 'section_title'
);

INSERT OR IGNORE INTO translations (language_id, content_type, content_id, field_name, translation)
VALUES
    -- Certification section translations
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'section_description', 'Our team has completed comprehensive DBT certification training'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'info_text', 'We are proud to have completed the official Dialectical Behavior Therapy certification program'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'download_button', 'View Certificate'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'badge_training_title', 'Professional Training'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'badge_training_description', 'Completed intensive DBT training program'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'badge_practice_title', 'Clinical Practice'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'badge_practice_description', 'Extensive experience in DBT implementation'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'badge_supervision_title', 'Expert Supervision'),
    ((SELECT id FROM languages WHERE code = 'en'), 'certification', 1, 'badge_supervision_description', 'Regular supervision from certified DBT experts'),

    -- Specialists section translations
    ((SELECT id FROM languages WHERE code = 'en'), 'specialists', 1, 'intro_title', 'Our Specialists'),
    ((SELECT id FROM languages WHERE code = 'pl'), 'specialists', 1, 'intro_title', 'Nasi Specjaliści'),
    ((SELECT id FROM languages WHERE code = 'en'), 'specialists', 1, 'intro_text', 'Meet our team of certified specialists'),
    ((SELECT id FROM languages WHERE code = 'pl'), 'specialists', 1, 'intro_text', 'Poznaj nasz zespół certyfikowanych specjalistów'),
    ((SELECT id FROM languages WHERE code = 'en'), 'specialists', 1, 'contact_button', 'Book a consultation'),
    ((SELECT id FROM languages WHERE code = 'pl'), 'specialists', 1, 'contact_button', 'Umów konsultację'),
    ((SELECT id FROM languages WHERE code = 'en'), 'team', 1, 'section_title', 'Our Team'),
    ((SELECT id FROM languages WHERE code = 'pl'), 'team', 1, 'section_title', 'Nasz Zespół'),
    ((SELECT id FROM languages WHERE code = 'en'), 'team', 1, 'section_description', 'Our team consists of certified specialists with extensive experience'),
    ((SELECT id FROM languages WHERE code = 'pl'), 'team', 1, 'section_description', 'Nasz zespół składa się z certyfikowanych specjalistów z bogatym doświadczeniem'),
    ((SELECT id FROM languages WHERE code = 'en'), 'specialist', 1, 'specializations_title', 'Specializations'),
    ((SELECT id FROM languages WHERE code = 'pl'), 'specialist', 1, 'specializations_title', 'Specjalizacje'),
    ((SELECT id FROM languages WHERE code = 'en'), 'specialist', 1, 'not_found', 'Specialist information not available'),
    ((SELECT id FROM languages WHERE code = 'pl'), 'specialist', 1, 'not_found', 'Informacje o specjaliście nie są dostępne'),

    -- Expertise section translations
    ((SELECT id FROM languages WHERE code = 'en'), 'expertise', 1, 'section_title', 'Areas of Expertise'),
    ((SELECT id FROM languages WHERE code = 'en'), 'expertise', 1, 'not_available', 'Expertise information currently not available'),
    ((SELECT id FROM languages WHERE code = 'en'), 'common', 1, 'icon_alt_text', 'icon'),

    -- Consultation section translations
    ((SELECT id FROM languages WHERE code = 'en'), 'consultation', 1, 'section_title', 'Consultations'),
    ((SELECT id FROM languages WHERE code = 'en'), 'consultation', 1, 'types_title', 'Types of Consultations'),
    ((SELECT id FROM languages WHERE code = 'en'), 'consultation', 1, 'book_title', 'Book a Consultation'),
    ((SELECT id FROM languages WHERE code = 'en'), 'consultation', 1, 'not_available', 'Consultation information currently not available'),
    ((SELECT id FROM languages WHERE code = 'en'), 'common', 1, 'minutes', 'minutes'),
    ((SELECT id FROM languages WHERE code = 'en'), 'common', 1, 'currency', 'USD'),

    -- Education section translations
    ((SELECT id FROM languages WHERE code = 'en'), 'education', 1, 'section_title', 'Education'),
    ((SELECT id FROM languages WHERE code = 'en'), 'education', 1, 'not_available', 'Education program information currently not available'),
    ((SELECT id FROM languages WHERE code = 'en'), 'education', 1, 'learn_more_button', 'Learn More');
