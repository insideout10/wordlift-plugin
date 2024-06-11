## Set-up a development environment

1. Clone the Repository
2. Configure wordlift.localhost to point to 127.0.0.1
3. Run `docker compose up`

### A note about db prefix

Please check the db prefix of your database backup, e.g.

```sh
gzcat database-backup.gz  | grep "CREATE TABLE"
```

If the db prefix is not `wp_` you'll need to configure an environment variables, e.g. if the db prefix is `custom_prefix_`:

```sh
WORDPRESS_TABLE_PREFIX=custom_prefix docker compose up
```

## Import a backup DB using docker compose

```sh
gzcat database-backup.gz | npm run wordpress:db-import
```

## Reset the admin password

```sh
npm run wordpress:reset-admin-password
```

## Use a specific WordPress version:

```sh
WORDPRESS_VERSION=6.5.4 docker compose up
```