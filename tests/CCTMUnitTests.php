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
 *
 * http://codex.wordpress.org/Automated_Testing
 * 
 * @package CCTM
 * @author Everett Griffiths
 * @url http://craftsmancoding.com/
 */

require_once(dirname(__FILE__) . '/simpletest/autorun.php');
require_once(dirname(__FILE__) . '/../../../../wp-config.php');
//require_once(CCTM_PATH .'/includes/CCTM_Validator.php');
//require_once(CCTM_PATH .'/validators/CCTM_FormElement.php');

//require_once(CCTM_PATH .'/includes/CCTM_FormElement.php');
require_once(CCTM_PATH .'/includes/SP_Post.php');
require_once(CCTM_PATH .'/includes/CCTM_Pagination.php');
require_once('functions.php');

class CCTMUnitTests extends UnitTestCase {
	
	
	/**
	 * Test whether a regular category page displays posts and 
	 * any pages from custom post-types that have been categorized
	 */

/*
    function testCategories() {
    	$page = file_get_contents('http://cctm:8888/category/uncategorized/');
    	
    	print $page;
    }
*/
	// Archives
	// Categories
	// tags
	
/*
    function testTags() {
    	$page = file_get_contents('http://cctm:8888/category/uncategorized/');
    	
    	print $page;
    }
*/

	/**
	 * Make sure we didn't accidentally bundle software that's under the 
	 * Creative Commons License.
	 */
/*
	function testNoCCL() {
	
	}
*/


	/**
	 * Change post_type name
	 */

	/**
	 * Test RSS feed
	 */

	function testRSS() {
		$xml = file_get_contents('http://cctm:8888/feed/');
		
		$this->assertTrue($xml);
	}
	
	//------------------------------------------------------------------------------
	//!Test Global Settings
	//------------------------------------------------------------------------------
	// Delete Posts
	// Delete Custom Fields
	// Save Empty Fields
	// Show Pages in RSS Feed
	
	// 
	
	//------------------------------------------------------------------------------
	//!Test Validators
	//------------------------------------------------------------------------------
	function testEmail() {
		$V = CCTM::load_object('emailaddress','validators');
		
		$email = 'notan-emailaddress.';
		
		$V->validate($email);		
		$this->assertFalse(empty($V->error_msg));
	}

	function testEmail2() {
		$V = CCTM::load_object('emailaddress','validators');
		$email = 'someone@yahoo.com';
		$V->validate($email);

		$this->assertTrue(empty($V->error_msg));
	}

	function testEmail3() {
		$V = CCTM::load_object('emailaddress','validators');
		$email = 'payer@player-hater.com';
		$V->validate($email);
		$this->assertTrue(empty($V->error_msg));
	}

	//------------------------------------------------------------------------------
	function testNumber() {
		$V = CCTM::load_object('number','validators');
		$number = 'asdf';
		$V->validate($number);
		$this->assertFalse(empty($V->error_msg));
	}


	function testNumber3() {
		$V = CCTM::load_object('number','validators');
		$number = '123';
		$V->validate($number);
		$this->assertTrue(empty($V->error_msg));
	}

	function testNumber4() {
		$V = CCTM::load_object('number','validators');
		$V->min = 4;
		$V->max = 6;
		$number = '10';
		$V->validate($number);
		$this->assertFalse(empty($V->error_msg));
	}

	function testNumber5() {
		$V = CCTM::load_object('number','validators');
		$V->min = 4;
		$V->max = 6;
		$number = '5';
		$V->validate($number);
		$this->assertTrue(empty($V->error_msg));
	}

	function testNumber6() {
		$V = CCTM::load_object('number','validators');
		$V->allow_negative = 1;
		$V->max = 6;
		$number = '-5';
		$V->validate($number);
		$this->assertTrue(empty($V->error_msg));
	}

