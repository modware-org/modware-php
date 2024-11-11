-- Menu items table schema
CREATE TABLE menu_items
(
    id         INTEGER PRIMARY KEY AUTOINCREMENT,
    title      TEXT NOT NULL,
    url        TEXT NOT NULL,
    parent_id  INTEGER  DEFAULT NULL,
    position   INTEGER  DEFAULT 0,
    is_active  BOOLEAN  DEFAULT 1,
    target     TEXT     DEFAULT '_self',
    icon_class TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menu_items (id)
);

-- Menu categories table
CREATE TABLE menu_categories
(
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    name          TEXT NOT NULL,
    slug          TEXT NOT NULL UNIQUE,
    description   TEXT,
    parent_id     INTEGER  DEFAULT NULL,
    sort_order    INTEGER  DEFAULT 0,
    is_active     BOOLEAN  DEFAULT 1,
    show_in_menu  BOOLEAN  DEFAULT 0,
    menu_position INTEGER  DEFAULT 0,
    created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menu_categories (id)
);
