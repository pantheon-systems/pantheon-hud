<?php
/**
 * API Class
 *
 * @package Pantheon HUD
 */

namespace Pantheon\HUD;

/**
 * Pull data from the Pantheon site API.
 */
class API {

	/**
	 * Base URL for all API requests.
	 *
	 * @var string
	 */
	const API_URL_BASE = 'https://api.live.getpantheon.com:8443';

	/**
	 * Holds the domains data when present.
	 *
	 * @var array<string, array>
	 */
	private $domains_data;

	/**
	 * Holds the environment settings data when present.
	 *
	 * @var array
	 */
	private $environment_settings_data;

	/**
	 * Get the id of the Pantheon site.
	 *
	 * @return string
	 */
	public function get_site_id(): string {
		return ! empty( $_ENV['PANTHEON_SITE'] ) ? $_ENV['PANTHEON_SITE'] : '';
	}

	/**
	 * Get the name of the Pantheon site.
	 *
	 * @return string
	 */
	public function get_site_name(): string {
		return ! empty( $_ENV['PANTHEON_SITE_NAME'] ) ? $_ENV['PANTHEON_SITE_NAME'] : '';
	}

	/**
	 * Get the timestamp of the last code push
	 *
	 * @return int
	 */
	public function get_last_code_push_timestamp(): int {
		$environment_settings = $this->get_environment_settings_data();
		if ( ! empty( $environment_settings['last_code_push']['timestamp'] ) ) {
			return strtotime( $environment_settings['last_code_push']['timestamp'] );
		} else {
			return 0;
		}
	}

	/**
	 * Get the primary url for the environment
	 *
	 * @param string $env Environment to fetch the domains of.
	 * @return string
	 */
	public function get_primary_environment_url( string $env ): string {
		$domains = $this->get_domains_data( $env );
		if ( ! empty( $domains[0]['key'] ) ) {
			return $domains[0]['key'];
		} else {
			return '';
		}
	}

	/**
	 * Get details about this particular environments
	 *
	 * @return array
	 */
	public function get_environment_details(): array {
		$details = [
			'web'      => [],
			'database' => [],
		];
		$environment_settings = $this->get_environment_settings_data();
		if ( ! empty( $environment_settings['appserver'] ) ) {
			$details['web']['appserver_count'] = $environment_settings['appserver'];
		}
		$php_version = $this->get_php_version();
		if ( $php_version ) {
			$php_version                    = (string) $php_version;
			$details['web']['php_version'] = 'PHP ' . $php_version;
		}
		if ( ! empty( $environment_settings['dbserver'] ) ) {
			$details['database']['dbserver_count'] = $environment_settings['dbserver'];
		}
		if ( isset( $environment_settings['allow_read_slaves'] ) ) {
			$details['database']['read_replication_enabled'] = (bool) $environment_settings['allow_read_slaves'];
		}
		return $details;
	}

	/**
	 * Gets the PHP version for the site.
	 *
	 * @return string|false
	 */
	public function get_php_version() {
		return ! empty( $_ENV['php_version'] ) ? $_ENV['php_version'] : PHP_VERSION;
	}

	/**
	 * Gets the domains data.
	 *
	 * TODO: Consider caching this data using WordPress transients to reduce API calls.
	 * Example implementation:
	 *   $cache_key = 'pantheon_hud_domains_' . $env;
	 *   $cached = get_transient( $cache_key );
	 *   if ( false !== $cached ) {
	 *       return $cached;
	 *   }
	 *   // Fetch from API...
	 *   set_transient( $cache_key, $data, HOUR_IN_SECONDS );
	 *
	 * @param string $env Environment to fetch the domains of.
	 * @return array
	 */
	private function get_domains_data( string $env ): array {
		if ( isset( $this->domains_data[ $env ] ) ) {
			return $this->domains_data[ $env ];
		}
		if ( ! empty( $env ) ) {
			$url                         = sprintf( '%s/sites/self/environments/%s/domains', self::API_URL_BASE, $env );
			$this->domains_data[ $env ] = self::fetch_api_data( $url );
		} else {
			$this->domains_data[ $env ] = [];
		}
		return $this->domains_data[ $env ];
	}

	/**
	 * Gets the environment settings data.
	 *
	 * TODO: Consider caching this data using WordPress transients to reduce API calls.
	 * This data changes infrequently and could be cached for better performance.
	 *
	 * @return array
	 */
	private function get_environment_settings_data(): array {
		if ( isset( $this->environment_settings_data ) ) {
			return $this->environment_settings_data;
		}
		if ( ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ) {
			$url                             = sprintf( '%s/sites/self/environments/%s/settings', self::API_URL_BASE, $_ENV['PANTHEON_ENVIRONMENT'] );
			$this->environment_settings_data = self::fetch_api_data( $url );
		} else {
			$this->environment_settings_data = [];
		}
		return $this->environment_settings_data;
	}

	/**
	 * Fetch data from a given Pantheon API URL.
	 *
	 * TODO: Improve error handling:
	 * - Currently returns empty array on all failures, making it hard to distinguish
	 *   between "no data" and "API error"
	 * - Consider logging errors with error_log() for debugging
	 * - Consider returning WP_Error on failure for better error messages
	 * - Add timeout handling for slow API responses
	 *
	 * Example improved error handling:
	 *   if ( is_wp_error( $response ) ) {
	 *       error_log( 'Pantheon HUD API Error: ' . $response->get_error_message() );
	 *       return new WP_Error( 'api_failed', $response->get_error_message() );
	 *   }
	 *
	 * @param string $url URL from which to fetch data.
	 * @return array Returns empty array on failure.
	 */
	private function fetch_api_data( string $url ): array {

		// Function internal to Pantheon infrastructure.
		$pem_file = apply_filters( 'pantheon_hud_pem_file', null );
		if ( function_exists( 'pantheon_curl' ) ) {
			$bits     = wp_parse_url( $url );
			$response = pantheon_curl( sprintf( '%s://%s%s', $bits['scheme'], $bits['host'], $bits['path'] ), null, $bits['port'] );
			$body     = ! empty( $response['body'] ) ? $response['body'] : '';
			return json_decode( $body, true );

			// For those developing locally who know what they're doing.
		} elseif ( $pem_file || ( defined( 'PANTHEON_HUD_PHPUNIT_RUNNING' ) && PANTHEON_HUD_PHPUNIT_RUNNING ) ) {
			$require_curl = function (): array {
				return [ 'curl' ];
			};
			add_filter( 'http_api_transports', $require_curl );
			$client_cert = function ( $handle ) use ( $pem_file ): void {
				curl_setopt( $handle, CURLOPT_SSLCERT, $pem_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			};
			add_action( 'http_api_curl', $client_cert );
			$response = wp_remote_get(
				$url,
				[ 'sslverify' => false ] // yolo.
			);
			// Silent failure: Returns empty array instead of providing error context.
			if ( is_wp_error( $response ) ) {
				return [];
			}
			remove_action( 'http_api_curl', $client_cert );
			remove_filter( 'http_api_transports', $require_curl );
			// Silent failure: Returns empty array instead of providing error context.
			if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
				return [];
			}
			$body = wp_remote_retrieve_body( $response );
			return json_decode( $body, true );
		}
		// Silent failure: Returns empty array when neither pantheon_curl nor local dev setup exists.
		return [];
	}
}
