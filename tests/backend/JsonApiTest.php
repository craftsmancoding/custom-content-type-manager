<?php

use Pimple\Container;

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;

use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\JsonEncodeOptions;

/**
 * Class JsonApiTest
 *
 * A bit of proofs for figuring out how the neomerx/json-api stuff works.
 */
class JsonApiTest extends PHPUnit_Framework_TestCase {

    public $dic;

    private $resource;
    private $schema;

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
        $this->dic['JsonApi'] = function ($c) {
            return Encoder::instance(array(
                'CCTM\\Model\\Field'  => 'CCTM\\Schema\\FieldSchema',
            ), new JsonEncodeOptions(JSON_PRETTY_PRINT));
        };
        $this->dic['resource_url'] = $this->dic->protect(function ($resource,$id) {
            return 'http://example.com/?action=cctm&_resource='.$resource.'&id='.$id;
        });

    }

    public function testResourceResponse()
    {
        $encoder = Encoder::instance([
            'CCTM\\Model\\Field'      => 'CCTM\\Schema\\FieldSchema',

        ], new JsonEncodeOptions(JSON_PRETTY_PRINT));

        $F = new CCTM\Model\Field($this->dic, __DIR__.'/defs/',$this->dic['Validator']);
        $F->fromArray(
            array(
                'id' => 'mytext',
                'discriminator' => 'CCTM\\Fields\\Text',
                'label' => 'My Text Field',
                'description' => 'Just a description',
                'css_class' => 'cctm-text',
                'extra' => '',
                'default_value' => '12',
                'default_filter' => '',
                'validator' => '',
                'meta' => array()
            )
        );
        //print get_class($this->resource); exit;
        $out = $encoder->encode($F);

        $expected = file_get_contents(__DIR__.'/responses/resource.json');
        $this->assertEquals(json_decode($expected), json_decode($out));
    }

    public function testCollectionResponse()
    {
        $encoder = Encoder::instance([
            'CCTM\\Model\\Field'      => 'CCTM\\Schema\\FieldSchema',

        ], new JsonEncodeOptions(JSON_PRETTY_PRINT));

        $F = new CCTM\Model\Field($this->dic, __DIR__.'/defs/',$this->dic['Validator']);
        $F->fromArray(
            array(
                'id' => 'mytext',
                'discriminator' => 'CCTM\\Fields\\Text',
                'label' => 'My Text Field',
                'description' => 'Just a description',
                'css_class' => 'cctm-text',
                'extra' => '',
                'default_value' => '12',
                'default_filter' => '',
                'validator' => array(
                    'discriminator' => '',
                    'meta' => array()
                ),
                'meta' => array()
            )
        );
        $collection[] = $F;

        $F2 = new CCTM\Model\Field($this->dic, __DIR__.'/defs/',$this->dic['Validator']);
        $F2->fromArray(
            array(
                'id' => 'othertext',
                'discriminator' => 'CCTM\\Fields\\Text',
                'label' => 'Some other Text Field',
                'description' => 'another a description',
                'css_class' => 'cctm-text',
                'extra' => '',
                'default_value' => '13',
                'default_filter' => '',
                'validator' => array(
                    'discriminator' => '',
                    'meta' => array()
                ),
                'meta' => array()
            )
        );
        $collection[] = $F2;

        //print get_class($this->resource); exit;
        $out = $encoder->encode($collection);

        $expected = file_get_contents(__DIR__.'/responses/collection.json');
        $this->assertEquals(json_decode($expected), json_decode($out));
    }

    public function testErrorResponse()
    {
        $error = new \Neomerx\JsonApi\Document\Error(
            (string) 'MyErrorId',
            'http://github.com/somewhere/wiki',
            (string) 500, // HTTP status code
            (string) 50066, // force this to be a string
            (string) 'There was a Problem',
            (string) "Details go here."
        );

        $out = Encoder::instance(array(), new JsonEncodeOptions(JSON_PRETTY_PRINT))->error($error);

        $expected = file_get_contents(__DIR__.'/responses/error.json');
        $this->assertEquals(json_decode($expected), json_decode($out));
    }

}