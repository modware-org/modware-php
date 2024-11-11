-- Home page configuration
INSERT INTO pages (site_id, title, slug, meta_description, meta_keywords, status)
VALUES
    (1, 'Unity DBT - Диалектическая поведенческая терапия', 'home', 'Эффективная помощь при эмоциональной нестабильности от сертифицированных DBT специалистов', 'DBT, диалектическая поведенческая терапия, эмоциональная нестабильность, психотерапия', 'published');

-- Meta configuration
INSERT INTO meta (
    page_id,
    title,
    description,
    keywords,
    author,
    og_title,
    og_description,
    twitter_card
) SELECT 
    1,
    'Unity DBT | Центр диалектической поведенческой терапии',
    'Unity DBT - ведущий центр диалектической поведенческой терапии в России. Сертифицированные специалисты, индивидуальные и групповые программы, научно доказанные методы.',
    'DBT терапия, диалектическая поведенческая терапия, центр DBT, эмоциональная регуляция, психотерапия DBT',
    'DBT Unity Team',
    'Unity DBT - Центр диалектической поведенческой терапии',
    'Профессиональная помощь в управлении эмоциями. Сертифицированные DBT специалисты, индивидуальные и групповые программы.',
    'summary'
WHERE NOT EXISTS (
    SELECT 1 FROM meta WHERE page_id = 1
);

-- Menu section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (1, 'menu', 'Main Menu', 'Navigation menu', 'menu', 0,
     '{"logo": "/img/unitydbt-logo.png", "logo_alt": "Unity DBT", "show_search": true, "sticky": true, "mobile_breakpoint": 768, "cta_text": "ЗАПИСАТЬСЯ НА ПРИЕМ", "cta_url": "#contact"}');


-- Footer section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (1, 'footer', 'Footer', 'Page footer', 'footer', 100, '{}');




-- Home page sections configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data, position)
VALUES
    (1, 'about', 'О нас', 'Узнайте больше о Unity DBT', 'content', 2, '{"content": "Unity DBT - это команда сертифицированных специалистов по диалектической поведенческой терапии. Мы помогаем людям развить навыки управления эмоциями и улучшить качество жизни."}', 1), (1, 'program', 'Наша программа', 'Основные компоненты DBT терапии', 'program', 3, '{"components": ["individual","group","phone","consultation"]}', 2), (1, 'indications', 'Показания', 'Кому подходит DBT терапия', 'indications', 4, '{"items": ["emotional","interpersonal","behavioral","cognitive"]}', 3), (1, 'team', 'Наша команда', 'Познакомьтесь с нашими специалистами', 'team', 5, '{}', 4);


-- Certification section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data)
VALUES
    (1, 'certification', 'Сертификация', 'Наши профессиональные достижения', 'certification', 1, '{}');


-- Team sections configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data)
VALUES
    (1, 'team', 'Наша команда', 'Познакомьтесь с нашими специалистами', 'team', 2, '{}');


-- Indications section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data)
VALUES
    (1, 'indications', 'Who Can Benefit', 'DBT can help with various challenges', 'indications', 3, '{"items": [
        {"title": "Emotion Regulation", "icon": "emotion-icon.svg", "description": "Learn to understand and manage emotions effectively"},
        {"title": "Mindfulness", "icon": "mindfulness-icon.svg", "description": "Develop present-moment awareness and focus"},
        {"title": "Interpersonal Skills", "icon": "interpersonal-icon.svg", "description": "Improve relationships and communication"},
        {"title": "Stress Tolerance", "icon": "stress-icon.svg", "description": "Build resilience and cope with difficult situations"}
    ]}');

-- About section configuration
INSERT OR REPLACE INTO sections (page_id, name, title, description, type, sort_order) VALUES
    (1, 'about', 'About Section', 'Information about the team and certification', 'about', 3);



-- Program section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (1, 'program', 'Our Programs', 'Comprehensive DBT Training and Support', 'program', 4,
    '{"programs": [
        {"title": "Individual Therapy", "description": "One-on-one sessions tailored to your needs"},
        {"title": "Group Skills Training", "description": "Learn and practice DBT skills in a supportive group setting"},
        {"title": "Phone Coaching", "description": "Get support between sessions when needed"},
        {"title": "Therapist Consultation", "description": "Our team meets regularly to provide the best care"}
    ]}');
