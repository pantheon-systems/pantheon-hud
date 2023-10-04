#!/bin/bash
set -e

# Set TMPDIR to /tmp.
TMPDIR="/tmp"

# Initialize a variable to hold the 'true' flag for skipping DB creation
SKIP_DB=""

# Check if '--no-db' is passed as an argument
if [[ "$1" == "--no-db" ]]; then
  SKIP_DB="true"
fi

# Run install-wp-tests.sh
echo "Installing local tests into ${TMPDIR}"
bash "$(dirname "$0")/install-wp-tests.sh" wordpress_test root '' 127.0.0.1 latest $SKIP_DB

# Run PHPUnit
echo "Running PHPUnit"
composer phpunit
