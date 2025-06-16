FROM php:8.2-apache

# Install mysqli extension and dependencies
RUN docker-php-ext-install mysqli

# Copy your app files
COPY . /var/www/html/

# Enable apache mod_rewrite if needed
RUN a2enmod rewrite
