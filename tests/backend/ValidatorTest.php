<?php

use Pimple\Container;
use Particle\Validator\Validator;

class ValidatorTest extends PHPUnit_Framework_TestCase {

    public $dic;


    public function setUp()
    {
        $this->dic = new Container();


    }

    public function tearDown()
    {

    }


    public function testValidateArray()
    {
        $validator = new Validator();
        $validator->required('first_name')->lengthBetween(0, 20);
        $validator->optional('age')->integer();

        $this->assertTrue($validator->validate(array('first_name'=>'Bob')));
    }

    public function testValidateObject()
    {
        $validator = new Validator();
        $validator->required('first_name')->lengthBetween(0, 20);
        $validator->optional('age')->integer();

        $obj = new stdClass();
        $obj->first_name = 'Bob';
        // Won't work: https://github.com/particle-php/Validator/issues/37
        // $this->assertTrue($validator->validate($obj));
    }
}
