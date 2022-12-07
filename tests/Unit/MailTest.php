<?php

namespace Tests\Unit;

use App\Lib\Mail;
use PHPUnit\Framework\TestCase;

/**
 * Class MailTest
 * @package Tests\Unit
 */
class MailTest extends TestCase {

	public function testSendMail() {
		$this->assertTrue(Mail::sendMail("test@nobody.com", "Unit Test", "Just a test"));
	}
}

namespace App\Lib;

/**
 * Override Default Implementation
 * @return bool
 */
function mail() {
	return true;
}