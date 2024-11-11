-- Fix truncated Polish team_full title
UPDATE section_translations 
SET translation = 'Nasz Zespół' 
WHERE section_name = 'team_full' 
  AND field_name = 'title' 
  AND language_id = (SELECT id FROM languages WHERE code = 'pl');
