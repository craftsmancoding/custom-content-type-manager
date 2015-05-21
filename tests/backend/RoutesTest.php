<?php
use Pimple\Container;
use CCTM\Routes;
use CCTM\Exceptions\NotFoundException;

class RoutesTest extends PHPUnit_Framework_TestCase {

    private $dic;

    protected function setUp()
    {
        $this->dic = new Container();
        $this->dic['POST'] = array();
    }

    public function testPimple()
    {
        $this->assertTrue(isset($this->dic['POST']));
        $this->assertFalse(isset($this->dic['unset']));
        $this->assertFalse(isset($this->dic['POST']['unset']));
    }

    /**
     *
     *
     */
    public function testInput()
    {
        $this->dic = new Container();
        $this->dic['GET'] = array(
            'x' => 'x-ray'
        );

        $R = new Routes($this->dic);
        $actual = $R->input('x');
        $this->assertEquals('x-ray',$actual);

    }

    public function testGetVerb()
    {
        $dic = new Container();
        $dic['POST'] = array(
            'x' => 'x-ray'
        );

        $dic['GET'] = array();
        $R = new Routes($dic);

        $actual = $R->getVerb();
        $this->assertEquals('get',$actual);
    }

    public function testGetResourceName()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'valid'
        );

        $R = new Routes($dic);
        $actual = $R->getResourceName();
        $this->assertEquals('Valid',$actual);
    }

    /**
     * @expectedException \CCTM\Exceptions\NotFoundException
     */
    public function testNotFound()
    {
        $R = new Routes($this->dic);
        $R->getResourceName();
    }

    /**
     * @expectedException \CCTM\Exceptions\InvalidVerbException
     */
    public function testInvalidVerb()
    {
        $this->dic = new Container();
        $this->dic['GET'] = array(
            '_verb' => 'invalid'
        );

        $R = new Routes($this->dic);
        $R->handle();
    }

    /**
     * @expectedException \CCTM\Exceptions\NotFoundException
     */
    public function testInvalidResourceName1()
    {
        $this->dic = new Container();
        $this->dic['GET'] = array(
            '_resource' => array('invalid')
        );

        $R = new Routes($this->dic);
        $R->handle();
    }

    /**
     * @expectedException \CCTM\Exceptions\NotFoundException
     */
    public function testInvalidResourceName2()
    {
        $this->dic = new Container();
        $this->dic['GET'] = array(
            '_resource' => 'not-valid-classname'
        );

        $R = new Routes($this->dic);
        $R->handle();
    }

    public function testGetControllerName()
    {
        $R = new Routes($this->dic);

        $this->assertEquals('SomethingController', $R->getControllerName('something'));
    }


    public function testGetMethodName()
    {
        $R = new Routes($this->dic);

        $this->assertEquals('getCollection',$R->getMethodName('get'));
        $this->assertEquals('getResource',$R->getMethodName('get', 123));
        $this->assertEquals('createResource',$R->getMethodName('post'));
        $this->assertEquals('updateResource',$R->getMethodName('post', 123));
        $this->assertEquals('overwriteResource',$R->getMethodName('put', 123));
        $this->assertEquals('deleteResource',$R->getMethodName('delete', 123));
    }

    /**
     * @expectedException \CCTM\Exceptions\NotAllowedException
     */
    public function testDisallowedMethodNames1()
    {
        $R = new Routes($this->dic);
        $R->getMethodName('put');
    }


    /**
     * @expectedException \CCTM\Exceptions\NotAllowedException
     */
    public function testDisallowedMethodNames2()
    {
        $R = new Routes($this->dic);
        $R->getMethodName('delete');
    }

    public function testHandler()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'Test'
        );
        $dic['TestController'] = function ($c) {
            return \Mockery::mock('TestController')
                ->shouldReceive('getCollection')
                ->andReturn('Yankee Doodle')
                ->getMock();
        };

        $R = new Routes($dic);
        $actual = $R->handle();
        $this->assertEquals('Yankee Doodle',$actual);

        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'Test',
            '_id' => 123
        );
        $dic['TestController'] = function ($c) {
            return \Mockery::mock('TestController')
                ->shouldReceive('getResource')
                ->andReturn('Fuzzcake')
                ->getMock();
        };

        $R = new Routes($dic);
        $actual = $R->handle();
        $this->assertEquals('Fuzzcake',$actual);
    }
}
