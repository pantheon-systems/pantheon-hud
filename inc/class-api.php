<?php

namespace Pantheon\HUD;

/**
 * Pull data from the Pantheon site API.
 */
class API {

	private static $endpoint_url = 'https://api.live.getpantheon.com:8443/sites/self/state';

	private $site_data;

	public function __construct() {
		$this->site_data = $this->fetch_site_data();
	}

	/**
	 * Get the id of the Pantheon site.
	 */
	public function get_site_id() {
		return $this->get_site_data( 'environments', 'dev', 'site' );
	}

	/**
	 * Get the name of the Pantheon site.
	 *
	 * @return string
	 */
	public function get_site_name() {
		return $this->get_site_data( 'site', 'name' );
	}

	/**
	 * Get the timestamp of the last code push
	 *
	 * @return int
	 */
	public function get_last_code_push_timestamp() {
		$timestamp = $this->get_site_data( 'site', 'last_code_push', 'timestamp' );
		if ( $timestamp ) {
			return strtotime( $timestamp );
		} else {
			return 0;
		}
	}

	/**
	 * Traverse the $site_data variable and return value if it exists
	 *
	 * @return mixed|null
	 */
	private function get_site_data() {
		$args = func_get_args();
		$val = $this->site_data;
		foreach( $args as $arg ) {
			if ( isset( $val[ $arg ] ) ) {
				$val = $val[ $arg ];
			} else {
				return null;
			}
		}
		return $val;
	}

	private function fetch_site_data() {

		$require_curl = function() {
			return array( 'curl' );
		};
		add_filter( 'http_api_transports', $require_curl );
		$client_cert = function( $handle ) {
			curl_setopt( $handle, CURLOPT_SSLCERT, dirname( dirname( __FILE__ ) ) . '/binding.pem' );
		};
		add_action( 'http_api_curl', $client_cert );
		$response = wp_remote_get( self::$endpoint_url, array(
			'sslcertificates' => dirname( dirname( __FILE__ ) ) . '/binding.crt',
			'sslverify' => false, // @todo need to get verification working
		) );
		remove_action( 'http_api_curl', $client_cert );
		remove_filter( 'http_api_transports', $require_curl );
		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return null;
		}
		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body, true );
	}

}
