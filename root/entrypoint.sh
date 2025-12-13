#!/bin/bash

# 1. Start MySQL Server in the background
echo "[*] Starting MariaDB..."
service mariadb start

# Wait for MySQL to wake up
sleep 5

# 2. Configure Database
echo "[*] Configuring Database..."
# Create DB, User, and Grant Privileges
mysql -u root -e "CREATE DATABASE IF NOT EXISTS ctf_db;"
mysql -u root -e "CREATE USER 'ctf_user'@'localhost' IDENTIFIED BY 'ctf_pass';"
mysql -u root -e "GRANT ALL PRIVILEGES ON ctf_db.* TO 'ctf_user'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

# 3. Import the Challenge Data
echo "[*] Importing Challenge Data..."
mysql -u root ctf_db < /docker-entrypoint-initdb.d/init.sql

# 4. Start Apache in the foreground (Keep container alive)
echo "[*] Starting Apache..."
apache2-foreground