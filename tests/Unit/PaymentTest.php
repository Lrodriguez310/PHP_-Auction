<?php

namespace Tests\Unit;

use App\Models\Payment;
use PHPUnit\Framework\TestCase;

/**
 * Class PaymentTest
 * @package Tests\Unit
 */
class PaymentTest extends TestCase {

	public function testGeneratePayment() {
		$result = Payment::generatePayment("999");
		$this->assertStringContainsString("999", $result);
	}

	public function test__construct() {
		$this->assertInstanceOf(Payment::class, new Payment(
			1,
			1,
			"completed",
			1,
			"TestUser",
			1,
			"TestUser@test.com",
			"Test",
			"User",
			123,
			"Fake",
			"Ont",
			"12345",
			"CAD",
			"00:00:00 Jan 1, 2018 PDT"
		));
	}
}
