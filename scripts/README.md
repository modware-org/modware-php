# Utility Scripts

This directory contains utility scripts for various system operations, maintenance, and automation tasks.

## Available Scripts

### User Management
- `create_user.php` - Create admin users
- `create_team_page.php` - Generate team pages

## Script Details

### create_user.php
Creates admin users with proper authentication:
```bash
php scripts/create_user.php
```

Options:
- `--username` - Admin username
- `--password` - User password
- `--email` - User email
- `--role` - User role (default: admin)

### create_team_page.php
Generates team member pages:
```bash
php scripts/create_team_page.php
```

Features:
- Creates team member profiles
- Generates necessary SQL
- Sets up page routing
- Configures sections

## Usage Guidelines

### 1. User Creation
```bash
# Create admin user
php scripts/create_user.php --username=admin --password=secure123 --email=admin@example.com

# Create editor user
php scripts/create_user.php --username=editor --password=secure123 --role=editor
```

### 2. Team Page Creation
```bash
# Generate team page
php scripts/create_team_page.php --template=default

# Generate with custom layout
php scripts/create_team_page.php --template=custom --layout=grid
```

## Best Practices

1. Security
   - Use strong passwords
   - Validate inputs
   - Handle errors
   - Log operations

2. Performance
   - Optimize operations
   - Handle large datasets
   - Use transactions
   - Clean up resources

3. Maintenance
   - Document changes
   - Version control
   - Regular updates
   - Error logging

## Creating New Scripts

1. Script Structure
```php
#!/usr/bin/env php
<?php

// Include required files
require_once __DIR__ . '/../config/env.php';
require_once __DIR__ . '/../config/Database.php';

// Parse arguments
$options = getopt('', ['param:']);

// Validate inputs
if (!isset($options['param'])) {
    die("Missing required parameter\n");
}

// Perform operations
try {
    // Script logic here
} catch (Exception $e) {
    die("Error: " . $e->getMessage() . "\n");
}
```

2. Add Documentation
- Script purpose
- Required parameters
- Usage examples
- Error handling

3. Add Testing
- Create test cases
- Verify functionality
- Test edge cases
- Document tests

## Error Handling

Scripts should handle common errors:
```php
try {
    // Operation
} catch (DatabaseException $e) {
    die("Database error: " . $e->getMessage() . "\n");
} catch (ValidationException $e) {
    die("Validation error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("Unexpected error: " . $e->getMessage() . "\n");
}
```

## Logging

Implement proper logging:
```php
function log_operation($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents(
        __DIR__ . '/../logs/scripts.log',
        "[$timestamp] [$level] $message\n",
        FILE_APPEND
    );
}
```

## Testing

Test scripts using:
```bash
./test.sh scripts/script_name.php
```

## Automation

Scripts can be automated using cron:
```bash
# Daily user cleanup
0 0 * * * /usr/bin/php /path/to/scripts/cleanup_users.php

# Hourly backup
0 * * * * /usr/bin/php /path/to/scripts/backup.php
```

## Troubleshooting

Common issues and solutions:

1. Permission Issues
   - Check file permissions
   - Verify user rights
   - Review directory access

2. Database Problems
   - Check connection
   - Verify credentials
   - Review queries

3. Input Errors
   - Validate parameters
   - Check data format
   - Handle missing inputs

## Development Guidelines

1. Code Style
   - Follow PSR standards
   - Use meaningful names
   - Add comments
   - Document changes

2. Security
   - Sanitize inputs
   - Validate data
   - Use prepared statements
   - Handle sensitive data

3. Performance
   - Optimize operations
   - Use transactions
   - Handle resources
   - Clean up properly
