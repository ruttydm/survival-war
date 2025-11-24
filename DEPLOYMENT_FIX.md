# Coolify Deployment Fix - MySQL Healthcheck

## Issue
MySQL container was failing healthcheck in Coolify, preventing app from starting.

**Error**: `dependency failed to start: container mysql-xxx is unhealthy`

## Root Cause
The healthcheck command required a password but `DB_PASS` had an empty default value:
```yaml
DB_PASS: ${DB_PASS:-}  # Empty default
```

When `DB_PASS` is not set in Coolify, MySQL gets an empty password, but the healthcheck command fails because it can't authenticate.

## Solution

### 1. Fixed Healthcheck
Changed MySQL healthcheck to handle both empty and non-empty passwords:

```yaml
healthcheck:
  test: ["CMD-SHELL", "mysqladmin ping -h localhost -u root $$([ -n \"$$MYSQL_ROOT_PASSWORD\" ] && echo \"-p$$MYSQL_ROOT_PASSWORD\" || echo \"\") || exit 1"]
```

This conditional logic:
- Checks if `MYSQL_ROOT_PASSWORD` is set
- Adds `-p$$MYSQL_ROOT_PASSWORD` if it exists
- Omits password flag if empty

### 2. Added Default Password
Changed default from empty to `changeme`:

```yaml
DB_PASS: ${DB_PASS:-changeme}
```

This allows zero-config deployment for testing while remaining obvious that it should be changed.

## Deploy to Coolify

### Zero Config (Testing Only)
Just deploy - uses `DB_PASS=changeme` by default.

### Production Setup
Set one variable in Coolify:
```
DB_PASS=your_secure_password_here
```

## Verification
After deploying, check container health:
```bash
docker ps
# Look for "healthy" status on both containers
```

View logs:
```bash
docker logs mysql-[container-id]
# Should show MySQL started successfully
```
