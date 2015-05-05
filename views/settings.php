<style>
	h2 {
		border-bottom: 1px solid black;
		width: 500px;
	}
</style>

<form id="custom_post_type_manager_settings" method="post">

	<h2><?php _e('Database', CCTM_TXTDOMAIN); ?></h2>
	<!--!Delete Posts -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_delete_posts">
		<input type="checkbox" name="delete_posts" class="cctm_checkbox" id="delete_posts" value="1" <?php print $data['settings']['delete_posts']; ?>/>
		<label for="delete_posts" class="cctm_label cctm_checkbox_label" id="cctm_label_delete_posts">
			<?php _e('Delete Posts', CCTM_TXTDOMAIN); ?>
		</label>
		<span class="cctm_description"><?php _e('Check this option if you want to delete posts when you delete a post-type definition.', CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Delete Custom Fields -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_delete_custom_fields">
		<input type="checkbox" name="delete_custom_fields" class="cctm_checkbox" id="delete_custom_fields" value="1" <?php print $data['settings']['delete_custom_fields']; ?>/>
		<label for="delete_custom_fields" class="cctm_label cctm_checkbox_label" id="cctm_label_delete_custom_fields">
			<?php _e('Delete Custom Fields', CCTM_TXTDOMAIN); ?>
		</label>
		<span class="cctm_description"><?php _e('Check this option if you want to delete custom fields from the database when you delete a custom field definition.', CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Save empty Fields -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_save_empty_fields">
		<input type="checkbox" name="save_empty_fields" class="cctm_checkbox" id="save_empty_fields" value="1" <?php print $data['settings']['save_empty_fields']; ?>/>
		<label for="save_empty_fields" class="cctm_label cctm_checkbox_label" id="cctm_label_save_empty_fields">
			<?php _e('Save Empty Fields', CCTM_TXTDOMAIN); ?>
		</label>
		<span class="cctm_description"><?php _e("If checked, the CCTM will create a row in the postmeta table for the values for each post's custom fields. Uncheck this if you need to save some space in your database.", CCTM_TXTDOMAIN); ?></span>
	</div>

	<h2><?php _e('Menus', CCTM_TXTDOMAIN); ?></h2>
	<!--!Custom Fields Menu -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_show_custom_fields_menu">
		<input type="checkbox" name="show_custom_fields_menu" class="cctm_checkbox" id="show_custom_fields_menu" value="1" <?php print $data['settings']['show_custom_fields_menu']; ?>/>
		<label for="show_custom_fields_menu" class="cctm_label cctm_checkbox_label" id="cctm_label_show_custom_fields_menu">
			<?php _e('Show Custom Fields Menu', CCTM_TXTDOMAIN); ?>
		</label>
		<span class="cctm_description"><?php _e('Check this option if you want a "Custom Fields" menu item to appear under each post-type.', CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Settings Menu -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_show_settings_menu">
		<input type="checkbox" name="show_settings_menu" class="cctm_checkbox" id="show_settings_menu" value="1" <?php print $data['settings']['show_settings_menu']; ?>/>
		<label for="show_settings_menu" class="cctm_label cctm_checkbox_label" id="cctm_label_show_settings_menu">
			<?php _e('Show Settings Menu', CCTM_TXTDOMAIN); ?>
		</label>
		<span class="cctm_description"><?php _e('Check this option if you want a "Settings" menu item to appear under each custom post-type.', CCTM_TXTDOMAIN); ?></span>
	</div>


	<!--!Hide Posts Menu -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_hide_posts">
		<input type="checkbox" name="hide_posts" class="cctm_checkbox" id="hide_posts" value="1" <?php print $data['settings']['hide_posts']; ?>/>
		<label for="hide_posts" class="cctm_label cctm_checkbox_label" id="cctm_label_hide_posts">
			<?php _e('Hide Posts Menu', CCTM_TXTDOMAIN); ?>
			<img src="<?php print CCTM_URL; ?>/images/wp-post.png" height="16" width="16" />
		</label>
		<span class="cctm_description"><?php _e('Hide Posts from the primary WordPress admin menu. Do not do this if you have customized the fields for posts!', CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Hide Pages Menu -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_hide_pages">
		<input type="checkbox" name="hide_pages" class="cctm_checkbox" id="hide_pages" value="1" <?php print $data['settings']['hide_pages']; ?>/>
		<label for="hide_pages" class="cctm_label cctm_checkbox_label" id="cctm_label_hide_pages">
			<?php _e('Hide Pages Menu', CCTM_TXTDOMAIN); ?>
			<img src="<?php print CCTM_URL; ?>/images/wp-page.png" height="16" width="16" />
		</label>
		<span class="cctm_description"><?php _e('Hide Pages from the primary WordPress admin menu. Do not do this if you have customized the fields for pages!', CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Hide Links Menu -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_hide_links">
		<input type="checkbox" name="hide_links" class="cctm_checkbox" id="hide_links" value="1" <?php print $data['settings']['hide_links']; ?>/>
		<label for="hide_links" class="cctm_label cctm_checkbox_label" id="cctm_label_hide_links">
			<?php _e('Hide Links Menu', CCTM_TXTDOMAIN); ?>
			<img src="<?php print CCTM_URL; ?>/images/wp-link.png" height="16" width="16" />
		</label>
		<span class="cctm_description"><?php _e('Hide Links from the primary WordPress admin menu.', CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Hide Comments Menu -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_hide_comments">
		<input type="checkbox" name="hide_comments" class="cctm_checkbox" id="hide_comments" value="1" <?php print $data['settings']['hide_comments']; ?>/>
		<label for="hide_comments" class="cctm_label cctm_checkbox_label" id="cctm_label_hide_comments">
			<?php _e('Hide Comments Menu', CCTM_TXTDOMAIN); ?>
			<img src="<?php print CCTM_URL; ?>/images/wp-comment.png" height="16" width="16" />
		</label>
		<span class="cctm_description"><?php _e('Hide Comments from the primary WordPress admin menu.', CCTM_TXTDOMAIN); ?></span>
	</div>


	<h2><?php _e('Appearance', CCTM_TXTDOMAIN); ?></h2>
	<!--!Show foreign post types -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_show_foreign_post_types">
		<input type="checkbox" name="show_foreign_post_types" class="cctm_checkbox" id="show_foreign_post_types" value="1" <?php print $data['settings']['show_foreign_post_types']; ?>/>
		<label for="show_foreign_post_types" class="cctm_label cctm_checkbox_label" id="cctm_label_show_foreign_post_types">
			<?php _e('Display Foreign Post Types', CCTM_TXTDOMAIN); ?> <img src="<?php print CCTM_URL; ?>/images/spy.png" height="16" width="16" />
		</label>
		<span class="cctm_description"><?php _e("Check this box if you want to display any post-types registered with some other plugin. You can add custom fields to them.", CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Include Summarize Posts TinyMCE button -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_summarizeposts_tinymce">
		<input type="checkbox" name="summarizeposts_tinymce" class="cctm_checkbox" id="summarizeposts_tinymce" value="1" <?php print $data['settings']['summarizeposts_tinymce']; ?>/>
		<label for="summarizeposts_tinymce" class="cctm_label cctm_checkbox_label" id="cctm_label_summarizeposts_tinymce">
			<?php _e('Summarize Posts TinyMCE Button', CCTM_TXTDOMAIN); ?> <img src="<?php print CCTM_URL; ?>/images/summarize_posts_icon.png" height="16" width="16" />
		</label>
		<span class="cctm_description"><?php _e("Provides a TinyMCE button for a graphically choosing search criteria and generating a Summarize Posts shortcode.", CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Include Custom Fields TinyMCE button -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_summarizeposts_tinymce">
		<input type="checkbox" name="custom_fields_tinymce" class="cctm_checkbox" id="custom_fields_tinymce" value="1" <?php print $data['settings']['custom_fields_tinymce']; ?>/>
		<label for="custom_fields_tinymce" class="cctm_label cctm_checkbox_label" id="cctm_label_custom_fields_tinymce">
			<?php _e('Custom Fields TinyMCE Button', CCTM_TXTDOMAIN); ?> <img src="<?php print CCTM_URL; ?>/images/wrench.png" height="16" width="16" />
		</label>
		<span class="cctm_description"><?php _e("Provides a TinyMCE button for a graphically choosing a custom field whose value you wish to display in the main content block.", CCTM_TXTDOMAIN); ?></span>
	</div>

	<!--!Enable Right Now Widget -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_enable_right_now">
		<input type="checkbox" name="enable_right_now" class="cctm_checkbox" id="enable_right_now" value="1" <?php print $data['settings']['enable_right_now']; ?>/>
		<label for="enable_right_now" class="cctm_label cctm_checkbox_label" id="cctm_label_enable_right_now">
			<?php _e('Right Now Widget Support', CCTM_TXTDOMAIN); ?>
		</label>
		<span class="cctm_description"><?php _e("Should custom post-types appear in the Dashboard's Right Now widget? You can customize this for each post-type.", CCTM_TXTDOMAIN); ?></span>
	</div>

	<h2><?php _e('Advanced', CCTM_TXTDOMAIN); ?></h2>

	<!--!Show Pages in RSS feed -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_pages_in_rss_feed">
		<input type="checkbox" name="pages_in_rss_feed" class="cctm_checkbox" id="pages_in_rss_feed" value="1" <?php print $data['settings']['pages_in_rss_feed']; ?>/>
		<label for="pages_in_rss_feed" class="cctm_label cctm_checkbox_label" id="cctm_label_pages_in_rss_feed">
			<?php _e('Show Pages in RSS Feed', CCTM_TXTDOMAIN); ?>
			<img src="<?php print CCTM_URL;?>/images/rss.jpg" height="16" width="16" als="RSS"/>
		</label>
		<span class="cctm_description"><?php _e("Should your pages show up in your RSS feed?", CCTM_TXTDOMAIN); ?></span>
	</div>
	
	<!--!Cache Thumbnail Images -->
	<div class="cctm_element_wrapper" id="custom_field_wrapper_cache_thumbnail_images">
		<input type="checkbox" name="cache_thumbnail_images" class="cctm_checkbox" id="cache_thumbnail_images" value="1" <?php print $data['settings']['cache_thumbnail_images']; ?>/>
		<label for="cache_thumbnail_images" class="cctm_label cctm_checkbox_label" id="cctm_label_cache_thumbnail_images">
			<?php _e('Cache Thumbnail Images', CCTM_TXTDOMAIN); ?>
		</label>
		<span class="cctm_description"><?php _e('Check this option to let CCTM generate low-quality, smaller images for use in the post-selector for relation, image, and media fields.', CCTM_TXTDOMAIN); ?>
		<br/>
			<font style="color:red;"><?php _e('<strong>WARNING:</strong> this is experimental.  The post-selector may encounter white-screens if your source images are too large (see <a href="http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=262">issue 262</a>',CCTM_TXTDOMAIN); ?>.</font>
		</span>
	</div>


	<!--!Custom Field settings links -->
	<?php print $data['custom_fields_settings_links']; ?>

	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	<br/>
	<div class="custom_content_type_mgr_form_controls">
		<input type="submit" name="Submit" class="button-primary" value="<?php print $data['submit']; ?>" />
	</div>
</form>