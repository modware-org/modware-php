# Sections

This directory contains modular page sections that can be dynamically loaded and arranged on any page of the website.

## Available Sections

### Core Sections
- `about/` - About page content
- `consultation/` - Consultation booking system
- `consultations/` - Consultations listing
- `contact-form/` - Contact form with SMTP integration
- `education/` - Educational content display
- `expertise/` - Expertise and specializations
- `footer/` - Site footer
- `hero/` - Hero/banner section
- `menu/` - Navigation menu
- `meta/` - SEO and metadata
- `modules/` - Module display section
- `specialist-profile/` - Specialist profile pages
- `team/` - Team members listing
- `team-full/` - Detailed team presentation
- `training-intro/` - Training introduction

## Section Structure
Each section follows a standardized structure:
```
section_name/
├── admin.php       # Admin interface
├── html.php        # Frontend template
├── query.php       # Database queries
├── style.css       # Section styles
├── script.js       # JavaScript (if needed)
├── data.sql        # Initial data
└── schema.sql      # Database structure
```

## Usage

### 1. Dynamic Loading
Sections are loaded dynamically based on SQL configuration:
```sql
INSERT INTO page_sections (page_id, section_name, position, enabled)
VALUES (1, 'hero', 1, 1);
```

### 2. Direct Inclusion
```php
require_once 'sections/hero/html.php';
```

## Creating New Sections

1. Create section directory:
```bash
mkdir sections/your_section_name
```

2. Create required files:
```bash
touch html.php query.php style.css admin.php schema.sql
```

3. Implement required functionality:
- Frontend template (html.php)
- Database queries (query.php)
- Styles (style.css)
- Admin interface (admin.php)
- Database schema (schema.sql)

4. Add to page configuration in database

## Best Practices

1. Keep sections independent
2. Use consistent naming conventions
3. Follow mobile-first approach
4. Implement proper error handling
5. Use prepared statements for queries
6. Include responsive design
7. Optimize assets
8. Document dependencies

## Testing

Test individual sections using:
```bash
./test.sh sections/section_name
```

## Admin Panel Integration

Each section's admin.php is automatically loaded in the admin panel under:
```
/admin/index.php?page=sections
```

## Database Structure

Sections use the following tables:
- page_sections: Section configuration per page
- section_content: Content for each section
- section_meta: Additional metadata
- section_media: Media files used in sections

## Configuration

Sections can be configured through:
1. Admin panel interface
2. Direct database manipulation
3. API endpoints
4. Configuration files

## Security

- All user input is sanitized
- SQL injection prevention
- XSS protection
- CSRF protection
- File upload validation

## Troubleshooting

Common issues and solutions:
1. Section not loading
   - Check database configuration
   - Verify file permissions
   - Check error logs

2. Styling issues
   - Clear cache
   - Check CSS specificity
   - Verify media queries

3. Database errors
   - Check schema installation
   - Verify query syntax
   - Check database connections
