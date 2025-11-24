# Coolify Deployment Fixes

## Issue 1: "No available server"
**Symptoms**: Coolify shows "No available server" when accessing the site.
**Cause**: The application container was crashing because the custom `ENTRYPOINT` script tried to execute `/entrypoint`, which does not exist in the `serversideup/php` image.
**Fix**: 
- Removed custom `ENTRYPOINT` override.
- Switched to using `/etc/entrypoint.d/` scripts, which is the native way `serversideup/php` handles custom startup tasks.
- Created `start-cron.sh` and copied `migrate.sh` to `/etc/entrypoint.d/`.

## Issue 2: Port Conflict
**Symptoms**: Deployment failed with "Bind for 0.0.0.0:8080 failed: port is already allocated".
**Cause**: `docker-compose.yml` had explicit port mappings (`8080:80`), which conflicted with Coolify's proxy (Traefik).
**Fix**:
- Removed `ports` section from `docker-compose.yml`.
- Created `docker-compose.override.yml` for local development ports (ignored by Coolify).

## Issue 3: MySQL Healthcheck
**Symptoms**: MySQL container marked "unhealthy".
**Cause**: Healthcheck command failed when `DB_PASS` was empty (default).
**Fix**:
- Updated healthcheck to handle empty passwords.
- Changed default `DB_PASS` to `changeme` for easier testing.

## How to Deploy Now

1. **Push changes** to Git.
2. **Redeploy** in Coolify.
3. **Verify**:
   - MySQL should start and be healthy.
   - App should start (no crash) and be healthy.
   - Site should be accessible via the Coolify-provided URL.

## Local Development
- Run `docker-compose up` as usual.
- It will use `docker-compose.override.yml` to expose ports 8080 and 3306.
