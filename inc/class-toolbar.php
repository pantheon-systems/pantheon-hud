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
		$title = '<img style="height:32px;width:32px;vertical-align:middle;margin-top:-4px;" src="' . esc_url( plugins_url( 'assets/img/pantheon-fist-color.svg', PANTHEON_HUD_ROOT_FILE ) ) . '" />';
		$environment = ! empty( $_ENV['PANTHEON_ENVIRONMENT'] ) ? $_ENV['PANTHEON_ENVIRONMENT'] : 'local';
		$title .= ' ' . esc_html( strtoupper( $environment ) );
		$wp_admin_bar->add_node( array(
			'id'       => 'pantheon-hud',
			'href'     => false,
			'title'    => $title,
			) );
	}

}
