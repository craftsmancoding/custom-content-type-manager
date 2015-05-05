<?php
/*------------------------------------------------------------------------------
This template is used as the basis for TinyMCE buttons (currently only the 
custom_fields plugin)
------------------------------------------------------------------------------*/
?>
<div id="cctm_thickbox">

	<h1><?php print $data['page_title']; ?></h1>
	
	<?php print $data['msg']; ?>

	<div class="cctm_description"><?php _e('You can place a shortcode in your content to print out custom field values.', CCTM_TXTDOMAIN); ?><a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/custom_field_shortcode"><img src="<?php print CCTM_URL; ?>/images/question-mark.gif" height="16" width="16" /></a></div>

	<div id="cctm_thickbox_content">
		<?php print $data['content']; ?>
	</div>

</div>