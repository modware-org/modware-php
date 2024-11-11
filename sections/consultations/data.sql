-- Consultation types
CREATE TABLE IF NOT EXISTS consultation_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(255),
    duration VARCHAR(50),
    price VARCHAR(50),
    features TEXT, -- JSON array of features
    booking_url VARCHAR(255),
    is_active INTEGER DEFAULT 1,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert consultation types
INSERT OR IGNORE INTO consultation_types (title, description, icon, duration, price, features, booking_url, is_active, sort_order) VALUES
    ('Первичная консультация', 
     'Знакомство с терапевтом, обсуждение проблем и целей терапии, определение подходящего формата работы',
     '/img/consultations/initial.svg',
     '50 минут',
     '5000 ₽',
     '["Оценка текущего состояния", "Определение целей терапии", "Рекомендации по формату работы", "План дальнейших действий"]',
     '/booking/initial',
     1, 1),
    
    ('Индивидуальная терапия',
     'Регулярные сессии с DBT терапевтом для работы над личными целями и развитием навыков',
     '/img/consultations/individual.svg',
     '50 минут',
     '4000 ₽',
     '["Работа с эмоциональной регуляцией", "Развитие навыков осознанности", "Улучшение межличностных отношений", "Повышение стрессоустойчивости"]',
     '/booking/individual',
     1, 2),
    
    ('Групповой тренинг навыков',
     'Еженедельные групповые занятия по обучению DBT навыкам в мини-группах',
     '/img/consultations/group.svg',
     '90 минут',
     '3000 ₽',
     '["Обучение DBT навыкам", "Практика в группе", "Домашние задания", "Поддержка участников группы"]',
     '/booking/group',
     1, 3),
    
    ('Семейная консультация',
     'Работа с семьей или близкими для улучшения взаимопонимания и поддержки',
     '/img/consultations/family.svg',
     '80 минут',
     '6000 ₽',
     '["Улучшение коммуникации", "Работа с конфликтами", "Обучение поддержке", "Совместное планирование"]',
     '/booking/family',
     1, 4);

-- Add consultation note to config
INSERT OR IGNORE INTO config (name, value, type, description) VALUES
    ('consultations_note',
     'Все консультации проводятся сертифицированными DBT специалистами. Возможно проведение онлайн консультаций.',
     'text',
     'Note displayed at the bottom of consultations section');
