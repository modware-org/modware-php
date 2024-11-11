CREATE TABLE IF NOT EXISTS contact_info (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    phone TEXT,
    email TEXT,
    address TEXT
);

-- Insert default record if not exists
INSERT OR IGNORE INTO contact_info (id, phone, email, address) 
VALUES (1, '+48 123 456 789', 'kontakt@example.com', 'ul. Przykładowa 123\n00-000 Warszawa');
