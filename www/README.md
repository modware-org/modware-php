# Website Projects

This directory contains website-specific configurations, data, and customizations for different domains.

## Directory Structure
```
www/
└── localhost/           # Example website
    ├── pages/          # Page-specific data
    │   ├── about-dbt.sql
    │   ├── consultations.sql
    │   ├── contact.sql
    │   ├── home.sql
    │   ├── specialists.sql
    │   ├── team.sql
    │   └── training.sql
    ├── sections/       # Section configurations
    │   └── {section}/
    │       └── data.sql
    └── site.sql       # Site-wide configuration
```

## Website Structure

Each website follows the structure:
```
{domain}/
├── pages/            # Page configurations
├── sections/         # Section data
└── site.sql         # Global settings
```

## Usage

### 1. Creating New Website
```sql
-- site.sql
INSERT INTO sites (domain, name, status)
VALUES ('example.com', 'Example Site', 'active');
```

### 2. Page Configuration
```sql
-- pages/home.sql
INSERT INTO pages (site_id, slug, title, meta_description)
VALUES (1, 'home', 'Welcome', 'Site description');
```

### 3. Section Data
```sql
-- sections/hero/data.sql
INSERT INTO section_content (page_id, section_name, content)
VALUES (1, 'hero', '{"title": "Welcome", "subtitle": "..."}');
```

## Configuration

### Site Settings
- Domain configuration
- Global parameters
- Theme settings
- Feature flags

### Page Settings
- SEO metadata
- Section ordering
- Content structure
- Navigation

### Section Data
- Content
- Styling
- Behavior
- Dependencies

## Best Practices

1. Organization
   - Use consistent naming
   - Maintain clear structure
   - Document configurations
   - Version control

2. Data Management
   - Regular backups
   - Data validation
   - Clean structure
   - Clear dependencies

3. Security
   - Access control
   - Data validation
   - Error handling
   - Logging

4. Performance
   - Query optimization
   - Cache management
   - Resource efficiency
   - Load balancing

## Adding New Website

1. Create website directory:
```bash
mkdir www/example.com
```

2. Create required structure:
```bash
mkdir -p www/example.com/{pages,sections}
touch www/example.com/site.sql
```

3. Configure site settings:
```sql
-- site.sql
INSERT INTO sites (domain, name, status)
VALUES ('example.com', 'Example Site', 'active');
```

4. Add pages and sections as needed

## Database Structure

### Sites Table
```sql
CREATE TABLE sites (
    id INTEGER PRIMARY KEY,
    domain VARCHAR(255),
    name VARCHAR(255),
    status VARCHAR(50)
);
```

### Pages Table
```sql
CREATE TABLE pages (
    id INTEGER PRIMARY KEY,
    site_id INTEGER,
    slug VARCHAR(255),
    title VARCHAR(255),
    meta_description TEXT
);
```

### Section Content Table
```sql
CREATE TABLE section_content (
    id INTEGER PRIMARY KEY,
    page_id INTEGER,
    section_name VARCHAR(255),
    content JSON
);
```

## Troubleshooting

Common issues and solutions:

1. Configuration Issues
   - Check SQL syntax
   - Verify file permissions
   - Review site settings
   - Check dependencies

2. Content Problems
   - Validate data format
   - Check references
   - Review section order
   - Verify content

3. Performance Issues
   - Optimize queries
   - Check indexes
   - Review caching
   - Monitor resources

## Development

### Local Development
1. Use localhost configuration
2. Test with sample data
3. Verify functionality
4. Deploy to production

### Production Deployment
1. Create domain directory
2. Configure settings
3. Import content
4. Verify functionality

## Maintenance

Regular tasks:
1. Backup data
2. Update configurations
3. Optimize database
4. Clean unused data
