<?php namespace CCTM\Validators;

use CCTM\Interfaces\ValidatorInterface;


abstract class BaseValidator implements ValidatorInterface
{
    public $validator;

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    public function getMessages()
    {
        return $this->validator->getMessages();
    }
}