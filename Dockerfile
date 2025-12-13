# Use PHP with Apache
FROM php:7.4-apache

# Install MariaDB (MySQL) Server inside the same container
RUN apt-get update && apt-get install -y mariadb-server mariadb-client

# Install PHP MySQL extension
RUN docker-php-ext-install mysqli

# Set the working directory
WORKDIR /var/www/html

COPY . /var/www/html/

# Copy the Database Initialization Script
COPY db/init.sql /docker-entrypoint-initdb.d/init.sql

# Copy the Startup Script
COPY entrypoint.sh /entrypoint.sh

# Fix Permissions
RUN chmod +x /entrypoint.sh && \
    chown -R www-data:www-data /var/www/html

# Expose Web Port
EXPOSE 80

# Run the startup script
CMD ["/entrypoint.sh"]