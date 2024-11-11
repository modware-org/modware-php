#!/bin/bash

# Navigate to the project root directory
cd "$(dirname "$0")/.."

# Run the PHP migration script
php scripts/run_footer_migration.php
