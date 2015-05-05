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
 * To run these tests, simply navigate to this file in your browser, e.g. 
 * http://cctm:8888/wp-content/plugins/custom-content-type-manager/tests/GetPostsFormUnitTests.php
 *
 * Or execute them via php on the command line:
 *	php /full/path/to/GetPostsFormUnitTests.php
 *
 * @package SummarizePosts
 * @author Everett Griffiths
 * @url http://craftsmancoding.com/
 */

require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../../../wp-config.php');
require_once(CCTM_PATH .'/includes/GetPostsForm.php');
require_once('functions.php');


class GetPostsFormUnitTests extends UnitTestCase {


//	function setUp() { }


	function __construct() {
		//parent::__construct('GetPostsForm Unit Tests');
	}
	

/*
	// SummarizePosts loaded
	function testSP() {
		$this->assertTrue(class_exists('SummarizePosts'));
		$this->assertTrue(class_exists('GetPostsQuery'));
		$this->assertTrue(class_exists('GetPostsForm'));
	}

	// Get our object
	function testInit() {
		$Q = new GetPostsForm();
		$this->assertTrue(is_object($Q));
	}
	
*/
	// Test basic form generation
	function testGenerate() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce

		//$actual = $Q->debug();
		//$actual = $Q->generate(true);	
		$actual = $Q->generate();

		
		
		//print $actual; exit;
		$expected = file_get_contents('generated_forms/default.html');
		//print $expected; exit;
		$this->assertTrue(in_html($expected, $actual));
	}	

	// Testing that the nonce works
	function testGenerate2() {
		$Q = new GetPostsForm();
		$actual = $Q->generate();
		$expected = file_get_contents('generated_forms/default.html');
		$this->assertFalse(in_html($expected, $actual));
	}
	// No CSS
	function testGenerate3() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$actual = $Q->generate();
		$expected = file_get_contents('generated_forms/no_css.html');
		$this->assertTrue(in_html($expected, $actual));
	}

	// Test bogus formatting string
	function testGenerate4() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$Q->set_tpl(array());
		$Q->generate();
		$errors = $Q->debug();
		$expected = '<ul><li>Invalid input to set_tpl() function. Input must be a string.</li></ul>';
		$this->assertTrue(in_html($expected, $errors));
	}
	
	// Test setting the id_prefix
	function testGenerate5() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$Q->set_id_prefix('zzzyyy_');
		$actual = $Q->generate();
		$expected = file_get_contents('generated_forms/custom_id_prefix.html');
		$this->assertTrue(in_html($expected, $actual));
	}	

	// Test setting the name_prefix
	function testGenerate6() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$Q->set_name_prefix('yeti_');
		$actual = $Q->generate();
		$expected = file_get_contents('generated_forms/custom_name_prefix.html');
		$this->assertTrue(in_html($expected, $actual));
	}

	// Test output given formatting template	
	function testGenerate7() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$Q->set_tpl('This is a horrible formatting template.');
		$actual = $Q->generate();
		$expected = 'This is a horrible formatting template.';
		$this->assertTrue(in_html($expected, $actual));
	}

	// Testing the [+help+] placeholder
	function testGenerate8() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$Q->set_tpl('[+help+]');
		$actual = $Q->generate();
		$expected = '&#91;+name_prefix+&#93;, &#91;+id_prefix+&#93;, &#91;+wrapper_class+&#93;, &#91;+input_class+&#93;, &#91;+label_class+&#93;, &#91;+description_class+&#93;, &#91;+form_name+&#93;, &#91;+form_number+&#93;, &#91;+action+&#93;, &#91;+method+&#93;, &#91;+search+&#93;, &#91;+filter+&#93;, &#91;+show_all+&#93;, &#91;+show_all_dates+&#93;, &#91;+show_all_post_types+&#93;, &#91;+css+&#93;, &#91;+content+&#93;, &#91;+search_term.name_prefix+&#93;, &#91;+search_term.id_prefix+&#93;, &#91;+search_term.wrapper_class+&#93;, &#91;+search_term.input_class+&#93;, &#91;+search_term.label_class+&#93;, &#91;+search_term.description_class+&#93;, &#91;+search_term.form_name+&#93;, &#91;+search_term.form_number+&#93;, &#91;+search_term.action+&#93;, &#91;+search_term.method+&#93;, &#91;+search_term.search+&#93;, &#91;+search_term.filter+&#93;, &#91;+search_term.show_all+&#93;, &#91;+search_term.show_all_dates+&#93;, &#91;+search_term.show_all_post_types+&#93;, &#91;+search_term.css+&#93;, &#91;+search_term.content+&#93;, &#91;+search_term.value+&#93;, &#91;+search_term.name+&#93;, &#91;+search_term.id+&#93;, &#91;+search_term.label+&#93;, &#91;+search_term.description+&#93;, &#91;+search_term+&#93;';
		print $actual; exit;
		$this->assertTrue(in_html($expected, $actual));
	}

	// Test that the form_number value iterates when using multiple forms on one page.  Note that the 
	// number of functions in this file will change that number!  The search_term.form_number will iterate with each
	// call of the GetPostsForm->generate() function!
	function testFormNumber() {
		$Q = new GetPostsForm();
		$Q->generate();
		$first = $Q->placeholders['form_number'];
		$Q->generate();
		$second = $Q->placeholders['form_number'];
		$this->assertTrue( ($first + 1) == $second);
	}
	
	// 
	function testSearchBy() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS

		$actual = $Q->generate(array('taxonomy'));
		//print '<pre>'; print_r($Q->placeholders); print '</pre>';exit;
		$expected = file_get_contents('generated_forms/search_by_taxonomy.html');
		$this->assertTrue(in_html($expected, $actual));

	}

	// Taxonomy placeholders
	function testSearchBy2() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$Q->set_tpl('[+taxonomy.options+]');
		$actual = $Q->generate(array('taxonomy'));
		//print $Q->placeholders['post_type.options']; exit;
