ARG PHP_VERSION=5.6
FROM php:${PHP_VERSION}

# install the PHP extensions we need (https://make.wordpress.org/hosting/handbook/handbook/server-environment/#php-extensions)
RUN set -ex; \
	\
	savedAptMark="$(apt-mark showmanual)"; \
	\
	apt-get update; \
	apt-get install -y --no-install-recommends \
	    subversion \
		libfreetype6-dev \
		libjpeg-dev \
		libmagickwand-dev \
		libpng-dev \
		libzip-dev \
	; \
	\
	# PHP 7.2 fails with `--with-freetype --with-jpeg`.
	# docker-php-ext-configure gd --with-freetype --with-jpeg; \
	docker-php-ext-configure gd; \
	docker-php-ext-install \
		bcmath \
		exif \
		gd \
		mysqli \
		zip \
	; \
	pecl install imagick-3.4.4; \
	docker-php-ext-enable imagick; \
	\
# reset apt-mark's "manual" list so that "purge --auto-remove" will remove all build dependencies
	apt-mark auto '.*' > /dev/null; \
	apt-mark manual $savedAptMark; \
	apt-mark hold subversion ; \
	ldd "$(php -r 'echo ini_get("extension_dir");')"/*.so \
		| awk '/=>/ { print $3 }' \
		| sort -u \
		| xargs -r dpkg-query -S \
		| cut -d: -f1 \
		| sort -u \
		| xargs -rt apt-mark manual; \
	\
	apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
	rm -rf /var/lib/apt/lists/*

ARG PHP_UNIT_VERSION=4.8
RUN curl -LO https://phar.phpunit.de/phpunit-${PHP_UNIT_VERSION}.phar && \
    chmod +x phpunit-${PHP_UNIT_VERSION}.phar && \
    mv phpunit-${PHP_UNIT_VERSION}.phar /usr/local/bin/phpunit && \
    phpunit --version

WORKDIR /tmp

COPY install-wp-tests.sh .

ARG WORDPRESS_VERSION=latest
RUN /tmp/install-wp-tests.sh wordpress wordpress password db ${WORDPRESS_VERSION} true

VOLUME ["/app"]
WORKDIR /app

ENTRYPOINT ["/usr/local/bin/phpunit"]
CMD ["--help"]
