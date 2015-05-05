<?php
/*------------------------------------------------------------------------------
Ugh. TODO: make an image uploader that does not suck. MVC

Abstraction of the media-upload.php file.
We have to abstract it so we can conditionally filter the contents WP's 
media-upload thickbox.

So this page gets iframed.
------------------------------------------------------------------------------*/



// media-upload.php?type=image&amp;TB_iframe=true

function my_media_upload_tabs($tabs) {
	$tabs = array(
		'type' => __('From Computer'), // handler action suffix => tab text
	);
	return $tabs;
}
add_filter('media_upload_tabs', 'my_media_upload_tabs');


$contents = file_get_contents('/sub/wp-admin/media-upload.php?type=image&amp;TB_iframe=true');
print $contents;
?>
<p>Upload thee an image!</p>