# Avatar Steward - Development Environment

## Language Policy
- All source code comments and documentation outside the `documentacion/` directory **must be written in English**.

## Prerequisites
- Docker Desktop (Compose v2 support)
- PHP 8.1+ with Composer
- Node.js 18+ with npm

## Initial Setup
1. Clone the repository and move to the project root.
2. Copy `.env.example` to `.env` and adjust variables if necessary.
3. Install PHP and JavaScript dependencies:
   ```bash
   composer install
   npm install
   ```

## Start the WordPress Environment
1. Ensure Docker is running.
2. Start the containers:
   ```bash
   docker compose -f docker-compose.dev.yml up -d
   ```
3. Access WordPress at `http://localhost:8080` and phpMyAdmin at `http://localhost:8081`.

## Plugin Management
- The plugin is mounted automatically from `src/` as `avatar-steward` inside WordPress.
- Main file: `src/avatar-steward.php`.
- Boot hook: `AvatarSteward\Plugin::instance()` triggers `do_action('avatarsteward/booted')` on load.

## Maintenance
- Stop containers:
  ```bash
  docker compose -f docker-compose.dev.yml down
  ```
- Run PHP linting:
  ```bash
  composer lint
  ```
- Execute PHP tests:
  ```bash
  composer test
  ```
