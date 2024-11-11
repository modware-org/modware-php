-- Blog section configuration
INSERT OR REPLACE INTO sections (name, title, description, type, sort_order) VALUES
('blog', 'Blog Section', 'Blog posts and articles', 'blog', 7);

-- Categories table
CREATE TABLE categories (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    parent_id INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);

-- Tags table
CREATE TABLE tags (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Blog posts table
CREATE TABLE blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    slug TEXT NOT NULL UNIQUE,
    content TEXT,
    excerpt TEXT,
    featured_image TEXT,
    category_id INTEGER,
    author_id INTEGER,
    status TEXT DEFAULT 'draft',
    comment_status TEXT DEFAULT 'open',
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);

-- Blog post tags relationship table
CREATE TABLE blog_post_tags (
    post_id INTEGER,
    tag_id INTEGER,
    PRIMARY KEY (post_id, tag_id),
    FOREIGN KEY (post_id) REFERENCES blog_posts(id),
    FOREIGN KEY (tag_id) REFERENCES tags(id)
);

-- Blog comments table
CREATE TABLE blog_comments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    post_id INTEGER NOT NULL,
    parent_id INTEGER DEFAULT NULL,
    author_name TEXT NOT NULL,
    author_email TEXT NOT NULL,
    author_url TEXT,
    content TEXT NOT NULL,
    status TEXT DEFAULT 'pending',
    ip_address TEXT,
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES blog_posts(id),
    FOREIGN KEY (parent_id) REFERENCES blog_comments(id)
);

-- Blog settings
INSERT INTO config (name, value, type, description) VALUES
('blog_posts_per_page', '10', 'number', 'Number of posts to display per page'),
('blog_show_author', 'true', 'boolean', 'Show author information'),
('blog_show_date', 'true', 'boolean', 'Show post date'),
('blog_show_categories', 'true', 'boolean', 'Show post categories'),
('blog_show_tags', 'true', 'boolean', 'Show post tags'),
('blog_show_comments', 'true', 'boolean', 'Enable comments'),
('blog_moderate_comments', 'true', 'boolean', 'Moderate comments before publishing'),
('blog_notify_comments', 'true', 'boolean', 'Send email notifications for new comments'),
('blog_excerpt_length', '150', 'number', 'Length of post excerpts'),
('blog_sidebar_position', 'right', 'text', 'Sidebar position (left, right, none)'),
('blog_featured_image_size', 'large', 'text', 'Default size for featured images');

-- Initial category
INSERT INTO categories (name, slug, description) VALUES
('Блог', 'blog', 'Основная категория блога');

-- Sample tags
INSERT INTO tags (name, slug, description) VALUES
('ДБТ', 'dbt', 'Статьи о диалектической поведенческой терапии'),
('Психотерапия', 'psychotherapy', 'Материалы о психотерапии'),
('Эмоции', 'emotions', 'Статьи об управлении эмоциями'),
('Осознанность', 'mindfulness', 'Материалы о практиках осознанности');

-- Sample blog post
INSERT INTO blog_posts (
    title, 
    slug, 
    content, 
    excerpt, 
    category_id, 
    author_id, 
    status, 
    published_at
) VALUES (
    'Введение в диалектическую поведенческую терапию',
    'introduction-to-dbt',
    '<!-- Content will be added through admin panel -->',
    'Узнайте основы диалектической поведенческой терапии (DBT) и как она может помочь в управлении эмоциями и улучшении качества жизни.',
    (SELECT id FROM categories WHERE slug = 'blog' LIMIT 1),
    1,
    'published',
    CURRENT_TIMESTAMP
);

-- Link tags to sample post
INSERT INTO blog_post_tags (post_id, tag_id) 
SELECT 
    (SELECT id FROM blog_posts WHERE slug = 'introduction-to-dbt'),
    id 
FROM tags 
WHERE slug IN ('dbt', 'psychotherapy');
