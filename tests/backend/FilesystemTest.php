<?php
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

class FilesystemTest extends PHPUnit_Framework_TestCase {

    /**
     * @
     */
    public function testReadFile()
    {
        $F = new Filesystem(new Adapter(__DIR__));
        $contents = $F->read(basename(__FILE__));
        $this->assertTrue(!empty($contents));

    }
    /**
     * @expectedException League\Flysystem\FileNotFoundException
     */
    public function testReadNonExistant()
    {
        $F = new Filesystem(new Adapter(__DIR__));
        $F->read('does-not-exist');
    }

    public function testCreateIntermediateDirs()
    {
        $F = new Filesystem(new Adapter(__DIR__));
        $dirname = 'dir'.rand(1,1000);
        $fulldir = __DIR__.'/'.$dirname;
        $F->put($dirname.'/test.txt', 'contents');

        $this->assertTrue(file_exists($fulldir));
        $this->assertTrue(is_dir($fulldir));
        $this->assertTrue(file_exists($fulldir.'/test.txt'));

        $F->deleteDir($dirname);
        $this->assertFalse(file_exists($fulldir));
    }

}
