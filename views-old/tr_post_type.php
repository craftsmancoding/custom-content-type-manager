<tr id="<?php print $data['post_type']; ?>" class="<?php print $data['class']; ?>">
	<td class="plugin-title">
		<strong><?php print $data['icon']; ?> <?php print $data['post_type']; ?></strong>
		<div class="row-actions-visible">
			<?php print $data['activate_deactivate_delete_links']; ?>
		</div>
	</td>
	<td class="column-description desc">
		<div class="plugin-description"><p><?php print $data['description']; ?></p></div>
		<div class="second plugin-version-author-uri">
			<?php print $data['edit_manage_view_links']; ?>
		</div>
	</td>
</tr>
