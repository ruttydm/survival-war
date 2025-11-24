#!/bin/bash
set -e

echo "=== Survival War Migration Runner ==="
echo "Waiting for MySQL to be ready..."

# Wait for MySQL to be healthy
until mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" --ssl-mode=DISABLED -e "SELECT 1" > /dev/null 2>&1; do
  echo "MySQL is unavailable - sleeping"
  sleep 2
done

echo "MySQL is ready!"

# Check if schema needs to be initialized
TABLE_COUNT=$(mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" --ssl-mode=DISABLED "${DB_NAME}" -sNe "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}'")

if [ "$TABLE_COUNT" -eq "0" ]; then
  echo "Database is empty. Running initial schema..."
  mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" --ssl-mode=DISABLED "${DB_NAME}" < /var/www/html/sql.sql
  echo "✓ Initial schema imported"
else
  echo "Database already has ${TABLE_COUNT} tables. Skipping initial schema."
fi

# Always run migrations (they should be idempotent or check if already applied)
echo "Running migrations..."
for migration in /var/www/html/migrations/*.sql; do
  if [ -f "$migration" ]; then
    echo "Running $(basename $migration)..."
    mysql -h"${DB_HOST}" -u"${DB_USER}" -p"${DB_PASS}" --ssl-mode=DISABLED "${DB_NAME}" < "$migration" || {
      echo "⚠ Warning: Migration $(basename $migration) encountered an issue (this is OK if already applied)"
    }
  fi
done

echo "✓ Migrations completed"
echo "=== Database setup complete ==="
