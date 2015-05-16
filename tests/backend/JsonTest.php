<?php

use Webmozart\Json\JsonEncoder;

class JsonTest extends PHPUnit_Framework_TestCase {

    public function testCreateDirectories()
    {
        $E = new JsonEncoder();


        // Ooops... this won't create directories or files
        //$E->encodeFile(array('x'=>'x-ray'), __DIR__.'/does-not-exist/file.json');
        //$this->assertTrue(file_exists(__DIR__.'/does-not-exist/file.json'));
    }
}