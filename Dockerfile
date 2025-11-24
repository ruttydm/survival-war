# =============================================================================
# DOCKERFILE - Educational Guide
# =============================================================================
# This Dockerfile defines how to build a Docker image for the Survival War app.
# Think of it as a recipe that creates a snapshot of your application with all
# its dependencies. This image can then be run as a container anywhere.

# -----------------------------------------------------------------------------
# BASE IMAGE SELECTION
# -----------------------------------------------------------------------------
# FROM: This is always the first instruction. It tells Docker which base image
# to start from. Think of it like choosing a foundation for your house.
# 
# We're using "serversideup/php:8.5-fpm-nginx" which includes:
# - PHP 8.5 (the programming language version we need)
# - PHP-FPM (FastCGI Process Manager - handles PHP requests efficiently)
# - Nginx (web server that serves your files and forwards PHP requests to FPM)
# - Pre-configured for production use with security best practices
#
# "AS base" names this build stage. Docker supports multi-stage builds where
# you can have multiple FROM statements and copy artifacts between stages.
FROM serversideup/php:8.5-fpm-nginx AS base

# -----------------------------------------------------------------------------
# WORKING DIRECTORY
# -----------------------------------------------------------------------------
# WORKDIR: Sets the working directory inside the container. All subsequent
# commands (RUN, COPY, etc.) will be executed from this directory.
# If the directory doesn't exist, Docker creates it automatically.
#
# This is like doing "cd /var/www/html" before running commands.
# "/var/www/html" is the standard web root directory in Linux web servers.
WORKDIR /var/www/html

# -----------------------------------------------------------------------------
# INSTALLING SYSTEM PACKAGES
# -----------------------------------------------------------------------------
# RUN: Executes commands inside the container during the build process.
# Each RUN instruction creates a new "layer" in your image.
#
# USER root: Temporarily switch to root user to install system packages.
# (The base image runs as a non-root user by default for security)
USER root

# apt-get update: Refreshes the package list (like checking for app updates)
# apt-get install -y: Installs packages (-y automatically says "yes" to prompts)
# 
# Why install these packages?
# - cron: Task scheduler for running periodic jobs (turn regeneration)
# - curl: Tool to make HTTP requests (used in health checks)
# - default-mysql-client: MySQL command-line tool (needed to run migrations)
#
# && \ : Chains commands together. All commands in one RUN = one layer (smaller image)
# apt-get clean: Removes downloaded package files to save space
# rm -rf /var/lib/apt/lists/*: Deletes package lists (no longer needed after install)
RUN apt-get update && \
    apt-get install -y cron curl default-mysql-client && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# -----------------------------------------------------------------------------
# COPYING APPLICATION FILES
# -----------------------------------------------------------------------------
# COPY: Copies files from your local machine (build context) into the container.
# Format: COPY <source on host> <destination in container>
#
# --chown=www-data:www-data: Sets the owner of copied files
# - www-data: Default web server user in Linux (non-root for security)
# - First www-data = user, second = group
# 
# . /var/www/html: Copy everything from current directory to /var/www/html
# The . represents the "build context" (usually the directory with Dockerfile)
COPY --chown=www-data:www-data . /var/www/html

# -----------------------------------------------------------------------------
# SETTING UP MIGRATION SCRIPT
# -----------------------------------------------------------------------------
# Copy migration script to /etc/entrypoint.d/ so it runs automatically on startup
# The "99-" prefix ensures it runs late in the initialization process
COPY migrate.sh /etc/entrypoint.d/99-migrate.sh
RUN chmod +x /etc/entrypoint.d/99-migrate.sh

# -----------------------------------------------------------------------------
# CREATING DIRECTORIES AND SETTING PERMISSIONS
# -----------------------------------------------------------------------------
# mkdir -p: Creates directory and any missing parent directories
# -p flag: "Make parent directories as needed" (like mkdir -p a/b/c creates all three)
#
# chown -R: Change ownership recursively (R = all files and subdirectories)
# chmod -R 775: Set permissions recursively
#   7 (owner): read(4) + write(2) + execute(1) = full access
#   7 (group): read(4) + write(2) + execute(1) = full access  
#   5 (others): read(4) + execute(1) = read and enter directory only
#
# Why? PHP-FPM (running as www-data) needs to write log files here
RUN mkdir -p /var/www/html/logs && \
    chown -R www-data:www-data /var/www/html/logs && \
    chmod -R 775 /var/www/html/logs

# Set proper permissions for the entire application
# chown -R: Make www-data own all application files
# find ... -type f -exec chmod 644: Find all files and set to 644
#   6 (owner): read(4) + write(2) = can read and modify
#   4 (group): read(4) = can only read
#   4 (others): read(4) = can only read
# find ... -type d -exec chmod 755: Find all directories and set to 755
#   7 (owner): read + write + execute = full access
#   5 (group/others): read + execute = can list and enter, but not modify
#
# Why different permissions for files vs directories?
# - Files don't need execute permission (they're not programs)
# - Directories need execute permission to be entered (cd into them)
RUN chown -R www-data:www-data /var/www/html && \
    find /var/www/html -type f -exec chmod 644 {} \; && \
    find /var/www/html -type d -exec chmod 755 {} \;

# -----------------------------------------------------------------------------
# SETTING UP CRON JOBS
# -----------------------------------------------------------------------------
# Make the cron script executable so it can be run
RUN chmod +x /var/www/html/cron/cronjob.php

# COPY and set up the crontab file
COPY crontab /etc/cron.d/survival-war
RUN chmod 0644 /etc/cron.d/survival-war && \
    crontab /etc/cron.d/survival-war

# Copy cron startup script to /etc/entrypoint.d/
COPY start-cron.sh /etc/entrypoint.d/98-start-cron.sh
RUN chmod +x /etc/entrypoint.d/98-start-cron.sh

# -----------------------------------------------------------------------------
# HEALTH CHECK
# -----------------------------------------------------------------------------
# HEALTHCHECK: Tells Docker how to test if the container is working properly.
# Docker will run this command periodically and mark the container as:
# - healthy: command exits with 0
# - unhealthy: command exits with 1 (or non-zero)
#
# Flags explained:
# --interval=30s: Run the check every 30 seconds
# --timeout=3s: If check takes > 3 seconds, it's considered failed
# --start-period=40s: Give container 40 seconds to start before checking
# --retries=3: Mark unhealthy only after 3 consecutive failures
#
# CMD: The actual command to run
# curl -f: Fetch URL and exit with error if HTTP status is not 2xx
# http://localhost/login.php: Check if the login page loads
# || exit 1: If curl fails, exit with code 1 (unhealthy)
#
# Why this matters:
# - Load balancers can route traffic away from unhealthy containers
# - Docker Compose can wait for services to be healthy before starting dependents
# - You can see health status in `docker ps`
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/login.php || exit 1

# -----------------------------------------------------------------------------
# EXPOSING PORTS
# -----------------------------------------------------------------------------
# EXPOSE: Documents which ports the container listens on. This is INFORMATIONAL
# only - it doesn't actually publish the port. Think of it as documentation.
#
# To actually make ports accessible, you need:
# - docker run -p 8080:80 (maps host port 8080 to container port 80)
# - ports: in docker-compose.yml
#
# Port 80: HTTP (web traffic)
# Port 443: HTTPS (encrypted web traffic)
#
# Note: In Coolify, ports are managed by the proxy, so we don't publish them
EXPOSE 80 443
