<?php
use Pimple\Container;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

/**
 * Class FractalTest
 *
 * This helps us sketch out the format of our response bodies.
 */
class FractalTest extends PHPUnit_Framework_TestCase
{

    public $fractal;

    public function setUp()
    {
        $this->fractal = new Manager();
    }

    public function testCollection()
    {
        $books = [
            [
                'id' => "1",
                'title' => "Hogfather",
                'yr' => "1998",
                'author_name' => 'Philip K Dick',
                'author_email' => 'philip@example.org',
            ],
            [
                'id' => "2",
                'title' => "Game Of Kill Everyone",
                'yr' => "2014",
                'author_name' => 'George R. R. Satan',
                'author_email' => 'george@example.org',
            ]
        ];

        $resource = new Collection($books, function(array $book) {
            return [
                'id'      => (int) $book['id'],
                'title'   => $book['title'],
                'year'    => (int) $book['yr'],
                'author'  => [
                    'name'  => $book['author_name'],
                    'email' => $book['author_email'],
                ],
                'links'   => [
                    [
                        'rel' => 'self',
                        'uri' => '/books/'.$book['id'],
                    ]
                ]
            ];
        });

        //print  $this->fractal->createData($resource)->toJson();
        print_r($this->fractal->createData($resource)->toArray());

    }

}
/*EOF*/