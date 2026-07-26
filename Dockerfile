FROM debian:bookworm

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y --no-install-recommends \
        apache2 \
        php8.2 \
        libapache2-mod-php8.2 \
        php8.2-mysql \
        php8.2-mbstring \
        php8.2-xml \
        php8.2-curl \
        php8.2-zip \
        php8.2-gd \
    && a2dismod mpm_event mpm_worker >/dev/null 2>&1 || true \
    && a2enmod mpm_prefork \
    && a2enmod php8.2 \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/

# Garante que a pasta de uploads existe antes do chown
RUN mkdir -p /var/www/html/public/uploads/ocorrencias \
    && chown -R www-data:www-data /var/www/html/public/uploads

COPY .htaccess /var/www/html/.htaccess

RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "Listen 0.0.0.0:80" > /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost 0.0.0.0:80>/' /etc/apache2/sites-enabled/000-default.conf

RUN sed -i '/DocumentRoot \/var\/www\/html/a\\tDirectoryIndex index.php index.html' /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

CMD ["apache2ctl", "-D", "FOREGROUND"]
