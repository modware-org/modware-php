-- Get the site_id for the Visual Website Builder
WITH visualizer_site AS (
    SELECT id FROM sites WHERE domain = 'devop' LIMIT 1
)
INSERT INTO pages (
    site_id, 
    title, 
    slug, 
    meta_description, 
    status
) SELECT 
    id, 
    'Visualizer', 
    'visualizer', 
    'Visualizer page for data exploration and visualization', 
    'published'
FROM visualizer_site;

-- Add a section for the visualizer page
WITH visualizer_page AS (
    SELECT id FROM pages WHERE slug = 'visualizer' LIMIT 1
)
INSERT INTO sections (
    page_id, 
    name, 
    title, 
    type, 
    position
) SELECT 
    id, 
    'visualizer', 
    'Visualizer', 
    'visualizer', 
    0
FROM visualizer_page;
