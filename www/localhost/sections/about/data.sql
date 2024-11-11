-- Site configuration
INSERT INTO config (name, value, type, description) VALUES
    ('site_name', 'Unity DBT', 'text', 'Website name');

-- Certification details
INSERT INTO certification_details (
    title,
    description,
    certification_date,
    expiry_date,
    issuing_body,
    certificate_number
) VALUES (
    'DBT-Linehan Board Certification',
    'Certified DBT program meeting highest standards of DBT treatment delivery',
    '2023-01-15',
    '2026-01-15',
    'DBT-Linehan Board of Certification',
    'DBT-2023-1234'
    );

-- Certification instructors
INSERT INTO certification_instructors (certification_id, name, title, sort_order) VALUES
                                                                                      (1, 'André Ivanoff', 'PhD', 1),
                                                                                      (1, 'Dmitry Pushkarev', 'MD, PhD', 2);

-- Certification configuration
INSERT INTO config (name, value, type, description) VALUES
                                                        ('about_cert_image', '/img/unitydbt-cert.png', 'text', 'Certification image path'),
                                                        ('about_cert_image_alt', 'Сертификат DBT Intensive Training от Behavioral Tech Institute', 'text', 'Certification image alt text');
