<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope="col" id="icon" class=""  style="width: 20px;"><?php _e('Type', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Post Types', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="options" class=""  style="width: 200px;"><?php _e('Options', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope="col" id="icon" class=""  style="width: 20px;"><?php _e('Type', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Field', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="name" class=""  style="width: 200px;"><?php _e('Post Types', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="options" class=""  style="width: 200px;"><?php _e('Options', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</tfoot>

<tbody id="custom-field-list">

	<?php print $data['fields']; ?>
	
</tbody>
</table>
