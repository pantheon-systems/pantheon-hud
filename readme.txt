=== Pantheon HUD ===
Contributors: getpantheon, danielbachhuber
Tags: Pantheon, hosting
Requires at least: 4.4
Tested up to: 4.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A heads-up display into your Pantheon environment.

== Description ==

[![Build Status](https://travis-ci.org/pantheon-systems/pantheon-hud.svg?branch=master)](https://travis-ci.org/pantheon-systems/pantheon-hud)

This plugin provides situational awareness of the Pantheon plaform from within your WordPress dashboard. It's helpful to be reminded what environment you're in, as well as providing quick links to get back to Pantheon's dashboard, or to interface with your WordPress installation via the command line.

Pantheon HUD is in early stages of development. We want your feedback! [Create a Github issue](https://github.com/pantheon-systems/pantheon-hud/issues) with questions, feature requests, or bug reports.

== Installation ==

Installation is vanilla. The plugin should have no ill effect when the site is running locally or if you move your site off the Pantheon platform. It knows how to nerf itself in other environments.

By default, the Pantheon HUD appears for logged-in users with the `manage_options` capability. You can instead restrict it to specific users with the `pantheon_hud_current_user_can_view` filter:

    add_filter( 'pantheon_hud_current_user_can_view', function(){
        $current_user = wp_get_current_user();
        if ( $current_user && in_array( $current_user->user_login, array( 'myuserlogin' ) ) ) {
            return true;
        }
        return false;
    });

== Screenshots ==

1. Pantheon HUD is present in the WordPress toolbar. On hover, it displays environmental details and helpful links.

== Changelog ==

= 0.1.0 (January 22, 2016) =
* Initial release / MVP functionality.
* Environment badge and basic container stats.
* Quick WP-CLI copy/paste.
* Links to other envs and to Pantheon dash.
