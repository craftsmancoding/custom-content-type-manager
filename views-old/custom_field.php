<script type="text/javascript">
	function set_continue_editing() {
		jQuery('#continue_editing').val(1);
		return true;
	}
</script>

<div class="metabox-holder">

<form id="custom_post_type_manager_basic_form" method="post" action="">

	<input type="hidden" name="continue_editing" id="continue_editing" value="0" />
	
	<table class="custom_field_info">
		<tr>
			<td colspan="2">
				<h3 class="field_type_name"><?php print $data['name']; ?></h3>
			</td>
		</tr>
		<tr>
			<td>
				<span class="custom_field_icon"><?php print $data['icon']; ?></span>
			</td>
			<td>
				<span class="custom_field_description"><?php print $data['description']; ?>
				<br />
				<a href="<?php print $data['url']; ?>" target="_blank"><?php _e('More Information', CCTM_TXTDOMAIN); ?></a>
				</span>
			</td>
		</tr>
	</table>
	<?php print $data['change_field_type']; ?>
	<br/>
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
	<?php print $data['fields']; ?>
	
	<div class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php _e('Associations', CCTM_TXTDOMAIN); ?></span></h3>
		<div class="inside">
			<p class="cctm_decscription"><?php _e('Which post-types should this field be attached to?', CCTM_TXTDOMAIN); ?></p>
			
			<?php print $data['associations']; ?>
		</div><!-- /inside -->
	</div><!-- /postbox -->
	
    
    
	<br />
	<input type="submit" class="button-primary" value="<?php _e('Save', CCTM_TXTDOMAIN ); ?>" />
	
	<input type="submit" class="button" onclick="javascript:set_continue_editing();" value="<?php _e('Save and Continue Editing', CCTM_TXTDOMAIN ); ?>" />

	<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm_fields&a=list_custom_field_types" title="<?php _e('Cancel'); ?>" class="button"><?php _e('Cancel'); ?></a>
</form>

</div><!-- /metabox-holder -->