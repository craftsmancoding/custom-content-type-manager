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
        // "type" and "id" are reserved keywords and cannot be used as resource object attributes
        return array(
            'discriminator'  => $field->get('discriminator'),
            'label'  => $field->get('label'),
            'description'  => $field->get('description'),
            'class'  => $field->get('class'),
            'extra'  => $field->get('extra'),
            'default_value'  => $field->get('default_value'),
            'default_filter'  => $field->get('default_filter'),
            'validator'  => $field->get('validator'),
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
        return $resource->getResourceUrl($this->resourceType, $this->getId($resource));
    }

}
/*EOF*/