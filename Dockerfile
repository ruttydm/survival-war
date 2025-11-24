FROM serversideup/php:8.5-fpm-nginx AS base

# Set working directory
WORKDIR /var/www/html

# Install cron and curl for health checks
USER root
RUN apt-get update && \
    apt-get install -y cron curl && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Copy application files
COPY --chown=www-data:www-data . /var/www/html

# Create logs directory with proper permissions
RUN mkdir -p /var/www/html/logs && \
    chown -R www-data:www-data /var/www/html/logs && \
    chmod -R 775 /var/www/html/logs

# Set proper permissions for the application
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    find /var/www/html -type d -exec chmod 755 {} \;

# Make cron script executable
RUN chmod +x /var/www/html/cron/cronjob.php

# Copy and set up cron job from crontab file
COPY crontab /etc/cron.d/survival-war
RUN chmod 0644 /etc/cron.d/survival-war && \
    crontab /etc/cron.d/survival-war

# Create entrypoint script to start cron and the main process
RUN echo '#!/bin/bash' > /usr/local/bin/docker-entrypoint.sh && \
    echo 'set -e' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Start cron in the background' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'cron' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '' >> /usr/local/bin/docker-entrypoint.sh && \
    echo '# Execute the original entrypoint' >> /usr/local/bin/docker-entrypoint.sh && \
    echo 'exec /entrypoint "$@"' >> /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/login.php || exit 1

# Expose ports
EXPOSE 80 443

# Use custom entrypoint to start cron and main process
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD []
