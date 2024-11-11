-- Home page configuration
INSERT INTO pages (site_id, title, slug, meta_description, meta_keywords, status)
VALUES (2, 'Visualiser', 'home',
        'Эффективная помощь при эмоциональной нестабильности от сертифицированных DBT специалистов',
        'DBT, диалектическая поведенческая терапия, эмоциональная нестабильность, психотерапия', 'published');

-- Meta configuration
INSERT INTO meta (page_id,
                  title,
                  description,
                  keywords,
                  author,
                  og_title,
                  og_description,
                  twitter_card)
VALUES (9,
        'Unity DBT | Центр диалектической поведенческой терапии',
        'Unity DBT - ведущий центр диалектической поведенческой терапии в России. Сертифицированные специалисты, индивидуальные и групповые программы, научно доказанные методы.',
        'DBT терапия, диалектическая поведенческая терапия, центр DBT, эмоциональная регуляция, психотерапия DBT',
        'DBT Unity Team',
        'Unity DBT - Центр диалектической поведенческой терапии',
        'Профессиональная помощь в управлении эмоциями. Сертифицированные DBT специалисты, индивидуальные и групповые программы.',
        'summary');

-- Menu section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data)

VALUES ( 9, 'visualizer', 'Main Menu', 'Navigation menu', 'visualizer', 0, '{}');


-- Add Visualizer Page
INSERT INTO pages (
    site_id,
    title,
    slug,
    meta_description,
    meta_keywords,
    status
) VALUES (
             2,
             'File Visualizer',
             'visualizer',
             'Visualize and manage uploaded files',
             'files, upload, visualization',
             'published'
         );

-- Add Visualizer Section
INSERT INTO sections (
    page_id,
    name,
    title,
    description,
    type,
    sort_order,
    position
) VALUES (
             (SELECT id FROM pages WHERE slug = 'visualizer' AND site_id = 2),
             'visualizer',
             'File Visualizer',
             'Section to visualize and manage uploaded files',
             'files',
             1,
             0
         );