	function testNumber7() {
		$V = CCTM::load_object('number','validators');
		$V->allow_negative = 0;
		$V->max = 6;
		$number = '-5';
		$V->validate($number);
		$this->assertFalse(empty($V->error_msg));
	}
	
	//------------------------------------------------------------------------------
	//!Output Filters
	//------------------------------------------------------------------------------
	// Test using the 'default' filter on an array
	function testFilter3() {
		CCTM::$post_id = 38;
		$emails = get_custom_field('email:default', 'None Provided');
		$this->assertTrue($emails[0] == 'test@test.com');
		$this->assertTrue($emails[1] == 'None Provided');
	}
	function testFilter4() {
		CCTM::$post_id = 38;
		$emails = get_custom_field('age:default', 'Unknown');
		$this->assertTrue($emails == 'Unknown');
	}
	// For some reason, when I call "do_shortcode" here, it *prints* the result
	// MADNESS!!!!
/*
	function testFilter5() {
		CCTM::$post_id = 93;
		ob_start();
		get_custom_field('bio:do_shortcode');
		$homepage = get_custom_field('homepage:raw');
		$bio = ob_get_contents();
		
		$this->assertTrue($homepage == $bio);
	}
*/

	// email
	function testFilter7() {
		$actual = CCTM::filter('test@test.com','email');
		$this->assertTrue($actual =='&#116;&#101;&#115;&#116;&#64;&#116;&#101;&#115;&#116;&#46;&#99;&#111;&#109;');
	}
	function testFilter8() {
		$actual = CCTM::filter(array('test@test.com','test2@test.com'),'email');
		$this->assertTrue($actual[0] =='&#116;&#101;&#115;&#116;&#64;&#116;&#101;&#115;&#116;&#46;&#99;&#111;&#109;');
		$this->assertTrue($actual[1] =='&#116;&#101;&#115;&#116;&#50;&#64;&#116;&#101;&#115;&#116;&#46;&#99;&#111;&#109;');
	}
	// excerpt
	function testFilter9() {
		$val = ' trans. put, lay, or stand (something) in a specified place or position : Dana set the mug of tea down | Catherine set a chair by the bed. ||SPLITHERE||
¥ ( be set) be situated or fixed in a specified place or position : the village was set among olive groves on a hill.';
		$actual = CCTM::filter($val,'excerpt', 5);
		$this->assertTrue($actual == 'trans. put, lay, or stand&#0133;');
	}
	function testFilter10() {
		$val = ' trans. put, lay, or stand (something) in a specified place or position : Dana set the mug of tea down | Catherine set a chair by the bed. ||SPLITHERE||
¥ ( be set) be situated or fixed in a specified place or position : the village was set among olive groves on a hill.';
		$actual = CCTM::filter($val,'excerpt', '||SPLITHERE||');
		$this->assertTrue($actual == 'trans. put, lay, or stand (something) in a specified place or position : Dana set the mug of tea down | Catherine set a chair by the bed.&#0133;');
	}
	function testFilter11() {
		$vals = array(
			' trans. put, lay, or stand (something) in a specified place or position : Dana set the mug of tea down | Catherine set a chair by the bed. ||SPLITHERE||
¥ ( be set) be situated or fixed in a specified place or position : the village was set among ',
			'adjective
1 an unspecified amount or number of : I made some money running errands | he played some records for me.'
		);
		$actual = CCTM::filter($vals,'excerpt', 5);
		$this->assertTrue($actual[0] == 'trans. put, lay, or stand&#0133;');
		$this->assertTrue($actual[1] == 'adjective 1 an unspecified amount&#0133;');
	}	
	
