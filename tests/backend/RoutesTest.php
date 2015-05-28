<?php
use Pimple\Container;
use CCTM\Routes;
use CCTM\Exceptions\NotFoundException;

class RoutesTest extends PHPUnit_Framework_TestCase {

    private $dic;
    private $callback;

    protected function setUp()
    {
        $this->dic = new Container();
        $this->dic['POST'] = array();
        $this->callback = function($out,$code){};
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

        $R = new Routes($this->dic, $this->callback);
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
        $R = new Routes($dic, $this->callback);

        $actual = $R->getVerb();
        $this->assertEquals('get',$actual);
    }

    public function testGetResourceName()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'fields'
        );

        $R = new Routes($dic, $this->callback);
        $actual = $R->getResourceName();
        $this->assertEquals('Fields',$actual);
    }

    /**
     * @expectedException \CCTM\Exceptions\NotFoundException
     * @expectedExceptionCode 40400
     */
    public function testNotFound()
    {
        $R = new Routes($this->dic, $this->callback);
        $R->getResourceName();
    }

    /**
     * @expectedException \CCTM\Exceptions\InvalidVerbException
     * @expectedExceptionCode 50100
     */
    public function testInvalidVerb()
    {
        $this->dic = new Container();
        $this->dic['GET'] = array(
            '_verb' => 'invalid'
        );

        $R = new Routes($this->dic, $this->callback);
        $R->getVerb();
    }

    /**
     * @expectedException \CCTM\Exceptions\NotFoundException
     * @expectedExceptionCode 40410
     */
    public function testInvalidResourceName1()
    {
        $this->dic = new Container();
        $this->dic['GET'] = array(
            '_resource' => array('invalid')
        );

        $R = new Routes($this->dic, $this->callback);
        $R->getResourceName();
    }

    /**
     * @expectedException \CCTM\Exceptions\NotFoundException
     * @expectedExceptionCode 40420
     */
    public function testInvalidResourceName2()
    {
        $this->dic = new Container();
        $this->dic['GET'] = array(
            '_resource' => 'not-valid-classname'
        );

        $R = new Routes($this->dic, $this->callback);
        $R->getResourceName();
    }

    public function testGetControllerName()
    {
        $R = new Routes($this->dic, $this->callback);

        $this->assertEquals('SomethingController', $R->getControllerName('something'));
    }


    public function testGetMethodName()
    {
        $R = new Routes($this->dic, $this->callback);

        $this->assertEquals('getCollection',$R->getMethodName('get'));
        $this->assertEquals('getResource',$R->getMethodName('get', 123));
        $this->assertEquals('createResource',$R->getMethodName('post'));
        $this->assertEquals('updateResource',$R->getMethodName('post', 123));
        $this->assertEquals('patchResource',$R->getMethodName('patch', 123));
        $this->assertEquals('deleteResource',$R->getMethodName('delete', 123));
    }

    /**
     * @expectedException \CCTM\Exceptions\NotAllowedException
     * @expectedExceptionCode 40000
     */
    public function testDisallowedMethodNames1()
    {
        $R = new Routes($this->dic, $this->callback);
        $R->getMethodName('patch');
    }


    /**
     * @expectedException \CCTM\Exceptions\NotAllowedException
     * @expectedExceptionCode 40010
     */
    public function testDisallowedMethodNames2()
    {
        $R = new Routes($this->dic, $this->callback);
        $R->getMethodName('delete');
    }

    public function testHandler()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'Fields'
        );
        $dic['FieldsController'] = function ($c) {
            return \Mockery::mock('FieldsController')
                ->shouldReceive('getCollection')
                ->andReturn('Yankee Doodle')
                ->getMock();
        };

        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();
        $this->assertEquals('Yankee Doodle',$actual);

        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'Fields',
            '_id' => 123
        );
        $dic['FieldsController'] = function ($c) {
            return \Mockery::mock('FieldsController')
                ->shouldReceive('getResource')
                ->andReturn('Fuzzcake')
                ->getMock();
        };

        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();
        $this->assertEquals('Fuzzcake',$actual);
    }

}
