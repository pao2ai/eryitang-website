#!/usr/bin/env bash
set -euo pipefail

required_vars=(SITE_ROOT SITE_URL DB_NAME DB_USER DB_PASSWORD DB_HOST ADMIN_USER ADMIN_EMAIL ADMIN_PASSWORD)
for name in "${required_vars[@]}"; do
  if [[ -z "${!name:-}" ]]; then
    echo "Missing required environment variable: ${name}" >&2
    exit 1
  fi
done

for command_name in wp mysql php rsync sha256sum; do
  command -v "$command_name" >/dev/null 2>&1 || {
    echo "Required command not found: $command_name" >&2
    exit 1
  }
done

WP_BIN="$(command -v wp)"
wp_cli() {
  php -d "memory_limit=${WP_CLI_MEMORY_LIMIT:-512M}" "$WP_BIN" "$@"
}

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"

(cd "$REPO_ROOT" && sha256sum -c MANIFEST.sha256)

mkdir -p "$SITE_ROOT"
if [[ -n "$(find "$SITE_ROOT" -mindepth 1 -maxdepth 1 -print -quit)" ]]; then
  echo "SITE_ROOT is not empty; refusing to overwrite: $SITE_ROOT" >&2
  exit 1
fi

existing_tables="$(MYSQL_PWD="$DB_PASSWORD" mysql -N -h "$DB_HOST" -u "$DB_USER" -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='${DB_NAME}'")"
database_exists="$(MYSQL_PWD="$DB_PASSWORD" mysql -N -h "$DB_HOST" -u "$DB_USER" -e "SELECT COUNT(*) FROM information_schema.schemata WHERE schema_name='${DB_NAME}'")"
if [[ "$database_exists" != "1" ]]; then
  echo "Database does not exist or is not visible to DB_USER: $DB_NAME" >&2
  exit 1
fi
if [[ "$existing_tables" != "0" ]]; then
  echo "Database is not empty; refusing to overwrite: $DB_NAME" >&2
  exit 1
fi

wp_cli core download --version=7.1 --locale=zh_CN --path="$SITE_ROOT" --allow-root
wp_cli core verify-checksums --version=7.1 --locale=zh_CN --path="$SITE_ROOT" --allow-root

rsync -a "$REPO_ROOT/site/wp-content/" "$SITE_ROOT/wp-content/"

wp_cli config create \
  --path="$SITE_ROOT" \
  --dbname="$DB_NAME" \
  --dbuser="$DB_USER" \
  --dbpass="$DB_PASSWORD" \
  --dbhost="$DB_HOST" \
  --dbprefix=wp_ \
  --skip-check \
  --force \
  --allow-root

wp_cli db import "$REPO_ROOT/database/users-schema.sql" --path="$SITE_ROOT" --allow-root
wp_cli db import "$REPO_ROOT/database/production-content.sql" --path="$SITE_ROOT" --allow-root

wp_cli user create "$ADMIN_USER" "$ADMIN_EMAIL" \
  --role=administrator \
  --user_pass="$ADMIN_PASSWORD" \
  --path="$SITE_ROOT" \
  --allow-root

test "$(wp_cli user get "$ADMIN_USER" --field=ID --path="$SITE_ROOT" --allow-root)" = "1"

wp_cli option update home "$SITE_URL" --path="$SITE_ROOT" --allow-root
wp_cli option update siteurl "$SITE_URL" --path="$SITE_ROOT" --allow-root
wp_cli option update admin_email "$ADMIN_EMAIL" --path="$SITE_ROOT" --allow-root
wp_cli theme activate eryitang --path="$SITE_ROOT" --allow-root
wp_cli plugin activate eryitang-core --path="$SITE_ROOT" --allow-root
wp_cli rewrite structure '/%postname%/' --hard --path="$SITE_ROOT" --allow-root
wp_cli cache flush --path="$SITE_ROOT" --allow-root

find "$SITE_ROOT" -type d -exec chmod 755 {} +
find "$SITE_ROOT" -type f -exec chmod 644 {} +

wp_cli core is-installed --path="$SITE_ROOT" --allow-root
wp_cli core verify-checksums --version=7.1 --locale=zh_CN --path="$SITE_ROOT" --allow-root

echo "Restore completed. Configure Nginx/TLS, then run scripts/verify-restored-site.sh."
