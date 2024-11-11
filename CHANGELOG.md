# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- Modular section architecture with independent HTML, CSS, JS, and SQL components
- Admin panel with password protection for content management
- API endpoints for frontend data access
- SQLite database integration
- Docker support with Dockerfile and docker-compose
- Testing infrastructure for website and admin panel
- Environment variable configuration
- Component system for reusable UI elements
- File upload system
- Gallery component
- Dynamic section loading based on SQL configuration
- Automated database installation and updates
- RSS and Sitemap modules
- Integration system for third-party services (webhooks, shortcodes, API)

### Changed
- Moved all variables to SQL for dynamic configuration
- Standardized CSS styles across pages
- Restructured project to follow modular architecture
- Separated admin and user databases

### In Progress
- Mobile menu implementation
- Mobile footer optimization
- SEO optimization
- Contact form with SMTP support
- Live testing interface in admin panel
- Meta section for SEO and social media integration
- Project structure for multiple websites (/www/{domain})

### Technical Debt
- API endpoint fixes (sitemap-exclusions, sections/program)
- Duplicate table/column cleanup in schema.sql
- Mobile responsiveness improvements for admin panel
- Test coverage expansion
