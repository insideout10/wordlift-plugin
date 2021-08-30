FROM wordpress:4.4-apache as wlp-wp-4.4
COPY --chown=www-data src/ /var/www/html/4.4/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
COPY manifests/entrypoint_4.4.sh /entrypoint.sh
RUN chmod +x /usr/local/bin/wp
RUN chmod +x /entrypoint.sh
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/4.4/

FROM wordpress:4.7-apache as wlp-wp-4.7
COPY --chown=www-data src/ /var/www/html/4.7/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
RUN chmod +x /usr/local/bin/wp
COPY manifests/entrypoint_4.7.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/4.7/

FROM wordpress:5.0-apache as wlp-wp-5.0
COPY --chown=www-data src/ /var/www/html/5.0/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
COPY manifests/entrypoint_5.0.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/wp
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/5.0/

FROM wordpress:5.4-apache as wlp-wp-5.4
COPY --chown=www-data src/ /var/www/html/5.4/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
COPY manifests/entrypoint_5.4.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/wp
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/5.4/

FROM wordpress:5.5-apache as wlp-wp-5.5
COPY --chown=www-data src/ /var/www/html/5.5/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
COPY manifests/entrypoint_5.5.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/wp
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/5.5/

FROM wordpress:5.6-apache as wlp-wp-5.6
COPY --chown=www-data src/ /var/www/html/5.6/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
COPY manifests/entrypoint_5.6.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/wp
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/5.6/

FROM wordpress:5.7-apache as wlp-wp-5.7
COPY --chown=www-data src/ /var/www/html/5.7/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
COPY manifests/entrypoint_5.7.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/wp
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/5.7/

FROM wordpress:5.8-apache as wlp-wp-5.8
COPY --chown=www-data src/ /var/www/html/5.8/wp-content/plugins/wordlift/
COPY manifests/wp-cli.phar /usr/local/bin/wp
COPY manifests/entrypoint_5.8.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/wp
RUN chown www-data:www-data /var/www/html -R
WORKDIR /var/www/html/5.8/

