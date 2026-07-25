FROM php:8.2-apache

# Corrige o conflito de MPM
RUN a2dismod mpm_event mpm_worker 2>/dev/null; \
    a2enmod mpm_prefork

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/public/uploads

COPY .htaccess /var/www/html/.htaccess

RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf
