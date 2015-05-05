<script type="text/javascript">
	function set_continue_editing() {
		jQuery('#continue_editing').val(1);
		return true;
	}
</script>

<style>
	<?php print $data['style']; ?>
</style>

<div class="metabox-holder">

<form id="custom_post_type_manager_basic_form" method="post" action="">

	<input type="hidden" name="continue_editing" id="continue_editing" value="0" />
	<input type="hidden" name="old_id" id="old_id" value="<?php print $data['id']; ?>" />
	
	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
	<div class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php _e('Metabox Definition', CCTM_TXTDOMAIN); ?></span></h3>
		<div class="inside">		
		
			<!-- !id -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_id">			
				<label for="id_label" class="cctm_label cctm_text_label" id="id">
					<?php _e('ID', CCTM_TXTDOMAIN); ?> *
				</label>
				<input type="text" name="id" class="cctm_text <?php print $data['id.error_class']; ?>" id="id" value="<?php print htmlspecialchars($data['id']); ?>"/>
				<?php print $data['id.error']; ?>
				<span class="cctm_description"><?php _e('Unique CSS ID for the metabox. No special characters allowed.', CCTM_TXTDOMAIN); ?></span>
			</div>
		
			<!-- !title -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_title">			
				<label for="title_label" class="cctm_label cctm_text_label" id="cctm_label_title">
					<?php _e('Title', CCTM_TXTDOMAIN); ?> *
				</label>
				<input type="text" name="title" class="cctm_text <?php print $data['title.error_class']; ?>" id="title" value="<?php print htmlspecialchars($data['title']); ?>"/>
				<?php print $data['title.error']; ?>
				<span class="cctm_description"><?php _e('Title of the Metabox', CCTM_TXTDOMAIN); ?></span>
			</div>
		
		
			<!--!context -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_context">		
				<label for="context" class="cctm_label cctm_text_label" id="cctm_label_context"><?php _e('Context', CCTM_TXTDOMAIN); ?></label>
				<select name="context" class="cctm_dropdown" id="context">
					<option value="normal" <?php print CCTM::is_selected('normal',$data['context']); ?>><?php _e('Normal'); ?></option>
					<option value="advanced" <?php print CCTM::is_selected('advanced',$data['context']); ?>><?php _e('Advanced'); ?></option>
					<option value="side" <?php print CCTM::is_selected('side',$data['context']); ?>><?php _e('Side'); ?></option>
				</select>
				<span class="cctm_description"><?php _e('The part of the page where the edit screen section should be shown.', CCTM_TXTDOMAIN); ?></span>
			</div>	

			<!--!priority -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_priority">		
				<label for="priority" class="cctm_label cctm_text_label" id="cctm_label_priority"><?php _e('Priority', CCTM_TXTDOMAIN); ?></label>
				<select name="priority" class="cctm_dropdown" id="priority">
					<option value="high" <?php print CCTM::is_selected('high',$data['priority']); ?>><?php _e('High'); ?></option>
					<option value="core" <?php print CCTM::is_selected('core',$data['priority']); ?>><?php _e('Core'); ?></option>
					<option value="default" <?php print CCTM::is_selected('default',$data['priority']); ?>><?php _e('Default'); ?></option>
					<option value="low" <?php print CCTM::is_selected('low',$data['priority']); ?>><?php _e('Low'); ?></option>
				</select>
				<span class="cctm_description"><?php _e('The priority within the context where the boxes should show.', CCTM_TXTDOMAIN); ?></span>
			</div>

			<!--!callback -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_callback">			
				<label for="callback_label" class="cctm_label cctm_text_label" id="cctm_label_callback">
					<?php _e('Callback Function', CCTM_TXTDOMAIN); ?>
				</label>
				<input type="text" name="callback" class="cctm_text <?php print $data['callback.error_class']; ?>" id="callback" value="<?php print htmlspecialchars($data['callback']); ?>"/>
				<?php print $data['callback.error']; ?>
				<span class="cctm_description"><?php _e('Use this only if you want to override the standard CCTM behavior.', CCTM_TXTDOMAIN); ?></span>
			</div>				

			<!--!callback_args -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_callback_args">			
				<label for="callback_args_label" class="cctm_label cctm_text_label" id="cctm_label_callback_args">
					<?php _e('Callback Arguments', CCTM_TXTDOMAIN); ?>
				</label>
				<input type="text" name="callback_args" class="cctm_text" id="callback_args" value="<?php print htmlspecialchars($data['callback_args']); ?>"/>
				<span class="cctm_description"><?php _e('Comma-separated arguments to pass into your callback function. These are used only if you supply a callback function.', CCTM_TXTDOMAIN); ?></span>
			</div>

            <!--!visiblity_control -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_visibility_control">			
				<label for="visibility_control_label" class="cctm_label cctm_text_label" id="cctm_label_visibility_control">
					<?php _e('Visibility Control', CCTM_TXTDOMAIN); ?>
				</label>
				<input type="text" name="visibility_control" class="cctm_text" id="visibility_control" value="<?php print htmlspecialchars($data['visibility_control']); ?>"/>
				<span class="cctm_description"><?php _e('Fine-tune the display of your metabox by specifying template file names, e.g. <code>page-about.php</code> or you may comma-separate multiple names.', CCTM_TXTDOMAIN); ?></span>
			</div>

		</div><!-- /inside -->
	</div><!-- /postbox -->
	
	<div class="postbox">
		<div class="handlediv" title="Click to toggle"><br /></div>
		<h3 class="hndle"><span><?php _e('Manual Overrides', CCTM_TXTDOMAIN); ?></span></h3>
		<div class="inside">
			<p class="custom_field_info">
			<?php _e('Check a box to force the metabox to be drawn even if no custom fields are in it.', CCTM_TXTDOMAIN); ?></p>
			<?php print $data['associations']; ?>
		</div><!-- /inside -->
	</div><!-- /postbox -->

	<br />
	<input type="submit" class="button-primary" value="<?php _e('Save', CCTM_TXTDOMAIN ); ?>" />
	
	<input type="submit" class="button" onclick="javascript:set_continue_editing();" value="<?php _e('Save and Continue Editing', CCTM_TXTDOMAIN ); ?>" />

<?php if (CCTM::get_value($_GET,'a') == 'edit_metabox'): ?>

	<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm&a=delete_metabox&id=<?php print $data['id']; ?>" title="<?php _e('Delete'); ?>" class="button"><?php _e('Delete'); ?></a>
	
<?php	endif; ?>
	<a href="<?php print get_admin_url(false, 'admin.php'); ?>?page=cctm" title="<?php _e('Cancel'); ?>" class="button"><?php _e('Cancel'); ?></a>
</form>

</div><!-- /metabox-holder -->