<?php

namespace Tests\Unit;

use App\Exceptions\ClassException;
use App\Lib\Database;
use App\Models\Item;
use PHPUnit\Framework\TestCase;


/**
 * Class ModelTest
 * @package Tests\Unit
 */
class Model4Test extends TestCase {

	/* @var $item \App\Models\Item */
	public $item;

	public function setUp()  : void {
		$this->item = new Item(1, 1, "TestItem", 1.99, "TestItem", "2018-12-24");
	}

	public function tearDown()  : void {
		Database::getConnection()->sqlQuery("DELETE FROM `items` WHERE name = 'TestItem' OR name = 'TestItem2';");
		Database::getConnection()->sqlQuery("ALTER TABLE `items` AUTO_INCREMENT = 1;");
	}

	public function testUpdate() {
		$this->item->create();
		$this->item->set("name", "TestItem2");
		$this->assertTrue($this->item->update());
		$this->assertInstanceOf(Item::class, Item::findFirst(["name" => "TestItem2"]));
	}

}
