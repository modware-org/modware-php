# API Tests

This directory contains tests for the DBT Unity API endpoints. The tests use curl to verify the functionality of all API endpoints.

## Prerequisites

- Bash shell
- curl installed
- PHP server running
- SQLite database properly configured
- Admin user created in the database

## Running the Tests

1. Make sure your PHP server is running and the API is accessible
2. Update the `API_URL` in `api_tests.sh` if your server URL is different
3. Run the tests:

```bash
./api_tests.sh
```

## Manual Testing with curl

You can also test individual endpoints manually using curl. Here are some examples:

### Authentication

```bash
# Login and get token
curl -X POST \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"admin123"}' \
  http://localhost:8007/api/auth
```

### Content Management

```bash
# Get all content
curl -X GET \
  -H "Authorization: Bearer your_token" \
  http://localhost/modware/app/php/api/content

# Create new content
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{
    "title": "Test Page",
    "slug": "test-page",
    "content": "Page content here",
    "type": "page",
    "status": "draft"
  }' \
  http://localhost/modware/app/php/api/content
```

### Menu Management

```bash
# Get menu items
curl -X GET \
  -H "Authorization: Bearer your_token" \
  http://localhost/modware/app/php/api/menu

# Create menu item
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{
    "title": "New Menu Item",
    "url": "/test-page",
    "position": 1
  }' \
  http://localhost/modware/app/php/api/menu
```

### SEO Settings

```bash
# Get SEO settings for a page
curl -X GET \
  -H "Authorization: Bearer your_token" \
  http://localhost/modware/app/php/api/seo?page_id=1

# Update SEO settings
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{
    "page_id": 1,
    "meta_title": "Page Title",
    "meta_description": "Page description"
  }' \
  http://localhost/modware/app/php/api/seo
```

### Configuration

```bash
# Get all configuration
curl -X GET \
  -H "Authorization: Bearer your_token" \
  http://localhost/modware/app/php/api/config

# Update configuration
curl -X POST \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer your_token" \
  -d '{
    "name": "site_name",
    "value": "New Site Name"
  }' \
  http://localhost/modware/app/php/api/config
```

### Media Upload

```bash
# Upload file
curl -X POST \
  -H "Authorization: Bearer your_token" \
  -F "files[]=@path/to/file.jpg" \
  http://localhost/modware/app/php/api/media/upload

# Get media list
curl -X GET \
  -H "Authorization: Bearer your_token" \
  http://localhost/modware/app/php/api/media
```

## Test Results

The test script will show:
- ✓ Green checkmarks for successful tests
- ✗ Red X's for failed tests with error messages
- Detailed response from each endpoint

## Troubleshooting

1. If authentication fails:
   - Verify admin user exists in database
   - Check password is correct
   - Ensure database connection is working

2. If endpoints return 404:
   - Check API_URL is correct
   - Verify .htaccess is properly configured
   - Check PHP server is running

3. If permission errors:
   - Check file permissions in uploads directory
   - Verify API token is being sent correctly
   - Ensure database user has proper permissions
