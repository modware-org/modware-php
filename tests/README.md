# Testing Infrastructure

This directory contains the testing infrastructure for both the main website and admin panel.

## Directory Structure
```
tests/
├── Admin/            # Admin panel tests
├── MainSite/         # Main website tests
└── e2e/             # End-to-end tests
    ├── test.php     # E2E test suite
    ├── run-tests.sh # Test runner
    ├── ansible.cfg  # Ansible configuration
    └── docker-compose-test.yml # Test environment
```

## Test Types

### 1. End-to-End Tests
Location: `/tests/e2e`
- Full system testing
- Browser automation
- API integration tests
- Performance testing

### 2. Admin Panel Tests
Location: `/tests/Admin`
- Authentication tests
- Permission tests
- API endpoint tests
- UI component tests

### 3. Main Site Tests
Location: `/tests/MainSite`
- Section rendering
- Component functionality
- Database queries
- Integration tests

## Running Tests

### Full Test Suite
```bash
./test.sh
```

### Specific Tests
```bash
# Run admin tests
./test.sh admin

# Run e2e tests
./test.sh e2e

# Run specific section tests
./test.sh sections/section_name

# Run specific component tests
./test.sh components/component_name
```

## Test Environment

### Docker Setup
```yaml
# docker-compose-test.yml
services:
  web:
    build: .
    ports:
      - "8080:80"
  db:
    image: mysql:5.7
    environment:
      MYSQL_DATABASE: test_db
```

### Configuration
```bash
# .env.test
DB_HOST=localhost
DB_NAME=test_db
DB_USER=test_user
DB_PASS=test_pass
```

## Writing Tests

### Section Tests
```php
class SectionTest extends TestCase {
    public function testRendering() {
        // Test section rendering
    }

    public function testQueries() {
        // Test database queries
    }
}
```

### Component Tests
```php
class ComponentTest extends TestCase {
    public function testFunctionality() {
        // Test component functionality
    }

    public function testIntegration() {
        // Test integration with sections
    }
}
```

### API Tests
```php
class ApiTest extends TestCase {
    public function testEndpoint() {
        // Test API endpoint
    }

    public function testAuthentication() {
        // Test API authentication
    }
}
```

## Continuous Integration

### GitHub Actions
```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Run tests
        run: ./test.sh
```

## Test Coverage

Coverage reports are generated for:
- PHP files
- JavaScript files
- SQL queries
- API endpoints

## Best Practices

1. Test Organization
   - Group related tests
   - Use descriptive names
   - Maintain independence
   - Clean up after tests

2. Test Data
   - Use fixtures
   - Reset database state
   - Mock external services
   - Use realistic data

3. Performance
   - Optimize test runs
   - Parallel execution
   - Cache test results
   - Clean up resources

4. Maintenance
   - Regular updates
   - Remove obsolete tests
   - Document changes
   - Version control

## Troubleshooting

Common issues and solutions:

1. Failed Tests
   - Check environment
   - Verify dependencies
   - Review logs
   - Check database state

2. Performance Issues
   - Optimize queries
   - Reduce file operations
   - Use test doubles
   - Parallel execution

3. Environment Problems
   - Check configuration
   - Verify permissions
   - Review Docker setup
   - Check network

## Adding New Tests

1. Create test file
2. Implement test cases
3. Add to test suite
4. Update documentation
5. Verify coverage

## Automated Testing

### Cron Job
```bash
0 0 * * * /path/to/test.sh > /var/log/tests.log 2>&1
```

### Test Reports
- HTML coverage reports
- JUnit XML reports
- Console output
- Error logs
