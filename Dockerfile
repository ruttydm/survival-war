FROM serversideup/php:8.5-fpm-nginx AS base

WORKDIR /var/www/html

# Set webroot to root directory (default is public/)
ENV NGINX_WEBROOT=/var/www/html

# Install system dependencies
USER root
RUN apt-get update && \
    apt-get install -y cron curl default-mysql-client && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Setup migration script
COPY migrate.sh /etc/entrypoint.d/99-migrate.sh
RUN chmod +x /etc/entrypoint.d/99-migrate.sh

# Setup directories and permissions
RUN mkdir -p /var/www/html/logs /var/www/html/temp/cache && \
    chown -R www-data:www-data /var/www/html/logs /var/www/html/temp && \
    chmod -R 775 /var/www/html/logs /var/www/html/temp

RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    find /var/www/html -type d -exec chmod 755 {} \;

# Setup cron
RUN chmod +x /var/www/html/cron/cronjob.php
COPY crontab /etc/cron.d/survival-war
RUN chmod 0644 /etc/cron.d/survival-war && \
    crontab /etc/cron.d/survival-war

COPY start-cron.sh /etc/entrypoint.d/98-start-cron.sh
RUN chmod +x /etc/entrypoint.d/98-start-cron.sh

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:8080/login.php || exit 1

# Expose ports (8080/8443 for unprivileged user)
EXPOSE 8080 8443
