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
 * http://cctm:8888/wp-content/plugins/custom-content-type-manager/tests/SP_PostUnitTests.php
 *
 * Or execute them via php on the command line:
 *	php /full/path/to/SP_PostUnitTests.php
 *
 * @package SummarizePosts
 * @author Everett Griffiths
 * @url http://craftsmancoding.com/
 */


require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../../../wp-config.php');
require_once('functions.php');
require_once(CCTM_PATH.'/includes/SP_Post.php');
class SP_PostUnitTests extends UnitTestCase {

    public $post_id; // used to store last-insert-id

//	function setUp() { }


	function __construct() {
		parent::__construct('SP_Post Unit Tests');
	}
	
	// Make sure we got WP loaded up
	function testWP() {
		$this->assertTrue(defined('CCTM_PATH'));
	}
	
	// SP loaded
	function testSP() {
		$this->assertTrue(class_exists('SP_Post'));
		$this->assertTrue(class_exists('GetPostsQuery'));
	}

	// Get our object
	function testInit() {
		$P = new SP_Post();
		$this->assertTrue(is_object($P));
		$Q = new GetPostsQuery();
		$this->assertTrue(is_object($Q));
	}
	
	
	//------------------------------------------------------------------------------
	//! Get
	//------------------------------------------------------------------------------
	function test_get1() {
		$P = new SP_Post();
		$page = $P->get(2);
		$this->assertTrue($page !== false);
		$this->assertTrue($page['ID'] == 2);
	}

	function test_get2() {
		$P = new SP_Post();
		$page = $P->get(4);
		$this->assertTrue($page !== false);
		$this->assertTrue($page['ID'] == 4);
	}

	function test_get3() {
		$P = new SP_Post();
		$page = $P->get(array('ID'=>4));
		// This should be false because it's a revision in inherit status.
		$this->assertTrue($page === false);

		$page = $P->get(array('ID'=>4,'post_status'=>'inherit','post_type'=>'revision'));
		// But now we should get it.
		$this->assertTrue($page !== false);
	}

    //------------------------------------------------------------------------------
    //! Insert
    //------------------------------------------------------------------------------
	function test_insert1() {
		$P = new SP_Post();
		$post = array();
		$post['9 #!bad/ Column Name'] = 'Testing';
        $result = $P->insert($post);
		$this->assertTrue($result === false);
	}

    // Invalid post id should cause update to fail
	function test_update() {
		$P = new SP_Post();
		$post = array();
		$post['post_title'] = 'Testing';
		$result = $P->update($post, 0);
		$this->assertTrue($result === false);
	}    


    // A little round trip here: creating and deleting a post.
	function test_insert2() {
		$P = new SP_Post();
		$post = array();
		$post['post_title'] = 'Testing';
        $this->post_id = $P->insert($post);
		$this->assertTrue($this->post_id);
		
		$post['post_title'] = 'Testing More...';
		$result = $P->update($post, $this->post_id);
		$this->assertTrue($result == $this->post_id);
		
		$post = $P->get($this->post_id);
		$this->assertTrue($post['ID'] == $this->post_id);

        // Cleanup: Make sure it's gone		
		$P->delete($this->post_id);
		$post = $P->get($this->post_id);
		$this->assertTrue(empty($post));
	}

    // Another round-trip
	function test_insert3() {
        
        global $wpdb;
        
		$P = new SP_Post();
		$post = array();
		$post['post_title'] = 'Test Meta';
		$post['a'] = 'Apple';
		$post['b'] = 'Banana';
		$post['c'] = 'Carrot';
        $this->post_id = $P->insert($post);
		$this->assertTrue($this->post_id);

        // Make sure each custom field corresponds to one row in wp_postmeta
        $sql = $wpdb->prepare("SELECT meta_key, count(*) as cnt FROM {$wpdb->postmeta} WHERE post_id=%s GROUP BY meta_key",$this->post_id); 
        $results = $wpdb->get_results($sql, ARRAY_A);
        $this->assertTrue($results);
        foreach($results as $r) {
            $this->assertTrue($r['cnt'] == 1);
        }
        
        // Nothing changed: make sure additional rows weren't created.
		$result = $P->update($post, $this->post_id);
		$this->assertTrue($result == $this->post_id);

        // Make sure each custom field corresponds to one row in wp_postmeta
        $sql = $wpdb->prepare("SELECT meta_key, count(*) as cnt FROM {$wpdb->postmeta} WHERE post_id=%s GROUP BY meta_key",$this->post_id); 
        $results = $wpdb->get_results($sql, ARRAY_A);
        $this->assertTrue($results);
        foreach($results as $r) {
            $this->assertTrue($r['cnt'] == 1);
        }

		
		// Try nuking a custom field
		unset($post['c']);

        // Nothing changed: make sure additional rows weren't created.
		$result = $P->update($post, $this->post_id,true);
		$this->assertTrue($result == $this->post_id);

        $post = $P->get($this->post_id);
        $this->assertTrue(!isset($post['c']));
        
        // Make sure each custom field corresponds to one row in wp_postmeta
        $sql = $wpdb->prepare("SELECT meta_key, count(*) as cnt FROM {$wpdb->postmeta} WHERE post_id=%s GROUP BY meta_key",$this->post_id); 
        $results = $wpdb->get_results($sql, ARRAY_A);
        $this->assertTrue($results);
        foreach($results as $r) {
            $this->assertTrue($r['cnt'] == 1);
        }

        // Cleanup: Make sure it's gone		
		$P->delete($this->post_id);
		$post = $P->get($this->post_id);
		$this->assertTrue(empty($post));

	}
    

}
 
/*EOF*/