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
**Cause**: MySQL 9.2 enables SSL by default with self-signed certs. The MySQL client in the app container rejects this.
**Fix**: Updated `migrate.sh` to use `--ssl-mode=DISABLED`.

## How to Deploy Now

1. **Push changes** to Git.
2. **Redeploy** in Coolify.
3. **Verify**:
   - `migrate.sh` should now connect successfully.
   - App should start.
   - Site should be accessible.

## Local Development
- Run `docker-compose up`.
- Access site at `http://localhost:8080`.
