<?php

namespace Tests\Unit;

use App\Models\Item;
use PHPUnit\Framework\TestCase;

/**
 * Class Item2Test
 * @package Tests\Unit
 */
class Item3Test extends TestCase {

	/* @var $item \App\Models\Item */
	public $item;

	public function setUp() :void {
		$this->item = new Item(1, 1, "TestItem", 1.99, "TestItem", "2018-12-24");
	}

	public function testDisplayError() {
		$this->assertStringContainsString("The bid entered is too low.", Item::displayError("lowprice"), "", true);
	}
}
