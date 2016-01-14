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
	 * Get the timestamp of the last code push
	 *
	 * @return int
	 */
	public function get_last_code_push_timestamp() {
		return ! empty( $this->site_data['site']['last_code_push']['timestamp'] ) ? strtotime( $this->site_data['site']['last_code_push']['timestamp'] ) : 0;
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
