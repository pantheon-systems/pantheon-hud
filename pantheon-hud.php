<?php
/**
 * Plugin Name: Pantheon HUD
 * Version: 0.1-alpha
 * Description: A heads-up display into your Pantheon environment.
 * Author: Pantheon
 * Author URI: https://pantheon.io
 * Plugin URI: https://pantheon.io
 * Text Domain: pantheon-hud
 * Domain Path: /languages
 * @package Pantheon HUD
 */

spl_autoload_register( function( $class ) {
	$class = ltrim( $class, '\\' );
	if ( 0 !== stripos( $class, 'Pantheon\HUD\\' ) ) {
		return;
	}

	$parts = explode( '\\', $class );
	array_shift( $parts ); // Don't need "Pantheon"
	array_shift( $parts ); // Don't need "HUD"
	$last = array_pop( $parts ); // File should be 'class-[...].php'
	$last = 'class-' . $last . '.php';
	$parts[] = $last;
	$file = dirname( __FILE__ ) . '/inc/' . str_replace( '_', '-', strtolower( implode( $parts, '/' ) ) );
	if ( file_exists( $file ) ) {
		require $file;
	}

});
