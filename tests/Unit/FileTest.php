<?php

namespace Tests\Unit;

use App\Exceptions\FileException;
use App\Lib\File;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Class FileTest
 * @package Tests\Unit
 */
class FileTest extends TestCase {
    /* @var $file \App\Lib\File */
    protected $file;

    public function setUp() : void {
        $_FILES = array(
            'test' => array(
                'name'     => 'dsttestimage.jpg',
                'type'     => 'image/jpeg',
                'size'     => 542,
                'tmp_name' => __DIR__ . '/srctestimage.jpg',
                'error'    => 0
            )
        );
        $this->file = new File('test');
    }

    public static function tearDownAfterClass() : void {
        unset($_FILES);
        @unlink(__DIR__ . '/dsttestimage.jpg');
    }

    public function test__construct() {
        $this->assertInstanceOf(File::class, $this->file);
    }

    public function testMoveUploadedFile() {
        $this->assertTrue($this->file->moveUploadedFile());
        $this->assertFileExists(__DIR__ . "/dsttestimage.jpg");
    }

    public function testDeleteFile() {
        $this->assertTrue(File::deleteFile(__DIR__ . "/dsttestimage.jpg"));
    }

    public function testDeleteFile2() {
        $this->expectException(FileException::class);
        $this->assertTrue(File::deleteFile(__DIR__ . "/DOESNOTEXIST.jpg"));
    }

    public function testGetImageSize() {
        $this->assertEquals(542, $this->file->get('size'));
    }
}

namespace App\Lib;

/**
 * Override Default Implementation
 * @param $filename
 * @return bool
 */
function is_uploaded_file($filename) {
    //Check only if file exists
    return file_exists($filename);
}

/**
 * Override Default Implementation
 * @param $filename
 * @param $destination
 * @return bool
 */
function move_uploaded_file($filename, $destination) {
    //Copy file
    return copy($filename, __DIR__ . "/" . $_FILES['test']['name']);
}