-- Menu section configuration
INSERT OR
REPLACE INTO sections (name, title, description, type, sort_order)
VALUES
    ('menu', 'Menu Section', 'Main navigation menu', 'menu', 1);

-- Menu settings in config
INSERT INTO config (name, value, type, description)
VALUES ('menu_logo', '/img/unitydbt-logo.png', 'text', 'Menu logo image path'),
       ('menu_logo_alt', 'DBT Unity', 'text', 'Menu logo alt text'),
       ('menu_mobile_breakpoint', '768', 'number', 'Mobile menu breakpoint in pixels'),
       ('menu_sticky', 'true', 'boolean', 'Enable sticky menu'),
       ('menu_show_search', 'true', 'boolean', 'Show search in menu'),
       ('menu_cta_text', 'ЗАПИСАТЬСЯ НА ПРИЕМ', 'text', 'Call to action button text'),
       ('menu_cta_url', '#contact', 'text', 'Call to action button URL');

-- Initial menu items
INSERT INTO menu_items (title, url, position, is_active)
VALUES ('О НАС', 'index', 1, 1),
       ('ТРЕНИНГ НАВЫКОВ', 'training', 2, 1),
       ('СПЕЦИАЛИСТЫ', 'specialists', 3, 1),
       ('ЧТО ТАКОЕ ДБТ', 'about-dbt', 4, 1),
       ('КОНТАКТЫ', 'contact', 5, 1);


-- Initial categories
INSERT INTO menu_categories (name, slug, description, show_in_menu, menu_position)
VALUES ('Блог', 'blog', 'Статьи и новости', 1, 6),
       ('Услуги', 'services', 'Наши услуги', 1, 7);
