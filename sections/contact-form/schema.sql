CREATE TABLE IF NOT EXISTS contact_form_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    is_active INTEGER DEFAULT 1,
    recipient_email TEXT NOT NULL,
    email_subject TEXT NOT NULL,
    success_message TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS contact_form_messages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL,
    subject TEXT NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Insert default settings if not exists
INSERT OR IGNORE INTO contact_form_settings (id, recipient_email, email_subject, success_message) 
VALUES (1, 'admin@example.com', 'New contact form message from {name}: {subject}', 'Thank you for your message. We will contact you soon.');
