-- Indications section configuration
INSERT OR
REPLACE INTO sections (name, title, description, type, sort_order)
VALUES
    ('indications', 'Indications Section', 'DBT therapy indications', 'indications', 4);

-- Initial DBT indications data
INSERT INTO dbt_indications (title, description, sort_order)
VALUES ('Трудности с регуляцией эмоционального состояния',
        'Помощь в управлении эмоциями и развитии навыков эмоциональной регуляции.', 1),
       ('Пограничное расстройство личности',
        'Эффективная терапия для людей с ПРЛ, помогающая стабилизировать эмоции и улучшить межличностные отношения.',
        2),
       ('Биполярное аффективное расстройство (БАР)',
        'Поддержка в управлении симптомами БАР и предотвращении рецидивов.', 3),
       ('Расстройства пищевого поведения',
        'Работа над нормализацией пищевого поведения и связанных с ним эмоциональных трудностей.', 4),
       ('Синдром дефицита внимания с гиперактивностью (СДВГ)',
        'Развитие навыков концентрации и управления импульсивностью.', 5),
       ('Посттравматическое стрессовое расстройство',
        'Помощь в преодолении последствий травмы и развитии устойчивости.', 6),
       ('Химические и нехимические зависимости', 'Работа с зависимым поведением и развитие здоровых копинг-стратегий.',
        7);

-- Indications section specific configuration
INSERT INTO config (name, value, type, description)
VALUES ('indications_title', 'Кому показана комплексная ДБТ программа', 'text', 'Indications section main title'),
       ('indications_subtitle', 'Диалектическая поведенческая терапия эффективна при следующих состояниях:', 'text',
        'Indications section subtitle'),
       ('indications_show_cta', 'true', 'boolean', 'Show call to action button'),
       ('indications_cta_text', 'Записаться на консультацию', 'text', 'Call to action button text'),
       ('indications_cta_link', '#contact', 'text', 'Call to action button link');
