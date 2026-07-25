FROM php:8.2-apache

# Desabilita TODOS os MPMs habilitados por padrão (mpm_event, mpm_worker, mpm_prefork)
# e força a habilitação apenas do mpm_prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork || true && \
    rm -f /etc/apache2/mods-enabled/mpm_*.conf \
          /etc/apache2/mods-enabled/mpm_*.load && \
    a2enmod mpm_prefork

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/public/uploads

COPY .htaccess /var/www/html/.htaccess

RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf
