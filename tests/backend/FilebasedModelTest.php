<?php

use Pimple\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;
use CCTM\Model\FilebasedModel;

class FilebasedModelTest extends PHPUnit_Framework_TestCase {

    public $dic;

    public function setUp()
    {
        $this->dic = new Container();
        $this->dic['storage_dir'] = __DIR__.'/tmp/';
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
        $this->dic['Validator'] = function ($c)
        {
            return \Mockery::mock('CCTM\\Interfaces\\ValidatorInterface')
                ->shouldReceive('validate')
                ->andReturn(true)
                ->getMock();
        };

    }

    // Root directories cannot be deleted
    public function tearDown()
    {
        $FS = new Filesystem(new Adapter(__DIR__));
        $FS->deleteDir('/tmp/');
    }


    public function testIsNew()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $this->assertTrue($M->isNew());

        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->fromArray(array('a'=>'apple','id'=>'test'));
        $this->assertTrue($M->isNew());

        $M->save();
        $this->assertFalse($M->isNew());

        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->fromArray(array('a' => 'apple'));
        $this->assertTrue($M->isNew());
        $test = $M->getOne('test');
        $this->assertFalse($test->isNew());

    }

    /**
     * @expectedException CCTM\Exceptions\NotFoundException
     */
    public function testGetOneFail()
    {
        // setUp
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $Item = $M->getOne('does-not-exist');
    }

    /**
     * @expectedException CCTM\Exceptions\NotFoundException
     */
    public function testGetFilename()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->getFilename('../../usr/password');
    }

    /**
     * @expectedException CCTM\Exceptions\NotFoundException
     */
    public function testGetFilename2()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->getFilename('');
    }

    public function testGetOne()
    {
        $M = new FilebasedModel($this->dic, new Filesystem(new Adapter(__DIR__.'/storage_dir/')), $this->dic['Validator']);
        $apple = $M->getOne('a');

        $this->assertEquals('banana', $apple->get('b'));

    }

    public function testGetId()
    {
        $M = new FilebasedModel($this->dic, new Filesystem(new Adapter(__DIR__.'/storage_dir/')), $this->dic['Validator']);
        $this->assertNull($M->getId());
        $one = $M->getOne('a');
        $this->assertEquals('a', $one->getId());
    }

    public function testSave()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();

        $this->assertTrue($this->dic['Filesystem']->has('x.json'));
    }


    public function testSetDeep()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->fromArray(array('x'=>array('y'=>'z')));

        $this->assertEquals('z',$M->get('x.y'));

        $M->fromArray(array('x'=>array('cat','dog','goat')));
        $this->assertTrue(is_array($M->get('x')));

    }

    public function testGetCollection()
    {
        $this->dic['Filesystem'] = function ($c)
        {
            return new Filesystem(new Adapter($c['storage_dir'].'collection/'));
        };

        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);

        // Add a non JSON file to the mix... make sure it is skipped.
        $this->dic['Filesystem']->put('a.json', '{"a":"apple"}');
        $this->dic['Filesystem']->put('b.json', '{"b":"banana"}');
        $this->dic['Filesystem']->put('fly.txt', 'In the ointment');

        $x = $M->getCollection();
        $this->assertEquals(2, count($x));

    }


    public function testDuplicate()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();
        $this->assertTrue($this->dic['Filesystem']->has('x.json'));
        $this->assertFalse($this->dic['Filesystem']->has('y.json'));
        $M->duplicate('y');
        $this->assertTrue($this->dic['Filesystem']->has('x.json'));
        $this->assertTrue($this->dic['Filesystem']->has('y.json'));
    }

    public function testDelete()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();

        $this->assertTrue($this->dic['Filesystem']->has('x.json'));

        $M->delete();
        $this->assertFalse($this->dic['Filesystem']->has('x.json'));
    }

    public function testRename()
    {
        $M = new FilebasedModel($this->dic, $this->dic['Filesystem'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();

        $this->assertTrue($this->dic['Filesystem']->has('x.json'));
        $this->assertFalse($this->dic['Filesystem']->has('zz.json'));

        $M->rename('zz');

        //$c = $this->dic['Filesystem']->listContents('/');
        //print_r($c);
        $this->assertTrue($this->dic['Filesystem']->has('zz.json'));
        $this->assertFalse($this->dic['Filesystem']->has('x.json'));
    }
}
