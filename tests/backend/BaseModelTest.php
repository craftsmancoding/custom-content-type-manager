<?php

use Pimple\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

class BaseModelTest extends PHPUnit_Framework_TestCase {

    public $dic;



    public function setUp()
    {
        $this->dic = new Container();
        $this->dic['storage_dir'] = __DIR__.'/tmp';

        $this->dic['Filesystem'] = function ($c)
        {
            return new Filesystem(new Adapter($c['storage_dir']));
        };

    }

    public function tearDown()
    {

        $this->dic['Filesystem']->deleteDir();

    }
    public static function tearDownAfterClass()
    {

    }



    public function testGetLocalDir()
    {

    }
}
