<?php
/**
 * Bootstrap PHPUnit
 *
 * @package Pantheon HUD
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', __DIR__ . '/../../vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php' );

define( 'PANTHEON_HUD_PHPUNIT_RUNNING', true );

require_once $_tests_dir . '/includes/functions.php';
/**
 * Manually Load Plugin.
 */
function _manually_load_plugin() {

	add_filter(
		'pre_http_request',
		function( $response, $request, $url ) {
			$data_files = [
				'/sites/self/environments/dev/domains'  => 'domains.json',
				'/sites/self/environments/dev/settings' => 'environment-settings.json',
			];
			$path       = wp_parse_url( $url, PHP_URL_PATH );
			if ( ! isset( $data_files[ $path ] ) ) {
				return $response;
			}
			return array(
				'headers'  => array(
					'date'            => 'Thu, 14 Jan 2016 13:46:02 GMT',
					'content-type'    => 'application/json',
					'x-pantheon-host' => 'yggdrasil4ead8a2d.chios.panth.io',
					'server'          => 'TwistedWeb/12.2.0',
				),
				'body'     => file_get_contents( __DIR__ . '/data/' . $data_files[ $path ] ), // phpcs:ignore WordPress.WP.AlternativeFunctions
				'response' => array(
					'code'    => 200,
					'message' => 'OK',
				),
				'cookies'  => array(),
				'filename' => null,
			);
		},
		10,
		3
	);

	require dirname( dirname( dirname( __FILE__ ) ) ) . '/pantheon-hud.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
