# Image siap deploy (Render, Railway, VPS, dsb) — PHP 8.3 + Apache + pdo_pgsql
FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

# Ikuti variabel PORT dari platform hosting (default 80).
CMD ["sh", "-c", "sed -i -E \"s/Listen [0-9]+/Listen ${PORT:-80}/\" /etc/apache2/ports.conf && sed -i -E \"s/<VirtualHost \\*:[0-9]+>/<VirtualHost *:${PORT:-80}>/\" /etc/apache2/sites-available/000-default.conf && apache2-foreground"]
