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
     */
    public function testNotFound()
    {
        $R = new Routes($this->dic, $this->callback);
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

        $R = new Routes($this->dic, $this->callback);
        $R->getVerb();
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

        $R = new Routes($this->dic, $this->callback);
        $R->getResourceName();
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
        $this->assertEquals('putResource',$R->getMethodName('put', 123));
        $this->assertEquals('deleteResource',$R->getMethodName('delete', 123));
    }

    /**
     * @expectedException \CCTM\Exceptions\NotAllowedException
     */
    public function testDisallowedMethodNames1()
    {
        $R = new Routes($this->dic, $this->callback);
        $R->getMethodName('put');
    }


    /**
     * @expectedException \CCTM\Exceptions\NotAllowedException
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


    public function testErrorResponse()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'invalid',
        );

        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(404, $data->errors[0]->status);
    }

    public function testErrorResponse2()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'fields',
            '_id' => 'doest-not-exist'
        );
        $dic['FieldsController'] = function ($c) {
            return \Mockery::mock('FieldsController')
                ->shouldReceive('getResource')
                ->andThrow('\\CCTM\\Exceptions\\FileNotFoundException','OMG!!')
                ->getMock();
        };

        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();
print $actual; exit;
        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(404, $data->errors[0]->status);
    }
}
