-- Specialists page configuration
INSERT INTO pages (site_id, title, slug, meta_description, meta_keywords, status) VALUES
    (1, 'Специалисты Unity DBT', 'specialists',
     'Познакомьтесь с нашей командой сертифицированных специалистов по диалектической поведенческой терапии. Опытные психологи и психотерапевты с международной сертификацией.',
     'DBT специалисты, психологи, психотерапевты, DBT терапевты, сертифицированные специалисты, команда Unity DBT',
     'published');

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
    5,
    'Профили DBT специалистов | Эксперты Unity DBT',
    'Подробные профили наших DBT специалистов: образование, опыт работы, сертификации и специализации. Выберите своего DBT терапевта в Unity DBT.',
    'профили DBT специалистов, DBT терапевты Москва, опытные психологи DBT, сертифицированные DBT специалисты, эксперты DBT терапии',
    'DBT Unity Team',
    'Профили специалистов DBT терапии | Unity DBT',
    'Познакомьтесь с нашими DBT специалистами. Опытные и сертифицированные психотерапевты помогут вам достичь желаемых изменений.',
    'summary'
WHERE NOT EXISTS (
    SELECT 1 FROM meta WHERE page_id = 5
);

-- Menu section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
(5, 'menu', 'Main Menu', 'Navigation menu', 'menu', 0,
    '{"logo": "/img/unitydbt-logo.png", "logo_alt": "Unity DBT", "show_search": true, "sticky": true, "mobile_breakpoint": 768, "cta_text": "ЗАПИСАТЬСЯ НА ПРИЕМ", "cta_url": "#contact"}');

-- Footer section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
(5, 'footer', 'Footer', 'Page footer', 'footer', 100, '{}');

-- Specialists page sections configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data, position) VALUES
    (5, 'specialists-intro', 'Специалист Unity DBT', 'Информация о специалисте', 'content', 1,
    '{"content": "Каждый специалист Unity DBT имеет обширный опыт работы и международную сертификацию в области диалектической поведенческой терапии. Мы постоянно совершенствуем свои навыки и применяем современные научно обоснованные методы терапии."}', 0),
    
    (5, 'specialist-profile', 'Профиль специалиста', 'Детальная информация о специалисте', 'specialist-profile', 2, '{}', 1),
    
    (5, 'expertise', 'Области экспертизы', 'Специализация и опыт работы', 'content', 3,
    '{"content": "Основные направления работы:
    - Индивидуальная психотерапия
    - Групповой тренинг навыков DBT
    - Работа с эмоциональной дисрегуляцией
    - Кризисное консультирование
    - Работа с травматическим опытом
    - Межличностная эффективность
    - Развитие осознанности
    - Управление стрессом"}', 2),
    
    (5, 'education', 'Образование и сертификация', 'Квалификация и обучение', 'content', 7,
    '{"content": "Профессиональная подготовка включает:
    - Базовое психологическое/медицинское образование
    - Интенсивный тренинг по DBT
    - Международная сертификация DBT-LBC
    - Регулярные супервизии и повышение квалификации
    - Участие в профессиональных конференциях
    - Постоянное изучение новых исследований в области DBT"}', 3),
    
    (5, 'consultation', 'Запись на консультацию', 'Как записаться на прием', 'content', 5,
    '{"content": "Чтобы записаться на консультацию:
    1. Используйте форму записи на сайте
    2. Позвоните по телефону
    3. Напишите на email
    
    На первой встрече мы обсудим ваш запрос и определим наиболее эффективный план работы."}', 4);
