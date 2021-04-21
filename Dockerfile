FROM wordpress:4.4-apache as wlp-wp-4.4
RUN mkdir -p /var/www/html/4.4/wp-content/plugins/ --mode=777
COPY --chown=www-data src/ /var/www/html/4.4/wp-content/plugins/wordlift/
RUN chown -R www-data:www-data /var/www/html/4.4/
WORKDIR /var/www/html/4.4/

FROM wordpress:4.7-apache as wlp-wp-4.7
RUN mkdir -p /var/www/html/4.7/wp-content/plugins/ --mode=777
COPY --chown=www-data src/ /var/www/html/4.7/wp-content/plugins/wordlift/
RUN chown -R www-data:www-data /var/www/html/4.7/
WORKDIR /var/www/html/4.7/

FROM wordpress:5.0-apache as wlp-wp-5.0
RUN mkdir -p /var/www/html/5.0/wp-content/plugins/ --mode=777
COPY --chown=www-data src/ /var/www/html/5.0/wp-content/plugins/wordlift/
RUN chown -R www-data:www-data /var/www/html/5.0/
WORKDIR /var/www/html/5.0/

FROM wordpress:5.4-apache as wlp-wp-5.4
RUN mkdir -p /var/www/html/5.4/wp-content/plugins/ --mode=777
COPY --chown=www-data src/ /var/www/html/5.4/wp-content/plugins/wordlift/
RUN chown -R www-data:www-data /var/www/html/5.4/
WORKDIR /var/www/html/5.4/

FROM wordpress:5.5-apache as wlp-wp-5.5
RUN mkdir -p /var/www/html/5.5/wp-content/plugins/ --mode=777
COPY --chown=www-data src/ /var/www/html/5.5/wp-content/plugins/wordlift/
RUN chown -R www-data:www-data /var/www/html/5.5/
WORKDIR /var/www/html/5.5/

FROM wordpress:5.6-apache as wlp-wp-5.6
RUN mkdir -p /var/www/html/5.6/wp-content/plugins/ --mode=777
COPY --chown=www-data src/ /var/www/html/5.6/wp-content/plugins/wordlift/
RUN chown -R www-data:www-data /var/www/html/5.6/
WORKDIR /var/www/html/5.6/

FROM wordpress:5.7-apache as wlp-wp-5.7
RUN mkdir -p /var/www/html/5.7/wp-content/plugins/ --mode=777
COPY --chown=www-data src/ /var/www/html/5.7/wp-content/plugins/wordlift/
RUN chown -R www-data:www-data /var/www/html/5.7/
WORKDIR /var/www/html/5.7/

