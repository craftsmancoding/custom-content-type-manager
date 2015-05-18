<?php

use Pimple\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;
use CCTM\Model\BaseModel;

class BaseModelTest extends PHPUnit_Framework_TestCase {

    public $dic;

    //public $subdir = '/tmp/';

    public function setUp()
    {
        $this->dic = new Container();
        $this->dic['storage_dir'] = __DIR__;
        $this->dic['subdir'] = '/tmp/';
        $this->dic['pk'] = 'pk';
        $this->dic['Filesystem'] = function ($c)
        {
            return new Filesystem(new Adapter($c['storage_dir']));
        };
        $this->dic['JsonDecoder'] = function ($c)
        {
            return new JsonDecoder();
        };
        $this->dic['JsonEncoder'] = function ($c)
        {
            return new JsonEncoder();
        };


    }
    // Root directories cannot be deleted
    public function tearDown()
    {

        $this->dic['Filesystem']->deleteDir($this->dic['subdir']);
    }



    public function testGetLocalDir()
    {
        $M = new BaseModel($this->dic);

        $dir = $M->getLocalDir();

        $this->assertEquals('tmp/', $dir);
    }

    public function testIsNew()
    {
        $M = new BaseModel($this->dic);
        $this->assertFalse($M->isNew());

        $M = new BaseModel($this->dic, array('a'=>'apple'));
        $this->assertTrue($M->isNew());

        $M->save();
        $this->assertFalse($M->isNew());

        $dic = new Container();
        $dic['storage_dir'] = __DIR__;
        $dic['subdir'] = '/storage_dir/';
        $dic['pk'] = 'pk';
        $dic['Filesystem'] = function ($c)
        {
            return new Filesystem(new Adapter($c['storage_dir']));
        };
        $dic['JsonDecoder'] = function ($c)
        {
            return new JsonDecoder();
        };
        $dic['JsonEncoder'] = function ($c)
        {
            return new JsonEncoder();
        };

        $M = new BaseModel($dic, array('a'=>'apple'));
        $this->assertTrue($M->isNew());
        $M->getItem('a');
        $this->assertFalse($M->isNew());

    }

    /**
     * @expectedException CCTM\Exceptions\NotFoundException
     */
    public function testGetItemFail()
    {
        // setUp
        $M = new BaseModel($this->dic);
        $Item = $M->getItem('does-not-exist');
    }

    public function testGetItem()
    {
        // Different setup for the REAL directory
        $dic = new Container();
        $dic['storage_dir'] = __DIR__;
        $dic['subdir'] = '/storage_dir/';
        $dic['pk'] = 'pk';
        $dic['Filesystem'] = function ($c)
        {
            return new Filesystem(new Adapter($c['storage_dir']));
        };
        $dic['JsonDecoder'] = function ($c)
        {
            return new JsonDecoder();
        };
        $dic['JsonEncoder'] = function ($c)
        {
            return new JsonEncoder();
        };


        $M = new BaseModel($dic);
        $apple = $M->getItem('a');

        $this->assertEquals('banana', $apple->b);

    }

    public function testGetId()
    {
        // Different setup for the REAL directory
        $dic = new Container();
        $dic['storage_dir'] = __DIR__;
        $dic['subdir'] = '/storage_dir/';
        $dic['pk'] = 'pk';
        $dic['Filesystem'] = function ($c)
        {
            return new Filesystem(new Adapter($c['storage_dir']));
        };
        $dic['JsonDecoder'] = function ($c)
        {
            return new JsonDecoder();
        };
        $dic['JsonEncoder'] = function ($c)
        {
            return new JsonEncoder();
        };


        $M = new BaseModel($dic);
        $this->assertNull($M->getId());
        $M->getItem('a');
        $this->assertEquals('a', $M->getId());
    }

    public function testSave()
    {
        $M = new BaseModel($this->dic, array('x'=>'Xerxes'));
        $M->save();

     //   $this->assertTrue($this->dic['Filesystem']->has('x.json'));
    }

}
