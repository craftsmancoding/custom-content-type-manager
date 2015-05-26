<?php namespace CCTM\Schema;

use \Neomerx\JsonApi\Schema\SchemaProvider;

class FieldSchema extends SchemaProvider
{

    protected $resourceType = 'fields';
    protected $baseSelfUrl  = 'http://example.com/fields';

    public function getId($field)
    {
        return $field->get('id');
    }
    public function getAttributes($field)
    {
        return array(
            'id'  => $field->get('id'),
            'type'  => $field->get('type'),
            'label'  => $field->get('label'),
            'description'  => $field->get('description'),
            'class'  => $field->get('class'),
            'extra'  => $field->get('extra'),
            'default_value'  => $field->get('default_value'),
            'default_filter'  => $field->get('default_filter'),
            'meta'  => $field->get('meta'),
        );
        // return $field->toArray();
    }

    public function getLinks($field)
    {
        return array();
//        return [
//            'author'   => [self::DATA => $field->author],
//            'comments' => [self::DATA => $field->comments],
//        ];
    }


    public function getSelfUrl($resource)
    {
        return '/asdfasdf';
        // TODO: set dynamically per installation location
        return $this->getBaseSelfUrl($resource).$this->getId($resource);
    }

}
/*EOF*/