	// formatted list 
	// simple comma-separation
	function testFilter12() {
		$actual = CCTM::filter(array('Man','Bear','Pig'),'formatted_list');
		$this->assertTrue($actual == 'Man, Bear, Pig');
	}
	// string separator
	function testFilter13() {
		$actual = CCTM::filter(array('Man','Bear','Pig'),'formatted_list','--derp--');
		$this->assertTrue($actual == 'Man--derp--Bear--derp--Pig');
	}
	// templates
	function testFilter14() {
		$actual = CCTM::filter(array('Man','Bear','Pig'),'formatted_list',array('<li>[+value+]</li>','<ul>[+content+]</ul>'));
		//print $actual; exit;
		$this->assertTrue($actual == '<ul><li>Man</li><li>Bear</li><li>Pig</li></ul>');
	}

	// gallery
	function testFilter15() {
		$actual = CCTM::filter(array(118,119,120),'gallery');
		$this->assertTrue($actual == '<div class="cctm_gallery" id="cctm_gallery_1"><img height="2592" width="1936" src="http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg" title="2012 VACATION 057" alt="" class="cctm_image" id="cctm_image_1"/></div><div class="cctm_gallery" id="cctm_gallery_2"><img height="2592" width="1936" src="http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-058.jpg" title="2012 VACATION 058" alt="" class="cctm_image" id="cctm_image_2"/></div><div class="cctm_gallery" id="cctm_gallery_3"><img height="2592" width="1936" src="http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-059.jpg" title="2012 VACATION 059" alt="" class="cctm_image" id="cctm_image_3"/></div>');
	}
	
	// get_post
	function testFilter20() {
		$post = CCTM::filter(1,'get_post');
		$this->assertTrue($post['post_title'] =='Post1');
		
		$str = CCTM::filter(array(1,2),'get_post', '[+post_title+]');
		$this->assertTrue($str =='Post1Page A');
		
		$post_title = CCTM::filter(1,'get_post','[+post_title+]');
		$this->assertTrue($post_title =='Post1');
		
		$result = CCTM::filter(null,'get_post','[+post_title+]');
		$this->assertTrue($result ==false);
	}
	
	// raw
	function testFilter30() {
		// default filter for this field is to_image_src
		CCTM::$post_id = 77;
		$img = get_custom_field('poster_image');
		$this->assertTrue($img =='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-059.jpg');
		$img = get_custom_field('poster_image:raw');
		$this->assertTrue($img =='120');
	}
	
