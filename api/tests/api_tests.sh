#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Get base URL from .env
APP_URL=$(grep APP_URL .env | cut -d '=' -f2)

# Base URL for API
API_URL="${APP_URL}/admin/api"
TOKEN=""

# Helper function for printing test results
print_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✓ $2${NC}"
    else
        echo -e "${RED}✗ $2${NC}"
        echo -e "${RED}Error: $3${NC}"
    fi
}

# Debug info
echo -e "${YELLOW}Debug Info:${NC}"
echo "API URL: ${API_URL}"

# Test Authentication
echo -e "\nTesting Authentication..."
echo -e "${YELLOW}Sending auth request to: ${API_URL}/auth${NC}"

AUTH_RESPONSE=$(curl -v -X POST \
    -H "Content-Type: application/json" \
    -d '{"username":"admin","password":"admin123"}' \
    "${API_URL}/auth" 2>&1)

echo -e "${YELLOW}Raw Response:${NC}"
echo "$AUTH_RESPONSE"

if [[ $AUTH_RESPONSE == *"token"* ]]; then
    TOKEN=$(echo $AUTH_RESPONSE | grep -o '"token":"[^"]*' | cut -d'"' -f4)
    print_result 0 "Authentication successful"
else
    print_result 1 "Authentication failed" "$AUTH_RESPONSE"
    exit 1
fi

# Test Content Management
echo -e "\nTesting Content Management..."

# Create content
CREATE_CONTENT_RESPONSE=$(curl -s -X POST \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer ${TOKEN}" \
    -d '{
        "title":"Test Page",
        "slug":"test-page",
        "content":"This is a test page content",
        "type":"page",
        "status":"draft"
    }' \
    "${API_URL}/content")

print_result $? "Create content" "$CREATE_CONTENT_RESPONSE"

# Get content
GET_CONTENT_RESPONSE=$(curl -s -X GET \
    -H "Authorization: Bearer ${TOKEN}" \
    "${API_URL}/content")

print_result $? "Get content list" "$GET_CONTENT_RESPONSE"

# Test Menu Management
echo -e "\nTesting Menu Management..."

# Create menu item
CREATE_MENU_RESPONSE=$(curl -s -X POST \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer ${TOKEN}" \
    -d '{
        "title":"Test Menu",
        "url":"/test-page",
        "position":1
    }' \
    "${API_URL}/menu")

print_result $? "Create menu item" "$CREATE_MENU_RESPONSE"

# Get menu items
GET_MENU_RESPONSE=$(curl -s -X GET \
    -H "Authorization: Bearer ${TOKEN}" \
    "${API_URL}/menu")

print_result $? "Get menu items" "$GET_MENU_RESPONSE"

# Test SEO Settings
echo -e "\nTesting SEO Settings..."

# Update SEO settings
UPDATE_SEO_RESPONSE=$(curl -s -X POST \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer ${TOKEN}" \
    -d '{
        "meta_title":"Test Page Title",
        "meta_description":"Test page description",
        "meta_keywords":"test, page, keywords",
        "page_id":1
    }' \
    "${API_URL}/seo")

print_result $? "Update SEO settings" "$UPDATE_SEO_RESPONSE"

# Get SEO settings
GET_SEO_RESPONSE=$(curl -s -X GET \
    -H "Authorization: Bearer ${TOKEN}" \
    "${API_URL}/seo?page_id=1")

print_result $? "Get SEO settings" "$GET_SEO_RESPONSE"

# Test Media Upload
echo -e "\nTesting Media Upload..."

# Create test file
echo "Test content" > test_upload.txt

# Upload file
UPLOAD_RESPONSE=$(curl -s -X POST \
    -H "Authorization: Bearer ${TOKEN}" \
    -F "files[]=@test_upload.txt" \
    "${API_URL}/media/upload")

print_result $? "Upload media file" "$UPLOAD_RESPONSE"

# Get media list
GET_MEDIA_RESPONSE=$(curl -s -X GET \
    -H "Authorization: Bearer ${TOKEN}" \
    "${API_URL}/media")

print_result $? "Get media list" "$GET_MEDIA_RESPONSE"

# Cleanup
rm test_upload.txt

echo -e "\nAPI Tests Completed!"
