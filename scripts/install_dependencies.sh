#!/bin/bash
# Update system and install Apache and PHP
sudo yum update -y
sudo yum install httpd -y
sudo systemctl start httpd
sudo systemctl enable httpd

# Enable and install PHP 8.0
sudo amazon-linux-extras enable php8.0
sudo yum clean metadata
sudo yum install php php-mysqlnd -y

