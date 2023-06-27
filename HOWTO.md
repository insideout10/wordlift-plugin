## Import a backup DB using docker compose

```sh
gzcat database-backup.gz | npm run wordpress:db-import
```

## Reset the admin pasword

```sh
npm run wordpress:reset-admin-password
```