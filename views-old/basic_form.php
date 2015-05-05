<?php
/*------------------------------------------------------------------------------
Basic form template. Made for use by the CCTM controllers.
This template expects the following variables inside the $data array:

	style; 		// can be used to print a <style> block above the form.
	page_header; 	// appears at the top of the page  
	content;		// your form fields and any other data
	action_name; 	// used by wp_nonce_field
	nonce_name; 	// used by wp_nonce_field
	submit;		// text that appears on the primary submit button
	
	cancel_target_url // (optional) Default is '?page='.self::admin_menu_slug;
------------------------------------------------------------------------------*/
if ( !isset($data['cancel_target_url']) ) {
	$data['cancel_target_url'] = '?page=cctm';
}

?>
<?php if (isset($data['style'])) { print $data['style']; } ?>
	

<form id="custom_post_type_manager_basic_form" method="post">

	<?php print $data['content']; ?>

	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
<br/>
	<div class="custom_content_type_mgr_form_controls">
		<input type="submit" name="Submit" class="button-primary" value="<?php print $data['submit']; ?>" />
		<a class="button" href="<?php print $data['cancel_target_url']; ?>"><?php _e('Cancel'); ?></a> 
	</div>

</form>
