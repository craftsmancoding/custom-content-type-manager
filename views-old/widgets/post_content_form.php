<p><?php print $data['description']; ?>
<a href="http://code.google.com/p/wordpress-custom-content-type-manager/wiki/Post_Widget"><img src="'.CCTM_URL.'/images/question-mark.gif" width="16" height="16" /></a>
</p>

<label class="cctm_label" for="<?php print $data['post_type_id']; ?>">Post Type</label>

<select name="<?php print $data['post_type_name']; ?>" id="<?php $this->get_field_id('post_type'); ?>">
	<?php print $data['post_type_options']; ?>
</select>
<span class="button" 
onclick="javascript:select_post('<?php print $data['target_id_id']; ?>','<?php print $data['target_id_name']; ?>','<?php print $data['post_type_id']; ?>');">
	<?php _e('Choose Post', CCTM_TXTDOMAIN); ?>
</span>

<br/><br/>
<strong>Selected Post</strong><br/>
<!-- also target for Ajax writes -->
<div id="<?php print $data['target_id_id']; ?>"></div>
<!-- Thickbox ID -->
<div id="target_<?php print $this->get_field_id('target_id'); ?>"></div>
<br/><br/>

<input type="checkbox" name="<?php print $this->get_field_name('override_title'); ?>" class="cctm_checkbox_label" value="1"/> <label class="">Override Post Title</label>
<label class="cctm_label" for="<?php print $this->get_field_id('title'); ?>"><?php _e('Title', CCTM_TXTDOMAIN); ?></label>
<input type="text" name="<?php $this->get_field_name('title'); ?>" id="<?php print $this->get_field_id('title'); ?>" value="<?php print $data['title']; ?>" />


<label class="cctm_label" for="<?php print $this->get_field_id('formatting_string'); ?>"><?php _e('Formatting String', CCTM_TXTDOMAIN); ?></label>
<textarea name="<?php print $this->get_field_name('formatting_string'); ?>" id="<?php print $this->get_field_id('formatting_string'); ?>" rows="3" cols="30"><?php print $data['formatting_string']; ?></textarea>
