<?php
/*------------------------------------------------------------------------------
This is the massive page that is used to create and edit post_type definitions.

~INPUT: This page should be included with two variables in scope:
	$action	string	can be set to 'create' if this form is to be used to create a def.
	$data['post_type']	string	the name of the post_type
	$def	mixed	The definition of the post_type which contains all the 
					information for the post_type in question.  The format 
					of this variable should be exactly the format accepted 
					by the register_post_type() function (yes, the input to 
					that function is unwieldy, but translating data structures 
					back and forth is probably worse).

Note that the $def array contains some extra keys for controlling UX and validation:
use_default_menu_icon	-- checkbox for controlling
permalink_action

I'm using some *probably* unnecessary instances of htmlspecialchars(), but I 
just want to make sure that the form is presented uncorrupted.
------------------------------------------------------------------------------*/
?>
<form id="custom_post_type_manager_basic_form" method="post">

<script type="text/javascript">
	/* Hide some of the divs by default */
	jQuery(document).ready(function(){
		toggle_image_detail();
		toggle_page_attributes();
		toggle_custom_columns();
		toggle_div('cctm_hierarchical_custom', 'custom_field_wrapper_custom_hierarchy', '1');
		jQuery('.checkall').click(function () {
			jQuery(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
		});

		/* Drives the tab layout for this page. */
		jQuery(function() {
			jQuery( "#tabs" ).tabs();
		});
		
		/* Handle the sortable table(s) */
		jQuery( "#custom-columns" ).sortable({
				items: "tr:not(.no-sort)"
			});
		jQuery( "#custom-columns" ).disableSelection();

	});
	
	
	/* Used to show additional menu icons if the "use default" is deselected. */
	function toggle_image_detail()
	{
		if( jQuery('#use_default_menu_icon:checked').val() == '1' )
		{
            jQuery('#menu_icon_container').hide("slide");
        } 
        else 
        {
            jQuery('#menu_icon_container').show("slide");

        }
	}

	/*------------------------------------------------------------------------------
	Check a given form element value and show or hide a separate div
	
	@param	string	CSS id of the form element
	@param	string	CSS of the div id  to show or hide
	@param	string	value to test against
	------------------------------------------------------------------------------*/
	function toggle_div(element_id, target_id, test_value)
	{
		if( jQuery('#'+element_id+':checked').val() == test_value || 'custom' == test_value)
		{
            jQuery('#'+target_id).show("slide");
        }
        else 
        {
            jQuery('#'+target_id).hide("slide");

        }
	}

	/*------------------------------------------------------------------------------
	Enables/Disables page attributes
	------------------------------------------------------------------------------*/
	function toggle_page_attributes() {
		if( jQuery('#supports_page-attributes:checked').val()) {
			//alert('Checked!');
			jQuery('#extended_page_attributes :input').removeAttr('disabled');
        }
        else {
			//alert('NOT Checked.');
			jQuery('#extended_page_attributes :input').attr('disabled', true);
        }

	}

	/*------------------------------------------------------------------------------
	Visually grays out all custom column stuff
	------------------------------------------------------------------------------*/
	function toggle_custom_columns() {
		if( jQuery('#cctm_custom_columns_enabled:checked').val()) {
			//alert('Checked!');
			jQuery('#custom-columns :input').removeAttr('disabled');
        }
        else {
			//alert('NOT Checked.');
			jQuery('#custom-columns :input').attr('disabled', true);
        }

	}
	
	/* Used to send a full img path to the id="menu_icon" field */
	function send_to_menu_icon(src)
	{
		jQuery('#menu_icon').val(src);
		// show the user some eye-candy so they know something happened
		jQuery('#sample_icon').html('<img src="'+src+'" height="16" width="16"/>');
	}	

</script>

<div id="tabs">
	<ul>
		<li><a href="#basic-tab"><?php _e('Basic', CCTM_TXTDOMAIN); ?></a></li>
		<li><a href="#labels-tab"><?php _e('Labels', CCTM_TXTDOMAIN); ?></a></li>
		<li><a href="#fields-tab"><?php _e('Fields', CCTM_TXTDOMAIN); ?></a></li>
		<li><a href="#columns-tab"><?php _e('Columns', CCTM_TXTDOMAIN); ?></a></li>
		<li><a href="#menu-tab"><?php _e('Menu', CCTM_TXTDOMAIN); ?></a></li>
		<li><a href="#urls-tab"><?php _e('URLs', CCTM_TXTDOMAIN); ?></a></li>
		<li><a href="#advanced-tab"><?php _e('Advanced', CCTM_TXTDOMAIN); ?></a></li>
		<li><a href="#taxonomies-tab"><?php _e('Taxonomies', CCTM_TXTDOMAIN); ?></a></li>
	</ul>

	<div style="clear:both;"></div>	
	
	<div id="basic-tab">
		
		<!--!Post Type -->
		<input type="hidden" name="original_post_type_name" value="<?php print $data['post_type']; ?>" />
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_post_type">

			<label for="post_type" class="cctm_label cctm_text_label" id="cctm_label_post_type">
				<?php _e('post_type', CCTM_TXTDOMAIN); ?>* </label>
			<input type="text" name="post_type" class="cctm_text" id="post_type" value="<?php print htmlspecialchars($data['post_type']); ?>"/>
			<span class="cctm_description"><?php _e('This name may show up in your URLs, e.g. ?movie=epic-movie. This will also make a new theme file available, starting with prefix named "single-", e.g. <code>single-movie.php</code>.', CCTM_TXTDOMAIN); ?> <?php print $data['edit_warning']; ?></span>
		</div>
		
		<!-- menu_name_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_menu_name_label">
			<label for="menu_name_label" class="cctm_label cctm_text_label" id="cctm_label_menu_name_label">
				<?php _e('Menu Name', CCTM_TXTDOMAIN); ?>* 
				<a rel="ungrouped" href="<?php print CCTM_URL; ?>/images/screenshots/menu-name.jpg" title="Menu Name*" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>

			<input type="text" name="labels[menu_name]" class="cctm_text" id="menu_name_label" value="<?php print htmlspecialchars($data['def']['labels']['menu_name']);?>"/>
			<span class="cctm_description"><?php _e('The menu name text. This string is the name to give menu items. Defaults to value of name.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		
		<!--!Description-->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_description">
					
			<label for="description" class="cctm_label cctm_textarea_label" id="cctm_label_description"><?php _e('Description', CCTM_TXTDOMAIN); ?></label>
			<textarea name="description" class="cctm_textarea" id="description" rows="4" cols="60"><?php print htmlspecialchars($data['def']['description']); ?></textarea>
		</div>
		
		<!--!Use Default Menu Icon -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_use_default_menu_icon">
			<input type="checkbox" name="use_default_menu_icon" class="cctm_checkbox" id="use_default_menu_icon" value="1"  onclick="javascript:toggle_image_detail('menu_icon_container');" <?php print CCTM::is_checked($data['def']['use_default_menu_icon']); ?>/> 
			<label for="use_default_menu_icon" class="cctm_label cctm_checkbox_label" id="cctm_label_use_default_menu_icon"><?php _e('Use Default Menu Icon', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Leave this checked to use the default posts icon.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<div id="menu_icon_container" style="display: none;">		
			<!--!Menu Icon -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_menu_icon">		
				<label for="menu_icon" class="cctm_label cctm_text_label" id="cctm_label_menu_icon"><?php _e('Menu Icon', CCTM_TXTDOMAIN); ?></label>
				<input type="text" name="menu_icon" class="cctm_text" id="menu_icon" value="<?php if (isset($data['def']['menu_icon'])) { print htmlspecialchars($data['def']['menu_icon']); } ?>" size="100"/>
					
					<span id="sample_icon"><?php 
					if (isset($data['def']['menu_icon'])) { 
						printf('<img src="%s" heigh="16" width="16" />', htmlspecialchars($data['def']['menu_icon']));
					}
					?></span>
					<br /><br />
						<span class="cctm_description"><?php _e('Choose an icon from the list below or paste a full URL to a 16x16 icon here.', CCTM_TXTDOMAIN); ?></span>
			</div>
		
			<div style="width:700px; margin-top:10px;">
				<?php print $data['icons']; ?>
			</div>
		</div>
	</div>

	<!--!LABELS================================================================================================ -->	
	<!--!Labels -->
	<div id="labels-tab">	
	
		<!--singular_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_singular_label">			
			<label for="labels[singular_name]" class="cctm_label cctm_text_label" id="cctm_label_labels[singular_name]">
			<?php _e('Singular', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="labels[singular_name]" class="cctm_text" id="labels[singular_name]" value="<?php print htmlspecialchars($data['def']['labels']['singular_name']); ?>"/>
					<span class="cctm_description"><?php _e('Human readable single instance of this content type, e.g. "Post"', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<!--!Plural Label (Main Label)-->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_label">					
			<label for="label" class="cctm_label cctm_text_label" id="cctm_label_label">
			<?php _e('Main Menu Label (Plural)', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="label" class="cctm_text" id="label" value="<?php print htmlspecialchars($data['def']['label']); ?>"/>
					<span class="cctm_description"><?php _e('Plural name used in the admin menu, e.g. "Posts"', CCTM_TXTDOMAIN); ?></span>
		</div>

		<!-- add_new_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_add_new_label_label">			
			<label for="add_new_label" class="cctm_label cctm_text_label" id="cctm_label_add_new_label">
				<?php _e('Add New', CCTM_TXTDOMAIN); ?>
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/add-new.jpg" title="Add New" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[add_new]" class="cctm_text" id="add_new_label" value="<?php print htmlspecialchars($data['def']['labels']['add_new']); ?>"/>
			<span class="cctm_description"><?php _e('The add new text. The default is Add New for both hierarchical and non-hierarchical types.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<!-- add_new_item_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_add_new_item_label_label">			
			<label for="add_new_item_label" class="cctm_label cctm_text_label" id="cctm_label_add_new_item_label">
				<?php _e('Add New Item', CCTM_TXTDOMAIN); ?>
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/add-new-item.jpg" title="Add New Item" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[add_new_item]" class="cctm_text" id="add_new_item_label" value="<?php print htmlspecialchars($data['def']['labels']['add_new_item']); ?>"/>
			<span class="cctm_description"><?php _e('The add new item text. Default is Add New Post/Add New Page', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<!-- edit_item_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_edit_item_label_label">			
			<label for="edit_item_label" class="cctm_label cctm_text_label" id="cctm_label_edit_item_label">
				<?php _e('Edit Item', CCTM_TXTDOMAIN); ?>
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/edit-item.jpg" title="Edit Item" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[edit_item]" class="cctm_text" id="edit_item_label" value="<?php print htmlspecialchars($data['def']['labels']['edit_item']); ?>"/>
			<span class="cctm_description"><?php _e('The edit item text. Default is Edit Post/Edit Page', CCTM_TXTDOMAIN); ?></span>
		</div>	
		
		<!-- new_item_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_new_item_label_label">			
			<label for="new_item_label" class="cctm_label cctm_text_label" id="cctm_label_new_item_label">
				<?php _e('New Item', CCTM_TXTDOMAIN); ?>
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/new-item.jpg" title="New Item" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[new_item]" class="cctm_text" id="new_item_label" value="<?php print htmlspecialchars($data['def']['labels']['new_item']); ?>"/>
			<span class="cctm_description"><?php _e('The new item text. Default is New Post/New Page', CCTM_TXTDOMAIN); ?></span>

		</div>

		
		<!-- view_item_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_view_item_label">
			<label for="view_item_label" class="cctm_label cctm_text_label" id="cctm_label_view_item_label">
				<?php _e('View Item', CCTM_TXTDOMAIN); ?> 
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/view-item.jpg" title="View Item" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[view_item]" class="cctm_text" id="view_item_label" value="<?php print htmlspecialchars($data['def']['labels']['view_item']); ?>"/>
			<span class="cctm_description"><?php _e('The view item text. Default is View Post/View Page', CCTM_TXTDOMAIN); ?></span>
		</div>

		
		<!-- search_items_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_search_items_label">
			<label for="search_items_label" class="cctm_label cctm_text_label" id="cctm_label_search_items_label">
				<?php _e('Search Items', CCTM_TXTDOMAIN); ?>
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/search-items.jpg" title="Search Items" class="thickbox">

					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[search_items]" class="cctm_text" id="search_items_label" value="<?php print htmlspecialchars($data['def']['labels']['search_items']); ?>"/>
			<span class="cctm_description"><?php _e('The search items text. Default is Search Posts/Search Pages', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<!-- not_found_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_not_found_label">
			<label for="not_found_label" class="cctm_label cctm_text_label" id="cctm_label_not_found_label">
				<?php _e('Not Found', CCTM_TXTDOMAIN); ?> 
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/not-found.jpg" title="Not Found" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[not_found]" class="cctm_text" id="not_found_label" value="<?php print htmlspecialchars($data['def']['labels']['not_found']); ?>"/>
			<span class="cctm_description"><?php _e('The not found text. Default is No posts found/No pages found', CCTM_TXTDOMAIN); ?></span>
		</div>

		
		<!-- not_found_in_trash_label -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_not_found_in_trash_label">
			<label for="not_found_in_trash_label" class="cctm_label cctm_text_label" id="cctm_label_not_found_in_trash_label">
				<?php _e('Not Found in Trash', CCTM_TXTDOMAIN); ?>
				<a rel="label-screenshots" href="<?php print CCTM_URL; ?>/images/screenshots/not-found-in-trash.jpg" title="Not Found in Trash" class="thickbox">
					<img src="<?php print CCTM_URL; ?>/images/question-mark.gif" width="16" height="16" />
				</a>
			</label>
			<input type="text" name="labels[not_found_in_trash]" class="cctm_text" id="not_found_in_trash_label" value="<?php print htmlspecialchars($data['def']['labels']['not_found_in_trash']); ?>"/>
			<span class="cctm_description"><?php _e('The not found in trash text. Default is No posts found in Trash/No pages found in Trash', CCTM_TXTDOMAIN); ?></span>
		</div>

		
		<!-- parent_item_colon_label -->		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_parent_item_colon_label">			
			<label for="labels[parent_item_colon]" class="cctm_label cctm_text_label" id="cctm_label_labels[parent_item_colon]">
			<?php _e('Parent Item Colon', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="labels[parent_item_colon]" class="cctm_text" id="labels[parent_item_colon]" value="<?php print htmlspecialchars($data['def']['labels']['parent_item_colon']); ?>"/>
					<span class="cctm_description"><?php _e('The parent text (used only on hierarchical types). Default is <em>Parent Page</em>', CCTM_TXTDOMAIN); ?></span>
		</div>
		
	</div>
	
	<!--!FIELDS================================================================================================ -->	
	<div id="fields-tab">
		<p><?php _e('Your post type must have either the title or content boxes checked; otherwise WordPress will revert to the default behavior and include the title and the content fields.', CCTM_TXTDOMAIN); ?></p>
		
		<!--!Supports -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_title">			
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_title" value="title" <?php print CCTM::is_checked($data['def']['supports'], 'title'); ?> /> 
			<label for="supports_title" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_title_label"><?php _e('Title', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Post Title.', CCTM_TXTDOMAIN); ?> <span style="color:red;"><?php _e('Unchecking this is not recommended.', CCTM_TXTDOMAIN); ?></span></span>
		</div>
			
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_editor">		
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_editor" value="editor" <?php print CCTM::is_checked($data['def']['supports'], 'editor'); ?> /> 
			<label for="supports_editor" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_content_label">
				<?php _e('Content', CCTM_TXTDOMAIN); ?></label>
					<span class="cctm_description"><?php _e('Main content block.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_author">			
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_author" value="author"  /<?php print CCTM::is_checked($data['def']['supports'], 'author'); ?> > 
			<label for="supports_author" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_author_label"><?php _e('Author', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Track the author.', CCTM_TXTDOMAIN); ?></span>
		</div>
					
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_excerpt">			
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_excerpt" value="excerpt"  <?php print CCTM::is_checked($data['def']['supports'], 'excerpt'); ?>/> 
			<label for="supports_excerpt" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_excerpt_label"><?php _e('Excerpt', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Small summary field.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_custom-fields">			
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_custom-fields" value="custom-fields" <?php print CCTM::is_checked($data['def']['supports'], 'custom-fields'); ?> /> 
			<label for="supports_custom-fields" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_custom_fields_label"><?php _e('Supports Custom Fields', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Currently, this functionality is overridden by any custom fields you have defined for this content type.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_post-formats">			
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_post-formats" value="post-formats" <?php print CCTM::is_checked($data['def']['supports'], 'post-formats'); ?> /> 
			<label for="supports_post-formats" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_post_formats_label"><?php _e('Post Formats', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('A Post Format is a piece of meta information that can be used by a theme to customize its presentation of a post.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<!-- supports_page-attributes -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_page-attributes">
			<input type="checkbox" 
				name="supports[]" 
				class="cctm_checkbox" 
				id="supports_page-attributes" 
				value="page-attributes" 
				onclick="javascript:toggle_page_attributes();" <?php print CCTM::is_checked($data['def']['supports'], 'page-attributes'); ?> />
			<label for="supports_page-attributes" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_page-attributes">
				<?php _e('Page Attributes', CCTM_TXTDOMAIN); ?>
			</label>
			<span class="cctm_description"><?php _e('When checked, the create/edit screens will include a meta box for menu position and other page attributes.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<div id="extended_page_attributes" style="width:500px; padding-left:50px">
		
			<!-- supports_thumbnail -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_thumbnail">
				<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_thumbnail" value="thumbnail" <?php print CCTM::is_checked($data['def']['supports'], 'thumbnail'); ?> /> 
				<label for="supports_thumbnail" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_thumbnail_label">
				<?php _e('Thumbnail', CCTM_TXTDOMAIN); ?></label>
				<span class="cctm_description"><?php _e("Featured image. The active theme must also support post-thumbnails. Include a line like the following in your theme's functions.php file: <br/><code>add_theme_support( 'post-thumbnails', array( 'name_of_your_post_type_here' ) );</code>", CCTM_TXTDOMAIN); ?></span>
			</div>
			
			<!-- hierarchical -->
			<div class="cctm_element_wrapper" id="custom_field_wrapper_hierarchical">
				<input type="checkbox" name="hierarchical" class="cctm_checkbox" id="hierarchical" value="1" <?php print CCTM::is_checked($data['def']['hierarchical']); ?>/> 
				<label for="hierarchical" class="cctm_label cctm_checkbox_label" id="cctm_label_hierarchical"><?php _e('Hierarchical', CCTM_TXTDOMAIN); ?></label>
				<span class="cctm_description"><?php _e('Allows parent to be specified.', CCTM_TXTDOMAIN); ?></span>
			</div>

			<div class="cctm_element_wrapper" id="custom_field_wrapper_hierarchical">
				<input type="checkbox" name="cctm_hierarchical_custom" class="cctm_checkbox" id="cctm_hierarchical_custom" value="1" <?php if (isset($data['def']['cctm_hierarchical_custom'])) { print CCTM::is_checked($data['def']['cctm_hierarchical_custom']); } ?> 
					onclick="javascript:toggle_div('cctm_hierarchical_custom', 'custom_field_wrapper_custom_hierarchy', '1');"/> 
				<label for="cctm_hierarchical_custom" class="cctm_label cctm_checkbox_label" id="cctm_label_hierarchical"><?php _e('Use Custom Hierarchy', CCTM_TXTDOMAIN); ?></label>
				<span class="cctm_description"><?php _e('Allows custom hierarchies to be specified.', CCTM_TXTDOMAIN); ?>
				(<?php _e('Hierarchical must be checked.', CCTM_TXTDOMAIN); ?>)</span>

				
			<!-- Working : Custom hierarchy-->
				<div id="custom_field_wrapper_custom_hierarchy" style="border: 1px solid black; background-color:#C0C0C0; padding: 10px;">
					<h3><?php _e('Custom Hierarchies', CCTM_TXTDOMAIN); ?></h3>
					<p><?php _e('Warning: this feature is experimental. Implementing it deviates from standard WordPress behavior. See <a href="http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=9" target="_blank">Issue 9</a> in the bugtracker.', CCTM_TXTDOMAIN); ?></p>
				
					<div class="cctm_element_wrapper" id="custom_field_wrapper_include_drafts">
						<input type="checkbox" name="cctm_hierarchical_includes_drafts" class="cctm_checkbox" id="cctm_hierarchical_includes_drafts" value="1" <?php if ( isset($data['def']['cctm_hierarchical_includes_drafts'])) { print CCTM::is_checked($data['def']['cctm_hierarchical_includes_drafts'], '1'); } ?> /> 
						<label for="cctm_hierarchical_includes_drafts" class="cctm_label cctm_checkbox_label" id="cctm_label_cctm_hierarchical_includes_drafts"><?php _e('Include Drafts?', CCTM_TXTDOMAIN); ?></label>
						<span class="cctm_description"><?php _e('By default, WordPress only allows you to use published pages in your hierarchy. Select this option to override that behavior.', CCTM_TXTDOMAIN); ?></span>
					</div>

					<h3><?php _e('Parent Post Types', CCTM_TXTDOMAIN); ?></h3>
					<span class="cctm_description"><?php _e('By default, WordPress only allows you to use posts of the same post-type in your hierarchy. Select which post types should be available as parents.', CCTM_TXTDOMAIN); ?></span>
<?php
				// checkbox_id, css_id, checked_value
				/* Handle custom hierarchical stuff */
				$i = 0;
				$args = array('public' => true );
				$post_types = get_post_types($args);
				//print_r($data['post_type']s); exit;
				foreach ( $post_types as $pt => $v ) {
					$is_checked = '';
					if ( isset($data['def']['cctm_hierarchical_post_types']) ) {
						if ( is_array($data['def']['cctm_hierarchical_post_types']) && in_array( $pt, $data['def']['cctm_hierarchical_post_types']) ) {
							$is_checked = 'checked="checked"';
						}
					}
					//  <input type="checkbox" name="vehicle" value="Car" checked="checked" />
					print '<span style="margin-left:20px;"><input type="checkbox" name="cctm_hierarchical_post_types[]" class="cctm_multiselect" id="cctm_hierarchical_post_types'.$i.'" value="'.$pt.'" '.$is_checked.'> <label class="cctm_muticheckbox" for="cctm_hierarchical_post_types'.$i.'">'.htmlspecialchars($pt).'</label></span><br/>';
					$i = $i + 1;					
				}
?>
				</div><!-- end custom hierarchical options -->
				
			</div>
			
			
			
		</div>
		
	</div>
	
	<!--!COLUMNS================================================================================================ -->
	<div id="columns-tab">
		<!-- cctm_custom_columns -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_cctm_custom_columns_enabled">
			<input type="checkbox" name="cctm_custom_columns_enabled" class="cctm_checkbox" id="cctm_custom_columns_enabled" value="1" <?php print CCTM::is_checked(CCTM::get_value($data['def'],'cctm_custom_columns_enabled', 0)); ?>
				onclick="javascript:toggle_custom_columns();" /> 
			<label for="cctm_custom_columns_enabled" class="cctm_label cctm_checkbox_label" id="cctm_label_cctm_custom_columns_enabled">
			<?php _e('Customize Columns', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('You can customize the columns visible when you display a list of all posts in this post-type.', CCTM_TXTDOMAIN);?> <span style="color:red;"><?php _e('WARNING: you can only use custom columns if the post-type name does not contain hyphens; underscores are Ok.', CCTM_TXTDOMAIN); ?></span></span>
		</div>
		<br />
		<!-- the columns -->
		<table class="wp-list-table widefat plugins" cellspacing="0">
			<thead>
				<tr>
					<th scope="col" id="sorter" class=""  style="width: 10px;">&nbsp;</th>
					<th scope="col" id="selected" class=""><?php _e('Display', CCTM_TXTDOMAIN); ?></th>
					<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
					<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
				</tr>
			</thead>
			
			<tfoot>
				<tr>
					<th scope="col" id="sorter" class=""  style="">&nbsp;</th>
					<th scope="col" id="selected" class="">&nbsp;</th>
					<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
					<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
				</tr>
			</tfoot>
			
			<tbody id="custom-columns">
			
				<?php if (isset($data['columns'])) { print $data['columns']; } ?>
				
			</tbody>
		</table>
		
	</div>
	
	<!--!MENU================================================================================================ -->
	<div id="menu-tab">
		<p><?php _e('These settings only apply if you have the <em>Show Admin User Interface</em> selected.', CCTM_TXTDOMAIN); ?></p>
		
		<!--!show_in_menu -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_cctm_show_in_menu">		
			<label for="cctm_show_in_menu" class="cctm_label cctm_text_label" id="cctm_label_cctm_show_in_menu"><?php _e('Show in Menus', CCTM_TXTDOMAIN); ?></label>
			<select name="cctm_show_in_menu" class="cctm_dropdown" id="cctm_show_in_menu">
				<option value="1" <?php print CCTM::is_selected('1',$data['def']['cctm_show_in_menu']); ?>><?php _e('Yes'); ?></option>
				<option value="0" <?php print CCTM::is_selected('0',$data['def']['cctm_show_in_menu']); ?>><?php _e('No'); ?></option>
				<option value="custom" <?php print CCTM::is_selected('custom',$data['def']['cctm_show_in_menu']); ?>><?php _e('Custom'); ?></option>
			</select>
			<div id="cctm_show_in_menu_wrapper" style="margin-left:20px;">
				<em><?php _e('Custom top-level Menu', CCTM_TXTDOMAIN); ?></em>: 
				<input type="text" name="cctm_show_in_menu_custom" id="cctm_show_in_menu_custom" value="<?php print htmlspecialchars(CCTM::get_value($data['def'], 'cctm_show_in_menu_custom')); ?>"/>
			</div>
			<span class="cctm_description"><?php _e('Whether to show the post type in the admin menu. Change this to <em>Custom</em> to specify a top level page like <code>tools.php</code> or <code>edit.php?post_type=page</code>', CCTM_TXTDOMAIN); ?></span>
		</div>

		<!--! show_in_admin_bar -->			
		<div class="cctm_element_wrapper" id="custom_field_wrapper_show_in_admin_bar">		
			<input type="checkbox" name="show_in_admin_bar" class="cctm_checkbox" id="show_in_admin_bar" value="1" <?php 
				print CCTM::is_checked($data['def']['show_in_admin_bar']); 
			?>/> 
			<label for="cctm_enable_right_now" class="cctm_label cctm_checkbox_label" id="cctm_label_cctm_enable_right_now"><?php _e('Show in Admin Bar', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Whether to make this post type available in the WordPress admin bar.', CCTM_TXTDOMAIN); ?></span>
		</div>
				
		<!--!Menu Position-->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_menu_position">
			<label for="menu_position" class="cctm_label cctm_text_label" id="cctm_label_menu_position"><?php _e('Menu Position', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="menu_position" class="cctm_text" id="menu_position" value="<?php print htmlspecialchars($data['def']['menu_position']); ?>"/>
			<span class="cctm_description"><?php _e('This setting determines where this post type should appear in the left-hand admin menu. Default: null (below Comments). E.g. "21" would cause this content type to display below Pages and above Comments.', CCTM_TXTDOMAIN); ?> 
				<ul style="margin-left:40px;">
					<li><strong>5</strong> - <?php _e('below Posts', CCTM_TXTDOMAIN); ?></li>
					<li><strong>10</strong> - <?php _e('below Media', CCTM_TXTDOMAIN); ?></li>
					<li><strong>15</strong> - <?php _e('below Links', CCTM_TXTDOMAIN); ?></li>
					<li><strong>20</strong> - <?php _e('below Pages', CCTM_TXTDOMAIN); ?></li>
					<li><strong>25</strong> - <?php _e('below Comments', CCTM_TXTDOMAIN); ?></li>
					<li><strong>60</strong> - <?php _e('below first separator', CCTM_TXTDOMAIN); ?></li>
					<li><strong>65</strong> - <?php _e('below Plugins', CCTM_TXTDOMAIN); ?></li>
					<li><strong>70</strong> - <?php _e('below Users', CCTM_TXTDOMAIN); ?></li>
					<li><strong>75</strong> - <?php _e('below Tools', CCTM_TXTDOMAIN); ?></li>
					<li><strong>80</strong> - <?php _e('below Settings', CCTM_TXTDOMAIN); ?></li>
					<li><strong>100</strong> - <?php _e('below second separator', CCTM_TXTDOMAIN); ?></li>
				</ul>
			</span>
		</div>

	</div>

	<!--!URLS================================================================================================ -->
	<div id="urls-tab">
	
		<!--!Rewrite with Front -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_rewrite_with_front">			
			<input type="checkbox" name="rewrite_with_front" class="cctm_checkbox" id="rewrite_with_front" value="1"  <?php print CCTM::is_checked($data['def']['rewrite_with_front']); ?>/> 
			<label for="rewrite_with_front" class="cctm_label cctm_checkbox_label" id="cctm_label_rewrite_with_front">
			<?php _e('Rewrite with Permalink Front', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Allow permalinks to be prepended with front base - defaults to checked', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_rewrite">			
			<label for="permalink_action" class="cctm_label" id="cctm_label_permalink_action">
				<?php _e('Permalink Action', CCTM_TXTDOMAIN); ?></label>
			<select name="permalink_action" class="cctm_dropdown cctm_dropdown_label" id="permalink_action">
				<option value="Off" <?php print CCTM::is_selected('Off', $data['def']['permalink_action']); ?>>Off</option>
				<option value="/%postname%/" <?php print CCTM::is_selected('/%postname%/', $data['def']['permalink_action']); ?>>/%postname%/</option>
				<option value="Custom" <?php print CCTM::is_selected('Custom', $data['def']['permalink_action']); ?>><?php _e('Custom', CCTM_TXTDOMAIN); ?></option>
			</select>
				
			<span class="cctm_description"><?php _e('Use permalink rewrites for this post_type? Default: Off', CCTM_TXTDOMAIN); ?>
				<ul style="margin-left:20px;">
					<li><strong><?php _e('Off', CCTM_TXTDOMAIN); ?></strong> - <?php _e('URLs for custom post_types will always look like: http://site.com/?post_type=book&p=39 even if the rest of the site is using a different permalink structure.', CCTM_TXTDOMAIN); ?></li>
					<li><strong>/%postname%/</strong> - <?php _e('Currently, this is the only custom permalink structure that is supported. Other formats are not supported.  Your URLs will look like http://site.com/your_post_type/your-title/', CCTM_TXTDOMAIN); ?></li>
					<li><strong><?php _e('Custom', CCTM_TXTDOMAIN); ?></strong> - <?php _e('Evaluate the contents of slug', CCTM_TXTDOMAIN); ?></li>
				<ul>
			</span>
		</div>
		
		<!--!Rewrite Slug -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_rewrite_slug">			
			<label for="rewrite_slug" class="cctm_label cctm_text_label" id="cctm_label_rewrite_slug"><?php _e('Rewrite Slug', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="rewrite_slug" class="cctm_text" id="rewrite_slug" value="<?php print htmlspecialchars($data['def']['rewrite_slug']); ?>"/>
			<span class="cctm_description"><?php _e("Prepend posts with this slug - defaults to post type's name", CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<!--!Permalink Action -->
		
		
		<!--!Query Var -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_query_var">
			<label for="query_var" class="cctm_label cctm_text_label" id="cctm_label_query_var"><?php _e('Query Variable', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="query_var" class="cctm_text" id="query_var" value="<?php print htmlspecialchars($data['def']['query_var']); ?>"/>
			<span class="cctm_description"><?php _e('(optional) Name of the query var to use for this post type. E.g. "my-var" would make for URLs like http://site.com/?my-var=your-title. If blank, the default structure is http://site.com/?post_type=your_post_type&p=18', CCTM_TXTDOMAIN); ?></span>

		</div>
	</div>
	
	<!--!ADVANCED================================================================================================ -->
	<div id="advanced-tab">
	

<div style="border:1px solid black; padding:10px;">
<fieldset>
		
		<!--!Public: check/uncheck all shortcut -->
		<input type="checkbox" id="public_checkall" class="checkall" />
		<label for="public_checkall" class="cctm_label cctm_checkbox_label" id="cctm_label_public_checkall"><?php _e('Check All', CCTM_TXTDOMAIN); ?></label>

	<div style="margin-left: 30px;">
		<!--!Public-->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_public">
			<input type="checkbox" name="public" class="cctm_checkbox" id="public" value="1" <?php print CCTM::is_checked($data['def']['public']); ?>/> 
			<label for="public" class="cctm_label cctm_checkbox_label" id="cctm_label_public"><?php _e('Public', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Set the public attribute verbosely so WordPress behaves as you would expect it to. See <a href="http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=212">Issue 212</a> in the bug tracker.', CCTM_TXTDOMAIN); ?></span>
		</div>		
		<!--!Show UI -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_show_ui">
			<input type="checkbox" name="show_ui" class="cctm_checkbox" id="show_ui" value="1" <?php print CCTM::is_checked($data['def']['show_ui']); ?>/> 
			<label for="show_ui" class="cctm_label cctm_checkbox_label" id="cctm_label_show_ui"><?php _e('Show Admin User Interface', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Should this post type be visible on the back-end?', CCTM_TXTDOMAIN); ?></span>
		</div>

		
		<!--! Show in Nav Menus -->			
		<div class="cctm_element_wrapper" id="custom_field_wrapper_show_in_nav_menus">		
			<input type="checkbox" name="show_in_nav_menus" class="cctm_checkbox" id="show_in_nav_menus" value="1" <?php print CCTM::is_checked($data['def']['show_in_nav_menus']); ?>/> 
			<label for="show_in_nav_menus" class="cctm_label cctm_checkbox_label" id="cctm_label_show_in_nav_menus"><?php _e('Show in Nav Menus', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Whether post_type is available for selection in navigation menus (under <em>Appearance --> Menus</em>). Your theme must support menus for this option to have any effect. Default: value of public argument', CCTM_TXTDOMAIN); ?></span>
		</div>

		<!--! Publicly Queriable -->			
		<div class="cctm_element_wrapper" id="custom_field_wrapper_publicly_queryable">		
			<input type="checkbox" name="publicly_queryable" class="cctm_checkbox" id="publicly_queryable" value="1" <?php 
				print CCTM::is_checked($data['def']['publicly_queryable']); 
			?>/> 
			<label for="publicly_queryable" class="cctm_label cctm_checkbox_label" id="cctm_label_publicly_queryable"><?php _e('Publicly Queriable', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Whether post_type queries can be performed from the front end. Usually this matches up with the <em>Public</em> setting.', CCTM_TXTDOMAIN); ?></span>
		</div>

		<!--! Include in Search -->			
		<div class="cctm_element_wrapper" id="custom_field_wrapper_include_in_search">		
			<input type="checkbox" name="include_in_search" class="cctm_checkbox" id="include_in_search" value="1" <?php 
				print CCTM::is_checked($data['def']['include_in_search']); 
			?>/> 
			<label for="include_in_search" class="cctm_label cctm_checkbox_label" id="cctm_label_include_in_search"><?php _e('Include in Search', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Whether to include posts with this post type in search results.', CCTM_TXTDOMAIN); ?></span>
		</div>

		<!--! Include in RSS -->			
		<div class="cctm_element_wrapper" id="custom_field_wrapper_include_in_rss">		
			<input type="checkbox" name="include_in_rss" class="cctm_checkbox" id="include_in_rss" value="1" <?php 
				print CCTM::is_checked($data['def']['include_in_rss']); 
			?>/> 
			<label for="include_in_rss" class="cctm_label cctm_checkbox_label" id="cctm_label_include_in_rss"><?php _e('Include in RSS feed', CCTM_TXTDOMAIN); ?> <img src="<?php print CCTM_URL;?>/images/rss.jpg" height="16" width="16" als="RSS"/></label>
			<span class="cctm_description"><?php _e('Should posts with this post type be included in the RSS feed?', CCTM_TXTDOMAIN); ?></span>
		</div>	
		
	</div>	
</fieldset>		
</div>

		<!-- Capability Type -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_capability_type">			
			<label for="capability_type" class="cctm_label cctm_text_label" id="cctm_label_capability_type"><?php _e('Capability Type', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="capability_type" class="cctm_text" id="capability_type" value="<?php print htmlspecialchars($data['def']['capability_type']); ?>"/>
			<span class="cctm_description"><?php _e('The string to use to build the read, edit, and delete capabilities. May be passed a comma-separated string to allow for alternative plurals (e.g. "child,children"). Default: "post".', CCTM_TXTDOMAIN); ?></span>
		</div>

		<!--!map_meta_cap -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_map_meta_cap">
			<input type="checkbox" name="map_meta_cap" class="cctm_checkbox" id="map_meta_cap" value="1" <?php print CCTM::is_checked($data['def']['map_meta_cap']); ?> /> 
			<label for="map_meta_cap" class="cctm_label cctm_checkbox_label" id="cctm_label_map_meta_cap"><?php _e('Map Meta Cap', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Whether to use the internal default meta capability handling. If checked, you must supply a valid mapping in the "Capabilities" field.', CCTM_TXTDOMAIN); ?></span>
		</div>
				
		<!-- Capabilities (string)-->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_capabilities">			
			<label for="capabilities" class="cctm_label cctm_text_label" id="cctm_label_capabilities"><?php _e('Capabilities', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="capabilities" class="cctm_text" id="capabilities" value="<?php print CCTM::get_value($data['def'],'capabilities'); ?>"/>
			<span class="cctm_description"><?php _e('URL-style notation of the capabilities for this post type, e.g. <code>publish_posts=publish_events&edit_posts=edit_events</code>.', CCTM_TXTDOMAIN); ?></span>
			<!-- span style="color:red;"><?php _e('WARNING: improper key/value combinations can throw numerous errors!', CCTM_TXTDOMAIN); ?></span-->
		</div>

		<!-- register_meta_box_cb -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_register_meta_box_cb">			
			<label for="register_meta_box_cb" class="cctm_label cctm_text_label" id="cctm_label_register_meta_box_cb"><?php _e('Meta Box Callback', CCTM_TXTDOMAIN); ?></label>
			<input type="text" name="register_meta_box_cb" class="cctm_text" id="register_meta_box_cb" value="<?php print htmlspecialchars(CCTM::get_value($data['def'],'register_meta_box_cb')); ?>"/>
			<span class="cctm_description"><?php _e('Provide an optional callback function that will be called when setting up the meta boxes for the edit form.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		<!--!Can Export -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_can_export">
			<input type="checkbox" name="can_export" class="cctm_checkbox" id="can_export" value="1" <?php print CCTM::is_checked($data['def']['can_export']); ?> /> 
			<label for="can_export" class="cctm_label cctm_checkbox_label" id="cctm_label_can_export"><?php _e('Can Export', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Can this post_type be exported.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
	
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_trackbacks">
					
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_trackbacks" value="trackbacks" <?php print CCTM::is_checked($data['def']['supports'], 'trackbacks'); ?> /> 
			<label for="supports_trackbacks" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_trackbacks_label"><?php _e('Trackbacks', CCTM_TXTDOMAIN); ?> <img src="<?php print CCTM_URL;?>/images/trackbacks.png" height="16" width="16" als="RSS"/></label>
			<span class="cctm_description"><?php _e('Allows cross-blog notification. See <a href="http://codex.wordpress.org/Introduction_to_Blogging#Trackbacks">official documentation</a>.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_comments">			
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_comments" value="comments"  <?php print CCTM::is_checked($data['def']['supports'], 'comments'); ?>/> 
			<label for="supports_comments" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_comments_label"><?php _e('Enable Comments', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('If checked, your template will require the <code>comments_template();</code> function.', CCTM_TXTDOMAIN); ?></span>
		</div>
		
		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_supports_revisions">			
			<input type="checkbox" name="supports[]" class="cctm_checkbox" id="supports_revisions" value="revisions" <?php print CCTM::is_checked($data['def']['supports'], 'revisions'); ?> /> 
			<label for="supports_revisions" class="cctm_label cctm_checkbox_label" id="cctm_label_supports_revisions_label"><?php _e('Store Revisions', CCTM_TXTDOMAIN); ?></label>
					<span class="cctm_description"><?php _e('Revisions are useful if you ever need to go back to an older version of a document.', CCTM_TXTDOMAIN);?> <span style="color:red;"><?php _e('WARNING: revisions do not store custom field data!  This is a limitation of WordPress (see <a href="http://code.google.com/p/wordpress-custom-content-type-manager/issues/detail?id=266">issue 266</a>).', CCTM_TXTDOMAIN); ?></span></span>
		</div>

		<div class="cctm_element_wrapper" id="custom_field_wrapper_has_archive">
			<input type="checkbox" name="has_archive" class="cctm_checkbox" id="has_archive" value="1" <?php if (isset($data['def']['has_archive']) ) { print CCTM::is_checked($data['def']['has_archive']); } ?>/>
			<label for="has_archive" class="cctm_label cctm_checkbox_label" id="cctm_label_has_archive_label">
				<?php _e('Enable Archives', CCTM_TXTDOMAIN); ?>
			</label>
			<span class="cctm_description"><?php _e('If enabled, posts will be listed in archive lists (e.g. by month).', CCTM_TXTDOMAIN); ?></span>
		</div>

		<!--! Appear in Right Now Widget -->			
		<div class="cctm_element_wrapper" id="custom_field_wrapper_cctm_enable_right_now">		
			<input type="checkbox" name="cctm_enable_right_now" class="cctm_checkbox" id="cctm_enable_right_now" value="1" <?php 
				print CCTM::is_checked($data['def']['cctm_enable_right_now']); 
			?>/> 
			<label for="cctm_enable_right_now" class="cctm_label cctm_checkbox_label" id="cctm_label_cctm_enable_right_now"><?php _e('Show in Right Now Widget', CCTM_TXTDOMAIN); ?></label>
			<span class="cctm_description"><?php _e('Should posts with this post type appear in the Dashboard "Right Now" widget? The global setting must be enabled.', CCTM_TXTDOMAIN); ?></span>
		</div>	

		<!--!Custom Order By -->		
		<div class="cctm_element_wrapper" id="custom_field_wrapper_custom_orderby">
			<label for="custom_orderby" class="cctm_label cctm_text_label" id="cctm_label_custom_orderby">
				<?php _e('Order By', CCTM_TXTDOMAIN); ?>
			</label>
			<select name="custom_orderby" class="cctm_dropdown" id="custom_orderby">
				<?php print $data['orderby_options']; ?>
			</select>
			<span class="cctm_description"><?php _e('How do you want your posts to sort?', CCTM_TXTDOMAIN); ?></span>
		</div>


		
		<!--!Custom order -->
		<div class="cctm_element_wrapper" id="custom_field_wrapper_custom_order">		
			<label for="custom_order" class="cctm_label cctm_text_label" id="cctm_label_custom_order"><?php _e('Sort Order', CCTM_TXTDOMAIN); ?></label>
			<select name="custom_order" class="cctm_dropdown" id="custom_order">
				<option value="ASC" <?php print CCTM::is_selected('ASC',$data['def']['custom_order']); ?>><?php _e('ASC'); ?></option>
				<option value="DESC" <?php print CCTM::is_selected('DESC',$data['def']['custom_order']); ?>><?php _e('DESC'); ?></option>
			</select>
			<span class="cctm_description"><?php _e('If you specify a custom <code>Order By</code>, this setting will determine if your posts sort in ascending or descending order.', CCTM_TXTDOMAIN); ?></span>
		</div>

			
	</div>

	<!--!TAXONOMIES================================================================================================ -->
	<div id="taxonomies-tab">
			<h3><?php _e('Taxonomies', CCTM_TXTDOMAIN); ?></h3>
			
			<p><?php _e('Taxonomies offer ways to classify data as an aid to searching.', CCTM_TXTDOMAIN); ?></p>
			<p><?php _e("Currently, this plugin only allows you to use default taxonomies with your content types. We recommend using momo360modena's <a href='http://wordpress.org/extend/plugins/simple-taxonomy/'>Simple Taxonomy</a> plugin to create custom taxonomies.", CCTM_TXTDOMAIN); ?></p>
						
			<div class="cctm_element_wrapper" id="custom_field_wrapper_taxonomy_categories">			
				<input type="checkbox" name="taxonomies[]" class="cctm_checkbox" id="taxonomy_categories" value="category" <?php print CCTM::is_checked($data['def']['taxonomies'], 'category'); ?> /> 
				<label for="taxonomy_categories" class="cctm_label cctm_checkbox_label" id="cctm_label_taxonomies[]"><?php _e('Enable Categories', CCTM_TXTDOMAIN); ?></label>
				<span class="cctm_description"><?php _e('Hierarchical based classification.', CCTM_TXTDOMAIN); ?></span>
			</div>
			
			
			<div class="cctm_element_wrapper" id="custom_field_wrapper_taxonomy_tags">			
				<input type="checkbox" name="taxonomies[]" class="cctm_checkbox" id="taxonomy_tags" value="post_tag"  <?php print CCTM::is_checked($data['def']['taxonomies'], 'post_tag'); ?>/> 
				<label for="taxonomy_tags" class="cctm_label cctm_checkbox_label" id="cctm_label_taxonomies[]"><?php _e('Enable Tags', CCTM_TXTDOMAIN); ?></label>
				<span class="cctm_description"><?php _e('Simple word associations.', CCTM_TXTDOMAIN); ?></span>
			</div>
			
			<?php 
			// Handle all other taxonomies
			$taxonomies = get_taxonomies( array(), 'objects');
			foreach ($taxonomies as $tax => $t): 
				if (in_array($tax, array('category','post_tag','nav_menu','link_category','post_format'))) {
					continue; // skip
				}
			?>
				<div class="cctm_element_wrapper" id="custom_field_wrapper_taxonomy_<?php print $t->name; ?>">			
					<input type="checkbox" name="taxonomies[]" class="cctm_checkbox" id="taxonomy_<?php print $t->name; ?>" value="<?php print $t->name; ?>" <?php print CCTM::is_checked($data['def']['taxonomies'], $t->name); ?> /> 
					<label for="taxonomy_<?php print $t->name; ?>" class="cctm_label cctm_checkbox_label" id="cctm_label_taxonomies[]"><?php print $t->labels->name; ?></label>
				</div>
			<?php endforeach; ?>
	</div>


</div>







		<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	<br/>
		<div class="custom_content_type_mgr_form_controls">
			<input type="submit" class="button-primary" value="<?php print $data['submit']; ?>" />
		</div>
