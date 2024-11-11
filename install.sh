#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

clear

echo -e "${YELLOW}Starting installation...${NC}"

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo -e "${RED}PHP is not installed. Please install PHP and try again.${NC}"
    exit 1
fi

# Kill any existing process on port 8007
echo "Checking for existing processes on port 8007..."
if command -v lsof >/dev/null 2>&1; then
    PORT_PID=$(lsof -ti:8007)
    if [ ! -z "$PORT_PID" ]; then
        echo "Killing process using port 8007..."
        kill -9 $PORT_PID
        sleep 1
    fi
else
    # Fallback to pkill if lsof is not available
    if pgrep -f "php -S.*:8007" > /dev/null; then
        echo "Killing existing PHP server on port 8007..."
        pkill -f "php -S.*:8007"
        sleep 1
    fi
fi

# Create necessary directories if they don't exist
echo "Creating necessary directories..."
mkdir -p uploads/images
mkdir -p uploads/documents
mkdir -p uploads/other

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 .
chmod -R 777 uploads
find . -type f -name "*.php" -exec chmod 644 {} \;
find . -type f -name "*.sh" -exec chmod 755 {} \;

# Copy .env.example to .env if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file..."
    cp .env.example .env
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}Created .env file${NC}"
    else
        echo -e "${RED}Failed to create .env file${NC}"
        exit 1
    fi
fi

# Copy admin/.env.example to admin/.env if it doesn't exist
#if [ ! -f admin/.env ]; then
#    echo "Creating admin/.env file..."
#    cp admin/.env.example admin/.env
#    if [ $? -eq 0 ]; then
#        echo -e "${GREEN}Created admin/.env file${NC}"
#    else
#        echo -e "${RED}Failed to create admin/.env file${NC}"
#        exit 1
#    fi
#fi

# ADMIN: Load Variables
#source admin/.env
#rm -f admin/*.sqlite
#
## Run the admin database installation
#echo "Running admin database installation..."
#ADMIN_INSTALL_RESULT=$(php -d display_errors=1 admin/install.php 2>&1)
#
## Check if the admin installation was successful by looking for success:true and no error messages
#if echo "$ADMIN_INSTALL_RESULT" | grep -q '"success": *true' && ! echo "$ADMIN_INSTALL_RESULT" | grep -q "PHP Warning\|PHP Notice\|PHP Error"; then
#    echo -e "${GREEN}Admin database installation completed successfully!${NC}"
#
#    # Create admin user if it doesn't exist
#    echo "Creating admin users..."
#    php admin/scripts/create_user.php
#
#    echo -e "${GREEN}Installation completed!${NC}"
#    echo -e "${YELLOW}Please ensure your web server has proper permissions to write to the uploads directory.${NC}"
#    echo -e "${YELLOW}You can now log in to the admin panel with the default credentials:${NC}"
#    echo "Username: admin"
#    echo "Password: admin123"
#    echo -e "${RED}IMPORTANT: Please change the default password after your first login!${NC}"
#
#
#else
#    echo -e "${RED}Admin database installation failed:${NC}"
#    echo "$ADMIN_INSTALL_RESULT"
#    exit 1
#fi


rm -rf logs/*.log

source .env
# USER: Remove existing databases
rm -f database.sqlite
# Run the main database installation
echo "Running main database installation..."
php install.php



# Load Variables
HOSTNAME=${HOSTNAME:-localhost}
PORT=${PORT:-8007}

# Local PHP setup
echo -e "\n${YELLOW}Starting PHP development server...${NC}"
echo -e "You can access the application at: http://${HOSTNAME}:${PORT}"
echo -e "Press Ctrl+C to stop the server"
php -S ${HOSTNAME}:${PORT}