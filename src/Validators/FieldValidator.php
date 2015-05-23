<?php namespace CCTM\Validators;

use Particle\Validator\Validator;

class FieldValidator extends BaseValidator
{

    public function validate(array $array, $context=null)
    {


        $this->validator->context('create', function(Validator $context) {
            $context->required('id')
                ->lengthBetween(1, 30)
                ->regex('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/')
            ;
            $context->required('label')->lengthBetween(2, 300);
            $context->optional('description')->lengthBetween(2, 300);
            $context->optional('class')
                ->lengthBetween(2, 300)
                ->regex('/^[a-zA-Z_\-\x7f-\xff][a-zA-Z0-9\-_\x7f-\xff]*$/');
            $context->optional('extra');
            $context->optional('default_value');
            $context->optional('default_filter');

        });

        $this->validator->context('update', function(Validator $context) {
            // copy the rules (and messages) of the "insert" context.
            $context->copyContext('create', function($rules) {
                $context->optional('first_name');
            });
        });

        return $this->validator->validate($array, $context);

    }

}
/*EOF*/