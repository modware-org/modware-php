-- Menu items
INSERT INTO menu_items (title, url, position, is_active) VALUES
                                                             ('О НАС', 'home', 1, 1),
                                                             ('ТРЕНИНГ НАВЫКОВ', 'training', 2, 1),
                                                             ('СПЕЦИАЛИСТЫ', 'specialists', 3, 1),
                                                             ('ЧТО ТАКОЕ ДБТ', 'about-dbt', 4, 1),
                                                             ('КОНТАКТЫ', 'contact', 5, 1);

-- Menu categories
INSERT INTO menu_categories (name, slug, description, show_in_menu, menu_position) VALUES
                                                                                       ('Блог', 'blog', 'Статьи и новости', 1, 6),
                                                                                       ('Услуги', 'services', 'Наши услуги', 1, 7);
