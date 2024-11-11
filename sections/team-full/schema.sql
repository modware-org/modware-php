-- Team members table
CREATE TABLE IF NOT EXISTS team_members
(
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          VARCHAR(100) NOT NULL,
    position_key  VARCHAR(50)  NOT NULL,
    bio_key       VARCHAR(50)  NOT NULL,
    photo         VARCHAR(255),
    credentials   VARCHAR(255),
    specialties   TEXT,         -- JSON array of specialty keys
    education     TEXT,         -- JSON array of education entries
    publications  TEXT,         -- JSON array of publication entries
    is_active     INTEGER DEFAULT 1,
    sort_order    INTEGER DEFAULT 0,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes after table exists
CREATE INDEX IF NOT EXISTS idx_team_members_active ON team_members(is_active);
CREATE INDEX IF NOT EXISTS idx_team_members_sort_order ON team_members(sort_order);

-- Ensure translations table exists (if not already created by other sections)
CREATE TABLE IF NOT EXISTS translations
(
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    language_id  INTEGER      NOT NULL,
    content_type VARCHAR(50)  NOT NULL,
    content_id   INTEGER      NOT NULL,
    field_name   VARCHAR(100) NOT NULL,
    translation  TEXT,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (language_id, content_type, content_id, field_name)
);

-- Ensure languages table exists (if not already created by other sections)
CREATE TABLE IF NOT EXISTS languages
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    code       VARCHAR(5)  NOT NULL UNIQUE,
    name       VARCHAR(50) NOT NULL,
    is_default INTEGER   DEFAULT 0,
    is_active  INTEGER   DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default translation keys for section title and description
INSERT OR IGNORE INTO translations (language_id, content_type, content_id, field_name, translation)
SELECT 
    l.id,
    'section',
    (SELECT id FROM sections WHERE name = 'team-full'),
    'title',
    'Our Team'
FROM languages l
WHERE l.code = 'en';

INSERT OR IGNORE INTO translations (language_id, content_type, content_id, field_name, translation)
SELECT 
    l.id,
    'section',
    (SELECT id FROM sections WHERE name = 'team-full'),
    'description',
    'Meet our experienced team of professionals'
FROM languages l
WHERE l.code = 'en';
