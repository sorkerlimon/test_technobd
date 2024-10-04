#!/bin/bash

# Change permissions for the index.php file
chmod 755 /var/www/technobd.com/public_html/index.php

# Check if the command was successful
if [ $? -eq 0 ]; then
    echo "Permissions set successfully for index.php."
else
    echo "Failed to set permissions for index.php."
    exit 1
fi
