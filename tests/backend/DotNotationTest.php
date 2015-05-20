<?php

use CCTM\Traits\DotNotation;

/**
 * Class DotNotationTest
 *
 * Testing traits is practically undocumented, but the gist is to NOT add the trait to
 * an arbitrary "container" class.
 *
 * See
 *  - https://sebastian-bergmann.de/archives/906-Testing-Traits.html
 *  - http://blog.florianwolters.de/educational/2012/09/20/Testing-Traits-with-PHPUnit/
 *
 */

class DotNotationTest extends PHPUnit_Framework_TestCase {

    public $array = array(
        'mammals' => array(
            'cat' => 'Garfield',
            'dog' => 'Odie'
        ),
        'fish' => 'salmon',
        'tags' => array('one','two','three')
    );

    /**
     * @covers HashMap::get
     */
    public function testIsInitiallyEmpty()
    {
        // Shortening via the "use" keyword does not appear to work.
        $dot = $this->getObjectForTrait('CCTM\\Traits\\DotNotation');

        $this->assertAttributeEmpty('data', $dot);

        return $dot;
    }

    /**
     * @depends testIsInitiallyEmpty
     */
    public function testSet($dot)
    {
        $dot->set('foo', 'bar');
        $this->assertEquals('bar', $dot->get('foo'));

        $dot->set('x.y', 'z');
        $this->assertEquals('z', $dot->get('x.y'));

        $dot->set('a.b', array('c','d','e'));
        $this->assertTrue(is_array($dot->get('a.b')));

    }

    /**
     * @depends testIsInitiallyEmpty
     */
    public function testArray($dot)
    {
        $dot->fromArray();
        $this->assertEquals(array(), $dot->toArray());


        $dot->fromArray($this->array);

        $this->assertEquals($this->array, $dot->toArray());

    }

    /**
     * @depends testIsInitiallyEmpty
     */
    public function testMergeArray($dot)
    {
        $dot->fromArray($this->array);

        $new = array('mammals'=>array(
            'dog'=>'Snoopy'),
            'tags' => array('four')
        );

        $out = $dot->mergeArray($new);

        $this->assertEquals('Snoopy', $dot->get('mammals.dog'));


    }
}