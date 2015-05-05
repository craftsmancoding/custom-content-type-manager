<script>
jQuery(function() {
	jQuery( "#custom-field-list" ).sortable({
			items: "tr:not(.no-sort)"
		});
	jQuery( "#custom-field-list" ).disableSelection();
});
</script>

<form id="custom_post_type_manager_basic_form" method="post">

	<input type="submit" class="button-primary" id="submit" value="<?php _e('Save', CCTM_TXTDOMAIN); ?>" />
	<?php printf('<a href="?page=cctm&a=list_post_types" class="button">%s</a>', __('Cancel', CCTM_TXTDOMAIN) );?>

	<br /><br/>

	<?php wp_nonce_field($data['action_name'], $data['nonce_name']); ?>
	
<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope="col" id="sorter" class=""  style="width: 10px;"><?php _e('Included', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="icon" class=""  style="width: 20px;">&nbsp;</th>
		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope="col" id="sorter" class=""  style="">&nbsp;</th>
		<th scope="col" id="icon" class=""  style="width: 20px;">&nbsp;</th>
		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</tfoot>

<tbody id="custom-field-list">

	<?php print $data['content']; ?>
	
</tbody>
</table>

	<br />
	<input type="submit" class="button-primary" id="submit" value="<?php _e('Save', CCTM_TXTDOMAIN); ?>" />
	<?php printf('<a href="?page=cctm&a=list_post_types" class="button">%s</a>', __('Cancel', CCTM_TXTDOMAIN) );?>
</form>