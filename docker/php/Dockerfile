FROM php:8.3-fpm
WORKDIR /app

RUN apt-get update && apt-get install -y \
        libicu-dev \
        libzip-dev \
        unzip \
        zip \
        pkg-config \
    && docker-php-ext-install \
        intl \
        pdo \
        pdo_mysql \
        sockets \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer Install
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy entrypoint script
COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose the default PHP-FPM port
EXPOSE 9000
