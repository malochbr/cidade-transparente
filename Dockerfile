FROM php:8.2-apache

# Remove agressivamente todos os MPMs conflitantes (mpm_event, mpm_worker) de
# mods-enabled e mods-available, garantindo que apenas o mpm_prefork exista
# no sistema. Isso evita o erro "More than one MPM loaded" causado pelo
# mpm_event vindo pré-configurado na imagem base.
RUN rm -f /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_worker.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_prefork.load \
          /etc/apache2/mods-enabled/mpm_prefork.conf \
          /etc/apache2/mods-available/mpm_event.load \
          /etc/apache2/mods-available/mpm_event.conf \
          /etc/apache2/mods-available/mpm_worker.load \
          /etc/apache2/mods-available/mpm_worker.conf && \
    find /etc/apache2/mods-enabled -lname '*mpm_*' -delete && \
    ln -s /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load && \
    ln -s /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf

RUN docker-php-ext-install pdo pdo_mysql mysqli
RUN a2enmod rewrite

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html/public/uploads

COPY .htaccess /var/www/html/.htaccess

RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>' >> /etc/apache2/apache2.conf
