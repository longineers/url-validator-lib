FROM php:8.4-cli

# Create a non-root user
RUN groupadd -r appuser && useradd -r -g appuser appuser

# Install system dependencies and PHP extensions in a single layer
RUN apt-get update && apt-get install -y --no-install-recommends \
    libicu-dev \
    libzip-dev \
    libxml2-dev \
    unzip \
    && docker-php-ext-install -j$(nproc) \
    intl \
    zip \
    xml \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install Composer with signature verification
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === file_get_contents('https://composer.github.io/installer.sig')) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); exit(1); }" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');"

# Set working directory
WORKDIR /app

# Set composer root version to avoid version detection issues
ENV COMPOSER_ROOT_VERSION=dev-main

# Copy composer files first for better caching
COPY composer.json composer.lock* ./

# Install PHP dependencies (without dev dependencies) as root, then change ownership
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader \
    && composer dump-autoload --optimize \
    && chown -R appuser:appuser /app

# allow Psalm to use a cache directory
RUN mkdir -p /home/appuser/.cache/psalm && chown -R appuser:appuser /home/appuser/.cache/psalm

# Set Psalm cache dir for all processes
ENV PSALM_CACHE_DIR=/tmp/psalm-cache

# Copy source code
COPY --chown=appuser:appuser . /app

# Switch to non-root user
USER appuser

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD php -v || exit 1

# Keep container running for interactive use
CMD ["tail", "-f", "/dev/null"]
