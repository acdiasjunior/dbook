FROM php:8.3-cli
WORKDIR /app

# Install required dependencies
RUN apt-get update && apt-get install -y supervisor

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

# Start supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
