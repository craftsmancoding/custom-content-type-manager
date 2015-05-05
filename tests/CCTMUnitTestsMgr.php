<?php
/**
 * The tests in this file are meant to test pages from the WP manager: when run, this 
 * class logs into the WordPress manager.
 *
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
 *
 * http://codex.wordpress.org/Automated_Testing
 * 
 * @package CCTM
 * @author Everett Griffiths
 * @url http://craftsmancoding.com/
 */

require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../../../wp-config.php');
require_once('functions.php');
class CCTMUnitTestsMgr extends UnitTestCase {

	public $ckfile; // used by curl as a cookie jar by all curl requests
		
	// http://www.html-form-guide.com/php-form/php-form-submit.html
	// http://coderscult.com/php/php-curl/2008/05/20/php-curl-cookies-example/
	// Run before each test
	//function setUp() { }
	
	function __construct() {
		parent::__construct();
		
		$username = 'cctm';
		$password = 'cctm';
		$login_url = 'http://cctm:8888/wp-login.php';
		
		
		$this->ckfile = tempnam ('/tmp', 'CURLCOOKIE');
		
		$post_data = array();
		$post_data['log'] = $username;
		$post_data['pwd'] = $password;
	
		foreach ( $post_data as $key => $value) {
			$post_items[] = $key . '=' . $value;
		}
		$post_string = implode ('&', $post_items);

		
		$ch = curl_init($login_url);
		curl_setopt ($ch, CURLOPT_COOKIEJAR, $this->ckfile); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);

		$result = curl_exec($ch);
		curl_close($ch);
	}
	
	// Run after each test
	//function tearDown() { }

	// Make sure we got WP loaded up

	function testWP() {
		$this->assertTrue(defined('CCTM_PATH'));
	}
	
	// CCTM loaded
	function testCCTM() {
		$this->assertTrue(class_exists('CCTM'));
	}
/*

	// Make sure the CCTM menu is loaded up (i.e. is the plugin active?)
	function testMenu() {
		$ch = curl_init ('http://cctm:8888/wp-admin/index.php');
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->ckfile); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		$haystack = curl_exec ($ch);
		
		$needle = "<div class='wp-submenu-head'>Custom Content Types</div>";		

		$this->assertTrue(in_html($needle, $haystack));
	}
*/

	// Check custom columns
	// Check custom columns when the post-title is not included!
	
	// Check custom menu

	// Check URL settings

	// Validation: bad value
	// Validation: required field
	
	// Test sorting on custom columns
	
	// Test: sorting on custom column, but custom columns are not visible
	
	//------------------------------------------------------------------------------
	//Check all pages in the admin for PHP warnings, errors, or notices
	//------------------------------------------------------------------------------
	
	// Hierarchical Enabled: does the page have the appropriate selection?
	function testHierarchy() {
		$ch = curl_init ('http://cctm:8888/wp-admin/post-new.php?post_type=people');
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->ckfile); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		
		$haystack = curl_exec ($ch);
		
		$needle = "<select name='parent_id' id='parent_id'>  <option value=\"\">(no parent)</option>";		

		$this->assertTrue(in_html($needle, $haystack));
		
		curl_close($ch);
	}
	
	// Custom Hierarchy : make sure we can select a parent that's from a foreign post-type
	function testCustomHierarchy() {
		$ch = curl_init ('http://cctm:8888/wp-admin/post-new.php?post_type=room');
		curl_setopt ($ch, CURLOPT_COOKIEFILE, $this->ckfile); 
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
		
		$haystack = curl_exec ($ch);
		
		$needle = '<option class="level-0" value="28">123 Main Street (house)</option>';		

		$this->assertTrue(in_html($needle, $haystack));
		curl_close($ch);
	}


	//------------------------------------------------------------------------------
	// Test Global Settings
	//------------------------------------------------------------------------------
	// Show Custom Fields Menu
	// Show Settings Menu
	// Display Foreign Post Types
	
	// Show Summarize Posts TinyMCE Button
	// Show Custom Fields TinyMCE Button
	
	

}
 
/*EOF*/