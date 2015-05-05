<tr id="cctm_custom_field_<?php print $data['name']; ?>" class="active">
	<td><?php print $data['icon']; ?></td>
	<td class="plugin-title">
		<strong><?php print $data['label']; ?></strong> (<?php print $data['name']; ?>)
	</td>
	<td><?php print $data['post_types']; ?></td>
	<td><?php print $data['options_desc']; ?></td>
	<td class="column-description desc">
		<div class="plugin-description"><p><?php print $data['description']; ?></p></div>
		<div class="active second plugin-version-author-uri">
			<?php print $data['edit_field_link']; ?> | <?php print $data['delete_field_link']; ?> | <?php /* print $data['manage_associations_link']; */ ?> <?php print $data['duplicate_field_link']; ?> | <a href="?page=cctm_fields&a=merge_custom_fields&field=<?php print $data['name'];?>">Merge</a></div>
	</td>
</tr>