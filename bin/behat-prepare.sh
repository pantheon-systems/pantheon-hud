#!/bin/bash

###
# Prepare a Pantheon site environment for the Behat test suite, by installing
# and configuring the plugin for the environment. This script is architected
# such that it can be run a second time if a step fails.
###

terminus whoami > /dev/null
if [ $? -ne 0 ]; then
	echo "Terminus unauthenticated; assuming unauthenticated build"
	exit 0
fi

if [ -z "$TERMINUS_SITE" ] || [ -z "$TERMINUS_ENV" ]; then
	echo "TERMINUS_SITE and TERMINUS_ENV environment variables must be set"
	exit 1
fi

if [ -z "$WORDPRESS_ADMIN_USERNAME" ] || [ -z "$WORDPRESS_ADMIN_PASSWORD" ]; then
	echo "WORDPRESS_ADMIN_USERNAME and WORDPRESS_ADMIN_PASSWORD environment variables must be set"
	exit 1
fi

set -ex

###
# Cleanup old CI multidev environments to avoid hitting the limit.
# Keep the 2 most recent and delete the rest.
###
echo "Cleaning up old CI multidev environments..."
terminus multidev:list $TERMINUS_SITE --format=list --field=id 2>/dev/null | grep "^ci-" | sort -r | tail -n +3 | while read -r env; do
  echo "Deleting old environment: $env"
  terminus multidev:delete $TERMINUS_SITE.$env --delete-branch --yes || echo "Failed to delete $env, continuing..."
done

###
# Check for and apply any outstanding upstream updates.
# This never happens manually, so we might as well do it in automation before we run tests.
###
terminus connection:set $TERMINUS_SITE.dev git
updates=$(terminus upstream:updates:list "$TERMINUS_SITE.dev")
if echo "$updates" | grep -q "There are no available updates for this site."; then
  echo "No upstream updates to apply."
else
  terminus upstream:updates:apply "$TERMINUS_SITE.dev" --accept-upstream
fi

###
# Create a new environment for this particular test run.
###
terminus env:create "${TERMINUS_SITE}.dev" "$TERMINUS_ENV"
terminus env:wipe "$SITE_ENV" --yes

###
# Get all necessary environment details.
###
PANTHEON_GIT_URL=$(terminus connection:info $SITE_ENV --field=git_url)
PANTHEON_SITE_URL="$TERMINUS_ENV-$TERMINUS_SITE.pantheonsite.io"
PREPARE_DIR="/tmp/$TERMINUS_ENV-$TERMINUS_SITE"
BASH_DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

PHP_VERSION="$(terminus env:info $SITE_ENV --field=php_version)"
echo "PHP Version: $PHP_VERSION"

###
# Switch to git mode for pushing the files up
###
rm -rf $PREPARE_DIR
git clone -b $TERMINUS_ENV $PANTHEON_GIT_URL $PREPARE_DIR

###
# Add the copy of this plugin itself to the environment
###
rm -rf $PREPARE_DIR/wp-content/plugins/pantheon-hud
cd $BASH_DIR/..
rsync -av --exclude='vendor/' --exclude='node_modules/' --exclude='tests/' ./* $PREPARE_DIR/wp-content/plugins/pantheon-hud
rm -rf $PREPARE_DIR/wp-content/plugins/pantheon-hud/.git

###
# Push files to the environment
###
cd $PREPARE_DIR
git add wp-content
git config user.email "pantheon-hud@getpantheon.com"
git config user.name "Pantheon"
git commit -m "Include Pantheon HUD and its configuration files"
git push

# Sometimes Pantheon takes a little time to refresh the filesystem
terminus workflow:wait $TERMINUS_SITE.$TERMINUS_ENV

###
# Update WordPress core to ensure PHP 8.2 compatibility
###
echo "Updating WordPress core to latest version..."
terminus wp $SITE_ENV -- core update --force || echo "WordPress core update failed, continuing..."

###
# Set up WordPress, theme, and plugins for the test run
###

# Retry WP core install as the environment may take a moment to be ready.
max_attempts=5
attempt_num=1
until terminus wp $SITE_ENV -- core install --title=$TERMINUS_ENV-$TERMINUS_SITE --url=$PANTHEON_SITE_URL --admin_user=$WORDPRESS_ADMIN_USERNAME --admin_email=pantheon-hud@getpantheon.com --admin_password=$WORDPRESS_ADMIN_PASSWORD; do
  if [ $attempt_num -eq $max_attempts ]; then
    echo "WP core install failed after $max_attempts attempts."
    exit 1
  fi
  echo "WP core install failed. Retrying in 15 seconds... (Attempt $attempt_num of $max_attempts)"
  sleep 15
  attempt_num=$((attempt_num+1))
done

terminus wp $SITE_ENV -- cache flush
terminus wp $SITE_ENV -- plugin activate pantheon-hud
terminus wp $SITE_ENV -- theme activate twentytwentythree
terminus wp $SITE_ENV -- rewrite structure '/%year%/%monthnum%/%day%/%postname%/'
