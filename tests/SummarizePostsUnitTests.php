<?php
/**
 * FOR THE DEVELOPER ONLY!!!
 *
 * This class contains unit tests using the SimpleTest framework: http://simpletest.org/
 * 
 * BEFORE YOU RUN TESTS
 *
 * These tests are meant to run in a controlled environment with a specific version of 
 * WordPress, with a specific theme, and with specific plugins enabled or disabled.
 * A dump of the database used is available upon request.
 *
 * RUNNING TESTS
 *
 * To run these tests, make sure you have THE test database loaded, then navigate 
 * to this file in your browser, e.g. 
 * http://cctm:8888/wp-content/plugins/custom-content-type-manager/tests/SummarizePostsUnitTests.php
 *
 * Or execute them via php on the command line:
 *	php /full/path/to/SummarizePostsUnitTests.php
 *
 * @package SummarizePosts
 * @author Everett Griffiths
 * @url http://craftsmancoding.com/
 */


require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../../../wp-config.php');
require_once('functions.php');
class SummarizePostsUnitTests extends UnitTestCase {


//	function setUp() { }


	function __construct() {
		parent::__construct('Summarize Posts Unit Tests');
	}
	
	// Make sure we got WP loaded up
	function testWP() {
		$this->assertTrue(defined('CCTM_PATH'));
	}
	
	// SummarizePosts loaded
	function testSP() {
		$this->assertTrue(class_exists('SummarizePosts'));
		$this->assertTrue(class_exists('GetPostsQuery'));
	}

	// Get our object
	function testInit() {
		$Q = new GetPostsQuery();
		$this->assertTrue(is_object($Q));
	}
	
	
	//------------------------------------------------------------------------------
	function test_count_posts1() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'page';
		
		$cnt = $Q->count_posts($args);

		$this->assertTrue($cnt == 7);
	}
	
	function test_count_posts2() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['ID'] = 1;
		$cnt = $Q->count_posts($args);
		$this->assertTrue($cnt == 1);
	}	

	function test_count_posts3() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['include'] = 1;
		$cnt = $Q->count_posts($args);
		$this->assertTrue($cnt == 1);
	}

	function test_count_posts4() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['include'] = '1,5,7';
		$cnt = $Q->count_posts($args);
