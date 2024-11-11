# Docker Compose E2E Tests

This directory contains end-to-end tests for verifying the Docker Compose deployment setup using Ansible.

## Prerequisites

- Docker
- Ansible
- Python Docker SDK (required for Ansible Docker modules)

The test script will automatically install the required Ansible collection:
- community.docker

## What's Being Tested

The e2e tests verify:

1. Docker service is running
2. Docker Compose deployment
3. Web service accessibility (port 8007)
4. PHP functionality
5. Directory permissions (logs and data)
6. Apache configuration

## Test Files

- `docker-compose-test.yml` - Ansible playbook for testing the deployment
- `test.php` - Simple PHP file to verify PHP functionality
- `run-tests.sh` - Shell script to execute the tests

## Running the Tests

1. Make sure you have all prerequisites installed:
   ```bash
   # Check Docker
   docker --version
   
   # Check Ansible
   ansible --version
   
   # Install Python Docker SDK
   pip install docker
   ```

2. Navigate to the e2e test directory:
   ```bash
   cd tests/e2e
   ```

3. Run the tests:
   ```bash
   ./run-tests.sh
   ```

The script will automatically install the required Ansible collection (community.docker) before running the tests.

## Test Flow

1. Checks for required dependencies
2. Installs necessary Ansible collections
3. Verifies Docker service is running
4. Builds and starts containers using Ansible's docker_compose module
5. Waits for web service to be accessible
6. Tests PHP functionality
7. Verifies directory permissions
8. Checks Apache configuration
9. Cleans up by stopping containers

## Cleanup

The test script automatically cleans up after itself by:
- Removing the temporary test.php file
- Bringing down the Docker containers (when cleanup flag is set)

## Troubleshooting

If tests fail, check:
1. Docker service status
2. Port 8007 availability
3. Docker Compose configuration
4. System permissions
5. Python Docker SDK installation

Common Issues:
- If you see errors about missing Docker modules, ensure Python Docker SDK is installed: `pip install docker`
- If port 8007 is unavailable, check if another service is using it
- If permission errors occur, ensure Docker has appropriate permissions
- If Ansible collection errors occur, try manually installing: `ansible-galaxy collection install community.docker`
