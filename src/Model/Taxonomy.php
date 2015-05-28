<?php namespace CCTM\Model;

class Taxonomy extends FilebasedModel
{
    function hydrate()
    {
        // "type" and "id" are reserved keywords and cannot be used as resource object attributes
        return array(
            'id' => '',
            'discriminator' => '', // full PHP class name, a la Single Table Inheritance
            'label' => '',
            'description' => '',
            'css_class' => '',
            'extra' => '',
            'default_value' => '',
            'default_filter' => '',
            'validator' => array(
                'discriminator' => '',
                'meta' => array(),
            ),
            'meta' => array()
        );
    }
}
/*EOF*/