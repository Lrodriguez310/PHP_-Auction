<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package Tests\Unit
 */
class UserTest extends TestCase {

	public function test__construct() {
		$this->assertInstanceOf(User::class, new User("TestUser", "TestPass", "Test@Test.com"));
	}
}