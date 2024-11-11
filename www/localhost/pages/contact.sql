-- Contact page configuration
INSERT INTO pages (site_id, title, slug, meta_description, meta_keywords, status) VALUES
    (1, 'Контакты Unity DBT', 'contact',
    'Свяжитесь с Unity DBT. Запись на консультации, контактная информация и адрес центра диалектической поведенческой терапии.',
    'контакты DBT, запись на консультацию, адрес центра DBT, телефон DBT центра',
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
    7,
    'Контакты Unity DBT | Запись на консультацию DBT терапии',
    'Свяжитесь с центром DBT терапии Unity DBT. Запись на консультации, онлайн-запись, контактные телефоны, адрес и схема проезда. Ответы на частые вопросы.',
    'контакты DBT центра, запись на DBT терапию, консультация DBT терапевта, онлайн запись DBT, адрес DBT центра',
    'DBT Unity Team',
    'Контакты и запись на консультацию | Unity DBT',
    'Запишитесь на консультацию DBT терапевта. Удобная онлайн-запись, контактные телефоны и адрес центра Unity DBT.',
    'summary'
WHERE NOT EXISTS (
    SELECT 1 FROM meta WHERE page_id = 7
);

-- Menu section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (7, 'menu', 'Main Menu', 'Navigation menu', 'menu', 0,
     '{"logo": "/img/unitydbt-logo.png", "logo_alt": "Unity DBT", "show_search": true, "sticky": true, "mobile_breakpoint": 768, "cta_text": "ЗАПИСАТЬСЯ НА ПРИЕМ", "cta_url": "#contact"}');


-- Footer section configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (7, 'footer', 'Footer', 'Page footer', 'footer', 100, '{}');


-- Contact page sections configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data, position) VALUES
    (7, 'contact-intro', 'Свяжитесь с нами', 'Контактная информация Unity DBT', 'content', 1,
    '{"content": "Мы готовы ответить на ваши вопросы и помочь записаться на консультацию. Выберите удобный для вас способ связи."}', 0),
    
    (7, 'contact-info', 'Контактная информация', 'Способы связи с нами', 'contact-info', 2,
    '{"phone": "+7 (999) 123-45-67",
      "email": "info@unitydbt.ru",
      "telegram": "@unitydbt",
      "whatsapp": "+7 (999) 123-45-67",
      "working_hours": "Пн-Пт: 9:00 - 21:00\nСб: 10:00 - 18:00\nВс: выходной",
      "address": "г. Москва, ул. Примерная, д. 123, офис 45"}', 1),
    
    (7, 'contact-form', 'Форма обратной связи', 'Отправьте нам сообщение', 'contact-form', 3,
    '{"success_message": "Спасибо за ваше сообщение! Мы свяжемся с вами в ближайшее время.",
      "fields": [
        {"name": "name", "label": "Ваше имя", "type": "text", "required": true},
        {"name": "phone", "label": "Телефон", "type": "tel", "required": true},
        {"name": "email", "label": "Email", "type": "email", "required": true},
        {"name": "preferred_contact", "label": "Предпочтительный способ связи", "type": "select", 
         "options": ["Телефон", "WhatsApp", "Telegram", "Email"], "required": true},
        {"name": "consultation_type", "label": "Интересующий формат консультации", "type": "select",
         "options": ["Первичная консультация", "Индивидуальная терапия", "Групповой тренинг", "Семейная консультация"],
         "required": false},
        {"name": "message", "label": "Сообщение", "type": "textarea", "required": false}
      ]}', 2),
    
    (7, 'map', 'Как добраться', 'Расположение центра на карте', 'map', 4,
    '{"latitude": "55.123456",
      "longitude": "37.123456",
      "zoom": "16",
      "marker_title": "Unity DBT",
      "directions": [
        "От метро Примерная - 7 минут пешком",
        "Выход из метро №3, направо до светофора",
        "Через дорогу, вдоль улицы Примерная",
        "Вход со стороны улицы, первый этаж"
      ]}', 3),
    
    (7, 'faq', 'Частые вопросы', 'Ответы на популярные вопросы', 'faq', 5,
    '{"questions": [
        {
            "question": "Как записаться на консультацию?",
            "answer": "Вы можете записаться на консультацию через форму на сайте, по телефону или в мессенджерах. Администратор поможет выбрать удобное время."
        },
        {
            "question": "Какие способы оплаты вы принимаете?",
            "answer": "Мы принимаем оплату наличными, банковскими картами и безналичным переводом. Оплата производится после консультации."
        },
        {
            "question": "Можно ли перенести или отменить консультацию?",
            "answer": "Да, консультацию можно перенести или отменить не позднее чем за 24 часа до назначенного времени. Пожалуйста, сообщите об этом администратору."
        },
        {
            "question": "Проводите ли вы онлайн консультации?",
            "answer": "Да, мы проводим онлайн консультации через защищенные каналы видеосвязи. Эффективность онлайн формата сопоставима с очными встречами."
        }
    ]}', 4);


-- Contact sections configuration
INSERT INTO sections (page_id, name, title, description, type, sort_order, data) VALUES
    (7, 'contact-info', 'Контактная информация', 'Как с нами связаться', 'contact', 1,
    '{"phone": "+7 (XXX) XXX-XX-XX", "email": "contact@unitydbt.ru", "address": "Адрес клиники"}'),
    (7, 'contact-form', 'Форма обратной связи', 'Отправьте нам сообщение', 'form', 2, '{}'),
    (7, 'map', 'Как добраться', 'Схема проезда', 'map', 3, '{"coordinates": "XX.XXXXX,XX.XXXXX"}');
