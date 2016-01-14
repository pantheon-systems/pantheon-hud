<?php

class APITest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->api = new Pantheon\HUD\API;
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

}
