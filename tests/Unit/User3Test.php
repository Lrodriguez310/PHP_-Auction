<?php

namespace Tests\Unit;

use App\Lib\Database;
use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit
 */
class User3Test extends TestCase {

	/* @var $user \App\Models\User */
	public $user;

	public function setUp()  : void {
		$this->user = new User("TestUser", "TestPass", "Test@Test.com");
		$this->user->create();
	}

	public function tearDown()  : void {
		$result = $this->user->findFirst(["username" => "TestUser"]);
		$result->delete();
		Database::getConnection()->sqlQuery("ALTER TABLE `users` AUTO_INCREMENT = 1;");
	}

	public function testRandStr() {
		$this->assertEquals(16, strlen($this->user->get('verify')));
	}

	public function testMailUser() {
		$this->assertTrue($this->user->mailUser());
	}
}