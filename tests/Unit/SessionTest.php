<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Class SessionTest
 * @package Tests\Unit
 */
class SessionTest extends TestCase {
	protected $mock;

	public function setUp()  : void {
		$this->mock = $this->getMockBuilder('App\Models\User')
			->disableOriginalConstructor()
			->disableOriginalClone()
			->disableArgumentCloning()
			->getMock();
	}

	public function testLogin() {
		global $session;
		$this->assertTrue($session->login($this->mock));
	}

	/*
	 * @depends testLogin
	 */
	public function testGetUser() {
		global $session;
		$this->assertInstanceOf(get_class($this->mock), $session->getUser());
	}

	/*
	 * @depends testGetUser
	 */
	public function testIsLoggedIn() {
		global $session;
		$this->assertInstanceOf(get_class($this->mock), $session->IsLoggedIn());
	}

	/*
	 * @depends testIsLoggedIn
	 */
	public function testLogout() {
		global $session;
		$this->assertTrue($session->logout());
	}

	/*
	 * @depends testLogout
	 */
	public function testGetUser2() {
		global $session;
		$this->assertFalse($session->getUser());
	}

	/*
	 * @depends testGetUser2
	 */
	public function testIsLoggedIn2() {
		global $session;
		$this->assertFalse($session->isLoggedIn());
	}
}
