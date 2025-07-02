# Pantheon HUD #
**Contributors:** [getpantheon](https://profiles.wordpress.org/getpantheon/), [danielbachhuber](https://profiles.wordpress.org/danielbachhuber/), [jspellman](https://profiles.wordpress.org/jspellman/), [jazzs3quence](https://profiles.wordpress.org/jazzs3quence), [pwtyler](https://profiles.wordpress.org/pwtyler)  
**Tags:** Pantheon, hosting, environment-indicator  
**Requires at least:** 4.9  
**Tested up to:** 6.8.1
**Requires PHP:** 7.4
**Stable tag:** 0.4.5-dev  
**License:** GPLv2 or later  
**License URI:** http://www.gnu.org/licenses/gpl-2.0.html

A heads-up display into your Pantheon environment.

## Description ##

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/pantheon-systems/pantheon-hud/tree/main.svg?style=svg)](https://dl.circleci.com/status-badge/redirect/gh/pantheon-systems/pantheon-hud/tree/main)
[![Lint & Test](https://github.com/pantheon-systems/pantheon-hud/actions/workflows/lint-test.yml/badge.svg)](https://github.com/pantheon-systems/pantheon-hud/actions/workflows/lint-test.yml)
[![Actively Maintained](https://img.shields.io/badge/Pantheon-Actively_Maintained-yellow?logo=pantheon&color=FFDC28)](https://docs.pantheon.io/oss-support-levels#actively-maintained-support)

This plugin provides situational awareness of the Pantheon plaform from within your WordPress dashboard. It's helpful to be reminded what environment you're in, as well as providing quick links to get back to Pantheon's dashboard, or to interface with your WordPress installation via the command line.

Pantheon HUD is in early stages of development. We want your feedback! [Create a Github issue](https://github.com/pantheon-systems/pantheon-hud/issues) with questions, feature requests, or bug reports.

## Installation ##

Installation is vanilla. The plugin should have no ill effect when the site is running locally or if you move your site off the Pantheon platform. It knows how to nerf itself in other environments.

By default, the Pantheon HUD appears for logged-in users with the `manage_options` capability. You can instead restrict it to specific users with the `pantheon_hud_current_user_can_view` filter:

    add_filter( 'pantheon_hud_current_user_can_view', function(){
        $current_user = wp_get_current_user();
        if ( $current_user && in_array( $current_user->user_login, array( 'myuserlogin' ) ) ) {
            return true;
        }
        return false;
    });

## Screenshots ##

### 1. Pantheon HUD is present in the WordPress toolbar. On hover, it displays environmental details and helpful links. ###
![Pantheon HUD is present in the WordPress toolbar. On hover, it displays environmental details and helpful links.](https://raw.githubusercontent.com/pantheon-systems/pantheon-hud/main/screenshot-1.png)


## Changelog ##
### 0.4.5-dev ###
* Supports PHP 8.4 [[#153](https://github.com/pantheon-systems/pantheon-hud/pull/153/)]

### 0.4.4 (December 6, 2024 ###
* Fix admin bar item layout issue [[#145](https://github.com/pantheon-systems/pantheon-hud/pull/145)] props @cbirdsong and @westonruter
* Update CONTRIBUTING.md [[#123](https://github.com/pantheon-systems/pantheon-hud/pull/123)]
* Added "environment-indicator" to tags [[#128](https://github.com/pantheon-systems/pantheon-hud/pull/128)]
* Updates Pantheon WP Coding Standards to 2.0 [[#131](https://github.com/pantheon-systems/pantheon-hud/pull/131)]

### 0.4.3 (April 6, 2023) ###
* Update Composer dependencies [[#116](https://github.com/pantheon-systems/pantheon-hud/pull/116)] [[#118](https://github.com/pantheon-systems/pantheon-hud/pull/118)]
* Update Actively Maintained anchor link [[#102](https://github.com/pantheon-systems/pantheon-hud/pull/102)]
* Update Tested up to version.

### 0.4.2 (January 23, 2023) ###
* PHP 8.2 compatibility and testing [[#110](https://github.com/pantheon-systems/pantheon-hud/pull/110)].
* Update Composer dependencies [[#112](https://github.com/pantheon-systems/pantheon-hud/pull/112)].
* Update images for lint and test-behat jobs [[#111](https://github.com/pantheon-systems/pantheon-hud/pull/111)].
* Make dependabot target develop branch [[#109](https://github.com/pantheon-systems/pantheon-hud/pull/109)].

### 0.4.1 (November 28, 2022) ###
* Moves .distignore to .gitattributes [[#106](https://github.com/pantheon-systems/pantheon-hud/pull/106)].

### 0.4.0 (November 22, 2022) ###
* Adds CONTRIBUTING.md and Github Action to automate deploys to wordpress.org [[#103](https://github.com/pantheon-systems/pantheon-hud/pull/103)].

### 0.3.1 (March 13, 2020) ###
* Fixes issue where indicator didn't properly load on the frontend [[#58](https://github.com/pantheon-systems/pantheon-hud/pull/58)].

### 0.3.0 (March 11, 2020) ###
* Improves performance by populating Pantheon HUD menu with an AJAX request on hover [[#55](https://github.com/pantheon-systems/pantheon-hud/pull/55)].
* Cleans up PHPCS errors [[#49](https://github.com/pantheon-systems/pantheon-hud/pull/49)].

### 0.2.2 (October 28, 2019) ###
* Fixes reversed argument order to `implode()` [[#52](https://github.com/pantheon-systems/pantheon-hud/pull/52)].

### 0.2.1 (September 9, 2019) ###
* Uses inline style system to add admin bar styles to page [[#44](https://github.com/pantheon-systems/pantheon-hud/pull/44)].

### 0.2.0 (July 8, 2019) ###
* Refactors API calls to use new API endpoints [[#35](https://github.com/pantheon-systems/pantheon-hud/pull/35)].

### 0.1.4 (July 18, 2018) ###
* Restores the CSS for the logo image, while retaining inline attrs [[#26](https://github.com/pantheon-systems/pantheon-hud/pull/26)].

### 0.1.3 (July 6, 2018) ###
* Defines image dimensions inline instead of via CSS [[#23](https://github.com/pantheon-systems/pantheon-hud/pull/23)].

### 0.1.2 (June 8, 2017) ###
* Renders styles in `admin_head` instead of `admin_footer`.

### 0.1.1 (February 27, 2017) ###
* Updates `terminus` stub command to use new syntax.

### 0.1.0 (January 22, 2016) ###
* Initial release / MVP functionality.
* Environment badge and basic container stats.
* Quick WP-CLI copy/paste.
* Links to other envs and to Pantheon dash.
