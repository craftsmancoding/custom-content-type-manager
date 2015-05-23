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
        return $field->toArray();
    }

    public function getLinks($field)
    {

        return [
            'author'   => [self::DATA => $field->author],
            'comments' => [self::DATA => $field->comments],
        ];
    }

}
/*EOF*/