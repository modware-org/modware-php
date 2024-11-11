-- Consultations page configuration
INSERT INTO pages (site_id, title, slug, meta_description, meta_keywords, status) VALUES
    (1, 'Консультации Unity DBT', 'consultations',
    'Запишитесь на консультацию к сертифицированным DBT специалистам. Индивидуальные, групповые и семейные форматы работы.',
    'DBT консультации, психотерапия, индивидуальная терапия, групповая терапия, семейная терапия, запись на консультацию',
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
    3,
    'Консультации DBT терапии | Запись на прием Unity DBT',
    'Профессиональные консультации DBT терапии: индивидуальные, групповые и семейные форматы. Очные и онлайн консультации от сертифицированных DBT специалистов.',
    'консультация DBT терапевта, запись к DBT специалисту, индивидуальная DBT терапия, групповая DBT терапия, онлайн консультация DBT',
    'DBT Unity Team',
    'Консультации DBT терапии | Все форматы работы',
    'Запишитесь на консультацию DBT терапии в удобном формате. Индивидуальные, групповые и онлайн консультации от экспертов DBT.',
    'summary'
WHERE NOT EXISTS (
    SELECT 1 FROM meta WHERE page_id = 3
);

-- Menu section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (3, 'menu', 'Main Menu', 'Navigation menu', 'menu', 0,
     '{"logo": "/img/unitydbt-logo.png", "logo_alt": "Unity DBT", "show_search": true, "sticky": true, "mobile_breakpoint": 768, "cta_text": "ЗАПИСАТЬСЯ НА ПРИЕМ", "cta_url": "#contact"}');


-- Footer section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (3, 'footer', 'Footer', 'Page footer', 'footer', 100, '{}');


-- Consultations page sections configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data, position) VALUES
    (3, 'consultations-intro', 'Консультации DBT', 'Форматы работы с DBT специалистами', 'content', 1,
    '{"content": "Мы предлагаем различные форматы консультаций, чтобы каждый мог выбрать наиболее подходящий для себя вариант работы. Все наши специалисты имеют сертификацию по DBT и регулярно повышают свою квалификацию."}', 0),
    
    (3, 'consultations', 'Варианты консультаций', 'Доступные форматы консультаций', 'consultations', 2, '{}', 1),
    
    (3, 'faq', 'Частые вопросы', 'Ответы на популярные вопросы о консультациях', 'faq', 3,
    '{"questions": [
        {
            "question": "Как проходит первичная консультация?",
            "answer": "На первичной консультации специалист познакомится с вами, выслушает ваш запрос, проведет первичную диагностику и предложит подходящий формат работы."
        },
        {
            "question": "Можно ли получить консультацию онлайн?",
            "answer": "Да, мы проводим онлайн-консультации через защищенные каналы видеосвязи. Эффективность онлайн и очных консультаций сопоставима."
        },
        {
            "question": "Как часто нужно посещать консультации?",
            "answer": "Частота консультаций определяется индивидуально, но обычно рекомендуется посещать индивидуальные сессии 1 раз в неделю, а групповые тренинги - 1 раз в неделю."
        },
        {
            "question": "Какова продолжительность терапии?",
            "answer": "Длительность терапии зависит от ваших целей и потребностей. Стандартный курс DBT длится около 6 месяцев, но может быть короче или длиннее."
        }
    ]}', 2);
