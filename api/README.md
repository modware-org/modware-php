# API Documentation

This directory contains the API implementation for accessing and managing website content and functionality.

## Directory Structure
```
api/
├── endpoints/           # API endpoint handlers
│   ├── auth.php        # Authentication
│   ├── menu.php        # Menu management
│   ├── pages.php       # Page management
│   ├── sections.php    # Section management
│   ├── seo.php         # SEO settings
│   └── sitemap.php     # Sitemap generation
├── tests/              # API tests
│   ├── api_tests.sh    # Test runner
│   └── README.md       # Test documentation
├── .htaccess          # Apache configuration
├── Api.php            # Core API class
└── index.php          # API entry point
```

## Endpoints

### Authentication
```
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh
```

### Pages
```
GET    /api/pages
POST   /api/pages
GET    /api/pages/{id}
PUT    /api/pages/{id}
DELETE /api/pages/{id}
```

### Sections
```
GET    /api/sections
POST   /api/sections
GET    /api/sections/{id}
PUT    /api/sections/{id}
DELETE /api/sections/{id}
```

### Menu
```
GET    /api/menu
POST   /api/menu
PUT    /api/menu/{id}
DELETE /api/menu/{id}
```

### SEO
```
GET    /api/seo
PUT    /api/seo
GET    /api/seo/{page_id}
PUT    /api/seo/{page_id}
```

## Authentication

The API uses JWT for authentication:

```php
// Request token
POST /api/auth/login
{
    "username": "admin",
    "password": "secure123"
}

// Response
{
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "expires": 3600
}
```

Use token in requests:
```bash
curl -H "Authorization: Bearer eyJhbGciOiJIUzI1NiIs..." \
     https://example.com/api/pages
```

## Response Format

All responses follow the format:
```json
{
    "status": "success|error",
    "data": {},
    "message": "Optional message",
    "errors": []
}
```

## Error Handling

HTTP status codes:
- 200: Success
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden
- 404: Not Found
- 500: Server Error

Error response:
```json
{
    "status": "error",
    "message": "Error description",
    "errors": [
        {
            "field": "username",
            "message": "Username is required"
        }
    ]
}
```

## Rate Limiting

- 1000 requests per hour per IP
- 100 requests per minute per token
- Headers include rate limit info:
  - X-RateLimit-Limit
  - X-RateLimit-Remaining
  - X-RateLimit-Reset

## Examples

### Fetch Pages
```bash
curl -H "Authorization: Bearer ${TOKEN}" \
     https://example.com/api/pages
```

### Create Section
```bash
curl -X POST \
     -H "Authorization: Bearer ${TOKEN}" \
     -H "Content-Type: application/json" \
     -d '{"name": "hero", "content": {}}' \
     https://example.com/api/sections
```

### Update SEO
```bash
curl -X PUT \
     -H "Authorization: Bearer ${TOKEN}" \
     -H "Content-Type: application/json" \
     -d '{"title": "New Title", "description": "New description"}' \
     https://example.com/api/seo/1
```

## Testing

Run API tests:
```bash
cd api/tests
./api_tests.sh
```

## Development

### Adding New Endpoint
1. Create endpoint file in endpoints/
2. Add route in index.php
3. Implement handler in Api.php
4. Add tests
5. Update documentation

### Security Checklist
- Validate all inputs
- Use prepared statements
- Implement rate limiting
- Check authentication
- Validate permissions
- Sanitize outputs
- Log access attempts

## Best Practices

1. API Design
   - Use REST conventions
   - Version endpoints
   - Document changes
   - Maintain backwards compatibility

2. Security
   - Validate inputs
   - Sanitize outputs
   - Use HTTPS
   - Implement rate limiting
   - Log access

3. Performance
   - Cache responses
   - Optimize queries
   - Compress data
   - Use pagination

4. Documentation
   - Keep docs updated
   - Include examples
   - Document errors
   - Version changes

## Troubleshooting

Common issues and solutions:

1. Authentication Issues
   - Check token validity
   - Verify credentials
   - Check permissions
   - Review logs

2. Rate Limiting
   - Monitor usage
   - Adjust limits
   - Handle errors
   - Implement caching

3. Performance
   - Optimize queries
   - Use caching
   - Monitor resources
   - Profile endpoints
