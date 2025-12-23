FROM php:8.3-apache

RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libpq-dev \
    libzip-dev \
  && docker-php-ext-install -j"$(nproc)" \
    intl \
    pdo_pgsql \
    zip \
    opcache \
  && a2enmod rewrite headers \
  && rm -rf /var/lib/apt/lists/*

# Point Apache to Symfony's public/ directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
  && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Route all requests through Symfony front controller (so Nelmio CORS can answer OPTIONS preflights)
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
  # Windows checkouts may introduce CRLF which breaks shebang execution inside Linux containers
  && sed -i 's/\r$//' /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]



