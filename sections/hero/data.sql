-- Hero section configuration
INSERT OR REPLACE INTO sections (name, title, description, type, sort_order) VALUES
('hero', 'Hero Section', 'Main hero banner with call to action', 'hero', 2);

-- Hero section specific configuration
INSERT INTO config (name, value, type, description, updated_at) VALUES
('hero_title', 'Комплексная<br>ДБТ терапия', 'text', 'Hero section main title', CURRENT_TIMESTAMP);

INSERT INTO config (name, value, type, description, updated_at) VALUES
('hero_subtitle', '"Создание жизни, достойной того чтобы жить"', 'text', 'Hero section subtitle', CURRENT_TIMESTAMP);

INSERT INTO config (name, value, type, description, updated_at) VALUES
('hero_cta_text', 'ЗАПИСАТЬСЯ НА ПРИЕМ СПЕЦИАЛИСТА', 'text', 'Hero section CTA button text', CURRENT_TIMESTAMP);

INSERT INTO config (name, value, type, description, updated_at) VALUES
('hero_image', '/img/unitydbt-logo.png', 'text', 'Hero section main image', CURRENT_TIMESTAMP);

INSERT INTO config (name, value, type, description, updated_at) VALUES
('hero_image_alt', 'DBT Unity логотип - Диалектическая поведенческая терапия', 'text', 'Hero section image alt text', CURRENT_TIMESTAMP);
