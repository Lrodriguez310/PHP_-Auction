<?php

namespace Tests\Unit;

use App\Lib\Database;
use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit
 */
class User2Test extends TestCase {

	public function setUp()  : void {
		$pass = password_hash("TestPass", PASSWORD_BCRYPT, ['cost' => 10]);
		Database::getConnection()->sqlQuery("INSERT INTO `users` (`username`, `password`, `email`, `verify`, `active`) VALUES('TestUser', '{$pass}', 'testauction123@test.com', '1234', 1);");
	}

	public function tearDown()  : void {
		Database::getConnection()->sqlQuery("DELETE FROM `users` WHERE email = 'testauction123@test.com';");
		Database::getConnection()->sqlQuery("ALTER TABLE `users` AUTO_INCREMENT = 1;");
	}

	public function testAuth() {
		$this->assertInstanceOf(User::class, User::auth("testauction123@test.com", "TestPass"));
	}

	public function testAuthFail() {
		$this->assertFalse(User::auth("TEST", "TestFAIL"));
	}
}