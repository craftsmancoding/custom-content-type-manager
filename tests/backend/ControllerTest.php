<?php
use Pimple\Container;
use CCTM\Routes;
use CCTM\Exceptions\NotFoundException;

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

    protected function setUp()
    {
        $this->dic         = new Container();
        $this->dic['POST'] = array();
        $this->dic['render_callback'] = $this->dic->protect(function ($out) {
            // do nothing
        });

//        $this->resource = $this->getMockBuilder('\\CCTM\\Interfaces\\ResourceInterface')
//            ->getMock();
        $this->resource = \Mockery::mock('\\CCTM\\Interfaces\\ResourceInterface')
            ->shouldReceive('getOne')
            ->andReturn(\Mockery::self())
            ->shouldReceive('getCollection')
            ->andReturn(array('of','stuff'))
            ->shouldReceive('delete')
            ->andReturn(true)
            ->getMock();

        $this->controller = $this->getMockBuilder('\\CCTM\\Controller\\ResourceController')
            ->setConstructorArgs(array($this->dic, $this->resource, $this->dic['render_callback']))
            ->getMockForAbstractClass();
    }

    // This is a bit hard to mock
    public function testConstructor()
    {
        $resource = $this->getMockBuilder('\\CCTM\\Interfaces\\ResourceInterface')
        ->getMock();

        $controller = $this->getMockBuilder('\\CCTM\\Controller\\ResourceController')
            ->setConstructorArgs(array($this->dic, $resource, $this->dic['render_callback']))
            ->getMockForAbstractClass();
    }

    public function testRender()
    {
        $this->assertEquals('garbage', $this->controller->render('garbage'));
    }

    public function testResponseCode()
    {
        $this->controller->setResponseCode(300);
        $this->assertEquals(300, $this->controller->getResponseCode());
    }

    public function testGetResource()
    {
        $r = $this->controller->getResource('test');
        $this->assertTrue(is_a($r, '\\CCTM\\Interfaces\\ResourceInterface'));
    }

    public function testGetCollection()
    {
        $r = $this->controller->getCollection('test');
        $this->assertTrue(is_array($r));
        $this->assertEquals(array('of','stuff'), $r);
    }

    public function testDeleteResource()
    {
        $r = $this->controller->deleteResource('test');
    $this->assertTrue($r);
    }

    public function testCreateResource()
    {

    }

    public function testUpdateResource()
    {

    }

    public function testPutResource()
    {

    }
}
/*EOF*/