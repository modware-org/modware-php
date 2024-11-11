# Components

This directory contains reusable UI components that can be integrated into any section or page of the website.

## Available Components

### Files Component
Location: `/components/files`
- File management system
- Secure file uploads
- File type validation
- Access control
- Admin interface

### Gallery Component
Location: `/components/gallery`
- Image gallery system
- Lightbox integration
- Image optimization
- Thumbnail generation
- Responsive design

### Upload Component
Location: `/components/upload`
- Drag-and-drop uploads
- Progress tracking
- Multi-file support
- File type validation
- Security measures

## Component Structure
```
component_name/
├── admin.php       # Admin interface
├── api.php         # API endpoints
├── html.php        # Frontend template
├── script.js       # JavaScript functionality
└── style.css       # Component styles
```

## Usage

### 1. HTML Integration
```php
require_once 'components/gallery/html.php';
```

### 2. JavaScript Integration
```javascript
import { Gallery } from 'components/gallery/script.js';
```

### 3. API Usage
```bash
# Test API endpoint
curl -X POST http://localhost/api/gallery/upload \
  -H "Authorization: Bearer ${TOKEN}" \
  -F "file=@image.jpg"
```

## Creating New Components

1. Create component directory:
```bash
mkdir components/your_component_name
```

2. Create required files:
```bash
touch admin.php api.php html.php script.js style.css
```

3. Implement required functionality:
- Frontend template (html.php)
- JavaScript functionality (script.js)
- Styles (style.css)
- Admin interface (admin.php)
- API endpoints (api.php)

## Testing

Each component includes API tests:
```bash
# Run component tests
./test.sh components/component_name
```

## API Authentication

Components use JWT authentication for API endpoints:
```php
// Generate token
$token = JWT::generate([
    'user_id' => 1,
    'exp' => time() + 3600
]);
```

## Best Practices

1. Keep components self-contained
2. Follow single responsibility principle
3. Use consistent naming conventions
4. Include proper documentation
5. Write unit tests
6. Implement error handling
7. Follow security best practices
8. Maintain backwards compatibility

## Security Features

- File type validation
- Size limitations
- Malware scanning
- Access control
- CSRF protection
- XSS prevention

## Integration Examples

### Gallery Component
```php
// PHP usage
$gallery = new Gallery([
    'container' => '#gallery',
    'thumbnailSize' => 150,
    'lightbox' => true
]);

// Display gallery
$gallery->render();
```

### Upload Component
```php
// PHP usage
$upload = new Upload([
    'maxSize' => '10MB',
    'allowedTypes' => ['image/jpeg', 'image/png'],
    'destination' => 'uploads/'
]);

// Handle upload
$upload->handleRequest();
```

### Files Component
```php
// PHP usage
$files = new Files([
    'baseDir' => 'documents/',
    'access' => 'private'
]);

// List files
$files->listFiles();
```

## Troubleshooting

Common issues and solutions:
1. Upload failures
   - Check file permissions
   - Verify size limits
   - Check allowed types

2. API errors
   - Verify authentication
   - Check request format
   - Review error logs

3. Display issues
   - Clear cache
   - Check dependencies
   - Verify paths
