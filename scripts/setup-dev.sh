#!/usr/bin/env bash
# setup-dev.sh — bootstrap a local development environment for WP Warranty Manager
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
PLUGIN_DIR="$REPO_ROOT/wp-warranty-manager"

# ── Colours ──────────────────────────────────────────────────────────────────
RED='\033[0;31m'; GREEN='\033[0;32m'; YELLOW='\033[1;33m'; NC='\033[0m'
ok()   { echo -e "${GREEN}✔${NC}  $*"; }
warn() { echo -e "${YELLOW}⚠${NC}  $*"; }
err()  { echo -e "${RED}✘${NC}  $*" >&2; }

echo ""
echo "WP Warranty Manager — dev setup"
echo "================================"
echo ""

# ── 1. Check PHP ─────────────────────────────────────────────────────────────
if ! command -v php &>/dev/null; then
  err "PHP not found. Install PHP 8.0+ and re-run."
  exit 1
fi
PHP_VER=$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;')
PHP_MAJOR=$(php -r 'echo PHP_MAJOR_VERSION;')
PHP_MINOR=$(php -r 'echo PHP_MINOR_VERSION;')
if [[ "$PHP_MAJOR" -lt 8 ]]; then
  err "PHP 8.0+ required (found $PHP_VER)."
  exit 1
fi
ok "PHP $PHP_VER"

# ── 2. PHP syntax check ───────────────────────────────────────────────────────
echo ""
echo "Running PHP syntax check..."
ERRORS=0
while IFS= read -r -d '' file; do
  if ! php -l "$file" &>/dev/null; then
    err "Syntax error: $file"
    php -l "$file" || true
    ERRORS=$((ERRORS + 1))
  fi
done < <(find "$PLUGIN_DIR" -name "*.php" -print0)
if [[ $ERRORS -eq 0 ]]; then
  ok "All PHP files pass syntax check"
else
  err "$ERRORS file(s) have syntax errors — fix before continuing."
  exit 1
fi

# ── 3. Detect local WordPress environment ────────────────────────────────────
echo ""
echo "Local WordPress environment"
echo "---------------------------"

WP_ENV_AVAILABLE=false
WP_CLI_AVAILABLE=false
DOCKER_AVAILABLE=false

command -v node &>/dev/null && command -v npm &>/dev/null && WP_ENV_AVAILABLE=true
command -v wp &>/dev/null && WP_CLI_AVAILABLE=true
command -v docker &>/dev/null && DOCKER_AVAILABLE=true

if $WP_ENV_AVAILABLE; then
  ok "Node + npm found — wp-env is available"
  echo ""
  echo "  To start a local WordPress environment:"
  echo "    npm install -g @wordpress/env   # first time only"
  echo "    cd \"$REPO_ROOT\""
  echo "    wp-env start"
  echo ""
  echo "  Then activate the plugin:"
  echo "    wp-env run cli wp plugin activate wp-warranty-manager"
  echo ""
  echo "  WordPress will be at http://localhost:8888"
  echo "  Admin:  http://localhost:8888/wp-admin  (admin / password)"
fi

if $WP_CLI_AVAILABLE; then
  ok "WP-CLI found"
fi

if ! $WP_ENV_AVAILABLE && ! $WP_CLI_AVAILABLE; then
  warn "Neither wp-env nor WP-CLI found."
  echo ""
  echo "  Manual setup:"
  echo "  1. Install WordPress locally (Local, MAMP, Docker, etc.)"
  echo "  2. Symlink or copy the plugin directory:"
  echo "       cp -r \"$PLUGIN_DIR\" /path/to/wordpress/wp-content/plugins/"
  echo "  3. Activate from WordPress Admin → Plugins"
fi

# ── 4. Optional: PHPCS ───────────────────────────────────────────────────────
echo ""
echo "Optional: PHP_CodeSniffer"
echo "--------------------------"
if command -v phpcs &>/dev/null; then
  ok "phpcs found"
elif command -v composer &>/dev/null; then
  warn "phpcs not found. Install with:"
  echo "    composer require --dev squizlabs/php_codesniffer wp-coding-standards/wpcs"
  echo "    ./vendor/bin/phpcs --standard=WordPress wp-warranty-manager/"
else
  warn "phpcs and composer not found — coding-standard checks unavailable."
fi

echo ""
echo "Claude Code"
echo "-----------"
echo "  This project uses global Claude Code skills that must be installed per machine:"
echo "    • Caveman  (/caveman)          — token-efficient communication mode"
echo "    • Frontend Design (/frontend-design) — UI/CSS/JS guidance"
echo "  Install via: claude skills install (see https://claude.ai/code)"
echo ""
echo "Setup complete."
echo ""
