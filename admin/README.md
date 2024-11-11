# Admin Panel

This directory contains the administration panel for managing website content, configurations, and integrations.

## Features

- Secure authentication system
- Content management
- Section configuration
- Component management
- Integration settings
- SEO optimization
- Media management
- User management
- System diagnostics

## Directory Structure
```
admin/
├── api/                # Admin API endpoints
│   ├── Api.php        # API handler
│   └── endpoints/     # API endpoint implementations
├── config/            # Admin configurations
│   ├── Database.php  # Database configuration
│   └── env.php       # Environment settings
├── pages/            # Admin panel pages
│   ├── components.php   # Component management
│   ├── content.php     # Content management
│   ├── dashboard.php   # Main dashboard
│   ├── diagnostics.php # System diagnostics
│   ├── integrations.php # Integration settings
│   ├── media.php       # Media management
│   ├── menu.php        # Menu configuration
│   ├── pages.php       # Page management
│   ├── sections.php    # Section management
│   └── seo.php         # SEO settings
├── scripts/          # Utility scripts
│   ├── create_user.php # User creation
│   └── hash_password.php # Password hashing
├── auth.php          # Authentication handler
├── index.php         # Main entry point
├── login.php         # Login page
├── logout.php        # Logout handler
└── schema.sql        # Admin database schema
```

## Installation

1. Set up environment:
```bash
cp .env.example .env
```

2. Configure database:
```bash
php install.php
```

3. Create admin user:
```bash
php scripts/create_user.php
```

## Authentication

The admin panel uses secure authentication:
- Password hashing with bcrypt
- Session management
- CSRF protection
- Brute force prevention
- IP-based security

## Features Overview

### Content Management
- Page creation and editing
- Section management
- Media uploads
- Content versioning

### Configuration
- Site settings
- Menu structure
- SEO parameters
- Integration settings

### User Management
- User creation
- Role assignment
- Permission management
- Activity logging

### System Tools
- Diagnostics
- Error logging
- Cache management
- Backup tools

## API Integration

Admin panel provides REST API endpoints:
```
GET    /admin/api/sections
POST   /admin/api/sections
PUT    /admin/api/sections/{id}
DELETE /admin/api/sections/{id}
```

## Security Features

1. Authentication
   - Secure password hashing
   - Session management
   - 2FA support

2. Authorization
   - Role-based access
   - Permission system
   - Action logging

3. Protection
   - CSRF tokens
   - XSS prevention
   - SQL injection protection
   - Rate limiting

## Database Structure

Key tables:
- admin_users
- admin_roles
- admin_permissions
- admin_sessions
- admin_logs

## Best Practices

1. Security
   - Regular password updates
   - Session timeouts
   - Access logging
   - Security audits

2. Performance
   - Cache management
   - Query optimization
   - Asset minification
   - Load balancing

3. Maintenance
   - Regular backups
   - Log rotation
   - Update management
   - Error monitoring

## Troubleshooting

Common issues and solutions:

1. Login Issues
   - Check credentials
   - Clear session data
   - Verify database connection

2. Permission Problems
   - Check user roles
   - Verify permissions
   - Review access logs

3. Database Errors
   - Check connection settings
   - Verify schema
   - Review error logs

## Development

### Adding New Features
1. Create new page in pages/
2. Add route to index.php
3. Implement API endpoint if needed
4. Add necessary database tables
5. Update documentation

### Testing
```bash
./test.sh admin/
```

## Customization

The admin panel can be customized through:
- Custom CSS themes
- Module extensions
- API integrations
- Custom pages
