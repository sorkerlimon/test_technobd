#!/bin/bash
# Create a directory for the website
sudo mkdir -p /var/www/technobd.com/public_html

# Set ownership and permissions
sudo chown -R $USER:$USER /var/www/technobd.com/public_html
sudo chmod -R 755 /var/www
sudo chown -R apache:apache /var/www/technobd.com/public_html

# Create a sample PHP file
cat <<EOT >> /var/www/technobd.com/public_html/index.php
<?php
echo "Welcome to technobd.com!";
?>
EOT

# Configure virtual host
cat <<EOT >> /etc/httpd/conf.d/technobd.com.conf
<VirtualHost *:80>
    ServerAdmin admin@technobd.com
    ServerName technobd.com
    ServerAlias www.technobd.com
    DocumentRoot /var/www/technobd.com/public_html
    ErrorLog /var/log/httpd/technobd.com-error.log
    CustomLog /var/log/httpd/technobd.com-access.log combined
</VirtualHost>
EOT
