-- Add Visualizer Page
INSERT INTO pages (
    site_id, 
    title, 
    slug, 
    meta_description, 
    meta_keywords, 
    status
) VALUES (
    1, 
    'File Visualizer', 
    'visualizer', 
    'Visualize and manage uploaded files', 
    'files, upload, visualization', 
    'published'
);

-- Add Visualizer Section
INSERT INTO sections (
    page_id, 
    name, 
    title, 
    description, 
    type, 
    sort_order, 
    position
) VALUES (
    (SELECT id FROM pages WHERE slug = 'visualizer' AND site_id = 1),
    'visualizer', 
    'File Visualizer', 
    'Section to visualize and manage uploaded files', 
    'files', 
    1, 
    0
);
