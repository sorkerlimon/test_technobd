#!/bin/bash
# Disable the default welcome page and restart Apache
sudo mv /etc/httpd/conf.d/welcome.conf /etc/httpd/conf.d/welcome.conf.bak
sudo systemctl restart httpd
sudo systemctl status httpd
