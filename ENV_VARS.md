# Coolify Environment Variables

This application uses minimal environment variable configuration for easy deployment on Coolify.

## Quick Start (Zero Configuration for Testing)

For quick testing, you can deploy **without setting any variables**:
- `DB_PASS` defaults to `changeme` (⚠️ change in production!)
- `SERVICE_FQDN_APP_8080` is auto-set by Coolify

## Recommended Setup (Production)

### Required Variables (Set in Coolify UI)

- **`DB_PASS`** - Your secure MySQL password (e.g., `your_secure_password_here`)
  - Default: `changeme` (only for testing!)
  - **⚠️ MUST be changed for production**

### Optional (but recommended)
- **`ADMIN_EMAIL`** - Admin email address for notifications

## Automatic Variables (Provided by Coolify)

Coolify automatically sets these - **no manual configuration needed**:

- **`SERVICE_FQDN_APP_8080`** - Your application's domain (e.g., `https://survival-war.yourdomain.com`)
  - Used for `SITE_URL`
  - Coolify sets this based on your domain configuration

## All Available Variables

### Database
- `DB_HOST` - Database hostname (default: `mysql`)
- `DB_NAME` - Database name (default: `survival_war`)
- `DB_USER` - Database user (default: `root`)
- `DB_PASS` - Database password (⚠️ **REQUIRED**)

### Site
- `SITE_URL` - Site URL (auto: `SERVICE_FQDN_APP_8080`)
- `SITE_NAME` - Site name (default: `Survival War`)
- `ADMIN_EMAIL` - Admin email (recommended to set)

### Security
- `SESSION_LIFETIME` - Session duration in seconds (default: `7200`)
- `PASSWORD_MIN_LENGTH` - Minimum password length (default: `8`)

### Debug/Development
- `DEBUG_MODE` - Enable debug mode (default: `false`)
- `DISPLAY_ERRORS` - Display PHP errors (default: `false`)
- `LOG_ERRORS` - Log errors to file (default: `true`)

## Coolify Setup Steps

### Absolute Minimum (Zero Config Deploy)

1. **Add Repository** in Coolify
2. **Deploy** 🚀

That's it! The app will use:
- `DB_PASS=changeme` (default password)
- `SERVICE_FQDN_APP_8080` (auto-set by Coolify)

⚠️ **Warning**: The default password is NOT secure. Change it for production!

### Minimal Setup (1-2 variables)

1. **Add Repository** in Coolify
2. **Set Environment Variables**:
   ```
   DB_PASS=your_secure_password_here
   ADMIN_EMAIL=your@email.com
   ```
3. **Deploy** 🚀

That's it! Coolify will automatically:
- Set `SERVICE_FQDN_APP_8080` to your domain
- Build the Docker image
- Start MySQL and apply migrations
- Start the application

### Recommended Production Setup

Add these for production hardening:

```env
# Required
DB_PASS=very_secure_random_password_here
ADMIN_EMAIL=admin@yourdomain.com

# Optional - Production Hardening
SESSION_LIFETIME=3600
PASSWORD_MIN_LENGTH=12
DEBUG_MODE=false
DISPLAY_ERRORS=false
```

## Local Development

For local development with docker-compose:

```bash
# Create .env.docker file
cat > .env.docker << EOF
DB_PASS=cdcdcd10
ADMIN_EMAIL=developer@localhost
DEBUG_MODE=true
DISPLAY_ERRORS=true
EOF

# Run with environment file
docker-compose --env-file .env.docker up
```

Or simply run without `.env` file - defaults will be used:

```bash
docker-compose up
```

## Verification

After deployment, check if variables are set correctly:

```bash
# View Coolify logs to see the SITE_URL being used
# It should show: SERVICE_FQDN_APP_8080=https://your-domain.com

# Or exec into container
docker exec -it survival-war-app env | grep -E '(DB_|SITE_|SERVICE_FQDN)'
```
