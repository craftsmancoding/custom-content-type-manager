<?php
use CCTM\Model\Field;
use Pimple\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;


class FieldTest extends PHPUnit_Framework_TestCase
{
    private $dic;




    protected function setUp()
    {
        $this->dic = new Container();
        $this->dic['storage_dir'] = dirname(__DIR__).'/defs/fields';

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

    public function testCollection()
    {
        $F = new Field($this->dic,  $this->dic['storage_dir'], $this->dic['Validator']);

        $collection = $F->getCollection();

        foreach($collection as $c)
        {
            print_r($c->toArray());
        }
        //print_r($collection);
        exit;
    }
}