	// to_array
	function testFilter40() {
		$array = CCTM::filter('["Man","Bear","Pig"]','to_array');
		$this->assertTrue(is_array($array));
		$this->assertTrue($array[0]=='Man');
		$this->assertTrue($array[1]=='Bear');
		$this->assertTrue($array[2]=='Pig');	
	}
	function testFilter41() {
		$array = CCTM::filter('Not','to_array');
		$this->assertTrue(is_array($array));
		$this->assertTrue($array[0]=='Not');
	}
	function testFilter42() {
		$array = CCTM::filter('["118","119","120"]','to_array', 'to_image_src');
		
		$this->assertTrue($array[0]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg');
		$this->assertTrue($array[1]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-058.jpg');
		$this->assertTrue($array[2]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-059.jpg');
	}

	// to_image_array
	function testFilter50() {
		$array = CCTM::filter('118','to_image_array');
		$this->assertTrue($array[0]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg');
		$this->assertTrue($array[1]=='1936');
		$this->assertTrue($array[2]=='2592');
	}
	function testFilter51() {
		$array = CCTM::filter(array('118','119'),'to_image_array');

		$this->assertTrue($array[0][0]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg');
		$this->assertTrue($array[0][1]=='1936');
		$this->assertTrue($array[0][2]=='2592');


		$this->assertTrue($array[1][0]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-058.jpg');
		$this->assertTrue($array[1][1]=='1936');
		$this->assertTrue($array[1][2]=='2592');
	}
	
	// to_image_src
	function testFilter60() {
		$src = CCTM::filter('118','to_image_src');
		$this->assertTrue($src=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg');
	}
	function testFilter61() {
		$array = CCTM::filter(array('118','119'),'to_image_src');
		$this->assertTrue($array[0]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg');
		$this->assertTrue($array[1]=='http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-058.jpg');
	}

	// to_image_tag
	function testFilter70() {
		$tag = CCTM::filter('118','to_image_tag');
//		$this->assertTrue($tag=='<img width="1936" height="2592" src="http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg" class="attachment-full" alt="2012 VACATION 057" title="2012 VACATION 057" />');
		$this->assertTrue(preg_match('#src="http://cctm:8888/wp-content/uploads/2012/06/2012\-VACATION\-057\.jpg"#', $tag));
	}
	function testFilter71() {
		// this filter always ouputs a string
		$tags = CCTM::filter(array('118','119'),'to_image_tag');
		//$this->assertTrue($tags=='<img width="1936" height="2592" src="http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-057.jpg" class="attachment-full" alt="2012 VACATION 057" title="2012 VACATION 057" /><img width="1936" height="2592" src="http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-058.jpg" class="attachment-full" alt="2012 VACATION 058" title="2012 VACATION 058" />');
		$this->assertTrue(is_scalar($tags));
	}
	
	// to_link_href
	function testFilter80() {
		$href = CCTM::filter('80','to_link_href');
		$this->assertTrue($href=='http://cctm:8888/harry-potter/');
	}
	function testFilter81() {
		$hrefs = CCTM::filter(array('80','11'),'to_link_href');
		$this->assertTrue($hrefs[0]=='http://cctm:8888/harry-potter/');
		$this->assertTrue($hrefs[1]=='http://cctm:8888/post5/');
	}

	// to_link
	function testFilter90() {
		$link = CCTM::filter('80','to_link');
		$this->assertTrue($link=='<a href="http://cctm:8888/harry-potter/" title="Harry Potter">Harry Potter</a>');
	}
	function testFilter91() {
		$links = CCTM::filter(array('80','11'),'to_link');
		$this->assertTrue($links=='<a href="http://cctm:8888/harry-potter/" title="Harry Potter">Harry Potter</a>, <a href="http://cctm:8888/post5/" title="Post5">Post5</a>');
	}
	function testFilter92() {
		$link = CCTM::filter('80','to_link','Click Me');
		$this->assertTrue($link=='<a href="http://cctm:8888/harry-potter/" title="Harry Potter">Click Me</a>');
	}


	// userinfo
	function testFilter100() {
		$info = CCTM::filter('1','userinfo');
		$this->assertTrue($info=='<div class="cctm_userinfo" id="cctm_user_1">cctm: dev@wpcctm.com</div>');
	}
	function testFilter101() {
		$info = CCTM::filter(array('1','2'),'userinfo');
		$this->assertTrue($info=='<div class="cctm_userinfo" id="cctm_user_1">cctm: dev@wpcctm.com</div><div class="cctm_userinfo" id="cctm_user_2">nada: nada@nowhere.com</div>');
	}
	function testFilter_userinfo1() {
		$info = CCTM::filter(1,'userinfo','[+user_nicename+]');
		$this->assertTrue($info=='cctm');
	}

	// wrapper
	function testFilter110() {
		$txt = CCTM::filter('','wrapper',array('<strong>','</strong>'));
		$this->assertFalse($txt);
	}
	function testFilter111() {
		$txt = CCTM::filter('Big Stuff','wrapper',array('<strong>','</strong>'));
		$this->assertTrue($txt =='<strong>Big Stuff</strong>');
	}
	function testFilter112() {
		$txt = CCTM::filter('Big Stuff','wrapper','<strong>[+content+]</strong>');
		$this->assertTrue($txt =='<strong>Big Stuff</strong>');
	}
	
	// help
/*
	function testFilter120() {
		$txt = CCTM::filter('ignore','help');
		$this->assertTrue(in_html('Output Filter Help',$txt));
	}
*/
	
	
		
	//------------------------------------------------------------------------------
	//! Helper Classes
	//------------------------------------------------------------------------------
	function testGetValidators() {
		$classes = CCTM::get_available_helper_classes('validators');
		$this->assertTrue(isset($classes['emailaddress']));
		$this->assertTrue(isset($classes['url']));
		$this->assertTrue(isset($classes['number']));
	}	

	function testGetOutputFilters() {
		$classes = CCTM::get_available_helper_classes('filters');
		$this->assertTrue(isset($classes['default']));
		$this->assertTrue(isset($classes['do_shortcode']));
		$this->assertTrue(isset($classes['email']));
		$this->assertTrue(isset($classes['excerpt']));
		$this->assertTrue(isset($classes['formatted_list']));
		$this->assertTrue(isset($classes['gallery']));
		$this->assertTrue(isset($classes['get_post']));
		$this->assertTrue(isset($classes['help']));
		$this->assertTrue(isset($classes['raw']));
		$this->assertTrue(isset($classes['to_array']));
		$this->assertTrue(isset($classes['to_image_array']));
		$this->assertTrue(isset($classes['to_image_src']));
		$this->assertTrue(isset($classes['to_image_tag']));
		$this->assertTrue(isset($classes['to_link']));
		$this->assertTrue(isset($classes['to_link_href']));
		$this->assertTrue(isset($classes['userinfo']));
		$this->assertTrue(isset($classes['wrapper']));
	}

	function testCustomFields() {
		$classes = CCTM::get_available_helper_classes('fields');
		$this->assertTrue(isset($classes['checkbox']));
		$this->assertTrue(isset($classes['colorselector']));
		$this->assertTrue(isset($classes['date']));
		$this->assertTrue(isset($classes['dropdown']));
		$this->assertTrue(isset($classes['image']));
		$this->assertTrue(isset($classes['media']));
		$this->assertTrue(isset($classes['multiselect']));
		$this->assertTrue(isset($classes['relation']));
		$this->assertTrue(isset($classes['text']));
		$this->assertTrue(isset($classes['textarea']));
		$this->assertTrue(isset($classes['user']));
		$this->assertTrue(isset($classes['wysiwyg']));

		// 3rd Party stuff
		$this->assertTrue(isset($classes['exercises']));

	}
	// Test bogus PHP file in one of the dirs... the turd in the punchbowl
	function testBogusValidator() {
		$V = CCTM::load_object('bogus','validators');
		$this->assertFalse($V);
	}

	//------------------------------------------------------------------------------
	//!Parser
	//------------------------------------------------------------------------------
	function testParser1() {
		$tpl = 'Hello my name is [+name+]';
		$hash = array('name' => 'John');
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == 'Hello my name is John');
	}	
	function testParser2() {
		$tpl = 'Hello my name is [+name+][+unused+]';
		$hash = array('name' => 'John');
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == 'Hello my name is John');
	}
	function testParser3() {
		$tpl = 'Hello my name is [+name+][+unused+]';
		$hash = array('name' => 'John');
		$output = CCTM::parse($tpl,$hash,true);
		$this->assertTrue($output == 'Hello my name is John[+unused+]');
	}
	function testParser4() {
		$tpl = '[+post_id:to_link_href+]';
		$hash = array('post_id' => 1);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == get_permalink(1));
	}
	function testParser5() {
		$tpl = '[+post_id:to_link==Click me here+]';
		$hash = array('post_id' => 1);
		$actual = CCTM::parse($tpl,$hash);
		$post_id = 1;
		$P = get_post($post_id);
		$expected = '<a href="'.get_permalink(1).'" title="'.$P->post_title.'">Click me here</a>';
		$this->assertTrue($expected == $actual);
	}
	function testParser6() {
		$tpl = 'This is my formatting string';
		$hash = 'This should be an array';
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == $tpl);
	}
	// You can't nest tags :( so we use different glyphs
	function testParser7() {
		$tpl = '[+post_id:get_post=={{post_title}}+]';
		$hash = array('post_id'=>80);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == 'Harry Potter');
	}

	// This usage requires that a string is returned, so
	// the output filter here should be ignored.
	function testParser8() {
		$tpl = '[+post_id:get_post+]';
		$hash = array('post_id'=>80);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == '80');
	}

	function testParser9() {
		$tpl = '[+post_id:get_post=={{post_title}}:email+]';
		$hash = array('post_id'=>80);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == '&#72;&#97;&#114;&#114;&#121;&#32;&#80;&#111;&#116;&#116;&#101;&#114;');
	}	
	function testParser10() {
		$tpl = '[+post_id:get_post==post_title+]';
		$hash = array('post_id'=>80);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == 'Harry Potter');
	}
	
