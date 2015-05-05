<?php
/**
 * Helper functions used by the testing suites.
 */
 
/**
 * We use this to verify that the $needle is in the haystack. We have
 * to "normalize" whitespace here since file_get_contents can pull up
 * weird characters as output.
 *
 * http://stackoverflow.com/questions/1360610/how-to-show-a-comparison-of-2-html-text-blocks ???
 * @param	string	$needle
 * @param	string	$haystack
 * @return	boolean	true if the $needle is in the $haystack
 */
function in_html($needle, $haystack) {
	// normalize whitespace
	$needle = str_replace(array(chr(202),chr(173),chr(0xC2),chr(0xA0)), ' ', $needle);
	$needle = str_replace(array("\r","\r\n","\n","\t"), '', $needle);
	$haystack = str_replace(array(chr(202),chr(173),chr(0xC2),chr(0xA0)), ' ', $haystack);
	$haystack = str_replace(array("\r","\r\n","\n","\t"), '', $haystack);
	$needle = preg_replace( '/\s+/', ' ', $needle);
	$haystack = preg_replace( '/\s+/', ' ', $haystack);
	
	$i = strpos($haystack, $needle);
	
	if ($i === false) {
		return false;
	}
	else {
		return true;
	}
} 
/*EOF*/