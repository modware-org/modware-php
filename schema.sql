-- Main application database schema

-- Core tables
CREATE TABLE IF NOT EXISTS sites (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     name VARCHAR(100) NOT NULL,
                                     domain VARCHAR(255) NOT NULL UNIQUE,
                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pages (
                                     id INTEGER PRIMARY KEY AUTOINCREMENT,
                                     site_id INTEGER NOT NULL,
                                     title VARCHAR(255) NOT NULL,
                                     slug VARCHAR(255) NOT NULL,
                                     meta_description TEXT,
                                     meta_keywords TEXT,
                                     status TEXT CHECK(status IN ('draft', 'published')) DEFAULT 'draft',
                                     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                     FOREIGN KEY (site_id) REFERENCES sites(id),
                                     UNIQUE (site_id, slug)
);

CREATE TABLE IF NOT EXISTS sections (
                                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                                        page_id INTEGER,
                                        name VARCHAR(100) NOT NULL,
                                        title VARCHAR(255),
                                        description TEXT,
                                        type VARCHAR(50) NOT NULL,
                                        sort_order INTEGER DEFAULT 0,
                                        data TEXT,
                                        position INTEGER NOT NULL DEFAULT 0,
                                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                        FOREIGN KEY (page_id) REFERENCES pages(id)
);

-- Configuration table
CREATE TABLE IF NOT EXISTS config (
                                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                                      name VARCHAR(100) NOT NULL UNIQUE,
                                      value TEXT,
                                      type VARCHAR(50) DEFAULT 'text',
                                      description TEXT,
                                      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                                      updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

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

-- Create indexes for team_members
CREATE INDEX IF NOT EXISTS idx_team_members_active ON team_members(is_active);
CREATE INDEX IF NOT EXISTS idx_team_members_sort_order ON team_members(sort_order);

-- Languages table
CREATE TABLE IF NOT EXISTS languages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add translations table with updated structure
CREATE TABLE IF NOT EXISTS translations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    language_id INTEGER NOT NULL,
    content_type VARCHAR(50) NOT NULL,
    content_id INTEGER NOT NULL,
    field_name VARCHAR(50) NOT NULL,
    translation TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (language_id) REFERENCES languages(id),
    UNIQUE(language_id, content_type, content_id, field_name)
);
