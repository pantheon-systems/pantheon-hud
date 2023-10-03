#!/bin/bash
set -e

# Check if TMPDIR is set; if not, default to /tmp
TMPDIR="/tmp"

# Explicitly set WP_TESTS_DIR based on TMPDIR
export WP_TESTS_DIR="${TMPDIR}/wordpress-tests-lib"
export WP_CORE_DIR="${TMPDIR}/wordpress/"

# Initialize a variable to hold the 'true' flag for skipping DB creation
SKIP_DB=""

# Check if '--no-db' is passed as an argument
if [[ "$1" == "--no-db" ]]; then
  SKIP_DB="true"
fi

# Run install-wp-tests.sh
echo "Installing local tests into ${WP_TESTS_DIR}"
bash "$(dirname "$0")/install-wp-tests.sh" wordpress_test root '' 127.0.0.1 latest $SKIP_DB

# Run PHPUnit
echo "Running PHPUnit"
composer phpunit --no-cache
