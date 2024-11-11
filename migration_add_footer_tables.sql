-- Migration to add footer_links and footer_social tables

-- Footer Links Table
CREATE TABLE IF NOT EXISTS footer_links (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    column_number INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Footer Social Links Table
CREATE TABLE IF NOT EXISTS footer_social (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    platform VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon_svg TEXT,
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_footer_links_column ON footer_links(column_number, sort_order);
CREATE INDEX IF NOT EXISTS idx_footer_social_sort ON footer_social(sort_order);
