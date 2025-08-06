#!/bin/sh
set -eux

export ACF_PRO_KEY="${ACF_PRO_KEY:-foobar}"
export ACF_PRO_ENABLED="${ACF_PRO_ENABLED:-0}"
export WORDLIFT_API_URL="${WORDLIFT_API_URL:-https://api.wordlift.io}"
export WORDLIFT_KEY="${WORDLIFT_KEY:-foobar}"
export YOUTUBE_DATA_API_KEY="${YOUTUBE_DATA_API_KEY:-foobar}"
export VIMEO_API_KEY="${VIMEO_API_KEY:-foobar}"

export WORDPRESS_VERSION="${WORDPRESS_VERSION:-php8.0-phpunit7.5woo-wordpress5.6}"
export PHPUNIT_XML="${PHPUNIT_XML:-tests/scenarios/phpunit.default.xml}"

# Database configuration
export MYSQL_ROOT_PASSWORD="password"
export MYSQL_USER="wordpress"
export MYSQL_PASSWORD="password"
export MYSQL_DATABASE="wordpress"

# Cleanup function
cleanup() {
    echo "Cleaning up containers and networks..."
    docker stop db 2>/dev/null || true
    docker rm db 2>/dev/null || true
    docker network rm test-network 2>/dev/null || true
}

# Set trap to run cleanup on script exit
trap cleanup EXIT INT TERM

# Create a network for the containers
docker network create test-network || true

# Start MySQL container
docker run -d \
    --name db \
    --network test-network \
    -e MYSQL_ROOT_PASSWORD \
    -e MYSQL_DATABASE \
    -e MYSQL_USER \
    -e MYSQL_PASSWORD \
    -p 3306 \
    mysql:8

# Wait for MySQL to be ready with timeout
echo "Waiting for MySQL to be ready..."
timeout=60
counter=0

until docker exec db mysql -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" -e "SELECT 1" >/dev/null 2>&1; do
    if [ $counter -eq $timeout ]; then
        echo "MySQL failed to start within $timeout seconds"
        exit 1
    fi
    sleep 1
    counter=$((counter + 1))
done
echo "MySQL is up - executing command"

# Run WordPress tests
docker run  --mount type=bind,source="$PWD/tests/php.ini",target=/usr/local/etc/php/php.ini \
            --mount type=bind,source="$PWD",target=/app \
            --platform linux/amd64 \
            --network test-network \
            --rm \
            -e ACF_PRO_ENABLED \
            -e YOUTUBE_DATA_API_KEY \
            -e VIMEO_API_KEY \
            -e ACF_PRO_KEY \
            -e WORDLIFT_API_URL \
            -e WORDPRESS_VERSION \
            -e DB_NAME \
            -e DB_USER \
            -e DB_PASSWORD \
            -e DB_HOST \
            ziodave/wordpress-tests:"${WORDPRESS_VERSION}" \
            -c "${PHPUNIT_XML}" "$@"
