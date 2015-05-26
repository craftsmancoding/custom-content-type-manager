<?php namespace CCTM\Model;

class Field extends FilebasedModel
{
    function hydrate()
    {
        return array(
            'id' => '',
            'type' => '', // full PHP class name, a la Single Table Inheritance
            'label' => '',
            'description' => '',
            'css_class' => '',
            'extra' => '',
            'default_value' => '',
            'default_filter' => '',
            'validator' => '',
            'meta' => array()
        );
    }
}
/*EOF*/