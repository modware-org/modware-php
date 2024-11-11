-- Example section schema
CREATE TABLE IF NOT EXISTS example_section_data
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    section_id INTEGER NOT NULL,
    video_id   VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (section_id) REFERENCES sections (id)
);

-- Create translations table if it doesn't exist
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

-- Create languages table if it doesn't exist
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
