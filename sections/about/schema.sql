-- Certification details table schema and data
CREATE TABLE IF NOT EXISTS certification_details
(
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    title            TEXT NOT NULL,
    description      TEXT,
    certification_date TEXT,
    expiry_date      TEXT,
    issuing_body     TEXT,
    certificate_number TEXT,
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Certification instructors table schema and data
CREATE TABLE IF NOT EXISTS certification_instructors
(
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    certification_id INTEGER NOT NULL,
    name             TEXT    NOT NULL,
    title            TEXT,
    sort_order       INTEGER  DEFAULT 0,
    created_at       DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (certification_id) REFERENCES certification_details (id)
);
