#!/bin/bash

# Make sure containers are up
docker-compose up -d

# Wait for web service to be ready
echo "Waiting for web service to be ready..."
sleep 5

# Run the tests inside the container with environment variables
docker-compose exec -e APP_URL=http://localhost -e APP_PORT=80 -e ADMIN_PATH=/admin web ./vendor/bin/phpunit --testdox

# Show the test results in a more readable format
echo "Test execution completed."
