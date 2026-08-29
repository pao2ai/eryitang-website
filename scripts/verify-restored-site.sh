#!/usr/bin/env bash
set -euo pipefail

: "${SITE_ROOT:?SITE_ROOT is required}"
: "${SITE_URL:?SITE_URL is required}"

WP_BIN="$(command -v wp)"
wp_cli() {
  php -d "memory_limit=${WP_CLI_MEMORY_LIMIT:-512M}" "$WP_BIN" "$@"
}

wp_cli core is-installed --path="$SITE_ROOT" --allow-root
wp_cli core verify-checksums --version=7.1 --locale=zh_CN --path="$SITE_ROOT" --allow-root

test "$(wp_cli option get template --path="$SITE_ROOT" --allow-root)" = "eryitang"
wp_cli plugin is-active eryitang-core --path="$SITE_ROOT" --allow-root

post_count="$(wp_cli post list --post_type=post --post_status=publish --format=count --path="$SITE_ROOT" --allow-root)"
page_count="$(wp_cli post list --post_type=page --post_status=publish --format=count --path="$SITE_ROOT" --allow-root)"
media_count="$(wp_cli post list --post_type=attachment --post_status=inherit --format=count --path="$SITE_ROOT" --allow-root)"

echo "published_posts=$post_count"
echo "published_pages=$page_count"
echo "media_attachments=$media_count"

for route in / /brand/ /contact/ /article/ /wp-sitemap.xml /llms.txt; do
  status="$(curl -L -sS -o /dev/null -w '%{http_code}' "${SITE_URL%/}${route}")"
  if [[ "$status" != "200" ]]; then
    echo "HTTP check failed: $route -> $status" >&2
    exit 1
  fi
  echo "HTTP 200 $route"
done

home_html="$(curl -L -sS "${SITE_URL%/}/")"
grep -q '蜀ICP备2026043530号' <<<"$home_html"
grep -q 'application/ld+json' <<<"$home_html"
grep -q 'https://beian.miit.gov.cn/' <<<"$home_html"

echo "Restored site verification passed."
