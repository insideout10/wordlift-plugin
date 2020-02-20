docker-compose exec wordpress chown -R www-data:www-data /var/www/html
docker-compose run cli core install --url="https://wordlift.localhost" --title="WordLift" --admin_user=admin --admin_password=password --admin_email=root@localhost.localdomain --skip-email
docker-compose run cli plugin activate wordlift
docker-compose run cli plugin install classic-editor --version=1.3 --activate
