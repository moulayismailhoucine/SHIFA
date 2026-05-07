#!/bin/bash

cd /var/www/html

echo "APP_KEY length: ${#APP_KEY}"
echo "DB_CONNECTION: $DB_CONNECTION"
echo "DATABASE_URL set: $(test -n "$DATABASE_URL" && echo 'yes' || echo 'no')"

# Run migrations and caches in the background so Apache can start immediately
(
    echo "Waiting for database..."
    i=0
    while [ $i -lt 30 ]; do
        php artisan migrate --force 2>/dev/null && break
        echo "DB not ready, retrying in 2s... ($i/30)"
        sleep 2
        i=$((i + 1))
    done

    if [ $i -eq 30 ]; then
        echo "WARNING: Database not ready after 60 seconds. Migrations skipped."
    else
        echo "Migrations complete."
        # Seed only if users table is empty (first deploy)
        USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
        if [ "$USER_COUNT" = "0" ] || [ "$USER_COUNT" = "" ]; then
            echo "No users found. Running seeders..."
            php artisan db:seed --force 2>/dev/null || echo "Seeding failed, continuing..."
        else
            echo "Users already exist ($USER_COUNT). Skipping seeders."
        fi
        php artisan config:cache 2>/dev/null || echo "config:cache skipped"
        php artisan route:cache 2>/dev/null || echo "route:cache skipped"
        php artisan view:cache 2>/dev/null || echo "view:cache skipped"
    fi
) &

# Start Apache in foreground (required for Render to detect the port)
exec apache2-foreground
