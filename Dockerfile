FROM debian:bookworm

# Debian bookworm ships PHP 8.2 by default, so we install Apache2 and PHP 8.2
# straight from the distro repos. Because we are starting from a bare Debian
# base (instead of php:8.2-apache, which ships with mpm_event compiled in),
# we have full control over which MPM gets installed/enabled from the start.
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
RUN chown -R www-data:www-data /var/www/html/public/uploads

COPY .htaccess /var/www/html/.htaccess

RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf

# Ensure Apache explicitly listens on port 80 for IPv4 connections and set a
# default ServerName to suppress the "Could not reliably determine the
# server's fully qualified domain name" warning. Without an explicit
# ServerName, Apache can be slow/unreliable to bind, which causes health
# checks to time out.
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "Listen 0.0.0.0:80" > /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost 0.0.0.0:80>/' /etc/apache2/sites-enabled/000-default.conf

# Ensure Apache serves index.php by default so the front controller pattern
# works when requesting the root URL or any directory. Without this, Apache
# has no DirectoryIndex configured at the virtual host level and will not
# know to hand requests off to index.php.
RUN sed -i '/DocumentRoot \/var\/www\/html/a\\tDirectoryIndex index.php index.html' /etc/apache2/sites-enabled/000-default.conf

EXPOSE 80

HEALTHCHECK --interval=10s --timeout=5s --retries=3 \
  CMD curl -f http://localhost/ || exit 1
  
CMD ["apache2ctl", "-D", "FOREGROUND"]
