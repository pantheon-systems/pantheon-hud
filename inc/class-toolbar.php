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
	}

	public function action_admin_bar_menu( $wp_admin_bar ) {
		$api = new API;
		$title = '<img style="height:32px;width:32px;vertical-align:middle;margin-top:-4px;" src="' . esc_url( plugins_url( 'assets/img/pantheon-fist-color.svg', PANTHEON_HUD_ROOT_FILE ) ) . '" />';
		$bits = array();
		if ( $name = $api->get_site_name() ) {
			$bits[] = $name;
		}
		$bits[] = $this->get_environment();
		$title .= ' ' . esc_html( strtolower( implode( ':', $bits ) ) );
		$wp_admin_bar->add_node( array(
			'id'       => 'pantheon-hud',
			'href'     => false,
			'title'    => $title,
			) );

		if ( $site_id = $api->get_site_id() ) {
			$dashboard_link = sprintf( 'https://dashboard.pantheon.io/sites/%s#%s/code', $site_id, $this->get_environment() );
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

	private function get_environment() {
		return ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ? $_ENV['PANTHEON_ENVIRONMENT'] : 'local';
	}

}
