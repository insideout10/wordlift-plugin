Run PHPUnit with XDEBUG
-----------------------

```shell
docker compose run --rm \
  -e WORDLIFT_KEY=key123 \
  -e PHP_IDE_CONFIG="serverName=phpunit" \
  -e XDEBUG_CONFIG="remote_enable=1 remote_host=host.docker.internal remote_port=9000" \
  phpunit -c phpunit.xml
```
