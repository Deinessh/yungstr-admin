# S7 MilletCo — Full Backup Restore Guide

## Contents
- `database.sql` — MySQL dump of the `s7millet` database
- `project/` — Laravel application files (code, uploads, .env, vendor)

## Restore on a new server

1. Extract this archive:
   ```bash
   tar -xzf s7millet-complete-backup.tar.gz
   ```

2. Copy project files:
   ```bash
   sudo cp -a project/* /var/www/s7millet/
   sudo chown -R www-data:www-data /var/www/s7millet/storage /var/www/s7millet/bootstrap/cache
   ```

3. Import database:
   ```bash
   mysql -u USER -p DATABASE_NAME < database.sql
   ```

4. Install dependencies (if vendor/node_modules missing):
   ```bash
   cd /var/www/s7millet
   composer install --no-dev
   npm ci && npm run build
   ```

5. Update `.env` for new server (APP_URL, DB_*, mail, payment keys).

6. Run:
   ```bash
   php artisan config:clear && php artisan cache:clear && php artisan storage:link
   ```

Backup created: 2026-06-19
