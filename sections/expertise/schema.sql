-- Expertise section database schema

-- Expertise translations table
CREATE TABLE IF NOT EXISTS section_translations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    language_id INTEGER,
    section_name VARCHAR(50) NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    translation TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (language_id) REFERENCES languages(id)
);

-- Expertise items table
CREATE TABLE IF NOT EXISTS expertise_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    sort_order INTEGER DEFAULT 0,
    active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
