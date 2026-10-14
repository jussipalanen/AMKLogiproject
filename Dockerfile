# syntax=docker/dockerfile:1

# Official Yii 2 image: PHP 8.5 + Apache (document root /app/web) with GD, Imagick,
# intl, pdo_mysql and pdo_sqlite.
FROM yiisoftware/yii2-php:8.5-apache AS base
WORKDIR /app
COPY docker/php.ini /usr/local/etc/php/conf.d/zz-amklogi.ini

# Test image: dev dependencies + SQLite. Run with `docker compose --profile test run --rm tests`.
FROM base AS test
ENV YII_ENV=test
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-interaction --no-progress --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --no-interaction
CMD ["composer", "tests"]

# Production image (default target).
FROM base AS app
ENV YII_ENV=prod \
    YII_DEBUG=0
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-interaction \
    && mkdir -p runtime web/assets web/uploads \
    && chown -R www-data:www-data runtime web/assets web/uploads
COPY --chmod=0755 docker/entrypoint.sh /usr/local/bin/amklogi-entrypoint
ENTRYPOINT ["amklogi-entrypoint"]
CMD ["apache2-foreground"]
