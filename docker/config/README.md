# Docker Configuration Files

This directory contains configuration files used by the Docker development environment.

## Files

### wp-config.php
WordPress configuration file with environment variable support for Docker.
This file is mounted as read-only into the WordPress container at `/var/www/html/wp-config.php`.

### theme-functions.php
Sample theme functions.php file from Twenty Twenty-Four theme.
This can be used for testing theme integration during development.

## Usage

These files are automatically mounted by `docker-compose.dev.yml`. You don't need to manually copy them.

See the main project README.md for Docker setup instructions.
