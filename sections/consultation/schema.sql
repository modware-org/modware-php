-- Consultation section database schema

-- Consultation settings table
CREATE TABLE IF NOT EXISTS consultation_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    description TEXT,
    booking_info TEXT,
    active INTEGER DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Consultation types table
CREATE TABLE IF NOT EXISTS consultation_types (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    duration INTEGER NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    active INTEGER DEFAULT 1,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact methods table
CREATE TABLE IF NOT EXISTS contact_methods (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    link VARCHAR(255) NOT NULL,
    active INTEGER DEFAULT 1,
    show_in_consultation INTEGER DEFAULT 1,
    display_order INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
