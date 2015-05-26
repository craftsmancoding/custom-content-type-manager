<?php

use Pimple\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

use Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\JsonEncodeOptions;

class JsonApiTest extends PHPUnit_Framework_TestCase {

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

    public function testXX()
    {

        $field = new \CCTM\Model\Field($this->dic, $this->dic['Filesystem'],$this->dic['Validator']);

        $encoder = Encoder::instance([
            'CCTM\Model\Field'  => 'CCTM\Schema\FieldSchema',
        ], new JsonEncodeOptions(JSON_PRETTY_PRINT));

        echo $encoder->encode($field) . PHP_EOL;
    }

    public function testError()
    {
        $error = new \Neomerx\JsonApi\Document\Error(
            'idx',
            'href',
            'status', // HTTP status code
            'code', // app specific code
            'title',
            'detail');

        $encoder = Encoder::instance([
            'CCTM\Model\Field'  => 'CCTM\Schema\FieldSchema',
            'CCTM\Model\Filter'  => 'CCTM\Schema\FilterSchema',
        ], new JsonEncodeOptions(JSON_PRETTY_PRINT));

        print $encoder->error($error);

    }
}