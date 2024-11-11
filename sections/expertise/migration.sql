-- Create expertise_sections table for section-level translations
CREATE TABLE IF NOT EXISTS expertise_sections (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    language_code VARCHAR(10) NOT NULL DEFAULT 'pl',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(language_code)
);

-- Add language column to expertise table
ALTER TABLE expertise ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';

-- Add language column to expertise_points table
ALTER TABLE expertise_points ADD COLUMN language_code VARCHAR(10) NOT NULL DEFAULT 'pl';

-- Create indexes for faster language-based queries
CREATE INDEX idx_expertise_lang ON expertise(language_code);
CREATE INDEX idx_expertise_points_lang ON expertise_points(language_code);

-- Insert default section translations
INSERT INTO expertise_sections (title, language_code) VALUES
('Obszary Ekspertyzy', 'pl'),
('Areas of Expertise', 'en');

-- Create a temporary table for expertise
CREATE TABLE expertise_temp AS SELECT * FROM expertise;

-- Create a temporary table for expertise_points
CREATE TABLE expertise_points_temp AS SELECT * FROM expertise_points;

-- Insert English translations (example - adjust based on your needs)
INSERT INTO expertise (title, description, icon_url, active, display_order, language_code)
SELECT title, description, icon_url, active, display_order, 'en'
FROM expertise_temp;

INSERT INTO expertise_points (expertise_id, point_text, language_code)
SELECT ep.expertise_id, ep.point_text, 'en'
FROM expertise_points_temp ep;

-- Drop temporary tables
DROP TABLE expertise_temp;
DROP TABLE expertise_points_temp;
