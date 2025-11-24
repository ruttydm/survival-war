# Database Migration Strategy

## How Migrations Work in Docker

### Migration Flow

**On container startup**, the following happens automatically:

1. **Wait for MySQL** - Script polls until database is accessible
2. **Check Schema** - Counts existing tables in database
3. **Import Base Schema** - If database is empty, imports `sql.sql`
4. **Run Migrations** - Executes all files in `migrations/` directory
5. **Start Services** - Launches cron daemon and web server

### Migration Script

Location: [`migrate.sh`](file:///Users/rutger/Sites/survival-war/migrate.sh)

Key features:
- **Idempotent**: Safe to run multiple times
- **Error Handling**: Continues even if migration already applied
- **Logging**: Outputs status to console (captured in Docker logs)
- **Sequential**: Runs migrations in alphabetical order

### Docker Integration

The migration script is executed **automatically** when the container starts:

```dockerfile
# In Dockerfile:
- Installs MySQL client
- Copies migrate.sh to /usr/local/bin/
- Makes script executable

# In entrypoint:
1. Runs /usr/local/bin/migrate.sh
2. Starts cron daemon
3. Starts web server
```

### Current Migrations

1. **`sql.sql`** - Base schema (only if database empty)
   - Creates 6 tables: users, admins, battlerecords, forums, messages, monsters
   - Inserts sample data

2. **`migrations/001_modernization_schema.sql`**
   - Extends password field to 255 chars
   - Adds 11 performance indices
   - Updates for PHP 8.5 compatibility

### Adding New Migrations

To add a new migration:

1. Create `migrations/00X_description.sql`
2. Make it idempotent (use `IF NOT EXISTS`, `IF EXISTS`, etc.)
3. Rebuild and restart containers

Example idempotent migration:

```sql
-- Add new column only if it doesn't exist
ALTER TABLE km_users 
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL DEFAULT NULL;

-- Create index if not exists (MySQL 8.0.13+)
CREATE INDEX IF NOT EXISTS idx_last_login ON km_users(last_login);
```

### Manual Migration Execution

If you need to run migrations manually:

```bash
# Inside running container
docker exec -it survival-war-app /usr/local/bin/migrate.sh

# Or via docker-compose
docker-compose exec app /usr/local/bin/migrate.sh
```

### Troubleshooting

**View migration logs**:
```bash
docker-compose logs app | grep -A 20 "Migration Runner"
```

**Check if tables exist**:
```bash
docker-compose exec mysql mysql -u root -p${DB_PASSWORD} survival_war -e "SHOW TABLES;"
```

**Reset database** (WARNING: deletes all data):
```bash
docker-compose down -v  # Remove volumes
docker-compose up -d    # Recreate and run migrations
```

### Production (Coolify)

On Coolify, migrations run automatically:

1. Container starts
2. `migrate.sh` executes before web server starts
3. If database is new, schema is imported
4. All migrations are applied
5. Application becomes available

The `depends_on` and `healthcheck` in docker-compose.yml ensure MySQL is ready before migrations run.
