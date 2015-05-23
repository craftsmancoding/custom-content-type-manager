<?php namespace CCTM\Schema;
use \Neomerx\JsonApi\Schema\SchemaProvider;

class PosttypeSchema extends SchemaProvider
{

    protected $resourceType = 'posts';
    protected $baseSelfUrl  = 'http://example.com/posts';

    public function getId($posttype)
    {
        return $posttype->post_type;
    }
    public function getAttributes($post)
    {
        /** @var Post $post */
        return [
            'title' => $post->title,
            'body'  => $post->body,
        ];
    }
    public function getLinks($post)
    {
        /** @var Post $post */
        return [
            'author'   => [self::DATA => $post->author],
            'comments' => [self::DATA => $post->comments],
        ];
    }

}
/*EOF*/