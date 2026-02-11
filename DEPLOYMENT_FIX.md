# Coolify Deployment Fixes

## Issue 1: "No available server" (Entrypoint Crash)
**Symptoms**: Container crashing immediately.
**Cause**: Custom `ENTRYPOINT` tried to run `/entrypoint` which doesn't exist.
**Fix**: Switched to native `/etc/entrypoint.d/` system.

## Issue 2: "No available server" (Port Mismatch)
**Symptoms**: Container runs but Coolify shows "No available server".
**Cause**: `serversideup/php` listens on port 8080, config assumed 80.
**Fix**: Updated Dockerfile/Compose to use port 8080.

## Issue 3: MySQL SSL Error (Current Blocker)
**Symptoms**: `migrate.sh` stuck in loop, app never starts.
**Error**: `ERROR 2026 (HY000): TLS/SSL error: self-signed certificate in certificate chain`
**Cause**: MySQL 9.2 enables SSL by default. The client (likely MariaDB-based) needs explicit flags to ignore this.
**Fix**: Updated `migrate.sh` to use `--skip-ssl` (more compatible than `--ssl-mode=DISABLED`).

## Issue 4: 404 Healthcheck Error (Current Blocker)
**Symptoms**: App starts but Healthcheck fails with 404. Coolify says "No available server".
**Cause**: `serversideup/php` defaults to serving from `public/` (Laravel style). Our app is in the root.
**Fix**: Added `ENV NGINX_WEBROOT=/var/www/html` to Dockerfile.

## Issue 5: Gateway Timeout (PHP Connection)
**Symptoms**: Healthcheck passes, but accessing site gives 504 Gateway Timeout.
**Cause**: `connect.php` had hardcoded `localhost` (wrong host) and deprecated `get_magic_quotes_gpc` (fatal error).
**Fix**: Updated `connect.php` to use environment variables (`DB_HOST`, etc.) and removed deprecated code.

## How to Deploy Now

1. **Push changes** to Git.
2. **Configure Coolify UI**:
   - **Domains**: Set to `https://survival.gingermedia.biz:8080`
     - *This tells Coolify to route traffic for this domain to port 8080 inside the container.*
   - **Ports Exposes**: You can leave this blank if you set the port in the domain field.
3. **Redeploy**.
4. **Verify**:
   - `migrate.sh` should now connect successfully.
   - App should start.
   - Site should be accessible.

## Local Development
- Run `docker-compose up`.
- Access site at `http://localhost:8080`.
