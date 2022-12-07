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
class ModelTest extends TestCase {

	/* @var $item \App\Models\Item */
	public $item;

	public function setUp()  : void {
		Database::getConnection()->sqlQuery("INSERT INTO `items` (`user_id`, `cat_id`, `name`, `price`, `description`, `date`, `notified`) VALUES('1', '1', 'TestItem', 1.99, 'TestItem', NOW(), 0);");
		$this->item = new Item(1, 1, "TestItem", 1.99, "TestItem", "2018-12-24");
	}

	public function tearDown()  : void {
		Database::getConnection()->sqlQuery("DELETE FROM `items` WHERE name = 'TestItem' OR name = 'TestItem2';");
		Database::getConnection()->sqlQuery("ALTER TABLE `items` AUTO_INCREMENT = 1;");
	}

	public function testFind() {
		$items = Item::find(["name" => "TestItem"], "name", "name", 1);
		$this->assertIsArray($items);
		$this->assertInstanceOf(Item::class, $items[0]);
	}

	public function testFind2() {
		$items = Item::find("name = 'TestItem'");
		$this->assertIsArray($items);
		$this->assertInstanceOf(Item::class, $items[0]);
	}

	public function testFindFirst() {
		$item = Item::findFirst(["name" => "TestItem"], "name");
		$this->assertInstanceOf(Item::class, $item);
	}

	public function testFindFirst2() {
		$this->expectException(ClassException::class);
		Item::findFirst(["name" => "DOESNOTEXIST"], "name");
	}

	public function testAll() {
		$items = Item::all("name", "name");
		$this->assertIsArray($items);
		$this->assertInstanceOf(Item::class, $items[0]);
	}

	public function testGet() {
		$this->assertEquals("TestItem", $this->item->get("name"));
	}

	public function testGet2() {
		$this->assertFalse($this->item->get("TEST"));
	}

	public function testSet() {
		$this->assertInstanceOf(Item::class, $this->item->set("name", "TestItem2"));
	}

	public function testSet2() {
		$this->assertFalse($this->item->set("TEST", "TEST"));
	}

}
