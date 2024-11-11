-- Sitemap section configuration
INSERT OR REPLACE INTO sections (name, title, description, type, sort_order) VALUES
('sitemap', 'Sitemap Section', 'XML sitemap generation', 'sitemap', 98);

-- Sitemap settings table schema
CREATE TABLE sitemap_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type TEXT NOT NULL,
    changefreq TEXT DEFAULT 'weekly',
    priority REAL DEFAULT 0.5,
    is_active BOOLEAN DEFAULT 1,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Sitemap exclusions table schema
CREATE TABLE sitemap_exclusions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    url TEXT NOT NULL,
    reason TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Initial sitemap settings
INSERT INTO sitemap_settings (type, changefreq, priority, is_active) VALUES
('home', 'daily', 1.0, 1),
('blog', 'daily', 0.9, 1),
('blog_posts', 'weekly', 0.8, 1),
('blog_categories', 'weekly', 0.7, 1),
('blog_tags', 'weekly', 0.6, 1),
('pages', 'monthly', 0.5, 1);

-- Sitemap configuration
INSERT INTO config (name, value, type, description) VALUES
('sitemap_auto_ping', 'true', 'boolean', 'Automatically ping search engines when sitemap is updated'),
('sitemap_include_images', 'true', 'boolean', 'Include image information in sitemap'),
('sitemap_include_last_mod', 'true', 'boolean', 'Include last modification date'),
('sitemap_max_urls', '50000', 'number', 'Maximum URLs per sitemap file'),
('sitemap_excluded_categories', '', 'text', 'Comma-separated list of category IDs to exclude'),
('sitemap_robots_txt', 'true', 'boolean', 'Add sitemap location to robots.txt'),
('sitemap_search_engines', 'google.com,bing.com,yandex.ru', 'text', 'Search engines to ping'),
('sitemap_compression', 'true', 'boolean', 'Enable sitemap compression (gzip)'),
('sitemap_cache_lifetime', '3600', 'number', 'Cache lifetime in seconds'),
('sitemap_additional_urls', '', 'text', 'Additional URLs to include in sitemap');
