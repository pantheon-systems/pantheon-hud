<?php

class APITest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->api = new Pantheon\HUD\API;
		$_ENV['PANTHEON_ENVIRONMENT'] = 'dev';
	}

	public function test_get_site_id() {
		$this->assertEquals( '73cae74a-b66e-440a-ad3b-4f0679eb5e97', $this->api->get_site_id() );
	}

	public function test_get_site_name() {
		$this->assertEquals( 'daniel-pantheon', $this->api->get_site_name() );
	}

	public function test_get_last_code_push_timestamp() {
		$this->assertEquals( 1446237120, $this->api->get_last_code_push_timestamp() );
	}

	/**
	 * Ensures the PHP version is pulled from the $_ENV variable as expected.
	 */
	public function test_get_php_version() {
		$_ENV['php_version'] = '7.3';
		$this->assertEquals( '7.3', $this->api->get_php_version() );
	}

	public function test_get_environment_details() {
		$_ENV['php_version'] = '7.3';
		$environment_details = $this->api->get_environment_details();
		$this->assertEquals( array(
			'web'      => array(
				'appserver_count'  => 1,
				'php_version'      => 'PHP 7.3',
			),
			'database' => array(
				'dbserver_count' => 1,
				'read_replication_enabled' => false,
			),
		), $environment_details );
	}

}
