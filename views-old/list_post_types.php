<table class="wp-list-table widefat plugins" cellspacing="0">
	<thead>
		<tr>
            <th scope="col" id="name" class="manage-column column-name" style=""><?php _e('Content Type',CCTM_TXTDOMAIN) ?></th>
			<th scope="col" id="description" class="manage-column column-description" style=""><?php _e('Description',CCTM_TXTDOMAIN) ?></th>	
		</tr>
	</thead>
	
	<tfoot>
		<tr>
			<th scope="col"  class="manage-column column-name" style=""><?php _e('Content Type',CCTM_TXTDOMAIN) ?></th>
			<th scope="col"  class="manage-column column-description" style=""><?php _e('Description',CCTM_TXTDOMAIN) ?></th>	
		</tr>
	</tfoot>

	<tbody id="the-list">

	<?php print $data['row_data']; ?>
	
	</tbody>
</table>