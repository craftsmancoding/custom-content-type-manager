<table class="wp-list-table widefat plugins" cellspacing="0">
<thead>
	<tr>
		<th scope="col" id="name" class=""><?php _e('Title', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="context" class="manage-column"  style=""><?php _e('Context', CCTM_TXTDOMAIN); ?></th>
		<th scope="col" id="priority" class="manage-column"  style=""><?php _e('Priority', CCTM_TXTDOMAIN); ?></th>
	</tr>
</thead>

<tfoot>
	<tr>
		<th scope="col" id="name" class="">&nbsp;</th>
		<th scope="col" id="context" class="manage-column"  style="">&nbsp;</th>
		<th scope="col" id="priority" class="manage-column"  style="">&nbsp;</th>	
	</tr>
</tfoot>

<tbody id="custom-field-list">

	<?php print $data['rows']; ?>
	
</tbody>
</table>
