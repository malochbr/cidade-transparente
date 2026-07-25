FROM php:8.2-apache

# Remove TODOS os MPMs habilitados e força apenas o prefork
RUN rm -f /etc/apache2/mods-enabled/mpm_*.conf \
           /etc/apache2/mods-enabled/mpm_*.load && \
    ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf && \
    ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/public/uploads

COPY .htaccess /var/www/html/.htaccess

RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf
