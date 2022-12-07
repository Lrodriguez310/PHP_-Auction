<?php

namespace Tests\Unit;

use App\Models\Category;
use PHPUnit\Framework\TestCase;

/**
 * Class CategoryTest
 * @package Tests\Unit
 */
class CategoryTest extends TestCase {

	public function test__construct() {
		$this->assertInstanceOf(Category::class, new Category("test"));
	}
}
