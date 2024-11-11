CREATE TABLE IF NOT EXISTS map_settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    is_active INTEGER DEFAULT 1,
    latitude REAL NOT NULL,
    longitude REAL NOT NULL,
    zoom INTEGER NOT NULL,
    marker_title TEXT NOT NULL,
    api_key TEXT NOT NULL
);

-- Insert default settings if not exists
INSERT OR IGNORE INTO map_settings (id, latitude, longitude, zoom, marker_title, api_key) 
VALUES (1, 52.2317604, 21.0172998, 15, 'Our Location', '');
