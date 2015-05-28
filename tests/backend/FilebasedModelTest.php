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

        $this->dic['Filesystem'] = $this->dic->protect(function ($dir) {
            return new Filesystem(new Adapter($dir));
        });
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
        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $this->assertTrue($M->isNew());

        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('a'=>'apple','id'=>'test'));
        $this->assertTrue($M->isNew());

        $M->save();
        $this->assertFalse($M->isNew());

        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('a' => 'apple'));
        $this->assertTrue($M->isNew());
        $test = $M->getOne('test');
        $this->assertFalse($test->isNew());

    }

    /**
     * @expectedException CCTM\Exceptions\FileNotFoundException
     * @expectedExceptionCode 50000
     */
    public function testGetOneFail()
    {
        // setUp
        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $Item = $M->getOne('does_not_exist');
    }

    /**
     * @expectedException CCTM\Exceptions\FileNotFoundException
     * @expectedExceptionCode 40450
     */
    public function testGetFilename()
    {
        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->getFilename('../../usr/password');
    }

    /**
     * @expectedException CCTM\Exceptions\FileNotFoundException
     * @expectedExceptionCode 40450
     */
    public function testGetFilename2()
    {
        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->getFilename('dashes-not-allowed');
    }

    /**
     * @expectedException CCTM\Exceptions\FileNotFoundException
     * @expectedExceptionCode 40450
     */
    public function testGetFilename3()
    {
        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->getFilename('');
    }

    public function testGetOne()
    {
        $M = new FilebasedModel($this->dic, __DIR__.'/storage_dir/', $this->dic['Validator']);
        $apple = $M->getOne('apple');

        $this->assertEquals('banana', $apple->get('b'));

    }

    public function testGetId()
    {
        $M = new FilebasedModel($this->dic, __DIR__.'/storage_dir/', $this->dic['Validator']);
        $this->assertNull($M->getId());
        $one = $M->getOne('apple');
        $one->setPk('a');
        $this->assertEquals('apple', $one->getId());
    }

    public function testSave()
    {
        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();
        $FS = $this->dic['Filesystem']($this->dic['storage_dir']);
        $this->assertTrue($FS->has('x.json'));
    }

    /**
     * @expectedException CCTM\Exceptions\InvalidAttributesException
     * @expectedExceptionCode 50020
     */
    public function testSaveFail()
    {
        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','missingid'=>'x'));

        $M->save();

    }

    /**
     * @expectedException CCTM\Exceptions\InvalidAttributesException
     * @expectedExceptionCode 40020
     */
    public function testSaveFail2()
    {
        $this->dic['Validator'] = function ($c)
        {
            return \Mockery::mock('CCTM\\Interfaces\\ValidatorInterface')
                ->shouldReceive('validate')
                ->andReturn(false)
                ->shouldReceive('getMessages')
                ->andReturn('what went wrong...')
                ->getMock();
        };

        $M = new FilebasedModel($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();

    }


    public function testSetDeep()
    {
        $M = new FilebasedModel($this->dic, $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>array('y'=>'z')));

        $this->assertEquals('z',$M->get('x.y'));

        $M->fromArray(array('x'=>array('cat','dog','goat')));
        $this->assertTrue(is_array($M->get('x')));

    }

    public function testGetCollection()
    {
        $M = new FilebasedModel($this->dic, $this->dic['storage_dir'].'collection/', $this->dic['Validator']);

        // Add a non JSON file to the mix... make sure it is skipped.
        $FS = $this->dic['Filesystem']($this->dic['storage_dir'].'collection/');
        $FS->put('a.json', '{"a":"apple"}');
        $FS->put('b.json', '{"b":"banana"}');
        $FS->put('fly.txt', 'In the ointment');

        $x = $M->getCollection();
        $this->assertEquals(2, count($x));

    }


    public function testDuplicate()
    {
        $M = new FilebasedModel($this->dic, $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();
        $FS = $this->dic['Filesystem']($this->dic['storage_dir']);
        $this->assertTrue($FS->has('x.json'));
        $this->assertFalse($FS->has('y.json'));
        $M->duplicate('y');
        $this->assertTrue($FS->has('x.json'));
        $this->assertTrue($FS->has('y.json'));
    }

    /**
     * @expectedException CCTM\Exceptions\FileExistsException
     * @expectedExceptionCode 40900
     */
    public function testDuplicateFail()
    {
        $M = new FilebasedModel($this->dic, $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));
        $M->save();

        $M2 = new FilebasedModel($this->dic, $this->dic['storage_dir'], $this->dic['Validator']);
        $M2->fromArray(array('y'=>'Yoyo','id'=>'y'));
        $M2->save();


        $M2->duplicate('x');

    }

    public function testDelete()
    {
        $M = new FilebasedModel($this->dic, $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();
        $FS = $this->dic['Filesystem']($this->dic['storage_dir']);
        $this->assertTrue($FS->has('x.json'));

        $M->delete();
        $this->assertFalse($FS->has('x.json'));
    }

    public function testRename()
    {
        $M = new FilebasedModel($this->dic, $this->dic['storage_dir'], $this->dic['Validator']);
        $M->fromArray(array('x'=>'Xerxes','id'=>'x'));

        $M->save();
        $FS = $this->dic['Filesystem']($this->dic['storage_dir']);
        $this->assertTrue($FS->has('x.json'));
        $this->assertFalse($FS->has('zz.json'));

        $M->rename('zz');

        $this->assertTrue($FS->has('zz.json'));
        $this->assertFalse($FS->has('x.json'));
    }
}
