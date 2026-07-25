FROM php:8.2-apache

RUN a2dismod mpm_event mpm_worker || true && \
    a2enmod mpm_prefork && \
    docker-php-ext-install pdo pdo_mysql mysqli && \
    a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/public/uploads

COPY .htaccess /var/www/html/.htaccess

RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf
