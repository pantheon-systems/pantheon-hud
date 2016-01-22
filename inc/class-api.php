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
	 * Get the primary url for the environment
	 *
	 * @return string
	 */
	public function get_primary_environment_url( $env ) {
		$urls = $this->get_site_data( 'environments', $env, 'urls' );
		if ( ! empty( $urls ) ) {
			return $urls[0];
		} else {
			return '';
		}
	}

	/**
	 * Get details about this particular environments
	 *
	 * @return array
	 */
	public function get_environment_details() {
		$env = ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ? $_ENV['PANTHEON_ENVIRONMENT'] : 'dev';
		$details = array(
			'web'      => array(),
			'database' => array(),
		);
		if ( $appserver_count = $this->get_site_data( 'environments', $env, 'appserver' ) ) {
			$details['web']['appserver_count'] = $appserver_count;
		}
		if ( $php_version = $this->get_site_data( 'environments', $env, 'php_version' ) ) {
			$php_version = (string) $php_version;
			$details['web']['php_version'] = "PHP " . $php_version[0] . "." . $php_version[1];
		}
		if ( $dbserver_count = $this->get_site_data( 'environments', $env, 'dbserver' ) ) {
			$details['database']['dbserver_count'] = $appserver_count;
		}
		if ( null !== ( $read_replication_enabled = $this->get_site_data( 'environments', $env, 'allow_read_slaves' ) ) ) {
			$details['database']['read_replication_enabled'] = (bool) $read_replication_enabled;
		}
		return $details;
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
		
		// Function internal to Pantheon infrastructure
		$pem_file = apply_filters( 'pantheon_hud_pem_file', null );
		if ( function_exists( 'pantheon_curl' ) ) {
			$bits = parse_url( self::$endpoint_url );
			$response = pantheon_curl( sprintf( '%s://%s%s', $bits['scheme'], $bits['host'], $bits['path'] ), null, $bits['port'] );
			$body = ! empty( $response['body'] ) ? $response['body'] : '';
			return json_decode( $body, true );
		// for those developing locally who know what they're doing
		} else if ( $pem_file || ( defined( 'PANTHEON_HUD_PHPUNIT_RUNNING' ) && PANTHEON_HUD_PHPUNIT_RUNNING ) ) {
			$require_curl = function() {
				return array( 'curl' );
			};
			add_filter( 'http_api_transports', $require_curl );
			$client_cert = function( $handle ) use ( $pem_file ) {
				curl_setopt( $handle, CURLOPT_SSLCERT, $pem_file );
			};
			add_action( 'http_api_curl', $client_cert );
			$response = wp_remote_get( self::$endpoint_url, array(
				'sslverify' => false, // yolo
			) );
			remove_action( 'http_api_curl', $client_cert );
			remove_filter( 'http_api_transports', $require_curl );
			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				return null;
			}
			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body, true );
		}
		return array();
	}

}
