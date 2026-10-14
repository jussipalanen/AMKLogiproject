#!/bin/sh
# Prepares the container before Apache starts: fixes volume permissions, runs
# database migrations (waiting for MySQL) and optionally seeds demo data.
set -e
cd /app

# In live mode Apache runs with the host user's uid (PHP_USER_ID) so it can write to the
# bind-mounted project; switch before fixing ownership below.
if [ -n "${PHP_USER_ID:-}" ] && [ "$PHP_USER_ID" != "0" ]; then
    usermod -u "$PHP_USER_ID" www-data >/dev/null 2>&1 || true
fi

# Named volumes are created empty and owned by root.
mkdir -p runtime web/assets web/uploads
chown -R www-data:www-data runtime web/assets web/uploads

as_www_data() {
    runuser -u www-data -- "$@"
}

if [ "$1" = "apache2-foreground" ]; then
    attempt=1
    until as_www_data php yii migrate --interactive=0; do
        if [ "$attempt" -ge 30 ]; then
            echo "amklogi: database migrations failed, giving up." >&2
            exit 1
        fi
        echo "amklogi: waiting for the database (attempt $attempt)..."
        attempt=$((attempt + 1))
        sleep 2
    done

    if [ "${SEED_DEMO_DATA:-false}" = "true" ]; then
        as_www_data php yii seed
    fi
fi

exec docker-php-entrypoint "$@"
