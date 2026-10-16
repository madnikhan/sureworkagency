# Surework

Surework is the public site and staff system for one agency. Production is this Laravel application on IONOS. The interface is Inertia and React.

## Local

PHP runs through the Composer Docker image on this machine.

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app --entrypoint php composer:2 artisan migrate --seed
npm run dev
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app -p 8000:8000 --entrypoint php composer:2 artisan serve --host=0.0.0.0 --port=8000
```

The local owner is `owner@surework.test`. The password is `LOCAL_OWNER_PASSWORD` in `.env`, default `local-only-change-me`. That account is seeded only when `APP_ENV=local`.

Tests use SQLite in memory:

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app --entrypoint php composer:2 artisan test
npm run typecheck
```

`docker-compose.yml` starts MySQL if you want the same engine as IONOS. Automated tests stay on SQLite.

## Production

See `deploy/IONOS.md`. Do not seed demonstration customers, and do not seed the local owner, on the live server.
