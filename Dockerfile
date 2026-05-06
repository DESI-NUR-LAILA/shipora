FROM dunglas/frankenphp:php8.3

RUN install-php-extensions \
    gd \
    pdo \
    pdo_mysql \
    mbstring \
    curl \
    xml \
    zip \
    intl \
    bcmath