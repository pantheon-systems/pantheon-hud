<?php

namespace Pantheon\HUD;

/**
 * Adds Pantheon details to the WordPress toolbar
 * Instantiation expects the user to be able to view Pantheon details
 */
class Toolbar {

	private static $instance;

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
			self::$instance->setup_actions();
		}
		return self::$instance;
	}

	private function setup_actions() {
		add_action( 'admin_bar_menu', array( $this, 'action_admin_bar_menu' ), 100 );
		add_action( 'wp_footer', array( $this, 'action_wp_footer' ) );
		add_action( 'admin_footer', array( $this, 'action_wp_footer' ) );
	}

	public function action_admin_bar_menu( $wp_admin_bar ) {
		$api = new API;
		$name = $api->get_site_name();
		$site_id = $api->get_site_id();
		$env = $this->get_environment();
		$title = '<img src="' . esc_url( plugins_url( 'assets/img/pantheon-fist-color.svg', PANTHEON_HUD_ROOT_FILE ) ) . '" />';
		$bits = array();
		if ( $name ) {
			$bits[] = $name;
		}
		$bits[] = $env;
		$title .= ' ' . esc_html( strtolower( implode( ':', $bits ) ) );
		$wp_admin_bar->add_node( array(
			'id'       => 'pantheon-hud',
			'href'     => false,
			'title'    => $title,
			) );
			
		if ( $name && $env ) {
			$wp_cli_stub = sprintf( 'terminus wp --site=%s --env=%s', $name, $env );
			$wp_admin_bar->add_node( array(
				'id'     => 'pantheon-hud-wp-cli-stub',
				'parent' => 'pantheon-hud',
				'title'  => '<h5>' . esc_html__( 'Quick Terminus', 'pantheon-hud' ) . '</h5><input value="' . esc_attr( $wp_cli_stub ) . '">',
				) );
		}

		if ( $site_id && $env ) {
			$dashboard_link = sprintf( 'https://dashboard.pantheon.io/sites/%s#%s/code', $site_id, $env );
			$wp_admin_bar->add_node( array(
				'id'     => 'pantheon-hud-dashboard-link',
				'parent' => 'pantheon-hud',
				'href'   => $dashboard_link,
				'title'  => esc_html__( 'Visit Pantheon Dashboard', 'pantheon-hud' ),
				'meta'   => array(
					'target' => '_blank',
					),
				) );
		}

	}
	
	public function action_wp_footer() {
?>
<style>
	#wpadminbar li#wp-admin-bar-pantheon-hud > .ab-item img {
		height:32px;
		width:32px;
		vertical-align:middle;
		margin-top:-4px;
	}
	#wpadminbar li#wp-admin-bar-pantheon-hud h5 {
		font-size: 11px;
		line-height: 13px;
		font-style: italic;
	}
	#wpadminbar ul li#wp-admin-bar-pantheon-hud-wp-cli-stub .ab-item {
		height: auto;
	}
	#wpadminbar ul li#wp-admin-bar-pantheon-hud-wp-cli-stub input {
		width: 100%;
		line-height: 15px;
		background-color: rgba( 255, 255, 255, 0.9 );
		border-width: 1px;
	}
	#wpadminbar ul li#wp-admin-bar-pantheon-hud-dashboard-link {
		padding-left: 3px;
		padding-right: 3px;
	}
	#wpadminbar ul li#wp-admin-bar-pantheon-hud-dashboard-link a {
		border-top: 1px solid rgba(240,245,250,.4);
		padding-top: 3px;
		margin-top: 6px;
	}
</style>
<?php
	}

	private function get_environment() {
		return ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ? $_ENV['PANTHEON_ENVIRONMENT'] : 'local';
	}

}
