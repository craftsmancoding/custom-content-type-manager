<?php
use Pimple\Container;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;
use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Encoder\JsonEncodeOptions;
use Webmozart\Json\JsonEncoder;
use Webmozart\Json\JsonDecoder;

/**
 * Class ControllerTest
 *
 * Testing abstract classes with constructors:
 * https://gist.github.com/loonies/1255249
 * https://jtreminio.com/2013/03/unit-testing-tutorial-part-5-mock-methods-and-overriding-constructors/
 */
class ControllerTest extends PHPUnit_Framework_TestCase
{

    private $dic;
    private $resource;
    private $controller;
    private $mock_resource;
    private $mock_controller;



    protected function setUp()
    {
        $this->dic         = new Container();
        $this->dic['POST'] = array();
        // Simulate a response
        $this->dic['render_callback'] = $this->dic->protect(function ($str, $headers=array('Content-Type: application/vnd.api+json'), $code) {
            $out = 'HTTP/1.1 '.$code."\n";
            //$out .= 'Content-Type: application/vnd.api+json'."\n";
            foreach ($headers as $h)
            {
                $out .= $h ."\n";
            }
            $out .= $str;
            return $out;

        });
        $this->dic['resource_url'] = $this->dic->protect(function ($resource,$id) {
            return 'http://example.com/?action=cctm&_resource='.$resource.'id='.$id;
        });


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
        $this->dic['JsonApiEncoder'] = function ($c) {
            return Encoder::instance(array(
                'CCTM\\Model\\Field'  => 'CCTM\\Schema\\FieldSchema',
            ), new JsonEncodeOptions(JSON_PRETTY_PRINT));
        };

        $this->resource = new \CCTM\Model\Field($this->dic,__DIR__.'/defs/fields',$this->dic['Validator']);
        $this->mock_resource = \Mockery::mock('\\CCTM\\Interfaces\\ResourceInterface')
            ->shouldReceive('getOne')
            ->andReturn(\Mockery::self())
            ->shouldReceive('delete')
            ->andReturn(true)
            ->shouldReceive('fromArray')
            ->andReturn(true)
            ->shouldReceive('getId')
            ->andReturn('test')
            ->shouldReceive('save')
            ->andReturn(true)
            ->getMock();

        $this->controller = $this->getMockBuilder('\\CCTM\\Controller\\ResourceController')
            ->setConstructorArgs(array($this->dic, $this->resource, $this->dic['render_callback']))
            ->getMockForAbstractClass();

        $this->mock_controller = $this->getMockBuilder('\\CCTM\\Controller\\ResourceController')
            ->setConstructorArgs(array($this->dic, $this->mock_resource, $this->dic['render_callback']))
            ->getMockForAbstractClass();

    }

    // This is a bit hard to mock
    public function testConstructor()
    {

        $controller = $this->getMockBuilder('\\CCTM\\Controller\\ResourceController')
            ->setConstructorArgs(array($this->dic, $this->resource, $this->dic['render_callback']))
            ->getMockForAbstractClass();
    }

    public function testRender()
    {
        $out = "HTTP/1.1 200
Content-Type: application/vnd.api+json
garbage";
        $this->assertEquals($out, $this->controller->render('garbage'));
    }

    public function testGetResource()
    {
        $out = $this->controller->getResource('mytext');

        $this->assertContains('HTTP/1.1 200', $out);
        $this->assertContains('Content-Type: application/vnd.api+json', $out);
        $this->assertContains('"type": "fields"', $out);

    }

    public function testGetCollection()
    {
        $out = $this->controller->getCollection();

        $this->assertContains('HTTP/1.1 200', $out);
        $this->assertContains('Content-Type: application/vnd.api+json', $out);
        $this->assertContains('"type": "fields"', $out);
        $this->assertContains('"id": "mytext"', $out);
        $this->assertContains('"id": "othertext"', $out);
    }

    public function testDeleteResource()
    {
        $out = $this->mock_controller->deleteResource('test');

        $this->assertContains('HTTP/1.1 204', $out);

    }

    public function testCreateResource()
    {
        $out = $this->mock_controller->createResource('test');

        $this->assertContains('HTTP/1.1 204', $out);
    }

    public function testUpdateResource()
    {

    }

    public function testPatchResource()
    {

    }
}
/*EOF*/