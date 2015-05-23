<?php namespace CCTM\Model;

class Field extends FilebasedModel
{
    function hydrate()
    {
        return array(
            'id' => '',
            'label' => '',
            'description' => '',
            'class' => '',
            'extra' => '',
            'default_value' => '',
            'default_filter' => '',
            'meta' => array()
        );
    }
}
/*EOF*/