# Modules

This directory contains standalone feature modules that can be integrated into any page of the website.

## Available Modules

### Blog Module
Location: `/modules/blog`
- Full-featured blog system
- Post management through admin panel
- Category and tag support
- SEO optimization
- Schema: `blog/schema.sql`

### RSS Module
Location: `/modules/rss`
- Automatic RSS feed generation
- Configurable feed settings
- Integration with blog and content pages
- Schema: `rss/schema.sql`

### Sitemap Module
Location: `/modules/sitemap`
- Dynamic XML sitemap generation
- SEO-friendly URL structure
- Automatic page discovery
- Schema: `sitemap/schema.sql`

## Module Structure
Each module follows a standardized structure:
```
module_name/
├── admin.php       # Admin interface for content management
├── html.php        # Frontend template
├── query.php       # Database queries
├── schema.sql      # Database structure
├── script.js       # JavaScript functionality
└── style.css       # Module styles
```

## Integration Methods

### 1. Direct Inclusion
```php
require_once 'modules/blog/html.php';
```

### 2. Dynamic Loading via SQL
Modules can be loaded dynamically based on SQL configuration:
```sql
INSERT INTO modules (name, enabled, position) VALUES ('blog', 1, 1);
```

### 3. API Integration
Modules can be accessed via API endpoints:
```
GET /api/modules/blog/posts
GET /api/modules/rss/feed
GET /api/modules/sitemap/xml
```

## Testing
Each module includes test files and can be tested using:
```bash
./test.sh modules/module_name
```

## Creating New Modules

1. Create module directory:
```bash
mkdir modules/your_module_name
```

2. Create required files:
- admin.php - Admin interface
- html.php - Frontend template
- query.php - Database queries
- schema.sql - Database structure
- script.js - JavaScript functionality
- style.css - Module styles

3. Add database schema in schema.sql
4. Implement admin interface
5. Create frontend templates
6. Add to module configuration in database

## Best Practices

1. Keep modules independent and self-contained
2. Follow consistent naming conventions
3. Include proper documentation
4. Write unit tests
5. Use prepared statements for database queries
6. Implement proper error handling
7. Follow security best practices
8. Maintain backwards compatibility