//		print $actual; exit;
		//print '<pre>'; print_r($Q->placeholders); print '</pre>';exit;
		$expected = '<option value="" >Select taxonomy</option>
			<option value="category" >category</option>
			<option value="post_tag" >post_tag</option>
			<option value="nav_menu" >nav_menu</option>
			<option value="link_category" >link_category</option>
			<option value="post_format" >post_format</option>';
		$this->assertTrue(in_html($expected, $actual));
	}

	// Post type options... maybe we should deregister custom post types?
	// See http://pastebin.com/VexHkgig
	function testSearchBy3() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS
		$Q->set_tpl('[+post_type.options+]');
		$actual = $Q->generate(array('post_type'));
		//print $Q->placeholders['post_type.options']; exit;
		//print $actual; exit;
		//print '<pre>'; print_r($Q->placeholders); print '</pre>';exit;
		$expected = '<option value="" >Select post-type</option>
			<option value="attachment" >attachment</option>
			<option value="house" >house</option>
			<option value="movie" >movie</option>
			<option value="page" >page</option>
			<option value="people" >people</option>
			<option value="post" >post</option>
			<option value="room" >room</option>
			<option value="snake" >snake</option>
			<option value="test" >test</option>';
		$this->assertTrue(in_html($expected, $actual));
	}


/*
	// Taxonomy placeholders
	function testSearchBy4() {
		$Q = new GetPostsForm();
		$Q->set_nonce_field(''); // override nonce
		$Q->set_css('',false); // blank out CSS

//		$actual = $Q->generate(true); // search by everything
		$actual = $Q->generate(array('append','author','date_column','date_format','date_max','date_min'
		,'exclude','include','limit','match_rule','meta_key','meta_value','offset','omit_post_type','order'
		,'orderby','paginate','post_date','post_mime_type','post_modified')); // search by everything
		//print $Q->placeholders['post_type.options']; exit;
		print $actual; exit;
		//print '<pre>'; print_r($Q->placeholders); print '</pre>';exit;
		$expected = '';
		$this->assertTrue(in_html($expected, $actual));
	}
*/


}
 
/*EOF*/