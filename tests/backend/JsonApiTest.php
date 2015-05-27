<?php

use Pimple\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;

use Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\JsonEncodeOptions;

class JsonApiTest extends PHPUnit_Framework_TestCase {

    public $dic;
    public $callback;

    public function setUp()
    {
        $this->dic = new Container();
        $this->dic['storage_dir'] = __DIR__.'/defs/';

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
        $this->dic['JsonApi'] = function ($c) {
            return Encoder::instance(array(
                'CCTM\\Model\\Field'  => 'CCTM\\Schema\\FieldSchema',
            ), new JsonEncodeOptions(JSON_PRETTY_PRINT));
        };

        $this->callback = function($out,$code){};
    }

    public function testGetResource()
    {

        $this->dic['Filesystem'] = function ($c)
        {
            return new Filesystem(new Adapter(__DIR__.'/defs/fields'));
        };
        $field = new \CCTM\Model\Field($this->dic, $this->dic['Filesystem'],$this->dic['Validator']);

        $controller = new \CCTM\Controller\FieldsController($this->dic, $field, $this->callback);
        $out = $controller->getResource('mytext');
        print_r($out); exit;
//        $f = $field->getOne('mytext');
//        print_r($f->toArray()); exit;
//        $encoder = Encoder::instance([
//            'CCTM\Model\Field'  => 'CCTM\Schema\FieldSchema',
//        ], new JsonEncodeOptions(JSON_PRETTY_PRINT));

        // echo $encoder->encode($field) . PHP_EOL;
    }


}