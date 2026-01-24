#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR=${MAHO_ROOT_DIR:-$(pwd)}
ENV_FILE="$ROOT_DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
  echo "ERROR: .env not found at $ENV_FILE"
  exit 2
fi

# Helper to read simple KEY=VALUE lines (handles optional quotes)
get_env() {
  local key="$1"
  local line
  line=$(grep -E "^${key}=" "$ENV_FILE" | tail -n1 || true)
  # Remove KEY= prefix
  line=${line#*=}
  # Strip surrounding matching quotes (" or ')
  if [ "${line:0:1}" = '"' ] && [ "${line: -1}" = '"' ]; then
    line="${line:1:-1}"
  elif [ "${line:0:1}" = "'" ] && [ "${line: -1}" = "'" ]; then
    line="${line:1:-1}"
  fi
  printf '%s' "$line"
}

MAHO_DB_ENGINE=$(get_env MAHO_DB_ENGINE)
MAHO_DB_NAME=$(get_env MAHO_DB_NAME)
MAHO_DB_HOST=$(get_env MAHO_DB_HOST)
MAHO_DB_USER=$(get_env MAHO_DB_USER)
MAHO_DB_PASS=$(get_env MAHO_DB_PASS)
MAHO_DB_PREFIX=$(get_env MAHO_DB_PREFIX)

export MAHO_DB_ENGINE MAHO_DB_NAME MAHO_DB_HOST MAHO_DB_USER MAHO_DB_PASS MAHO_DB_PREFIX MAHO_ROOT_DIR=$ROOT_DIR

echo "Using MAHO_ROOT_DIR=$ROOT_DIR"
echo "MAHO_DB_ENGINE=$MAHO_DB_ENGINE"
echo "MAHO_DB_NAME=$MAHO_DB_NAME"

if [ "$MAHO_DB_ENGINE" = "sqlite" ]; then
  if [ "$MAHO_DB_NAME" = ":memory:" ]; then
    echo "SQLite in-memory DB; cannot inspect file directly. Will attempt bootstrap check."
  else
    if [[ "$MAHO_DB_NAME" = /* ]]; then
      DB_PATH="$MAHO_DB_NAME"
    else
      DB_PATH="$ROOT_DIR/var/db/$MAHO_DB_NAME"
    fi
    echo "Checking sqlite DB at $DB_PATH"
    if [ -f "$DB_PATH" ]; then
      echo "DB file exists: $(stat -c '%A %u:%g %s bytes' "$DB_PATH")"
      if command -v sqlite3 >/dev/null 2>&1; then
        echo "Query: core_config_data -> global/install/date"
        sqlite3 "$DB_PATH" "SELECT path, value FROM core_config_data WHERE path='global/install/date' LIMIT 1;" || true
      else
        echo "sqlite3 not available in PATH"
      fi
    else
      echo "DB file not found: $DB_PATH"
    fi
  fi
fi

# Try to bootstrap Mage::app() via PHP CLI and report Mage::isInstalled() and any errors
echo -e "\nAttempting to bootstrap Mage::app() via PHP CLI (this will use exported MAHO_DB_* vars)..."
php -d display_errors=1 <<'PHP'
<?php
define('MAHO_ROOT_DIR', getenv('MAHO_ROOT_DIR') ?: '' );
if (!MAHO_ROOT_DIR) { echo "MAHO_ROOT_DIR not set\n"; exit(3); }
define('MAHO_PUBLIC_DIR', MAHO_ROOT_DIR . '/public');
require MAHO_ROOT_DIR . '/vendor/autoload.php';

try {
    Mage::app();
    echo "Mage::isInstalled() = " . (Mage::isInstalled() ? "true" : "false") . "\n";
    // Try reading install date via model if available
    $install = Mage::getModel("core/install_date");
    if ($install) {
        echo "Model core/install_date exists. get() => ";
        var_export($install->get());
        echo "\n";
    } else {
        echo "Model core/install_date not available\n";
    }
} catch (Throwable $e) {
    echo "Bootstrap failed: " . $e->getMessage() . "\n";
    exit(4);
}
PHP

exit 0
