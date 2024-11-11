#!/bin/bash

# Exit on any error
set -e

echo "Starting e2e tests for Docker Compose deployment..."

# Check if ansible is installed
if ! command -v ansible-playbook &> /dev/null; then
    echo "Ansible is not installed. Please install it first."
    exit 1
fi

# Check if docker is installed
if ! command -v docker &> /dev/null; then
    echo "Docker is not installed. Please install it first."
    exit 1
fi

# Install required Ansible collections
echo "Installing required Ansible collections..."
ansible-galaxy collection install community.docker

# Copy test.php to root directory for testing
cp test.php ../../test.php

# Run the ansible playbook
ansible-playbook docker-compose-test.yml

# Clean up
rm ../../test.php

echo "E2E tests completed successfully!"