	function testParser11() {
		$tpl = '[+post_id:get_post==post_title:wrapper==<strong>||</strong>+]';
		$hash = array('post_id'=>80);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == '<strong>Harry Potter</strong>');
	}
	function testParser12() {
		$tpl = '[+post_id:get_post==post_title:wrapper==<strong>{{content}}</strong>+]';
		$hash = array('post_id'=>80);
		$output = CCTM::parse($tpl,$hash);
		$this->assertTrue($output == '<strong>Harry Potter</strong>');
	}
	
	
	// Custom Field with custom settings -- does the link appear?
	
	//------------------------------------------------------------------------------
	//! Template Functions
	//------------------------------------------------------------------------------
	function test_get_custom_field() {
		CCTM::$post_id = 75;
		$this->assertTrue(get_custom_field('rating') == 'R');
		$this->assertTrue(get_custom_field('rating:raw') == 'R');
		CCTM::$post_id = 77;
		$this->assertTrue(get_custom_field('gallery:gallery') == '<div class="cctm_gallery" id="cctm_gallery_1"><img height="2592" width="1936" src="http://cctm:8888/wp-content/uploads/2012/06/2012-VACATION-059.jpg" title="2012 VACATION 059" alt="" class="cctm_image" id="cctm_image_1"/></div><div class="cctm_gallery" id="cctm_gallery_2"><img height="313" width="637" src="http://cctm:8888/wp-content/uploads/2012/08/I-just-had-sex.jpg" title="I just had sex" alt="" class="cctm_image" id="cctm_image_2"/></div>');
		
	}
	
	function test_get_custom_field_meta() {
		$meta = get_custom_field_meta('poster_image');
		$this->assertTrue($meta['description'] == 'Main image for this film.');
		$meta = get_custom_field_meta('does_not_exist');
		$this->assertTrue($meta == 'Invalid field name: does_not_exist');
	}
	
	function test_get_custom_image() {
		CCTM::$post_id = 77;
		$this->assertTrue( get_custom_image('poster_image') == wp_get_attachment_image(120, 'full'));
	}
	
	function test_get_incoming_links() {
		$posts = get_incoming_links(array('movie'), 33);
		$this->assertTrue($posts[0] == 77);
	}
	
	function test_get_post_complete() {
		$post = get_post_complete(123);
		$this->assertTrue($post['post_name'] == 'img_0448');
	}
	
	function test_get_posts_sharing_custom_field_value() {
		$posts = get_posts_sharing_custom_field_value('rating','R');
		$this->assertTrue(is_array($posts));
		$this->assertTrue($posts[0]['ID'] == 75);
		$this->assertTrue($posts[1]['ID'] == 78);
	}
	
	function test_get_relation() {
		CCTM::$post_id = 77;
		$rel = get_relation('poster_image');
		$this->assertTrue($rel['ID'] == 120);
	}
	
	function test_get_unique_values_this_custom_field() {
		$values = get_unique_values_this_custom_field('rating');
		$this->assertTrue(is_array($values));
		$this->assertTrue(in_array('R',$values));
		$this->assertTrue(in_array('PG-13',$values));
	}
	
	function test_print_custom_field() {
	
	}
	
	function test_print_custom_field_meta() {
	
	}
	
	function test_print_incoming_links() {
	
	}
	
	//------------------------------------------------------------------------------
	//! SP_Post
	//------------------------------------------------------------------------------
	function test_sppost1() {
		$SP = new SP_Post();	
		$post = $SP->get(21);
		$this->assertTrue($post['post_title'] == 'Page C-1');
		
		$post['post_title'] = 'Page C-1-test';
		$SP->update($post,21);
		$post = $SP->get(21);
		$this->assertTrue($post['post_title'] == 'Page C-1-test');
		$post['post_title'] = 'Page C-1';
		$SP->update($post,21);
	}
	
	
	//------------------------------------------------------------------------------
	//! Pagination
	//------------------------------------------------------------------------------
	function test_pagination1() {
		$P = new CCTM_Pagination();
		$actual = $P->paginate(100);
		$expected = '<div id="pagination">&nbsp;<span>1</span>&nbsp;&nbsp;<a href="?&offset=25" >2</a>&nbsp;&nbsp;<a href="?&offset=50" >3</a>&nbsp;&nbsp;<a href="?&offset=75" >4</a>&nbsp;&nbsp;<a href="?&offset=25" >Next &rsaquo;</a>&nbsp;<a href="?&offset=75" >Last &raquo;</a><br/>
				Page 1 of 4<br/>
				Displaying records 1 thru 25 of 100
			</div>';
			
		$this->assertTrue(in_html($actual,$expected));
	}
	
	function test_pagination2() {
		$P = new CCTM_Pagination();
		$P->set_base_url('http://mysite.com/page');
		$actual = $P->paginate(100);
		$expected = '<div id="pagination">&nbsp;<span>1</span>&nbsp;&nbsp;<a href="http://mysite.com/page?&offset=25" >2</a>&nbsp;&nbsp;<a href="http://mysite.com/page?&offset=50" >3</a>&nbsp;&nbsp;<a href="http://mysite.com/page?&offset=75" >4</a>&nbsp;&nbsp;<a href="http://mysite.com/page?&offset=25" >Next &rsaquo;</a>&nbsp;<a href="http://mysite.com/page?&offset=75" >Last &raquo;</a><br/>
				Page 1 of 4<br/>
				Displaying records 1 thru 25 of 100
			</div>';
		$this->assertTrue(in_html($actual,$expected));
	}

	function test_pagination3() {
		$P = new CCTM_Pagination();
		$P->set_link_cnt(3);
		$actual = $P->paginate(100);
		$expected = '<div id="pagination">&nbsp;<span>1</span>&nbsp;&nbsp;<a href="?&offset=25" >2</a>&nbsp;&nbsp;<a href="?&offset=50" >3</a>&nbsp;&nbsp;<a href="?&offset=25" >Next &rsaquo;</a>&nbsp;<a href="?&offset=75" >Last &raquo;</a><br/>
				Page 1 of 4<br/>
				Displaying records 1 thru 25 of 100
			</div>';
		$this->assertTrue(in_html($actual,$expected));
	}

	function test_pagination4() {
		$P = new CCTM_Pagination();
		$P->set_link_cnt(3);
		$actual = $P->paginate(100);
		$expected = '<div id="pagination">&nbsp;<span>1</span>&nbsp;&nbsp;<a href="?&offset=25" >2</a>&nbsp;&nbsp;<a href="?&offset=50" >3</a>&nbsp;&nbsp;<a href="?&offset=25" >Next &rsaquo;</a>&nbsp;<a href="?&offset=75" >Last &raquo;</a><br/>
				Page 1 of 4<br/>
				Displaying records 1 thru 25 of 100
			</div>';
		$this->assertTrue(in_html($actual,$expected));
	}

	
	
}
 
/*EOF*/