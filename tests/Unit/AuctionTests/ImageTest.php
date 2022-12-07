<?php

namespace Tests\Unit;

use App\Models\Image;
use PHPUnit\Framework\TestCase;

/**
 * Class ImageTest
 * @package Tests\Unit
 */
class ImageTest extends TestCase {

	public function test__construct() {
		$this->assertInstanceOf(Image::class, new Image(1, "test"));
	}
}