//		print $Q->debug();
//		exit;
		$this->assertTrue($cnt == 3);
	}


	//------------------------------------------------------------------------------
	// Test the # of tagged posts
	function test_get_posts1() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['taxonomy'] = 'post_tag';
		$args['taxonomy_slug'] = 'tag1';
		$cnt = $Q->count_posts($args);
		$this->assertTrue($cnt == 3);
	}

	// Make sure the taxonomy argument is ignored unless the taxonomy_slug OR taxonomy_term accompanies it.
	function test_get_posts2() {
		$Q = new GetPostsQuery();
		$args = array();
		$cnt1 = $Q->count_posts($args);

		$Q = new GetPostsQuery();
		$args = array();
		$args['taxonomy'] = 'post_tag';
		$cnt2 = $Q->count_posts($args);


		$this->assertTrue($cnt1 == $cnt2);
	}

	// Test for a non-existant tag
	function test_get_posts3() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['taxonomy'] = 'does_not_exist';
		$args['taxonomy_slug'] = 'does_not_exist';
		$cnt = $Q->count_posts($args);
		$warnings = strip_tags($Q->get_warnings());
		$this->assertTrue(strpos($warnings, 'Taxonomy does not exist: does_not_exist'));
	}
    
    // Test for Join
	function test_join() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['include'] = 1;
		$args['join'] = 'taxonomy';

		$results = $Q->get_posts($args);		
		$this->assertTrue(isset($results[0]['taxonomy_names']));
		$this->assertTrue(isset($results[0]['taxonomy_slugs']));
		$this->assertTrue(!isset($results[0]['author']));
		
		$Q = new GetPostsQuery();
		$args = array();
		$args['include'] = 1;
		$results = $Q->get_posts($args);		
		$this->assertTrue(isset($results[0]['author']));
		$this->assertTrue(!isset($results[0]['taxonomy_names']));
	}

    // Test summarize-posts shortcode
	function test_summarize_posts1() {
		$str = '[summarize-posts post_type="movie" tpl="x.tpl"]';
		$actual = do_shortcode($str);
		$expected = '<ul class="summarize-posts"><li>FROM FILE: Letters from Iwo Jima</li><li>FROM FILE: Harry Potter</li><li>FROM FILE: Bourne Identity</li><li>FROM FILE: Fellowship of the Ring</li><li>FROM FILE: Lord of the Rings</li></ul>';
		$this->assertTrue(in_html($actual,$expected));
	}
	function test_summarize_posts2() {
		$str = '[summarize-posts post_type="movie" join="taxonomy" tpl="x.tpl"]';
		$actual = do_shortcode($str);
		$expected = '<ul class="summarize-posts"><li>FROM FILE: Letters from Iwo Jima</li><li>FROM FILE: Harry Potter</li><li>FROM FILE: Bourne Identity</li><li>FROM FILE: Fellowship of the Ring</li><li>FROM FILE: Lord of the Rings</li></ul>';
		$this->assertTrue(in_html($actual,$expected));
	}
	function test_summarize_posts3() {
		$str = '[summarize-posts post_type="movie" before="AAAA..." after="...ZZZZ" tpl="x.tpl" help="1"]';
		$actual = do_shortcode($str);
		$expected = 'AAAA...<li>FROM FILE: Letters from Iwo Jima</li><li>FROM FILE: Harry Potter</li><li>FROM FILE: Bourne Identity</li><li>FROM FILE: Fellowship of the Ring</li><li>FROM FILE: Lord of the Rings</li>...ZZZZ';
		$this->assertTrue(in_html($actual,$expected));
	}

    // Date filters
	function test_date() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'page';
		$args['date_column'] = 'post_date';
		$args['date_min'] = '2012-06-10 20:03:43';
		$args['date_max'] = '2012-06-10 20:04:59';
		$results = $Q->get_posts($args);
		$this->assertTrue($results[0]['ID'] == 23);
		$this->assertTrue($results[1]['ID'] == 21);
		$this->assertTrue($results[2]['ID'] == 19);		
	}    

    // Date Range custom field
	function test_date_cf() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['date_column'] = 'release_date';
		$args['date_min'] = '2011-01-01';
		$args['date_max'] = '2011-12-31';
		$results = $Q->get_posts($args);
		$this->assertTrue($results[0]['ID'] == 77);		
	}    


    // Search Terms
	function test_search() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['search_columns'] =  array('post_title', 'post_content');
		$args['search_term'] = 'wordpress';
		$results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 2);
	}    

	function test_search2() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['search_columns'] =  array('post_title');
		$args['search_term'] = 'Page';
		$args['match_rule'] = 'starts_with';
		$results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 6);
	}    

	// Order By
	function test_order_posts1() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'page';
		$args['orderby'] = 'post_title';
		$args['order'] = 'ASC';
		$results = $Q->get_posts($args);
		$this->assertTrue($results[0]['ID'] == 2);
		$this->assertTrue($results[1]['ID'] == 17);
		$this->assertTrue($results[2]['ID'] == 19);
	}
	
	// Sort on Custom column
	function test_order_posts2() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['orderby'] = 'rating';
		$args['order'] = 'ASC';
		$results = $Q->get_posts($args);
		$this->assertTrue($results[0]['ID'] == 33);
		$this->assertTrue($results[1]['ID'] == 77);
		$this->assertTrue($results[2]['ID'] == 32);
	}
		
	// Operators on custom columns
	function test_get_posts_operators1() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['release_date']['>'] = '2013-02-01';
		$results = $Q->get_posts($args);		
		$this->assertTrue(count($results) == 1);
		$this->assertTrue($results[0]['ID'] == 32);
	}

	function test_get_posts_operators2() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['release_date']['='] = '2005-06-01';
		$results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 1);
		$this->assertTrue($results[0]['ID'] == 78);
	}


	function test_get_posts_operators3() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['rating']['LIKE'] = 'G';
		$results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 3);
	}

	function test_get_posts_operators4() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['release_date']['starts_with'] = '2005-';
		$results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 1);
		$this->assertTrue($results[0]['ID'] == 78);
	}
	
	function test_get_posts_operators5() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['rating']['NOT_LIKE'] = 'G'; // get the R films
		$results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 2);
	}	

	function test_get_posts_operators6() {
		$Q = new GetPostsQuery();
		$args = array();
		$args['post_type'] = 'movie';
		$args['post_title']['starts_with'] = 'L';
		$results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 2);
	}

    // https://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=508	
	function test_get_posts_operators7() {
        $Q = new GetPostsQuery();
        $args = array();
        $args['post_type'] = 'movie';
        $args['rating']['like'] = array('PG','R');
        $results = $Q->get_posts($args);
		$this->assertTrue(count($results) == 4);
    }

}
 
/*EOF*/