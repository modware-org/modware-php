
INSERT INTO team_members (name, sort_order) VALUES
('Daria Dymont', 1),
('Ekaterina Khisamieva', 2),
('Anastasia Nikolaeva', 3),
('Alsou Fazullina', 4),
('Olga Sapietta', 5),
('Liudmila Grishanova', 6);


INSERT INTO certification_details (institution, program, part1_dates, part2_dates, certificate_file) VALUES
('Behavioral Tech Institute', 'DBT Intensive Training', 
 'March 1-3, 2024 & April 5-7, 2024', 
 'September 20-22, 2024 & October 11-13, 2024',
 'Cert_Unity_241108_073542.pdf');

INSERT INTO certification_instructors (certification_id, name, title, sort_order) VALUES
(1, 'André Ivanoff', 'PhD', 1),
(1, 'Dmitry Pushkarev', 'MD, PhD', 2);

-- About section specific configuration
INSERT INTO config (name, value, type, description) VALUES
('about_cert_image', '/img/unitydbt-cert.png', 'text', 'Certification image path'),
('about_cert_image_alt', 'Сертификат DBT Intensive Training от Behavioral Tech Institute', 'text', 'Certification image alt text');
