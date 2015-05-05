<p><?php _e('Choose the type of field you want to create.', CCTM_TXTDOMAIN); ?></p>
<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope="col" id="name" class=""  style="width: 20px;">&nbsp;</th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Field Type Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope="col" id="name" class=""  style="width: 20px;">&nbsp;</th>
		<th scope="col" id="description" class="manage-column column-description"  style=""><?php _e('Field Type Description', CCTM_TXTDOMAIN); ?></th>	
	</tr>
</tfoot>

<tbody id="custom-field-list">

	<?php print $data['fields']; ?>
	
</tbody>
</table>
