-- Admin database schema

-- Admin users and authentication
CREATE TABLE IF NOT EXISTS admin_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role TEXT CHECK(role IN ('admin', 'editor', 'viewer')) DEFAULT 'editor',
    is_active INTEGER DEFAULT 1,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Example users (passwords will be hashed by create_user.php)
INSERT OR IGNORE INTO admin_users ( username, email, password_hash, role, is_active, last_login) VALUES
( 'admin', 'admin@example.com', '$2y$10$Q2ZAV0yw4yAG/VUshJmIIul9xXnXy6pqgGpZ7.0CP6/u3jfivz/9O', 'admin', 1, null),
( 'editor', 'editor@example.com', '$2y$10$t7RVFz8uhMUdemoXBfa9KOCk3kin4HjFKplZGeIBPH5spFukr8ucS', 'editor', 1, null),
( 'viewer', 'viewer@example.com', '$2y$10$BcAOoAvF1jevVLpNL1p5fOL3OOHw4l80pweNQ8e3yGi3mvXPYwg7e', 'viewer', 1, null),
('olga', 'olga@example.com', '$2y$10$byYKuhx/V3mxoulJ/JezzupwtQialcJ7brZ2jKN3xN9547.XJkAX2', 'admin', 1, null);


CREATE TABLE IF NOT EXISTS admin_sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id)
);

-- Admin settings and configurations
CREATE TABLE IF NOT EXISTS admin_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    category VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (category, name)
);

-- Components configuration
CREATE TABLE IF NOT EXISTS admin_components (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    type VARCHAR(50) NOT NULL,
    config TEXT,
    is_active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Modules configuration
CREATE TABLE IF NOT EXISTS admin_modules (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    type VARCHAR(50) NOT NULL,
    config TEXT,
    is_active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sections configuration
CREATE TABLE IF NOT EXISTS admin_sections (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    type VARCHAR(50) NOT NULL,
    config TEXT,
    is_active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Integration settings
CREATE TABLE IF NOT EXISTS admin_integrations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE,
    type VARCHAR(50) NOT NULL,
    config TEXT,
    is_active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Example webhook configuration
INSERT OR IGNORE INTO admin_integrations (name, type, config, is_active) VALUES (
    'Example Section Updates',
    'webhook',
    '{
        "url": "https://api.example.com/webhooks/content-updates",
        "events": ["content.created", "content.updated", "content.deleted"],
        "secret_key": "your-secret-key-here"
    }',
    1
);

-- Activity logging
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id INTEGER NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id)
);
