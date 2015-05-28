<?php
use Pimple\Container;


class PimpleTest extends PHPUnit_Framework_TestCase {

    private $dic;


    protected function setUp()
    {
        $this->dic = new Container();
        $this->dic['POST'] = array();
        $this->dic['add'] = $this->dic->protect(function ($x,$y) {
            return $x + $y;
        });

    }

    public function testPimple()
    {
        $this->assertTrue(isset($this->dic['POST']));
        $this->assertFalse(isset($this->dic['unset']));
        $this->assertFalse(isset($this->dic['POST']['unset']));

        $result = $this->dic['add'](2,3);
        $this->assertEquals(5, $result);
    }
}
/*EOF*/