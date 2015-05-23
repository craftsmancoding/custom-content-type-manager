<?php

use Neomerx\JsonApi\Encoder\Encoder;
use \Neomerx\JsonApi\Encoder\JsonEncodeOptions;

class JsonApiTest extends PHPUnit_Framework_TestCase {

    public function testCreateDirectories()
    {



        $encoder = Encoder::instance([
            '\Author'  => '\AuthorSchema',
        ], new JsonEncodeOptions(JSON_PRETTY_PRINT));

        echo $encoder->encode($author) . PHP_EOL;
    }
}