<?php
/**
 * Toolbar Class
 *
 * @package Pantheon HUD
 */

namespace Pantheon\HUD;

/**
 * Adds Pantheon details to the WordPress toolbar
 * Instantiation expects the user to be able to view Pantheon details
 */
class Toolbar {

	/**
	 * Class Instance.
	 *
	 * @var Toolbar
	 */
	private static $instance;

	/**
	 * Singleton
	 *
	 * @return Toolbar
	 */
	public static function get_instance(): Toolbar {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->setup_actions();
		}
		return self::$instance;
	}

	/**
	 * Setup Actions
	 *
	 * @return void
	 */
	private function setup_actions(): void {
		add_action( 'admin_bar_menu', [ $this, 'action_admin_bar_menu' ], 100 );
		add_action( 'wp_ajax_pantheon_hud_markup', [ $this, 'action_handle_ajax_markup' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_admin_bar_assets' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_bar_assets' ] );
	}

	/**
	 * Hook into Admin Bar
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar The Admin Bar Object.
	 * @return void
	 */
	public function action_admin_bar_menu( \WP_Admin_Bar $wp_admin_bar ): void {
		$api  = new API();
		$name = $api->get_site_name();
		$env  = $this->get_environment();
		$title = '<img src="' . esc_url( plugins_url( 'assets/img/pantheon-fist-color.svg', PANTHEON_HUD_ROOT_FILE ) ) . '" width="32" height="32" />';
		$bits = [];
		if ( $name ) {
			$bits[] = $name;
		}
		$bits[] = $env;
		$title .= ' ' . esc_html( strtolower( implode( ':', $bits ) ) );
		$wp_admin_bar->add_node( [
			'id'    => 'pantheon-hud',
			'href'  => false,
			'title' => $title,
		] );

		$wp_admin_bar->add_node( [
			'id'     => 'pantheon-hud-wp-admin-loading',
			'parent' => 'pantheon-hud',
			'href'   => false,
			'title'  => 'Loading&hellip;',
		] );
	}

	/**
	 * Handles an AJAX request to fetch additional markup for the dropdown menu.
	 *
	 * @return void
	 */
	public function action_handle_ajax_markup(): void {
		check_ajax_referer( 'pantheon_hud' );

		$api     = new API();
		$name    = $api->get_site_name();
		$site_id = $api->get_site_id();
		$env     = $this->get_environment();
		$markup  = [];
		$markup[] = '<ul id="wp-admin-bar-pantheon-hud-default" class="ab-submenu">';

		$env_admins = '';
		// TODO: List envs from API to include Multidev.
		$envs = apply_filters( 'pantheon_hud_envs', [ 'dev', 'test', 'live' ] );
		foreach ( $envs as $e ) {
			$url = $api->get_primary_environment_url( $e );
			if ( $url ) {
				$env_admins .= '<a target="_blank" href="' . esc_url( rtrim( $url ) . '/wp-admin/' ) . '">' . esc_html( $e ) . '</a> | ';
			}
		}

		if ( ! empty( $env_admins ) ) {
			$markup[] = '<li id="wp-admin-bar-pantheon-hud-wp-admin-links"><div class="ab-item ab-empty-item"><em>wp-admin links</em><br />' . rtrim( $env_admins, ' |' ) . '</div></li>';
		}

		$environment_details = $api->get_environment_details();
		if ( $environment_details ) {
			$details_html = [];
			if ( isset( $environment_details['web']['appserver_count'] ) ) {
				$pluralize  = $environment_details['web']['appserver_count'] > 1 ? 's' : '';
				$web_detail = $environment_details['web']['appserver_count'] . ' app container' . $pluralize;
				if ( isset( $environment_details['web']['php_version'] ) ) {
					$web_detail .= ' running ' . $environment_details['web']['php_version'];
				}
				$details_html[] = $web_detail;
			}
			if ( isset( $environment_details['database']['dbserver_count'] ) ) {
				$pluralize = $environment_details['database']['dbserver_count'] > 1 ? 's' : '';
				$db_detail = $environment_details['database']['dbserver_count'] . ' db container' . $pluralize;
				if ( isset( $environment_details['database']['read_replication_enabled'] ) ) {
					$db_detail .= ' with ' . ( $environment_details['database']['read_replication_enabled'] ? 'replication enabled' : 'replication disabled' );
				}
				$details_html[] = $db_detail;
			}
			if ( ! empty( $details_html ) ) {
				$details_html = '<em>' . esc_html__( 'Environment Details', 'pantheon-hud' ) . '</em><br /> - ' . implode( '<br /> - ', $details_html );
				$markup[]     = '<li id="wp-admin-bar-pantheon-hud-environment-details"><div class="ab-item ab-empty-item">' . $details_html . '</div></li>';
			}
		}

		if ( $name && $env ) {
			$wp_cli_stub = sprintf( 'terminus wp %s.%s', $name, $env );
			$markup[]    = '<li id="wp-admin-bar-pantheon-hud-wp-cli-stub"><div class="ab-item ab-empty-item"><em>' . esc_html__( 'WP-CLI via Terminus', 'pantheon-hud' ) . '</em><br /><input value="' . esc_attr( $wp_cli_stub ) . '"></div></li>';
		}

		if ( $site_id && $env ) {
			$dashboard_link = sprintf( 'https://dashboard.pantheon.io/sites/%s#%s/code', $site_id, $env );
			$markup[]       = sprintf( '<li id="wp-admin-bar-pantheon-hud-dashboard-link"><a class="ab-item" href="%s" target="_blank">Visit Pantheon Dashboard</a></li>', esc_url( $dashboard_link ) );
		}

		$markup[] = '</ul>';
		// All values in $markup are escaped at point of insertion, safe to output.
		echo implode( PHP_EOL, $markup ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All values are escaped above.
		exit;
	}

	/**
	 * Enqueue admin bar CSS and JavaScript assets.
	 *
	 * @return void
	 */
	public function enqueue_admin_bar_assets(): void {
		if ( ! is_admin_bar_showing() ) {
			return;
		}

		// Get plugin version from main plugin file header.
		$plugin_data = get_file_data(
			PANTHEON_HUD_ROOT_FILE,
			[ 'Version' => 'Version' ]
		);
		$version = $plugin_data['Version'] ?? '0.4.5';

		// Enqueue CSS.
		wp_enqueue_style(
			'pantheon-hud',
			plugins_url( 'assets/css/pantheon-hud.css', PANTHEON_HUD_ROOT_FILE ),
			[ 'admin-bar' ],
			$version
		);

		// Prepare AJAX request URL.
		$request_url = add_query_arg(
			[
				'action'   => 'pantheon_hud_markup',
				'_wpnonce' => wp_create_nonce( 'pantheon_hud' ),
			],
			admin_url( 'admin-ajax.php' )
		);

		// Enqueue JavaScript.
		wp_enqueue_script(
			'pantheon-hud',
			plugins_url( 'assets/js/pantheon-hud.js', PANTHEON_HUD_ROOT_FILE ),
			[ 'admin-bar' ],
			$version,
			true
		);

		// Pass data to JavaScript.
		wp_localize_script(
			'pantheon-hud',
			'pantheonHudData',
			[
				'requestUrl' => $request_url,
			]
		);

		// AMP compatibility filter.
		add_filter(
			'amp_dev_mode_element_xpaths',
			static function ( array $xpaths ): array {
				$xpaths[] = '//script[ contains( @src, "pantheon-hud.js" ) ]';
				return $xpaths;
			}
		);
	}

	/**
	 * Get Pantheon Env.
	 *
	 * @return string Pantheon environment or 'local'.
	 */
	private function get_environment(): string {
		return ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ? $_ENV['PANTHEON_ENVIRONMENT'] : 'local';
	}
}
