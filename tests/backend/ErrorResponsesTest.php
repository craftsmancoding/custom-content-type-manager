<?php
use Pimple\Container;
use CCTM\Routes;


class ErrorResponsesTest extends PHPUnit_Framework_TestCase {

    private $dic;
    private $callback;

    protected function setUp()
    {
        $this->dic = new Container();
        $this->dic['POST'] = array();
        $this->callback = function($out,$code){};
    }

    

    public function test40000()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'fields',
            '_verb' => 'patch'
        );
        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(400, $data->errors[0]->status);
        $this->assertEquals(40000, $data->errors[0]->code);
    }

    public function test40010()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'fields',
            '_verb' => 'delete'
        );
        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(400, $data->errors[0]->status);
        $this->assertEquals(40010, $data->errors[0]->code);
    }

    public function test40400()
    {
        $dic = new Container();
        $dic['GET'] = array(

        );
        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(404, $data->errors[0]->status);
        $this->assertEquals(40400, $data->errors[0]->code);
    }

    public function test40410()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => array('ruh-roh'),
        );
        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(404, $data->errors[0]->status);
        $this->assertEquals(40410, $data->errors[0]->code);
    }

    public function test40420()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'ruh-roh',
        );
        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(404, $data->errors[0]->status);
        $this->assertEquals(40420, $data->errors[0]->code);
    }

    public function test40450()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'fields',
            '_id' => 'does-not-exist'
        );

        $dic['FieldsController'] = function ($c) {
            $x = new \CCTM\Exceptions\FileNotFoundException('Invalid File Name', 40450, array(
                'id' => 'FileNotFoundException',
                'href' => '',
                'status' => 404,
                'detail' => 'Files must be identified by stubs only, without extensions.  Directory traversing and sub-directories are not allowed',
            ));
            return \Mockery::mock('FieldsController')
                ->shouldReceive('getResource')
                ->andThrow($x)
                ->getMock();
        };
        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(404, $data->errors[0]->status);
        $this->assertEquals(40450, $data->errors[0]->code);

    }

    public function test50100()
    {
        $dic = new Container();
        $dic['GET'] = array(
            '_resource' => 'fields',
            '_verb' => 'invalid'
        );


        $R = new Routes($dic, $this->callback);
        $actual = $R->handle();

        $this->assertTrue(!empty($actual));
        $data = json_decode($actual);
        $this->assertTrue(is_object($data));
        $this->assertTrue(isset($data->errors));
        $this->assertTrue(is_array($data->errors));
        $this->assertEquals(501, $data->errors[0]->status);
        $this->assertEquals(50100, $data->errors[0]->code);

    }

}
