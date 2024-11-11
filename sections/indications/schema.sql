-- DBT indications table schema
CREATE TABLE IF NOT EXISTS dbt_indications
(
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    title       TEXT NOT NULL,
    description TEXT,
    sort_order  INTEGER  DEFAULT 0,
    is_active   BOOLEAN  DEFAULT 1,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);
