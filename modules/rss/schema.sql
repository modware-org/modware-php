-- RSS section configuration
INSERT OR REPLACE INTO sections (name, title, description, type, sort_order) VALUES
('rss', 'RSS Feed Section', 'RSS feed generation', 'rss', 99);

-- RSS settings table schema
CREATE TABLE rss_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    feed_title TEXT NOT NULL,
    feed_description TEXT,
    feed_language TEXT DEFAULT 'ru',
    items_count INTEGER DEFAULT 10,
    is_active BOOLEAN DEFAULT 1,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- RSS feed types table schema
CREATE TABLE rss_feed_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- RSS exclusions table schema
CREATE TABLE rss_exclusions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    content_id INTEGER NOT NULL,
    content_type TEXT NOT NULL,
    reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Initial RSS settings
INSERT INTO rss_settings (
    feed_title, 
    feed_description, 
    feed_language, 
    items_count, 
    is_active
) VALUES (
    'DBT Unity Blog',
    'Новости и статьи о диалектической поведенческой терапии',
    'ru',
    10,
    1
);

-- Initial feed types
INSERT INTO rss_feed_types (name, slug, description, is_active) VALUES
('Все записи', 'all', 'Все записи блога', 1),
('Новости', 'news', 'Новости и обновления', 1),
('Статьи', 'articles', 'Статьи и публикации', 1),
('Категории', 'categories', 'Записи по категориям', 1);

-- RSS configuration
INSERT INTO config (name, value, type, description) VALUES
('rss_auto_discovery', 'true', 'boolean', 'Add RSS auto-discovery link to pages'),
('rss_include_content', 'true', 'boolean', 'Include full content in feed'),
('rss_include_featured_image', 'true', 'boolean', 'Include featured images in feed'),
('rss_include_categories', 'true', 'boolean', 'Include category information'),
('rss_include_author', 'true', 'boolean', 'Include author information'),
('rss_cache_lifetime', '3600', 'number', 'Cache lifetime in seconds'),
('rss_compression', 'true', 'boolean', 'Enable feed compression (gzip)'),
('rss_excluded_categories', '', 'text', 'Comma-separated list of category IDs to exclude'),
('rss_custom_namespaces', '', 'text', 'Custom XML namespaces'),
('rss_custom_elements', '', 'text', 'Custom feed elements');
