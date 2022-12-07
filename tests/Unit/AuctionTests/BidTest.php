<?php

namespace Tests\Unit;

use App\Models\Bid;
use PHPUnit\Framework\TestCase;

/**
 * Class BidTest
 * @package Tests\Unit
 */
class BidTest extends TestCase {

	public function test__construct() {
		$this->assertInstanceOf(Bid::class, new Bid(1,1,1));
	}
}
