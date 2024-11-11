-- Create site record for localhost
INSERT INTO sites (name, domain) VALUES ('Visual Website Builder', 'devop');


-- Insert default language
INSERT OR IGNORE INTO languages (code, name) VALUES ('en', 'English');
INSERT OR IGNORE INTO languages (code, name) VALUES ('pl', 'Polish');
