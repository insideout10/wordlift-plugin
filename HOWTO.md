## Set-up a development environment

1. Clone the Repository
2. Configure wordlift.localhost to point to 127.0.0.1
3. Run `docker compose up`

## Import a backup DB using docker compose

```sh
gzcat database-backup.gz | npm run wordpress:db-import
```

## Reset the admin pasword

```sh
npm run wordpress:reset-admin-password
```