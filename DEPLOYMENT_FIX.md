# Coolify Deployment Fixes

## Issue 1: "No available server" (Entrypoint Crash)
**Symptoms**: Container crashing immediately.
**Cause**: Custom `ENTRYPOINT` tried to run `/entrypoint` which doesn't exist.
**Fix**: Switched to native `/etc/entrypoint.d/` system.

## Issue 2: "No available server" (Port Mismatch)
**Symptoms**: Container runs but Coolify shows "No available server".
**Cause**: 
- `serversideup/php` image listens on **port 8080** (unprivileged default).
- Our config assumed **port 80**.
- Healthcheck failed (`curl localhost/login.php` -> connection refused).
- Traefik couldn't route traffic because it looks for exposed ports.

**Fix**:
- Updated `Dockerfile` to `EXPOSE 8080`.
- Updated Healthchecks to check `http://localhost:8080/login.php`.
- Updated local dev ports to `8080:8080`.

## Issue 3: MySQL Healthcheck
**Symptoms**: MySQL container unhealthy.
**Cause**: Empty password handling.
**Fix**: Updated healthcheck command and set default password.

## How to Deploy Now

1. **Push changes** to Git.
2. **Redeploy** in Coolify.
3. **Verify**:
   - App should be healthy (Healthcheck passing on port 8080).
   - Coolify should detect port 8080 automatically.
   - Site should be accessible.

## Local Development
- Run `docker-compose up`.
- Access site at `http://localhost:8080`.
