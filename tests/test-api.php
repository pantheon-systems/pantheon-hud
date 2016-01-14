<?php

class APITest extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->api = new Pantheon\HUD\API;
	}

	public function test_get_last_code_push_timestamp() {
		$this->assertEquals( 1446237120, $this->api->get_last_code_push_timestamp() );
	}

}
