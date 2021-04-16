FROM wordpress:4.4-apache as wlp-wp-4.4
COPY src/ /var/www/html/4.4/wp-content/plugins/wordlift/
WORKDIR /var/www/html/4.4/

FROM wordpress:4.7-apache as wlp-wp-4.7
COPY src/ /var/www/html/4.7/wp-content/plugins/wordlift/
WORKDIR /var/www/html/4.7/

FROM wordpress:5.0-apache as wlp-wp-5.0
COPY src/ /var/www/html/5.0/wp-content/plugins/wordlift/
WORKDIR /var/www/html/5.0/

FROM wordpress:5.4-apache as wlp-wp-5.4
COPY src/ /var/www/html/5.4/wp-content/plugins/wordlift/
WORKDIR /var/www/html/5.4/

FROM wordpress:5.5-apache as wlp-wp-5.5
COPY src/ /var/www/html/5.5/wp-content/plugins/wordlift/
WORKDIR /var/www/html/5.5/

FROM wordpress:5.6-apache as wlp-wp-5.6
COPY src/ /var/www/html/5.6/wp-content/plugins/wordlift/
WORKDIR /var/www/html/5.6/

FROM wordpress:5.7-apache as wlp-wp-5.7
COPY src/ /var/www/html/5.7/wp-content/plugins/wordlift/
WORKDIR /var/www/html/5.7/

