<script type="text/javascript">
	/* Used to control the behavior of the submitted form. */
	function set_export_type(value){
		jQuery('#export_type').val(value);
	}
</script>

<p><?php _e('Before exporting, please describe your current configuration. Your settings will be preserved.', CCTM_TXTDOMAIN); ?></p>

<form id="cctm_export_form"  method="post">

	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
	<label for="title" class="cctm_label" id="title_label"><?php _e('Title', CCTM_TXTDOMAIN); ?></label>
	<input type="text" name="title" class="" id="title" value="<?php print $data['title']; ?>" /><br/><br/>
	
	<label for="author" class="cctm_label" id="author_label"><?php _e('Author', CCTM_TXTDOMAIN); ?></label>
	<input type="text" name="author" class=""id="author" value="<?php print $data['author']; ?>" /><br/><br/>

	<label for="url" class="cctm_label" id="url_label"><?php _e('Author URL', CCTM_TXTDOMAIN); ?></label>
	<input type="text" name="url" class="" id="url" size="60" value="<?php print $data['url']; ?>" /><br/><br/>

	<label for="template_url" class="cctm_label" id="template_url_label"><?php _e('Template URL', CCTM_TXTDOMAIN); ?></label>
	<input type="text" name="template_url" class="" id="template_url" size="60" value="<?php print $data['template_url']; ?>" /><br/>
	<span class="cctm_description"><?php _e('If you have created a public theme that goes along with this content definition, paste the URL here.', CCTM_TXTDOMAIN); ?></span><br/>

	<label for="description" class="cctm_label" id="description_label"><?php _e('Description', CCTM_TXTDOMAIN); ?></label>
	<textarea name="description" class="" id="description" rows="5" cols="80"><?php print $data['description']; ?></textarea>
	<br/>
	
	<!-- Use this field to determine where the exported file should go -->
	<input type="hidden" id="export_type" name="export_type" value="download" />
	
	<input type="submit" name="submit" class="button" onclick="javascript:set_export_type('download');" value="<?php _e('Download'); ?>"/>
	<input type="submit" name="submit" class="button" onclick="javascript:set_export_type('to_library');" value="<?php _e('Save to Library', CCTM_TXTDOMAIN); ?>"/>
	
</form>